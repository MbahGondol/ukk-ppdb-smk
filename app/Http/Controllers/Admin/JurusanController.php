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
     * Menampilkan form untuk mengedit jurusan.
     * (LOGIKA "UPDATE" - FORM)
     */
    public function edit(string $id)
    {
        // 1. Cari data jurusan berdasarkan ID
        $jurusan = Jurusan::findOrFail($id);

        // 2. Tampilkan View edit, kirim data $jurusan
        return view('admin.jurusan.edit', [
            'jurusan' => $jurusan
        ]);
    }

    /**
     * Mengupdate data jurusan di database.
     * (LOGIKA "UPDATE" - SIMPAN)
     */
    public function update(Request $request, string $id)
    {
        // 1. Cari data yang mau di-update
        $jurusan = Jurusan::findOrFail($id);

        // 2. Validasi data
        $validator = Validator::make($request->all(), [
            // Rule unique: abaikan ID diri sendiri agar tidak error jika nama tidak berubah
            'kode_jurusan' => 'required|string|unique:jurusan,kode_jurusan,' . $jurusan->id,
            'nama_jurusan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.jurusan.edit', $jurusan->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        // 3. Update data
        $jurusan->update([
            'kode_jurusan' => $request->kode_jurusan,
            'nama_jurusan' => $request->nama_jurusan,
            'deskripsi' => $request->deskripsi,
        ]);

        // 4. Redirect
        return redirect()->route('admin.jurusan.index')
                         ->with('success', 'Data jurusan berhasil diperbarui.');
    }

    /**
     * Menghapus jurusan.
     * (LOGIKA "DELETE")
     */
    public function destroy(string $id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);
            $jurusan->delete();
            
            return redirect()->route('admin.jurusan.index')
                             ->with('success', 'Jurusan berhasil dihapus.');
                             
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika gagal karena foreign key (misal sudah ada siswa atau kuota)
            return redirect()->route('admin.jurusan.index')
                             ->with('error', 'GAGAL! Jurusan ini tidak bisa dihapus karena sedang digunakan (mungkin ada siswa atau kuota yang terhubung).');
        }
    }
}
