@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
@endphp

<div class="max-w-5xl mx-auto pb-10">

    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-blue-600 bg-blue-200">
                Langkah 3 dari 3
            </span>
            <span class="text-xs font-semibold inline-block text-blue-600">
                Pembayaran & Konfirmasi
            </span>
        </div>
        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
            <div style="width: 100%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
            <p class="font-bold">Berhasil!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
            <p class="font-bold">Gagal!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
            <strong class="font-bold">Periksa inputan Anda:</strong>
            <ul class="list-disc list-inside mt-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Informasi Tagihan</h3>
            </div>
            <div class="p-6">
                
                @php
                    // Ambil data siswa untuk cek promo
                    $siswa_view = Auth::user()->calonSiswa;
                    // Ambil nilai potongan (jika ada)
                    $potongan = $siswa_view->promo ? $siswa_view->promo->potongan : 0;
                    // Hitung harga asli (Total Akhir + Potongan yang sudah dikurangi)
                    $total_asli = $tagihan->total_nominal_biaya + $potongan;
                @endphp

                <table class="w-full text-sm">
                    <tr class="border-b">
                        <td class="py-2 text-gray-600">Total Biaya Normal</td>
                        <td class="py-2 text-right text-gray-500" style="text-decoration: line-through;">
                            Rp {{ number_format($total_asli, 0, ',', '.') }}
                        </td>
                    </tr>

                    @if($siswa_view->promo)
                        <tr class="border-b bg-green-50">
                            <td class="py-2 text-green-700 pl-2">
                                <strong>Potongan Promo</strong><br>
                                <span class="text-xs">({{ $siswa_view->promo->nama_promo }})</span>
                            </td>
                            <td class="py-2 text-right text-green-700 font-bold pr-2">
                                - Rp {{ number_format($potongan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    <tr class="border-b border-blue-200 bg-blue-50">
                        <td class="py-3 text-blue-900 font-bold text-lg pl-2">Total Tagihan Akhir</td>
                        <td class="py-3 text-right text-blue-900 font-bold text-lg pr-2">
                            Rp {{ number_format($tagihan->total_nominal_biaya, 0, ',', '.') }}
                        </td>
                    </tr>

                    <tr>
                        <td class="py-2 pt-4 text-gray-600">Sudah Dibayar (Verifikasi)</td>
                        <td class="py-2 pt-4 text-right font-semibold text-green-600">
                            Rp {{ number_format($tagihan->total_sudah_dibayar, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-600 font-bold">Sisa Tagihan</td>
                        <td class="py-2 text-right text-2xl font-bold text-red-600">
                            Rp {{ number_format($tagihan->total_nominal_biaya - $tagihan->total_sudah_dibayar, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
                
                <div class="mt-6 pt-4 border-t text-center">
                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $tagihan->status == 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        Status: {{ $tagihan->status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                <h3 class="text-lg font-bold text-gray-800">Rekening Tujuan Transfer</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">1</div>
                    <div class="ml-4">
                        <h4 class="text-lg font-bold text-gray-900">Bank BCA</h4>
                        <p class="text-gray-600">No. Rek: <span class="font-mono font-bold text-black bg-gray-100 px-2 py-1 rounded">123456789</span></p>
                        <p class="text-sm text-gray-500">a/n SMK Pejantan Tangguh</p>
                    </div>
                </div>
                <hr>
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">2</div>
                    <div class="ml-4">
                        <h4 class="text-lg font-bold text-gray-900">Bank BNI</h4>
                        <p class="text-gray-600">No. Rek: <span class="font-mono font-bold text-black bg-gray-100 px-2 py-1 rounded">987654321</span></p>
                        <p class="text-sm text-gray-500">a/n Yayasan Pendidikan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Konfirmasi Pembayaran</h3>
            <p class="text-sm text-gray-500">Silakan upload bukti transfer setelah melakukan pembayaran.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('siswa.pembayaran.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Transfer (Rp)</label>
                    <input type="number" name="jumlah" value="{{ old('jumlah') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="Contoh: 1000000">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Transfer</label>
                    <input type="date" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bukti Foto (Struk/Resi)</label>
                    <input type="file" name="file_bukti" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                </div>
                
                <div class="md:col-span-3 text-right">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-8 rounded-lg shadow transition flex items-center justify-center w-full md:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Kirim Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Pembayaran Anda</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jumlah</th>
                        <th class="px-6 py-3">Bukti</th>
                        <th class="px-6 py-3">Status Verifikasi</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat_pembayaran as $pembayaran)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if ($pembayaran->buktiPembayaran)
                                    <a href="{{ Storage::disk('public')->url($pembayaran->buktiPembayaran->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($pembayaran->status == 'Verified')
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Diterima</span>
                                @elseif($pembayaran->status == 'Failed')
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Ditolak</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($pembayaran->status == 'Pending' || $pembayaran->status == 'Failed')
                                    <form action="{{ route('siswa.pembayaran.destroy', $pembayaran->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Anda yakin ingin menghapus pembayaran ini?')" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-xs italic">Terkunci</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada riwayat pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('siswa.dashboard') }}" class="text-gray-600 hover:text-gray-800 font-medium">
            &larr; Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection