<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin (Role: admin)
        User::create([
            'name' => 'Admin PPDB',
            'email' => 'admin@ppdb.com', // Gunakan email ini untuk login admin
            'password' => Hash::make('password_admin'), // Password default: 'password'
            'role' => 'admin', // Role wajib 'admin'
        ]);

        // 2. Buat Beberapa Akun Siswa (Role: siswa)
        User::create([
            'name' => 'MbahGondol',
            'email' => 'mbahgondol@ppdb.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        User::create([
            'name' => 'Narzavael',
            'email' => 'narzavael@ppdb.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);
        
        // Pesan konfirmasi di terminal
        $this->command->info('Akun Admin dan Siswa berhasil dibuat: admin@ppdb.com / siswa1@ppdb.com (password: password)');
    }
}
