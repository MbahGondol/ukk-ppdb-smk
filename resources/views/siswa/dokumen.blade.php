<!DOCTYPE html>
<html lang="en">
<head><title>Upload Dokumen</title></head>
<body>
    <a href="{{ route('siswa.dashboard') }}">&laquo; Kembali ke Dashboard</a>
    <h1>Upload Dokumen Persyaratan</h1>
    <p>Silakan unggah semua dokumen yang diperlukan. (Format: JPG, PNG, PDF. Maks: 2MB)</p>

    @if (session('success')) <div style="color: green;">{{ session('success') }}</div> @endif
    @if (session('error')) <div style="color: red;">{{ session('error') }}</div> @endif
    @if ($errors->any())
        <div style="color: red;">
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <hr>
    <h3>Form Upload</h3>
    <form action="{{ route('siswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="tipe_dokumen">Pilih Jenis Dokumen:</label><br>
            <select name="tipe_dokumen" id="tipe_dokumen" required>
                <option value="">-- Pilih Jenis --</option>
                @foreach ($dokumen_wajib as $tipe)
                    <option value="{{ $tipe }}">{{ $tipe }}</option>
                @endforeach
            </select>
        </div>
        <br>
        <div>
            <label for="file_dokumen">Pilih File:</label><br>
            <input type="file" name="file_dokumen" id="file_dokumen" required>
        </div>
        <br>
        <button type="submit">Upload Dokumen</button>
    </form>
    
    <hr>

    <h3>Dokumen Terupload</h3>
    <table border="1" style="width: 80%;">
        <thead>
            <tr>
                <th>Jenis Dokumen</th>
                <th>Nama File</th>
                <th>Status Verifikasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dokumen_terupload as $dokumen)
                <tr>
                    <td>{{ $dokumen->tipe_dokumen }}</td>
                    <td>
                        <a href="{{ Storage::disk('public')->url($dokumen->file_path) }}" target="_blank">{{ $dokumen->nama_asli_file }} (Lihat)</a>
                    </td>
                    <td>{{ $dokumen->status_verifikasi }}</td>
                    <td>
                        <form action="{{ route('siswa.dokumen.destroy', $dokumen->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Anda yakin ingin menghapus file ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Anda belum mengunggah dokumen apapun.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>