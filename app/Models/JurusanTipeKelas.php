<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JurusanTipeKelas extends Model
{
    use HasFactory;

    protected $table = 'jurusan_tipe_kelas';
    protected $guarded = ['id'];

    /**
     * Relasi N:1 ke Jurusan
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    /**
     * Relasi N:1 ke TipeKelas
     */
    public function tipeKelas(): BelongsTo
    {
        return $this->belongsTo(TipeKelas::class, 'tipe_kelas_id');
    }


    /**
     * Relasi 1:N ke BiayaPerJurusanTipeKelas
     */
    public function biayaPerKelas(): HasMany
    {
        return $this->hasMany(BiayaPerJurusanTipeKelas::class, 'jurusan_tipe_kelas_id');
    }
}
