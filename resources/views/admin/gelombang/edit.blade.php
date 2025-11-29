@extends('layouts.admin')
@section('title', 'Edit Gelombang')
@section('header', 'Edit Gelombang')

@section('content')
    <div class="max-w-2xl bg-white p-8 rounded-lg shadow">
        <form action="{{ route('admin.gelombang.update', $gelombang->id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nama Gelombang</label>
                <input type="text" name="nama_gelombang" value="{{ $gelombang->nama_gelombang }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ $gelombang->tanggal_mulai->format('Y-m-d') }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ $gelombang->tanggal_selesai->format('Y-m-d') }}" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Promo (Opsional)</label>
                <select name="promo_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Tidak Ada Promo --</option>
                    @foreach($promos as $promo)
                        <option value="{{ $promo->id }}" {{ $gelombang->promo_id == $promo->id ? 'selected' : '' }}>
                            {{ $promo->nama_promo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.gelombang.index') }}" class="text-gray-600 font-bold py-2 px-4 mr-4">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection