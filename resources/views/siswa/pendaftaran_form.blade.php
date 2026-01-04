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
                Isi / Edit Biodata
            </span>
        </div>
        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
            <div style="width: 33%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <div class="px-8 py-6 bg-gray-50 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">
                {{-- LOGIKA JUDUL --}}
                {{ $calonSiswa ? 'Edit Biodata Pendaftaran' : 'Formulir Pendaftaran Baru' }}
            </h2>
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
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('siswa.pendaftaran.store') }}" method="POST" id="formPendaftaran">
                @csrf
                <input type="hidden" name="tahun_akademik_id" value="{{ $tahun_aktif->id }}">
                <input type="hidden" name="gelombang_id" value="{{ $gelombang_aktif->id }}">

                @php
                    $ayah = $calonSiswa ? $calonSiswa->penanggungJawab->where('hubungan', 'Ayah')->first() : null;
                    $ibu = $calonSiswa ? $calonSiswa->penanggungJawab->where('hubungan', 'Ibu')->first() : null;
                    $wali = $calonSiswa ? $calonSiswa->penanggungJawab->where('hubungan', 'Wali')->first() : null;
                @endphp

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4 inline-block">A. Data Pribadi Calon Siswa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NISN <span class="text-red-500">*</span></label>
                            <input type="text" name="nisn" value="{{ old('nisn', $calonSiswa->nisn ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="10 digit angka" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" value="{{ old('nik', $calonSiswa->nik ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="16 digit angka" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $calonSiswa->nama_lengkap ?? Auth::user()->name) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $calonSiswa->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $calonSiswa->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Agama <span class="text-red-500">*</span></label>
                            <select name="agama" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                                <option value="">-- Pilih --</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $calonSiswa->agama ?? '') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $calonSiswa->tempat_lahir ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($calonSiswa->tanggal_lahir ?? null)->format('Y-m-d')) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. HP (WhatsApp) <span class="text-red-500">*</span></label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $calonSiswa->no_hp ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="081234567890" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Anak Ke-</label>
                                <input type="number" name="anak_ke" value="{{ old('anak_ke', $calonSiswa->anak_ke ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" min="1">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Jml Saudara</label>
                                <input type="number" name="jumlah_saudara" value="{{ old('jumlah_saudara', $calonSiswa->jumlah_saudara ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" min="0">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tinggi (cm)</label>
                                <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $calonSiswa->tinggi_badan ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Berat (kg)</label>
                                <input type="number" name="berat_badan" value="{{ old('berat_badan', $calonSiswa->berat_badan ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4 inline-block">B. Alamat Tempat Tinggal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Jalan / Dusun <span class="text-red-500">*</span></label>
                            <textarea name="alamat" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="Jl. Mawar No. 10, Dusun Krajan...">{{ old('alamat', $calonSiswa->alamat ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">RT / RW <span class="text-red-500">*</span></label>
                            <input type="text" name="rt_rw" value="{{ old('rt_rw', $calonSiswa->rt_rw ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="001/002">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan / Desa <span class="text-red-500">*</span></label>
                            <input type="text" name="desa_kelurahan" value="{{ old('desa_kelurahan', $calonSiswa->desa_kelurahan ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan <span class="text-red-500">*</span></label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan', $calonSiswa->kecamatan ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kota / Kabupaten <span class="text-red-500">*</span></label>
                            <input type="text" name="kota_kab" value="{{ old('kota_kab', $calonSiswa->kota_kab ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kode Pos <span class="text-red-500">*</span></label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $calonSiswa->kode_pos ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4 inline-block">C. Data Sekolah Asal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Sekolah Asal (SMP/MTS) <span class="text-red-500">*</span></label>
                            <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $calonSiswa->asal_sekolah ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Lulus <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus', $calonSiswa->tahun_lulus ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required placeholder="2025">
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4 inline-block">D. Pilihan Jurusan</h3>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Jurusan & Tipe Kelas <span class="text-red-500">*</span></label>
                        <select name="jurusan_tipe_kelas_id" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400 text-lg font-medium" required>
                            <option value="">-- Klik untuk Memilih --</option>
                            @php 
                                $selected_id = $calonSiswa ? \App\Models\JurusanTipeKelas::where('jurusan_id', $calonSiswa->jurusan_id)->where('tipe_kelas_id', $calonSiswa->tipe_kelas_id)->value('id') : null;
                            @endphp

                            @foreach ($data_jurusan_tipe_kelas as $kombinasi)
                                @php
                                    $sisa_kuota = $kombinasi->kuota_kelas - $kombinasi->jumlah_pendaftar;
                                    $is_full = $sisa_kuota <= 0;
                                    $is_selected = old('jurusan_tipe_kelas_id', $selected_id) == $kombinasi->id;
                                @endphp
                                <option value="{{ $kombinasi->id }}" 
                                        {{ ($is_full && !$is_selected) ? 'disabled' : '' }} 
                                        {{ $is_selected ? 'selected' : '' }}
                                        class="{{ ($is_full && !$is_selected) ? 'bg-gray-200 text-red-500' : '' }}">
                                    {{ $kombinasi->jurusan->nama_jurusan }} ({{ $kombinasi->tipeKelas->nama_tipe_kelas }})
                                    @if($is_full && !$is_selected) [PENUH] @else - Sisa: {{ $sisa_kuota }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-blue-500 pb-2 mb-6 inline-block">E. Data Orang Tua / Wali</h3>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h4 class="font-bold text-blue-600 mb-4">13.1 Data Ayah Kandung</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Ayah <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $ayah->nama_lengkap ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">NIK Ayah</label>
                                <input type="text" name="nik_ayah" value="{{ old('nik_ayah', $ayah->nik ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Lahir</label>
                                <input type="number" name="tahun_lahir_ayah" value="{{ old('tahun_lahir_ayah', $ayah->tahun_lahir ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_ayah" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['SD', 'SMP', 'SMA/SMK', 'S1', 'S2', 'S3', 'Tidak Sekolah'] as $edu)
                                        <option value="{{ $edu }}" {{ old('pendidikan_ayah', $ayah->pendidikan_terakhir ?? '') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $ayah->pekerjaan ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" oninput="this.value = this.value.replace(/[0-9]/g, '')">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Penghasilan Bulanan (Rp)</label>
                                <input type="number" name="penghasilan_ayah" value="{{ old('penghasilan_ayah', $ayah->penghasilan_bulanan ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. HP Ayah</label>
                                <input type="text" name="nohp_ayah" value="{{ old('nohp_ayah', $ayah->no_hp ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h4 class="font-bold text-pink-600 mb-4">13.2 Data Ibu Kandung</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Ibu <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $ibu->nama_lengkap ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">NIK Ibu</label>
                                <input type="text" name="nik_ibu" value="{{ old('nik_ibu', $ibu->nik ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Lahir</label>
                                <input type="number" name="tahun_lahir_ibu" value="{{ old('tahun_lahir_ibu', $ibu->tahun_lahir ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_ibu" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['SD', 'SMP', 'SMA/SMK', 'S1', 'S2', 'S3', 'Tidak Sekolah'] as $edu)
                                        <option value="{{ $edu }}" {{ old('pendidikan_ibu', $ibu->pendidikan_terakhir ?? '') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $ibu->pekerjaan ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" oninput="this.value = this.value.replace(/[0-9]/g, '')">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Penghasilan Bulanan (Rp)</label>
                                <input type="number" name="penghasilan_ibu" value="{{ old('penghasilan_ibu', $ibu->penghasilan_bulanan ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. HP Ibu</label>
                                <input type="text" name="nohp_ibu" value="{{ old('nohp_ibu', $ibu->no_hp ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-bold text-yellow-700 mb-4">14. Data Wali (Opsional)</h4>
                        <p class="text-sm text-gray-600 mb-4">Isi bagian ini jika Anda tinggal bersama Wali.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Wali</label>
                                <input type="text" name="nama_wali" value="{{ old('nama_wali', $wali->nama_lengkap ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" oninput="this.value = this.value.replace(/[0-9]/g, '')">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Hubungan</label>
                                <input type="text" name="hubungan_wali" value="{{ old('hubungan_wali', $wali->pekerjaan ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" placeholder="Cth: Paman" oninput="this.value = this.value.replace(/[0-9]/g, '')">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Wali</label>
                                <textarea name="alamat_wali" rows="2" class="w-full px-3 py-2 border rounded focus:ring-blue-200">{{ old('alamat_wali', $wali->alamat_wali ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. HP Wali</label>
                                <input type="text" name="nohp_wali" value="{{ old('nohp_wali', $wali->no_hp ?? '') }}" class="w-full px-3 py-2 border rounded focus:ring-blue-200" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex justify-end">
                    <button type="submit" class="bg-gradient-to-r from-green-500 to-green-700 hover:from-green-600 hover:to-green-800 text-white font-bold py-3 px-10 rounded-full shadow-lg transform transition hover:scale-105 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan & Lanjut
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection