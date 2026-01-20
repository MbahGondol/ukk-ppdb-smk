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
            
            @php
                // Cek apakah ada data siswa (Mode Edit) atau tidak (Mode Daftar Baru)
                $isEdit = isset($calonSiswa) && $calonSiswa;
                
                // Tentukan URL tujuan: ke 'update' jika edit, ke 'store' jika baru
                $urlAction = $isEdit 
                    ? route('siswa.pendaftaran.update', $calonSiswa->id) 
                    : route('siswa.pendaftaran.store');
            @endphp

        <form action="{{ $urlAction }}" method="POST" id="pendaftaranForm">
            @csrf
            
            {{-- Jika Mode Edit, kita harus memalsukan method menjadi PUT agar Controller Update merespon --}}
            @if($isEdit)
                @method('PUT')
            @endif

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
                                    // Hitung sisa kuota
                                    $sisa = $jtk->kuota_kelas - $jtk->jumlah_pendaftar;
                                    
                                    // Cek apakah ini jurusan siswa saat ini?
                                    $isMyChoice = $calonSiswa && 
                                                  $calonSiswa->jurusan_id == $jtk->jurusan_id && 
                                                  $calonSiswa->tipe_kelas_id == $jtk->tipe_kelas_id;

                                    // Disable jika penuh KECUALI ini adalah pilihan siswa itu sendiri (sedang diedit)
                                    $isDisabled = $sisa <= 0 && !$isMyChoice;
                                @endphp
                                
                                <option value="{{ $jtk->id }}" 
                                    {{-- Logika selection: Prioritas old input (jika gagal validasi), jika tidak ada cek data database --}}
                                    {{ old('jurusan_tipe_kelas_id', $isMyChoice ? $jtk->id : '') == $jtk->id ? 'selected' : '' }}
                                    {{ $isDisabled ? 'disabled' : '' }}
                                >
                                    {{ $jtk->jurusan->nama_jurusan }} - {{ $jtk->tipeKelas->nama_tipe_kelas }} (Sisa Kuota: {{ $sisa > 0 ? $sisa : 'PENUH' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div> 
                            <label class="required-label font-bold text-gray-700">NISN</label>
                            <input type="text" 
                                name="nisn" 
                                value="{{ old('nisn', $calonSiswa?->nisn) }}" 
                                required 
                                class="form-control-lg font-mono tracking-wide"
                                placeholder="10 digit angka"
                                inputmode="numeric" 
                                pattern="[0-9]*" 
                                minlength="10" 
                                maxlength="10"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>

                        <div>
                            <label class="required-label font-bold text-gray-700">NIK Siswa</label>
                            <input type="text" 
                                name="nik" 
                                value="{{ old('nik', $calonSiswa?->nik) }}" 
                                required 
                                class="form-control-lg font-mono tracking-wide" 
                                placeholder="16 digit angka sesuai KK"
                                inputmode="numeric" 
                                pattern="[0-9]*" 
                                minlength="16" 
                                maxlength="16"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>

                        {{-- BARIS 2: NAMA LENGKAP (Full Width) --}}
                        <div class="md:col-span-2">
                            <label class="required-label font-bold text-gray-700">Nama Lengkap (Sesuai Ijazah)</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $calonSiswa?->nama_lengkap) }}" required class="form-control-lg uppercase" placeholder="Nama lengkap tanpa gelar">
                        </div>
                        
                        {{-- BARIS 3: TTL (Logis berdampingan) --}}
                        <div>
                            <label class="required-label font-bold text-gray-700">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $calonSiswa?->tempat_lahir) }}" required class="form-control-lg" placeholder="Kota kelahiran">
                        </div>
                        <div>
                            <label class="required-label font-bold text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $calonSiswa?->tanggal_lahir ? \Carbon\Carbon::parse($calonSiswa->tanggal_lahir)->format('Y-m-d') : '') }}" required class="form-control-lg cursor-pointer">
                        </div>
                        
                        {{-- BARIS 4: JK & AGAMA --}}
                        <div>
                            <label class="required-label font-bold text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required class="form-control-lg cursor-pointer bg-white">
                                <option value="">- Pilih -</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $calonSiswa?->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $calonSiswa?->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="required-label font-bold text-gray-700">Agama</label>
                            <select name="agama" required class="form-control-lg cursor-pointer bg-white">
                                <option value="Islam" {{ old('agama', $calonSiswa?->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                {{-- Option lainnya tetap sama --}}
                                <option value="Kristen" {{ old('agama', $calonSiswa?->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $calonSiswa?->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $calonSiswa?->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Budha" {{ old('agama', $calonSiswa?->agama) == 'Budha' ? 'selected' : '' }}>Budha</option>
                                <option value="Konghucu" {{ old('agama', $calonSiswa?->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                        </div>

                        {{-- BARIS 5: Kontak & Asal Sekolah --}}
                        <div>
                            <label class="required-label font-bold text-gray-700">No. HP / WA Siswa</label>
                            <input type="text" 
                                name="no_hp" 
                                value="{{ old('no_hp', $calonSiswa?->no_hp) }}" 
                                required 
                                class="form-control-lg font-mono" 
                                placeholder="08xxxxxxxxxx"
                                inputmode="numeric" 
                                pattern="[0-9]*"
                                minlength="10" 
                                maxlength="13"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div>
                            <label class="required-label font-bold text-gray-700">Asal Sekolah (SMP/MTs)</label>
                            <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $calonSiswa?->asal_sekolah) }}" required class="form-control-lg" placeholder="Nama sekolah asal">
                        </div>

                    </div>    

                        <div class="mt-6 md:col-span-2 grid grid-cols-2 md:grid-cols-5 gap-4 bg-gray-50 p-6 rounded-xl border border-gray-200">
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

                        <div class="mt-10 flex justify-end">
                            <button type="button" onclick="nextStep(2)" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg hover:shadow-xl transition flex items-center gap-3 font-semibold text-lg">
                            Lanjut ke Alamat <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                        </div>
                </div>

                <div id="step-2" class="step-content">
                    <div class="mb-8 pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800">Alamat Tempat Tinggal</h3>
                        <p class="text-gray-500 text-sm mt-1">Isi data domisili sesuai dengan Kartu Keluarga (KK) terbaru.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">                       
                        {{-- ALAMAT JALAN --}}
                        <div class="md:col-span-12">
                            <label class="required-label font-bold text-gray-700 mb-1.5">Alamat Lengkap (Jalan/Gang)</label>
                            <textarea name="alamat" rows="2" required class="form-control-lg" placeholder="Contoh: Jl. Merpati No. 45">{{ old('alamat', $calonSiswa?->alamat) }}</textarea>
                        </div>
                        
                        {{-- RT / RW --}}
                        <div class="md:col-span-3"> 
                            <label class="required-label font-bold text-gray-700 mb-1.5">RT / RW</label>
                            <input type="text" name="rt_rw" value="{{ old('rt_rw', $calonSiswa?->rt_rw) }}" 
                                required 
                                class="form-control-lg font-mono text-center" 
                                placeholder="001/005"
                                maxlength="10"
                                oninput="this.value = this.value.replace(/[^0-9\/]/g, '');">
                        </div>

                        {{-- PROVINSI (Baru) --}}
                        <div class="md:col-span-5">
                            <label class="required-label font-bold text-gray-700 mb-1.5">Provinsi</label>
                            <select id="select-provinsi" class="form-control-lg bg-white" required>
                                <option value="">Pilih Provinsi...</option>
                            </select>
                            <input type="hidden" name="provinsi" id="input-provinsi" value="{{ old('provinsi', $calonSiswa?->provinsi) }}">
                        </div>

                        {{-- KOTA / KAB (Otomatis) --}}
                        <div class="md:col-span-4">
                            <label class="required-label font-bold text-gray-700 mb-1.5">Kota / Kabupaten</label>
                            <select id="select-kota" class="form-control-lg bg-gray-100" disabled required>
                                <option value="">Pilih Provinsi Dulu...</option>
                            </select>
                            <input type="hidden" name="kota_kab" id="input-kota" value="{{ old('kota_kab', $calonSiswa?->kota_kab) }}">
                        </div>

                        {{-- KECAMATAN (Otomatis) --}}
                        <div class="md:col-span-4">
                            <label class="required-label font-bold text-gray-700 mb-1.5">Kecamatan</label>
                            <select id="select-kecamatan" class="form-control-lg bg-gray-100" disabled required>
                                <option value="">Pilih Kota Dulu...</option>
                            </select>
                            <input type="hidden" name="kecamatan" id="input-kecamatan" value="{{ old('kecamatan', $calonSiswa?->kecamatan) }}">
                        </div>

                        {{-- DESA / KELURAHAN (Otomatis) --}}
                        <div class="md:col-span-4"> 
                            <label class="required-label font-bold text-gray-700 mb-1.5">Desa / Kelurahan</label>
                            <select id="select-desa" class="form-control-lg bg-gray-100" disabled required>
                                <option value="">Pilih Kecamatan Dulu...</option>
                            </select>
                            <input type="hidden" name="desa_kelurahan" id="input-desa" value="{{ old('desa_kelurahan', $calonSiswa?->desa_kelurahan) }}">
                        </div>

                        {{-- KODE POS --}}
                        <div class="md:col-span-4">
                            <label class="required-label font-bold text-gray-700 mb-1.5">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $calonSiswa?->kode_pos) }}" 
                                required 
                                class="form-control-lg font-mono tracking-wider" 
                                placeholder="5 digit"
                                maxlength="5"
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);"> 
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
                        <p class="text-gray-500 text-sm mt-1">Silakan pilih penanggung jawab siswa selama bersekolah.</p>
                    </div>
                    
                    @php
                        $ayah = $calonSiswa ? $calonSiswa->data_ayah : null;
                        $ibu  = $calonSiswa ? $calonSiswa->data_ibu : null;
                        $wali = $calonSiswa ? $calonSiswa->data_wali : null;
                    @endphp

                    {{-- PILIHAN TINGGAL BERSAMA --}}
                    <div class="mb-8 bg-blue-50 p-4 rounded-xl border border-blue-200">
                        <label class="font-bold text-gray-800 mb-2 block">Siswa Tinggal Bersama:</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tinggal_bersama" value="ortu" class="w-5 h-5 text-blue-600" checked onchange="toggleWali(false)">
                                <span class="font-medium">Orang Tua Kandung</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tinggal_bersama" value="wali" class="w-5 h-5 text-blue-600" onchange="toggleWali(true)">
                                <span class="font-medium">Wali (Kerabat/Lainnya)</span>
                            </label>
                        </div>
                    </div>

                    {{-- WRAPPER DATA ORTU --}}
                    <div id="section-ortu" class="transition-all duration-300">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            {{-- AYAH --}}
                            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                                <div class="flex items-center gap-3 mb-6 border-b border-slate-200 pb-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-lg">DATA AYAH</h4>
                                </div>
                                <div class="space-y-4">
                                    <div><label class="required-label">Nama Lengkap Ayah</label><input type="text" name="nama_ayah" value="{{ old('nama_ayah', $ayah?->nama_lengkap) }}" required class="form-control-lg input-ortu"></div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="col-span-1"><label>NIK Ayah</label><input type="text" name="nik_ayah" value="{{ old('nik_ayah', $ayah?->nik) }}" class="form-control-lg input-ortu" maxlength="16" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></div>
                                        <div><label>Tahun Lahir</label><input type="number" name="tahun_lahir_ayah" value="{{ old('tahun_lahir_ayah', $ayah?->tahun_lahir) }}" required class="form-control-lg text-center input-ortu"></div>
                                    </div>
                                    <div><label>No. HP</label><input type="text" name="nohp_ayah" value="{{ old('nohp_ayah', $ayah?->no_hp) }}" class="form-control-lg input-ortu" maxlength="13" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><label>Pendidikan</label>
                                            <select name="pendidikan_ayah" class="form-control-lg input-ortu">
                                                @foreach(['SD', 'SMP', 'SMA/SMK', 'S1', 'S2', 'S3', 'Tidak Sekolah'] as $p)
                                                    <option value="{{ $p }}" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == $p ? 'selected' : '' }}>
                                                        {{ $p }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div><label>Penghasilan</label><input type="number" name="penghasilan_ayah" value="{{ old('penghasilan_ayah', $ayah?->penghasilan_bulanan ? (int)$ayah->penghasilan_bulanan : '') }}" class="form-control-lg input-ortu"></div>
                                    </div>
                                    <div><label>Pekerjaan</label><input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $ayah?->pekerjaan) }}" required class="form-control-lg input-ortu"></div>
                                </div>
                            </div>
                            {{-- IBU --}}
                            <div class="bg-pink-50/50 p-6 rounded-2xl border border-pink-100">
                                <div class="flex items-center gap-3 mb-6 border-b border-pink-200 pb-3">
                                    <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <h4 class="font-bold text-pink-800 text-lg">DATA IBU</h4>
                                </div>
                                <div class="space-y-4">
                                    <div><label class="required-label">Nama Lengkap Ibu</label><input type="text" name="nama_ibu" value="{{ old('nama_ibu', $ibu?->nama_lengkap) }}" required class="form-control-lg input-ortu"></div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="col-span-1"><label>NIK Ibu</label><input type="text" name="nik_ibu" value="{{ old('nik_ibu', $ibu?->nik) }}" class="form-control-lg input-ortu" maxlength="16" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></div>
                                        <div><label>Tahun Lahir</label><input type="number" name="tahun_lahir_ibu" value="{{ old('tahun_lahir_ibu', $ibu?->tahun_lahir) }}" required class="form-control-lg text-center input-ortu"></div>
                                    </div>
                                    <div><label>No. HP</label><input type="text" name="nohp_ibu" value="{{ old('nohp_ibu', $ibu?->no_hp) }}" class="form-control-lg input-ortu" maxlength="13" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><label>Pendidikan</label>
                                            <select name="pendidikan_ibu" class="form-control-lg input-ortu">
                                                @foreach(['SD', 'SMP', 'SMA/SMK', 'S1', 'S2', 'S3', 'Tidak Sekolah'] as $p)
                                                    <option value="{{ $p }}" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == $p ? 'selected' : '' }}>
                                                        {{ $p }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div><label>Penghasilan</label><input type="number" name="penghasilan_ibu" value="{{ old('penghasilan_ibu', $ibu?->penghasilan_bulanan ? (int)$ibu->penghasilan_bulanan : '') }}" class="form-control-lg input-ortu"></div>
                                    </div>
                                    <div><label>Pekerjaan</label><input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $ibu?->pekerjaan) }}" required class="form-control-lg input-ortu"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- WRAPPER DATA WALI --}}
                    <div id="section-wali" class="mt-8 pt-6 border-t border-dashed border-gray-300 hidden transition-all duration-300">
                        <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Data Wali Murid
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-gray-50 rounded-xl border border-gray-200">
                            <div><label class="required-label">Nama Wali</label><input type="text" name="nama_wali" value="{{ old('nama_wali', $wali?->nama_lengkap) }}" class="form-control-lg input-wali"></div>
                            <div><label class="required-label">Hubungan</label><input type="text" name="hubungan_wali" value="{{ old('hubungan_wali', $wali?->pekerjaan) }}" placeholder="Contoh: Paman" class="form-control-lg input-wali"></div>
                            <div><label class="required-label">No. HP</label><input type="text" name="nohp_wali" value="{{ old('nohp_wali', $wali?->no_hp) }}" class="form-control-lg input-wali" maxlength="13" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></div>
                            <div><label class="required-label">Alamat Wali</label><input type="text" name="alamat_wali" value="{{ old('alamat_wali', $wali?->alamat_wali) }}" class="form-control-lg input-wali"></div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-between">
                        <button type="button" onclick="prevStep(2)" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">&larr; Kembali</button>
                        <button type="button" onclick="nextStep(4)" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg transition font-semibold">Cek Data & Konfirmasi &rarr;</button>
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
                        <p class="text-gray-600 max-w-lg mx-auto">
                            Pastikan Anda telah mengisi semua data dengan benar sesuai dokumen asli (KK/Ijazah). 
                            Data yang tidak valid dapat <span class="font-bold text-red-600">menghambat proses verifikasi</span>.
                        </p>
                    </div>

                    <label class="flex gap-4 p-5 bg-white border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer w-full group" style="align-items: flex-start;">       
                        <input type="checkbox" id="agree" required 
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer group-hover:ring-2 group-hover:ring-blue-200 flex-shrink-0"
                            style="margin-top: 0.25rem;">
                        
                        <span class="text-gray-700 select-none text-base leading-relaxed">
                            Saya menyatakan bahwa data yang saya isikan adalah benar dan dapat dipertanggungjawabkan. 
                            Saya bersedia menerima <strong>sanksi apabila ditemukan pemalsuan data</strong> di kemudian hari.
                        </span>
                    </label>

                    <div class="mt-10 flex justify-between">
                        <button type="button" onclick="prevStep(3)" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                            &larr; Cek Lagi
                        </button>
                        
                        <button type="submit" class="bg-green-600 text-white px-10 py-3 rounded-lg font-bold hover:bg-green-700 shadow-xl transform hover:scale-105 transition flex items-center gap-2 text-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ $calonSiswa ? 'SIMPAN PERUBAHAN' : 'DAFTAR SEKARANG' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async function() {

        // --- 0. AUTO-RESTORE DRAFT (ANTI-REFRESH) ---
        const formId = 'pendaftaranForm';
        
        // Fungsi Simpan Draft saat ngetik
        function saveDraft() {
            const formData = new FormData(document.getElementById(formId));
            const data = {};
            formData.forEach((value, key) => data[key] = value);
            // Simpan hidden inputs untuk wilayah juga
            data['provinsi'] = document.getElementById('input-provinsi').value;
            data['kota_kab'] = document.getElementById('input-kota').value;
            data['kecamatan'] = document.getElementById('input-kecamatan').value;
            data['desa_kelurahan'] = document.getElementById('input-desa').value;
            
            localStorage.setItem('ppdb_draft_data', JSON.stringify(data));
        }

        // Event Listener untuk Auto-Save (Setiap ada perubahan)
        document.getElementById(formId).addEventListener('input', saveDraft);
        document.getElementById(formId).addEventListener('change', saveDraft);

        // Fungsi Restore Draft saat halaman dimuat
        function restoreDraft() {
            const raw = localStorage.getItem('ppdb_draft_data');
            if (!raw) return;

            try {
                const data = JSON.parse(raw);
                
                // 1. Restore Input Biasa
                Object.keys(data).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input && input.type !== 'file' && input.type !== 'hidden') {
                        input.value = data[key];
                    }
                });

                // 2. Restore Hidden Input Wilayah (Agar logika API mu jalan)
                if(data['provinsi']) document.getElementById('input-provinsi').value = data['provinsi'];
                if(data['kota_kab']) document.getElementById('input-kota').value = data['kota_kab'];
                if(data['kecamatan']) document.getElementById('input-kecamatan').value = data['kecamatan'];
                if(data['desa_kelurahan']) document.getElementById('input-desa').value = data['desa_kelurahan'];

            } catch (e) {
                console.error("Gagal restore draft:", e);
            }
        }

        // JALANKAN RESTORE SEBELUM LOGIKA LAIN!
        restoreDraft();

        // HAPUS DRAFT SAAT SUBMIT SUKSES
        document.getElementById(formId).addEventListener('submit', function() {
            localStorage.removeItem('ppdb_draft_data');
            localStorage.removeItem('ppdb_last_step_v1');
        });

        // --- 1. SETUP VARIABEL & URL ---
        const baseUrlWilayah = "https://www.emsifa.com/api-wilayah-indonesia/api";
        
        const els = {
            prov: { select: document.getElementById('select-provinsi'), input: document.getElementById('input-provinsi') },
            kota: { select: document.getElementById('select-kota'),     input: document.getElementById('input-kota') },
            kec:  { select: document.getElementById('select-kecamatan'), input: document.getElementById('input-kecamatan') },
            desa: { select: document.getElementById('select-desa'),      input: document.getElementById('input-desa') }
        };

        // Helper: Cari Option ID berdasarkan Nama (Case Insensitive)
        function findIdByName(selectEl, nameToFind) {
            if (!nameToFind) return null;
            const options = Array.from(selectEl.options);
            const found = options.find(opt => {
                const dataName = opt.getAttribute('data-name');
                return dataName && dataName.toUpperCase() === nameToFind.toUpperCase();
            });
            return found ? found.value : null;
        }

        // Helper: Load Data API
        async function loadData(url, selectEl, placeholder) {
            selectEl.innerHTML = '<option value="">Loading...</option>';
            selectEl.disabled = true;
            try {
                const res = await fetch(url);
                const data = await res.json();
                let opts = `<option value="">${placeholder}</option>`;
                data.forEach(item => {
                    opts += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
                });
                selectEl.innerHTML = opts;
                selectEl.disabled = false;
            } catch (error) {
                console.error("Gagal load data:", error);
                selectEl.innerHTML = '<option value="">Gagal Memuat</option>';
            }
        }

        // --- 2. LOGIKA UTAMA (LOAD & RESTORE - DB AWARE) ---
        
        // Fungsi helper untuk menunggu elemen select terisi
        const waitForOptions = (selectEl) => {
            return new Promise(resolve => {
                const check = setInterval(() => {
                    if (selectEl.options.length > 1) { 
                        clearInterval(check);
                        resolve();
                    }
                }, 100);
                setTimeout(() => { clearInterval(check); resolve(); }, 5000); 
            });
        };

        // A. Load Provinsi
        await loadData(`${baseUrlWilayah}/provinces.json`, els.prov.select, 'Pilih Provinsi...');

        // B. Cek Data Tersimpan (Prioritas: Draft LocalStorage -> DB Value)
        // Kita ambil value dari input hidden yang sudah diisi oleh Blade (old/db)
        const dbProv = els.prov.input.value;
        const dbKota = els.kota.input.value;
        const dbKec  = els.kec.input.value;
        const dbDesa = els.desa.input.value;

        if (dbProv) {
            // Cari ID provinsi berdasarkan NAMA yang tersimpan
            const provId = findIdByName(els.prov.select, dbProv);
            if (provId) {
                els.prov.select.value = provId;
                
                // Trigger Load Kota
                await loadData(`${baseUrlWilayah}/regencies/${provId}.json`, els.kota.select, 'Pilih Kota/Kab...');
                
                if (dbKota) {
                    const kotaId = findIdByName(els.kota.select, dbKota);
                    if (kotaId) {
                        els.kota.select.value = kotaId;
                        
                        // Trigger Load Kecamatan
                        await loadData(`${baseUrlWilayah}/districts/${kotaId}.json`, els.kec.select, 'Pilih Kecamatan...');
                        
                        if (dbKec) {
                            const kecId = findIdByName(els.kec.select, dbKec);
                            if (kecId) {
                                els.kec.select.value = kecId;
                                
                                // Trigger Load Desa
                                await loadData(`${baseUrlWilayah}/villages/${kecId}.json`, els.desa.select, 'Pilih Desa...');
                                
                                if (dbDesa) {
                                    const desaId = findIdByName(els.desa.select, dbDesa);
                                    if (desaId) els.desa.select.value = desaId;
                                }
                            }
                        }
                    }
                }
            }
        }

        // --- 3. EVENT LISTENERS (INTERAKSI USER) ---
        
        els.prov.select.addEventListener('change', async function() {
            const id = this.value;
            const name = this.options[this.selectedIndex].getAttribute('data-name');
            els.prov.input.value = name || ''; // Simpan ke Hidden

            // Reset anak-anaknya
            els.kota.select.innerHTML = '<option value="">Pilih Provinsi Dulu...</option>'; els.kota.select.disabled = true; els.kota.input.value = '';
            els.kec.select.innerHTML  = '<option value="">Pilih Kota Dulu...</option>'; els.kec.select.disabled = true; els.kec.input.value = '';
            els.desa.select.innerHTML = '<option value="">Pilih Kecamatan Dulu...</option>'; els.desa.select.disabled = true; els.desa.input.value = '';

            if(id) await loadData(`${baseUrlWilayah}/regencies/${id}.json`, els.kota.select, 'Pilih Kota/Kab...');
        });

        els.kota.select.addEventListener('change', async function() {
            const id = this.value;
            const name = this.options[this.selectedIndex].getAttribute('data-name');
            els.kota.input.value = name || '';

            els.kec.select.innerHTML  = '<option value="">Pilih Kota Dulu...</option>'; els.kec.select.disabled = true; els.kec.input.value = '';
            els.desa.select.innerHTML = '<option value="">Pilih Kecamatan Dulu...</option>'; els.desa.select.disabled = true; els.desa.input.value = '';

            if(id) await loadData(`${baseUrlWilayah}/districts/${id}.json`, els.kec.select, 'Pilih Kecamatan...');
        });

        els.kec.select.addEventListener('change', async function() {
            const id = this.value;
            const name = this.options[this.selectedIndex].getAttribute('data-name');
            els.kec.input.value = name || '';

            els.desa.select.innerHTML = '<option value="">Pilih Kecamatan Dulu...</option>'; els.desa.select.disabled = true; els.desa.input.value = '';

            if(id) await loadData(`${baseUrlWilayah}/villages/${id}.json`, els.desa.select, 'Pilih Desa...');
        });

        els.desa.select.addEventListener('change', function() {
            const name = this.options[this.selectedIndex].getAttribute('data-name');
            els.desa.input.value = name || '';
        });

        // --- 4. STEPPER LOGIC (YANG ANDA PUNYA) ---       
        window.currentStep = 1;

        window.showStep = function(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-badge').forEach(el => el.classList.remove('active'));

            document.getElementById('step-' + step).classList.add('active');
            for (let i = 1; i <= step; i++) {
                document.getElementById('badge-' + i).classList.add('active');
            }
            window.currentStep = step;
            localStorage.setItem('ppdb_last_step_v1', step);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        window.nextStep = function(targetStep) {
            const currentDiv = document.getElementById('step-' + window.currentStep);
            if (!currentDiv) return; 

            const inputs = currentDiv.querySelectorAll('input, select, textarea');
            let isValid = true;
            for (const input of inputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    isValid = false;
                    input.classList.add('border-red-500');
                    input.addEventListener('input', () => input.classList.remove('border-red-500'), {once: true});
                    break;
                }
            }
            if (isValid) window.showStep(targetStep);
        }

        window.prevStep = function(targetStep) {
            window.showStep(targetStep);
        }

        // Restore Step Terakhir
        const savedStep = localStorage.getItem('ppdb_last_step_v1');
        if (savedStep && parseInt(savedStep) > 1) {
            window.showStep(parseInt(savedStep));
        }

        // --- 6. LOADING STATE SAAT SUBMIT ---
        const form = document.getElementById('pendaftaranForm');
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            
            // Cek validitas dulu (manual check karena kita override submit)
            if(this.checkValidity()) {
                // Ubah tampilan tombol
                btn.disabled = true;
                btn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sedang Memproses...
                `;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        });

    });

    // --- 5. LOGIKA TOGGLE ORTU / WALI (Permintaan Pak Putra) ---
    function toggleWali(isWali) {
        const secOrtu = document.getElementById('section-ortu');
        const secWali = document.getElementById('section-wali');
        
        const inputsOrtu = document.querySelectorAll('.input-ortu');
        const inputsWali = document.querySelectorAll('.input-wali');

        if (isWali) {
            // Mode Wali: Sembunyikan Ortu, Munculkan Wali
            secOrtu.classList.add('hidden');
            secWali.classList.remove('hidden');

            // Matikan 'required' di Ortu agar bisa submit
            inputsOrtu.forEach(el => el.removeAttribute('required'));
            
            // Nyalakan 'required' di Wali
            inputsWali.forEach(el => el.setAttribute('required', 'required'));
        } else {
            // Mode Ortu: Sembunyikan Wali, Munculkan Ortu
            secOrtu.classList.remove('hidden');
            secWali.classList.add('hidden');

            // Nyalakan 'required' di Ortu (Field tertentu saja yg wajib)
            // Note: Hati-hati, tidak semua field ortu wajib. 
            // Kita kembalikan logic required sesuai HTML awal.
            // Cara cepat: Set required hanya ke nama & pekerjaan (sesuai form awal)
            document.getElementsByName('nama_ayah')[0].setAttribute('required', 'required');
            document.getElementsByName('nama_ibu')[0].setAttribute('required', 'required');
            document.getElementsByName('pekerjaan_ayah')[0].setAttribute('required', 'required');
            document.getElementsByName('pekerjaan_ibu')[0].setAttribute('required', 'required');
            document.getElementsByName('tahun_lahir_ayah')[0].setAttribute('required', 'required');
            document.getElementsByName('tahun_lahir_ibu')[0].setAttribute('required', 'required');

            // Matikan 'required' di Wali
            inputsWali.forEach(el => el.removeAttribute('required'));
        }
    }

    // Jalankan sekali saat load untuk memastikan status awal
    // (Misal saat edit data dan ternyata dia mode Wali)
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah ada data wali tersimpan? Kalau ada, auto switch ke mode wali
        const adaWali = document.querySelector('input[name="nama_wali"]').value !== "";
        if(adaWali) {
            document.querySelector('input[value="wali"]').checked = true;
            toggleWali(true);
        } else {
            toggleWali(false); // Default Ortu
        }
    });

</script>

@endsection