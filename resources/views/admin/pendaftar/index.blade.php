@extends('layouts.admin')

@section('title', 'Laporan Pendaftar')
@section('header', 'Semua Data Pendaftar')

@section('content')
    <div class="mb-6">
        <p class="text-gray-600 mb-4">Arsip lengkap seluruh data pendaftar. Gunakan filter untuk menyortir.</p>
        
        <div class="flex space-x-2 border-b border-gray-200 pb-1 overflow-x-auto">
            <a href="{{ route('admin.pendaftar.index') }}" 
               class="px-4 py-2 rounded-t-lg font-medium text-sm transition {{ !$status_sekarang ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
               Semua
            </a>
            <a href="{{ route('admin.pendaftar.index', ['status' => 'Terdaftar']) }}" 
               class="px-4 py-2 rounded-t-lg font-medium text-sm transition {{ $status_sekarang == 'Terdaftar' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
               Inbox Verifikasi
            </a>
            <a href="{{ route('admin.pendaftar.index', ['status' => 'Resmi Diterima']) }}" 
               class="px-4 py-2 rounded-t-lg font-medium text-sm transition {{ $status_sekarang == 'Resmi Diterima' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
               Diterima
            </a>
            <a href="{{ route('admin.pendaftar.index', ['status' => 'Ditolak']) }}" 
               class="px-4 py-2 rounded-t-lg font-medium text-sm transition {{ $status_sekarang == 'Ditolak' ? 'bg-red-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
               Ditolak
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-b-lg border border-t-0 border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">No. Daftar</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jurusan</th>
                    
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Gelombang</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tgl Submit</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($data_siswa as $siswa)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono">{{ $siswa->no_pendaftaran }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                            <div class="text-xs text-gray-500">{{ $siswa->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $siswa->jurusan->kode_jurusan }}
                            <span class="text-xs text-gray-500">({{ $siswa->tipeKelas->nama_tipe_kelas }})</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $siswa->gelombang->nama_gelombang }}
                            </span>
                            @if($siswa->promo)
                                <span class="block text-xs text-red-500 mt-1 font-bold">Promo: {{ $siswa->promo->nama_promo }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $siswa->tanggal_submit->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $siswa->status_pendaftaran == 'Resmi Diterima' ? 'bg-green-100 text-green-800' : 
                                  ($siswa->status_pendaftaran == 'Ditolak' ? 'bg-red-100 text-red-800' : 
                                  ($siswa->status_pendaftaran == 'Terdaftar' ? 'bg-yellow-100 text-yellow-800' : 
                                  ($siswa->status_pendaftaran == 'Melengkapi Berkas' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800'))) }}">
                                {{ $siswa->status_pendaftaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.verifikasi.show', $siswa->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-bold hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection