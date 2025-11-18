<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Jurusan;
use App\Models\TipeKelas;
use App\Models\JurusanTipeKelas;

class JurusanTipeKelasSeeder extends Seeder
{
    public function run(): void
    {       
        // === 1. CARI SEMUA ID YANG DIPERLUKAN ===
        
        // Cari ID Jurusan
        $rpl = Jurusan::where('kode_jurusan', 'RPL')->first();
        $tkr = Jurusan::where('kode_jurusan', 'TKR')->first();
        $tpm = Jurusan::where('kode_jurusan', 'TPM')->first();
        $titl = Jurusan::where('kode_jurusan', 'TITL')->first();
        $tei = Jurusan::where('kode_jurusan', 'TEI')->first();

        // Cari ID Tipe Kelas
        $reguler = TipeKelas::where('nama_tipe_kelas', 'Reguler')->first();
        $unggulan = TipeKelas::where('nama_tipe_kelas', 'Unggulan')->first();
        
        // Kapasitas per kelas
        $kapasitas = 36;

        // === 2. BUAT RESEP KUOTA BERDASARKAN DATA BARU ===

        // --- TKR (Punya 2 Tipe) ---
        JurusanTipeKelas::create([
            'jurusan_id' => $tkr->id,
            'tipe_kelas_id' => $reguler->id,
            'kuota_kelas' => 8 * $kapasitas // 288
        ]);
        JurusanTipeKelas::create([
            'jurusan_id' => $tkr->id,
            'tipe_kelas_id' => $unggulan->id,
            'kuota_kelas' => 2 * $kapasitas // 72
        ]);

        // --- TPM (Punya 2 Tipe) ---
        JurusanTipeKelas::create([
            'jurusan_id' => $tpm->id,
            'tipe_kelas_id' => $reguler->id,
            'kuota_kelas' => 5 * $kapasitas // 180
        ]);
        JurusanTipeKelas::create([
            'jurusan_id' => $tpm->id,
            'tipe_kelas_id' => $unggulan->id,
            'kuota_kelas' => 2 * $kapasitas // 72
        ]);

        // --- RPL (Hanya Reguler) ---
        JurusanTipeKelas::create([
            'jurusan_id' => $rpl->id,
            'tipe_kelas_id' => $reguler->id,
            'kuota_kelas' => 2 * $kapasitas // 72
        ]);
        
        // --- TITL (Hanya Reguler) ---
        JurusanTipeKelas::create([
            'jurusan_id' => $titl->id,
            'tipe_kelas_id' => $reguler->id,
            'kuota_kelas' => 2 * $kapasitas // 72
        ]);

        // --- TEI (Hanya Reguler) ---
        JurusanTipeKelas::create([
            'jurusan_id' => $tei->id,
            'tipe_kelas_id' => $reguler->id,
            'kuota_kelas' => 2 * $kapasitas // 72
        ]);
    }
}