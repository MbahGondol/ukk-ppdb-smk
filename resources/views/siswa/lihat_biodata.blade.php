@extends('layouts.app')

@section('title', 'Biodata Saya')

@section('content')
<div class="max-w-4xl mx-auto pb-10">
    
    {{-- HEADER & TOMBOL AKSI --}}
    <div class="flex justify-between items-center mb-6 print:hidden">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Biodata Pendaftaran</h1>
            <p class="text-gray-500 text-sm mt-1">Review data diri yang telah tersimpan.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('siswa.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                &larr; Kembali
            </a>
            {{-- Tombol Print Javascript Sederhana --}}
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Halaman
            </button>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 print:shadow-none print:border-none">
        
        {{-- CARD HEADER --}}
        <div class="px-6 py-4 bg-blue-600 border-b border-gray-200 flex justify-between items-center print:bg-white print:border-b-2 print:border-black">
            <span class="text-white font-bold text-lg print:text-black">Data Diri & Akademik</span>
            <span class="bg-white text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase print:border print:border-black print:text-black">
                Status: {{ $siswa->status_pendaftaran }}
            </span>
        </div>

        <div class="p-6">
            {{-- 1. INFORMASI AKUN --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Informasi Akun</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500 block">Nama Akun</span>
                        <span class="font-medium">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 block">Email Login</span>
                        <span class="font-medium">{{ $user->email }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. DETAIL PENDAFTARAN --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Detail Pendaftaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 print:bg-white print:border-gray-300">
                        <span class="text-sm text-gray-500 block mb-1">Gelombang Pendaftaran</span>
                        <span class="font-bold text-lg text-blue-800 print:text-black">
                            {{ $siswa->gelombang->nama_gelombang }}
                        </span>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg border border-green-100 print:bg-white print:border-gray-300">
                        <span class="text-sm text-gray-500 block mb-1">Promo yang Didapat</span>
                        @if($siswa->promo)
                            <span class="font-bold text-lg text-green-800 print:text-black">
                                {{ $siswa->promo->nama_promo }}
                            </span>
                            <span class="block text-sm text-green-600 mt-1 print:text-black">
                                (Potongan: Rp {{ number_format($siswa->promo->potongan, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="font-medium text-gray-600 italic">- Tidak mendapatkan promo -</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. DATA PRIBADI (SUDAH DIPERBAIKI BUG DIV-NYA) --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Data Pribadi Siswa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                    <div><span class="text-sm text-gray-500 block">No. Pendaftaran</span> <span class="font-bold text-blue-600 print:text-black">{{ $siswa->no_pendaftaran }}</span></div>
                    <div><span class="text-sm text-gray-500 block">NISN</span> <span class="font-medium">{{ $siswa->nisn }}</span></div>
                    <div><span class="text-sm text-gray-500 block">NIK</span> <span class="font-medium">{{ $siswa->nik }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Nama Lengkap</span> <span class="font-medium">{{ $siswa->nama_lengkap }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Jenis Kelamin</span> <span class="font-medium">{{ $siswa->jenis_kelamin }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Tempat, Tgl Lahir</span> <span class="font-medium">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d F Y') }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Agama</span> <span class="font-medium">{{ $siswa->agama }}</span></div>
                    <div><span class="text-sm text-gray-500 block">No. HP (WA)</span> <span class="font-medium">{{ $siswa->no_hp }}</span></div>
                    
                    <div><span class="text-sm text-gray-500 block">Anak Ke-</span> <span class="font-medium">{{ $siswa->anak_ke ?? '-' }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Jumlah Saudara</span> <span class="font-medium">{{ $siswa->jumlah_saudara ?? '-' }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Tinggi Badan</span> <span class="font-medium">{{ $siswa->tinggi_badan ? $siswa->tinggi_badan . ' cm' : '-' }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Berat Badan</span> <span class="font-medium">{{ $siswa->berat_badan ? $siswa->berat_badan . ' kg' : '-' }}</span></div>
                </div>
            </div>

            {{-- 4. ALAMAT --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Alamat Lengkap</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                    <div class="md:col-span-2">
                        <span class="text-sm text-gray-500 block">Alamat Jalan</span>
                        <span class="font-medium">{{ $siswa->alamat }}</span>
                    </div>
                    <div><span class="text-sm text-gray-500 block">RT / RW</span> <span class="font-medium">{{ $siswa->rt_rw }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Kelurahan/Desa</span> <span class="font-medium">{{ $siswa->desa_kelurahan }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Kecamatan</span> <span class="font-medium">{{ $siswa->kecamatan }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Kota/Kabupaten</span> <span class="font-medium">{{ $siswa->kota_kab }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Kode Pos</span> <span class="font-medium">{{ $siswa->kode_pos }}</span></div>
                </div>
            </div>

            {{-- 5. PILIHAN JURUSAN --}}
            <div class="mb-8 bg-blue-50 p-4 rounded-lg border border-blue-100 print:bg-white print:border-gray-300">
                <h3 class="text-lg font-bold text-blue-800 mb-2 print:text-black">Pilihan Jurusan</h3>
                <p class="text-gray-700">
                    {{ $siswa->jurusan->nama_jurusan }} 
                    <span class="mx-2 text-gray-400">|</span> 
                    Kelas: <span class="font-semibold">{{ $siswa->tipeKelas->nama_tipe_kelas }}</span>
                </p>
            </div>

            {{-- 6. SEKOLAH ASAL --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Sekolah Asal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><span class="text-sm text-gray-500 block">Nama Sekolah</span> <span class="font-medium">{{ $siswa->asal_sekolah }}</span></div>
                    <div><span class="text-sm text-gray-500 block">Tahun Lulus</span> <span class="font-medium">{{ $siswa->tahun_lulus }}</span></div>
                </div>
            </div>

            {{-- 7. DATA PENANGGUNG JAWAB --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Data Penanggung Jawab</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    @forelse ($siswa->penanggungJawab as $pj)
                        <div class="border rounded-lg p-4 bg-gray-50 relative print:bg-white print:border-gray-300">
                            <span class="absolute top-0 right-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-1 rounded-bl-lg uppercase print:border print:border-gray-400">
                                {{ $pj->hubungan }}
                            </span>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6">
                                <div>
                                    <span class="text-xs text-gray-500 uppercase font-bold">Nama Lengkap</span>
                                    <div class="font-medium text-gray-900">{{ $pj->nama_lengkap }}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase font-bold">NIK</span>
                                    <div class="font-medium text-gray-900">{{ $pj->nik ?? '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase font-bold">Tahun Lahir</span>
                                    <div class="font-medium text-gray-900">{{ $pj->tahun_lahir ?? '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase font-bold">Pendidikan Terakhir</span>
                                    <div class="font-medium text-gray-900">{{ $pj->pendidikan_terakhir ?? '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase font-bold">Pekerjaan</span>
                                    <div class="font-medium text-gray-900">{{ $pj->pekerjaan ?? '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase font-bold">Penghasilan Bulanan</span>
                                    <div class="font-medium text-gray-900">
                                        {{ $pj->penghasilan_bulanan ? 'Rp ' . number_format($pj->penghasilan_bulanan, 0, ',', '.') : '-' }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase font-bold">No. HP</span>
                                    <div class="font-medium text-gray-900">{{ $pj->no_hp ?? '-' }}</div>
                                </div>
                                
                                @if ($pj->hubungan == 'Wali')
                                    <div class="md:col-span-2">
                                        <span class="text-xs text-gray-500 uppercase font-bold">Alamat Wali</span>
                                        <div class="font-medium text-gray-900">{{ $pj->alamat_wali ?? '-' }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 italic py-4">Belum ada data penanggung jawab.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection