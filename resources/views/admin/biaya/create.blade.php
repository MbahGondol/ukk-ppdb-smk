<!DOCTYPE html>
<html lang="en">
<head><title>Tambah Harga Baru</title></head>
<body>
    <h1>Tambah Harga Baru</h1>

    @if ($errors->any())
        <div style="color: red;">
            <strong>Error!</strong>
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.biaya.store') }}" method="POST">
        @csrf
        <div>
            <label>Pilih Tipe Biaya:</label><br>
            <select name="jenis_biaya_id" style="width: 300px;">
                <option value="">-- Pilih Tipe Biaya --</option>
                @foreach ($data_jenis_biaya as $jenis)
                    <option value="{{ $jenis->id }}" {{ old('jenis_biaya_id') == $jenis->id ? 'selected' : '' }}>
                        {{ $jenis->nama_biaya }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>
        <div>
            <label>Pilih Kombinasi Jurusan & Kelas:</label><br>
            <select name="jurusan_tipe_kelas_id" style="width: 300px;">
                <option value="">-- Pilih Jurusan --</option>
                @foreach ($data_jurusan_tipe_kelas as $kombinasi)
                    <option value="{{ $kombinasi->id }}" {{ old('jurusan_tipe_kelas_id') == $kombinasi->id ? 'selected' : '' }}>
                        {{ $kombinasi->jurusan->nama_jurusan }} ({{ $kombinasi->tipeKelas->nama_tipe_kelas }})
                    </option>
                @endforeach
            </select>
        </div>
        <br>
        <div>
            <label>Nominal Harga (Rp):</label><br>
            <input type="number" name="nominal" value="{{ old('nominal', 0) }}">
        </div>
        <br>
        <div>
            <label>Catatan (Opsional):</label><br>
            <textarea name="catatan" rows="3" style="width: 300px;">{{ old('catatan') }}</textarea>
        </div>
        <br>
        <button type="submit">Simpan Harga</button>
    </form>
    <br>
    <a href="{{ route('admin.biaya.index') }}">Batal</a>
</body>
</html>