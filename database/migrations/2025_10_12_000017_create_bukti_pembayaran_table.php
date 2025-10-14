<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bukti_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->unique()->constrained('pembayaran')->onDelete('cascade')->onUpdate('cascade');
            $table->string('file_path', 255)->comment('Path file bukti transfer');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bukti_pembayaran');
    }
};
