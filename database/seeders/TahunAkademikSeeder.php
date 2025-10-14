<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAkademik;
use Illuminate\Support\Facades\DB;

class TahunAkademikSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        // Truncate dilakukan melalui DatabaseSeeder untuk menghindari FK error
        DB::table('tahun_akademik')->truncate(); 
        
        // Ambil tahun saat ini
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $tahunAjaranAktif = "{$currentYear}/{$nextYear}";

        // Data Tahun Akademik Aktif
        TahunAkademik::create([
            'tahun_ajaran' => $tahunAjaranAktif,
            'aktif' => true, // FIX: Mengganti 'is_aktif' menjadi 'aktif'
        ]);

        // Tambahkan data dummy untuk tahun sebelumnya
        TahunAkademik::create([
            'tahun_ajaran' => ($currentYear - 1) . '/' . $currentYear,
            'aktif' => false, // FIX: Mengganti 'is_aktif' menjadi 'aktif'
        ]);

        $this->command->info('Data Tahun Akademik berhasil di-seed.');
    }
}
