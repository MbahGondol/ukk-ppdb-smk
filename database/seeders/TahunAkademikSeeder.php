<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TahunAkademik; // <-- 1. IMPORT MODEL

class TahunAkademikSeeder extends Seeder
{
    public function run(): void
    {
        // 2. BUAT RESEPNYA
        TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'aktif' => true // Ini adalah tahun ajaran yang sedang berjalan
        ]);
        
        TahunAkademik::create([
            'tahun_ajaran' => '2024/2025',
            'aktif' => false
        ]);
    }
}