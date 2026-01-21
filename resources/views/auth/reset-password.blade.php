@extends('layouts.app')

@section('title', 'Buat Password Baru')

@section('content')
<div class="flex justify-center mt-10">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center mb-6">Password Baru</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Email:</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required readonly class="w-full px-3 py-2 border bg-gray-100 rounded-lg cursor-not-allowed">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Password Baru:</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Konfirmasi Password:</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border rounded-lg">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-lg">
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection