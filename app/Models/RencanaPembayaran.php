<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RencanaPembayaran extends Model
{
    use HasFactory;

    protected $table = 'rencana_pembayaran';
    protected $guarded = ['id'];
    protected $dates = ['tanggal_jatuh_tempo'];

    /**
     * Relasi N:1 ke CalonSiswa
     */
    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'id_siswa');
    }

    /**
     * Relasi N:1 ke BiayaPerJurusanTipeKelas (Detail Nominal Biaya yang ditagihkan)
     */
    public function detailBiaya(): BelongsTo
    {
        return $this->belongsTo(BiayaPerJurusanTipeKelas::class, 'id_biaya');
    }

    /**
     * Relasi 1:N ke PembayaranSiswa (Transaksi aktual yang dilakukan)
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranSiswa::class, 'id_rencana_pembayaran');
    }
}