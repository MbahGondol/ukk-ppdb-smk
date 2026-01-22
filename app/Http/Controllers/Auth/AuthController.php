<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rules;
use App\Models\CalonSiswa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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


    public function register(Request $request)
    {
        // 1. Validasi KETAT (Password harus Rumit)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'g-recaptcha-response' => 'required|captcha'
        ]);

        // 2. Buat User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Assign Role
        $user->assignRole('siswa');

        // 4. Trigger Event Email
        event(new Registered($user));

        // 5. Login
        Auth::login($user);

        // 6. Redirect ke Verifikasi
        return redirect()->route('verification.notice');
    }

    public function login(Request $request)
    {
        // 1. Validasi SEDERHANA (Cuma cek isian ada atau tidak)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'], 
            'g-recaptcha-response' => 'required|captcha'
        ]);

        unset($credentials['g-recaptcha-response']);

        // 2. Cek Login & Remember Me
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // 3. Jika Gagal
        return back()->withErrors([
            'email' => 'Email atau Password salah.',
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

    // ================================
    // --- LOGIKA RESET PASSWORD ---
    // ================================

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Kirim link reset (karena pakai MAIL_MAILER=log, link akan muncul di laravel.log)
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}