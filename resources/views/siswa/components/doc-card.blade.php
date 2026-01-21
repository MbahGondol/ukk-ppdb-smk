@props(['judul', 'kode', 'icon', 'wajib' => false, 'desc' => '', 'dokumen' => null])

<div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
    {{-- Header Kartu --}}
    <div class="p-5 flex items-start justify-between bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                {{-- Icon Sederhana (SVG) berdasarkan parameter --}}
                @if($icon == 'users') 
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                @elseif($icon == 'document-text')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                @elseif($icon == 'academic-cap')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                @elseif($icon == 'identification')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.5 2-2 2h4c-1.5 0-2-1.116-2-2z"></path></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                @endif
            </div>
            <div>
                <h3 class="font-bold text-gray-800">{{ $judul }}</h3>
                @if($wajib)
                    <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold">Wajib</span>
                @else
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Opsional</span>
                @endif
            </div>
        </div>
        
        {{-- Status Badge --}}
        @if($dokumen)
            <div class="bg-green-100 text-green-700 p-1 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        @else
            <div class="bg-gray-100 text-gray-400 p-1 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
        @endif
    </div>

    {{-- Body Kartu --}}
    <div class="p-5 flex-grow">
        @if($desc)
            <p class="text-sm text-gray-500 mb-4">{{ $desc }}</p>
        @else
            <p class="text-sm text-gray-500 mb-4">Unggah file {{ $judul }} dalam format JPG/PDF (Max 2MB).</p>
        @endif

        {{-- Jika Sudah Ada File --}}
        @if($dokumen)
            <div class="bg-gray-50 rounded p-3 border border-gray-200 mb-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 truncate max-w-[150px]">{{ $dokumen->nama_file_asli ?? 'File Terunggah' }}</span>
                    <a href="{{ Storage::url($dokumen->path_file) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat</a>
                </div>
            </div>
        @endif
    </div>

    {{-- Footer Actions --}}
    <div class="p-4 bg-gray-50 border-t border-gray-100">
        @if($dokumen)
             <form action="{{ route('siswa.dokumen.destroy', $dokumen->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus file ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-200">
                    Hapus / Ganti File
                </button>
            </form>
        @else
            {{-- Form Upload --}}
            <form action="{{ route('siswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jenis_dokumen" value="{{ $kode }}">
                
                {{-- Input File Tersembunyi + Label Bagus --}}
                <label class="block">
                    <span class="sr-only">Choose file</span>
                    <input type="file" name="file_dokumen" required
                        class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                        cursor-pointer
                        "/>
                </label>
                <button type="submit" class="mt-3 w-full bg-blue-600 text-white py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors shadow-sm">
                    Unggah {{ $judul }}
                </button>
            </form>
        @endif
    </div>
</div>