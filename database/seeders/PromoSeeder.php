<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Promo; // <-- IMPORT

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        Promo::create([
            'nama_promo' => 'Diskon Gelombang 1',
            'potongan' => 50000,
            'aktif' => true,
            'tanggal_mulai' => now()->subDays(30), // Mulai 30 hari yang lalu
            'tanggal_selesai' => now()->addDays(30), // Selesai 30 hari lagi
        ]);
    }
}