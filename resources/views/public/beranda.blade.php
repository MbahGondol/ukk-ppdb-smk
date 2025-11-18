@extends('layouts.app')

@section('title', 'Selamat Datang di PPDB Online')

@section('content')

    @if ($gelombang_aktif)
        <div class="p-6 mb-6 bg-green-100 border-l-4 border-green-500 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4 flex-grow">
                    <h2 class="text-2xl font-bold text-green-800">Pendaftaran Telah Dibuka!</h2>
                    <p class="text-lg text-green-700">
                        <strong>{{ $gelombang_aktif->nama_gelombang }}</strong> sedang berlangsung
                        (s/d {{ \Carbon\Carbon::parse($gelombang_aktif->tanggal_selesai)->format('d F Y') }}).
                    </p>
                    
                    @if ($gelombang_aktif->promo)
                        <p class="mt-1 text-lg font-semibold text-red-600">
                            🔥 PROMO AKTIF: {{ $gelombang_aktif->promo->nama_promo }} 
                            (Potongan Rp {{ number_format($gelombang_aktif->promo->potongan, 0, ',', '.') }})
                        </p>
                    @endif
                </div>
                <div class="flex-shrink-0 ml-4">
                    <a href="{{ route('register') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg shadow-lg">
                        Daftar Sekarang!
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="p-6 mb-6 bg-red-100 border-l-4 border-red-500 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-red-800">Pendaftaran Saat Ini Ditutup</h2>
                    <p class="text-lg text-red-700">
                        Silakan cek kembali halaman ini secara berkala untuk informasi gelombang pendaftaran selanjutnya.
                    </p>
                </div>
            </div>
        </div>
    @endif
    <div class="p-10 mb-6 bg-white rounded-lg shadow-md">
        <div class="container-fluid py-5 text-center">
            
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800">
                Sistem Informasi PPDB Online
                <br>
                <span class="text-blue-600">SMK Pejantan Tangguh</span>
            </h1>
            
            <p class="mt-4 text-xl text-gray-600 max-w-2xl mx-auto">
                Membentuk Generasi Tangguh, Kreatif, dan Siap Kerja. Kami adalah sekolah kejuruan yang berfokus pada teknologi dan industri, siap mengantarkan Anda ke masa depan.
            </p>
            
        </div>
    </div>

    <div class="p-10 mb-6 bg-white rounded-lg shadow-md">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Jurusan Unggulan Kami</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border p-4 rounded-lg">
                <h3 class="text-xl font-semibold text-blue-600">Rekayasa Perangkat Lunak (RPL)</h3>
                <p class="mt-2 text-gray-600">Fokus pada pengembangan software, web development, dan mobile apps.</p>
            </div>
            <div class="border p-4 rounded-lg">
                <h3 class="text-xl font-semibold text-blue-600">Teknik Kendaraan Ringan (TKR)</h3>
                <p class="mt-2 text-gray-600">Mempelajari perawatan, perbaikan, dan teknologi terbaru otomotif.</p>
            </div>
            <div class="border p-4 rounded-lg">
                <h3 class="text-xl font-semibold text-blue-600">Teknik Pemesinan (TPM)</h3>
                <p class="mt-2 text-gray-600">Ahli dalam mengoperasikan mesin bubut, CNC, dan manufaktur presisi.</p>
            </div>
        </div>
    </div>
@endsection