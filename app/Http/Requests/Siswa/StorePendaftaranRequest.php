<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StorePendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = Auth::id();
        
        // Ambil input pilihan (ortu/wali)
        $tinggalBersama = $this->input('tinggal_bersama');

        // 1. RULES DASAR (Wajib untuk semua)
        $rules = [
            'tinggal_bersama' => ['required', 'in:ortu,wali'], 

            'nisn' => [
                'required', 'numeric', 'digits:10', 
                Rule::unique('calon_siswa')->ignore($userId, 'user_id')
            ],
            'nik' => [
                'required', 'numeric', 'digits:16', 
                Rule::unique('calon_siswa')->ignore($userId, 'user_id')
            ],
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'no_hp' => ['required', 'numeric', 'digits_between:10,13'],
            'asal_sekolah' => 'required|string|max:150',
            'jurusan_tipe_kelas_id' => 'required|exists:jurusan_tipe_kelas,id',
            'alamat' => 'required|string',
            'rt_rw' => ['required', 'string', 'max:10', 'regex:/^[0-9\/]+$/'],
            'desa_kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kota_kab' => 'required|string|max:100',
            'kode_pos' => ['required', 'numeric', 'digits:5'],
            // Validasi Logis untuk Tahun Lulus
            'tahun_lulus' => 'required|integer|digits:4|min:2000|max:'.(date('Y')+1),
            
            // Validasi Logis untuk Data Fisik & Saudara
            'anak_ke' => 'nullable|integer|min:1|max:20',
            'jumlah_saudara' => 'nullable|integer|min:0|max:20', 
            'tinggi_badan' => 'nullable|integer|min:50|max:300',
            'berat_badan' => 'nullable|integer|min:20|max:200',
            
            // Tambahkan validasi Provinsi agar tidak 'Amnesia' saat disimpan
            'provinsi' => 'required|string|max:100',
        ];

        // 2. LOGIKA KONDISIONAL
        
        if ($tinggalBersama === 'wali') {
            // --- JIKA PILIH WALI ---
            $rules['nama_wali']     = 'required|string|max:100';
            $rules['nohp_wali']     = ['required', 'numeric', 'digits_between:10,13'];
            $rules['hubungan_wali'] = 'required|string';
            $rules['alamat_wali']   = 'required|string';

            $rules['nama_ayah'] = 'nullable|string';
            $rules['nama_ibu']  = 'nullable|string';
            
        } else {
            // --- JIKA PILIH ORTU (Default) ---
            $rules['nama_ayah'] = 'required|string|max:100';
            $rules['nama_ibu']  = 'required|string|max:100';
            $rules['pekerjaan_ayah'] = 'required|string';
            $rules['pekerjaan_ibu']  = 'required|string';
            
            // Validasi format angka Ortu
            $rules['nik_ayah'] = ['nullable', 'numeric', 'digits:16'];
            $rules['nik_ibu']  = ['nullable', 'numeric', 'digits:16'];
            $rules['nohp_ayah'] = ['nullable', 'numeric', 'digits_between:10,13'];
            $rules['nohp_ibu']  = ['nullable', 'numeric', 'digits_between:10,13'];
            $rules['tahun_lahir_ayah'] = ['required', 'integer', 'digits:4', 'min:1920', 'max:'.date('Y')];
            $rules['tahun_lahir_ibu']  = ['required', 'integer', 'digits:4', 'min:1920', 'max:'.date('Y')];
            
            $rules['nama_wali'] = 'nullable|string';
        }

        return $rules;
    }

    /**
     * Pesan kustom untuk validasi spesifik ini.
     * Mengoreksi logika "Gunakan yang lain" menjadi instruksi yang benar.
     */
    public function messages(): array
    {
        return [
            // Kustomisasi pesan unique khusus untuk NISN & NIK
            'nisn.unique' => 'NISN ini sudah terdaftar di sistem. Jika Anda merasa belum pernah mendaftar, segera hubungi Panitia PPDB.',
            'nik.unique'  => 'NIK ini sudah digunakan oleh pendaftar lain. Mohon cek kembali atau lapor ke operator sekolah.',
            
            // Validasi kondisional Ortu/Wali
            'nama_wali.required' => 'Karena Anda memilih tinggal bersama Wali, Nama Wali wajib diisi.',
            'nohp_wali.required' => 'Nomor HP Wali wajib dicantumkan untuk keperluan darurat.',
            'nama_ayah.required' => 'Nama Ayah wajib diisi (kecuali Anda tinggal dengan Wali).',
            'nama_ibu.required'  => 'Nama Ibu wajib diisi (kecuali Anda tinggal dengan Wali).',
        ];
    }
}