@extends('layouts.admin')

@section('title', 'Setting Biaya Pendidikan')
@section('header', 'Matriks Biaya Pendidikan')

@section('content')

<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan Biaya Massal</h2>
        <p class="text-gray-600 mt-1">Atur nominal biaya untuk setiap jurusan dan gelombang dalam satu tabel.</p>
    </div>
    
    <button type="submit" form="form-matrix" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-lg transform hover:scale-105 transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
        SIMPAN SEMUA PERUBAHAN
    </button>
</div>

@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
    
    <form action="{{ route('admin.biaya.store') }}" method="POST" id="form-matrix">
        @csrf
        
        <div class="overflow-x-auto"> <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider sticky left-0 bg-gray-800 z-10 w-64 border-r border-gray-600">
                            Jurusan & Tipe Kelas
                        </th>
                        
                        @foreach($list_jenis_biaya as $biaya)
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider min-w-[150px]">
                                {{ $biaya->nama_biaya }}
                            </th>
                        @endforeach

                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider min-w-[150px] bg-gray-700">
                            Total Estimasi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    
                    @foreach($list_kelas as $kelas)
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200 shadow-sm">
                                <div class="text-sm font-bold text-gray-900">{{ $kelas->jurusan->nama_jurusan }}</div>
                                <div class="text-xs mt-1">
                                    <span class="px-2 py-0.5 rounded-full {{ $kelas->tipeKelas->nama_tipe_kelas == 'Unggulan' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $kelas->tipeKelas->nama_tipe_kelas }}
                                    </span>
                                </div>
                            </td>

                            @php $total_baris = 0; @endphp
                            @foreach($list_jenis_biaya as $biaya)
                                @php
                                    // Ambil nilai dari matrix controller, default 0
                                    $nominal = $matrix[$kelas->id][$biaya->id] ?? 0;
                                    $total_baris += $nominal;
                                @endphp
                                <td class="px-4 py-3 text-center">
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 sm:text-xs">Rp</span>
                                        </div>
                                        <input type="text" 
                                            name="biaya[{{ $kelas->id }}][{{ $biaya->id }}]" 
                                            value="{{ number_format($nominal, 0, ',', '.') }}" 
                                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 sm:text-sm border-gray-300 rounded-md text-right font-mono rupiah-input"
                                            placeholder="0">
                                    </div>
                                </td>
                            @endforeach

                            <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-700 bg-gray-50 border-l">
                                Rp {{ number_format($total_baris, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
            <p class="text-sm text-gray-500 italic">
                * Pastikan nominal yang diisi adalah angka tanpa titik/koma. Sistem akan otomatis menyimpannya.
            </p>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded shadow-lg transform hover:scale-105 transition">
                SIMPAN SEMUA PERUBAHAN
            </button>
        </div>

    </form>
</div>

<script>
    // Script sederhana untuk auto-format Rupiah saat mengetik
    document.querySelectorAll('.rupiah-input').forEach(function(input) {
        input.addEventListener('keyup', function(e) {
            // 1. Ambil value dan buang semua yang bukan angka
            let value = this.value.replace(/[^,\d]/g, '').toString();
            // 2. Format jadi ribuan Indonesia
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            
            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            this.value = rupiah;
        });
    });
</script>

@endsection