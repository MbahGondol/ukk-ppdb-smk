<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pembayaran_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_pembayaran_id')->constrained('rencana_pembayaran')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->dateTime('tanggal_pembayaran');
            $table->string('metode', 50)->nullable();
            $table->string('nomor_transaksi', 50)->nullable()->unique();
            $table->enum('status', ['Pending', 'Verified', 'Failed'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pembayaran_siswa');
    }
};
