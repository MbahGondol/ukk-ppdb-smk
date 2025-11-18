<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('penanggung_jawab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->onDelete('cascade');
            $table->enum('hubungan', ['Ayah', 'Ibu', 'Wali']);
            $table->string('nama_lengkap', 100);
            $table->integer('tahun_lahir')->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->char('nik', 16)->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable();
            $table->decimal('penghasilan_bulanan', 15, 2)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->timestamps();

            // Index dan unique constraint
            $table->unique(['calon_siswa_id', 'hubungan'], 'uk_siswa_hubungan');
            $table->index('calon_siswa_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('penanggung_jawab');
    }
};
