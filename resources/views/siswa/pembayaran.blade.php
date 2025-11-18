<!DOCTYPE html>
<html lang="en">
<head><title>Konfirmasi Pembayaran</title></head>
<body>
    <a href="{{ route('siswa.dashboard') }}">&laquo; Kembali ke Dashboard</a>
    <h1>Pembayaran & Konfirmasi</h1>
    <p>Ini adalah langkah terakhir pendaftaran Anda.</p>

    @if (session('success')) <div style="color: green;">{{ session('success') }}</div> @endif
    @if (session('error')) <div style="color: red;">{{ session('error') }}</div> @endif
    @if ($errors->any())
        <div style="color: red;">
            <strong>Error!</strong>
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <hr>
    <h3>Informasi Tagihan Anda</h3>
    <table border="1" style="width: 50%;">
        <tr>
            <th>Total Tagihan</th>
            <td>Rp {{ number_format($tagihan->total_nominal_biaya, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Sudah Dibayar (Terverifikasi)</th>
            <td>Rp {{ number_format($tagihan->total_sudah_dibayar, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight: bold;">
            <th>Sisa Tagihan</th>
            <td>Rp {{ number_format($tagihan->total_nominal_biaya - $tagihan->total_sudah_dibayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Status Tagihan</th>
            <td>{{ $tagihan->status }}</td>
        </tr>
    </table>

    <hr>
    <h3>Informasi Rekening Sekolah</h3>
    <p>Silakan lakukan transfer ke salah satu rekening di bawah ini:</p>
    <ul>
        <li><strong>Bank BCA:</strong> 123456789 (a/n SMK Antartika 1 Sidoarjo)</li>
        <li><strong>Bank BNI:</strong> 987654321 (a/n Yayasan SMK Antartika)</li>
    </ul>

    <hr>
    <h3>Form Konfirmasi Pembayaran</h3>
    <p>Jika sudah transfer, silakan isi form di bawah ini.</p>
    <form action="{{ route('siswa.pembayaran.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="jumlah">Jumlah yang Ditransfer (Rp):</label><br>
            <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" required>
        </div>
        <br>
        <div>
            <label for="tanggal_pembayaran">Tanggal Transfer:</label><br>
            <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ old('tanggal_pembayaran') }}" required>
        </div>
        <br>
        <div>
            <label for="file_bukti">Upload Bukti Transfer (JPG, PNG, PDF. Maks: 2MB):</label><br>
            <input type="file" name="file_bukti" id="file_bukti" required>
        </div>
        <br>
        <button type="submit">Konfirmasi Pembayaran</button>
    </form>

    <hr>

    <h3>Riwayat Pembayaran Anda</h3>
    <table border="1" style="width: 80%;">
        <thead>
            <tr>
                <th>Tgl Bayar</th>
                <th>Jumlah (Rp)</th>
                <th>Bukti</th>
                <th>Status Verifikasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                use Carbon\Carbon;
                use Illuminate\Support\Facades\Storage;
            @endphp
            @forelse ($riwayat_pembayaran as $pembayaran)
                <tr>
                    <td>{{ Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}</td>
                    <td>{{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                    <td>
                        @if ($pembayaran->buktiPembayaran)
                            <a href="{{ Storage::disk('public')->url($pembayaran->buktiPembayaran->file_path) }}" target="_blank">
                                Lihat Bukti
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $pembayaran->status }}</td>
                    <td>
                        @if ($pembayaran->status == 'Pending' || $pembayaran->status == 'Failed')
                            <form action="{{ route('siswa.pembayaran.destroy', $pembayaran->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Anda yakin?')">Hapus</button>
                            </form>
                        @else
                            (Terkunci)
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Anda belum melakukan pembayaran.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>