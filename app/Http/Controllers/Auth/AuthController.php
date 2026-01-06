<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rules;
use App\Models\CalonSiswa;

class AuthController extends Controller
{
    // --- FUNGSI UNTUK MENAMPILKAN VIEW ---

    public function showRegisterForm()
    {
        return view('auth.register'); 
    }

    public function showLoginForm()
    {
        return view('auth.login'); 
    }


    // --- FUNGSI UNTUK MEMPROSES DATA (LOGIKA) ---

    public function register(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Buat User 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Assign Role Spatie (Lakukan SETELAH user dibuat)
        $user->assignRole('siswa');

        // 4. Login & Redirect
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Dashboard Router
     * Mengarahkan user berdasarkan Role Spatie
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Cek Role menggunakan SPATIE 
        if ($user->hasRole('admin')) {
            
            // Logika Admin Dashboard
            $total_pendaftar = CalonSiswa::count();
            $perlu_verifikasi = CalonSiswa::where('status_pendaftaran', 'Terdaftar')->count();
            $diterima = CalonSiswa::where('status_pendaftaran', 'Resmi Diterima')->count();
            $ditolak = CalonSiswa::where('status_pendaftaran', 'Ditolak')->count();

            return view('admin.dashboard', compact('total_pendaftar', 'perlu_verifikasi', 'diterima', 'ditolak'));
        
        } elseif ($user->hasRole('siswa')) {
            
            // Redirect Siswa ke Dashboard Siswa
            return redirect()->route('siswa.dashboard');
            
        }

        // Default jika tidak punya role (Safety Net)
        abort(403, 'Akun Anda tidak memiliki peran yang valid.');
    }
}