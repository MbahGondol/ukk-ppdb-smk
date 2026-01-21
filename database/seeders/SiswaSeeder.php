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
use Carbon\Carbon;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $tahun = TahunAkademik::where('aktif', true)->first();
        $gelombang = Gelombang::first();
        $pilihan_jurusan = JurusanTipeKelas::all();

        if (!$tahun || !$gelombang || $pilihan_jurusan->isEmpty()) {
            $this->command->error('Data Master kosong. Seeder berhenti.');
            return;
        }

        for ($i = 1; $i <= 15; $i++) {
            $email = "siswa{$i}@gmail.com";
            if (User::where('email', $email)->exists()) continue;

            $jurusan_terpilih = $pilihan_jurusan->random();
            $statuses = ['Melengkapi Berkas', 'Terdaftar', 'Ditolak', 'Resmi Diterima'];
            $status = $statuses[array_rand($statuses)];

            $user = User::create([
                'name' => $faker->name,
                'email' => $email,
                'password' => Hash::make('#Password123'),
                'email_verified_at' => Carbon::now(),
            ]);

            $user->assignRole('siswa');

            $calonSiswa = CalonSiswa::create([
                'user_id' => $user->id,
                'no_pendaftaran' => date('Y') . sprintf('%02d', $gelombang->id) . sprintf('%02d', $jurusan_terpilih->jurusan_id) . sprintf('%04d', $i),
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
                
                // BARIS INI SUDAH DIHAPUS (Progres Pendaftaran)
                
                'status_pendaftaran' => $status,
                'catatan_admin' => ($status == 'Ditolak') ? 'Data tidak valid.' : null,
                'tanggal_submit' => now()->subDays(rand(1, 10)),
            ]);

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
                'penghasilan_bulanan' => 0
            ]);

            if ($status != 'Melengkapi Berkas') {
                $dokumens = ['Akte Kelahiran', 'Kartu Keluarga', 'Ijazah SMP', 'Foto Formal'];
                foreach ($dokumens as $tipe) {
                    DokumenSiswa::create([
                        'calon_siswa_id' => $calonSiswa->id,
                        'tipe_dokumen' => $tipe,
                        'file_path' => 'dummy/path/file.jpg',
                        'nama_asli_file' => 'scan_' . strtolower(str_replace(' ', '_', $tipe)) . '.jpg',
                        'status_verifikasi' => ($status == 'Resmi Diterima') ? 'Valid' : 'Pending',
                    ]);
                }

                $biaya = BiayaPerJurusanTipeKelas::where('jurusan_tipe_kelas_id', $jurusan_terpilih->id)->sum('nominal');
                if ($biaya == 0) $biaya = 5000000;
                $total_sudah_dibayar = ($status == 'Resmi Diterima') ? $biaya : 1000000;
                $status_tagihan = ($status == 'Resmi Diterima') ? 'Lunas' : 'Belum Lunas';

                $rencana = RencanaPembayaran::create([
                    'calon_siswa_id' => $calonSiswa->id,
                    'total_nominal_biaya' => $biaya,
                    'total_sudah_dibayar' => $total_sudah_dibayar,
                    'status' => $status_tagihan,
                ]);

                $pembayaran = PembayaranSiswa::create([
                    'rencana_pembayaran_id' => $rencana->id,
                    'jumlah' => $total_sudah_dibayar,
                    'tanggal_pembayaran' => now(),
                    'metode' => 'Transfer Bank',
                    'status' => ($status == 'Resmi Diterima') ? 'Verified' : 'Pending',
                ]);

                BuktiPembayaran::create([
                    'pembayaran_id' => $pembayaran->id,
                    'file_path' => 'dummy/path/bukti_transfer.jpg'
                ]);
            }
        }
    }
}