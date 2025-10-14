<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    use HasFactory;

    protected $table = 'tahun_akademik'; // Nama tabel yang spesifik
    protected $guarded = ['id']; // Semua kolom boleh diisi kecuali ID

    /**
     * Relasi 1:N ke CalonSiswa (Tahun akademik yang dipilih siswa)
     */
    public function calonSiswa()
    {
        return $this->hasMany(CalonSiswa::class, 'tahun_akademik_id');
    }
}
