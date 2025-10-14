<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KuotaGelombang extends Model
{
    use HasFactory;

    protected $table = 'kuota_gelombang_jurusan';
    protected $guarded = ['id'];

    /**
     * Relasi N:1 ke GelombangPendaftaran
     */
    public function gelombang(): BelongsTo
    {
        return $this->belongsTo(Gelombang::class, 'id_gelombang');
    }

    /**
     * Relasi N:1 ke JurusanTipeKelas
     */
    public function jurusanTipeKelas(): BelongsTo
    {
        return $this->belongsTo(JurusanTipeKelas::class, 'id_jurusan_tipe_kelas');
    }
}
