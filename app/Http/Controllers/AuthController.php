<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Pastikan model User sudah diimport

class AuthController extends Controller
{
    // ... [showLoginForm tetap sama] ...
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menampilkan halaman formulir registrasi.
     */
    public function showRegistrationForm() // <<< BARU
    {
        return view('auth.register');
    }

    /**
     * Menangani proses POST request untuk registrasi siswa baru.
     */
    public function register(Request $request) // <<< BARU
    {
        // 1. Validasi Input Registrasi
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Buat User Baru (Role Default: siswa)
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'siswa', // Default role untuk pendaftar baru adalah 'siswa'
        ]);

        // 3. Langsung Login setelah Registrasi berhasil
        Auth::login($user);

        Log::info('Pengguna siswa baru berhasil registrasi dan login.', ['email' => $user->email]);

        // Arahkan ke dashboard siswa
        return redirect()->route('siswa.dashboard');
    }


    /**
     * Menangani proses POST request untuk login (Admin & Siswa).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user(); // Ambil data user yang sedang login

            Log::info('Pengguna berhasil login.', ['email' => $user->email, 'role' => $user->role]);

            // 3. Logika Pengalihan Berdasarkan ROLE
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'siswa') {
                return redirect()->route('siswa.dashboard');
            }
            
            // Pengalihan default jika role tidak terdefinisi
            return redirect('/'); 
        }

        Log::warning('Percobaan login gagal.', ['email' => $request->email]);
        
        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan tidak valid.',
        ])->onlyInput('email');
    }

    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Pengguna berhasil logout.');
        
        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }
}