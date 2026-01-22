<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\DokumenSiswa;
use App\Http\Requests\Siswa\StoreDokumenRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cek Siswa
        $calonSiswa = CalonSiswa::where('user_id', $user->id)->first();
        if (!$calonSiswa) {
            return redirect()->route('siswa.pendaftaran.create')
                             ->with('error', 'Isi biodata terlebih dahulu.');
        }

        // Ambil dokumen yang sudah diupload
        $dokumen = DokumenSiswa::where('calon_siswa_id', $calonSiswa->id)
                    ->get()
                    ->keyBy('tipe_dokumen');

        // DEFINISIKAN DOKUMEN WAJIB SECARA DINAMIS
        $daftarDokumen = [
            'Kartu Keluarga',
            'Akte Kelahiran',
            'Ijazah SMP',
            'Foto Formal'
        ];

        // Logika Dinamis untuk Controller
        if ($calonSiswa->tinggal_bersama == 'Wali') {
            $daftarDokumen[] = 'KTP Wali';
        } else {
            $daftarDokumen[] = 'KTP Ayah';
            $daftarDokumen[] = 'KTP Ibu';
        }

        // Kirim variabel $daftarDokumen ke View
        return view('siswa.dokumen', compact('calonSiswa', 'dokumen', 'daftarDokumen'));
    }

    /**
     * Store menggunakan updateOrCreate (Lebih Cerdas dari sekadar Create)
     */
    public function store(StoreDokumenRequest $request)
    {
        $user = Auth::user();
        $calonSiswa = CalonSiswa::where('user_id', $user->id)->firstOrFail();

        $file = $request->file('file_dokumen');
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        $folderPath = 'dokumen_siswa/' . $calonSiswa->id;

        // 1. CEK DULU: Apakah dokumen ini sudah ada sebelumnya?
        $existingDoc = DokumenSiswa::where('calon_siswa_id', $calonSiswa->id)
                        ->where('tipe_dokumen', $request->tipe_dokumen)
                        ->first();
        
        // Tentukan kata kerja pesan berdasarkan kondisi
        $pesanAksi = $existingDoc ? 'diperbarui' : 'disimpan';

        // 2. Hapus file lama fisik jika ada (Cleaning)
        if ($existingDoc && Storage::exists($existingDoc->file_path)) {
            Storage::delete($existingDoc->file_path);
        }

        // 3. Simpan file baru
        $path = $file->storeAs($folderPath, $fileName); 

        // 4. Update/Create Database
        DokumenSiswa::updateOrCreate(
            [
                'calon_siswa_id' => $calonSiswa->id,
                'tipe_dokumen' => $request->tipe_dokumen
            ],
            [
                'nama_file' => $fileName,
                'nama_asli_file' => $file->getClientOriginalName(),
                'file_path' => $path,
                'status_verifikasi' => 'Pending' 
            ]
        );

        // 5. Kembalikan pesan yang spesifik
        return back()->with('success', "Dokumen {$request->tipe_dokumen} berhasil {$pesanAksi}.");
    }

    /**
     * Method untuk menampilkan file PRIVATE ke browser (Preview)
     */
    public function show($id)
    {
        $dokumen = DokumenSiswa::findOrFail($id);
        $user = Auth::user();

        // AUTHORIZATION CHECK (PENTING!)
        // 1. Apakah dia Admin?
        // 2. Atau apakah dia Pemilik dokumen ini?
        $isOwner = $user->calonSiswa && $user->calonSiswa->id === $dokumen->calon_siswa_id;
        $isAdmin = $user->hasRole('admin'); // Pastikan Spatie Permission jalan

        if (!$isOwner && !$isAdmin) {
            abort(403, 'AKSES DITOLAK: Ini bukan dokumen Anda.');
        }

        // Cek fisik file
        if (!Storage::exists($dokumen->file_path)) {
            abort(404, 'File fisik tidak ditemukan.');
        }

        return response()->file(Storage::path($dokumen->file_path), [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

}