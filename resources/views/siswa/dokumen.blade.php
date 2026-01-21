@extends('layouts.app')

@section('title', 'Upload Dokumen')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <a href="{{ route('siswa.dashboard') }}" class="text-gray-500 hover:text-blue-600 flex items-center mb-2 transition text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-extrabold text-gray-800">Berkas Persyaratan</h1>
            <p class="text-gray-600 mt-1">Lengkapi dokumen di bawah ini untuk verifikasi panitia.</p>
        </div>
        
        {{-- Progress Bar Mini --}}
        <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 md:w-1/3">
            <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                <span>Kelengkapan</span>
                <span>{{ $dokumen->count() }} Item Terunggah</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                {{-- Hitung persentase kasar (misal total 7 item) --}}
                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ ($dokumen->count() / count($daftarDokumen)) * 100 }}%"></div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-start">
            <svg class="w-6 h-6 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- GRID SYSTEM (BEDA DENGAN TEMANMU YANG PAKAI LIST) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach($daftarDokumen as $item)
            @php
                // --- 1. LOGIKA STATUS WAJIB/OPSIONAL ---
                $wajib = true; // Default semua wajib
                $hidden = false; // Default semua muncul

                // LOGIKA: KTP Wali
                if ($item == 'KTP Wali') {
                    // Jika tinggal sama Ortu, KTP Wali jadi OPSIONAL (atau disembunyikan)
                    if ($calonSiswa->tinggal_bersama == 'ortu') {
                        $wajib = false; 
                        // $hidden = true; // Uncomment baris ini jika ingin kartunya hilang total
                    }
                }

                // LOGIKA: KTP Ayah & Ibu (Kebalikannya)
                if (($item == 'KTP Ayah' || $item == 'KTP Ibu') && $calonSiswa->tinggal_bersama == 'wali') {
                    // Jika tinggal sama Wali, KTP Ortu jadi OPSIONAL
                    $wajib = false; 
                }

                // Skip jika diset hidden
                if ($hidden) continue;

                // --- 2. SETUP TAMPILAN ---
                $uploaded = $dokumen->has($item);
                $dataDokumen = $uploaded ? $dokumen[$item] : null;
                
                // Icon Default
                $icon = 'doc';
                
                // Text Deskripsi (Dinamis berdasarkan status Wajib)
                if ($wajib) {
                    $desc = 'Wajib diunggah. Format PDF/JPG (Max 2MB).';
                    $badgeWajib = '<span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold ml-2">WAJIB</span>';
                } else {
                    $desc = 'Opsional / Tidak Wajib.';
                    $badgeWajib = '<span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-bold ml-2">OPSIONAL</span>';
                }

                // Custom Icon biar cantik
                if(str_contains($item, 'KTP')) { $icon = 'id'; }
                if(str_contains($item, 'Foto')) { $icon = 'photo'; }
                if(str_contains($item, 'Ijazah')) { $icon = 'academic'; }

                // Status Badge (Sudah/Belum)
                if($uploaded) {
                    $badgeColor = 'bg-green-100 text-green-700';
                    $badgeText = 'Selesai';
                } else {
                    $badgeColor = $wajib ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-500';
                    $badgeText = $wajib ? 'Belum Ada' : 'Boleh Kosong';
                }
            @endphp
            
            {{-- START CARD (Tampilan HTML tetap sama, cuma variabelnya yang main) --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200 overflow-hidden flex flex-col">
                
                {{-- Header --}}
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
                    <div class="flex items-center space-x-3 w-full">
                        <div class="p-2 bg-white rounded-lg border border-gray-200 text-blue-600 shadow-sm flex-shrink-0">
                             {{-- Icon SVG Sederhana --}}
                            @if($icon == 'id') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.5 2-2 2h4c-1.5 0-2-1.116-2-2z"></path></svg>
                            @elseif($icon == 'photo') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @else <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            @endif
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-gray-800 text-sm">{{ $item }}</h3>
                                {!! $badgeWajib !!} {{-- Render Badge WAJIB/OPSIONAL --}}
                            </div>
                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] uppercase tracking-wide font-bold rounded-full {{ $badgeColor }}">
                                {{ $badgeText }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div class="mb-3">
                        @if($uploaded)
                            <div class="text-xs text-gray-500 mb-2 p-2 bg-blue-50 rounded border border-blue-100">
                                <span class="font-semibold text-blue-800">File:</span> {{ Str::limit($dataDokumen->nama_asli_file, 18) }}
                            </div>
                            <a href="{{ route('siswa.dokumen.show', $dataDokumen->id) }}?t={{ time() }}" target="_blank" class="w-full text-center block text-xs border border-blue-600 text-blue-600 py-1.5 rounded hover:bg-blue-50 transition font-semibold">
                                Lihat Dokumen
                            </a>
                        @else
                            <p class="text-xs text-gray-500 leading-relaxed">{{ $desc }}</p>
                        @endif
                    </div>

                    {{-- Form Upload --}}
                    <form action="{{ route('siswa.dokumen.store') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <input type="hidden" name="tipe_dokumen" value="{{ $item }}">
                        <input type="file" name="file_dokumen" id="file_{{ $loop->index }}" class="hidden" onchange="this.form.submit()" accept=".pdf,.jpg,.jpeg,.png">
                        
                        <label for="file_{{ $loop->index }}" class="cursor-pointer w-full block text-center py-2 px-4 rounded-lg text-xs font-bold transition border border-dashed {{ $uploaded ? 'border-gray-300 text-gray-500 hover:bg-gray-50' : 'border-blue-400 text-blue-600 bg-blue-50 hover:bg-blue-100' }}">
                            {{ $uploaded ? 'Ganti File' : '+ Unggah' }}
                        </label>
                    </form>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection