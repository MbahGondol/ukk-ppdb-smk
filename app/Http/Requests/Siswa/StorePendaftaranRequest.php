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
            'tinggal_bersama' => ['required', 'in:ortu,wali'], // Validasi radio button

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
            'rt_rw' => 'required|string|max:20',
            'desa_kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kota_kab' => 'required|string|max:100',
            'kode_pos' => 'required|numeric|digits:5',
            'tahun_lulus' => 'required|integer|digits:4',
            
            'anak_ke' => 'nullable|integer',
            'jumlah_saudara' => 'nullable|integer',
            'tinggi_badan' => 'nullable|integer',
            'berat_badan' => 'nullable|integer',
        ];

        // 2. LOGIKA KONDISIONAL (Sesuai Permintaan Pak Putra)
        
        if ($tinggalBersama === 'wali') {
            // --- JIKA PILIH WALI ---
            // Wali WAJIB, Ortu BOLEH KOSONG (Nullable)
            $rules['nama_wali']     = 'required|string|max:100';
            $rules['nohp_wali']     = ['required', 'numeric', 'digits_between:10,13'];
            $rules['hubungan_wali'] = 'required|string';
            $rules['alamat_wali']   = 'required|string';

            $rules['nama_ayah'] = 'nullable|string';
            $rules['nama_ibu']  = 'nullable|string';
            // Field ortu lain otomatis nullable jika tidak disebut required
            
        } else {
            // --- JIKA PILIH ORTU (Default) ---
            // Ortu WAJIB, Wali BOLEH KOSONG
            $rules['nama_ayah'] = 'required|string|max:100';
            $rules['nama_ibu']  = 'required|string|max:100';
            $rules['pekerjaan_ayah'] = 'required|string';
            $rules['pekerjaan_ibu']  = 'required|string';
            
            // Validasi format angka Ortu
            $rules['nik_ayah'] = ['nullable', 'numeric', 'digits:16'];
            $rules['nik_ibu']  = ['nullable', 'numeric', 'digits:16'];
            $rules['nohp_ayah'] = ['nullable', 'numeric', 'digits_between:10,13'];
            $rules['nohp_ibu']  = ['nullable', 'numeric', 'digits_between:10,13'];
            
            $rules['nama_wali'] = 'nullable|string';
        }

        return $rules;
    }
}