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
        // Ambil ID user yang sedang login untuk pengecualian "unique" (agar bisa update data sendiri)
        $userId = Auth::id();

        return [
            'nisn' => [
                'required', 
                'numeric', 
                'digits:10', 
                Rule::unique('calon_siswa')->ignore($userId, 'user_id')
            ],

            'nik' => [
                'required', 
                'numeric', 
                'digits:16', 
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
            
            // --- DATA AYAH ---
            'nama_ayah' => 'required|string|max:100',
            'nik_ayah' => ['nullable', 'numeric', 'digits:16'],
            'tahun_lahir_ayah' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),
            'pendidikan_ayah' => 'nullable|string',
            'pekerjaan_ayah' => 'nullable|string',
            'penghasilan_ayah' => 'nullable|numeric|max:9999999999999',
            'nohp_ayah' => ['nullable', 'numeric', 'digits_between:10,13'],

            // --- DATA IBU ---
            'nama_ibu' => 'required|string|max:100',
            'nik_ibu' => ['nullable', 'numeric', 'digits:16'],
            'tahun_lahir_ibu' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),
            'pendidikan_ibu' => 'nullable|string',
            'pekerjaan_ibu' => 'nullable|string',
            'penghasilan_ibu' => 'nullable|numeric|max:9999999999999',
            'nohp_ibu' => ['nullable', 'numeric', 'digits_between:10,13'],

            // --- DATA WALI ---
            'nama_wali' => 'nullable|string|max:100',
            'hubungan_wali' => 'nullable|string',
            'alamat_wali' => 'nullable|string',
            'nohp_wali' => ['nullable', 'numeric', 'digits_between:10,13'],
        ];
    }
}