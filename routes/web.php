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

Route::get('/', [PublicController::class, 'beranda'])->name('beranda');
Route::get('/profil-sekolah', [PublicController::class, 'profil'])->name('profil');

Route::get('/info-jurusan', [PublicController::class, 'infoJurusan'])->name('info.jurusan');


// == GRUP AUTENTIKASI ==
// Menggunakan Controller Group agar lebih rapi
Route::controller(AuthController::class)->group(function () {
    
    // --- Rute untuk Tamu (GUEST) ---
    // Hanya bisa diakses jika BELUM LOGIN
    Route::middleware('guest')->group(function () {
        // Rute untuk menampilkan form register
        Route::get('/register', 'showRegisterForm')->name('register');
        // Rute untuk memproses data dari form register
        Route::post('/register', 'register');

        // Rute untuk menampilkan form login
        Route::get('/login', 'showLoginForm')->name('login');
        // Rute untuk memproses data dari form login
        Route::post('/login', 'login');
    });

    // --- Rute untuk yang SUDAH LOGIN ---
    // Hanya bisa diakses jika SUDAH LOGIN
    Route::middleware('auth')->group(function () {
        // Rute untuk logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

        // ========================
        // --- GRUP KHUSUS ADMIN ---
        // ========================
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            
            // Rute untuk CRUD Jurusan
            Route::resource('jurusan', JurusanController::class);

            Route::resource('gelombang', App\Http\Controllers\Admin\GelombangController::class);
            Route::resource('promo', App\Http\Controllers\Admin\PromoController::class);
            
            // Rute Manajemen Kuota
            Route::get('kuota', [KuotaController::class, 'index'])->name('kuota.index');
            Route::get('kuota/create', [KuotaController::class, 'create'])->name('kuota.create');
            Route::post('kuota', [KuotaController::class, 'store'])->name('kuota.store');
            Route::get('kuota/{id}/edit', [KuotaController::class, 'edit'])->name('kuota.edit');
            Route::put('kuota/{id}', [KuotaController::class, 'update'])->name('kuota.update');

            // Rute untuk Manajemen Tipe Biaya
            Route::resource('jenis-biaya', JenisBiayaController::class);

            // Rute untuk Manajemen Harga (CRUD Lengkap)
            Route::resource('biaya', BiayaController::class);

            // Rute untuk Verifikasi Pendaftar
            // 1. Halaman utama (daftar siswa)
            Route::get('verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
            // 2. Halaman detail per siswa
            Route::get('verifikasi/{id}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
            // 3. Aksi untuk mengubah status
            Route::post('verifikasi/update-status/{id}', [VerifikasiController::class, 'updateStatus'])->name('verifikasi.updateStatus');

            // Rute untuk Laporan / Manajemen Semua Pendaftar
            Route::get('pendaftar', [PendaftarController::class, 'index'])->name('pendaftar.index');
            
        });

        // =========================
        // --- GRUP KHUSUS SISWA ---
        // =========================
        Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
            
            // Rute untuk Dashboard Siswa (yang "pintar")
            Route::get('dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

            Route::get('biodata', [SiswaDashboardController::class, 'lihatBiodata'])->name('biodata');

            Route::get('cetak-bukti', [SiswaDashboardController::class, 'cetakBukti'])->name('cetak.bukti');
            
            // Rute untuk menampilkan form pendaftaran
            Route::get('pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
            
            // Rute untuk MENYIMPAN form pendaftaran
            Route::post('pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
            
            // Rute untuk Halaman Upload Dokumen
            Route::get('dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
            
            // Rute untuk MENYIMPAN file dokumen
            Route::post('dokumen', [DokumenController::class, 'store'])->name('dokumen.store');

            // Rute untuk MENGHAPUS file dokumen
            Route::delete('dokumen/{id}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');

            // Rute untuk Halaman Pembayaran
            Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
            
            // Rute untuk MENYIMPAN bukti pembayaran
            Route::post('pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');

            // Rute untuk MENGHAPUS bukti pembayaran
            Route::delete('pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
        });
    });
});