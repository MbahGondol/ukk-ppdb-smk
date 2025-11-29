@extends('layouts.admin')
@section('title', 'Tambah Promo')
@section('header', 'Buat Kode Promo Baru')

@section('content')
    <div class="max-w-2xl bg-white p-8 rounded-lg shadow">
        
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <strong class="font-bold">Periksa inputan Anda!</strong>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.promo.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nama Promo</label>
                <input type="text" name="nama_promo" value="{{ old('nama_promo') }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="Contoh: Diskon Gelombang 1" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nominal Potongan (Rp)</label>
                <input type="number" name="potongan" value="{{ old('potongan') }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="Contoh: 50000" required>
                <p class="text-xs text-gray-500 mt-1">Masukkan angka saja tanpa titik atau koma.</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="3" 
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          placeholder="Keterangan tambahan tentang promo ini...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.promo.index') }}" class="text-gray-600 font-bold py-2 px-4 mr-4 hover:underline">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition">
                    Simpan Promo
                </button>
            </div>
        </form>
    </div>
@endsection