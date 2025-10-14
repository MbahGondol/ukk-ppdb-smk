<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promo;
use App\Models\Gelombang; // DIUBAH: Menggunakan Model Gelombang (sesuai nama tabel)
use Illuminate\Support\Facades\DB;

class GelombangPromoSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        // FIX: Truncate tabel 'promo'
        DB::table('promo')->truncate();
        
        // FIX UTAMA: Mengganti 'gelombang_pendaftaran' menjadi 'gelombang'
        DB::table('gelombang')->truncate(); 

        // 1. Data Promo
        $promoAwal = Promo::create([
            'nama_promo' => 'Diskon Pendaftaran Awal',
            'potongan' => 200000.00, // Rp 200.000
            'deskripsi' => 'Potongan biaya pendaftaran untuk 100 pendaftar pertama.',
            'aktif' => true,
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addMonths(2), 
        ]);
        
        $promoNormal = Promo::create([
            'nama_promo' => 'Promo Normal',
            'potongan' => 0.00, // Tidak ada potongan
            'deskripsi' => 'Promo standar tanpa potongan biaya.',
            'aktif' => true,
        ]);


        // 2. Data Gelombang Pendaftaran
        // Gelombang 1 menggunakan diskon
        // DIUBAH: Menggunakan Model Gelombang
        Gelombang::create([
            'nama_gelombang' => 'Gelombang 1',
            'tanggal_mulai' => now()->subDays(7), // Dimulai 7 hari lalu
            'tanggal_selesai' => now()->addMonths(1), // Selesai 1 bulan lagi
            'promo_id' => $promoAwal->id,
        ]);

        // Gelombang 2 tidak menggunakan diskon
        // DIUBAH: Menggunakan Model Gelombang
        Gelombang::create([
            'nama_gelombang' => 'Gelombang 2',
            'tanggal_mulai' => now()->addMonths(1)->addDays(1), // Dimulai setelah gelombang 1 berakhir
            'tanggal_selesai' => now()->addMonths(2),
            'promo_id' => $promoNormal->id,
        ]);

        $this->command->info('Data Promo dan Gelombang Pendaftaran berhasil di-seed.');
    }
}