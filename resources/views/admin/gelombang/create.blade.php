@extends('layouts.admin')
@section('title', 'Tambah Gelombang')
@section('header', 'Buat Gelombang Baru')

@section('content')
    <div class="max-w-2xl bg-white p-8 rounded-lg shadow">
        <form action="{{ route('admin.gelombang.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nama Gelombang</label>
                <input type="text" name="nama_gelombang" class="w-full border rounded px-3 py-2" placeholder="Contoh: Gelombang 1 (2025)" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="w-full border rounded px-3 py-2" min="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Promo (Opsional)</label>
                <select name="promo_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Promo (Jika Ada) --</option>
                    @foreach($promos as $promo)
                        <option value="{{ $promo->id }}">{{ $promo->nama_promo }} (Potongan: {{ $promo->potongan }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Siswa yang mendaftar di tanggal ini akan otomatis mendapat promo ini.</p>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.gelombang.index') }}" class="text-gray-600 font-bold py-2 px-4 mr-4">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
            </div>
        </form>
    </div>
@endsection