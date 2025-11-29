<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\CalonSiswa;

class PendaftarController extends Controller
{
    /**
     * Menampilkan daftar semua pendaftar (dengan filter).
     * (LOGIKA "READ")
     */
    public function index(Request $request) 
    {
        // 1. Ambil data status yang diminta dari URL (jika ada)
        // Contoh: /admin/pendaftar?status=Ditolak
        $status_filter = $request->query('status');

        // 2. Mulai bangun query. Ambil relasi yang kita butuhkan.
        $query = CalonSiswa::with(['user', 'jurusan', 'tipeKelas', 'gelombang']);

        // 3. LOGIKA FILTER: Jika ada filter 'status' di URL...
        if ($status_filter) {
            // ...maka tambahkan 'where' ke query
            $query->where('status_pendaftaran', $status_filter);
        }
        
        // 4. Jika tidak ada filter, query akan mengambil SEMUA data.
        // Urutkan dari yang paling baru mendaftar.
        $data_siswa = $query->orderBy('tanggal_submit', 'desc')->get();

        // 5. Tampilkan View, kirim data $data_siswa dan $status_filter
        return view('admin.pendaftar.index', [
            'data_siswa' => $data_siswa,
            'status_sekarang' => $status_filter // Untuk menandai link mana yang aktif
        ]);
    }
}