<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CalonSiswa;
use App\Enums\StatusPendaftaran; 

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama
     */
    public function index()
    {
        $userId = Auth::id();

        // Kita gunakan 'with()' (Eager Loading) untuk mengambil relasi 'rencanaPembayaran' sekaligus.
        // Ini mencegah query berulang saat Dashboard mengecek status lunas/belum.
        $calonSiswa = CalonSiswa::with(['rencanaPembayaran'])
                        ->where('user_id', $userId)
                        ->first();

        return view('siswa.dashboard', [
            'calonSiswa' => $calonSiswa
        ]);
    }

    /**
     * Menampilkan detail biodata (Read Only)
     */
    public function lihatBiodata()
    {
        $user = Auth::user();
        
        // Eager loading di sini sudah bagus, pertahankan.
        $calonSiswa = CalonSiswa::where('user_id', $user->id)
                        ->with(['jurusan', 'tipeKelas', 'penanggungJawab', 'gelombang', 'promo'])
                        ->first();

        if (!$calonSiswa) {
            return redirect()->route('siswa.pendaftaran.create')
                             ->with('error', 'Anda belum mengisi biodata.');
        }

        return view('siswa.lihat_biodata', [
            'siswa' => $calonSiswa,
            'user' => $user
        ]);
    }

    /**
     * Cetak Bukti Pendaftaran/Diterima
     */
    public function cetakBukti()
    {
        $user = Auth::user();
        $siswa = CalonSiswa::where('user_id', $user->id)
                    ->with(['jurusan', 'tipeKelas', 'gelombang', 'rencanaPembayaran'])
                    ->first();

        // 1. Cek Data
        if (!$siswa) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        // 2. Cek Status Kelulusan (Hard Security Check)
        // Kita memblokir akses jika statusnya belum Resmi Diterima.
        if ($siswa->status_pendaftaran !== 'Resmi Diterima') {
            return back()->with('error', 'Maaf, Anda belum dinyatakan lulus seleksi.');
        }

        // 3. Cek Administrasi (Opsional: Blokir cetak jika belum lunas)
        // Jika Anda ingin memaksa lunas dulu baru bisa cetak, aktifkan blok ini:
        /*
        if ($siswa->masih_punya_hutang) {
             return back()->with('error', 'Silakan lunasi administrasi untuk mencetak bukti.');
        }
        */

        $pdf = Pdf::loadView('siswa.cetak_bukti', [
            'siswa' => $siswa,
            'user' => $user
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Bukti-Diterima-' . $siswa->no_pendaftaran . '.pdf');
    }
}