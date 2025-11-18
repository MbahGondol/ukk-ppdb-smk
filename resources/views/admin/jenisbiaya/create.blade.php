<!DOCTYPE html>
<html lang="en">
<head><title>Tambah Tipe Biaya</title></head>
<body>
    <h1>Tambah Tipe Biaya Baru</h1>

    @if ($errors->any())
        <div style="color: red;">
            <strong>Error!</strong>
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.jenis-biaya.store') }}" method="POST">
        @csrf
        <div>
            <label>Nama Biaya (Cth: Uang Gedung, SPP):</label><br>
            <input type="text" name="nama_biaya" value="{{ old('nama_biaya') }}" style="width: 300px;">
        </div>
        <br>
        <div>
            <label>Keterangan (Opsional):</label><br>
            <textarea name="keterangan" rows="4" style="width: 300px;">{{ old('keterangan') }}</textarea>
        </div>
        <br>
        <button type="submit">Simpan Tipe Biaya</button>
    </form>
    <br>
    <a href="{{ route('admin.jenis-biaya.index') }}">Batal</a>
</body>
</html>