@extends('layouts.app')

@section('title', 'Formulir Pendaftaran')

@section('content')

<style>
    .step-content {
        display: none;
    }
    .step-content.active {
        display: block;
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        position: relative;
    }
    /* Garis penghubung steps */
    .step-indicator::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }
    .step-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #374151;
        position: relative;
        z-index: 1; /* Agar di atas garis */
        border: 2px solid #fff; /* Border putih agar terpisah dari garis */
    }
    .step-badge.active {
        background: #3b82f6; 
        color: white;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }
    .step-label {
        position: absolute;
        top: 35px;
        font-size: 0.75rem;
        font-weight: 600;
        width: 80px;
        text-align: center;
        transform: translateX(-25px); /* Center label relative to badge */
        color: #6b7280;
    }
    .step-wrapper {
        position: relative;
    }
    .required-label::after {
        content: " *";
        color: red;
    }
</style>

<div class="max-w-6xl mx-auto pb-20">
    
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    {{ $calonSiswa ? 'Edit Data Pendaftaran' : 'Formulir Pendaftaran Baru' }}
                </h2>
                <p class="text-sm text-gray-600">
                    Tahun Ajaran: <span class="font-bold text-blue-600">{{ $tahun_aktif->tahun_ajaran }}</span> | 
                    Gelombang: <span class="font-bold text-blue-600">{{ $gelombang_aktif->nama_gelombang }}</span>
                </p>
            </div>
            @if($calonSiswa)
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $calonSiswa->status_pendaftaran == 'Diterima' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                Status: {{ $calonSiswa->status_pendaftaran }}
            </span>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
            <p class="font-bold mb-1">Terjadi Kesalahan:</p>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
        <div class="p-8">
            
            <form action="{{ route('siswa.pendaftaran.store') }}" method="POST" id="pendaftaranForm">
                @csrf
                
                <input type="hidden" name="tahun_akademik_id" value="{{ $tahun_aktif->id }}">
                <input type="hidden" name="gelombang_id" value="{{ $gelombang_aktif->id }}">

                <div class="mb-12 px-4 step-indicator">
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
                    <h3 class="text-lg font-bold mb-6 text-gray-800 border-b pb-2">Pilihan Jurusan & Identitas Diri</h3>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Pilih Kompetensi Keahlian (Jurusan)</label>
                        <select name="jurusan_tipe_kelas_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($data_jurusan_tipe_kelas as $jtk)
                                @php
                                    // Hitung sisa kuota (opsional visual)
                                    $sisa = $jtk->kuota_kelas - $jtk->jumlah_pendaftar;
                                    $label = $jtk->jurusan->nama_jurusan . ' - ' . $jtk->tipeKelas->nama_tipe_kelas;
                                    $isDisabled = $sisa <= 0 && (!$calonSiswa || $calonSiswa->jurusan_id != $jtk->jurusan_id);
                                @endphp
                                <option value="{{ $jtk->id }}" 
                                    {{ old('jurusan_tipe_kelas_id', $calonSiswa?->jurusan_id && $calonSiswa?->tipe_kelas_id ? $calonSiswa->jurusan_tipe_kelas_id_logic_disini_sebaiknya_match_id : '') == $jtk->id ? 'selected' : '' }}
                                    {{ $isDisabled ? 'disabled' : '' }}
                                >
                                    {{ $label }} (Sisa: {{ $sisa > 0 ? $sisa : 'PENUH' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">*Jika kuota penuh, jurusan tidak dapat dipilih.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">NISN</label>
                            <input type="number" name="nisn" value="{{ old('nisn', $calonSiswa?->nisn) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">NIK Siswa</label>
                            <input type="number" name="nik" value="{{ old('nik', $calonSiswa?->nik) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Nama Lengkap (Sesuai Ijazah)</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $calonSiswa?->nama_lengkap) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 uppercase">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $calonSiswa?->tempat_lahir) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $calonSiswa?->tanggal_lahir) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">- Pilih -</option>
                                <option value="L" {{ old('jenis_kelamin', $calonSiswa?->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $calonSiswa?->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Agama</label>
                            <select name="agama" required class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="Islam" {{ old('agama', $calonSiswa?->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama', $calonSiswa?->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $calonSiswa?->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $calonSiswa?->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Budha" {{ old('agama', $calonSiswa?->agama) == 'Budha' ? 'selected' : '' }}>Budha</option>
                                <option value="Konghucu" {{ old('agama', $calonSiswa?->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">No. HP / WA Siswa</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $calonSiswa?->no_hp) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Asal Sekolah (SMP/MTs)</label>
                            <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $calonSiswa?->asal_sekolah) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Tahun Lulus</label>
                            <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus', $calonSiswa?->tahun_lulus) }}" required class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 2025">
                        </div>
                         <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Anak Ke-</label>
                                <input type="number" name="anak_ke" value="{{ old('anak_ke', $calonSiswa?->anak_ke) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Jml Saudara</label>
                                <input type="number" name="jumlah_saudara" value="{{ old('jumlah_saudara', $calonSiswa?->jumlah_saudara) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                         </div>
                         <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (cm)</label>
                                <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $calonSiswa?->tinggi_badan) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Berat (kg)</label>
                                <input type="number" name="berat_badan" value="{{ old('berat_badan', $calonSiswa?->berat_badan) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                         </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="button" onclick="nextStep(2)" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-blue-700 transition flex items-center gap-2">
                            Selanjutnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                <div id="step-2" class="step-content">
                    <h3 class="text-lg font-bold mb-6 text-gray-800 border-b pb-2">Alamat Tempat Tinggal</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Alamat Lengkap (Jalan/Dusun)</label>
                            <textarea name="alamat" rows="3" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('alamat', $calonSiswa?->alamat) }}</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">RT / RW</label>
                            <input type="text" name="rt_rw" value="{{ old('rt_rw', $calonSiswa?->rt_rw) }}" required class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 005 / 002">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Desa / Kelurahan</label>
                            <input type="text" name="desa_kelurahan" value="{{ old('desa_kelurahan', $calonSiswa?->desa_kelurahan) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Kecamatan</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan', $calonSiswa?->kecamatan) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Kota / Kabupaten</label>
                            <input type="text" name="kota_kab" value="{{ old('kota_kab', $calonSiswa?->kota_kab) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 required-label">Kode Pos</label>
                            <input type="number" name="kode_pos" value="{{ old('kode_pos', $calonSiswa?->kode_pos) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="prevStep(1)" class="bg-gray-500 text-white px-6 py-2.5 rounded-lg shadow hover:bg-gray-600 transition">
                            &larr; Kembali
                        </button>
                        <button type="button" onclick="nextStep(3)" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-blue-700 transition">
                            Selanjutnya &rarr;
                        </button>
                    </div>
                </div>

                <div id="step-3" class="step-content">
                    <h3 class="text-lg font-bold mb-6 text-gray-800 border-b pb-2">Data Orang Tua / Wali</h3>
                    
                    @php
                        $ayah = $calonSiswa ? $calonSiswa->orangTua->where('hubungan', 'Ayah')->first() : null;
                        $ibu = $calonSiswa ? $calonSiswa->orangTua->where('hubungan', 'Ibu')->first() : null;
                        $wali = $calonSiswa ? $calonSiswa->orangTua->where('hubungan', 'Wali')->first() : null;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div class="bg-blue-50 p-5 rounded-lg border border-blue-100">
                            <h4 class="font-bold text-blue-800 mb-4 uppercase text-sm border-b border-blue-200 pb-2">Data Ayah Kandung</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Nama Ayah</label>
                                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $ayah?->nama_lengkap) }}" required class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">NIK Ayah</label>
                                    <input type="number" name="nik_ayah" value="{{ old('nik_ayah', $ayah?->nik) }}" required class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 uppercase">Tahun Lahir</label>
                                        <input type="number" name="tahun_lahir_ayah" value="{{ old('tahun_lahir_ayah', $ayah?->tahun_lahir) }}" required class="w-full text-sm border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 uppercase">No HP</label>
                                        <input type="text" name="nohp_ayah" value="{{ old('nohp_ayah', $ayah?->no_hp) }}" required class="w-full text-sm border-gray-300 rounded">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Pendidikan</label>
                                    <select name="pendidikan_ayah" class="w-full text-sm border-gray-300 rounded">
                                        <option value="SD" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMA/SMK" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                        <option value="S1" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ old('pendidikan_ayah', $ayah?->pendidikan_terakhir) == 'S2' ? 'selected' : '' }}>S2</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Pekerjaan</label>
                                    <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $ayah?->pekerjaan) }}" required class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Penghasilan / Bulan</label>
                                    <select name="penghasilan_ayah" class="w-full text-sm border-gray-300 rounded">
                                        <option value="< 1 Juta" {{ old('penghasilan_ayah', $ayah?->penghasilan_bulanan) == '< 1 Juta' ? 'selected' : '' }}>< 1 Juta</option>
                                        <option value="1 - 3 Juta" {{ old('penghasilan_ayah', $ayah?->penghasilan_bulanan) == '1 - 3 Juta' ? 'selected' : '' }}>1 - 3 Juta</option>
                                        <option value="3 - 5 Juta" {{ old('penghasilan_ayah', $ayah?->penghasilan_bulanan) == '3 - 5 Juta' ? 'selected' : '' }}>3 - 5 Juta</option>
                                        <option value="> 5 Juta" {{ old('penghasilan_ayah', $ayah?->penghasilan_bulanan) == '> 5 Juta' ? 'selected' : '' }}>> 5 Juta</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-pink-50 p-5 rounded-lg border border-pink-100">
                            <h4 class="font-bold text-pink-800 mb-4 uppercase text-sm border-b border-pink-200 pb-2">Data Ibu Kandung</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Nama Ibu</label>
                                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $ibu?->nama_lengkap) }}" required class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">NIK Ibu</label>
                                    <input type="number" name="nik_ibu" value="{{ old('nik_ibu', $ibu?->nik) }}" required class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 uppercase">Tahun Lahir</label>
                                        <input type="number" name="tahun_lahir_ibu" value="{{ old('tahun_lahir_ibu', $ibu?->tahun_lahir) }}" required class="w-full text-sm border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 uppercase">No HP</label>
                                        <input type="text" name="nohp_ibu" value="{{ old('nohp_ibu', $ibu?->no_hp) }}" required class="w-full text-sm border-gray-300 rounded">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Pendidikan</label>
                                    <select name="pendidikan_ibu" class="w-full text-sm border-gray-300 rounded">
                                        <option value="SD" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMA/SMK" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                        <option value="S1" {{ old('pendidikan_ibu', $ibu?->pendidikan_terakhir) == 'S1' ? 'selected' : '' }}>S1</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Pekerjaan</label>
                                    <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $ibu?->pekerjaan) }}" required class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Penghasilan / Bulan</label>
                                    <select name="penghasilan_ibu" class="w-full text-sm border-gray-300 rounded">
                                        <option value="< 1 Juta" {{ old('penghasilan_ibu', $ibu?->penghasilan_bulanan) == '< 1 Juta' ? 'selected' : '' }}>< 1 Juta</option>
                                        <option value="1 - 3 Juta" {{ old('penghasilan_ibu', $ibu?->penghasilan_bulanan) == '1 - 3 Juta' ? 'selected' : '' }}>1 - 3 Juta</option>
                                        <option value="3 - 5 Juta" {{ old('penghasilan_ibu', $ibu?->penghasilan_bulanan) == '3 - 5 Juta' ? 'selected' : '' }}>3 - 5 Juta</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 pt-6 border-t">
                        <h4 class="font-bold text-gray-700 mb-4">Data Wali (Opsional / Jika tidak tinggal bersama Orang Tua)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-gray-600">Nama Wali</label>
                                <input type="text" name="nama_wali" value="{{ old('nama_wali', $wali?->nama_lengkap) }}" class="w-full border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Hubungan dengan Siswa</label>
                                <input type="text" name="hubungan_wali" value="{{ old('hubungan_wali', $wali?->pekerjaan) }}" placeholder="Paman/Kakek/dll" class="w-full border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">No HP Wali</label>
                                <input type="text" name="nohp_wali" value="{{ old('nohp_wali', $wali?->no_hp) }}" class="w-full border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Alamat Wali</label>
                                <input type="text" name="alamat_wali" value="{{ old('alamat_wali', $wali?->alamat_wali) }}" class="w-full border-gray-300 rounded">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="prevStep(2)" class="bg-gray-500 text-white px-6 py-2.5 rounded-lg shadow hover:bg-gray-600 transition">
                            &larr; Kembali
                        </button>
                        <button type="button" onclick="nextStep(4)" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-blue-700 transition">
                            Selanjutnya &rarr;
                        </button>
                    </div>
                </div>

                <div id="step-4" class="step-content">
                    <h3 class="text-lg font-bold mb-6 text-gray-800 border-b pb-2">Konfirmasi Data</h3>
                    
                    <div class="bg-yellow-50 p-6 border border-yellow-200 rounded-lg text-center mb-6">
                        <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h4 class="text-lg font-bold text-gray-800 mb-2">Apakah data sudah benar?</h4>
                        <p class="text-gray-700">Pastikan Anda telah mengisi semua data dengan benar dan sesuai dokumen asli (KK/Ijazah).</p>
                        <p class="text-sm text-red-600 font-bold mt-2">Data tidak dapat diubah sendiri setelah tombol Simpan ditekan!</p>
                    </div>

                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded border">
                        <input type="checkbox" id="agree" required class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="agree" class="text-sm text-gray-700 cursor-pointer">
                            Saya menyatakan bahwa data yang saya isikan adalah benar dan dapat dipertanggungjawabkan. Saya bersedia menerima sanksi apabila ditemukan pemalsuan data dikemudian hari.
                        </label>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="prevStep(3)" class="bg-gray-500 text-white px-6 py-2.5 rounded-lg shadow hover:bg-gray-600 transition">
                            &larr; Cek Lagi
                        </button>
                        
                        <button type="submit" class="bg-green-600 text-white px-8 py-2.5 rounded-lg font-bold hover:bg-green-700 shadow-lg transform hover:scale-105 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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
        // 1. Sembunyikan semua konten step
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        // 2. Reset warna badge indikator
        document.querySelectorAll('.step-badge').forEach(el => el.classList.remove('active'));

        // 3. Tampilkan step yang dipilih
        document.getElementById('step-' + step).classList.add('active');
        
        // 4. Highlight badge sampai step saat ini
        for (let i = 1; i <= step; i++) {
            document.getElementById('badge-' + i).classList.add('active');
        }
        
        currentStep = step;
        
        // Scroll ke atas otomatis
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextStep(targetStep) {
        // Validasi HTML5 Native (Cek Required, Email, dll sebelum lanjut)
        const currentDiv = document.getElementById('step-' + currentStep);
        const inputs = currentDiv.querySelectorAll('input, select, textarea');
        
        let isValid = true;
        
        // Cek validitas setiap input di halaman aktif
        for (const input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity(); // Munculkan pesan error browser
                isValid = false;
                break; // Stop di error pertama
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