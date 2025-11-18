<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jurusan_tipe_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tipe_kelas_id')->constrained('tipe_kelas')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('kuota_kelas');
            
            $table->unique(['jurusan_id', 'tipe_kelas_id'], 'uk_jurusan_tipe_kelas');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('jurusan_tipe_kelas');
    }
};
