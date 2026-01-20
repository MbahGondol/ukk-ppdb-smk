@extends('layouts.admin')

@section('title', 'Detail Siswa')
@section('header', 'Detail Lengkap Pendaftar')

@section('content')
    @php
        use Illuminate\Support\Facades\Storage;
        use Carbon\Carbon;
        use App\Enums\StatusPendaftaran; 
    @endphp

    <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded inline-flex items-center mb-6">
        &larr; Kembali
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: SEMUA DATA SISWA (Data Diri, Ortu, Dokumen, Pembayaran) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- 1. Status Header --}}
            <div class="bg-white shadow rounded-lg overflow-hidden border-l-4 
                {{ $siswa->status_pendaftaran == 'Terdaftar' ? 'border-yellow-400' : 
                   ($siswa->status_pendaftaran == 'Ditolak' ? 'border-red-500' : 'border-green-500') }}">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $siswa->nama_lengkap }}</h2>
                        <p class="text-sm text-gray-500">No. Daftar: <span class="font-mono font-bold">{{ $siswa->no_pendaftaran }}</span></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-bold 
                        {{ $siswa->status_pendaftaran == 'Terdaftar' ? 'bg-yellow-100 text-yellow-800' : 
                           ($siswa->status_pendaftaran == 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800') }}">
                        {{ $siswa->status_pendaftaran }}
                    </span>
                </div>
                
                @if (($siswa->status_pendaftaran == 'Ditolak' || $siswa->status_pendaftaran == 'Melengkapi Berkas') && $siswa->catatan_admin)
                    <div class="bg-red-50 p-4 border-t border-red-100">
                        <strong class="text-red-800 block mb-1">Catatan Admin / Alasan Penolakan:</strong>
                        <p class="text-red-700">{{ $siswa->catatan_admin }}</p>
                    </div>
                @endif
            </div>

            {{-- 2. Data Pribadi --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 uppercase text-sm tracking-wider">
                    Data Pribadi Siswa
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                    <div><span class="block text-gray-500 text-xs">NISN</span> <span class="font-semibold">{{ $siswa->nisn }}</span></div>
                    <div><span class="block text-gray-500 text-xs">NIK</span> <span class="font-semibold">{{ $siswa->nik }}</span></div>
                    <div><span class="block text-gray-500 text-xs">Jenis Kelamin</span> <span class="font-semibold">{{ $siswa->jenis_kelamin }}</span></div>
                    <div><span class="block text-gray-500 text-xs">Tempat, Tgl Lahir</span> <span class="font-semibold">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d M Y') }}</span></div>
                    <div><span class="block text-gray-500 text-xs">Agama</span> <span class="font-semibold">{{ $siswa->agama }}</span></div>
                    <div><span class="block text-gray-500 text-xs">No. HP</span> <span class="font-semibold">{{ $siswa->no_hp }}</span></div>
                    
                    <div class="border-t pt-2 mt-2 md:col-span-2 grid grid-cols-2 gap-4">
                        <div><span class="block text-gray-500 text-xs">Anak Ke-</span> <span class="font-semibold">{{ $siswa->anak_ke ?? '-' }}</span></div>
                        <div><span class="block text-gray-500 text-xs">Jumlah Saudara</span> <span class="font-semibold">{{ $siswa->jumlah_saudara ?? '-' }}</span></div>
                        <div><span class="block text-gray-500 text-xs">Tinggi Badan</span> <span class="font-semibold">{{ $siswa->tinggi_badan ? $siswa->tinggi_badan . ' cm' : '-' }}</span></div>
                        <div><span class="block text-gray-500 text-xs">Berat Badan</span> <span class="font-semibold">{{ $siswa->berat_badan ? $siswa->berat_badan . ' kg' : '-' }}</span></div>
                    </div>
                </div>
            </div>

            {{-- 3. Alamat --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 uppercase text-sm tracking-wider">
                    Alamat Tempat Tinggal
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                    <div class="md:col-span-2">
                        <span class="block text-gray-500 text-xs">Alamat Jalan</span>
                        <span class="font-semibold">{{ $siswa->alamat }}</span>
                    </div>
                    <div><span class="block text-gray-500 text-xs">RT / RW</span> <span class="font-semibold">{{ $siswa->rt_rw }}</span></div>
                    <div><span class="block text-gray-500 text-xs">Kelurahan</span> <span class="font-semibold">{{ $siswa->desa_kelurahan }}</span></div>
                    <div><span class="block text-gray-500 text-xs">Kecamatan</span> <span class="font-semibold">{{ $siswa->kecamatan }}</span></div>
                    <div><span class="block text-gray-500 text-xs">Kabupaten/Kota</span> <span class="font-semibold">{{ $siswa->kota_kab }}</span></div>
                    <div><span class="block text-gray-500 text-xs">Kode Pos</span> <span class="font-semibold">{{ $siswa->kode_pos }}</span></div>
                </div>
            </div>

            {{-- 4. Akademik --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 uppercase text-sm tracking-wider">
                    Akademik & Pilihan
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                    <div><span class="block text-gray-500 text-xs">Sekolah Asal</span> <span class="font-semibold">{{ $siswa->asal_sekolah }}</span></div>
                    <div><span class="block text-gray-500 text-xs">Tahun Lulus</span> <span class="font-semibold">{{ $siswa->tahun_lulus }}</span></div>
                    
                    <div class="md:col-span-2 mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-blue-50 rounded border border-blue-100">
                            <span class="block text-blue-500 text-xs uppercase font-bold">Jurusan Pilihan</span>
                            <span class="text-lg font-bold text-blue-800">
                                {{ $siswa->jurusan->nama_jurusan }} 
                                <span class="text-sm font-normal text-gray-600">({{ $siswa->tipeKelas->nama_tipe_kelas ?? 'Reguler' }})</span>
                            </span>
                        </div>
                        <div class="p-3 bg-green-50 rounded border border-green-100">
                            <span class="block text-green-600 text-xs uppercase font-bold">Gelombang & Promo</span>
                            <div class="text-gray-800 font-semibold">
                                {{ $siswa->gelombang->nama_gelombang }}
                            </div>
                            <div class="text-sm mt-1">
                                @if($siswa->promo)
                                    <span class="text-red-600 font-bold">🔥 {{ $siswa->promo->nama_promo }}</span>
                                    <span class="text-gray-500 text-xs block">(Potongan: Rp {{ number_format($siswa->promo->potongan, 0, ',', '.') }})</span>
                                @else
                                    <span class="text-gray-400 italic">- Tidak ada promo -</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. Penanggung Jawab --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 uppercase text-sm tracking-wider">
                    Data Penanggung Jawab
                </div>
                <div class="p-6 space-y-6">
                    @foreach($siswa->penanggungJawab as $pj)
                        <div class="border rounded-lg p-4 relative hover:bg-gray-50 transition">
                            <span class="absolute top-0 right-0 bg-gray-200 text-gray-600 text-xs font-bold px-2 py-1 rounded-bl-lg uppercase">
                                {{ $pj->hubungan }}
                            </span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-4 text-sm">
                                <div><span class="text-xs text-gray-500 block">Nama Lengkap</span> <span class="font-bold">{{ $pj->nama_lengkap }}</span></div>
                                <div><span class="text-xs text-gray-500 block">NIK</span> <span class="font-semibold">{{ $pj->nik ?? '-' }}</span></div>
                                <div><span class="text-xs text-gray-500 block">Tahun Lahir</span> <span class="font-semibold">{{ $pj->tahun_lahir ?? '-' }}</span></div>
                                <div><span class="text-xs text-gray-500 block">Pendidikan</span> <span class="font-semibold">{{ $pj->pendidikan_terakhir ?? '-' }}</span></div>
                                <div><span class="text-xs text-gray-500 block">Pekerjaan</span> <span class="font-semibold">{{ $pj->pekerjaan ?? '-' }}</span></div>
                                <div><span class="text-xs text-gray-500 block">Penghasilan</span> <span class="font-semibold text-green-700">{{ $pj->penghasilan_bulanan ? 'Rp ' . number_format($pj->penghasilan_bulanan, 0, ',', '.') : '-' }}</span></div>
                                <div><span class="text-xs text-gray-500 block">No. HP</span> <span class="font-semibold">{{ $pj->no_hp ?? '-' }}</span></div>
                                @if($pj->hubungan == 'Wali')
                                    <div class="md:col-span-2"><span class="text-xs text-gray-500 block">Alamat Wali</span> <span class="font-semibold">{{ $pj->alamat_wali ?? '-' }}</span></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 6. DOKUMEN SISWA (DIPINDAHKAN KE SINI) --}}
            <div class="bg-white shadow rounded-lg overflow-hidden mt-6">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 uppercase text-sm tracking-wider">
                    Dokumen Siswa
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($siswa->dokumen as $dokumen)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="truncate pr-2">
                                <span class="block text-sm font-medium text-gray-900">{{ $dokumen->tipe_dokumen }}</span>
                                <span class="block text-xs text-gray-500 truncate">{{ $dokumen->nama_asli_file }}</span>
                            </div>
                            <a href="{{ route('admin.dokumen.show', $dokumen->id) }}" target="_blank" class="flex-shrink-0 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold hover:bg-blue-200">
                                Lihat
                            </a>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-gray-500">Tidak ada dokumen.</div>
                    @endforelse
                </div>
            </div>

            {{-- 7. BUKTI PEMBAYARAN (DIPINDAHKAN KE SINI) --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 uppercase text-sm tracking-wider">
                    Bukti Pembayaran
                </div>
                <div class="divide-y divide-gray-100">
                    @if($siswa->rencanaPembayaran)
                        @forelse($siswa->rencanaPembayaran->pembayaran as $bayar)
                            <div class="p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-500">{{ $bayar->tanggal_pembayaran->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800">{{ $bayar->status }}</span>
                                    @if($bayar->buktiPembayaran)
                                        <a href="{{ Storage::disk('public')->url($bayar->buktiPembayaran->file_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Bukti</a>
                                    @endif
                                </div>
                            </div>
                        @empty
                             <div class="p-4 text-center text-sm text-gray-500">Belum ada upload bayar.</div>
                        @endforelse
                    @else
                        <div class="p-4 text-center text-sm text-gray-500">Belum ada tagihan.</div>
                    @endif
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: KHUSUS PANEL AKSI (STICKY) --}}
        <div class="space-y-6">
            
            @if ($siswa->status_pendaftaran == 'Terdaftar')
                <div class="bg-white shadow-lg rounded-lg overflow-hidden border-2 border-blue-500 sticky top-6">
                    <div class="px-6 py-4 bg-blue-600 text-white font-bold text-lg text-center">
                        Panel Aksi Admin
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4 text-center">Cek seluruh data di samping. Pilih aksi:</p>
                        
                        <form action="{{ route('admin.verifikasi.updateStatus', $siswa->id) }}" method="POST" class="mb-6">
                            @csrf
                            <input type="hidden" name="aksi" value="terima">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded shadow transition flex justify-center items-center" onclick="return confirm('Yakin ingin MENERIMA siswa ini secara RESMI?')">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                ✔ TERIMA SISWA (RESMI)
                            </button>
                        </form>

                        <div class="relative flex py-3 items-center">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-400 text-xs">ATAU</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>

                        <form action="{{ route('admin.verifikasi.updateStatus', $siswa->id) }}" method="POST">
                            @csrf
                            
                            <label class="block text-xs font-bold text-gray-700 mb-2">Catatan / Alasan (Wajib diisi):</label>
                            <textarea name="catatan_admin" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm mb-4" placeholder="Contoh: Foto Ijazah buram, mohon upload ulang..." required></textarea>
                            
                            <div class="grid grid-cols-2 gap-2">
                                <button type="submit" name="aksi" value="revisi" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-2 rounded text-sm flex justify-center items-center" onclick="return confirm('Minta siswa memperbaiki data?')">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Minta Revisi
                                </button>

                                <button type="submit" name="aksi" value="tolak" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded text-sm flex justify-center items-center" onclick="return confirm('Yakin MENOLAK siswa ini secara PERMANEN?')">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection