<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisBiaya;
use App\Models\JurusanTipeKelas;
use App\Models\BiayaPerJurusanTipeKelas;

class BiayaPerJurusanTipeKelasSeeder extends Seeder
{
    public function run(): void
    {

        // 2. Ambil Data Master Biaya (Pastikan JenisBiayaSeeder sudah jalan duluan)
        $biayaFormulir = JenisBiaya::where('nama_biaya', 'Formulir Pendaftaran')->first();
        $biayaGedung = JenisBiaya::where('nama_biaya', 'Uang Gedung')->first();
        $biayaSPP = JenisBiaya::where('nama_biaya', 'SPP Bulan Pertama')->first();
        $biayaSeragam = JenisBiaya::where('nama_biaya', 'Seragam Sekolah')->first();

        // 3. Ambil SEMUA Kombinasi Jurusan & Kelas yang ada
        // (Ini akan mengambil RPL-Reguler, TKR-Unggulan, dll yang sudah dibuat di JurusanTipeKelasSeeder)
        $semua_kombinasi = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->get();

        // 4. Loop untuk memberi harga pada setiap kombinasi
        foreach ($semua_kombinasi as $kombinasi) {
            
            $nama_jurusan = $kombinasi->jurusan->kode_jurusan; // RPL, TKR, dst
            $tipe_kelas = $kombinasi->tipeKelas->nama_tipe_kelas; // Reguler, Unggulan

            // --- LOGIKA PENENTUAN HARGA ---
            
            // A. Harga Dasar
            $harga_gedung = 4000000; 
            $harga_spp = 250000;
            $harga_seragam = 600000;

            // B. Jika Jurusan Teknik Berat (TKR/TPM), tambah biaya praktik
            if (in_array($nama_jurusan, ['TKR', 'TPM'])) {
                $harga_gedung += 1500000; // Tambah 1.5 Juta
                $harga_spp += 50000;      // Tambah 50rb
            }

            // C. Jika Kelas Unggulan, tambah biaya fasilitas
            if ($tipe_kelas == 'Unggulan') {
                $harga_gedung += 2000000; // Tambah 2 Juta
                $harga_spp += 150000;     // Tambah 150rb
                $harga_seragam += 200000; // Seragam lebih bagus
            }

            // --- SIMPAN KE DATABASE ---

            // 1. Biaya Formulir (Sama rata semua jurusan)
            if ($biayaFormulir) {
                BiayaPerJurusanTipeKelas::create([
                    'jenis_biaya_id' => $biayaFormulir->id,
                    'jurusan_tipe_kelas_id' => $kombinasi->id,
                    'nominal' => 150000,
                    'catatan' => 'Wajib lunas di awal'
                ]);
            }

            // 2. Uang Gedung (Sesuai perhitungan di atas)
            if ($biayaGedung) {
                BiayaPerJurusanTipeKelas::create([
                    'jenis_biaya_id' => $biayaGedung->id,
                    'jurusan_tipe_kelas_id' => $kombinasi->id,
                    'nominal' => $harga_gedung,
                    'catatan' => 'Bisa dicicil 3x'
                ]);
            }

            // 3. SPP (Sesuai perhitungan)
            if ($biayaSPP) {
                BiayaPerJurusanTipeKelas::create([
                    'jenis_biaya_id' => $biayaSPP->id,
                    'jurusan_tipe_kelas_id' => $kombinasi->id,
                    'nominal' => $harga_spp,
                    'catatan' => 'SPP bulan Juli'
                ]);
            }

            // 4. Seragam (Sesuai perhitungan)
            if ($biayaSeragam) {
                BiayaPerJurusanTipeKelas::create([
                    'jenis_biaya_id' => $biayaSeragam->id,
                    'jurusan_tipe_kelas_id' => $kombinasi->id,
                    'nominal' => $harga_seragam,
                    'catatan' => '3 Setel Kain (Putih Abu, Pramuka, Kejuruan)'
                ]);
            }
        }
    }
}