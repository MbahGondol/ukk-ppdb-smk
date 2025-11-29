@extends('layouts.admin')

@section('title', 'Tambah Harga')
@section('header', 'Tetapkan Harga Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Form Penetapan Harga</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Hubungkan Tipe Biaya dengan Jurusan dan tentukan nominalnya.</p>
        </div>
        
        <div class="p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Periksa inputan!</strong>
                    <ul class="list-disc list-inside text-sm mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.biaya.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Tipe Biaya</label>
                    <select name="jenis_biaya_id" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required>
                        <option value="">-- Pilih Tipe Biaya --</option>
                        @foreach ($data_jenis_biaya as $jenis)
                            <option value="{{ $jenis->id }}" {{ old('jenis_biaya_id') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama_biaya }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Kombinasi Jurusan & Kelas</label>
                    <select name="jurusan_tipe_kelas_id" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required>
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach ($data_jurusan_tipe_kelas as $kombinasi)
                            <option value="{{ $kombinasi->id }}" {{ old('jurusan_tipe_kelas_id') == $kombinasi->id ? 'selected' : '' }}>
                                {{ $kombinasi->jurusan->nama_jurusan }} ({{ $kombinasi->tipeKelas->nama_tipe_kelas }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Harga (Rp)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="nominal" value="{{ old('nominal') }}" 
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md p-2 border" 
                               placeholder="0" required min="0">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="3" 
                              class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">{{ old('catatan') }}</textarea>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('admin.biaya.index') }}" class="text-gray-600 hover:text-gray-900 font-medium mr-4">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow focus:outline-none focus:shadow-outline">
                        Simpan Harga
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection