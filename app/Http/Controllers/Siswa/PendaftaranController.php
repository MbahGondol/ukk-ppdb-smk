<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\StatusPendaftaran;
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
use App\Http\Requests\Siswa\StorePendaftaranRequest;

class PendaftaranController extends Controller
{

    /**
     * Menampilkan daftar pendaftar (ADMIN VIEW)
     */
    public function index(Request $request)
    {
        // 1. Ambil filter dari URL
        $status_filter = $request->query('status');

        // 2. Query Data Utama (Untuk Tabel)
        $query = CalonSiswa::with(['user', 'jurusan', 'tipeKelas', 'gelombang']);

        if ($status_filter) {
            $query->where('status_pendaftaran', $status_filter);
        }
        
        $data_siswa = $query->orderBy('tanggal_submit', 'desc')->get();

        // 3. OPTIMASI COUNTING (Hanya 1 Query ke DB)
        // Kita gunakan Enum value di dalam query selectRaw agar konsisten
        $counts = CalonSiswa::toBase()
            ->selectRaw("count(*) as semua")
            ->selectRaw("count(case when status_pendaftaran = ? then 1 end) as draft", [StatusPendaftaran::MELENGKAPI_BERKAS->value])
            ->selectRaw("count(case when status_pendaftaran = ? then 1 end) as terdaftar", [StatusPendaftaran::TERDAFTAR->value])
            ->selectRaw("count(case when status_pendaftaran = ? then 1 end) as diterima", [StatusPendaftaran::DITERIMA->value])
            ->selectRaw("count(case when status_pendaftaran = ? then 1 end) as ditolak", [StatusPendaftaran::DITOLAK->value])
            ->first();

        // Konversi object ke array agar view tidak error (karena view mengharapkan array)
        $counts = (array) $counts;

        // 4. Kirim data dan hitungan ke View
        return view('admin.pendaftar.index', [
            'data_siswa' => $data_siswa,
            'status_sekarang' => $status_filter,
            'counts' => $counts
        ]);
    }

    /**
     * Menampilkan form pendaftaran (CREATE - Form)
     */
    public function create()
    {
        $user = Auth::user();
        $calonSiswa = $user->calonSiswa;

        // Jika sudah submit dan status bukan 'Melengkapi Berkas', redirect ke dashboard
        if ($calonSiswa && $calonSiswa->status_pendaftaran != StatusPendaftaran::MELENGKAPI_BERKAS->value) {
            return redirect()->route('siswa.dashboard');
        }
        
        $data_jurusan_tipe_kelas = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])
            ->addSelect(['jumlah_pendaftar' => CalonSiswa::selectRaw('count(*)')
                ->whereColumn('calon_siswa.jurusan_id', 'jurusan_tipe_kelas.jurusan_id')
                ->whereColumn('calon_siswa.tipe_kelas_id', 'jurusan_tipe_kelas.tipe_kelas_id')
                ->whereNotIn('status_pendaftaran', [
                    StatusPendaftaran::DITOLAK->value, 
                    StatusPendaftaran::DRAFT->value
                ])
            ])
            ->get();
        
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
    public function store(StorePendaftaranRequest $request)
    {
        $user = Auth::user();
        $calonSiswa = $user->calonSiswa;

        try {
            DB::transaction(function () use ($request, $user, $calonSiswa) {
                
                // 1. LOCK & LOAD
                $jurusan_tipe_kelas = JurusanTipeKelas::where('id', $request->jurusan_tipe_kelas_id)
                                                        ->lockForUpdate() 
                                                        ->firstOrFail();

                // 2. HITUNG KUOTA
                $terisi = CalonSiswa::where('jurusan_id', $jurusan_tipe_kelas->jurusan_id)
                                    ->where('tipe_kelas_id', $jurusan_tipe_kelas->tipe_kelas_id)
                                    ->whereNotIn('status_pendaftaran', [
                                        StatusPendaftaran::DITOLAK->value, 
                                        StatusPendaftaran::DRAFT->value
                                    ])
                                    ->count();

                $sedangPindahJurusan = $calonSiswa && ($calonSiswa->jurusan_id != $jurusan_tipe_kelas->jurusan_id || $calonSiswa->tipe_kelas_id != $jurusan_tipe_kelas->tipe_kelas_id);

                if ((!$calonSiswa || $sedangPindahJurusan) && $terisi >= $jurusan_tipe_kelas->kuota_kelas) {
                    throw new \Exception('Mohon maaf, kuota jurusan ini PENUH.');
                }

                $tahun_aktif = TahunAkademik::findOrFail($request->tahun_akademik_id);
                $gelombang_aktif = Gelombang::findOrFail($request->gelombang_id);

                // 3. SIAPKAN DATA SISWA
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
                    $data_siswa['status_pendaftaran'] = StatusPendaftaran::MELENGKAPI_BERKAS->value;
                    $data_siswa['tanggal_submit'] = now();
                    $data_siswa['promo_id'] = $gelombang_aktif->promo_id;
                }

                $siswa_terupdate = CalonSiswa::updateOrCreate(
                    ['user_id' => $user->id],
                    $data_siswa
                );

                // 4. LOGIKA PENYIMPANAN PENANGGUNG JAWAB (DINAMIS)
                $pilihTinggal = $request->tinggal_bersama; // 'ortu' atau 'wali'

                if ($pilihTinggal === 'ortu') {
                    // --- SIMPAN ORTU ---
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
                    
                    // Hapus data Wali jika ada (agar bersih)
                    PenanggungJawab::where('calon_siswa_id', $siswa_terupdate->id)
                                    ->where('hubungan', 'Wali')
                                    ->delete();

                } else {
                    // --- SIMPAN WALI ---
                    PenanggungJawab::updateOrCreate(
                        ['calon_siswa_id' => $siswa_terupdate->id, 'hubungan' => 'Wali'],
                        [
                            'nama_lengkap' => $request->nama_wali,
                            'alamat_wali' => $request->alamat_wali,
                            'no_hp' => $request->nohp_wali,
                            'pekerjaan' => $request->hubungan_wali,
                        ]
                    );
                }
            });

        } catch (\Exception $e) {
            return redirect()->back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
        }

        return redirect()->route('siswa.dashboard')
                        ->with('success', 'Biodata berhasil disimpan!');
    }
}