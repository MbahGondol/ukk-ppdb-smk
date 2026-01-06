<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil semua pabrik kita secara BERURUTAN
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            TahunAkademikSeeder::class,
            PromoSeeder::class,
            GelombangSeeder::class,
            JurusanSeeder::class,
            TipeKelasSeeder::class,
            JurusanTipeKelasSeeder::class, 
            JenisBiayaSeeder::class,
            BiayaPerJurusanTipeKelasSeeder::class, 
            SiswaSeeder::class,
        ]);
    }
}