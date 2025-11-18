<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <-- 1. IMPORT MODEL USER
use Illuminate\Support\Facades\Hash; // <-- 2. IMPORT HASH (untuk enkripsi password)

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 3. BUAT RESEPNYA
        User::create([
            'name' => 'Admin PPDB',
            'email' => 'admin@ppdb.com',
            'password' => Hash::make('password'), // 'password' adalah passwordnya
            'role' => 'admin', // Sesuai migrasi Anda (File A)
            'aktif' => true,
            'email_verified_at' => now() // Anggap email admin sudah terverifikasi
        ]);
    }
}