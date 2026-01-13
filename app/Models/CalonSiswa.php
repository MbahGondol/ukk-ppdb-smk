<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute; 
use App\Enums\StatusPendaftaran;

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
    public function penanggungJawab()
    {
        return $this->hasMany(PenanggungJawab::class, 'calon_siswa_id');
    }

    // 2. Buat Helper Accessor untuk mempermudah pengambilan data di View
    // Ini agar di blade cukup panggil $calonSiswa->ayah, $calonSiswa->ibu
    
    public function getDataAyahAttribute()
    {
        return $this->penanggungJawab->where('hubungan', 'Ayah')->first();
    }

    public function getDataIbuAttribute()
    {
        return $this->penanggungJawab->where('hubungan', 'Ibu')->first();
    }

    public function getDataWaliAttribute()
    {
        return $this->penanggungJawab->where('hubungan', 'Wali')->first();
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

    /**
     * Cek apakah siswa masih punya tanggungan pembayaran.
     */
    public function getMasihPunyaHutangAttribute(): bool
    {
        // Jika belum ada rencana pembayaran, dianggap tidak berhutang (atau sesuaikan logic Anda)
        if (!$this->rencanaPembayaran) {
            return false;
        }
        return $this->rencanaPembayaran->status === 'Belum Lunas';
    }

    /**
     * Cek apakah tombol aksi perlu ditampilkan.
     */
    public function getButuhTindakanAttribute(): bool
    {
        // Ganti string ini dengan Enum nanti: StatusPendaftaran::MELENGKAPI_BERKAS->value, dst.
        $statusProses = [
            'Melengkapi Berkas', 
            'Terdaftar', 
            'Proses Verifikasi'
        ];

        return in_array($this->status_pendaftaran, $statusProses) 
            || ($this->status_pendaftaran === 'Resmi Diterima' && $this->masih_punya_hutang);
    }
    
    /**
     * Helper untuk menentukan progress bar (0-100%)
     */
    public function getProgresPendaftaranAttribute(): int
    {
        // Logika sederhana untuk UX Bar
        return match ($this->status_pendaftaran) {
            'Melengkapi Berkas' => 25,
            'Terdaftar' => 50,
            'Proses Verifikasi' => 75,
            'Resmi Diterima' => 100,
            'Ditolak' => 0,
            default => 0,
        };
    }
}
