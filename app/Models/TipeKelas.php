<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeKelas extends Model
{
    use HasFactory;

    protected $table = 'tipe_kelas';
    protected $guarded = ['id'];

    /**
     * Relasi 1:N ke JurusanTipeKelas (Relasi Many-to-Many via pivot)
     */
    public function jurusanTipeKelas(): HasMany
    {
        return $this->hasMany(JurusanTipeKelas::class, 'id_tipe_kelas');
    }

    /**
     * Relasi 1:N ke CalonSiswa (Tipe Kelas yang dipilih siswa)
     */
    public function calonSiswa(): HasMany
    {
        return $this->hasMany(CalonSiswa::class, 'tipe_kelas_id');
    }
}