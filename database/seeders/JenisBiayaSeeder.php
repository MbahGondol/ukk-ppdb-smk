<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisBiaya; // <-- 1. IMPORT MODEL (Mandor)

class JenisBiayaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. Kita buat resepnya
        
        JenisBiaya::create([
            'nama_biaya' => 'Formulir Pendaftaran',
            'keterangan' => 'Biaya administrasi untuk pendaftaran awal.'
        ]);

        JenisBiaya::create([
            'nama_biaya' => 'Uang Gedung',
            'keterangan' => 'Biaya pembangunan dan pemeliharaan fasilitas.'
        ]);

        JenisBiaya::create([
            'nama_biaya' => 'SPP Bulan Pertama',
            'keterangan' => 'Biaya SPP untuk bulan Juli.'
        ]);

        JenisBiaya::create([
            'nama_biaya' => 'Seragam Sekolah',
            'keterangan' => 'Biaya untuk 3 setel seragam (OSIS, Pramuka, Jurusan).'
        ]);
    }
}