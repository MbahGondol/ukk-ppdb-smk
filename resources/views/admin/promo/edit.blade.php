@extends('layouts.admin')
@section('title', 'Edit Promo')
@section('header', 'Edit Kode Promo')

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

        <form action="{{ route('admin.promo.update', $promo->id) }}" method="POST">
            @csrf
            @method('PUT') <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nama Promo</label>
                <input type="text" name="nama_promo" value="{{ old('nama_promo', $promo->nama_promo) }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nominal Potongan (Rp)</label>
                <input type="number" name="potongan" value="{{ old('potongan', $promo->potongan) }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="3" 
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi', $promo->deskripsi) }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.promo.index') }}" class="text-gray-600 font-bold py-2 px-4 mr-4 hover:underline">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection