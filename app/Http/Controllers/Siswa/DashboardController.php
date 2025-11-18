<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Dapatkan ID user yang sedang login
        $userId = Auth::id();

        // 2. Cek apakah user ini sudah punya data di tabel 'calon_siswa'
        // Kita gunakan relasi 'calonSiswa' yang sudah kita buat di Model User
        $calonSiswa = Auth::user()->calonSiswa;

        return view('siswa.dashboard', [
            'calonSiswa' => $calonSiswa
        ]);
    }
}