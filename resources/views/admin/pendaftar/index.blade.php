<!DOCTYPE html>
<html lang="en">
<head><title>Manajemen Pendaftar</title></head>
<body>
    <h1>Manajemen Pendaftar (Semua Arsip)</h1>
    <p>Lihat semua pendaftar dan filter berdasarkan status.</p>

    <div style="margin-bottom: 20px;">
        <strong>Filter Status:</strong>
        <a href="{{ route('admin.pendaftar.index') }}" 
           style="padding: 5px; {{ !$status_sekarang ? 'font-weight: bold; background: #ddd;' : '' }}">
           Tampilkan Semua
        </a>
        
        <a href="{{ route('admin.pendaftar.index', ['status' => 'Terdaftar']) }}" 
           style="padding: 5px; {{ $status_sekarang == 'Terdaftar' ? 'font-weight: bold; background: #ddd;' : '' }}">
           Terdaftar (Inbox)
        </a>
        
        <a href="{{ route('admin.pendaftar.index', ['status' => 'Proses Verifikasi']) }}" 
           style="padding: 5px; {{ $status_sekarang == 'Proses Verifikasi' ? 'font-weight: bold; background: #ddd;' : '' }}">
           Proses Verifikasi
        </a>

        <a href="{{ route('admin.pendaftar.index', ['status' => 'Ditolak']) }}" 
           style="padding: 5px; {{ $status_sekarang == 'Ditolak' ? 'font-weight: bold; background: #ddd;' : '' }}">
           Ditolak
        </a>
        
        </div>
    <table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="5">
        <thead>
            <tr>
                <th>No. Daftar</th>
                <th>Nama Siswa</th>
                <th>Email</th>
                <th>Pilihan Jurusan</th>
                <th>Tgl Submit</th>
                <th style="background: #eee;">Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data_siswa as $siswa)
                <tr>
                    <td>{{ $siswa->no_pendaftaran }}</td>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td>{{ $siswa->user->email }}</td>
                    <td>{{ $siswa->jurusan->nama_jurusan }} ({{ $siswa->tipeKelas->nama_tipe_kelas }})</td>
                    <td>{{ $siswa->tanggal_submit->format('d M Y H:i') }}</td>
                    <td style="background: #eee;">
                        <strong>{{ $siswa->status_pendaftaran }}</strong>
                    </td>
                    <td>
                        <a href="{{ route('admin.verifikasi.show', $siswa->id) }}">Lihat Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data siswa yang cocok dengan filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <br>
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
</body>
</html>