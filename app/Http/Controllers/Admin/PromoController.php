<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::all();
        return view('admin.promo.index', compact('promos'));
    }

    public function create()
    {
        return view('admin.promo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_promo' => 'required|string|max:150',
            'potongan' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        Promo::create($request->all());

        return redirect()->route('admin.promo.index')->with('success', 'Promo baru berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $promo = Promo::findOrFail($id);
        return view('admin.promo.edit', compact('promo'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_promo' => 'required|string|max:150',
            'potongan' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $promo = Promo::findOrFail($id);

        // LOGIC PENYELAMAT: Cek apakah promo sudah dipakai siswa
        if ($promo->calonSiswa()->exists()) {
            // Jika nilai potongan yang dikirim beda dengan yang di database
            if ($request->potongan != $promo->potongan) {
                return redirect()->back()
                    ->with('error', 'FATAL: Nilai potongan TIDAK BOLEH DIUBAH karena sudah digunakan oleh calon siswa. Silakan buat promo baru jika ingin nilai berbeda.')
                    ->withInput();
            }
        }

        $promo->update($request->all());

        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        try {
            $promo = Promo::findOrFail($id);
            $promo->delete();
            return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.promo.index')->with('error', 'Gagal hapus. Promo sedang digunakan di Gelombang atau Siswa.');
        }
    }
}