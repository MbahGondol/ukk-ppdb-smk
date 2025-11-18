<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Validator;

class JurusanController extends Controller
{
    /**
     * Menampilkan halaman daftar semua jurusan.
     * (LOGIKA "READ")
     */
    public function index()
    {
        // 1. Ambil SEMUA data jurusan dari database
        $semua_jurusan = Jurusan::all(); 

        // 2. Tampilkan View, dan "kirim" data $semua_jurusan ke dalam View
        return view('admin.jurusan.index', [
            'semua_jurusan' => $semua_jurusan
        ]);
    }

    /**
     * Menampilkan form untuk membuat jurusan baru.
     */
    public function create()
    {
        // Fungsi ini HANYA menampilkan form
        return view('admin.jurusan.create');
    }

    /**
     * Menyimpan data dari form 'create' ke database.
     * (LOGIKA "CREATE")
     */
    public function store(Request $request)
    {
        // 1. Validasi data (Aturan)
        $validator = Validator::make($request->all(), [
            'kode_jurusan' => 'required|string|unique:jurusan,kode_jurusan',
            'nama_jurusan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.jurusan.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        // 2. Jika lolos, simpan HANYA data jurusan
        Jurusan::create([
            'kode_jurusan' => $request->kode_jurusan,
            'nama_jurusan' => $request->nama_jurusan,
            'deskripsi' => $request->deskripsi,
        ]);
        
        // 3. Alihkan kembali
        return redirect()->route('admin.jurusan.index')
                         ->with('success', 'Jurusan baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
