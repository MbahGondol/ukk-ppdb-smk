<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DokumenSiswa;
use App\Models\CalonSiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 

class DokumenController extends Controller
{
    /**
     * Menampilkan halaman upload dokumen.
     * (LOGIKA "READ" - DAFTAR DOKUMEN)
     */
    public function index()
    {
        // 1. Dapatkan data siswa yang sedang login
        $siswa = Auth::user()->calonSiswa;

        // 2. Jika siswa belum mendaftar, lempar dia
        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda harus mengisi formulir pendaftaran terlebih dahulu.');
        }

        // 3. Ambil daftar dokumen yang sudah di-upload oleh siswa ini
        // Kita gunakan relasi 'dokumen' yang sudah kita buat di Model CalonSiswa
        // (Di Migrasi Anda sepertinya nama relasinya 'dokumen', pastikan di Model CalonSiswa juga 'dokumen')
        $dokumen_terupload = $siswa->dokumen()->get(); 

        // 4. Definisikan dokumen apa saja yang WAJIB (sesuai formulir offline Anda)
        $dokumen_wajib = [
            'Akte Kelahiran',
            'Ijazah SMP', // Meng-cover Ijazah SD dan SKL SMP
            'Kartu Keluarga',
            'KTP Ayah',
            'KTP Ibu',
            'KTP Wali', 
            'Foto Formal' 
        ];

        // 5. Tampilkan View, kirim data $dokumen_wajib dan $dokumen_terupload
        return view('siswa.dokumen', [
            'dokumen_wajib' => $dokumen_wajib,
            'dokumen_terupload' => $dokumen_terupload,
        ]);
    }

    /**
     * Menyimpan file dokumen yang di-upload.
     * (LOGIKA "CREATE" - UPLOAD)
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'tipe_dokumen' => 'required|string',
            'file_dokumen' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Maks 2MB
        ]);

        $siswa = Auth::user()->calonSiswa;
        $file = $request->file('file_dokumen');
        $tipe_dokumen = $request->tipe_dokumen;

        // 2. Cek apakah siswa sudah upload dokumen jenis ini?
        $cek_duplikat = DokumenSiswa::where('calon_siswa_id', $siswa->id)
                                    ->where('tipe_dokumen', $tipe_dokumen)
                                    ->first();
        if ($cek_duplikat) {
            return back()->with('error', 'Anda sudah mengunggah dokumen ' . $tipe_dokumen . '. Hapus dulu jika ingin mengganti.');
        }

        // 3. Buat nama file yang unik
        // Format: [id_siswa]_[tipe_dokumen_snake_case].[ekstensi]
        // Contoh: 1_akte_kelahiran.pdf
        $nama_file_unik = $siswa->id . '_' . str_replace(' ', '_', strtolower($tipe_dokumen)) . '.' . $file->getClientOriginalExtension();

        // 4. Simpan file ke "Gudang"
        $path = $file->storeAs('dokumen_siswa', $nama_file_unik, 'public');

        // 5. Simpan path-nya ke Database
        DokumenSiswa::create([
            'calon_siswa_id' => $siswa->id,
            'tipe_dokumen' => $tipe_dokumen,
            'file_path' => $path, // Path lengkap di storage
            'nama_asli_file' => $file->getClientOriginalName(),
            'status_verifikasi' => 'Pending', // Status awal
        ]);

        return back()->with('success', 'Dokumen ' . $tipe_dokumen . ' berhasil diunggah.');
    }

    /**
     * Menghapus file dokumen.
     * (LOGIKA "DELETE")
     */
    public function destroy(string $id)
    {
        $dokumen = DokumenSiswa::findOrFail($id);
        $siswa = Auth::user()->calonSiswa;

        // 1. Keamanan: Pastikan yang menghapus adalah pemilik dokumen
        if ($dokumen->calon_siswa_id != $siswa->id) {
            return back()->with('error', 'Anda tidak punya izin.');
        }

        // 2. Keamanan: Jangan biarkan siswa menghapus dokumen yang sudah "Valid"
        if ($dokumen->status_verifikasi == 'Valid') {
            return back()->with('error', 'Tidak bisa menghapus dokumen yang sudah divalidasi oleh Admin.');
        }

        // 3. Hapus file dari "Gudang" (Storage)
        Storage::disk('public')->delete($dokumen->file_path);

        // 4. Hapus data dari Database
        $dokumen->delete();

        return back()->with('success', 'Dokumen ' . $dokumen->tipe_dokumen . ' berhasil dihapus.');
    }
}