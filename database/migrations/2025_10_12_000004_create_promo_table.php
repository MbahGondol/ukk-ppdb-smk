<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('promo', function (Blueprint $table) {
            $table->id();
            $table->string('nama_promo', 150);
            $table->decimal('potongan', 15, 2)->default(0.00);
            $table->text('deskripsi')->nullable();
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('promo');
    }
};
