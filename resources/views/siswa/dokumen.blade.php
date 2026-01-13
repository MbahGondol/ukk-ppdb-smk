@extends('layouts.app')

@section('title', 'Upload Dokumen')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('siswa.dashboard') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4 transition">
            &larr; Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Kelengkapan Dokumen</h1>
        <p class="text-gray-600">Silakan unggah dokumen asli yang di-scan (PDF/JPG/PNG). Maksimal 2MB per file.</p>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    {{-- Error Validasi --}}
    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        {{-- List Dokumen Wajib --}}

        <div class="divide-y divide-gray-200">
            @foreach($daftarDokumen as $item)
                @php
                    // Cek apakah dokumen ini sudah ada di database
                    $uploaded = $dokumen->has($item);
                    $dataDokumen = $uploaded ? $dokumen[$item] : null;
                @endphp

                <div class="p-6 flex flex-col md:flex-row md:items-center justify-between hover:bg-gray-50 transition">
                    <div class="flex-1">
                        <div class="flex items-center flex-wrap gap-2">
                            <h3 class="text-lg font-bold text-gray-800">{{ $item }}</h3>
                            
                            {{-- LOGIKA TAMPILAN STATUS --}}
                            @if($uploaded)
                                {{-- Jika Sudah Upload: Tampilkan Badge Hijau + Link Lihat File --}}
                                <span class="px-2 py-1 text-xs font-bold text-green-700 bg-green-100 rounded-full flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Sudah Diupload
                                </span>
                                
                                <span class="text-gray-300">|</span>

                                <a href="{{ route('siswa.dokumen.show', $dataDokumen->id) }}?t={{ time() }}" target="_blank" class="text-xs flex items-center text-blue-600 hover:text-blue-800 font-semibold underline transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat File
                                </a>
                            @else
                                {{-- Jika Belum Upload: Tampilkan Badge Merah --}}
                                <span class="px-2 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full">
                                    Belum Ada
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mt-1">
                            @if($uploaded)
                                <span class="text-xs text-gray-400">Diupload: {{ $dataDokumen->updated_at->format('d M Y H:i') }}</span>
                            @else
                                Wajib diunggah. Format PDF/JPG.
                            @endif
                        </p>
                    </div>

                    {{-- FORM UPLOAD (Kanan) --}}
                    <div class="mt-4 md:mt-0 md:ml-6">
                        <form action="{{ route('siswa.dokumen.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center">
                            @csrf
                            {{-- PENTING: name="tipe_dokumen" agar sesuai controller --}}
                            <input type="hidden" name="tipe_dokumen" value="{{ $item }}">
                            
                            <div class="relative">
                                <input type="file" name="file_dokumen" id="file_{{ $loop->index }}" class="hidden" onchange="document.getElementById('btn_submit_{{ $loop->index }}').click();" accept=".pdf,.jpg,.jpeg,.png">
                                <label for="file_{{ $loop->index }}" class="cursor-pointer inline-flex items-center px-4 py-2 border {{ $uploaded ? 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50' : 'border-blue-600 text-white bg-blue-600 hover:bg-blue-700' }} text-sm font-medium rounded-md shadow-sm transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    {{ $uploaded ? 'Ganti File' : 'Pilih File' }}
                                </label>
                            </div>
                            <button type="submit" id="btn_submit_{{ $loop->index }}" class="hidden"></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection