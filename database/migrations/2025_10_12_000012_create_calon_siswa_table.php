<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('calon_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade')->onUpdate('cascade'); // 1 akun = 1 calon siswa
            $table->string('no_pendaftaran', 20)->unique();
            $table->string('nisn', 15)->unique()->index();
            $table->string('nama_lengkap', 150);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('agama', 50);
            $table->string('nik', 20)->unique()->index();
            $table->unsignedSmallInteger('tinggi_badan')->nullable();
            $table->unsignedSmallInteger('berat_badan')->nullable();
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();
            $table->string('asal_sekolah', 150)->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt_rw', 20)->nullable();
            $table->string('desa_kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kota_kab', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('no_hp', 20)->nullable();
            
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('tipe_kelas_id')->nullable()->constrained('tipe_kelas')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('gelombang_id')->constrained('gelombang')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('promo_id')->nullable()->constrained('promo')->nullOnDelete()->cascadeOnUpdate();

            $table->enum('status_pendaftaran', [
                'Draft',
                'Melengkapi Berkas',
                'Terdaftar',
                'Proses Verifikasi',
                'Ditolak',
                'Lunas',
                'Resmi Diterima'
            ])->default('Draft')->index();

            $table->text('catatan_admin')->nullable()->comment('Catatan dari admin, misal alasan penolakan');

            $table->dateTime('tanggal_submit')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('calon_siswa');
    }
};
