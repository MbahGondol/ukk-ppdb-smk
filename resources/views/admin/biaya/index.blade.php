<!DOCTYPE html>
<html lang="en">
<head><title>Manajemen Harga</title></head>
<body>
    <h1>Manajemen Daftar Harga</h1>
    <a href="{{ route('admin.biaya.create') }}">Tambah Harga Baru</a>
    <br><br>

    @if (session('success')) <div style="color: green;">{{ session('success') }}</div> @endif
    @if (session('error')) <div style="color: red;">{{ session('error') }}</div> @endif

    <table border="1" style="width: 80%;">
        <thead>
            <tr>
                <th>Tipe Biaya</th>
                <th>Jurusan</th>
                <th>Tipe Kelas</th>
                <th>Nominal (Rp)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_harga as $harga)
                <tr>
                    <td>{{ $harga->jenisBiaya->nama_biaya }}</td>
                    <td>{{ $harga->jurusanTipeKelas->jurusan->nama_jurusan }}</td>
                    <td>{{ $harga->jurusanTipeKelas->tipeKelas->nama_tipe_kelas }}</td>
                    <td>{{ number_format($harga->nominal, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('admin.biaya.edit', $harga->id) }}">Edit</a>
                        <form action="{{ route('admin.biaya.destroy', $harga->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Anda yakin?')">Hapus</button>
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