<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kuota_jurusan_tipe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('tipe_kelas_id')->constrained('tipe_kelas')->onDelete('restrict')->onUpdate('cascade');
            $table->integer('kuota_total')->comment('Kuota total untuk kombinasi jurusan & tipe');
            $table->timestamps();

            $table->unique(['jurusan_id', 'tipe_kelas_id'], 'uk_jurusan_tipe');
        });
    }

    public function down(): void {
        Schema::dropIfExists('kuota_jurusan_tipe');
    }
};
