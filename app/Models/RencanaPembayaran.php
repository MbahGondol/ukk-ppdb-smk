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
    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
    ];

    /**
     * Relasi N:1 ke CalonSiswa
     */
    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    /**
     * Relasi N:1 ke BiayaPerJurusanTipeKelas (Detail Nominal Biaya yang ditagihkan)
     */
    public function detailBiaya(): BelongsTo
    {
        return $this->belongsTo(BiayaPerJurusanTipeKelas::class, 'biaya_per_jurusan_tipe_kelas_id');
    }

    /**
     * Relasi 1:N ke PembayaranSiswa (Transaksi aktual yang dilakukan)
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranSiswa::class, 'rencana_pembayaran_id');
    }
}