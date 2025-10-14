<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Jurusan;
use App\Models\TipeKelas;
use App\Models\JurusanTipeKelas;

class RelasiKelasSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        DB::table('jurusan_tipe_kelas')->truncate();

        // Ambil ID yang sudah di-seed
        $jurusanRPL = Jurusan::where('kode_jurusan', 'RPL')->first()->id;
        $jurusanAKL = Jurusan::where('kode_jurusan', 'AKL')->first()->id;
        $jurusanPMM = Jurusan::where('kode_jurusan', 'PMM')->first()->id;

        $kelasReguler = TipeKelas::where('nama_tipe_kelas', 'Reguler')->first()->id;
        $kelasUnggulan = TipeKelas::where('nama_tipe_kelas', 'Unggulan')->first()->id;

        $dataRelasi = [
            // FIX: Mengganti 'kuota_total' menjadi 'kuota_kelas' agar sesuai dengan migration
            
            // RPL - Reguler
            ['jurusan_id' => $jurusanRPL, 'tipe_kelas_id' => $kelasReguler, 'kuota_kelas' => 60],
            // RPL - Unggulan
            ['jurusan_id' => $jurusanRPL, 'tipe_kelas_id' => $kelasUnggulan, 'kuota_kelas' => 30],

            // AKL - Reguler
            ['jurusan_id' => $jurusanAKL, 'tipe_kelas_id' => $kelasReguler, 'kuota_kelas' => 60],
            // AKL - Unggulan
            ['jurusan_id' => $jurusanAKL, 'tipe_kelas_id' => $kelasUnggulan, 'kuota_kelas' => 30],

            // Pemasaran - Reguler
            ['jurusan_id' => $jurusanPMM, 'tipe_kelas_id' => $kelasReguler, 'kuota_kelas' => 30],
        ];

        JurusanTipeKelas::insert($dataRelasi);

        $this->command->info('Data Relasi Jurusan dan Tipe Kelas berhasil di-seed.');
    }
}