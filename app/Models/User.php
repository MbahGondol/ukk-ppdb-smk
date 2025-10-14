<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // Dihapus karena menyebabkan error
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasRoles; // Tambahan untuk Spatie Roles & Permissions
    
    use HasFactory, Notifiable;
    // use HasApiTokens, HasFactory, Notifiable; // HasApiTokens dihapus

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Tambahan dari migration
    ];

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast ke tipe bawaan.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi 1:1 ke CalonSiswa.
     * FK: id_users pada tabel calon_siswa
     */
    public function calonSiswa(): HasOne
    {
        return $this->hasOne(CalonSiswa::class, 'id_users');
    }

    /**
     * Relasi 1:N ke Log Aktivitas (Admin/Siswa)
     */
    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'id_users');
    }
}
