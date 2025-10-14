<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Peran yang disyaratkan (contoh: 'admin', 'siswa').
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Pastikan pengguna sudah login
        if (!Auth::check()) {
            // Jika belum login, arahkan ke halaman login
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. Cek apakah role pengguna sesuai dengan role yang disyaratkan
        if ($user->role === $role) {
            // Jika role sesuai, izinkan akses ke halaman
            return $next($request);
        }

        // 3. Jika role tidak sesuai (misalnya siswa mencoba akses admin)
        // Kita arahkan kembali ke dashboard yang sesuai dengan role-nya
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Akses Ditolak: Anda mencoba masuk ke area yang tidak diizinkan.');
        } elseif ($user->role === 'siswa') {
            return redirect()->route('siswa.dashboard')->with('error', 'Akses Ditolak: Anda mencoba masuk ke area yang tidak diizinkan.');
        }

        // Default fallback
        return redirect('/')->with('error', 'Akses Ditolak.');
    }
}
