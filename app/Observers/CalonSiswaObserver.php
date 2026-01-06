<?php

namespace App\Observers;

use App\Models\CalonSiswa;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class CalonSiswaObserver
{
    /**
     * Mencatat aktivitas ke database
     */
    private function catatLog($model, string $jenisAktivitas, $dataLama = null, $dataBaru = null)
    {
        // Hanya catat jika ada user yang login (menghindari error saat seeder/automasi)
        if (Auth::check()) {
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'jenis_aktivitas' => $jenisAktivitas, // Create, Update, Delete
                'deskripsi' => 'Aktivitas pada data siswa: ' . $model->nama_lengkap,
                'nama_tabel' => 'calon_siswa',
                'record_id' => $model->id,
                'data_lama' => $dataLama ? json_encode($dataLama) : null,
                'data_baru' => $dataBaru ? json_encode($dataBaru) : null,
                'ip_address' => Request::ip(),
                'platform' => substr(Request::userAgent(), 0, 250),
            ]);
        }
    }

    /**
     * Handle the CalonSiswa "created" event.
     */
    public function created(CalonSiswa $calonSiswa): void
    {
        $this->catatLog($calonSiswa, 'Create', null, $calonSiswa->toArray());
    }

    /**
     * Handle the CalonSiswa "updated" event.
     */
    public function updated(CalonSiswa $calonSiswa): void
    {
        $perubahan = $calonSiswa->getChanges(); 
        
        // Ambil data asli sebelum berubah
        $original = array_intersect_key($calonSiswa->getOriginal(), $perubahan);

        $this->catatLog($calonSiswa, 'Update', $original, $perubahan);
    }

    /**
     * Handle the CalonSiswa "deleted" event.
     */
    public function deleted(CalonSiswa $calonSiswa): void
    {
        $this->catatLog($calonSiswa, 'Delete', $calonSiswa->toArray(), null);
    }
}