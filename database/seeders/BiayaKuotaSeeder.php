<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisBiaya;
use App\Models\JurusanTipeKelas;
use App\Models\BiayaPerJurusanTipeKelas;
use App\Models\Gelombang;
use App\Models\KuotaGelombang; 
use Illuminate\Support\Facades\DB;

class BiayaKuotaSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        // Pastikan tabel relasi yang akan diisi dikosongkan terlebih dahulu
        DB::table('biaya_per_jurusan_tipe_kelas')->truncate();
        DB::table('kuota_gelombang_jurusan')->truncate();

        // --- 1. Ambil Data Master yang dibutuhkan & Cek Ketersediaan ---

        // ID Jenis Biaya
        $uangPangkalId = JenisBiaya::where('nama_biaya', 'Uang Pangkal/Gedung')->value('id');
        $sppId = JenisBiaya::where('nama_biaya', 'SPP Bulanan')->value('id');
        $seragamId = JenisBiaya::where('nama_biaya', 'Seragam')->value('id');

        // Pengecekan Kritis: Pastikan Jenis Biaya ada
        if (!$uangPangkalId || !$sppId || !$seragamId) {
            $this->command->error('Gagal: Salah satu (atau lebih) Jenis Biaya tidak ditemukan. Pastikan KonfigurasiAwalSeeder berhasil membuat: "Uang Pangkal/Gedung", "SPP Bulanan", dan "Seragam".');
            return;
        }

        // ID Gelombang
        $gelombang1 = Gelombang::where('nama_gelombang', 'Gelombang 1')->first();
        
        // Semua kombinasi Jurusan dan Tipe Kelas (dari RelasiKelasSeeder)
        $semuaKombinasiKelas = JurusanTipeKelas::with('jurusan', 'tipeKelas')->get();

        if ($semuaKombinasiKelas->isEmpty() || !$gelombang1) {
            $this->command->error('Gagal: Pastikan RelasiKelasSeeder dan GelombangPromoSeeder sudah dijalankan dan menghasilkan data.');
            return;
        }


        // --- 2. SEED DATA BIAYA (Nominal) per Kombinasi Kelas ---
        $this->command->info('Memulai seeding data biaya...');
        foreach ($semuaKombinasiKelas as $kombinasi) {
            $baseBiayaPangkal = 3000000; 

            // Tentukan apakah ini kelas Unggulan (dengan pengecekan null yang aman)
            $isUnggulan = false;
            // Perbaikan: Lakukan pengecekan properti HANYA jika relasi tipeKelas tidak null
            if ($kombinasi->tipeKelas && $kombinasi->tipeKelas->nama_tipe_kelas == 'Unggulan') {
                $isUnggulan = true;
            }

            if ($isUnggulan) {
                // Kelas Unggulan harganya lebih tinggi
                $nominalPangkal = $baseBiayaPangkal + 1500000; 
                $nominalSpp = 450000;
            } else {
                // Kelas Reguler, Tipe Kelas lain, atau Tipe Kelas tidak ditemukan (null)
                $nominalPangkal = $baseBiayaPangkal; 
                $nominalSpp = 350000;
                
                if (!$kombinasi->tipeKelas) {
                     $this->command->warn("Peringatan: Tipe Kelas tidak ditemukan (null) untuk kombinasi ID: {$kombinasi->id}. Menggunakan harga Reguler.");
                }
            }

            // A. Uang Pangkal/Gedung
            BiayaPerJurusanTipeKelas::create([
                'jenis_biaya_id' => $uangPangkalId, 
                'jurusan_tipe_kelas_id' => $kombinasi->id, 
                'nominal' => $nominalPangkal,
                // Gunakan operator null-coalescing (??) untuk tampilan 'catatan' yang aman
                'catatan' => 'Uang Pangkal untuk ' . ($kombinasi->jurusan->nama_jurusan ?? 'Jurusan Tidak Dikenal') . ' Kelas ' . ($kombinasi->tipeKelas->nama_tipe_kelas ?? 'Tipe Tidak Dikenal'),
            ]);

            // B. SPP Bulanan
            BiayaPerJurusanTipeKelas::create([
                'jenis_biaya_id' => $sppId,
                'jurusan_tipe_kelas_id' => $kombinasi->id,
                'nominal' => $nominalSpp,
            ]);

            // C. Seragam (Harga seragam sama untuk semua)
            BiayaPerJurusanTipeKelas::create([
                'jenis_biaya_id' => $seragamId,
                'jurusan_tipe_kelas_id' => $kombinasi->id,
                'nominal' => 750000,
            ]);
        }
        $this->command->info('Data Biaya per Kelas/Jurusan berhasil di-seed.');


        // --- 3. SEED DATA KUOTA GELOMBANG ---
        $this->command->info('Memulai seeding data kuota gelombang...');
        // Kita alokasikan 50% dari kuota total kelas ke Gelombang 1
        foreach ($semuaKombinasiKelas as $kombinasi) {
            
            // Asumsi: 'kuota_kelas' adalah properti yang tersedia di model JurusanTipeKelas
            $kuotaKelas = $kombinasi->kuota_kelas ?? 0;

            if ($kuotaKelas === 0) {
                 $this->command->warn("Peringatan: Kuota Kelas (kuota_kelas) tidak ditemukan/nol untuk kombinasi ID: {$kombinasi->id}. Kuota Gelombang diatur ke 0.");
            }
            
            $kuotaGelombang1 = floor($kuotaKelas * 0.5); 

            KuotaGelombang::create([
                'gelombang_id' => $gelombang1->id,
                'jurusan_tipe_kelas_id' => $kombinasi->id,
                'kuota_gelombang' => $kuotaGelombang1, // Kuota penerimaan di gelombang ini
            ]);
        }
        $this->command->info('Data Kuota Gelombang berhasil di-seed.');
    }
}
