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
                            'rencanaPembayaran.pembayaran.buktiPembayaran' 
                        ])
                        ->findOrFail($id);
        
        // 2. Tampilkan View, kirim data $siswa
        return view('admin.verifikasi.show', [
            'siswa' => $siswa
        ]);
    }

    /**
     * Memproses aksi (verifikasi / tolak) dari Admin.
     * (LOGIKA "UPDATE" - STATUS)
     */
    public function updateStatus(Request $request, string $id)
    {
        // ... (di dalam updateStatus)
        // 1. Validasi input dari admin
        $request->validate([
            'aksi' => 'required|in:terima,tolak',
            // 'required_if' artinya: 'catatan_admin' WAJIB diisi JIKA 'aksi' == 'tolak'
            'catatan_admin' => 'required_if:aksi,tolak|nullable|string|max:1000'
        ]);

        $siswa = CalonSiswa::findOrFail($id);

        if ($request->aksi == 'terima') {
            // 2. Jika diterima (Verifikasi Awal)
            $siswa->update([
                'status_pendaftaran' => 'Proses Verifikasi',
                'catatan_admin' => null // Kosongkan catatan jika dia diterima
            ]);
            
            return redirect()->route('admin.verifikasi.index')
                            ->with('success', 'Siswa ' . $siswa->nama_lengkap . ' berhasil diverifikasi.');

        } elseif ($request->aksi == 'tolak') {
            // 3. Jika ditolak
            $siswa->update([
                'status_pendaftaran' => 'Ditolak',
                'catatan_admin' => $request->catatan_admin // <-- SIMPAN CATATANNYA
            ]);

            return redirect()->route('admin.verifikasi.index')
                            ->with('success', 'Siswa ' . $siswa->nama_lengkap . ' berhasil ditolak.');
        }
        
    }
}