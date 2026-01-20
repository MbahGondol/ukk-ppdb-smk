<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\PembayaranSiswa;
use Illuminate\Support\Facades\DB;

class VerifikasiController extends Controller
{
    /**
     * Menampilkan antrean siswa yang statusnya 'Terdaftar' (Belum Diterima/Ditolak)
     */
    public function index()
    {
        // Optimasi Query: Eager Loading untuk mencegah N+1 Problem
        $data_siswa = CalonSiswa::with(['user', 'jurusan', 'gelombang'])
                                ->where('status_pendaftaran', 'Terdaftar') 
                                ->orderBy('tanggal_submit', 'asc')
                                ->get();

        return view('admin.verifikasi.index', ['data_siswa' => $data_siswa]);
    }

    public function show(string $id)
    {
        $siswa = CalonSiswa::with([
                            'user', 'jurusan', 'tipeKelas', 'penanggungJawab', 'dokumen', 
                            'rencanaPembayaran.pembayaran.buktiPembayaran', // Nested Relation
                            'gelombang', 'promo'
                        ])->findOrFail($id);
        
        return view('admin.verifikasi.show', ['siswa' => $siswa]);
    }

    /**
     * Update Status Manual (Terima/Tolak/Revisi Data Diri)
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'aksi' => 'required|in:terima,tolak,revisi',
            'catatan_admin' => 'required_if:aksi,tolak,revisi|nullable|string|max:500'
        ], [
            'catatan_admin.required_if' => 'Alasan wajib diisi jika Anda menolak atau meminta revisi siswa!'
        ]);
        
        DB::transaction(function() use ($request, $id) {
            $siswa = CalonSiswa::findOrFail($id);
            
            $dataUpdate = [
                'catatan_admin' => $request->catatan_admin
            ];

            if ($request->aksi == 'terima') {
                $dataUpdate['status_pendaftaran'] = 'Resmi Diterima';
                // Otomatis validasi semua dokumen jika admin klik Terima Manual
                $siswa->dokumen()->update(['status_verifikasi' => 'Valid']);
            } 
            elseif ($request->aksi == 'revisi') {
                $dataUpdate['status_pendaftaran'] = 'Melengkapi Berkas'; // Kembalikan ke siswa
            } 
            elseif ($request->aksi == 'tolak') {
                $dataUpdate['status_pendaftaran'] = 'Ditolak';
            }

            $siswa->update($dataUpdate);
        });

        return redirect()->route('admin.verifikasi.index')->with('success', 'Status siswa berhasil diperbarui.');
    }

    /**
     * THE MAGIC METHOD: Verifikasi Pembayaran + Auto Accept
     */
    public function verifikasiPembayaran(Request $request, string $id)
    {
        $request->validate(['aksi' => 'required|in:terima,tolak']);
        
        $pembayaran = PembayaranSiswa::findOrFail($id);
        $pesan = 'Status pembayaran diperbarui.';

        DB::transaction(function () use ($pembayaran, $request, &$pesan) {
            
            // 1. Update Status Pembayaran (Verified / Failed)
            $statusBaru = ($request->aksi == 'terima') ? 'Verified' : 'Failed';
            $pembayaran->update(['status' => $statusBaru]);

            // 2. HITUNG ULANG TOTAL (Rekapitulasi Otomatis)
            $rencana = $pembayaran->rencanaPembayaran;
            
            // Hitung hanya yang 'Verified'
            $total_terbayar = $rencana->pembayaran()->where('status', 'Verified')->sum('jumlah');
            $rencana->total_sudah_dibayar = $total_terbayar;

            // Cek Lunas?
            if ($total_terbayar >= $rencana->total_nominal_biaya) {
                $rencana->status = 'Lunas';
            } else {
                $rencana->status = 'Belum Lunas';
            }
            $rencana->save();

            // 3. AUTO-ACCEPTANCE LOGIC (FITUR WOW FACTOR)
            // Jika pembayaran Valid DAN Lunas (atau minimal 50% - opsional), otomatis TERIMA siswa
            // Syarat: Siswa belum 'Resmi Diterima' dan belum 'Ditolak'
            
            $siswa = $rencana->calonSiswa;
            $ambang_batas = $rencana->total_nominal_biaya * 0.5; // Contoh: Minimal bayar 50% bisa diterima

            if ($statusBaru == 'Verified' && 
                $total_terbayar >= $ambang_batas && 
                in_array($siswa->status_pendaftaran, ['Terdaftar', 'Melengkapi Berkas'])) {
                
                $siswa->update([
                    'status_pendaftaran' => 'Resmi Diterima',
                    'catatan_admin' => 'Selamat! Anda diterima secara otomatis setelah verifikasi pembayaran.'
                ]);

                // Opsional: Kunci Dokumen jadi Valid juga
                $siswa->dokumen()->update(['status_verifikasi' => 'Valid']);

                $pesan .= ' DAN SISWA OTOMATIS DITERIMA (Auto-Accept).';
            }
        });

        return back()->with('success', $pesan);
    }
}