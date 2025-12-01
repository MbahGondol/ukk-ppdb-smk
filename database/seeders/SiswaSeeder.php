<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CalonSiswa;
use App\Models\PenanggungJawab;
use App\Models\JurusanTipeKelas;
use App\Models\TahunAkademik;
use App\Models\Gelombang;
use App\Models\DokumenSiswa;
use App\Models\RencanaPembayaran;
use App\Models\PembayaranSiswa;
use App\Models\BuktiPembayaran;
use App\Models\BiayaPerJurusanTipeKelas;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Pakai Faker Indonesia
        
        // Ambil Data Master
        $tahun = TahunAkademik::where('aktif', true)->first();
        $gelombang = Gelombang::first();
        
        // Ambil semua opsi jurusan untuk dipilih acak
        $pilihan_jurusan = JurusanTipeKelas::all();

        // Loop membuat 15 Siswa
        for ($i = 1; $i <= 15; $i++) {
            
            // 1. Pilih Jurusan Acak
            $jurusan_terpilih = $pilihan_jurusan->random();

            // 2. Tentukan Status secara Acak
            // Kita buat probabilitas agar datanya variatif
            $statuses = ['Melengkapi Berkas', 'Terdaftar', 'Ditolak', 'Resmi Diterima'];
            $status = $statuses[array_rand($statuses)];

            // 3. Buat User
            $user = User::create([
                'name' => $faker->name,
                'email' => "siswa{$i}@gmail.com", // siswa1@gmail.com, dst
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);

            // 4. Buat Calon Siswa
            $calonSiswa = CalonSiswa::create([
                'user_id' => $user->id,
                'no_pendaftaran' => date('Y') . $gelombang->id . $jurusan_terpilih->jurusan_id . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nisn' => $faker->unique()->numerify('##########'),
                'nik' => $faker->unique()->numerify('################'),
                'nama_lengkap' => $user->name,
                'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '2008-12-31'),
                'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                'no_hp' => $faker->phoneNumber,
                'asal_sekolah' => 'SMP ' . $faker->city,
                'alamat' => $faker->address,
                'rt_rw' => $faker->numerify('00#/00#'),
                'desa_kelurahan' => $faker->streetName,
                'kecamatan' => $faker->citySuffix,
                'kota_kab' => $faker->city,
                'kode_pos' => $faker->postcode,
                'tahun_lulus' => 2024,
                'anak_ke' => $faker->numberBetween(1, 4),
                'jumlah_saudara' => $faker->numberBetween(1, 5),
                'tinggi_badan' => $faker->numberBetween(150, 180),
                'berat_badan' => $faker->numberBetween(45, 80),
                
                'jurusan_id' => $jurusan_terpilih->jurusan_id,
                'tipe_kelas_id' => $jurusan_terpilih->tipe_kelas_id,
                'tahun_akademik_id' => $tahun->id,
                'gelombang_id' => $gelombang->id,
                
                'status_pendaftaran' => $status,
                'catatan_admin' => ($status == 'Ditolak') ? 'Data tidak valid, mohon perbaiki foto ijazah.' : null,
                'tanggal_submit' => now()->subDays(rand(1, 10)),
            ]);

            // 5. Buat Orang Tua (Wajib ada untuk semua)
            PenanggungJawab::create([
                'calon_siswa_id' => $calonSiswa->id,
                'hubungan' => 'Ayah',
                'nama_lengkap' => $faker->name('male'),
                'nik' => $faker->numerify('################'),
                'pekerjaan' => $faker->jobTitle,
                'no_hp' => $faker->phoneNumber,
                'penghasilan_bulanan' => $faker->numberBetween(1000000, 10000000)
            ]);
            PenanggungJawab::create([
                'calon_siswa_id' => $calonSiswa->id,
                'hubungan' => 'Ibu',
                'nama_lengkap' => $faker->name('female'),
                'nik' => $faker->numerify('################'),
                'pekerjaan' => 'Ibu Rumah Tangga',
                'no_hp' => $faker->phoneNumber,
            ]);

            // ============================================================
            // LOGIKA TAMBAHAN: JIKA STATUS BUKAN "Melengkapi Berkas"
            // Maka siswa ini dianggap SUDAH Upload Dokumen & Bayar
            // ============================================================
            if ($status != 'Melengkapi Berkas') {
                
                // A. Buat Dummy Dokumen
                $dokumens = ['Akte Kelahiran', 'Kartu Keluarga', 'Ijazah SMP', 'Foto Formal'];
                foreach ($dokumens as $tipe) {
                    DokumenSiswa::create([
                        'calon_siswa_id' => $calonSiswa->id,
                        'tipe_dokumen' => $tipe,
                        'file_path' => 'dummy/path/file.jpg', // File palsu, hanya agar tidak error di view
                        'nama_asli_file' => 'scan_' . strtolower(str_replace(' ', '_', $tipe)) . '.jpg',
                        'status_verifikasi' => ($status == 'Resmi Diterima') ? 'Valid' : 'Pending',
                    ]);
                }

                // B. Buat Tagihan & Pembayaran
                // Hitung total tagihan dulu
                $biaya = BiayaPerJurusanTipeKelas::where('jurusan_tipe_kelas_id', $jurusan_terpilih->id)->sum('nominal');
                
                $rencana = RencanaPembayaran::create([
                    'calon_siswa_id' => $calonSiswa->id,
                    'total_nominal_biaya' => $biaya,
                    'total_sudah_dibayar' => ($status == 'Resmi Diterima') ? $biaya : 1000000, // Lunas jika diterima, DP jika belum
                    'status' => ($status == 'Resmi Diterima') ? 'Lunas' : 'Belum Lunas',
                ]);

                // Buat Riwayat Transaksi
                $pembayaran = PembayaranSiswa::create([
                    'rencana_pembayaran_id' => $rencana->id,
                    'jumlah' => ($status == 'Resmi Diterima') ? $biaya : 1000000,
                    'tanggal_pembayaran' => now(),
                    'metode' => 'Transfer Bank',
                    'status' => ($status == 'Resmi Diterima') ? 'Verified' : 'Pending',
                ]);

                // Buat Bukti Bayar
                BuktiPembayaran::create([
                    'pembayaran_id' => $pembayaran->id,
                    'file_path' => 'dummy/path/bukti_transfer.jpg'
                ]);
            }
        }
    }
}