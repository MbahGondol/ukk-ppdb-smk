<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // <-- 1. IMPORT MODEL USER
use Illuminate\Support\Facades\Hash; // <-- 2. IMPORT HASH (untuk enkripsi)
use Illuminate\Support\Facades\Auth; // <-- 3. IMPORT AUTH (Satpam Laravel)
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    // --- FUNGSI UNTUK MENAMPILKAN VIEW ---

    /**
     * Menampilkan halaman/form register
     */
    public function showRegisterForm()
    {
        return view('auth.register'); // Mengarah ke resources/views/auth/register.blade.php
    }

    /**
     * Menampilkan halaman/form login
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Mengarah ke resources/views/auth/login.blade.php
    }


    // --- FUNGSI UNTUK MEMPROSES DATA (LOGIKA) ---

    /**
     * Memproses data dari form register
     */
    public function register(Request $request)
    {
        // 1. Validasi data (Aturan)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Buat User baru (jika lolos validasi)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa', // Sesuai migrasi Anda (File A)
            'aktif' => true,
        ]);

        // 3. Login-kan user yang baru daftar
        Auth::login($user);

        // 4. Arahkan ke dashboard
        return redirect()->route('dashboard');
    }


    /**
     * Memproses data dari form login
     */
    public function login(Request $request)
    {
        // 1. Validasi data (hanya email & password)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba lakukan login (Ini "Sihir" Laravel)
        // Auth::attempt() akan otomatis:
        // - Cari user berdasarkan 'email'
        // - Enkripsi 'password' dari form
        // - Bandingkan dengan password di database
        // - Jika cocok, dia akan membuat "Session" (Status Login)
        if (Auth::attempt($credentials)) {
            // 3. Jika berhasil, perbarui session (keamanan)
            $request->session()->regenerate();

            // 4. Arahkan ke dashboard
            return redirect()->route('dashboard');
        }

        // 5. Jika gagal (email atau password salah)
        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email'); // Kembalikan ke form, tapi hanya isi email (password jangan)
    }


    /**
     * Memproses logout
     */
    public function logout(Request $request)
    {
        Auth::logout(); // 1. Suruh Satpam hapus data login

        $request->session()->invalidate(); // 2. Hancurkan session
        $request->session()->regenerateToken(); // 3. Buat token baru (keamanan)

        return redirect('/'); // 4. Arahkan ke halaman utama
    }


    /**
     * Menampilkan dashboard (Pemandu)
     * Ini adalah halaman pertama setelah login
     */
    // ...
    public function dashboard()
    {
        $role = Auth::user()->role;

        if ($role == 'admin') {
            return view('admin.dashboard'); 
        
        } 
        elseif ($role == 'siswa') {
            
            $logoutForm = '
                <form action="' . route('logout') . '" method="POST">
                    ' . csrf_field() . '
                    <button type="submit">Logout</button>
                </form>
            ';
            return '<h3>Ini Dashboard SISWA.</h3>' . $logoutForm; 
        }

        return 'Role tidak dikenal.';
    }
}