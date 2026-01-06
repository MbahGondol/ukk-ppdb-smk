<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\PembayaranSiswa;
use Illuminate\Support\Facades\DB; // PERBAIKAN 1: Import Facade DB

class VerifikasiController extends Controller
{
    /**
     * Menampilkan daftar semua siswa yang perlu diverifikasi.
     */
    public function index()
    {
        $data_siswa = CalonSiswa::with(['user', 'jurusan'])
                                ->where('status_pendaftaran', 'Terdaftar')
                                ->orderBy('tanggal_submit', 'asc')
                                ->get();

        return view('admin.verifikasi.index', [
            'data_siswa' => $data_siswa
        ]);
    }

    /**
     * Menampilkan data LENGKAP satu siswa.
     */
    public function show(string $id)
    {
        $siswa = CalonSiswa::with([
                            'user', 
                            'jurusan', 
                            'tipeKelas', 
                            'penanggungJawab', 
                            'dokumen', 
                            'rencanaPembayaran.pembayaran.buktiPembayaran',
                            'gelombang', 
                            'promo'
                        ])
                        ->findOrFail($id);
        
        return view('admin.verifikasi.show', [
            'siswa' => $siswa
        ]);
    }

    /**
     * Memproses aksi (verifikasi / tolak) dari Admin.
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'aksi' => 'required|in:terima,tolak,revisi',
            'catatan_admin' => 'required_if:aksi,tolak,revisi|nullable|string|max:1000'
        ]);
        
        return DB::transaction(function() use ($request, $id) {
            $siswa = CalonSiswa::findOrFail($id);

            // 1. Logika TERIMA
            if ($request->aksi == 'terima') {
                
                $siswa->update([
                    'status_pendaftaran' => 'Resmi Diterima',
                    'catatan_admin' => null
                ]);

                // Update Dokumen jadi Valid
                $siswa->dokumen()->update([
                    'status_verifikasi' => 'Valid'
                ]);

                // Update Pembayaran jadi Verified
                if ($siswa->rencanaPembayaran) {
                    $siswa->rencanaPembayaran->pembayaran()->update([
                        'status' => 'Verified'
                    ]);
                }
                
                return redirect()->route('admin.verifikasi.index')
                                 ->with('success', 'Selamat! Siswa ' . $siswa->nama_lengkap . ' telah RESMI DITERIMA.');
            } 
            
            // 2. Logika REVISI
            elseif ($request->aksi == 'revisi') {
                $siswa->update([
                    'status_pendaftaran' => 'Melengkapi Berkas',
                    'catatan_admin' => $request->catatan_admin
                ]);

                return redirect()->route('admin.verifikasi.index')
                                 ->with('warning', 'Status dikembalikan ke "Melengkapi Berkas".');
            }

            // 3. Logika TOLAK
            elseif ($request->aksi == 'tolak') {
                $siswa->update([
                    'status_pendaftaran' => 'Ditolak',
                    'catatan_admin' => $request->catatan_admin
                ]);

                return redirect()->route('admin.verifikasi.index')
                                 ->with('error', 'Siswa ' . $siswa->nama_lengkap . ' telah DITOLAK.');
            }
            
            // Default jika tidak masuk kondisi di atas (seharusnya tidak mungkin karena validasi)
            return redirect()->route('admin.verifikasi.index');
        });
    }

    public function verifikasiPembayaran(Request $request, string $id)
    {
        $request->validate([
            'aksi' => 'required|in:terima,tolak',
        ]);
        
        $pembayaran = PembayaranSiswa::findOrFail($id);
        
        if ($request->aksi == 'terima') {
            $pembayaran->update(['status' => 'Verified']);
        } else {
            $pembayaran->update(['status' => 'Failed']);
        }

        // LOGIKA HITUNG ULANG
        $rencana = $pembayaran->rencanaPembayaran;
        
        $total_terbayar = $rencana->pembayaran()
                                 ->where('status', 'Verified')
                                 ->sum('jumlah');

        $rencana->total_sudah_dibayar = $total_terbayar;

        if ($total_terbayar >= $rencana->total_nominal_biaya) {
            $rencana->status = 'Lunas';
        } else {
            $rencana->status = 'Belum Lunas';
        }
        
        $rencana->save();

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}