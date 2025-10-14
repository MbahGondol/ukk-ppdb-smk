<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jurusan', 10)->unique();
            $table->string('nama_jurusan', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('jurusan');
    }
};
