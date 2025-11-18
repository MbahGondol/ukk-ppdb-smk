@extends('layouts.app')

@section('title', 'Selamat Datang di PPDB Online')

@section('content')
    <div class="p-10 mb-6 bg-white rounded-lg shadow-md">
        <div class="container-fluid py-5">
            <h1 class="text-4xl font-bold text-gray-800">Selamat Datang!</h1>
            <p class="mt-4 text-xl text-gray-600">
                Ini adalah halaman utama PPDB SMK Online.
            </p>

            <div class="mt-8">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
                    Ini Tombol Tailwind!
                </button>
                <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg ml-4">
                    Login
                </a>
                <a href="{{ route('register') }}" class="bg-cyan-500 hover:bg-cyan-700 text-white font-bold py-3 px-6 rounded-lg text-lg ml-4">
                    Register
                </a>
            </div>
        </div>
    </div>
@endsection