<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';
    protected $guarded = ['id'];

    /**
     * Relasi 1:N ke JurusanTipeKelas (Relasi Many-to-Many via pivot)
     */
    public function jurusanTipeKelas(): HasMany
    {
        return $this->hasMany(JurusanTipeKelas::class, 'jurusan_id');
    }

    /**
     * Relasi 1:N ke CalonSiswa (Jurusan yang dipilih siswa)
     */
    public function calonSiswa(): HasMany
    {
        return $this->hasMany(CalonSiswa::class, 'jurusan_id');
    }
}