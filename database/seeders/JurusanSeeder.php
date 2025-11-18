<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        
        Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Mempelajari tentang pengembangan software'
        ]);
        
        Jurusan::create([
            'kode_jurusan' => 'TKR',
            'nama_jurusan' => 'Teknik Kendaraan Ringan',
            'deskripsi' => 'Mempelajari tentang otomotif'
        ]);
        Jurusan::create([
            'kode_jurusan' => 'TPM',
            'nama_jurusan' => 'Teknik Pemesinan',
            'deskripsi' => 'Mempelajari tentang mesin produksi'
        ]);

        Jurusan::create([
            'kode_jurusan' => 'TITL',
            'nama_jurusan' => 'Teknik Instalasi Tenaga Listrik',
            'deskripsi' => 'Mempelajari tentang instalasi listrik'
        ]);

        Jurusan::create([
            'kode_jurusan' => 'TEI',
            'nama_jurusan' => 'Teknik Elektronika Industri',
            'deskripsi' => 'Mempelajari tentang sistem elektronik industri'
        ]);
    }
}