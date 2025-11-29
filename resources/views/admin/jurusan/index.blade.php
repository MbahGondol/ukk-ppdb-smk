@extends('layouts.admin')

@section('title', 'Manajemen Jurusan')
@section('header', 'Data Jurusan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-medium text-gray-900">Daftar Jurusan Sekolah</h2>
    <a href="{{ route('admin.jurusan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Jurusan
    </a>
</div>

@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm" role="alert">
        <p>{{ session('success') }}</p>
    </div>
@endif

<div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jurusan</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($semua_jurusan as $jurusan)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $jurusan->kode_jurusan }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $jurusan->nama_jurusan }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ Str::limit($jurusan->deskripsi, 50) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('admin.jurusan.edit', $jurusan->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        
                        <form action="{{ route('admin.jurusan.destroy', $jurusan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada data jurusan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection