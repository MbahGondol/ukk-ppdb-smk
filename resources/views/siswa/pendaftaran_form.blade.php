<!DOCTYPE html>
<html lang="en">
<head><title>Formulir Pendaftaran Siswa Baru</title></head>
<body>
    <h1>Formulir Pendaftaran</h1>
    <p>Tahun Ajaran: <strong>{{ $tahun_aktif->tahun_ajaran }}</strong></p>
    <p>Gelombang: <strong>{{ $gelombang_aktif->nama_gelombang }}</strong></p>
    <hr>

    @if ($errors->any())
        <div style="color: red;">
            <strong>Error! Data tidak valid:</strong>
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('siswa.pendaftaran.store') }}" method="POST">
        @csrf

        <input type="hidden" name="tahun_akademik_id" value="{{ $tahun_aktif->id }}">
        <input type="hidden" name="gelombang_id" value="{{ $gelombang_aktif->id }}">

        <h3>Data Pribadi Calon Siswa</h3>
        <div>
            <label>NISN:</label><br>
            <input type="text" name="nisn" value="{{ old('nisn') }}" required>
        </div>
        <div>
            <label>NIK:</label><br>
            <input type="text" name="nik" value="{{ old('nik') }}" required>
        </div>
        <div>
            <label>Nama Lengkap:</label><br>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
        </div>
        <div>
            <label>Jenis Kelamin:</label><br>
            <select name="jenis_kelamin" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div>
            <label>Tempat Lahir:</label><br>
            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required>
        </div>
        <div>
            <label>Tanggal Lahir (YYYY-MM-DD):</label><br>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
        </div>
        <div>
            <label>Agama:</label><br>
            <input type="text" name="agama" value="{{ old('agama') }}" required>
        </div>
        <div>
            <label>No. HP (WhatsApp):</label><br>
            <input type="text" name="no_hp" value="{{ old('no_hp') }}" required>
        </div>
        <div>
            <label>Asal Sekolah:</label><br>
            <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required>
        </div>

        <h3>Pilihan Jurusan</h3>
        <div>
            <label>Pilih Jurusan & Tipe Kelas:</label><br>
            <select name="jurusan_tipe_kelas_id" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach ($data_jurusan_tipe_kelas as $kombinasi)
                    <option value="{{ $kombinasi->id }}" {{ old('jurusan_tipe_kelas_id') == $kombinasi->id ? 'selected' : '' }}>
                        {{ $kombinasi->jurusan->nama_jurusan }} ({{ $kombinasi->tipeKelas->nama_tipe_kelas }})
                    </option>
                @endforeach
            </select>
        </div>

        <hr>
        <h3>Data Ayah</h3>
        <div>
            <label>Nama Ayah:</label><br>
            <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" required>
        </div>
        <div>
            <label>NIK Ayah:</label><br>
            <input type="text" name="nik_ayah" value="{{ old('nik_ayah') }}">
        </div>

        <hr>
        <h3>Data Ibu</h3>
        <div>
            <label>Nama Ibu:</label><br>
            <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" required>
        </div>
        
        <hr>
        <h3>Data Wali (Isi jika tinggal dengan Wali)</h3>
        <div>
            <label>Nama Wali:</label><br>
            <input type="text" name="nama_wali" value="{{ old('nama_wali') }}">
        </div>

        <br><br>
        <button type="submit">Simpan & Lanjutkan</button>
    </form>
    
    <br>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

</body>
</html>