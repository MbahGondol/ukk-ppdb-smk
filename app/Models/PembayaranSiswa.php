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
    protected $dates = ['tanggal_bayar'];

    /**
     * Relasi N:1 ke RencanaPembayaran
     */
    public function rencanaPembayaran(): BelongsTo
    {
        return $this->belongsTo(RencanaPembayaran::class, 'id_rencana_pembayaran');
    }

    /**
     * Relasi 1:1 ke BuktiPembayaran
     */
    public function buktiPembayaran(): HasOne
    {
        return $this->hasOne(BuktiPembayaran::class, 'id_pembayaran');
    }
}
