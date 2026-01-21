@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="flex justify-center mt-10">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        
        <div class="flex justify-center mb-4">
            <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-center mb-2">Verifikasi Email Anda</h2>
        
        <div class="text-gray-600 text-center mb-6 text-sm">
            Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan ke email Anda.
        </div>

        @if (session('message') == 'Link verifikasi telah dikirim ulang!')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                Link verifikasi baru telah dikirim ke alamat email Anda.
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm font-semibold underline focus:outline-none">
                    Kirim Ulang Link Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm underline focus:outline-none">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection