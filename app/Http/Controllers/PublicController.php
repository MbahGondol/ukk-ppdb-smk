<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gelombang; 
use App\Models\Jurusan;

class PublicController extends Controller
{
    /**
     * Menampilkan halaman Beranda (Landing Page).
     */
    public function beranda()
    {
        // 2. TAMBAHKAN LOGIKA PENCARIAN INI
        $gelombang_aktif = Gelombang::where('tanggal_mulai', '<=', now())
                                    ->where('tanggal_selesai', '>=', now())
                                    ->with('promo')
                                    ->first();

        // 3. KIRIM DATA KE VIEW
        return view('public.beranda', [
            'gelombang_aktif' => $gelombang_aktif
        ]);
    }

    /**
     * Menampilkan halaman Profil Sekolah.
     */
    public function profil()
    {
        return view('public.profil');
    }

    /**
     * Menampilkan halaman Info Jurusan (Publik).
     */
    public function infoJurusan()
    {
        // Ambil semua data jurusan yang statusnya 'aktif'
        $semua_jurusan = Jurusan::where('aktif', true)->get();
        
        // Kirim data ke view
        return view('public.info_jurusan', [
            'semua_jurusan' => $semua_jurusan
        ]);
    }
}