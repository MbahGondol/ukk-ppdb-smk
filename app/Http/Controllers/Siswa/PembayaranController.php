<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\BiayaPerJurusanTipeKelas;
use App\Models\RencanaPembayaran;
use App\Models\PembayaranSiswa;
use App\Models\BuktiPembayaran;
use App\Models\JurusanTipeKelas; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    /**
     * Menampilkan halaman tagihan dan form upload.
     */
    public function index()
    {
        $siswa = Auth::user()->calonSiswa;

        // 1. Keamanan: Pastikan siswa sudah mendaftar
        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda harus mengisi formulir pendaftaran terlebih dahulu.');
        }

        // --- LANGKAH 1: HITUNG TOTAL HARGA TERBARU (DENGAN DISKON) ---
        
        // A. Cari ID JurusanTipeKelas siswa
        $jurusanTipeKelas = JurusanTipeKelas::where('jurusan_id', $siswa->jurusan_id)
                                            ->where('tipe_kelas_id', $siswa->tipe_kelas_id)
                                            ->first();

        if (!$jurusanTipeKelas) {
            return back()->with('error', 'Gagal menemukan data jurusan. Hubungi Admin.');
        }

        // B. Hitung Total Harga KASAR (Tanpa Diskon)
        $total_kasar = BiayaPerJurusanTipeKelas::where('jurusan_tipe_kelas_id', $jurusanTipeKelas->id)
                                                ->sum('nominal');

        // C. Hitung Potongan PROMO (LOGIKA BARU)
        $potongan = 0;
        if ($siswa->promo) {
            $potongan = $siswa->promo->potongan;
        }

        // D. Hitung Total BERSIH (Total - Potongan)
        $total_bersih = $total_kasar - $potongan;
        if ($total_bersih < 0) $total_bersih = 0; // Jaga-jaga agar tidak minus


        // --- LANGKAH 2: CEK ATAU BUAT TAGIHAN INDUK ---

        $tagihanInduk = RencanaPembayaran::firstOrCreate(
            ['calon_siswa_id' => $siswa->id],
            [
                // Jika baru pertama kali, pakai Total BERSIH
                'total_nominal_biaya' => $total_bersih, 
                'total_sudah_dibayar' => 0,
                'status' => 'Belum Lunas',
            ]
        );

        // --- LANGKAH 3: FITUR AUTO-UPDATE (PINTAR) ---
        
        // Jika siswa BELUM pernah bayar, kita cek apakah harganya berubah?
        // (Misal: Admin ubah harga, atau Promo baru masuk)
        if ($tagihanInduk->total_sudah_dibayar == 0) {
            // Cek apakah tagihan di database BEDA dengan perhitungan baru kita?
            // Kita pakai intval() untuk memastikan perbandingan angka akurat
            if (intval($tagihanInduk->total_nominal_biaya) != intval($total_bersih)) {
                
                $tagihanInduk->update([
                    'total_nominal_biaya' => $total_bersih
                ]);
                
                $tagihanInduk->refresh(); // Refresh data agar tampilan view update
            }
        }

        // --- LANGKAH 4: AMBIL RIWAYAT ---
        
        $riwayat_pembayaran = PembayaranSiswa::where('rencana_pembayaran_id', $tagihanInduk->id)
                                            ->with('buktiPembayaran')
                                            ->orderBy('tanggal_pembayaran', 'desc')
                                            ->get();

        return view('siswa.pembayaran', [
            'tagihan' => $tagihanInduk,
            'riwayat_pembayaran' => $riwayat_pembayaran,
        ]);
    }

    /**
     * Menyimpan file bukti pembayaran.
     */
    public function store(Request $request) 
    {
        // 1. Validasi
        $request->validate([
            'jumlah' => 'required|integer|min:10000',
            'tanggal_pembayaran' => 'required|date',
            'file_bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Maks 2MB
        ]);

        $siswa = Auth::user()->calonSiswa;
        $file = $request->file('file_bukti');
        $tagihanInduk = $siswa->rencanaPembayaran()->first(); 

        // 2. Validasi sisa tagihan
        $sisa_tagihan = $tagihanInduk->total_nominal_biaya - $tagihanInduk->total_sudah_dibayar;
        if ($request->jumlah > ($sisa_tagihan + 50000)) { // Batas toleransi 50rb
            return back()->with('error', 'Jumlah pembayaran terlalu jauh melebihi sisa tagihan.');
        }
        
        // 3. Simpan
        try {
            DB::transaction(function () use ($request, $siswa, $file, $tagihanInduk) {
                
                $pembayaran = PembayaranSiswa::create([
                    'rencana_pembayaran_id' => $tagihanInduk->id,
                    'jumlah' => $request->jumlah,
                    'tanggal_pembayaran' => $request->tanggal_pembayaran,
                    'metode' => $request->metode ?? 'Transfer Bank',
                    'status' => 'Pending', 
                ]);

                $nama_file_unik = 'bayar_' . $siswa->id . '_' . $pembayaran->id . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('bukti_pembayaran', $nama_file_unik, 'public');

                BuktiPembayaran::create([
                    'pembayaran_id' => $pembayaran->id,
                    'file_path' => $path,
                ]);

                if ($siswa->status_pendaftaran == 'Melengkapi Berkas') {
                    $siswa->update(['status_pendaftaran' => 'Terdaftar']);
                }

            }); 

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi.');
    }

    /**
     * Menghapus file bukti pembayaran.
     */
    public function destroy(string $id) 
    {
        $pembayaran = PembayaranSiswa::findOrFail($id);
        $siswa = Auth::user()->calonSiswa;

        if ($pembayaran->rencanaPembayaran->calon_siswa_id != $siswa->id) {
            return back()->with('error', 'Anda tidak punya izin.');
        }

        if ($pembayaran->status == 'Verified') {
            return back()->with('error', 'Tidak bisa menghapus pembayaran yang sudah divalidasi.');
        }

        try {
            DB::transaction(function () use ($pembayaran) {
                if ($pembayaran->buktiPembayaran) {
                    Storage::disk('public')->delete($pembayaran->buktiPembayaran->file_path);
                    $pembayaran->buktiPembayaran->delete();
                }
                $pembayaran->delete();
            }); 

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back()->with('success', 'Data pembayaran berhasil dihapus.');
    }
}