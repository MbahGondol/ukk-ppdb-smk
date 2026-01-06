@extends('layouts.app')

@section('title', 'Upload Dokumen')

@section('content')
<div class="max-w-5xl mx-auto pb-10">

    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-blue-600 bg-blue-200">
                Langkah 2 dari 3
            </span>
            <span class="text-xs font-semibold inline-block text-blue-600">
                Upload Dokumen
            </span>
        </div>
        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
            <div style="width: 66%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-1">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                    <h3 class="text-lg font-bold text-gray-800">Upload File Baru</h3>
                </div>
                <div class="p-6">
                    
                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('siswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="tipe_dokumen" class="block text-sm font-bold text-gray-700 mb-2">Jenis Dokumen</label>
                            <select name="tipe_dokumen" id="tipe_dokumen" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach ($dokumen_wajib as $tipe)
                                    <option value="{{ $tipe }}">{{ $tipe }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="file_dokumen" class="block text-sm font-bold text-gray-700 mb-2">Pilih File</label>
                            <input type="file" name="file_dokumen" id="file_dokumen" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Maks: 2MB.</p>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                            Upload
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <a href="{{ route('siswa.dashboard') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Dokumen Terupload</h3>
                    <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-1 rounded-full">
                        Total: {{ $dokumen_terupload->count() }}
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-6 py-3">Jenis Dokumen</th>
                                <th class="px-6 py-3">File</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php use Illuminate\Support\Facades\Storage; @endphp
                            
                            @forelse ($dokumen_terupload as $dokumen)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $dokumen->tipe_dokumen }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('dokumen.show', $dokumen->id) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat File
                                        </a>
                                        @if(in_array(pathinfo($dokumen->file_path, PATHINFO_EXTENSION), ['jpg','jpeg','png']))
                                            <img src="{{ route('dokumen.show', $dokumen->id) }}" alt="Preview" style="max-width: 200px; margin-top: 10px; border-radius: 4px;">
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($dokumen->status_verifikasi == 'Valid')
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Valid</span>
                                        @elseif($dokumen->status_verifikasi == 'Invalid')
                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Ditolak</span>
                                        @else
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Menunggu</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($dokumen->status_verifikasi != 'Valid')
                                            <form action="{{ route('siswa.dokumen.destroy', $dokumen->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Anda yakin ingin menghapus file ini?')" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase hover:underline">
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
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada dokumen yang diunggah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 text-xs text-gray-500 border-t border-gray-200">
                    <p>Pastikan dokumen terbaca jelas. Jika status "Valid", dokumen tidak dapat dihapus lagi.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection