<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DokumenSiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; 

class DokumenController extends Controller
{
    /**
     * Menampilkan halaman upload dokumen.
     */
    public function index()
    {
        $siswa = Auth::user()->calonSiswa;

        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda harus mengisi formulir pendaftaran terlebih dahulu.');
        }

        $dokumen_terupload = $siswa->dokumen()->get(); 

        $dokumen_wajib = [
            'Akte Kelahiran',
            'Ijazah SMP',
            'Kartu Keluarga',
            'KTP Ayah',
            'KTP Ibu',
            'KTP Wali', 
            'Foto Formal' 
        ];

        return view('siswa.dokumen', [
            'dokumen_wajib' => $dokumen_wajib,
            'dokumen_terupload' => $dokumen_terupload,
        ]);
    }

    /**
     * Menyimpan file dokumen yang di-upload.
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'tipe_dokumen' => 'required|string',
            // Gunakan validasi MIME yang ketat
            'file_dokumen' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', 
        ]);

        $siswa = Auth::user()->calonSiswa;
        $file = $request->file('file_dokumen');
        $tipe_dokumen = $request->tipe_dokumen;

        // 2. Cek Duplikat
        $cek_duplikat = DokumenSiswa::where('calon_siswa_id', $siswa->id)
                                    ->where('tipe_dokumen', $tipe_dokumen)
                                    ->first();
        if ($cek_duplikat) {
            return back()->with('error', 'Anda sudah mengunggah dokumen ' . $tipe_dokumen . '. Hapus dulu jika ingin mengganti.');
        }

        // 3. SECURITY: Generate Nama File Acak (Anti-Tebak)
        $ext = $file->getClientOriginalExtension();
        $nama_file_acak = Str::uuid() . '.' . $ext;

        // 4. SECURITY: Simpan di folder PRIVATE ('local'), bukan 'public'
        // Folder 'dokumen_rahasia' tidak akan bisa diakses via browser langsung.
        $path = $file->storeAs('dokumen_rahasia', $nama_file_acak, 'local');

        // 5. Simpan ke Database
        DokumenSiswa::create([
            'calon_siswa_id' => $siswa->id,
            'tipe_dokumen' => $tipe_dokumen,
            'file_path' => $path, // Path di storage private
            'nama_asli_file' => $file->getClientOriginalName(),
            'status_verifikasi' => 'Pending',
        ]);

        return back()->with('success', 'Dokumen ' . $tipe_dokumen . ' berhasil diunggah.');
    }

    /**
     * SECURITY: Method khusus untuk melihat file private
     */
    public function show($id)
    {
        $dokumen = DokumenSiswa::findOrFail($id);
        $user = Auth::user();

        // 1. Cek Hak Akses (Authorization)
        if ($user->hasRole('siswa') && $dokumen->calon_siswa_id != $user->calonSiswa->id) {
            abort(403, 'Anda tidak berhak melihat dokumen ini.');
        }

        // 2. Cek apakah file fisik ada di storage local
        if (!Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        // 3. Ambil Full Path (Lokasi asli di harddisk server)
        // Storage::path() mengubah 'dokumen_rahasia/xxx.jpg' menjadi 'C:\Projects\...\storage\app\private\...'
        $path = Storage::disk('local')->path($dokumen->file_path);

        // 4. Return file agar bisa tampil di browser (Inline View)
        return response()->file($path);
    }

    /**
     * Menghapus file dokumen.
     */
    public function destroy(string $id)
    {
        $dokumen = DokumenSiswa::findOrFail($id);
        $siswa = Auth::user()->calonSiswa;

        // 1. Validasi Pemilik
        if ($dokumen->calon_siswa_id != $siswa->id) {
            return back()->with('error', 'Anda tidak punya izin.');
        }

        // 2. Validasi Status
        if ($dokumen->status_verifikasi == 'Valid') {
            return back()->with('error', 'Tidak bisa menghapus dokumen yang sudah divalidasi oleh Admin.');
        }

        // 3. Hapus file dari Disk 'local'
        Storage::disk('local')->delete($dokumen->file_path);

        // 4. Hapus data dari Database
        $dokumen->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}