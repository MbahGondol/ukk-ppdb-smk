<!DOCTYPE html>
<html lang="en">
<head><title>Manajemen Jurusan</title></head>
<body>
    <h1>Manajemen Jurusan</h1>
    <a href="{{ route('admin.jurusan.create') }}">Tambah Jurusan Baru</a>
    <br><br>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode</th>
                <th>Nama Jurusan</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($semua_jurusan as $jurusan)
                <tr>
                    <td>{{ $jurusan->id }}</td>
                    <td>{{ $jurusan->kode_jurusan }}</td>
                    <td>{{ $jurusan->nama_jurusan }}</td>
                    <td>{{ $jurusan->deskripsi }}</td>
                    <td>
                        <a href="#">Edit</a>
                        <a href="#">Hapus</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
</body>
</html>