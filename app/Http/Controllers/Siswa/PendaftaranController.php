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
        $user = Auth::user();
        $calonSiswa = $user->calonSiswa;

        // LOGIKA EDIT: Jika sudah daftar & bukan 'Melengkapi Berkas', lempar ke dashboard
        if ($calonSiswa && $calonSiswa->status_pendaftaran != 'Melengkapi Berkas') {
            return redirect()->route('siswa.dashboard');
        }

        // 1. Ambil data JurusanTipeKelas
        $data_jurusan_tipe_kelas = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->get();
        
        // 2. PERBAIKAN: Hitung jumlah pendaftar secara MANUAL (Looping)
        // Agar tidak kena error "Call to undefined method"
        foreach ($data_jurusan_tipe_kelas as $kombinasi) {
            $jumlah = CalonSiswa::where('jurusan_id', $kombinasi->jurusan_id)
                                ->where('tipe_kelas_id', $kombinasi->tipe_kelas_id)
                                ->whereNotIn('status_pendaftaran', ['Ditolak', 'Draft'])
                                ->count();
            
            // Tempelkan data jumlah ini ke objek agar bisa dibaca di View
            $kombinasi->jumlah_pendaftar = $jumlah;
        }
        
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
            'calonSiswa' => $calonSiswa // Kirim data lama untuk mode edit
        ]);
    }

    /**
     * Menyimpan data pendaftaran (CREATE - Logic)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $calonSiswa = $user->calonSiswa;

        // 1. Validasi Data Input
        $validator = Validator::make($request->all(), [
            // Ignore unique milik user sendiri saat update
            'nisn' => ['required', 'string', 'digits:10', Rule::unique('calon_siswa')->ignore($userId, 'user_id')],
            'nik' => ['required', 'string', 'digits:16', Rule::unique('calon_siswa')->ignore($userId, 'user_id')],
            
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
            
            'anak_ke' => 'nullable|integer',
            'jumlah_saudara' => 'nullable|integer',
            'tinggi_badan' => 'nullable|integer',
            'berat_badan' => 'nullable|integer',
            
            'nama_ayah' => 'required|string|max:100',
            'nik_ayah' => 'nullable|string|max:16', 
            'tahun_lahir_ayah' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),
            'pendidikan_ayah' => 'nullable|string',
            'pekerjaan_ayah' => 'nullable|string',
            'penghasilan_ayah' => 'nullable|numeric|max:9999999999999',
            'nohp_ayah' => 'nullable|string',

            'nama_ibu' => 'required|string|max:100',
            'nik_ibu' => 'nullable|string|max:16', 
            'tahun_lahir_ibu' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),
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

        // 2. VALIDASI KUOTA (MANUAL JUGA)
        $pilihan_jurusan = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->findOrFail($request->jurusan_tipe_kelas_id);
        
        // Hitung manual lagi
        $terisi = CalonSiswa::where('jurusan_id', $pilihan_jurusan->jurusan_id)
                            ->where('tipe_kelas_id', $pilihan_jurusan->tipe_kelas_id)
                            ->whereNotIn('status_pendaftaran', ['Ditolak', 'Draft'])
                            ->count();

        // Logika: Jika ini pendaftaran BARU (bukan edit) dan kuota penuh -> TOLAK
        if (!$calonSiswa && $terisi >= $pilihan_jurusan->kuota_kelas) {
            return redirect()->route('siswa.pendaftaran.create')
                ->withInput()
                ->withErrors(['jurusan_tipe_kelas_id' => 'Mohon maaf, kuota untuk jurusan ' . $pilihan_jurusan->jurusan->nama_jurusan . ' baru saja PENUH. Silakan pilih jurusan lain.']);
        }

        // 3. Simpan Data (Transaction)
        try {
            DB::transaction(function () use ($request, $user, $calonSiswa) {
                $tahun_aktif = TahunAkademik::findOrFail($request->tahun_akademik_id);
                $gelombang_aktif = Gelombang::findOrFail($request->gelombang_id);
                $jurusan_tipe_kelas = JurusanTipeKelas::findOrFail($request->jurusan_tipe_kelas_id);

                $data_siswa = [
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
                ];

                if (!$calonSiswa) {
                    $data_siswa['no_pendaftaran'] = date('Y') . $gelombang_aktif->id . $jurusan_tipe_kelas->jurusan_id . rand(1000, 9999);
                    $data_siswa['status_pendaftaran'] = 'Melengkapi Berkas';
                    $data_siswa['tanggal_submit'] = now();
                    $data_siswa['promo_id'] = $gelombang_aktif->promo_id;
                }

                $siswa_terupdate = CalonSiswa::updateOrCreate(
                    ['user_id' => $user->id],
                    $data_siswa
                );

                // Simpan Ortu
                PenanggungJawab::updateOrCreate(
                    ['calon_siswa_id' => $siswa_terupdate->id, 'hubungan' => 'Ayah'],
                    [
                        'nama_lengkap' => $request->nama_ayah,
                        'nik' => $request->nik_ayah,
                        'tahun_lahir' => $request->tahun_lahir_ayah,
                        'pendidikan_terakhir' => $request->pendidikan_ayah,
                        'pekerjaan' => $request->pekerjaan_ayah,
                        'penghasilan_bulanan' => $request->penghasilan_ayah,
                        'no_hp' => $request->nohp_ayah,
                    ]
                );

                PenanggungJawab::updateOrCreate(
                    ['calon_siswa_id' => $siswa_terupdate->id, 'hubungan' => 'Ibu'],
                    [
                        'nama_lengkap' => $request->nama_ibu,
                        'nik' => $request->nik_ibu,
                        'tahun_lahir' => $request->tahun_lahir_ibu,
                        'pendidikan_terakhir' => $request->pendidikan_ibu,
                        'pekerjaan' => $request->pekerjaan_ibu,
                        'penghasilan_bulanan' => $request->penghasilan_ibu,
                        'no_hp' => $request->nohp_ibu,
                    ]
                );

                if ($request->filled('nama_wali')) {
                    PenanggungJawab::updateOrCreate(
                        ['calon_siswa_id' => $siswa_terupdate->id, 'hubungan' => 'Wali'],
                        [
                            'nama_lengkap' => $request->nama_wali,
                            'alamat_wali' => $request->alamat_wali,
                            'no_hp' => $request->nohp_wali,
                            'pekerjaan' => $request->hubungan_wali,
                        ]
                    );
                } else {
                    // Cara 1 (Paling aman):
                    PenanggungJawab::where('calon_siswa_id', $siswa_terupdate->id)
                                ->where('hubungan', 'Wali')
                                ->delete();
                }
            });

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['database' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('siswa.dashboard')
                        ->with('success', 'Biodata berhasil disimpan/diperbarui! Silakan lanjut upload dokumen.');
    }
}