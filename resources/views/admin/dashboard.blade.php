<!DOCTYPE html>
<html lang="en">
<head><title>Dashboard Admin</title></head>
<body>
    <h1>Halo, {{ Auth::user()->name }}!</h1>
    <p>Anda login sebagai Admin.</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <hr>

    <h2>Menu Navigasi</h2>
    <ul>
        <li>
            <a href="{{ route('admin.verifikasi.index') }}">
                Verifikasi Siswa (Inbox)
            </a>
        </li>
        
        <li>
            <a href="{{ route('admin.pendaftar.index') }}">
                Laporan / Semua Pendaftar
            </a>
        </li>
        <hr style="margin: 10px 0;">
        
        <li>
            <a href="{{ route('admin.jurusan.index') }}">Manajemen Jurusan</a>
        </li>
        <li>
            <a href="{{ route('admin.kuota.index') }}">Manajemen Kuota</a>
        </li>
        <li>
            <a href="{{ route('admin.jenis-biaya.index') }}">Manajemen Tipe Biaya</a>
        </li>
        <li>
            <a href="{{ route('admin.biaya.index') }}">Manajemen Harga</a>
        </li>
    </ul>
</body>
</html>