<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gelombang extends Model
{
    use HasFactory;

    protected $table = 'gelombang';
    protected $guarded = ['id'];
    /**
     * Atribut yang harus di-cast ke tipe bawaan.
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relasi N:1 ke Promo
     */
    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }


    /**
     * Relasi 1:N ke CalonSiswa
     */
    public function calonSiswa(): HasMany
    {
        return $this->hasMany(CalonSiswa::class, 'gelombang_id');
    }
}
