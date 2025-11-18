<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gelombang; 

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
}