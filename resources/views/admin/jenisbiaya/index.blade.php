<!DOCTYPE html>
<html lang="en">
<head><title>Manajemen Tipe Biaya</title></head>
<body>
    <h1>Manajemen Tipe Biaya</h1>
    <a href="{{ route('admin.jenis-biaya.create') }}">Tambah Tipe Biaya Baru</a>
    <br><br>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Biaya</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_biaya as $biaya)
                <tr>
                    <td>{{ $biaya->id }}</td>
                    <td>{{ $biaya->nama_biaya }}</td>
                    <td>{{ $biaya->keterangan }}</td>
                    <td>
                        <a href="{{ route('admin.jenis-biaya.edit', $biaya->id) }}">Edit</a>
                        
                        <form action="{{ route('admin.jenis-biaya.destroy', $biaya->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Anda yakin ingin menghapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
</body>
</html>