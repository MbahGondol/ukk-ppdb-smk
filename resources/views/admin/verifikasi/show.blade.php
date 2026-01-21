@extends('layouts.admin')

@section('title', 'Detail Verifikasi Siswa')
@section('header', 'Detail Lengkap Pendaftar')

@section('content')
    @php
        use Illuminate\Support\Facades\Storage;
        use Carbon\Carbon;
    @endphp

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.verifikasi.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded inline-flex items-center transition">
            &larr; Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: DATA SISWA LENGKAP --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- 1. FOTO & STATUS UTAMA --}}
            <div class="bg-white shadow rounded-lg overflow-hidden border-t-4 border-blue-500">
                <div class="p-6 text-center">
                    @php 
                        $foto = $siswa->dokumen->where('tipe_dokumen', 'Foto Formal')->first(); 
                    @endphp
                    @if($foto)
                        <img src="{{ Storage::url($foto->file_path) }}" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-100 shadow-sm mb-4">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gray-100 mx-auto flex items-center justify-center text-gray-400 mb-4">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    @endif
                    
                    <h2 class="text-xl font-bold text-gray-800">{{ $siswa->nama_lengkap }}</h2>
                    <div class="flex justify-center items-center gap-2 mt-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-mono">{{ $siswa->no_pendaftaran }}</span>
                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded font-mono">{{ $siswa->nisn }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. AKSI VERIFIKASI (Sesuai perbaikan sebelumnya) --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 text-sm uppercase">
                    Keputusan Admin
                </div>
                <div class="p-4">
                    <div class="mb-4 text-center">
                        <span class="px-4 py-2 rounded-lg text-sm font-bold border 
                            {{ $siswa->status_pendaftaran == 'Resmi Diterima' ? 'bg-green-50 text-green-700 border-green-200' : 
                            ($siswa->status_pendaftaran == 'Ditolak' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200') }}">
                            {{ strtoupper($siswa->status_pendaftaran) }}
                        </span>
                    </div>

                    <form action="{{ route('admin.verifikasi.updateStatus', $siswa->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Catatan Admin</label>
                            <textarea name="catatan_admin" rows="2" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500" {{ $siswa->status_pendaftaran == 'Resmi Diterima' ? 'disabled' : '' }}>{{ $siswa->catatan_admin }}</textarea>
                        </div>

                        @if($siswa->status_pendaftaran != 'Resmi Diterima')
                            <div class="grid grid-cols-2 gap-2">
                                <button type="submit" name="aksi" value="terima" class="col-span-2 bg-green-600 hover:bg-green-700 text-white py-2 rounded text-sm font-bold" onclick="return confirm('Yakin terima siswa ini?')">✔ Terima Resmi</button>
                                <button type="submit" name="aksi" value="revisi" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded text-sm font-bold">↺ Minta Revisi</button>
                                <button type="submit" name="aksi" value="tolak" class="bg-red-600 hover:bg-red-700 text-white py-2 rounded text-sm font-bold" onclick="return confirm('Yakin tolak permanen?')">✖ Tolak</button>
                            </div>
                        @else
                            <div class="text-center text-xs text-green-600 font-bold bg-green-50 p-2 rounded">Data Terkunci (Sudah Diterima)</div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- 3. DATA PRIBADI & ALAMAT --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 text-sm uppercase flex justify-between">
                    <span>Data Pribadi</span>
                    <span class="text-xs font-normal normal-case text-gray-500">NIK: {{ $siswa->nik }}</span>
                </div>
                <div class="p-4 text-sm space-y-3">
                    {{-- Baris Data --}}
                    <div class="grid grid-cols-2 gap-2 border-b border-dashed pb-2">
                        <div><div class="text-xs text-gray-500">Tempat, Tgl Lahir</div><div class="font-semibold">{{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') }}</div></div>
                        <div><div class="text-xs text-gray-500">Jenis Kelamin</div><div class="font-semibold">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div></div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 border-b border-dashed pb-2">
                        <div><div class="text-xs text-gray-500">Agama</div><div class="font-semibold">{{ $siswa->agama }}</div></div>
                        <div><div class="text-xs text-gray-500">Anak Ke / Jml Sdr</div><div class="font-semibold">{{ $siswa->anak_ke }} dari {{ $siswa->jumlah_saudara }}</div></div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 border-b border-dashed pb-2">
                        <div><div class="text-xs text-gray-500">Tinggi / Berat</div><div class="font-semibold">{{ $siswa->tinggi_badan }} cm / {{ $siswa->berat_badan }} kg</div></div>
                        <div><div class="text-xs text-gray-500">No. HP / WA</div><div class="font-semibold text-blue-600">{{ $siswa->no_hp }}</div></div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 mb-1">Alamat Lengkap</div>
                        <div class="bg-gray-50 p-2 rounded text-gray-700 text-xs leading-relaxed border">
                            {{ $siswa->alamat }}<br>
                            RT {{ $siswa->rt_rw }}, Kel. {{ $siswa->desa_kelurahan }}<br>
                            Kec. {{ $siswa->kecamatan }}, {{ $siswa->kota_kab }}<br>
                            Prov. {{ $siswa->provinsi }} - {{ $siswa->kode_pos }}
                        </div>
                    </div>

                    <div class="pt-2">
                        <div class="text-xs text-gray-500">Asal Sekolah (Lulus Thn)</div>
                        <div class="font-bold text-gray-800">{{ $siswa->asal_sekolah }} <span class="font-normal text-gray-500">({{ $siswa->tahun_lulus }})</span></div>
                    </div>
                </div>
            </div>

            {{-- 4. DATA ORANG TUA / WALI (Dinamis dari Relasi) --}}
            @php
                // Mengambil data orang tua dari relasi hasMany
                $ayah = $siswa->penanggungJawab->where('hubungan', 'Ayah')->first();
                $ibu  = $siswa->penanggungJawab->where('hubungan', 'Ibu')->first();
                $wali = $siswa->penanggungJawab->where('hubungan', 'Wali')->first();
            @endphp

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 text-sm uppercase">
                    Data Orang Tua / Wali
                </div>
                <div class="p-4 space-y-4">
                    
                    @if($wali)
                        {{-- JIKA WALI --}}
                        <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                            <div class="font-bold text-yellow-800 text-xs uppercase mb-2 border-b border-yellow-200 pb-1">Wali Siswa</div>
                            <div class="text-sm">
                                <div class="font-bold">{{ $wali->nama_lengkap }}</div>
                                <div class="text-xs text-gray-600">{{ $wali->pekerjaan }}</div>
                                <div class="text-xs mt-1">HP: {{ $wali->no_hp }}</div>
                                <div class="text-xs mt-1 italic">{{ $wali->alamat_wali }}</div>
                            </div>
                        </div>
                    @else
                        {{-- JIKA ORTU (AYAH & IBU) --}}
                        @if($ayah)
                        <div class="relative pl-3 border-l-2 border-blue-400">
                            <div class="font-bold text-blue-800 text-xs uppercase mb-1">Ayah</div>
                            <div class="text-sm font-semibold">{{ $ayah->nama_lengkap }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $ayah->pekerjaan }} • Penghasilan: {{ $ayah->penghasilan_bulanan }}
                            </div>
                            <div class="text-xs text-gray-500">HP: {{ $ayah->no_hp }}</div>
                        </div>
                        @endif

                        @if($ibu)
                        <div class="relative pl-3 border-l-2 border-pink-400 mt-3">
                            <div class="font-bold text-pink-800 text-xs uppercase mb-1">Ibu</div>
                            <div class="text-sm font-semibold">{{ $ibu->nama_lengkap }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $ibu->pekerjaan }} • Penghasilan: {{ $ibu->penghasilan_bulanan }}
                            </div>
                            <div class="text-xs text-gray-500">HP: {{ $ibu->no_hp }}</div>
                        </div>
                        @endif
                    @endif

                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: PEMBAYARAN & DOKUMEN --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. TABEL PEMBAYARAN --}}
            <div class="bg-white shadow rounded-lg overflow-hidden border-t-4 border-green-500">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-green-50">
                    <h3 class="font-bold text-green-800 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Riwayat Pembayaran (Cicilan)
                    </h3>
                    @php
                        $rencana = $siswa->rencanaPembayaran;
                        $sisa = $rencana ? ($rencana->total_nominal_biaya - $rencana->total_sudah_dibayar) : 0;
                    @endphp
                    <span class="bg-white px-3 py-1 rounded border border-green-200 text-green-700 text-sm font-bold">
                        Sisa: Rp {{ number_format($sisa, 0, ',', '.') }}
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3">Bukti</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center w-48">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($siswa->rencanaPembayaran)
                                @forelse($siswa->rencanaPembayaran->pembayaran as $bayar)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ Carbon::parse($bayar->tanggal_pembayaran)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 font-bold text-gray-900">Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        @if($bayar->buktiPembayaran)
                                            <a href="{{ Storage::url($bayar->buktiPembayaran->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Lihat
                                            </a>
                                        @else - @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($bayar->status == 'Verified')
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5 rounded">Sah</span>
                                        @elseif($bayar->status == 'Failed')
                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5 rounded">Ditolak</span>
                                        @else
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5 rounded">Menunggu</span>
                                        @endif
                                    </td>
                                    
                                    {{-- TOMBOL AKSI (FIXED: Hanya POST, tanpa PUT) --}}
                                    <td class="px-4 py-3 text-center">
                                        @if($bayar->status == 'Pending')
                                            <form action="{{ route('admin.verifikasi.pembayaran', $bayar->id) }}" method="POST" class="flex justify-center space-x-2">
                                                @csrf
                                                
                                                <button type="submit" name="aksi" value="terima" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-bold shadow" title="Verifikasi Sah">
                                                    ✔ Sah
                                                </button>
                                                <button type="submit" name="aksi" value="tolak" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold shadow" title="Tolak" onclick="return confirm('Tolak pembayaran ini?')">
                                                    ✖ Tolak
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Belum ada pembayaran.</td></tr>
                                @endforelse
                            @else
                                <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Rencana pembayaran belum dibuat.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. TABEL DOKUMEN --}}
            <div class="bg-white shadow rounded-lg overflow-hidden border-t-4 border-gray-500">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 font-bold text-gray-700">
                    Kelengkapan Dokumen
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                    @foreach($siswa->dokumen as $doc)
                        <div class="border rounded p-3 flex justify-between items-center {{ $doc->status_verifikasi == 'Valid' ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                            <div class="truncate">
                                <div class="font-bold text-sm text-gray-800">{{ $doc->tipe_dokumen }}</div>
                                <div class="text-xs text-gray-500 truncate w-32">{{ $doc->nama_asli_file }}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($doc->status_verifikasi == 'Valid')
                                    <span class="text-green-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                @endif
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold hover:bg-blue-200">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection