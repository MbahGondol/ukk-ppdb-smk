<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BiayaPerJurusanTipeKelas;
use App\Models\RencanaPembayaran;
use App\Models\PembayaranSiswa;
use App\Models\BuktiPembayaran;
use App\Models\JurusanTipeKelas; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->calonSiswa;

        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Isi formulir pendaftaran dulu.');
        }

        // --- SATPAM (GATEKEEPER) MULAI ---
        // Integritas Data: Mencegah akses pembayaran jika dokumen belum lengkap
        
        // A. Tentukan dokumen dasar yang WAJIB ada
        $wajibDocs = ['Kartu Keluarga', 'Akte Kelahiran', 'Ijazah SMP', 'Foto Formal'];

        // B. Logika Kondisional
        if ($siswa->tinggal_bersama == 'Wali') {
            $wajibDocs[] = 'KTP Wali'; 
        } else {
            // Jika tinggal dengan Orang Tua
            $wajibDocs[] = 'KTP Ayah';
            $wajibDocs[] = 'KTP Ibu';
        }

        // C. Ambil dokumen yang SUDAH diupload siswa ini
        $uploadedDocs = $siswa->dokumen->pluck('tipe_dokumen')->toArray();

        // D. Bandingkan
        $kurang = array_diff($wajibDocs, $uploadedDocs);

        // E. Tindakan Tegas
        if (!empty($kurang)) {
            return redirect()->route('siswa.dokumen.index')
                             ->with('error', 'Mohon lengkapi dokumen wajib berikut sebelum lanjut: ' . implode(', ', $kurang));
        }
        // --- SATPAM SELESAI ---

        // --- LOGIKA PEMBAYARAN ---
        $jurusanTipeKelas = JurusanTipeKelas::where('jurusan_id', $siswa->jurusan_id)
                                            ->where('tipe_kelas_id', $siswa->tipe_kelas_id)
                                            ->firstOrFail();

        $total_kasar = BiayaPerJurusanTipeKelas::where('jurusan_tipe_kelas_id', $jurusanTipeKelas->id)
                                                ->sum('nominal');

        $potongan = $siswa->promo ? $siswa->promo->potongan : 0;
        $total_bersih = max(0, $total_kasar - $potongan); 

        // Sync Tagihan (Self-Healing)
        $tagihanInduk = RencanaPembayaran::firstOrCreate(
            ['calon_siswa_id' => $siswa->id],
            ['total_nominal_biaya' => $total_bersih, 'total_sudah_dibayar' => 0, 'status' => 'Belum Lunas']
        );

        // Update jika nominal berubah (selama belum ada yang dibayar)
        if ($tagihanInduk->total_sudah_dibayar == 0 && intval($tagihanInduk->total_nominal_biaya) != intval($total_bersih)) {
            $tagihanInduk->update(['total_nominal_biaya' => $total_bersih]);
        }

        $riwayat_pembayaran = PembayaranSiswa::where('rencana_pembayaran_id', $tagihanInduk->id)
                                            ->with('buktiPembayaran')
                                            ->orderBy('tanggal_pembayaran', 'desc')
                                            ->get();

        return view('siswa.pembayaran', [
            'tagihan' => $tagihanInduk,
            'riwayat_pembayaran' => $riwayat_pembayaran,
        ]);
    }

    public function store(Request $request) 
    {
        $request->validate([
            'jumlah' => 'required|integer|min:10000',
            'tanggal_pembayaran' => 'required|date',
            'file_bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $siswa = Auth::user()->calonSiswa;
        $tagihanInduk = $siswa->rencanaPembayaran()->first(); 
        
        $sisa = $tagihanInduk->total_nominal_biaya - $tagihanInduk->total_sudah_dibayar;
        if ($request->jumlah > ($sisa + 50000)) { 
            return back()->with('error', 'Nominal pembayaran melebihi sisa tagihan.');
        }

        try {
            DB::transaction(function () use ($request, $siswa, $tagihanInduk) {
                
                $pembayaran = PembayaranSiswa::create([
                    'rencana_pembayaran_id' => $tagihanInduk->id,
                    'jumlah' => $request->jumlah,
                    'tanggal_pembayaran' => $request->tanggal_pembayaran,
                    'metode' => 'Transfer Bank',
                    'status' => 'Pending', 
                ]);

                $file = $request->file('file_bukti');
                $nama_file = 'bukti_' . $siswa->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('bukti_pembayaran', $nama_file, 'public');

                BuktiPembayaran::create([
                    'pembayaran_id' => $pembayaran->id,
                    'file_path' => $path,
                ]);

                if ($siswa->status_pendaftaran == 'Melengkapi Berkas') {
                    $siswa->update(['status_pendaftaran' => 'Terdaftar']);
                }
            }); 

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload: ' . $e->getMessage());
        }

        return back()->with('success', 'Bukti pembayaran berhasil dikirim. Tunggu verifikasi Admin.');
    }

    public function destroy(string $id) 
    {
        $pembayaran = PembayaranSiswa::findOrFail($id);
        
        if ($pembayaran->rencanaPembayaran->calon_siswa_id != Auth::user()->calonSiswa->id) abort(403);
        if ($pembayaran->status == 'Verified') return back()->with('error', 'Data yang sudah valid tidak bisa dihapus.');

        if ($pembayaran->buktiPembayaran) {
            Storage::disk('public')->delete($pembayaran->buktiPembayaran->file_path);
            $pembayaran->buktiPembayaran->delete();
        }
        $pembayaran->delete();

        return back()->with('success', 'Pembayaran dibatalkan.');
    }
}