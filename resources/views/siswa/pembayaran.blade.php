@extends('layouts.app')

@section('title', 'Pembayaran Administrasi')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
@endphp

{{-- STYLE ADAPTASI DARI PENDAFTARAN FORM AGAR KONSISTEN --}}
<style>
    .step-indicator { display: flex; justify-content: center; align-items: center; margin-bottom: 30px; position: relative; max-width: 500px; margin-left: auto; margin-right: auto; gap: 40px; }
    .step-indicator::before { content: ''; position: absolute; top: 20px; left: 10%; width: 80%; height: 3px; background: #e5e7eb; z-index: 0; border-radius: 99px; }
    
    .step-wrapper { position: relative; z-index: 1; text-align: center; width: 100px; }
    
    .step-badge { 
        width: 42px; height: 42px; border-radius: 50%; background: #f3f4f6; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: bold; font-size: 1.1rem; color: #9ca3af; 
        margin: 0 auto; border: 4px solid #fff; transition: all 0.3s ease;
    }
    .step-badge.active { background: #2563eb; color: white; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2); transform: scale(1.1); }
    .step-badge.done { background: #10b981; color: white; border-color: #fff; } 
    
    .step-label { margin-top: 8px; font-size: 0.85rem; font-weight: 600; color: #6b7280; display: block; }
    .step-badge.active + .step-label { color: #2563eb; }
    
    /* Input Style Premium */
    .form-control-lg {
        width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem;
        font-size: 0.95rem; transition: all 0.2s; background-color: #fff;
    }
    .form-control-lg:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none; }
</style>

<div class="max-w-5xl mx-auto pb-24 pt-6">

    {{-- HEADER GRADIENT (Meniru Pendaftaran) --}}
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100 mb-8">
        <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-800 text-white flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold">Penyelesaian Administrasi</h2>
                <p class="text-blue-100 mt-1 text-sm opacity-90">
                    Lakukan pembayaran untuk finalisasi status pendaftaran.
                </p>
            </div>
            <div class="bg-white/90 backdrop-blur text-gray-800 px-4 py-2 rounded-lg shadow-lg font-bold text-sm">
                Status: 
                <span class="{{ $tagihan->status == 'Lunas' ? 'text-green-600' : 'text-orange-600' }}">
                    {{ $tagihan->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- STEPPER VISUAL (Langkah 1 & 2 dianggap selesai secara visual) --}}
    <div class="step-indicator">
        <div class="step-wrapper">
            <div class="step-badge done">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span class="step-label">Data Diri</span>
        </div>
        <div class="step-wrapper">
            <div class="step-badge done">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span class="step-label">Dokumen</span>
        </div>
        <div class="step-wrapper">
            <div class="step-badge active">3</div>
            <span class="step-label text-blue-600">Pembayaran</span>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r shadow-sm flex items-center animate-pulse-once">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div><p class="font-bold">Berhasil!</p><p>{{ session('success') }}</p></div>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div><p class="font-bold">Gagal!</p><p>{{ session('error') }}</p></div>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm">
            <strong class="font-bold text-lg">Periksa inputan Anda:</strong>
            <ul class="list-disc list-inside mt-2 text-sm">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD UTAMA: TAGIHAN & FORM --}}
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 p-8 md:p-10 mb-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            
            {{-- KOLOM KIRI: RINCIAN BIAYA --}}
            <div>
                <h3 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-2 border-b pb-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Informasi Tagihan
                </h3>

                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    @php
                        $siswa_view = Auth::user()->calonSiswa;
                        $potongan = $siswa_view->promo ? $siswa_view->promo->potongan : 0;
                        $total_asli = $tagihan->total_nominal_biaya + $potongan;
                    @endphp

                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex justify-between items-center">
                            <span>Biaya Normal</span>
                            <span class="line-through text-gray-400">Rp {{ number_format($total_asli, 0, ',', '.') }}</span>
                        </div>

                        @if($siswa_view->promo)
                        <div class="flex justify-between items-center bg-green-100 text-green-800 p-2 rounded-lg border border-green-200">
                            <div>
                                <span class="font-bold block text-xs uppercase tracking-wider">Potongan Promo</span>
                                <span class="font-bold">{{ $siswa_view->promo->nama_promo }}</span>
                            </div>
                            <span class="font-bold text-lg">- Rp {{ number_format($potongan, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="border-t border-gray-300 pt-3 flex justify-between items-center text-blue-900">
                            <span class="font-bold text-lg">Total Tagihan</span>
                            <span class="font-bold text-xl">Rp {{ number_format($tagihan->total_nominal_biaya, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-2 space-y-2">
                            <div class="flex justify-between items-center text-green-700">
                                <span>Sudah Dibayar (Verified)</span>
                                <span class="font-bold">Rp {{ number_format($tagihan->total_sudah_dibayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-red-600 bg-red-50 p-2 rounded-lg">
                                <span class="font-bold">SISA TAGIHAN</span>
                                <span class="font-extrabold text-xl">Rp {{ number_format($tagihan->total_nominal_biaya - $tagihan->total_sudah_dibayar, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: REKENING --}}
            <div>
                <h3 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-2 border-b pb-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Tujuan Transfer
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-blue-400 hover:shadow-md transition group bg-white">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold group-hover:bg-blue-600 group-hover:text-white transition">BCA</div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-bold text-gray-900">Bank Central Asia (BCA)</h4>
                            <div class="flex flex-wrap items-center justify-between mt-1 gap-2">
                                <span class="font-mono text-lg font-bold text-gray-700 tracking-wider bg-gray-50 px-2 rounded border border-gray-100">123456789</span>
                                <span class="text-xs text-gray-500">a/n SMK Pejantan Tangguh</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-400 hover:shadow-md transition group bg-white">
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold group-hover:bg-orange-600 group-hover:text-white transition">BNI</div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-bold text-gray-900">Bank Negara Indonesia (BNI)</h4>
                            <div class="flex flex-wrap items-center justify-between mt-1 gap-2">
                                <span class="font-mono text-lg font-bold text-gray-700 tracking-wider bg-gray-50 px-2 rounded border border-gray-100">987654321</span>
                                <span class="text-xs text-gray-500">a/n Yayasan Pendidikan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <hr class="my-10 border-gray-100">

        {{-- FORM KONFIRMASI --}}
        <div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-800">Konfirmasi Pembayaran</h3>
                <p class="text-gray-500">Sudah transfer? Segera upload buktinya di sini.</p>
            </div>

            <form action="{{ route('siswa.pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Ditransfer (Rp)</label>
                        <input type="number" name="jumlah" value="{{ old('jumlah') }}" class="form-control-lg" required placeholder="Contoh: 1000000">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Transfer</label>
                        <input type="date" name="tanggal_pembayaran" 
                        value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}" 
                        min="{{ date('Y') }}-01-01" 
                        max="{{ date('Y-m-d') }}"
                        class="form-control-lg cursor-pointer" 
                        required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bukti Foto (Struk/Resi)</label>
                        <input type="file" name="file_bukti" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer" required>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-green-600 to-green-700 text-white font-bold py-3 px-10 rounded-lg shadow-lg hover:shadow-xl hover:from-green-700 hover:to-green-800 transform hover:scale-105 transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim Bukti Pembayaran
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- RIWAYAT (TABEL) --}}
    <div class="mt-10">
        <h3 class="text-xl font-bold text-gray-800 mb-4 px-2">Riwayat Pembayaran Anda</h3>
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Jumlah</th>
                            <th class="px-6 py-4">Bukti</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayat_pembayaran as $pembayaran)
                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium">{{ Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @if ($pembayaran->buktiPembayaran)
                                        <a href="{{ Storage::disk('public')->url($pembayaran->buktiPembayaran->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat Foto
                                        </a>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($pembayaran->status == 'Verified')
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">Diterima</span>
                                    @elseif($pembayaran->status == 'Failed')
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full">Ditolak</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full">Menunggu</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($pembayaran->status == 'Pending' || $pembayaran->status == 'Failed')
                                        <form action="{{ route('siswa.pembayaran.destroy', $pembayaran->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus bukti ini?')" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic bg-gray-50">
                                    Belum ada data pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-10 text-center">
        <a href="{{ route('siswa.dashboard') }}" class="text-gray-500 hover:text-blue-600 font-medium transition inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection