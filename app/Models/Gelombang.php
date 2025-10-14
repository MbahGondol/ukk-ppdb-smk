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
    protected $dates = ['tanggal_mulai', 'tanggal_selesai'];

    /**
     * Relasi N:1 ke Promo
     */
    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }

    /**
     * Relasi 1:N ke KuotaGelombang
     */
    public function kuotaGelombang(): HasMany
    {
        return $this->hasMany(KuotaGelombang::class, 'id_gelombang');
    }

    /**
     * Relasi 1:N ke CalonSiswa
     */
    public function calonSiswa(): HasMany
    {
        return $this->hasMany(CalonSiswa::class, 'gelombang_id');
    }
}
