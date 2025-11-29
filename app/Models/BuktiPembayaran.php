<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiPembayaran extends Model
{
    use HasFactory;

    protected $table = 'bukti_pembayaran';
    protected $guarded = ['id'];

    /**
     * Relasi N:1 ke PembayaranSiswa
     */
    public function pembayaranSiswa(): BelongsTo
    {
        return $this->belongsTo(PembayaranSiswa::class, 'pembayaran_id');
    }
}
