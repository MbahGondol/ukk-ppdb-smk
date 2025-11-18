<!DOCTYPE html>
<html lang="en">
<head><title>Manajemen Kuota</title></head>
<body>
    <h1>Manajemen Kuota Jurusan</h1>
    <p>Di sini Anda bisa mengubah kuota untuk setiap kombinasi Jurusan dan Tipe Kelas.</p>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <table border="1">
        <thead>
            <tr>
                <th>Jurusan</th>
                <th>Tipe Kelas</th>
                <th>Kuota Saat Ini</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_kuota as $item)
                <tr>
                    <td>{{ $item->jurusan->nama_jurusan }}</td>
                    
                    <td>{{ $item->tipeKelas->nama_tipe_kelas }}</td>

                    <td>{{ $item->kuota_kelas }}</td>
                    <td>
                        <a href="{{ route('admin.kuota.edit', $item->id) }}">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
</body>
</html>