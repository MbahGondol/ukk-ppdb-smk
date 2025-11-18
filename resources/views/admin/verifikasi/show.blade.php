<!DOCTYPE html>
<html lang="en">
<head><title>Detail Siswa: {{ $siswa->nama_lengkap }}</title></head>
<body style="font-family: sans-serif;">
    @php
        use Illuminate\Support\Facades\Storage;
        use Carbon\Carbon;
    @endphp

    <a href="{{ url()->previous() }}">&laquo; Kembali ke Halaman Sebelumnya</a>
    <hr>
    
    <h1>Detail Pendaftar: {{ $siswa->nama_lengkap }}</h1>
    <p>No. Pendaftaran: <strong>{{ $siswa->no_pendaftaran }}</strong> | Status: <strong>{{ $siswa->status_pendaftaran }}</strong></p>
    
    @if ($siswa->status_pendaftaran == 'Ditolak' && $siswa->catatan_admin)
        <div style="background: #fff0f0; border: 1px solid red; padding: 10px; margin-bottom: 20px;">
            <strong>Alasan Penolakan:</strong><br>
            {{ $siswa->catatan_admin }}
        </div>
    @endif

    @if ($siswa->status_pendaftaran == 'Terdaftar')
        <div style="background: #eee; padding: 15px; margin-bottom: 20px;">
            <h3>Aksi Verifikasi</h3>
            <p>Setelah memeriksa semua data di bawah, setujui atau tolak pendaftaran ini.</p>
            
            <form action="{{ route('admin.verifikasi.updateStatus', $siswa->id) }}" method="POST" style="display: inline-block; vertical-align: top;">
                @csrf
                <input type="hidden" name="aksi" value="terima">
                <button type="submit" style="background: green; color: white; padding: 10px;">
                    ✔ Setujui (Lolos Verifikasi Awal)
                </button>
            </form>
            
            <form action="{{ route('admin.verifikasi.updateStatus', $siswa->id) }}" method="POST" style="display: inline-block; vertical-align: top; margin-left: 10px;">
                @csrf
                <input type="hidden" name="aksi" value="tolak">
                
                <div>
                    <label for="catatan_admin">Alasan Penolakan (Wajib diisi jika menolak):</label><br>
                    <textarea name="catatan_admin" id="catatan_admin" rows="3" style="width: 250px;"></textarea>
                </div>
                
                <button type="submit" style="background: red; color: white; padding: 10px;" onclick="return confirm('Anda yakin ingin MENOLAK siswa ini?')">
                    X Tolak Pendaftaran
                </button>
            </form>
        </div>
    @endif
    <h3>Data Akun</h3>
    <table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="5">
        <tr> <td style="width: 20%;">Email Akun</td> <td>{{ $siswa->user->email }}</td> </tr>
        <tr> <td>Nama Akun</td> <td>{{ $siswa->user->name }}</td> </tr>
    </table>

    <h3>Data Pribadi Siswa</h3>
    <table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="5">
        <tr> <td style="width: 20%;">NISN</td> <td>{{ $siswa->nisn }}</td> </tr>
        <tr> <td>NIK</td> <td>{{ $siswa->nik }}</td> </tr>
        <tr> <td>Jenis Kelamin</td> <td>{{ $siswa->jenis_kelamin }}</td> </tr>
        <tr> <td>Tempat Lahir</td> <td>{{ $siswa->tempat_lahir }}</td> </tr>
        <tr> <td>Tanggal Lahir</td> <td>{{ $siswa->tanggal_lahir->format('d F Y') }}</td> </tr>
        <tr> <td>Agama</td> <td>{{ $siswa->agama }}</td> </tr>
        <tr> <td>Asal Sekolah</td> <td>{{ $siswa->asal_sekolah }}</td> </tr>
        <tr> <td>No. HP</td> <td>{{ $siswa->no_hp }}</td> </tr>
    </table>

    <h3>Pilihan Pendaftaran</h3>
    <table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="5">
        <tr> <td style="width: 20%;">Pilihan Jurusan</td> <td>{{ $siswa->jurusan->nama_jurusan }}</td> </tr>
        <tr> <td>Tipe Kelas</td> <td>{{ $siswa->tipeKelas->nama_tipe_kelas }}</td> </tr>
    </table>

    <h3>Data Penanggung Jawab (Orang Tua / Wali)</h3>
    <table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="5">
        <thead>
            <tr style="background: #f9f9f9;">
                <th style="width: 20%;">Hubungan</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($siswa->penanggungJawab as $pj)
                <tr>
                    <td>{{ $pj->hubungan }}</td>
                    <td>{{ $pj->nama_lengkap }}</td>
                    <td>{{ $pj->nik ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Data Dokumen Terupload</h3>
    <table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="5">
        <thead>
            <tr style="background: #f9f9f9;">
                <th>Jenis Dokumen</th>
                <th>File</th>
                <th>Status</th>
                <th>Aksi Admin</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswa->dokumen as $dokumen)
                <tr>
                    <td>{{ $dokumen->tipe_dokumen }}</td>
                    <td>
                        <a href="{{ Storage::disk('public')->url($dokumen->file_path) }}" target="_blank">
                            Lihat File ({{ $dokumen->nama_asli_file }})
                        </a>
                    </td>
                    <td>{{ $dokumen->status_verifikasi }}</td>
                    <td>
                        (Tombol Verifikasi/Tolak Dokumen belum dibuat)
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Siswa belum mengunggah dokumen apapun.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Data Riwayat Pembayaran</h3>
    @if ($siswa->rencanaPembayaran)
        <table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="5">
            <thead>
                <tr style="background: #f9f9f9;">
                    <th>Tgl Bayar</th>
                    <th>Jumlah (Rp)</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Aksi Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswa->rencanaPembayaran->pembayaran as $pembayaran)
                    <tr>
                        <td>{{ $pembayaran->tanggal_pembayaran->format('d F Y H:i') }}</td>
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
                            (Tombol Verifikasi/Tolak Pembayaran belum dibuat)
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Siswa belum mengunggah bukti pembayaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <p>Siswa belum sampai pada tahap pembayaran.</p>
    @endif
</body>
</html>