<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    /**
     * Menampilkan dashboard untuk calon siswa.
     */
    public function index()
    {
        // Mendapatkan data user yang sedang login
        $user = Auth::user();

        // Tampilkan view dashboard siswa
        return view('siswa.dashboard', compact('user'));
    }
}