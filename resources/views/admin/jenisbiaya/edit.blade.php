<!DOCTYPE html>
<html lang="en">
<head><title>Edit Tipe Biaya</title></head>
<body>
    <h1>Edit Tipe Biaya: {{ $jenis_biaya->nama_biaya }}</h1>

    @if ($errors->any())
        <div style="color: red;">
            <strong>Error!</strong>
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.jenis-biaya.update', $jenis_biaya->id) }}" method="POST">
        @csrf
        @method('PUT') <div>
            <label>Nama Biaya:</label><br>
            <input type="text" name="nama_biaya" value="{{ old('nama_biaya', $jenis_biaya->nama_biaya) }}" style="width: 300px;">
        </div>
        <br>
        <div>
            <label>Keterangan (Opsional):</label><br>
            <textarea name="keterangan" rows="4" style="width: 300px;">{{ old('keterangan', $jenis_biaya->keterangan) }}</textarea>
        </div>
        <br>
        <button type="submit">Simpan Perubahan</button>
    </form>
    <br>
    <a href="{{ route('admin.jenis-biaya.index') }}">Batal</a>
</body>
</html>