<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BiayaPerJurusanTipeKelas extends Model
{
    use HasFactory;

    protected $table = 'biaya_per_jurusan_tipe_kelas';
    protected $guarded = ['id'];

    /**
     * Relasi N:1 ke JenisBiaya
     */
    public function jenisBiaya(): BelongsTo
    {
        return $this->belongsTo(JenisBiaya::class, 'id_jenis_biaya');
    }

    /**
     * Relasi N:1 ke JurusanTipeKelas
     */
    public function jurusanTipeKelas(): BelongsTo
    {
        return $this->belongsTo(JurusanTipeKelas::class, 'id_jurusan_tipe_kelas');
    }

    /**
     * Relasi 1:N ke RencanaPembayaran.
     * Nominal biaya ini dapat dijadikan rencana pembayaran untuk banyak siswa.
     */
    public function rencanaPembayaran(): HasMany
    {
        return $this->hasMany(RencanaPembayaran::class, 'id_biaya');
    }
}