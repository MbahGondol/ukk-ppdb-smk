<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisBiaya;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JenisBiayaController extends Controller
{
    /**
     * Menampilkan daftar semua tipe biaya. (READ)
     */
    public function index()
    {
        $data_biaya = JenisBiaya::all();
        return view('admin.jenisbiaya.index', [
            'data_biaya' => $data_biaya
        ]);
    }

    /**
     * Menampilkan form untuk membuat tipe biaya baru. (CREATE - Form)
     */
    public function create()
    {
        return view('admin.jenisbiaya.create');
    }

    /**
     * Menyimpan data dari form 'create' ke database. (CREATE - Logic)
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'nama_biaya' => 'required|string|unique:jenis_biaya,nama_biaya',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.jenis-biaya.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        // 2. Simpan ke DB
        JenisBiaya::create([
            'nama_biaya' => $request->nama_biaya,
            'keterangan' => $request->keterangan,
        ]);

        // 3. Redirect
        return redirect()->route('admin.jenis-biaya.index')
                         ->with('success', 'Tipe Biaya baru berhasil ditambahkan.');
    }

    /**
     * (Opsional) Menampilkan detail 1 data. Kita lewati dulu.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Menampilkan form untuk mengedit tipe biaya. (UPDATE - Form)
     */
    public function edit(string $id)
    {
        $jenis_biaya = JenisBiaya::findOrFail($id);
        return view('admin.jenisbiaya.edit', [
            'jenis_biaya' => $jenis_biaya
        ]);
    }

    /**
     * Memproses data dari form 'edit' dan menyimpannya. (UPDATE - Logic)
     */
    public function update(Request $request, string $id)
    {
        // 1. Cari datanya
        $jenis_biaya = JenisBiaya::findOrFail($id);

        // 2. Validasi
        $validator = Validator::make($request->all(), [
            'nama_biaya' => [
                'required',
                'string',
                Rule::unique('jenis_biaya')->ignore($jenis_biaya->id),
            ],
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.jenis-biaya.edit', $jenis_biaya->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        // 3. Update datanya
        $jenis_biaya->update([
            'nama_biaya' => $request->nama_biaya,
            'keterangan' => $request->keterangan,
        ]);

        // 4. Redirect
        return redirect()->route('admin.jenis-biaya.index')
                         ->with('success', 'Tipe Biaya berhasil diperbarui.');
    }

    /**
     * Menghapus data dari database. (DELETE - Logic)
     */
    public function destroy(string $id)
    {
        try {
            $jenis_biaya = JenisBiaya::findOrFail($id);
            $jenis_biaya->delete(); 

            return redirect()->route('admin.jenis-biaya.index')
                             ->with('success', 'Tipe Biaya berhasil dihapus.');

        } catch (\Illuminate\Database\QueryException $e) {
            
            // Cek kode error untuk "foreign key constraint"
            if ($e->getCode() == "23000") { // Kode 23000 = error integritas
                // Jika error karena datanya dipakai
                return redirect()->route('admin.jenis-biaya.index')
                                 ->with('error', 'GAGAL! Tipe Biaya ini tidak bisa dihapus karena sudah digunakan di Daftar Harga.');
            }

            // Jika error lain
            return redirect()->route('admin.jenis-biaya.index')
                             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}