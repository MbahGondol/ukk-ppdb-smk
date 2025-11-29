@extends('layouts.admin')

@section('title', 'Tipe Biaya')
@section('header', 'Master Data Tipe Biaya')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-600">Kelola kategori biaya (misal: SPP, Uang Gedung, Seragam).</p>
    <a href="{{ route('admin.jenis-biaya.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Tipe Biaya
    </a>
</div>

@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Biaya</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($data_biaya as $biaya)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                        {{ $biaya->nama_biaya }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $biaya->keterangan ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('admin.jenis-biaya.edit', $biaya->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        
                        <form action="{{ route('admin.jenis-biaya.destroy', $biaya->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus tipe biaya ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada data tipe biaya.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection