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
        // Cek dulu, jangan-jangan dia sudah daftar
        if (Auth::user()->calonSiswa) {
            return redirect()->route('siswa.dashboard');
        }

        // 1. Ambil data JurusanTipeKelas
        $data_jurusan_tipe_kelas = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->get();
        
        // 2. PERBAIKAN: Hitung jumlah pendaftar secara MANUAL (Looping)
        // Karena kita tidak punya relasi langsung di model
        foreach ($data_jurusan_tipe_kelas as $kombinasi) {
            $jumlah = CalonSiswa::where('jurusan_id', $kombinasi->jurusan_id)
                                ->where('tipe_kelas_id', $kombinasi->tipe_kelas_id)
                                ->whereNotIn('status_pendaftaran', ['Ditolak', 'Draft'])
                                ->count();
            
            // Kita "tempelkan" data jumlah ini ke objek agar bisa dibaca di View
            $kombinasi->jumlah_pendaftar = $jumlah;
        }
        
        // 3. Ambil data tahun & gelombang aktif
        $tahun_aktif = TahunAkademik::where('aktif', true)->first();
        $gelombang_aktif = Gelombang::where('tanggal_mulai', '<=', now())
                                    ->where('tanggal_selesai', '>=', now())
                                    ->first();

        if (!$tahun_aktif || !$gelombang_aktif) {
            return 'Pendaftaran belum dibuka oleh Admin. (Tahun Ajaran / Gelombang tidak ditemukan)';
        }

        return view('siswa.pendaftaran_form', [
            'data_jurusan_tipe_kelas' => $data_jurusan_tipe_kelas,
            'tahun_aktif' => $tahun_aktif,
            'gelombang_aktif' => $gelombang_aktif,
        ]);
    }

    /**
     * Menyimpan data pendaftaran (CREATE - Logic)
     */
    public function store(Request $request)
    {
        // 1. Validasi Data Input
        $validator = Validator::make($request->all(), [
            // Data Wajib
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
            'alamat' => 'required|string',
            'rt_rw' => 'required|string|max:20',
            'desa_kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kota_kab' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
            'tahun_lulus' => 'required|integer|digits:4',
            
            // Data Tambahan
            'anak_ke' => 'nullable|integer',
            'jumlah_saudara' => 'nullable|integer',
            'tinggi_badan' => 'nullable|integer',
            'berat_badan' => 'nullable|integer',
            
            // Data Ortu
            'nama_ayah' => 'required|string|max:100',
            'nik_ayah' => 'nullable|string|max:16', 
            'tahun_lahir_ayah' => 'nullable|integer|digits:4',
            'pendidikan_ayah' => 'nullable|string',
            'pekerjaan_ayah' => 'nullable|string',
            'penghasilan_ayah' => 'nullable|numeric|max:9999999999999',
            'nohp_ayah' => 'nullable|string',

            'nama_ibu' => 'required|string|max:100',
            'nik_ibu' => 'nullable|string|max:16', 
            'tahun_lahir_ibu' => 'nullable|integer|digits:4',
            'pendidikan_ibu' => 'nullable|string',
            'pekerjaan_ibu' => 'nullable|string',
            'penghasilan_ibu' => 'nullable|numeric|max:9999999999999',
            'nohp_ibu' => 'nullable|string',

            'nama_wali' => 'nullable|string|max:100',
            'hubungan_wali' => 'nullable|string',
            'alamat_wali' => 'nullable|string',
            'nohp_wali' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('siswa.pendaftaran.create')->withErrors($validator)->withInput();
        }

        // 2. VALIDASI KUOTA (PERBAIKAN DI SINI JUGA)
        // Ambil data jurusan pilihan
        $pilihan = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->findOrFail($request->jurusan_tipe_kelas_id);
        
        // Hitung pendaftar saat ini secara manual
        $terisi = CalonSiswa::where('jurusan_id', $pilihan->jurusan_id)
                            ->where('tipe_kelas_id', $pilihan->tipe_kelas_id)
                            ->whereNotIn('status_pendaftaran', ['Ditolak', 'Draft'])
                            ->count();

        if ($terisi >= $pilihan->kuota_kelas) {
            return redirect()->route('siswa.pendaftaran.create')
                ->withInput()
                ->withErrors(['jurusan_tipe_kelas_id' => 'Mohon maaf, kuota untuk jurusan ' . $pilihan->jurusan->nama_jurusan . ' baru saja PENUH. Silakan pilih jurusan lain.']);
        }

        // 3. Simpan Data (Transaction)
        try {
            DB::transaction(function () use ($request) {
                $tahun_aktif = TahunAkademik::findOrFail($request->tahun_akademik_id);
                $gelombang_aktif = Gelombang::findOrFail($request->gelombang_id);
                $jurusan_tipe_kelas = JurusanTipeKelas::findOrFail($request->jurusan_tipe_kelas_id);

                $no_pendaftaran = date('Y') . $gelombang_aktif->id . $jurusan_tipe_kelas->jurusan_id . rand(1000, 9999);

                // Simpan Siswa
                $calonSiswa = CalonSiswa::create([
                    'user_id' => Auth::id(),
                    'no_pendaftaran' => $no_pendaftaran,
                    'status_pendaftaran' => 'Melengkapi Berkas',
                    'tanggal_submit' => now(),
                    'nisn' => $request->nisn,
                    'nik' => $request->nik,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'agama' => $request->agama,
                    'no_hp' => $request->no_hp,
                    'asal_sekolah' => $request->asal_sekolah,
                    'alamat' => $request->alamat,
                    'rt_rw' => $request->rt_rw,
                    'desa_kelurahan' => $request->desa_kelurahan,
                    'kecamatan' => $request->kecamatan,
                    'kota_kab' => $request->kota_kab,
                    'kode_pos' => $request->kode_pos,
                    'tahun_lulus' => $request->tahun_lulus,
                    'anak_ke' => $request->anak_ke,
                    'jumlah_saudara' => $request->jumlah_saudara,
                    'tinggi_badan' => $request->tinggi_badan,
                    'berat_badan' => $request->berat_badan,
                    'jurusan_id' => $jurusan_tipe_kelas->jurusan_id,
                    'tipe_kelas_id' => $jurusan_tipe_kelas->tipe_kelas_id,
                    'tahun_akademik_id' => $tahun_aktif->id,
                    'gelombang_id' => $gelombang_aktif->id,
                    'promo_id' => $gelombang_aktif->promo_id,
                ]);

                // Simpan Ayah
                PenanggungJawab::create([
                    'calon_siswa_id' => $calonSiswa->id,
                    'hubungan' => 'Ayah',
                    'nama_lengkap' => $request->nama_ayah,
                    'nik' => $request->nik_ayah,
                    'tahun_lahir' => $request->tahun_lahir_ayah,
                    'pendidikan_terakhir' => $request->pendidikan_ayah,
                    'pekerjaan' => $request->pekerjaan_ayah,
                    'penghasilan_bulanan' => $request->penghasilan_ayah,
                    'no_hp' => $request->nohp_ayah,
                ]);

                // Simpan Ibu
                PenanggungJawab::create([
                    'calon_siswa_id' => $calonSiswa->id,
                    'hubungan' => 'Ibu',
                    'nama_lengkap' => $request->nama_ibu,
                    'nik' => $request->nik_ibu,
                    'tahun_lahir' => $request->tahun_lahir_ibu,
                    'pendidikan_terakhir' => $request->pendidikan_ibu,
                    'pekerjaan' => $request->pekerjaan_ibu,
                    'penghasilan_bulanan' => $request->penghasilan_ibu,
                    'no_hp' => $request->nohp_ibu,
                ]);

                // Simpan Wali
                if ($request->filled('nama_wali')) {
                    PenanggungJawab::create([
                        'calon_siswa_id' => $calonSiswa->id,
                        'hubungan' => 'Wali',
                        'nama_lengkap' => $request->nama_wali,
                        'alamat_wali' => $request->alamat_wali,
                        'no_hp' => $request->nohp_wali,
                        'pekerjaan' => $request->hubungan_wali,
                    ]);
                }
            });

        } catch (\Exception $e) {
            return redirect()->route('siswa.pendaftaran.create')
                        ->withErrors(['database' => 'Terjadi kesalahan: ' . $e->getMessage()])
                        ->withInput();
        }

        return redirect()->route('siswa.dashboard')
                        ->with('success', 'Pendaftaran berhasil! Silakan upload dokumen.');
    }
}