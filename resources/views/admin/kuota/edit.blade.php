@extends('layouts.admin')

@section('title', 'Edit Kuota')
@section('header', 'Edit Kuota Kelas')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Form Edit Kuota</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Ubah daya tampung siswa untuk kelas ini.</p>
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

            <form action="{{ route('admin.kuota.update', $kuota->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Jurusan
                    </label>
                    <input type="text" value="{{ $kuota->jurusan->nama_jurusan }}" disabled
                           class="bg-gray-100 text-gray-600 cursor-not-allowed shadow-sm block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Tipe Kelas
                    </label>
                    <input type="text" value="{{ $kuota->tipeKelas->nama_tipe_kelas }}" disabled
                           class="bg-gray-100 text-gray-600 cursor-not-allowed shadow-sm block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="kuota_kelas">
                        Kuota Maksimal (Siswa)
                    </label>
                    <input type="number" name="kuota_kelas" id="kuota_kelas" value="{{ old('kuota_kelas', $kuota->kuota_kelas) }}" 
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border text-lg font-semibold" 
                           required min="0">
                    <p class="text-xs text-gray-500 mt-1">Masukkan angka saja.</p>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('admin.kuota.index') }}" class="text-gray-600 hover:text-gray-900 font-medium mr-4">
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