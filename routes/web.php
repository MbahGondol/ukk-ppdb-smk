<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Siswa\SiswaController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda.
|
*/

// --- Rute Halaman Depan ---
Route::get('/', function () {
    return view('welcome'); // Ini bisa Anda ganti nanti dengan landing page PPDB
});


// --- Rute Otentikasi (Login, Register, & Logout) ---
Route::middleware('guest')->group(function () {
    // 1. Menampilkan Formulir Login (GET request)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    
    // 2. Menangani Proses Login (POST request)
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // 3. Menampilkan Formulir Registrasi (GET request)
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register'); // <<< BARU
    
    // 4. Menangani Proses Registrasi (POST request)
    Route::post('/register', [AuthController::class, 'register'])->name('register.post'); // <<< BARU
});

// 5. Proses Logout (POST request)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// --- Rute Halaman Admin (Memerlukan Login) ---
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// --- Rute Halaman Siswa (Memerlukan Login) ---
Route::get('/siswa/dashboard', [SiswaController::class, 'index'])->name('siswa.dashboard');

// --- Rute Halaman Admin (Hanya bisa diakses oleh role 'admin') ---
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Rute ini hanya akan diakses jika pengguna sudah login DAN memiliki role 'admin'
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Tambahkan semua rute manajemen (data siswa, pengaturan, dll.) di sini
});


// --- Rute Halaman Siswa (Hanya bisa diakses oleh role 'siswa') ---
Route::middleware(['auth', 'role:siswa'])->group(function () {
    // Rute ini hanya akan diakses jika pengguna sudah login DAN memiliki role 'siswa'
    Route::get('/siswa/dashboard', [SiswaController::class, 'index'])->name('siswa.dashboard');
    // Tambahkan semua rute pendaftaran (isi formulir, cek status, dll.) di sini
});
