<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gelombang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gelombang', 50)->unique();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->foreignId('promo_id')->nullable()->constrained('promo')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('gelombang');
    }
};
