<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JurusanTipeKelas;
use Illuminate\Support\Facades\Validator;

class KuotaController extends Controller
{
    /**
     * Menampilkan daftar semua kuota.
     * (LOGIKA "READ")
     */
    public function index()
    {
        // 1. Ambil data kuota.
        $data_kuota = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->get();

        // 2. Tampilkan View, kirim data $data_kuota ke dalamnya
        return view('admin.kuota.index', [
            'data_kuota' => $data_kuota
        ]);
    }

    /**
     * Menampilkan form untuk mengedit kuota.
     * (LOGIKA UNTUK "UPDATE" - TAHAP 1)
     */
    public function edit(string $id)
    {
        // 1. Cari 1 data kuota yang mau diedit berdasarkan ID
        $kuota = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])->findOrFail($id);

        // 2. Tampilkan view form edit, kirim data $kuota
        return view('admin.kuota.edit', [
            'kuota' => $kuota
        ]);
    }

    /**
     * Memproses data dari form 'edit' dan menyimpannya.
     * (LOGIKA UNTUK "UPDATE" - TAHAP 2)
     */
    public function update(Request $request, string $id)
    {
        // 1. Validasi data (Aturan)
        $validator = Validator::make($request->all(), [
            'kuota_kelas' => 'required|integer|min:0', 
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.kuota.edit', $id)
                        ->withErrors($validator)
                        ->withInput();
        }

        // 2. Cari data yang mau di-update
        $kuota = JurusanTipeKelas::findOrFail($id);

        // 3. Update datanya di database
        $kuota->update([
            'kuota_kelas' => $request->kuota_kelas
        ]);

        // 4. Alihkan kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.kuota.index')
                         ->with('success', 'Kuota berhasil diperbarui.');
    }
}