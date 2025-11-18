<!DOCTYPE html>
<html lang="en">
<head><title>Edit Kuota</title></head>
<body>
    <h1>Edit Kuota: {{ $kuota->jurusan->nama_jurusan }} ({{ $kuota->tipeKelas->nama_tipe_kelas }})</h1>

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

    <form action="{{ route('admin.kuota.update', $kuota->id) }}" method="POST">
        @csrf @method('PUT') <div>
            <label>Jurusan:</label><br>
            <input type="text" value="{{ $kuota->jurusan->nama_jurusan }}" disabled>
        </div>
        <br>
        <div>
            <label>Tipe Kelas:</label><br>
            <input type="text" value="{{ $kuota->tipeKelas->nama_tipe_kelas }}" disabled>
        </div>
        <br>
        <div>
            <label>Masukkan Kuota Baru:</label><br>
            <input type="number" name="kuota_kelas" value="{{ old('kuota_kelas', $kuota->kuota_kelas) }}">
        </div>
        <br>
        <button type="submit">Simpan Perubahan</button>
    </form>
    <br>
    <a href="{{ route('admin.kuota.index') }}">Batal</a>
</body>
</html>