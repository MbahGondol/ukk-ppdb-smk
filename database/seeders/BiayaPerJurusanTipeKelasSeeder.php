<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// 1. IMPORT SEMUA MODEL YANG KITA BUTUHKAN
use App\Models\JenisBiaya;
use App\Models\Jurusan;
use App\Models\TipeKelas;
use App\Models\JurusanTipeKelas;
use App\Models\BiayaPerJurusanTipeKelas;

class BiayaPerJurusanTipeKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === LANGKAH 1: CARI SEMUA ID YANG DIPERLUKAN ===
        
        // A. Cari ID dari JenisBiaya (Daftar Menu dari Tugas A)
        $biayaFormulir = JenisBiaya::where('nama_biaya', 'Formulir Pendaftaran')->first();
        $biayaGedung = JenisBiaya::where('nama_biaya', 'Uang Gedung')->first();
        $biayaSPP = JenisBiaya::where('nama_biaya', 'SPP Bulan Pertama')->first();

        // B. Cari ID Jurusan
        $rpl = Jurusan::where('kode_jurusan', 'RPL')->first();
        $tkr = Jurusan::where('kode_jurusan', 'TKR')->first();

        // C. Cari ID Tipe Kelas
        $reguler = TipeKelas::where('nama_tipe_kelas', 'Reguler')->first();
        $unggulan = TipeKelas::where('nama_tipe_kelas', 'Unggulan')->first();

        // D. Cari ID "Gabungan" dari tabel 'jurusan_tipe_kelas'
        // Kita butuh ID 'RPL-Reguler', 'RPL-Unggulan', 'TKJ-Reguler'
        $rplReguler_ID = JurusanTipeKelas::where('jurusan_id', $rpl->id)
                                         ->where('tipe_kelas_id', $reguler->id)
                                         ->first()->id;

        // === LANGKAH 2: BUAT RESEP DAFTAR HARGANYA ===

        // --- HARGA UNTUK JURUSAN RPL REGULER ---
        BiayaPerJurusanTipeKelas::create([
            'jenis_biaya_id' => $biayaFormulir->id,
            'jurusan_tipe_kelas_id' => $rplReguler_ID,
            'nominal' => 150000,
            'catatan' => 'Berlaku untuk semua jurusan'
        ]);
        
        BiayaPerJurusanTipeKelas::create([
            'jenis_biaya_id' => $biayaGedung->id,
            'jurusan_tipe_kelas_id' => $rplReguler_ID,
            'nominal' => 5000000,
            'catatan' => 'Uang Gedung RPL Reguler'
        ]);

        BiayaPerJurusanTipeKelas::create([
            'jenis_biaya_id' => $biayaSPP->id,
            'jurusan_tipe_kelas_id' => $rplReguler_ID,
            'nominal' => 300000,
            'catatan' => 'SPP per bulan RPL Reguler'
        ]);
    }
}