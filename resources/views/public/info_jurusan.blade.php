@extends('layouts.app')

@section('title', 'Informasi Jurusan')

@section('content')
    <div class="p-10 mb-6 bg-white rounded-lg shadow-md">
        <h1 class="text-4xl font-bold text-gray-800">Informasi Jurusan</h1>
        <p class="mt-4 text-xl text-gray-600">
            SMK Pejantan Tangguh memiliki 5 jurusan unggulan yang siap mengantar Anda ke dunia industri.
        </p>
        <hr class="my-6">

        <div class="space-y-8">
            @forelse ($semua_jurusan as $jurusan)
                <div class="border-b pb-6">
                    <h2 class="text-3xl font-semibold text-blue-600">{{ $jurusan->nama_jurusan }} ({{ $jurusan->kode_jurusan }})</h2>
                    <p class="mt-2 text-lg text-gray-700">
                        {{ $jurusan->deskripsi }}
                    </p>
                    <div class="mt-6">
                        <img 
                            src="{{ asset('img/jurusan/' . $jurusan->kode_jurusan . '.jpg') }}" 
                            alt="Ruang Praktik {{ $jurusan->nama_jurusan }}"
                            class="w-full h-64 object-cover rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300"
                            onerror="this.onerror=null; this.src='https://via.placeholder.com/800x400?text=Foto+Tidak+Tersedia';"
                        >
                        <p class="mt-2 text-sm text-gray-500 text-center italic">
                            Suasana kegiatan praktik di jurusan {{ $jurusan->nama_jurusan }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-lg text-red-500">Admin belum memasukkan data jurusan.</p>
            @endforelse
        </div>

    </div>
@endsection