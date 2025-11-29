@extends('layouts.admin')

@section('title', 'Edit Tipe Biaya')
@section('header', 'Edit Tipe Biaya')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit: {{ $jenis_biaya->nama_biaya }}</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Perbarui informasi tipe biaya ini.</p>
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

            <form action="{{ route('admin.jenis-biaya.update', $jenis_biaya->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_biaya">
                        Nama Biaya
                    </label>
                    <input type="text" name="nama_biaya" id="nama_biaya" value="{{ old('nama_biaya', $jenis_biaya->nama_biaya) }}" 
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" 
                           required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="keterangan">
                        Keterangan (Opsional)
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="3" 
                              class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">{{ old('keterangan', $jenis_biaya->keterangan) }}</textarea>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('admin.jenis-biaya.index') }}" class="text-gray-600 hover:text-gray-900 font-medium mr-4">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow focus:outline-none focus:shadow-outline">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection