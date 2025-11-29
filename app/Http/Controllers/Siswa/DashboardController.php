<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Dapatkan ID user yang sedang login
        $userId = Auth::id();

        // 2. Cek apakah user ini sudah punya data di tabel 'calon_siswa'
        // Kita gunakan relasi 'calonSiswa' yang sudah kita buat di Model User
        $calonSiswa = Auth::user()->calonSiswa;

        return view('siswa.dashboard', [
            'calonSiswa' => $calonSiswa
        ]);
    }

    public function lihatBiodata()
    {
        $user = Auth::user();
        
        $calonSiswa = $user->calonSiswa()->with(['jurusan', 'tipeKelas', 'penanggungJawab', 'gelombang', 'promo'])->first();

        if (!$calonSiswa) {
            return redirect()->route('siswa.pendaftaran.create')
                             ->with('error', 'Anda belum mengisi biodata.');
        }

        return view('siswa.lihat_biodata', [
            'siswa' => $calonSiswa,
            'user' => $user
        ]);
    }

    public function cetakBukti()
    {
        $user = Auth::user();
        $siswa = $user->calonSiswa()->with(['jurusan', 'tipeKelas', 'gelombang'])->first();

        // Cek Keamanan: Hanya boleh cetak jika sudah selesai daftar
        if (!$siswa || $siswa->status_pendaftaran == 'Melengkapi Berkas' || $siswa->status_pendaftaran == 'Draft') {
            return back()->with('error', 'Anda belum menyelesaikan pendaftaran. Silakan upload dokumen dan pembayaran dulu.');
        }

        // Render View khusus PDF
        $pdf = Pdf::loadView('siswa.cetak_bukti', [
            'siswa' => $siswa,
            'user' => $user
        ]);

        // Set ukuran kertas A4
        $pdf->setPaper('a4', 'portrait');

        // Download file dengan nama khusus
        return $pdf->stream('Bukti-Pendaftaran-' . $siswa->no_pendaftaran . '.pdf');
    }
}