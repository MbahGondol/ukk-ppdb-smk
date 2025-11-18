<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gelombang; // <-- IMPORT
use App\Models\Promo; // <-- IMPORT

class GelombangSeeder extends Seeder
{
    public function run(): void
    {
        // Cari promo yang baru kita buat
        $promo = Promo::where('nama_promo', 'Diskon Gelombang 1')->first();

        Gelombang::create([
            'nama_gelombang' => 'Gelombang 1 (2025)',
            'tanggal_mulai' => now()->subDays(30), // Mulai 30 hari yang lalu
            'tanggal_selesai' => now()->addDays(30), // Selesai 30 hari lagi
            'promo_id' => $promo ? $promo->id : null
        ]);
    }
}