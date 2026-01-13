@extends('layouts.app')

@section('title', 'Formulir Pendaftaran')

@section('content')

<style>
    /* ANIMASI & STEPPER */
    .step-content { display: none; }
    .step-content.active { display: block; animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .step-indicator { display: flex; justify-content: space-between; margin-bottom: 30px; position: relative; max-width: 800px; margin-left: auto; margin-right: auto; }
    .step-indicator::before { content: ''; position: absolute; top: 20px; left: 0; width: 100%; height: 3px; background: #e5e7eb; z-index: 0; border-radius: 99px; }
    
    .step-wrapper { position: relative; z-index: 1; text-align: center; width: 100px; }
    
    .step-badge { 
        width: 42px; height: 42px; border-radius: 50%; background: #f3f4f6; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: bold; font-size: 1.1rem; color: #9ca3af; 
        margin: 0 auto; border: 4px solid #fff; transition: all 0.3s ease;
    }
    
    .step-badge.active { 
        background: #2563eb; color: white; 
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2); 
        transform: scale(1.1);
    }
    
    .step-label { 
        margin-top: 8px; font-size: 0.85rem; font-weight: 600; 
        color: #6b7280; display: block; transition: color 0.3s;
    }
    .step-badge.active + .step-label { color: #2563eb; }
    
    .required-label::after { content: " *"; color: #ef4444; }

    /* CUSTOM INPUT STYLE AGAR LEBIH LUAS */
    .form-control-lg {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem; /* Rounded-lg */
        padding-top: 0.75rem; /* py-3 */
        padding-bottom: 0.75rem;
        padding-left: 1rem; /* px-4 */
        padding-right: 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
        background-color: #fff;
    }
    .form-control-lg:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    label {
        margin-bottom: 0.5rem; /* mb-2 */
        display: block;
        font-size: 0.875rem; /* text-sm */
        font-weight: 600; /* font-semibold */
        color: #374151; /* text-gray-700 */
    }
</style>

<div class="max-w-5xl mx-auto pb-24 pt-6">
    
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100 mb-8">
        <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-800 text-white flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold">
                    {{ $calonSiswa ? 'Edit Data Pendaftaran' : 'Formulir Pendaftaran Baru' }}
                </h2>
                <p class="text-blue-100 mt-1 text-sm opacity-90">
                    Tahun Ajaran: <span class="font-bold bg-white/20 px-2 py-0.5 rounded text-white">{{ $tahun_aktif->tahun_ajaran }}</span> &bull; 
                    Gelombang: <span class="font-bold bg-white/20 px-2 py-0.5 rounded text-white">{{ $gelombang_aktif->nama_gelombang }}</span>
                </p>
            </div>
            @if($calonSiswa)
            <div class="bg-white/90 backdrop-blur text-gray-800 px-4 py-2 rounded-lg shadow-lg font-bold text-sm">
                Status: {{ $calonSiswa->status_pendaftaran }}
            </div>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-8 bg-red-50 border-l-4 border-red-500 text-red-700 p-5 rounded-r-lg shadow-sm">
            <h4 class="font-bold text-lg mb-2">Mohon Perbaiki Kesalahan Berikut:</h4>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 relative">
        <div class="p-8 md:p-10">
            
            <form action="{{ route('siswa.pendaftaran.store') }}" method="POST" id="pendaftaranForm">
                @csrf
                <input type="hidden" name="tahun_akademik_id" value="{{ $tahun_aktif->id }}">
                <input type="hidden" name="gelombang_id" value="{{ $gelombang_aktif->id }}">

                <div class="mb-12 step-indicator">
                    <div class="step-wrapper">
                        <div class="step-badge active" id="badge-1">1</div>
                        <span class="step-label">Jurusan & Diri</span>
                    </div>
                    <div class="step-wrapper">
                        <div class="step-badge" id="badge-2">2</div>
                        <span class="step-label">Alamat</span>
                    </div>
                    <div class="step-wrapper">
                        <div class="step-badge" id="badge-3">3</div>
                        <span class="step-label">Orang Tua</span>
                    </div>
                    <div class="step-wrapper">
                        <div class="step-badge" id="badge-4">4</div>
                        <span class="step-label">Konfirmasi</span>
                    </div>
                </div>

                <div id="step-1" class="step-content active">
                    <div class="mb-8 pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800">Pilihan Jurusan & Identitas Diri</h3>
                        <p class="text-gray-500 text-sm mt-1">Isi data diri Anda sesuai dengan Ijazah/Akte Kelahiran.</p>
                    </div>
                    
                    <div class="mb-8 bg-blue-50/50 p-6 rounded-xl border border-blue-100">
                        <label class="required-label text-lg text-blue-800">Pilih Kompetensi Keahlian (Jurusan)</label>
                        <select name="jurusan_tipe_kelas_id" required class="form-control-lg mt-2 border-blue-200 focus:border-blue-500 focus:ring-blue-200">
                            <option value="">-- Silakan Pilih Jurusan --</option>
                            @foreach($data_jurusan_tipe_kelas as $jtk)
                                @php
                                    $sisa = $jtk->kuota_kelas - $jtk->jumlah_pendaftar;
                                    $isDisabled = $sisa <= 0 && (!$calonSiswa || $calonSiswa->jurusan_id != $jtk->jurusan_id);
                                @endphp
                                <option value="{{ $jtk->id }}" 
                                    {{ old('jurusan_tipe_kelas_id', $calonSiswa?->jurusan_id && $calonSiswa?->tipe_kelas_id ? $calonSiswa->jurusan_tipe_kelas_id_logic_disini_sebaiknya_match_id : '') == $jtk->id ? 'selected' : '' }}
                                    {{ $isDisabled ? 'disabled' : '' }}
                                >
                                    {{ $jtk->jurusan->nama_jurusan }} - {{ $jtk->tipeKelas->nama_tipe_kelas }} (Sisa Kuota: {{ $sisa > 0 ? $sisa : 'PENUH' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="col-span-1">
                            <label class="required-label">NISN</label>
                            <input type="number" name="nisn" value="{{ old('nisn', $calonSiswa?->nisn) }}" required class="form-control-lg" placeholder="Nomor Induk Siswa Nasional">
                        </div>
                        <div class="col-span-1">
                            <label class="required-label">NIK Siswa</label>
                            <input type="number" name="nik" value="{{ old('nik', $calonSiswa?->nik) }}" required class="form-control-lg" placeholder="Nomor Induk Kependudukan">
                        </div>
                        <div class="md:col-span-2">
                            <label class="required-label">Nama Lengkap (Sesuai Ijazah)</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $calonSiswa?->nama_lengkap) }}" required class="form-control-lg uppercase" placeholder="Nama lengkap tanpa gelar">
                        </div>
                        
                        <div class="col-span-1">
                            <label class="required-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $calonSiswa?->tempat_lahir) }}" required class="form-control-lg">
                        </div>
                        <div class="col-span-1">
                            <label class="required-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $calonSiswa?->tanggal_lahir) }}" required class="form-control-lg">
                        </div>
                        
                        <div class="col-span-1">
                            <label class="required-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required class="form-control-lg">
                                <option value="">- Pilih -</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $calonSiswa?->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $calonSiswa?->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-span-1">
                            <label class="required-label">Agama</label>
                            <select name="agama" required class="form-control-lg">
                                <option value="Islam" {{ old('agama', $calonSiswa?->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama', $calonSiswa?->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $calonSiswa?->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $calonSiswa?->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Budha" {{ old('agama', $calonSiswa?->agama) == 'Budha' ? 'selected' : '' }}>Budha</option>
                                <option value="Konghucu" {{ old('agama', $calonSiswa?->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="required-label">No. HP / WA Siswa</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $calonSiswa?->no_hp) }}" required class="form-control-lg" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-span-1">
                            <label class="required-label">Asal Sekolah (SMP/MTs)</label>
                            <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $calonSiswa?->asal_sekolah) }}" required class="form-control-lg">
                        </div>
                        
                        <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-5 gap-4 bg-gray-50 p-6 rounded-xl border border-gray-200 mt-2">
                            <div>
                                <label class="required-label">Thn Lulus</label>
                                <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus', $calonSiswa?->tahun_lulus) }}" required class="form-control-lg text-center" placeholder="2025">
                            </div>
                            <div>
                                <label class="required-label">Anak Ke</label>
                                <input type="number" name="anak_ke" value="{{ old('anak_ke', $calonSiswa?->anak_ke) }}" required class="form-control-lg text-center">
                            </div>
                            <div>
                                <label class="required-label">Jml Sdr</label>
                                <input type="number" name="jumlah_saudara" value="{{ old('jumlah_saudara', $calonSiswa?->jumlah_saudara) }}" required class="form-control-lg text-center">
                            </div>
                            <div>
                                <label>Tinggi (cm)</label>
                                <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $calonSiswa?->tinggi_badan) }}" class="form-control-lg text-center">
                            </div>
                            <div>
                                <label>Berat (kg)</label>
                                <input type="number" name="berat_badan" value="{{ old('berat_badan', $calonSiswa?->berat_badan) }}" class="form-control-lg text-center">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="button" onclick="nextStep(2)" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg hover:shadow-xl transition flex items-center gap-3 font-semibold text-lg">
                            Lanjut ke Alamat <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                <div id="step-2" class="step-content">
                    <div class="mb-8 pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800">Alamat Tempat Tinggal</h3>
                        <p class="text-gray-500 text-sm mt-1">Isi sesuai dengan Kartu Keluarga (KK).</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="md:col-span-2">
                            <label class="required-label">Alamat Lengkap (Nama Jalan, Gang, Nomor Rumah)</label>
                            <textarea name="alamat" rows="3" required class="form-control-lg" placeholder="Contoh: Jl. Merpati No. 45, Dsn. Krajan">{{ old('alamat', $calonSiswa?->alamat) }}</textarea>
                        </div>
                        
                        <div>
                            <label class="required-label">RT / RW</label>
                            <input type="text" name="rt_rw" value="{{ old('rt_rw', $calonSiswa?->rt_rw) }}" required class="form-control-lg" placeholder="Contoh: 005 / 002">
                        </div>
                        
                        <div>
                            <label class="required-label">Desa / Kelurahan</label>
                            <input type="text" name="desa_kelurahan" value="{{ old('desa_kelurahan', $calonSiswa?->desa_kelurahan) }}" required class="form-control-lg">
                        </div>

                        <div>
                            <label class="required-label">Kecamatan</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan', $calonSiswa?->kecamatan) }}" required class="form-control-lg">
                        </div>

                        <div>
                            <label class="required-label">Kota / Kabupaten</label>
                            <input type="text" name="kota_kab" value="{{ old('kota_kab', $calonSiswa?->kota_kab) }}" required class="form-control-lg">
                        </div>

                        <div>
                            <label class="required-label">Kode Pos</label>
                            <input type="number" name="kode_pos" value="{{ old('kode_pos', $calonSiswa?->kode_pos) }}" required class="form-control-lg">
                        </div>
                    </div>

                    <div class="mt-10 flex justify-between">
                        <button type="button" onclick="prevStep(1)" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                            &larr; Kembali
                        </button>
                        <button type="button" onclick="nextStep(3)" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg transition font-semibold">
                            Lanjut ke Data Ortu &rarr;
                        </button>
                    </div>
                </div>

                <div id="step-3" class="step-content">
                    <div class="mb-8 pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800">Data Orang Tua / Wali</h3>
                        <p class="text-gray-500 text-sm mt-1">Data penghasilan dan pekerjaan akan digunakan untuk validasi beasiswa (jika ada).</p>
                    </div>
                    
                    @php
                        $ayah = $calonSiswa ? $calonSiswa->data_ayah : null;
                        $ibu  = $calonSiswa ? $calonSiswa->data_ibu : null;
                        $wali = $calonSiswa ? $calonSiswa->data_wali : null;
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                            <div class="flex items-center gap-3 mb-6 border-b border-slate-200 pb-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 text-lg">DATA AYAH</h4>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label>Nama Lengkap Ayah</label>
                                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $ayah?->nama_lengkap) }}" required class="form-control-lg">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label>NIK Ayah</label>
                                        <input type="number" name="nik_ayah" value="{{ old('nik_ayah', $ayah?->nik) }}" required class="form-control-lg">
                                    </div>
                                    <div>
                                        <label>Tahun Lahir</label>
                                        <input type="number" name="tahun_lahir_ayah" value="{{ old('tahun_lahir_ayah', $ayah?->tahun_lahir) }}" required class="form-control-lg text-center" placeholder="19xx">
                                    </div>
                                </div>
                                <div>
                                    <label>No. HP / WA</label>
                                    <input type="text" name="nohp_ayah" value="{{ old('nohp_ayah', $ayah?->no_hp) }}" required class="form-control-lg">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label>Pendidikan</label>
                                        <select name="pendidikan_ayah" class="form-control-lg">
                                            <option value="SD" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'SD' ? 'selected' : '' }}>SD</option>
                                            <option value="SMP" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                            <option value="SMA/SMK" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                            <option value="S1" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label>Penghasilan Bulanan (Rp)</label>
                                        <input type="number" name="penghasilan_ayah" 
                                            value="{{ old('penghasilan_ayah', $ayah?->penghasilan_bulanan ? (int)$ayah->penghasilan_bulanan : '') }}" 
                                            class="form-control-lg" 
                                            placeholder="Contoh: 2500000">
                                        <span class="text-xs text-gray-500">*Tulis angka saja tanpa titik/koma</span>
                                    </div>       
                                </div>
                                <div>
                                    <label>Pekerjaan Utama</label>
                                    <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $ayah?->pekerjaan) }}" required class="form-control-lg">
                                </div>
                            </div>
                        </div>

                        <div class="bg-pink-50/50 p-6 rounded-2xl border border-pink-100">
                            <div class="flex items-center gap-3 mb-6 border-b border-pink-200 pb-3">
                                <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <h4 class="font-bold text-pink-800 text-lg">DATA IBU</h4>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label>Nama Lengkap Ibu</label>
                                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $ibu?->nama_lengkap) }}" required class="form-control-lg">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label>NIK Ibu</label>
                                        <input type="number" name="nik_ibu" value="{{ old('nik_ibu', $ibu?->nik) }}" required class="form-control-lg">
                                    </div>
                                    <div>
                                        <label>Tahun Lahir</label>
                                        <input type="number" name="tahun_lahir_ibu" value="{{ old('tahun_lahir_ibu', $ibu?->tahun_lahir) }}" required class="form-control-lg text-center" placeholder="19xx">
                                    </div>
                                </div>
                                <div>
                                    <label>No. HP / WA</label>
                                    <input type="text" name="nohp_ibu" value="{{ old('nohp_ibu', $ibu?->no_hp) }}" required class="form-control-lg">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label>Pendidikan</label>
                                        <select name="pendidikan_ibu" class="form-control-lg">
                                            <option value="SD" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'SD' ? 'selected' : '' }}>SD</option>
                                            <option value="SMP" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                            <option value="SMA/SMK" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                            <option value="S1" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select> </div>
                                    
                                    <div>
                                        <label>Penghasilan Bulanan (Rp)</label>
                                        <input type="number" name="penghasilan_ibu" 
                                            value="{{ old('penghasilan_ibu', $ibu?->penghasilan_bulanan ? (int)$ibu->penghasilan_bulanan : '') }}" 
                                            class="form-control-lg" 
                                            placeholder="Contoh: 1000000">
                                        <span class="text-xs text-gray-500">*Tulis angka saja tanpa titik/koma</span>
                                    </div>
                                </div>
                                <div>
                                    <label>Pekerjaan Utama</label>
                                    <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $ibu?->pekerjaan) }}" required class="form-control-lg">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-dashed border-gray-300">
                        <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Data Wali (Opsional / Jika tidak tinggal bersama Ortu)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-gray-50 rounded-xl border border-gray-200">
                            <div>
                                <label>Nama Wali</label>
                                <input type="text" name="nama_wali" value="{{ old('nama_wali', $wali?->nama_lengkap) }}" class="form-control-lg">
                            </div>
                            <div>
                                <label>Hubungan dengan Siswa</label>
                                <input type="text" name="hubungan_wali" value="{{ old('hubungan_wali', $wali?->pekerjaan) }}" placeholder="Contoh: Paman, Kakek" class="form-control-lg">
                            </div>
                            <div>
                                <label>No HP Wali</label>
                                <input type="text" name="nohp_wali" value="{{ old('nohp_wali', $wali?->no_hp) }}" class="form-control-lg">
                            </div>
                            <div>
                                <label>Alamat Wali</label>
                                <input type="text" name="alamat_wali" value="{{ old('alamat_wali', $wali?->alamat_wali) }}" class="form-control-lg">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-between">
                        <button type="button" onclick="prevStep(2)" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                            &larr; Kembali
                        </button>
                        <button type="button" onclick="nextStep(4)" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg transition font-semibold">
                            Cek Data & Konfirmasi &rarr;
                        </button>
                    </div>
                </div>

                <div id="step-4" class="step-content">
                    <div class="mb-8 pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800">Konfirmasi Data</h3>
                    </div>
                    
                    <div class="bg-yellow-50 p-8 border border-yellow-200 rounded-2xl text-center mb-8">
                        <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Apakah data sudah benar?</h4>
                        <p class="text-gray-600 max-w-lg mx-auto">Pastikan Anda telah mengisi semua data dengan benar sesuai dokumen asli (KK/Ijazah). Data yang disimpan <span class="font-bold text-red-600">tidak dapat diubah lagi</span>.</p>
                    </div>

                    <label class="flex items-center gap-3 p-5 bg-white border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer w-full group">      
                        <input type="checkbox" id="agree" required 
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer group-hover:ring-2 group-hover:ring-blue-200 flex-shrink-0">
                        
                        <span class="text-gray-700 select-none text-base leading-relaxed">
                            Saya menyatakan bahwa data yang saya isikan adalah benar dan dapat dipertanggungjawabkan. Saya bersedia menerima sanksi apabila ditemukan pemalsuan data dikemudian hari.
                        </span>
                    </label>

                    <div class="mt-10 flex justify-between">
                        <button type="button" onclick="prevStep(3)" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                            &larr; Cek Lagi
                        </button>
                        
                        <button type="submit" class="bg-green-600 text-white px-10 py-3 rounded-lg font-bold hover:bg-green-700 shadow-xl transform hover:scale-105 transition flex items-center gap-2 text-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            SIMPAN PENDAFTARAN
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 4;

    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.step-badge').forEach(el => el.classList.remove('active'));

        document.getElementById('step-' + step).classList.add('active');
        
        for (let i = 1; i <= step; i++) {
            document.getElementById('badge-' + i).classList.add('active');
        }
        
        currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextStep(targetStep) {
        const currentDiv = document.getElementById('step-' + currentStep);
        const inputs = currentDiv.querySelectorAll('input, select, textarea');
        
        let isValid = true;
        
        for (const input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                isValid = false;
                
                // Tambahkan border merah sementara untuk visual feedback
                input.classList.add('border-red-500');
                input.addEventListener('input', () => input.classList.remove('border-red-500'), {once: true});
                
                break;
            }
        }

        if (isValid) {
            showStep(targetStep);
        }
    }

    function prevStep(targetStep) {
        showStep(targetStep);
    }
</script>

@endsection