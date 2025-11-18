<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CalonSiswa extends Model
{
    use HasFactory;

    protected $table = 'calon_siswa';
    protected $guarded = ['id'];
    /**
     * Atribut yang harus di-cast ke tipe bawaan.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_submit' => 'datetime',
    ];

    /**
     * Relasi N:1 ke User (Pemilik Akun)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi N:1 ke Jurusan (Pilihan Siswa)
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    /**
     * Relasi N:1 ke TipeKelas (Pilihan Siswa)
     */
    public function tipeKelas(): BelongsTo
    {
        return $this->belongsTo(TipeKelas::class, 'tipe_kelas_id');
    }

    /**
     * Relasi N:1 ke Tahun Akademik
     */
    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    /**
     * Relasi N:1 ke Gelombang Pendaftaran
     */
    public function gelombang(): BelongsTo
    {
        return $this->belongsTo(Gelombang::class, 'gelombang_id');
    }

    /**
     * Relasi N:1 ke Promo
     */
    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }

    /**
     * Relasi 1:N ke Penanggung Jawab (Ayah, Ibu, Wali)
     */
    public function penanggungJawab(): HasMany
    {
        return $this->hasMany(PenanggungJawab::class, 'calon_siswa_id');
    }

    /**
     * Relasi 1:N ke Dokumen Siswa
     */
    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenSiswa::class, 'calon_siswa_id');
    }

    /**
     * Relasi 1:N ke Rencana Pembayaran
     */
    public function rencanaPembayaran(): HasOne
    {
        return $this->hasOne(RencanaPembayaran::class, 'calon_siswa_id');
    }
}
