<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User dan SIMPAN ke variabel
        $user = User::create([
            'name' => 'Admin PPDB',
            'email' => 'admin@ppdb.com',
            'password' => Hash::make('password'),
            'aktif' => true,
            'email_verified_at' => now()
        ]);

        // 2. Assign Role
        $user->assignRole('admin'); 
    }
}