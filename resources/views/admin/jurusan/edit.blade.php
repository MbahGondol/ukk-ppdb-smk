@extends('layouts.admin')

@section('title', 'Edit Jurusan')
@section('header', 'Edit Data Jurusan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Jurusan: {{ $jurusan->kode_jurusan }}</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Perbarui informasi jurusan ini.</p>
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

            <form action="{{ route('admin.jurusan.update', $jurusan->id) }}" method="POST">
                @csrf
                @method('PUT') <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="kode_jurusan">
                        Kode Jurusan
                    </label>
                    <input type="text" name="kode_jurusan" id="kode_jurusan" value="{{ old('kode_jurusan', $jurusan->kode_jurusan) }}" 
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" 
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_jurusan">
                        Nama Jurusan Lengkap
                    </label>
                    <input type="text" name="nama_jurusan" id="nama_jurusan" value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}" 
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" 
                           required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="deskripsi">
                        Deskripsi (Opsional)
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" 
                              class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">{{ old('deskripsi', $jurusan->deskripsi) }}</textarea>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('admin.jurusan.index') }}" class="text-gray-600 hover:text-gray-900 font-medium mr-4">
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