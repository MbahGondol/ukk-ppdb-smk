<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TipeKelas; 

class TipeKelasSeeder extends Seeder
{
    public function run(): void
    {
        // 2. BUAT RESEPNYA
        TipeKelas::create([
            'nama_tipe_kelas' => 'Reguler',
            'keterangan' => 'Kelas reguler standar'
        ]);
        
        TipeKelas::create([
            'nama_tipe_kelas' => 'Unggulan',
            'keterangan' => 'Kelas unggulan dengan fasilitas tambahan'
        ]);
    }
}