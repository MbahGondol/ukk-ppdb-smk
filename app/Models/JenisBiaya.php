<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBiaya extends Model
{
    use HasFactory;

    protected $table = 'jenis_biaya';
    protected $guarded = ['id'];

    /**
     * Relasi 1:N ke BiayaPerJurusanTipeKelas.
     * Jenis biaya ini dapat memiliki banyak nominal harga berbeda 
     * tergantung Jurusan dan Tipe Kelasnya.
     */
    public function biayaPerKelas(): HasMany
    {
        return $this->hasMany(BiayaPerJurusanTipeKelas::class, 'id_jenis_biaya');
    }
}
