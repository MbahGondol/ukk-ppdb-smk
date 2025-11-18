<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rencana_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('biaya_per_jurusan_tipe_kelas_id')->nullable()->constrained('biaya_per_jurusan_tipe_kelas')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('jenis_pembayaran', ['Full', 'Cicilan']);
            $table->decimal('total_nominal_biaya', 15, 2);
            $table->decimal('total_sudah_dibayar', 15, 2)->default(0.00);
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rencana_pembayaran');
    }
};