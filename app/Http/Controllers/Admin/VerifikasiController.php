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
                // 1. Update Status Siswa
                $dataUpdate['status_pendaftaran'] = 'Resmi Diterima';
                
                // 2. Validasi Dokumen Otomatis
                $siswa->dokumen()->update(['status_verifikasi' => 'Valid']);

                // 3. Validasi Pembayaran 'Pending' Otomatis
                // Jika Admin menerima siswa, anggap pembayaran yang sedang pending itu SAH.
                $rencana = $siswa->rencanaPembayaran;
                if ($rencana) {
                    // Update semua pembayaran Pending menjadi Verified
                    $rencana->pembayaran()->where('status', 'Pending')->update(['status' => 'Verified']);

                    // HITUNG ULANG (Rekapitulasi)
                    $total_terbayar = (int) $rencana->pembayaran()->where('status', 'Verified')->sum('jumlah');
                    $total_biaya    = (int) $rencana->total_nominal_biaya;

                    $rencana->total_sudah_dibayar = $total_terbayar;

                    // Cek Lunas
                    if (($total_biaya - $total_terbayar) <= 0) {
                        $rencana->status = 'Lunas';
                    } else {
                        $rencana->status = 'Belum Lunas';
                    }
                    $rencana->save();
                }

            } 
            elseif ($request->aksi == 'revisi') {
                $dataUpdate['status_pendaftaran'] = 'Melengkapi Berkas'; 
            } 
            elseif ($request->aksi == 'tolak') {
                $dataUpdate['status_pendaftaran'] = 'Ditolak';
                // Opsional: Jika ditolak, pembayaran pending bisa di-set Failed
                // $siswa->rencanaPembayaran->pembayaran()->where('status', 'Pending')->update(['status' => 'Failed']);
            }

            $siswa->update($dataUpdate);
        });

        return redirect()->route('admin.verifikasi.index')->with('success', 'Status siswa dan data terkait berhasil diperbarui.');
    }

    /**
     * THE MAGIC METHOD: Verifikasi Pembayaran + Aturan Cicilan Cerdas
     */
    public function verifikasiPembayaran(Request $request, string $id)
    {
        $request->validate(['aksi' => 'required|in:terima,tolak']);
        
        $pembayaran = PembayaranSiswa::findOrFail($id);
        $pesan = 'Status pembayaran diperbarui.';

        DB::transaction(function () use ($pembayaran, $request, &$pesan) {
            
            // -----------------------------------------------------------
            // 1. UPDATE STATUS PEMBAYARAN (Cicilan Masuk)
            // -----------------------------------------------------------
            $statusBaru = ($request->aksi == 'terima') ? 'Verified' : 'Failed';
            
            // Pakai forceFill untuk memaksa update status
            $pembayaran->forceFill(['status' => $statusBaru])->save();

            // -----------------------------------------------------------
            // 2. HITUNG ULANG TOTAL (Rekapitulasi Keuangan)
            // -----------------------------------------------------------
            $rencana = $pembayaran->rencanaPembayaran;
            
            // Hitung total uang yang statusnya 'Verified' (Sah masuk kas)
            $total_terbayar = (int) $rencana->pembayaran()->where('status', 'Verified')->sum('jumlah');
            $total_biaya    = (int) $rencana->total_nominal_biaya;
            
            // Update data di tabel rencana
            $rencana->total_sudah_dibayar = $total_terbayar;

            // Cek Lunas: Jika sisa <= 0, maka Lunas. Jika tidak, Belum Lunas.
            if (($total_biaya - $total_terbayar) <= 0) {
                $rencana->status = 'Lunas';
            } else {
                $rencana->status = 'Belum Lunas';
            }
            $rencana->save();

            // -----------------------------------------------------------
            // 3. LOGIKA STRATEGIS: AUTO-ACCEPTANCE BERDASARKAN PERSENTASE
            // -----------------------------------------------------------
            
            // ATURAN MAIN: Minimal bayar 50% baru dianggap RESMI DITERIMA
            // Ubah 0.5 menjadi 0.3 jika ingin 30%, atau 0.7 untuk 70%.
            $persentase_syarat = 0.5; 
            $nominal_syarat    = $total_biaya * $persentase_syarat;

            $siswa = $rencana->calonSiswa;

            // Logika: 
            // 1. Pembayaran baru saja di-ACC (Verified)
            // 2. Total uang masuk sudah melewati batas minimal (Threshold)
            // 3. Status siswa saat ini masih 'Terdaftar' atau 'Melengkapi Berkas' (Belum final)
            if ($statusBaru == 'Verified' && 
                $total_terbayar >= $nominal_syarat && 
                in_array($siswa->status_pendaftaran, ['Terdaftar', 'Melengkapi Berkas'])) {
                
                // BOM! Ubah status jadi Resmi Diterima
                $siswa->update([
                    'status_pendaftaran' => 'Resmi Diterima',
                    'catatan_admin' => 'Selamat! Pembayaran Anda telah memenuhi syarat minimal registrasi ulang.'
                ]);

                // Kunci Dokumen jadi Valid agar tidak bisa diutak-atik lagi
                $siswa->dokumen()->update(['status_verifikasi' => 'Valid']);

                $pesan .= ' Karena pembayaran sudah mencapai ' . ($persentase_syarat * 100) . '%, siswa OTOMATIS DITERIMA.';
            } 
            elseif ($statusBaru == 'Verified' && $total_terbayar < $nominal_syarat) {
                // Jika uang masuk tapi belum cukup target
                $pesan .= ' Pembayaran cicilan diterima, namun belum mencapai batas minimal untuk status "Resmi Diterima".';
            }
        });

        return back()->with('success', $pesan);
    }
}