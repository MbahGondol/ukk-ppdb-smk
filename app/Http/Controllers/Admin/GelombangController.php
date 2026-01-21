<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gelombang;
use App\Models\Promo; 

class GelombangController extends Controller
{
    /**
     * Menampilkan daftar gelombang.
     */
    public function index()
    {
        $gelombang = Gelombang::with('promo')->get(); // Ambil data beserta promonya
        return view('admin.gelombang.index', compact('gelombang'));
    }

    /**
     * Menampilkan form tambah gelombang.
     */
    public function create()
    {
        $promos = Promo::where('aktif', true)->get(); // Ambil promo aktif untuk dropdown
        return view('admin.gelombang.create', compact('promos'));
    }

    /**
     * Menyimpan data gelombang baru.
     */
    public function store(Request $request)
    {

        $request->validate([
            'nama_gelombang' => 'required|string|max:255',
            // 'today' memaksa tanggal harus hari ini atau masa depan
            'tanggal_mulai'  => 'required|date|after_or_equal:today', 
            // Tanggal selesai harus sesudah tanggal mulai
            'tanggal_selesai'=> 'required|date|after:tanggal_mulai', 
            'aktif'          => 'boolean'
        ], [
            // Custom pesan error biar user paham
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu!',
            'tanggal_selesai.after'        => 'Tanggal selesai harus sesudah tanggal mulai!'
        ]);

        // 1. Tampung hasil validasi ke variabel
        $validatedData = $request->validate([
            'nama_gelombang' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'promo_id' => 'nullable|exists:promo,id',
        ]);

        // 2. Gunakan variabel terverifikasi itu untuk create
        Gelombang::create($validatedData);

        return redirect()->route('admin.gelombang.index')
                            ->with('success', 'Gelombang pendaftaran berhasil dibuat.');
    }

    /**
     * Menampilkan form edit gelombang.
     */
    public function edit(string $id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $promos = Promo::where('aktif', true)->get();
        return view('admin.gelombang.edit', compact('gelombang', 'promos'));
    }

    /**
     * Mengupdate data gelombang.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'nama_gelombang' => 'required|string|max:255',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'required|date|after:tanggal_mulai',
            'aktif'          => 'boolean'
        ]);

        // 1. Tampung hasil validasi (Sama seperti di store)
        $validatedData = $request->validate([
            'nama_gelombang' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'promo_id' => 'nullable|exists:promo,id',
        ]);

        $gelombang = Gelombang::findOrFail($id);
        
        // 2. Gunakan variabel hasil validasi
        $gelombang->update($validatedData); 

        return redirect()->route('admin.gelombang.index')
                            ->with('success', 'Data gelombang berhasil diperbarui.');
    }

    /**
     * Menghapus gelombang.
     */
    public function destroy(string $id)
    {
        try {
            $gelombang = Gelombang::findOrFail($id);
            $gelombang->delete();
            return redirect()->route('admin.gelombang.index')
                             ->with('success', 'Gelombang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika gagal karena sudah ada siswa yang daftar di gelombang ini
            return redirect()->route('admin.gelombang.index')
                             ->with('error', 'Gagal menghapus! Gelombang ini sudah digunakan oleh pendaftar.');
        }
    }
}