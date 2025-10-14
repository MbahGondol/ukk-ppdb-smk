<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promo';
    protected $guarded = ['id'];
    protected $dates = ['tanggal_mulai', 'tanggal_selesai'];

    /**
     * Relasi 1:N ke GelombangPendaftaran
     */
    public function gelombang(): HasMany
    {
        return $this->hasMany(Gelombang::class, 'promo_id');
    }

    /**
     * Relasi 1:N ke CalonSiswa (Promo yang diterima siswa)
     */
    public function calonSiswa(): HasMany
    {
        return $this->hasMany(CalonSiswa::class, 'promo_id');
    }
}
