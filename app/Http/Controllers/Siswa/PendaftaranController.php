<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JurusanTipeKelas;
use App\Models\TahunAkademik;
use App\Models\Gelombang;
use App\Models\CalonSiswa;
use App\Models\PenanggungJawab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PendaftaranController extends Controller
{
    /**
     * Menampilkan form pendaftaran (CREATE - Form)
     */
    public function create()
    {
        // Cek dulu, jangan-jangan dia sudah daftar tapi maksa buka URL
        if (Auth::user()->calonSiswa) {
            return redirect()->route('siswa.dashboard');
        }

        // 1. Ambil data master untuk dropdown
        $data_jurusan_tipe_kelas = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->get();
        
        // 2. Ambil data tahun ajaran & gelombang yang AKTIF
        // (Kita anggap hanya ada 1 yang aktif)
        $tahun_aktif = TahunAkademik::where('aktif', true)->first();
        $gelombang_aktif = Gelombang::where('tanggal_mulai', '<=', now())
                                    ->where('tanggal_selesai', '>=', now())
                                    ->first();

        // 3. Validasi darurat jika admin lupa setting
        if (!$tahun_aktif || !$gelombang_aktif) {
            return 'Pendaftaran belum dibuka oleh Admin. (Tahun Ajaran / Gelombang tidak ditemukan)';
        }

        // 4. Kirim data ke View
        return view('siswa.pendaftaran_form', [
            'data_jurusan_tipe_kelas' => $data_jurusan_tipe_kelas,
            'tahun_aktif' => $tahun_aktif,
            'gelombang_aktif' => $gelombang_aktif,
        ]);
    }

    /**
     * Menyimpan data pendaftaran (CREATE - Logic)
     * Ini adalah logika paling penting di sisi siswa.
     */
    public function store(Request $request)
    {
        // 1. Validasi data (Bisa Anda tambahkan lebih banyak)
        $validator = Validator::make($request->all(), [
            // Data Siswa
            'nisn' => 'required|string|digits:10|unique:calon_siswa,nisn',
            'nik' => 'required|string|digits:16|unique:calon_siswa,nik',
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'no_hp' => 'required|string|max:20',
            'asal_sekolah' => 'required|string|max:150',
            'jurusan_tipe_kelas_id' => 'required|exists:jurusan_tipe_kelas,id',
            // ... (validasi lain: tempat_lahir, tgl_lahir, alamat, dll)

            // Data Ayah
            'nama_ayah' => 'required|string|max:100',
            'nik_ayah' => 'nullable|string|digits:16',
            // ... (validasi lain: pekerjaan_ayah, no_hp_ayah, dll)

            // Data Ibu
            'nama_ibu' => 'required|string|max:100',
            // ... (validasi lain: pekerjaan_ibu, no_hp_ibu, dll)
        ]);

        if ($validator->fails()) {
            return redirect()->route('siswa.pendaftaran.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        // 2. LOGIKA PENDAFTARAN (WAJIB PAKAI TRANSACTION)
        // "Transaction" memastikan jika salah satu query gagal (misal simpan data Ayah gagal),
        // maka data Siswa juga akan di-rollback (dibatalkan).
        // Ini mencegah data "hantu" (ada data siswa, tapi data ayahnya tidak ada).
        
        try {
            DB::transaction(function () use ($request) {
                // Ambil data aktif (dari hidden input)
                $tahun_aktif = TahunAkademik::findOrFail($request->tahun_akademik_id);
                $gelombang_aktif = Gelombang::findOrFail($request->gelombang_id);
                $jurusan_tipe_kelas = JurusanTipeKelas::findOrFail($request->jurusan_tipe_kelas_id);

                // A. Buat Nomor Pendaftaran Unik
                // Format: TAHUN + ID_GELOMBANG + ID_JURUSAN + 4 Digit Acak
                $no_pendaftaran = date('Y') . $gelombang_aktif->id . $jurusan_tipe_kelas->jurusan_id . rand(1000, 9999);

                // B. Simpan ke tabel 'calon_siswa' (File L)
                $calonSiswa = CalonSiswa::create([
                    'user_id' => Auth::id(), // <-- Relasi ke Akun
                    'no_pendaftaran' => $no_pendaftaran,
                    'status_pendaftaran' => 'Melengkapi Berkas', 
                    'tanggal_submit' => now(),

                    // Data dari Form
                    'nisn' => $request->nisn,
                    'nik' => $request->nik,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'agama' => $request->agama,
                    'no_hp' => $request->no_hp,
                    'asal_sekolah' => $request->asal_sekolah,
                    // ... (isi semua kolom lain dari $request)

                    // Data Pilihan
                    'jurusan_id' => $jurusan_tipe_kelas->jurusan_id,
                    'tipe_kelas_id' => $jurusan_tipe_kelas->tipe_kelas_id,
                    
                    // Data Sistem
                    'tahun_akademik_id' => $tahun_aktif->id,
                    'gelombang_id' => $gelombang_aktif->id,
                    'promo_id' => $gelombang_aktif->promo_id, // Ambil promo dari gelombang
                ]);

                // C. Simpan ke tabel 'penanggung_jawab' (Data Ayah)
                PenanggungJawab::create([
                    'calon_siswa_id' => $calonSiswa->id, // <-- Relasi ke Siswa
                    'hubungan' => 'Ayah',
                    'nama_lengkap' => $request->nama_ayah,
                    'nik' => $request->nik_ayah,
                    // ... (isi semua kolom data Ayah dari $request)
                ]);

                // D. Simpan ke tabel 'penanggung_jawab' (Data Ibu)
                PenanggungJawab::create([
                    'calon_siswa_id' => $calonSiswa->id, // <-- Relasi ke Siswa
                    'hubungan' => 'Ibu',
                    'nama_lengkap' => $request->nama_ibu,
                    // ... (isi semua kolom data Ibu dari $request)
                ]);

                // E. (Opsional) Simpan Data Wali jika diisi
                if ($request->filled('nama_wali')) {
                    PenanggungJawab::create([
                        'calon_siswa_id' => $calonSiswa->id,
                        'hubungan' => 'Wali',
                        'nama_lengkap' => $request->nama_wali,
                        // ... (isi semua kolom data Wali dari $request)
                    ]);
                }

            }); // <-- Akhir dari Transaction

        } catch (\Exception $e) {
            // Jika terjadi error di dalam Transaction
            return redirect()->route('siswa.pendaftaran.create')
                         ->withErrors(['database' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
                         ->withInput();
        }

        // 3. Jika Transaction SUKSES
        return redirect()->route('siswa.dashboard')
                         ->with('success', 'Pendaftaran Anda berhasil disubmit! Silakan lanjut ke tahap upload dokumen.');
    }
}