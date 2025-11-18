<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PembayaranSiswa extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_siswa';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
    ];

    /**
     * Relasi N:1 ke RencanaPembayaran
     */
    public function rencanaPembayaran(): BelongsTo
    {
        return $this->belongsTo(RencanaPembayaran::class, 'rencana_pembayaran_id');
    }

    /**
     * Relasi 1:1 ke BuktiPembayaran
     */
    public function buktiPembayaran(): HasOne
    {
        return $this->hasOne(BuktiPembayaran::class, 'pembayaran_id');
    }
}