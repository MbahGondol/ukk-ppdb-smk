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
        // 1. Ambil filter dari URL
        $status_filter = $request->query('status');

        // 2. Query Data Utama (Untuk Tabel)
        $query = CalonSiswa::with(['user', 'jurusan', 'tipeKelas', 'gelombang']);

        if ($status_filter) {
            $query->where('status_pendaftaran', $status_filter);
        }
        
        $data_siswa = $query->orderBy('tanggal_submit', 'desc')->get();

        // 3. HITUNG JUMLAH (COUNTING) - INI BAGIAN BARUNYA
        // Kita hitung terpisah agar angka di Tab selalu muncul, 
        // tidak peduli filter apa yang sedang aktif.
        $counts = [
            'semua' => CalonSiswa::count(),
            'draft' => CalonSiswa::where('status_pendaftaran', 'Melengkapi Berkas')->count(),
            'terdaftar' => CalonSiswa::where('status_pendaftaran', 'Terdaftar')->count(),
            'diterima' => CalonSiswa::where('status_pendaftaran', 'Resmi Diterima')->count(),
            'ditolak' => CalonSiswa::where('status_pendaftaran', 'Ditolak')->count(),
        ];

        // 4. Kirim data dan hitungan ke View
        return view('admin.pendaftar.index', [
            'data_siswa' => $data_siswa,
            'status_sekarang' => $status_filter,
            'counts' => $counts
        ]);
    }
}