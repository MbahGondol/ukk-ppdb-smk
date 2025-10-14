<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenSiswa extends Model
{
    use HasFactory;

    protected $table = 'dokumen_siswa';
    protected $guarded = ['id'];

    /**
     * Relasi N:1 ke CalonSiswa
     */
    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'id_siswa');
    }
}
