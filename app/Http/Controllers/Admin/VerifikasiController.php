<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;

class VerifikasiController extends Controller
{
    /**
     * Menampilkan daftar semua siswa yang perlu diverifikasi.
     * (LOGIKA "READ" - DAFTAR)
     */
    public function index()
    {
        // 1. Ambil data siswa yang statusnya "Terdaftar"
        // Kita juga ambil relasi 'user' (untuk nama) dan 'jurusan' (untuk pilihan)
        $data_siswa = CalonSiswa::with(['user', 'jurusan'])
                                ->where('status_pendaftaran', 'Terdaftar')
                                ->orderBy('tanggal_submit', 'asc') // Urutkan dari yang terlama
                                ->get();

        // 2. Tampilkan View, kirim data $data_siswa
        return view('admin.verifikasi.index', [
            'data_siswa' => $data_siswa
        ]);
    }

    /**
     * Menampilkan data LENGKAP satu siswa.
     * (LOGIKA "READ" - DETAIL)
     */
    public function show(string $id)
    {
        // 1. Ambil SEMUA data relasi dari satu siswa
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
        
        // 2. Tampilkan View, kirim data $siswa
        return view('admin.verifikasi.show', [
            'siswa' => $siswa
        ]);
    }

    /**
     * Memproses aksi (verifikasi / tolak) dari Admin.
     */
    public function updateStatus(Request $request, string $id)
    {
        // 1. Validasi input
        $request->validate([
            'aksi' => 'required|in:terima,tolak,revisi',
            'catatan_admin' => 'required_if:aksi,tolak,revisi|nullable|string|max:1000'
        ]);

        $siswa = CalonSiswa::findOrFail($id);

        // 2. Logika TERIMA (Lulus & Validasi Semua Berkas)
        if ($request->aksi == 'terima') {
            
            // A. Ubah Status Siswa
            $siswa->update([
                'status_pendaftaran' => 'Resmi Diterima',
                'catatan_admin' => null
            ]);

            // B. Ubah Semua Status DOKUMEN menjadi 'Valid'
            // (Kita pakai update() massal query builder agar cepat)
            $siswa->dokumen()->update([
                'status_verifikasi' => 'Valid'
            ]);

            // C. Ubah Semua Status PEMBAYARAN menjadi 'Verified'
            if ($siswa->rencanaPembayaran) {
                $siswa->rencanaPembayaran->pembayaran()->update([
                    'status' => 'Verified'
                ]);
            }
            
            return redirect()->route('admin.verifikasi.index')
                             ->with('success', 'Selamat! Siswa ' . $siswa->nama_lengkap . ' telah RESMI DITERIMA dan seluruh berkas divalidasi.');
        } 
        
        // 3. Logika REVISI (Kembalikan ke Siswa)
        elseif ($request->aksi == 'revisi') {
            $siswa->update([
                'status_pendaftaran' => 'Melengkapi Berkas',
                'catatan_admin' => $request->catatan_admin
            ]);

            // Opsional: Kita bisa set dokumen jadi 'Invalid' jika mau, 
            // tapi membiarkannya 'Pending' juga tidak masalah agar siswa bisa hapus/ganti.

            return redirect()->route('admin.verifikasi.index')
                             ->with('warning', 'Status dikembalikan ke "Melengkapi Berkas". Siswa diminta memperbaiki data.');
        }

        // 4. Logika TOLAK (Gagal Permanen)
        elseif ($request->aksi == 'tolak') {
            $siswa->update([
                'status_pendaftaran' => 'Ditolak',
                'catatan_admin' => $request->catatan_admin
            ]);

            return redirect()->route('admin.verifikasi.index')
                             ->with('error', 'Siswa ' . $siswa->nama_lengkap . ' telah DITOLAK.');
        }
        
        return redirect()->route('admin.verifikasi.index');
    }
}