@extends('layouts.app')

@section('title', 'Formulir Pendaftaran')

@section('content')
<div class="max-w-5xl mx-auto pb-10">
    
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-blue-600 bg-blue-200">
                Langkah 1 dari 3
            </span>
            <span class="text-xs font-semibold inline-block text-blue-600">
                Isi Biodata Lengkap
            </span>
        </div>
        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
            <div style="width: 33%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <div class="px-8 py-6 bg-gray-50 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Formulir Pendaftaran Siswa Baru</h2>
            <p class="text-sm text-gray-600 mt-1">
                Tahun Ajaran: <span class="font-semibold text-blue-600">{{ $tahun_aktif->tahun_ajaran }}</span> | 
                Gelombang: <span class="font-semibold text-blue-600">{{ $gelombang_aktif->nama_gelombang }}</span>
            </p>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <p class="font-bold">Harap perbaiki kesalahan berikut:</p>
                    <ul class="list-disc list-inside text-sm mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('siswa.pendaftaran.store') }}" method="POST" id="formPendaftaran">
                @csrf
                <input type="hidden" name="tahun_akademik_id" value="{{ $tahun_aktif->id }}">
                <input type="hidden" name="gelombang_id" value="{{ $gelombang_aktif->id }}">

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4 inline-block">
                        A. Data Pribadi Calon Siswa
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NISN <span class="text-red-500">*</span></label>
                            <input type="text" name="nisn" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required placeholder="10 digit angka">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required placeholder="16 digit angka">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Agama <span class="text-red-500">*</span></label>
                            <select name="agama" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                                <option value="">-- Pilih --</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. HP (WhatsApp) <span class="text-red-500">*</span></label>
                            <input type="text" name="no_hp" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required placeholder="Contoh: 081234567890">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Anak Ke-</label>
                                <input type="number" name="anak_ke" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Jml Saudara</label>
                                <input type="number" name="jumlah_saudara" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tinggi (cm)</label>
                                <input type="number" name="tinggi_badan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Berat (kg)</label>
                                <input type="number" name="berat_badan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4 inline-block">
                        B. Alamat Tempat Tinggal
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Jalan / Dusun <span class="text-red-500">*</span></label>
                            <textarea name="alamat" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="Jl. Mawar No. 10, Dusun Krajan..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">RT / RW <span class="text-red-500">*</span></label>
                            <input type="text" name="rt_rw" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="001/002">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan / Desa <span class="text-red-500">*</span></label>
                            <input type="text" name="desa_kelurahan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan <span class="text-red-500">*</span></label>
                            <input type="text" name="kecamatan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kota / Kabupaten <span class="text-red-500">*</span></label>
                            <input type="text" name="kota_kab" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kode Pos <span class="text-red-500">*</span></label>
                            <input type="text" name="kode_pos" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4 inline-block">
                        C. Data Sekolah Asal
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Sekolah Asal (SMP/MTS) <span class="text-red-500">*</span></label>
                            <input type="text" name="asal_sekolah" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Lulus <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun_lulus" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="2025">
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4 inline-block">
                        D. Pilihan Jurusan
                    </h3>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Jurusan & Tipe Kelas <span class="text-red-500">*</span></label>
                        <select name="jurusan_tipe_kelas_id" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400 text-lg font-medium" required>
                            <option value="">-- Klik untuk Memilih --</option>
                            
                            @foreach ($data_jurusan_tipe_kelas as $kombinasi)
                                @php
                                    // Hitung Sisa Kuota
                                    $sisa_kuota = $kombinasi->kuota_kelas - $kombinasi->jumlah_pendaftar;
                                    $is_full = $sisa_kuota <= 0;
                                @endphp

                                <option value="{{ $kombinasi->id }}" 
                                        {{-- Jika penuh, disable opsi ini --}}
                                        {{ $is_full ? 'disabled' : '' }} 
                                        {{-- Jika dipilih sebelumnya, keep selected --}}
                                        {{ old('jurusan_tipe_kelas_id') == $kombinasi->id ? 'selected' : '' }}
                                        {{-- Beri warna abu-abu/merah jika penuh --}}
                                        class="{{ $is_full ? 'bg-gray-200 text-red-500' : '' }}"
                                >
                                    {{ $kombinasi->jurusan->nama_jurusan }} ({{ $kombinasi->tipeKelas->nama_tipe_kelas }})
                                    
                                    @if($is_full)
                                        [PENUH - Kuota Habis]
                                    @else
                                        - Sisa Kuota: {{ $sisa_kuota }} Kursi
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-6 inline-block">
                        E. Data Orang Tua / Wali
                    </h3>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h4 class="font-bold text-blue-600 mb-4">13.1 Data Ayah Kandung</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Ayah <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ayah" class="w-full px-3 py-2 border rounded focus:ring-blue-200" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">NIK Ayah</label>
                                <input type="text" name="nik_ayah" class="w-full px-3 py-2 border rounded focus:ring-blue-200" maxlength="16">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Lahir</label>
                                <input type="number" name="tahun_lahir_ayah" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_ayah" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA/SMK">SMA/SMK</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="Tidak Sekolah">Tidak Sekolah</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_ayah" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Penghasilan Bulanan (Rp)</label>
                                <input type="number" name="penghasilan_ayah" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. HP Ayah</label>
                                <input type="text" name="nohp_ayah" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h4 class="font-bold text-pink-600 mb-4">13.2 Data Ibu Kandung</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Ibu <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ibu" class="w-full px-3 py-2 border rounded focus:ring-blue-200" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">NIK Ibu</label>
                                <input type="text" name="nik_ibu" class="w-full px-3 py-2 border rounded focus:ring-blue-200" maxlength="16">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Lahir</label>
                                <input type="number" name="tahun_lahir_ibu" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_ibu" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA/SMK">SMA/SMK</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="Tidak Sekolah">Tidak Sekolah</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_ibu" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Penghasilan Bulanan (Rp)</label>
                                <input type="number" name="penghasilan_ibu" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. HP Ibu</label>
                                <input type="text" name="nohp_ibu" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-bold text-yellow-700 mb-4">14. Data Wali (Opsional)</h4>
                        <p class="text-sm text-gray-600 mb-4">Isi bagian ini jika Anda tinggal bersama Wali.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Wali</label>
                                <input type="text" name="nama_wali" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Hubungan dengan Siswa</label>
                                <input type="text" name="hubungan_wali" class="w-full px-3 py-2 border rounded focus:ring-blue-200" placeholder="Cth: Paman, Kakek">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Wali</label>
                                <textarea name="alamat_wali" rows="2" class="w-full px-3 py-2 border rounded focus:ring-blue-200"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. HP Wali</label>
                                <input type="text" name="nohp_wali" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex justify-end">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-10 rounded-full shadow-lg transform transition hover:scale-105 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Biodata & Lanjut
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector('#formPendaftaran'); // Pastikan ID form sesuai
            const storageKey = 'ppdb_form_draft_v1';

            // 1. Load Data
            const savedData = JSON.parse(localStorage.getItem(storageKey));
            if (savedData) {
                Object.keys(savedData).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input && (input.type !== 'hidden')) {
                        input.value = savedData[key];
                    }
                });
            }

            // 2. Save Data on Input
            form.addEventListener('input', function(e) {
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    if (key !== '_token' && key !== 'tahun_akademik_id' && key !== 'gelombang_id') {
                        data[key] = value;
                    }
                });
                localStorage.setItem(storageKey, JSON.stringify(data));
            });

            // 3. Clear Data on Submit
            form.addEventListener('submit', function() {
                localStorage.removeItem(storageKey);
            });
        });
    </script>
</div>
@endsection