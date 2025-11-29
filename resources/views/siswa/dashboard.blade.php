@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}!</h1>
            <p class="mt-2 text-gray-600">Selamat datang di Portal PPDB SMK Pejantan Tangguh.</p>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Status Pendaftaran</h2>

            @if ($calonSiswa)
                
                @if ($calonSiswa->status_pendaftaran == 'Resmi Diterima')
                    <div class="bg-green-100 border-l-4 border-green-500 p-6 mb-6 rounded shadow-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-green-800">SELAMAT! ANDA DITERIMA</h3>
                                <p class="text-green-700 mt-1">
                                    Selamat bergabung di SMK Pejantan Tangguh. Anda telah resmi menjadi siswa kami.
                                    Silakan cetak bukti pendaftaran dan bawa ke sekolah untuk pengambilan seragam.
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('siswa.cetak.bukti') }}" target="_blank" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak Bukti Diterima
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 w-full">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                                <div class="mb-2 md:mb-0">
                                    <p class="text-sm text-blue-700">
                                        Mendaftar pada: <span class="font-bold">{{ $calonSiswa->tanggal_submit->format('d F Y, H:i') }}</span>
                                    </p>
                                    <p class="text-lg mt-1 text-blue-800">
                                        Status Saat Ini: 
                                        <span class="px-3 py-1 rounded-full text-sm font-bold 
                                            {{ $calonSiswa->status_pendaftaran == 'Terdaftar' ? 'bg-green-200 text-green-800' : 
                                               ($calonSiswa->status_pendaftaran == 'Resmi Diterima' ? 'bg-green-600 text-white' : 
                                               ($calonSiswa->status_pendaftaran == 'Ditolak' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800')) }}">
                                            {{ $calonSiswa->status_pendaftaran }}
                                        </span>
                                    </p>
                                </div>
                                
                                <a href="{{ route('siswa.biodata') }}" class="text-sm bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 font-semibold py-2 px-4 rounded shadow-sm flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Biodata Saya
                                </a>
                            </div>

                            @if($calonSiswa->catatan_admin)
                                <div class="mt-3 p-3 bg-red-100 text-red-800 rounded border border-red-200 text-sm">
                                    <strong>Pesan dari Admin:</strong> {{ $calonSiswa->catatan_admin }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if ($calonSiswa->status_pendaftaran == 'Melengkapi Berkas' || $calonSiswa->status_pendaftaran == 'Terdaftar' || $calonSiswa->status_pendaftaran == 'Proses Verifikasi')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('siswa.dokumen.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">1. Upload Dokumen</h5>
                            <p class="font-normal text-gray-700">Unggah KK, Akte, dan Ijazah Anda di sini.</p>
                            <div class="mt-4 text-blue-600 font-semibold">Buka Halaman &rarr;</div>
                        </a>

                        <a href="{{ route('siswa.pembayaran.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">2. Pembayaran</h5>
                            <p class="font-normal text-gray-700">Lakukan pembayaran dan upload bukti transfer.</p>
                            <div class="mt-4 text-green-600 font-semibold">Buka Halaman &rarr;</div>
                        </a>
                    </div>
                @endif

            @else
                <div class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data pendaftaran</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulailah dengan mengisi formulir biodata diri Anda.</p>
                    <div class="mt-6">
                        <a href="{{ route('siswa.pendaftaran.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Mulai Pendaftaran Sekarang
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection