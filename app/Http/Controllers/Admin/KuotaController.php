<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JurusanTipeKelas;
use App\Models\Jurusan;
use App\Models\TipeKelas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KuotaController extends Controller
{
    /**
     * Menampilkan daftar semua kuota.
     */
    public function index()
    {
        $data_kuota = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->get();

        return view('admin.kuota.index', [
            'data_kuota' => $data_kuota
        ]);
    }

    /**
     * Menampilkan form untuk menambah kombinasi Jurusan-Kelas baru.
     */
    public function create()
    {
        // Ambil data untuk Dropdown
        $jurusans = Jurusan::where('aktif', true)->get();
        $tipeKelas = TipeKelas::all();
        
        return view('admin.kuota.create', compact('jurusans', 'tipeKelas'));
    }

    /**
     * Menyimpan data kuota baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jurusan_id' => 'required|exists:jurusan,id',
            'tipe_kelas_id' => [
                'required',
                'exists:tipe_kelas,id',
                // Validasi Unik Kombinasi:
                // Pastikan pasangan Jurusan + Tipe Kelas ini belum ada di database
                Rule::unique('jurusan_tipe_kelas')->where(function ($query) use ($request) {
                    return $query->where('jurusan_id', $request->jurusan_id);
                }),
            ],
            'kuota_kelas' => 'required|integer|min:1',
        ], [
            'tipe_kelas_id.unique' => 'Kombinasi Jurusan dan Tipe Kelas ini sudah ada. Silakan edit data yang lama saja.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.kuota.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JurusanTipeKelas::create($request->all());

        return redirect()->route('admin.kuota.index')
                         ->with('success', 'Kelas dan Kuota baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit kuota.
     */
    public function edit(string $id)
    {
        $kuota = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->findOrFail($id);

        return view('admin.kuota.edit', [
            'kuota' => $kuota
        ]);
    }

    /**
     * Memproses data dari form 'edit' dan menyimpannya.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'kuota_kelas' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.kuota.edit', $id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $kuota = JurusanTipeKelas::findOrFail($id);
        $kuota->update([
            'kuota_kelas' => $request->kuota_kelas
        ]);

        return redirect()->route('admin.kuota.index')
                         ->with('success', 'Kuota berhasil diperbarui.');
    }
}