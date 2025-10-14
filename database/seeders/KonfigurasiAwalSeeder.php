<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Jurusan;
use App\Models\TipeKelas;
use App\Models\JenisBiaya;

class KonfigurasiAwalSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        // Truncate semua tabel konfigurasi
        DB::table('jurusan')->truncate();
        DB::table('tipe_kelas')->truncate();
        DB::table('jenis_biaya')->truncate();

        // 1. Data Jurusan
        $jurusanData = [
            ['nama_jurusan' => 'Rekayasa Perangkat Lunak (RPL)', 'kode_jurusan' => 'RPL'],
            ['nama_jurusan' => 'Akuntansi Keuangan Lembaga (AKL)', 'kode_jurusan' => 'AKL'],
            ['nama_jurusan' => 'Pemasaran', 'kode_jurusan' => 'PMM'],
        ];
        Jurusan::insert($jurusanData);

        // 2. Data Tipe Kelas
        $tipeKelasData = [
            ['nama_tipe_kelas' => 'Reguler', 'keterangan' => 'Kelas standar tanpa fasilitas tambahan.'],
            ['nama_tipe_kelas' => 'Unggulan', 'keterangan' => 'Kelas dengan fasilitas ekstra (AC, kuota terbatas).'],
        ];
        TipeKelas::insert($tipeKelasData);

        // 3. Data Jenis Biaya (Dibutuhkan untuk Pembayaran)
        $jenisBiayaData = [
            // DIGANTI: Sebelumnya 'Uang Pangkal Pendaftaran'
            ['nama_biaya' => 'Uang Pangkal/Gedung', 'keterangan' => 'Biaya wajib dibayar sekali di awal (uang gedung/pangkal).'],
            
            ['nama_biaya' => 'SPP Bulanan', 'keterangan' => 'Sumbangan Pembinaan Pendidikan bulanan.'],
            
            // DIGANTI: Sebelumnya 'Biaya Seragam'
            ['nama_biaya' => 'Seragam', 'keterangan' => 'Biaya pembelian paket seragam sekolah.'],
        ];
        JenisBiaya::insert($jenisBiayaData);

        $this->command->info('Data Jurusan, Tipe Kelas, dan Jenis Biaya berhasil di-seed.');
    }
}