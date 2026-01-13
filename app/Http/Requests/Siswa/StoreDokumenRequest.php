<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe_dokumen' => 'required|string|in:Akte Kelahiran,Ijazah SMP,KTP Ayah,KTP Ibu,KTP Wali,Kartu Keluarga,Foto Formal',
            'file_dokumen' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png', 
                'max:2048', 
            ],
        ];
    }
}