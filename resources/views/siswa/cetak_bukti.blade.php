<!DOCTYPE html>
<html>
<head>
    <title>Bukti Pendaftaran</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid black; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 0; font-size: 9pt; margin-top: 5px; }
        
        .title-box { text-align: center; margin-bottom: 20px; margin-top: 20px; }
        .title-box h3 { text-decoration: underline; margin: 0; font-size: 14pt; }
        .title-box p { margin: 5px 0 0 0; font-size: 10pt; }

        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; text-decoration: underline; font-size: 11pt; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        td { padding: 3px; vertical-align: top; }
        .label-col { width: 160px; }
        .colon-col { width: 15px; text-align: center; }

        .info-box { border: 1px solid #000; padding: 10px; margin-bottom: 15px; }

        /* INFO KEUANGAN TAMBAHAN */
        .financial-box { border: 1px dashed #555; padding: 10px; margin-bottom: 20px; background-color: #f9f9f9; }
        .financial-box .section-title { margin-top: 0; margin-bottom: 10px; font-size: 10pt; }

        .status-box { 
            border: 2px solid #000; 
            padding: 10px; 
            text-align: center; 
            font-weight: bold; 
            font-size: 12pt; 
            margin: 20px auto;
            width: 80%;
        }

        .notes { font-size: 9pt; font-style: italic; border-top: 1px solid #ccc; padding-top: 10px; margin-top: 20px; }

        .footer-sign { margin-top: 40px; width: 100%; }
        .sign-box { float: right; width: 200px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SMK PEJANTAN TANGGUH</h1>
        <p>Jl. Raya Pendidikan No. 123, Sidoarjo, Jawa Timur | Kode Pos: 61234</p>
        <p>Telp: (031) 1234567 | Email: info@smkpejantantangguh.sch.id</p>
    </div>

    <div class="title-box">
        <h3>TANDA BUKTI PENDAFTARAN</h3>
        <p>PPDB TAHUN AJARAN {{ date('Y') }}/{{ date('Y')+1 }}</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td class="label-col">No. Pendaftaran</td>
                <td class="colon-col">:</td>
                <td><strong>{{ $siswa->no_pendaftaran }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">Tanggal Daftar</td>
                <td class="colon-col">:</td>
                <td>{{ $siswa->tanggal_submit->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label-col">Gelombang</td>
                <td class="colon-col">:</td>
                <td>{{ $siswa->gelombang->nama_gelombang }}</td>
            </tr>
            <tr>
                <td class="label-col">Jurusan Pilihan</td>
                <td class="colon-col">:</td>
                <td><strong>{{ $siswa->jurusan->nama_jurusan }}</strong> ({{ $siswa->tipeKelas->nama_tipe_kelas }})</td>
            </tr>
        </table>
    </div>

    {{-- 🔥 RINGKASAN KEUANGAN 🔥 --}}
    @php
        $rencana = $siswa->rencanaPembayaran;
        $total_biaya = $rencana ? $rencana->total_nominal_biaya : 0;
        $sudah_bayar = $rencana ? $rencana->total_sudah_dibayar : 0;
        $sisa_tagihan = $total_biaya - $sudah_bayar;
        $status_lunas = $sisa_tagihan <= 0 ? 'LUNAS' : 'BELUM LUNAS';
    @endphp

    <div class="financial-box">
        <div class="section-title">INFORMASI KEUANGAN</div>
        <table style="margin-bottom: 0;">
            <tr>
                <td class="label-col">Total Biaya</td>
                <td class="colon-col">:</td>
                <td>Rp {{ number_format($total_biaya, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-col">Total Terbayar</td>
                <td class="colon-col">:</td>
                <td>Rp {{ number_format($sudah_bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-col">Sisa Tagihan</td>
                <td class="colon-col">:</td>
                <td>
                    <strong>Rp {{ number_format($sisa_tagihan, 0, ',', '.') }}</strong>
                    <span style="margin-left: 10px; font-size: 8pt; padding: 2px 5px; border: 1px solid #000; border-radius: 3px; font-weight: bold; {{ $status_lunas == 'LUNAS' ? 'background-color: #ddd;' : 'background-color: #fff;' }}">
                        {{ $status_lunas }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
    {{-- 🔥 END BAGIAN BARU 🔥 --}}


    <div class="section-title">A. DATA CALON SISWA</div>
    <table>
        <tr>
            <td class="label-col">Nama Lengkap</td>
            <td class="colon-col">:</td>
            <td>{{ $siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label-col">NISN / NIK</td>
            <td class="colon-col">:</td>
            <td>{{ $siswa->nisn }} / {{ $siswa->nik }}</td>
        </tr>
        <tr>
            <td class="label-col">Tempat, Tgl Lahir</td>
            <td class="colon-col">:</td>
            <td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="label-col">Jenis Kelamin</td>
            <td class="colon-col">:</td>
            <td>{{ $siswa->jenis_kelamin }}</td>
        </tr>
        <tr>
            <td class="label-col">Agama</td>
            <td class="colon-col">:</td>
            <td>{{ $siswa->agama }}</td>
        </tr>
        <tr>
            <td class="label-col">No. HP</td>
            <td class="colon-col">:</td>
            <td>{{ $siswa->no_hp }}</td>
        </tr>
        <tr>
            <td class="label-col">Asal Sekolah</td>
            <td class="colon-col">:</td>
            <td>{{ $siswa->asal_sekolah }} (Lulus: {{ $siswa->tahun_lulus }})</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Lengkap</td>
            <td class="colon-col">:</td>
            <td>
                {{ $siswa->alamat }} <br>
                RT/RW: {{ $siswa->rt_rw }}, Kel. {{ $siswa->desa_kelurahan }} <br>
                Kec. {{ $siswa->kecamatan }}, {{ $siswa->kota_kab }} - {{ $siswa->kode_pos }}
            </td>
        </tr>
        <tr>
            <td class="label-col">Data Fisik</td>
            <td class="colon-col">:</td>
            <td>TB: {{ $siswa->tinggi_badan ?? '-' }} cm, BB: {{ $siswa->berat_badan ?? '-' }} kg</td>
        </tr>
    </table>

    <div class="section-title">B. DATA PENANGGUNG JAWAB</div>
    
    @foreach($siswa->penanggungJawab as $pj)
        <div style="margin-bottom: 8px;">
            <strong>{{ $loop->iteration }}. Data {{ $pj->hubungan }}</strong>
            <table style="margin-top: 0; margin-bottom: 0;">
                <tr>
                    <td class="label-col" style="padding-left: 15px;">Nama Lengkap</td>
                    <td class="colon-col">:</td>
                    <td>{{ $pj->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td class="label-col" style="padding-left: 15px;">Pekerjaan</td>
                    <td class="colon-col">:</td>
                    <td>{{ $pj->pekerjaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col" style="padding-left: 15px;">No. HP</td>
                    <td class="colon-col">:</td>
                    <td>{{ $pj->no_hp ?? '-' }}</td>
                </tr>
            </table>
        </div>
    @endforeach

    <div class="status-box">
        STATUS: {{ strtoupper($siswa->status_pendaftaran) }}
    </div>

    <div class="notes">
        <strong>Catatan Penting:</strong>
        <br>
        1. Bukti ini adalah dokumen sah pendaftaran PPDB SMK Pejantan Tangguh.
        <br>
        2. Harap membawa bukti ini beserta dokumen asli saat melakukan verifikasi fisik atau pengambilan seragam.
        <br>
        3. Segala bentuk pemalsuan data akan mengakibatkan pembatalan penerimaan siswa.
        <br>
        4. Jika status keuangan "BELUM LUNAS", harap selesaikan administrasi sebelum pengambilan seragam.
    </div>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Sidoarjo, {{ date('d F Y') }}</p>
            <br>
            <p style="font-size: 9pt;">(Tanda Tangan & Stempel)</p>
            <br><br><br>
            <p><strong>Panitia PPDB</strong></p>
        </div>
    </div>

</body>
</html>