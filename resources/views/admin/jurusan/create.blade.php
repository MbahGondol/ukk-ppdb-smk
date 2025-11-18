<!DOCTYPE html>
<html lang="en">
<head><title>Tambah Jurusan</title></head>
<body>
    <h1>Tambah Jurusan Baru</h1>

    @if ($errors->any())
        <div style="color: red;">
            <strong>Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.jurusan.store') }}" method="POST">
        @csrf <div>
            <label>Kode Jurusan (cth: RPL, TKJ):</label><br>
            <input type="text" name="kode_jurusan" value="{{ old('kode_jurusan') }}">
        </div>
        <br>
        <div>
            <label>Nama Jurusan Lengkap:</label><br>
            <input type="text" name="nama_jurusan" value="{{ old('nama_jurusan') }}" style="width: 300px;">
        </div>
        <br>
        <div>
            <label>Deskripsi (Opsional):</label><br>
            <textarea name="deskripsi" rows="4" style="width: 300px;">{{ old('deskripsi') }}</textarea>
        </div>
        <br>
        <button type="submit">Simpan Jurusan</button>
    </form>
    <br>
    <a href="{{ route('admin.jurusan.index') }}">Batal</a>
</body>
</html>