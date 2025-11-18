<!DOCTYPE html>
<html lang="en">
<head><title>Verifikasi Pendaftar</title></head>
<body>
    <h1>Verifikasi Pendaftar</h1>
    <p>Daftar siswa baru yang berstatus "Terdaftar" dan perlu diverifikasi.</p>

    @if (session('success')) <div style="color: green;">{{ session('success') }}</div> @endif
    @if (session('error')) <div style="color: red;">{{ session('error') }}</div> @endif

    <table border="1" style="width: 80%;">
        <thead>
            <tr>
                <th>No. Pendaftaran</th>
                <th>Nama Siswa</th>
                <th>Email Akun</th>
                <th>Pilihan Jurusan</th>
                <th>Tgl Submit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data_siswa as $siswa)
                <tr>
                    <td>{{ $siswa->no_pendaftaran }}</td>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td>{{ $siswa->user->email }}</td> <td>{{ $siswa->jurusan->nama_jurusan }}</td> <td>{{ $siswa->tanggal_submit }}</td>
                    <td>
                        <a href="{{ route('admin.verifikasi.show', $siswa->id) }}">Lihat Detail & Verifikasi</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data siswa baru yang perlu diverifikasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <br>
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
</body>
</html>