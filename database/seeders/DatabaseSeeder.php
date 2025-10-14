<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. NONAKTIFKAN FOREIGN KEY CHECKS ---
        // Ini mengatasi error 1701 saat menggunakan truncate() di seeder.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            // Dipanggil dalam urutan yang memastikan dependensi terpenuhi
            TahunAkademikSeeder::class,
            UserSeeder::class,
            KonfigurasiAwalSeeder::class,
            RelasiKelasSeeder::class,
            GelombangPromoSeeder::class,
            BiayaKuotaSeeder::class,
        ]);

        // --- 2. AKTIFKAN KEMBALI FOREIGN KEY CHECKS ---
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}