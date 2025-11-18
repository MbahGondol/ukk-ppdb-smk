<!DOCTYPE html>
<html lang="en">
<head><title>Dashboard Siswa</title></head>
<body>
    <h1>Halo, {{ Auth::user()->name }}!</h1>
    <p>Selamat datang di Portal PPDB SMK.</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
    <hr>

    <h2>Status Pendaftaran Anda</h2>

    @if ($calonSiswa)
        <p>Anda sudah mendaftar pada tanggal: {{ $calonSiswa->tanggal_submit->format('d F Y H:i') }}</p>
        <p>Status Anda saat ini: <strong>{{ $calonSiswa->status_pendaftaran }}</strong></p>
        
        @if ($calonSiswa->status_pendaftaran == 'Melengkapi Berkas' || $calonSiswa->status_pendaftaran == 'Terdaftar' || $calonSiswa->status_pendaftaran == 'Proses Verifikasi')
            
            <p>Langkah selanjutnya adalah melengkapi dokumen dan pembayaran.</p>
            
            <a href="{{ route('siswa.dokumen.index') }}" style="font-size: 1.1em; padding: 8px; background: blue; color: white;">
                Lanjut ke Upload Dokumen
            </a>
            
            <a href="{{ route('siswa.pembayaran.index') }}" style="font-size: 1.1em; padding: 8px; background: green; color: white; margin-left: 10px;">
                Lanjut ke Pembayaran
            </a>
            @endif

    @else
        <p>Anda belum mengisi formulir pendaftaran.</p>
        <p>Silakan klik tombol di bawah ini untuk memulai proses pendaftaran Anda.</p>
        <a href="{{ route('siswa.pendaftaran.create') }}" style="font-size: 1.2em; padding: 10px; background: green; color: white;">
            Mulai Pendaftaran Sekarang
        </a>
    @endif

</body>
</html>