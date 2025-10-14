<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kuota_gelombang_jurusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_id')->constrained('gelombang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('jurusan_tipe_kelas_id')->constrained('jurusan_tipe_kelas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('kuota_gelombang')->comment('Kuota untuk kombinasi ini di gelombang tertentu');
            $table->timestamps();

            $table->unique(['gelombang_id', 'jurusan_tipe_kelas_id'], 'uk_gelombang_kuota');
        });
    }

    public function down(): void {
        Schema::dropIfExists('kuota_gelombang_jurusan');
    }
};
