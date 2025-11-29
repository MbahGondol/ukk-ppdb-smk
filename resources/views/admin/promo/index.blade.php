@extends('layouts.admin')
@section('title', 'Kode Promo')
@section('header', 'Manajemen Kode Promo')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <p class="text-gray-600">Kelola diskon dan potongan biaya.</p>
        <a href="{{ route('admin.promo.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
            + Buat Promo Baru
        </a>
    </div>

    @if(session('success')) <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">{{ session('success') }}</div> @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Promo</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nominal Potongan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($promos as $item)
                <tr>
                    <td class="px-6 py-4 font-bold">{{ $item->nama_promo }}</td>
                    <td class="px-6 py-4 text-green-600 font-bold">Rp {{ number_format($item->potongan, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->deskripsi ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.promo.edit', $item->id) }}" class="text-blue-600 mr-3 hover:underline">Edit</a>
                        <form action="{{ route('admin.promo.destroy', $item->id) }}" method="POST" class="inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Hapus promo ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection