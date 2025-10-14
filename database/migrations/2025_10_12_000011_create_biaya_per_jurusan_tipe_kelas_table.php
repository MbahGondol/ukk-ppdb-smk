<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('biaya_per_jurusan_tipe_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_biaya_id')->constrained('jenis_biaya')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('jurusan_tipe_kelas_id')->constrained('jurusan_tipe_kelas')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->text('catatan')->nullable();
            $table->timestamps(); 
        });
    }

    public function down(): void {
        Schema::dropIfExists('biaya_per_jurusan_tipe_kelas');
    }
};
