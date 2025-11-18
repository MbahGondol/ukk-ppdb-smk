<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BiayaPerJurusanTipeKelas;
use App\Models\JenisBiaya;
use App\Models\JurusanTipeKelas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BiayaController extends Controller
{
    /**
     * Menampilkan daftar semua harga. (READ)
     */
    public function index()
    {
        // Eager load relasi BERANTAI!
        // Ambil 'jenisBiaya' DAN 'jurusanTipeKelas'
        // DAN dari 'jurusanTipeKelas', ambil 'jurusan' dan 'tipeKelas'
        $data_harga = BiayaPerJurusanTipeKelas::with([
            'jenisBiaya', 
            'jurusanTipeKelas.jurusan', 
            'jurusanTipeKelas.tipeKelas'
        ])->get();

        return view('admin.biaya.index', [
            'data_harga' => $data_harga
        ]);
    }

    /**
     * Menampilkan form untuk membuat harga baru. (CREATE - Form)
     * Ini adalah bagian paling rumit, kita perlu 2 data master.
     */
    public function create()
    {
        // 1. Ambil semua Tipe Biaya (untuk dropdown 1)
        $data_jenis_biaya = JenisBiaya::all();

        // 2. Ambil semua Jurusan + Tipe Kelas (untuk dropdown 2)
        $data_jurusan_tipe_kelas = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->get();

        // 3. Kirim kedua data itu ke View
        return view('admin.biaya.create', [
            'data_jenis_biaya' => $data_jenis_biaya,
            'data_jurusan_tipe_kelas' => $data_jurusan_tipe_kelas,
        ]);
    }

    /**
     * Menyimpan data dari form 'create' ke database. (CREATE - Logic)
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'jenis_biaya_id' => [
                'required',
                // Validasi 'unique' pada DUA kolom sekaligus
                // Pastikan kombinasi 'jenis_biaya_id' dan 'jurusan_tipe_kelas_id' UNIK
                Rule::unique('biaya_per_jurusan_tipe_kelas')->where(function ($query) use ($request) {
                    return $query->where('jurusan_tipe_kelas_id', $request->jurusan_tipe_kelas_id);
                }),
            ],
            'jurusan_tipe_kelas_id' => 'required',
            'nominal' => 'required|integer|min:0',
        ], [
            // Pesan error kustom
            'jenis_biaya_id.unique' => 'Kombinasi Tipe Biaya dan Jurusan ini sudah memiliki harga.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.biaya.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        // 2. Simpan ke DB
        BiayaPerJurusanTipeKelas::create([
            'jenis_biaya_id' => $request->jenis_biaya_id,
            'jurusan_tipe_kelas_id' => $request->jurusan_tipe_kelas_id,
            'nominal' => $request->nominal,
            'catatan' => $request->catatan,
        ]);

        // 3. Redirect
        return redirect()->route('admin.biaya.index')
                         ->with('success', 'Harga baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit harga. (UPDATE - Form)
     */
    public function edit(string $id)
    {
        // 1. Ambil semua data master (sama seperti 'create')
        $data_jenis_biaya = JenisBiaya::all();
        $data_jurusan_tipe_kelas = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->get();

        // 2. Ambil data harga yang mau diedit
        $harga = BiayaPerJurusanTipeKelas::findOrFail($id);

        // 3. Kirim semua data ke View
        return view('admin.biaya.edit', [
            'harga' => $harga,
            'data_jenis_biaya' => $data_jenis_biaya,
            'data_jurusan_tipe_kelas' => $data_jurusan_tipe_kelas,
        ]);
    }

    /**
     * Memproses data dari form 'edit' dan menyimpannya. (UPDATE - Logic)
     */
    public function update(Request $request, string $id)
    {
        // 1. Cari datanya
        $harga = BiayaPerJurusanTipeKelas::findOrFail($id);

        // 2. Validasi
        $validator = Validator::make($request->all(), [
            'jenis_biaya_id' => [
                'required',
                Rule::unique('biaya_per_jurusan_tipe_kelas')->where(function ($query) use ($request) {
                    return $query->where('jurusan_tipe_kelas_id', $request->jurusan_tipe_kelas_id);
                })->ignore($harga->id), // Abaikan data lama (dirinya sendiri)
            ],
            'jurusan_tipe_kelas_id' => 'required',
            'nominal' => 'required|integer|min:0',
        ], [
            'jenis_biaya_id.unique' => 'Kombinasi Tipe Biaya dan Jurusan ini sudah memiliki harga.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.biaya.edit', $harga->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        // 3. Update datanya
        $harga->update([
            'jenis_biaya_id' => $request->jenis_biaya_id,
            'jurusan_tipe_kelas_id' => $request->jurusan_tipe_kelas_id,
            'nominal' => $request->nominal,
            'catatan' => $request->catatan,
        ]);

        // 4. Redirect
        return redirect()->route('admin.biaya.index')
                         ->with('success', 'Harga berhasil diperbarui.');
    }

    /**
     * Menghapus data harga. (DELETE - Logic)
     */
    public function destroy(string $id)
    {
        // Untuk fitur cicilan Anda (rencana_pembayaran), 
        // kita juga harus pakai try-catch
        
        try {
            $harga = BiayaPerJurusanTipeKelas::findOrFail($id);
            $harga->delete();

            return redirect()->route('admin.biaya.index')
                             ->with('success', 'Harga berhasil dihapus.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Jika harga ini sudah dipakai di 'rencana_pembayaran'
            if ($e->getCode() == "23000") {
                return redirect()->route('admin.biaya.index')
                                 ->with('error', 'GAGAL! Harga ini tidak bisa dihapus karena sudah digunakan oleh siswa.');
            }
            return redirect()->route('admin.biaya.index')
                             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}