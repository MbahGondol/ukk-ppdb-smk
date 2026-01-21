@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    {{-- 1. HEADER SECTION: Sambutan Personal --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-lg p-8 text-white flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">Halo, {{ Auth::user()->name }}! 👋</h1>
            <p class="mt-2 text-blue-100 opacity-90">
                Selamat datang di Portal PPDB. Pantau status pendaftaranmu di sini.
            </p>
        </div>
        {{-- Status Badge Besar --}}
        @if($calonSiswa)
            <div class="hidden md:block text-right">
                <span class="text-sm uppercase tracking-wider opacity-75">Status Saat Ini</span>
                <div class="font-bold text-2xl bg-white/20 px-4 py-2 rounded-lg mt-1 backdrop-blur-sm">
                    {{ $calonSiswa->status_pendaftaran }}
                </div>
            </div>
        @endif
    </div>

    @if(!$calonSiswa)
        {{-- STATE 0: BELUM DAFTAR --}}
        <div class="bg-white rounded-xl shadow-sm p-10 text-center border border-gray-100">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Mari Mulai Pendaftaran!</h2>
            <p class="text-gray-500 mt-2 max-w-md mx-auto">Anda belum terdaftar. Silakan isi formulir biodata awal untuk memulai proses seleksi.</p>
            <a href="{{ route('siswa.pendaftaran.create') }}" class="mt-8 inline-flex items-center px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition transform hover:-translate-y-1 shadow-lg">
                Isi Formulir Pendaftaran &rarr;
            </a>
        </div>

    @else
        {{-- STATE 1: SUDAH DAFTAR (TAMPILKAN PROGRESS & MENU) --}}
        
        {{-- Alert Admin Message (Jika Ada) --}}
        @if($calonSiswa->catatan_admin)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start">
                <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <h3 class="font-bold text-red-800">Catatan Penting dari Admin:</h3>
                    <p class="text-red-700 text-sm mt-1">{{ $calonSiswa->catatan_admin }}</p>
                </div>
            </div>
        @endif

        {{-- PROGRESS TRACKER (Simple) --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Progres Pendaftaran</h3>
            <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                <div class="bg-green-500 h-4 rounded-full transition-all duration-1000 ease-out" style="width: {{ $calonSiswa->progres_pendaftaran }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 font-medium">
                <span>Daftar</span>
                <span>Berkas</span>
                <span>Verifikasi</span>
                <span>Diterima</span>
            </div>
        </div>

        {{-- GRID ACTION CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- CARD 1: BIODATA (Selalu Aktif) --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border-t-4 border-blue-500 flex flex-col h-full">
                <div class="flex-1">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">1. Biodata Diri</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Data diri dasar, asal sekolah, dan pemilihan jurusan.</p>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    @if($calonSiswa->status_pendaftaran == 'Melengkapi Berkas')
                        <a href="{{ route('siswa.pendaftaran.create') }}" class="block w-full text-center py-2 px-4 bg-yellow-100 text-yellow-700 rounded font-semibold hover:bg-yellow-200 transition">
                            Edit Biodata
                        </a>
                    @else
                        <a href="{{ route('siswa.biodata') }}" class="block w-full text-center py-2 px-4 border border-gray-300 text-gray-600 rounded font-semibold hover:bg-gray-50 transition">
                            Lihat Biodata
                        </a>
                    @endif
                </div>
            </div>

            {{-- CARD 2: DOKUMEN (Status Logic) --}}
            @php
                // Logic Tampilan Visual Card
                $isDokumenLengkap = false; // Logic check ke Model Document nanti
                // Contoh simpel:
                $dokumenUploaded = \App\Models\DokumenSiswa::where('calon_siswa_id', $calonSiswa->id)->exists(); 
            @endphp
            
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border-t-4 {{ $dokumenUploaded ? 'border-green-500' : 'border-indigo-500' }} flex flex-col h-full">
                <div class="flex-1">
                    <div class="flex items-center mb-4">
                        <div class="p-3 {{ $dokumenUploaded ? 'bg-green-100 text-green-600' : 'bg-indigo-100 text-indigo-600' }} rounded-lg mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">2. Upload Berkas</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Kartu Keluarga, Akta Kelahiran, dan Ijazah/SKL.</p>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                     <a href="{{ route('siswa.dokumen.index') }}" class="block w-full text-center py-2 px-4 {{ $dokumenUploaded ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-indigo-600 text-white hover:bg-indigo-700' }} rounded font-bold transition shadow-sm">
                        {{ $dokumenUploaded ? 'Cek / Tambah Berkas' : 'Upload Sekarang' }}
                    </a>
                </div>
            </div>

            {{-- CARD 3: PEMBAYARAN (Conditional Logic) --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border-t-4 {{ $calonSiswa->masih_punya_hutang ? 'border-orange-500' : 'border-gray-300' }} flex flex-col h-full relative overflow-hidden">
                {{-- Jika dokumen belum, beri overlay 'Locked' --}}
                @if(!$dokumenUploaded)
                    <div class="absolute inset-0 bg-gray-50 bg-opacity-90 flex items-center justify-center z-10 backdrop-blur-[1px]">
                        <div class="text-center p-4">
                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span class="text-sm font-semibold text-gray-500">Selesaikan Upload Berkas Dulu</span>
                        </div>
                    </div>
                @endif

                <div class="flex-1">
                    <div class="flex items-center mb-4">
                        <div class="p-3 {{ $calonSiswa->masih_punya_hutang ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }} rounded-lg mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">3. Pembayaran</h3>
                    </div>
                    
                    @if($calonSiswa->masih_punya_hutang)
                        <div class="bg-orange-50 text-orange-800 text-xs px-2 py-1 rounded inline-block mb-2 font-bold">
                            ⚠️ Belum Lunas
                        </div>
                    @endif
                    <p class="text-gray-600 text-sm mb-4">Administrasi pendaftaran dan daftar ulang.</p>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('siswa.pembayaran.index') }}" class="block w-full text-center py-2 px-4 {{ $calonSiswa->masih_punya_hutang ? 'bg-orange-600 text-white hover:bg-orange-700' : 'bg-gray-800 text-white hover:bg-gray-900' }} rounded font-bold transition shadow-sm">
                        {{ $calonSiswa->masih_punya_hutang ? 'Bayar Tagihan' : 'Lihat Riwayat' }}
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-100">
            <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Dokumen Kelulusan</h3>
                    <p class="text-sm text-gray-500">Unduh bukti penerimaan Anda setelah memenuhi syarat administrasi.</p>
                </div>

                <div>
                    @if(Auth::user()->calonSiswa->status_pendaftaran == 'Resmi Diterima')
                        {{-- JIKA SUDAH LUNAS / 50% --}}
                        <a href="{{ route('siswa.cetak.bukti') }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Cetak Surat Diterima
                        </a>
                    @else
                        {{-- JIKA BELUM MENCAPAI TARGET --}}
                        <button disabled class="inline-flex items-center px-6 py-3 bg-gray-300 border border-transparent rounded-lg font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Lunasi Minimal 50%
                        </button>
                    @endif
                </div>
            </div>
        </div>

    @endif
</div>
@endsection