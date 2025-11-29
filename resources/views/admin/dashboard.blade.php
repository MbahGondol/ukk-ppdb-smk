@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('header', 'Dashboard Overview')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-sm text-gray-500 font-bold uppercase">Total Pendaftar</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $total_pendaftar }}</div>
                </div>
                <div class="text-blue-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-sm text-gray-500 font-bold uppercase">Inbox Verifikasi</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $perlu_verifikasi }}</div>
                </div>
                <div class="text-yellow-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            @if($perlu_verifikasi > 0)
                <div class="mt-3">
                    <a href="{{ route('admin.verifikasi.index') }}" class="text-sm text-yellow-600 hover:underline font-semibold">Proses Sekarang &rarr;</a>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-sm text-gray-500 font-bold uppercase">Resmi Diterima</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $diterima }}</div>
                </div>
                <div class="text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

         <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-sm text-gray-500 font-bold uppercase">Ditolak</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $ditolak }}</div>
                </div>
                <div class="text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Selamat Datang di Panel Admin</h3>
        <p class="text-gray-600">
            Silakan gunakan menu di sebelah kiri untuk mengelola data Jurusan, Kuota, Biaya, dan memverifikasi pendaftaran siswa baru.
        </p>
    </div>
@endsection