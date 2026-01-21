<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KuotaController;
use App\Http\Controllers\Admin\JenisBiayaController;
use App\Http\Controllers\Admin\BiayaController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\PendaftaranController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\Siswa\DokumenController;
use App\Http\Controllers\Siswa\PembayaranController;
use App\Http\Controllers\Admin\PendaftarController;
use App\Http\Controllers\PublicController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', [PublicController::class, 'beranda'])->name('beranda');
Route::get('/profil-sekolah', [PublicController::class, 'profil'])->name('profil');
Route::get('/info-jurusan', [PublicController::class, 'infoJurusan'])->name('info.jurusan');


// == GRUP AUTENTIKASI ==
Route::controller(AuthController::class)->group(function () {
    
    // --- TAMU (Belum Login) ---
    Route::middleware('guest')->group(function () {
        Route::get('/register', 'showRegisterForm')->name('register');
        Route::post('/register', 'register');
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->middleware('throttle:5,1');

        // --- RUTE LUPA PASSWORD ---
        // 1. Form isi email
        Route::get('/forgot-password', 'showForgotPasswordForm')->name('password.request');
        
        // 2. Proses kirim link reset ke email
        Route::post('/forgot-password', 'sendResetLink')->name('password.email');
        
        // 3. Form ganti password baru (dari link email)
        Route::get('/reset-password/{token}', 'showResetPasswordForm')->name('password.reset');
        
        // 4. Proses update password baru ke database
        Route::post('/reset-password', 'resetPassword')->name('password.update');
        
    });

    // --- USER LOGIN (Siswa & Admin) ---
    Route::middleware('auth')->group(function () {
        
        // --- LOGIKA VERIFIKASI EMAIL (Langsung di sini, jangan di-nest lagi) ---
        Route::get('/email/verify', function () {
            return view('auth.verify-email');
        })->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();
            return redirect()->route('dashboard');
        })->middleware(['signed'])->name('verification.verify');

        Route::post('/email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('message', 'Link verifikasi telah dikirim ulang!');
        })->middleware(['throttle:6,1'])->name('verification.send');
        // ---------------------------------------------------------------------

        Route::post('/logout', 'logout')->name('logout');
        Route::get('/dashboard', 'dashboard')->middleware(['verified'])->name('dashboard');

        // --- GRUP KHUSUS ADMIN ---
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::resource('jurusan', JurusanController::class);
            Route::resource('gelombang', App\Http\Controllers\Admin\GelombangController::class);
            Route::resource('promo', App\Http\Controllers\Admin\PromoController::class);
            
            Route::get('kuota', [KuotaController::class, 'index'])->name('kuota.index');
            Route::get('kuota/create', [KuotaController::class, 'create'])->name('kuota.create');
            Route::post('kuota', [KuotaController::class, 'store'])->name('kuota.store');
            Route::get('kuota/{id}/edit', [KuotaController::class, 'edit'])->name('kuota.edit');
            Route::put('kuota/{id}', [KuotaController::class, 'update'])->name('kuota.update');

            Route::resource('jenis-biaya', JenisBiayaController::class);
            Route::resource('biaya', BiayaController::class);

            // VERIFIKASI SISWA
            Route::get('verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
            Route::get('verifikasi/{id}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
            Route::post('verifikasi/update-status/{id}', [VerifikasiController::class, 'updateStatus'])->name('verifikasi.updateStatus');
            Route::post('pembayaran/verifikasi/{id}', [VerifikasiController::class, 'verifikasiPembayaran'])->name('verifikasi.pembayaran');
            
            Route::get('pendaftar', [PendaftarController::class, 'index'])->name('pendaftar.index');
            Route::get('dokumen/{id}/preview', [DokumenController::class, 'show'])->name('dokumen.show'); 
        });

        // --- GRUP KHUSUS SISWA ---
        Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
            Route::get('dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
            Route::get('biodata', [SiswaDashboardController::class, 'lihatBiodata'])->name('biodata');
            Route::get('cetak-bukti', [SiswaDashboardController::class, 'cetakBukti'])->name('cetak.bukti');
            
            Route::get('pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
            Route::post('pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
            Route::put('pendaftaran/{id}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');

            Route::get('dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
            Route::post('dokumen', [DokumenController::class, 'store'])->name('dokumen.store');
            Route::get('dokumen/{id}/preview', [DokumenController::class, 'show'])->name('dokumen.show'); 
            Route::delete('dokumen/{id}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');

            Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
            Route::post('pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
            Route::delete('pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
        });
    });
});