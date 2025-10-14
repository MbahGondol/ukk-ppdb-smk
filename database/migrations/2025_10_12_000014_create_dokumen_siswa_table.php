<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dokumen_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('tipe_dokumen', ['Akte Kelahiran', 'Ijazah SMP', 'KTP Ayah', 'KTP Ibu', 'KTP Wali', 'Kartu Keluarga', 'Foto Formal']);
            $table->string('file_path', 255)->comment('Path file di storage');
            $table->string('nama_asli_file', 255);
            $table->enum('status_verifikasi', ['Pending', 'Valid', 'Invalid'])->default('Pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('dokumen_siswa');
    }
};
