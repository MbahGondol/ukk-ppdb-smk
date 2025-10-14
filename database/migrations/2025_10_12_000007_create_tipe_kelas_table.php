<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tipe_kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tipe_kelas', 50)->unique(); // Reguler, Unggulan, dll.
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tipe_kelas');
    }
};
