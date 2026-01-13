<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BiayaPerJurusanTipeKelas;
use App\Models\JenisBiaya;
use App\Models\JurusanTipeKelas;
use Illuminate\Support\Facades\DB;

class BiayaController extends Controller
{
    /**
     * Menampilkan MATRIX HARGA (Tabel Besar).
     */
    public function index()
    {
        // 1. Siapkan Kolom (Jenis Biaya)
        $list_jenis_biaya = JenisBiaya::all();

        // 2. Siapkan Baris (Jurusan + Kelas)
        $list_kelas = JurusanTipeKelas::with(['jurusan', 'tipeKelas'])
                        ->orderBy('jurusan_id')
                        ->get();
        
        // 3. Siapkan Data Harga yang Sudah Ada
        // Kita ubah formatnya jadi array 2 dimensi biar mudah dipanggil di View
        // Format: $matrix[id_kelas][id_biaya] = nominal
        $harga_existing = BiayaPerJurusanTipeKelas::all();
        $matrix = [];
        
        foreach($harga_existing as $h) {
            $matrix[$h->jurusan_tipe_kelas_id][$h->jenis_biaya_id] = $h->nominal;
        }

        return view('admin.biaya.index', [
            'list_jenis_biaya' => $list_jenis_biaya,
            'list_kelas' => $list_kelas,
            'matrix' => $matrix
        ]);
    }

    /**
     * Menyimpan perubahan harga secara MASSAL (Bulk Update).
     * Kita menggunakan route 'store' agar tidak perlu mengubah web.php
     */
    public function store(Request $request)
    {
        // Validasi: Pastikan input 'biaya' ada dan berbentuk array
        $request->validate([
            'biaya' => 'required|array',
        ]);

        $data_biaya = $request->input('biaya'); // Array 2 Dimensi

        try {
            DB::transaction(function () use ($data_biaya) {
                
                // Loop Baris (Jurusan/Kelas)
                foreach ($data_biaya as $jtk_id => $jenis_biaya_array) {
                    
                    // Loop Kolom (Jenis Biaya)
                    foreach ($jenis_biaya_array as $jb_id => $nominal) {
                        
                        // Bersihkan format rupiah jika ada (jaga-jaga)
                        $nominal_bersih = (int) str_replace('.', '', $nominal);

                        // Simpan atau Update (Upsert)
                        // Kita pakai updateOrCreate biar cerdas
                        BiayaPerJurusanTipeKelas::updateOrCreate(
                            [
                                'jurusan_tipe_kelas_id' => $jtk_id,
                                'jenis_biaya_id' => $jb_id
                            ],
                            [
                                'nominal' => $nominal_bersih,
                                // Catatan kita kosongkan dulu untuk efisiensi Matrix
                                // Jika butuh catatan, bisa ditambahkan logika lain nanti
                            ]
                        );
                    }
                }
            });

            return back()->with('success', 'Berhasil! Semua pengaturan harga telah diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // --- Method di bawah ini tidak lagi dibutuhkan dalam mode Matrix ---
    // Tapi kita biarkan kosong atau redirect agar tidak error jika ada link lama
    public function create() { return redirect()->route('admin.biaya.index'); }
    public function edit($id) { return redirect()->route('admin.biaya.index'); }
    public function update(Request $request, $id) { return redirect()->route('admin.biaya.index'); }
    public function destroy($id) { return redirect()->route('admin.biaya.index'); }
}