<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama admin.
     */
    public function index()
    {
        // Di sini kita bisa mengambil statistik: jumlah siswa terdaftar, total pendapatan, dll.
        
        // Contoh data statistik sederhana (Data statis untuk saat ini)
        $statistik = [
            'totalSiswa' => 150,
            'siswaBaruHariIni' => 12,
            'totalPendapatan' => 550000000, // Rp 550 Juta
            'gelombangAktif' => 'Gelombang 1',
        ];

        // Memanggil view dashboard dan mengirimkan data statistik
        return view('admin.dashboard', compact('statistik'));
    }
}