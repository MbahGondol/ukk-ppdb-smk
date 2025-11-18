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
     * (LOGIKA "READ" - CANGGIH)
     */
    public function index()
    {
        $siswa = Auth::user()->calonSiswa;

        // 1. Keamanan: Pastikan siswa sudah mendaftar
        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda harus mengisi formulir pendaftaran terlebih dahulu.');
        }

        // 2. LOGIKA KUNCI: Cari "Tagihan Induk" (RencanaPembayaran)
        $tagihanInduk = RencanaPembayaran::where('calon_siswa_id', $siswa->id)->first();

        // 3. Jika "Tagihan Induk" BELUM ADA, maka kita BUATKAN
        if (!$tagihanInduk) {

            // A. Cari ID JurusanTipeKelas yang dipilih siswa
            $jurusanTipeKelas = JurusanTipeKelas::where('jurusan_id', $siswa->jurusan_id)
                                              ->where('tipe_kelas_id', $siswa->tipe_kelas_id)
                                              ->first();

            // B. Tambahkan keamanan jika kombinasinya tidak ditemukan
            if (!$jurusanTipeKelas) {
                return back()->with('error', 'Gagal menemukan data biaya untuk jurusan Anda. Hubungi Admin.');
            }

            // C. Sekarang kita punya ID yang benar
            $jurusanTipeKelasId = $jurusanTipeKelas->id; 
                            
            // D. Cari semua biaya yang terkait dengan ID itu
            $semua_biaya = BiayaPerJurusanTipeKelas::where('jurusan_tipe_kelas_id', $jurusanTipeKelasId)->get();
            
            // E. Hitung totalnya
            $total_biaya = $semua_biaya->sum('nominal');

            // F. Buat "Tagihan Induk" (RencanaPembayaran - File O)
            $tagihanInduk = RencanaPembayaran::create([
                'calon_siswa_id' => $siswa->id,
                'total_nominal_biaya' => $total_biaya,
                'total_sudah_dibayar' => 0,
                'status' => 'Belum Lunas',
            ]);
        }

        // 4. Ambil semua riwayat pembayaran yang sudah di-upload
        $riwayat_pembayaran = PembayaranSiswa::where('rencana_pembayaran_id', $tagihanInduk->id)
                                            ->with('buktiPembayaran')
                                            ->orderBy('tanggal_pembayaran', 'desc')
                                            ->get();

        // 5. Tampilkan View, kirim data tagihan dan riwayat
        return view('siswa.pembayaran', [
            'tagihan' => $tagihanInduk,
            'riwayat_pembayaran' => $riwayat_pembayaran,
        ]);
    }

    /**
     * Menyimpan file bukti pembayaran.
     * (LOGIKA "CREATE" - CANGGIH)
     */
    // PERBAIKAN 2: Tambahkan (Request $request)
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
        $tagihanInduk = $siswa->rencanaPembayaran()->first(); // Asumsi tagihan sudah ada

        // 2. Validasi tambahan (opsional tapi bagus)
        $sisa_tagihan = $tagihanInduk->total_nominal_biaya - $tagihanInduk->total_sudah_dibayar;
        if ($request->jumlah > $sisa_tagihan) {
            return back()->with('error', 'Jumlah yang Anda bayarkan melebihi sisa tagihan.');
        }
        
        // 3. LOGIKA KUNCI: Gunakan Transaction
        try {
            DB::transaction(function () use ($request, $siswa, $file, $tagihanInduk) {
                
                // A. Simpan data pembayaran (File P)
                $pembayaran = PembayaranSiswa::create([
                    'rencana_pembayaran_id' => $tagihanInduk->id,
                    'jumlah' => $request->jumlah,
                    'tanggal_pembayaran' => $request->tanggal_pembayaran,
                    'metode' => $request->metode ?? 'Transfer Bank',
                    'status' => 'Pending', 
                ]);

                // B. Buat nama file unik
                $nama_file_unik = 'bayar_' . $siswa->id . '_' . $pembayaran->id . '.' . $file->getClientOriginalExtension();

                // C. Simpan file ke "Gudang"
                $path = $file->storeAs('bukti_pembayaran', $nama_file_unik, 'public');

                // D. Simpan data bukti pembayaran (File Q)
                BuktiPembayaran::create([
                    'pembayaran_id' => $pembayaran->id,
                    'file_path' => $path,
                ]);

                // E. LOGIKA UTAMA: Ubah Status Siswa
                if ($siswa->status_pendaftaran == 'Melengkapi Berkas') {
                    $siswa->update(['status_pendaftaran' => 'Terdaftar']);
                }

            }); // <-- Akhir dari Transaction

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi.');
    }

    /**
     * Menghapus file bukti pembayaran.
     * (LOGIKA "DELETE")
     */
    public function destroy(string $id) 
    {
        $pembayaran = PembayaranSiswa::findOrFail($id);
        $siswa = Auth::user()->calonSiswa;

        // 1. Keamanan: Pastikan pemilik yang menghapus
        if ($pembayaran->rencanaPembayaran->calon_siswa_id != $siswa->id) {
            return back()->with('error', 'Anda tidak punya izin.');
        }

        // 2. Keamanan: Jangan biarkan siswa menghapus yang sudah "Verified"
        if ($pembayaran->status == 'Verified') {
            return back()->with('error', 'Tidak bisa menghapus pembayaran yang sudah divalidasi.');
        }

        // 3. Gunakan Transaction (Hapus file, Bukti, dan Pembayaran)
        try {
            DB::transaction(function () use ($pembayaran) {
                // A. Hapus file dari "Gudang" (Storage)
                if ($pembayaran->buktiPembayaran) {
                    Storage::disk('public')->delete($pembayaran->buktiPembayaran->file_path);
                    $pembayaran->buktiPembayaran->delete(); // Hapus dari tabel bukti_pembayaran
                }
                
                // B. Hapus dari tabel pembayaran
                $pembayaran->delete();
            }); // <-- Akhir dari Transaction

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back()->with('success', 'Data pembayaran berhasil dihapus.');
    }
}