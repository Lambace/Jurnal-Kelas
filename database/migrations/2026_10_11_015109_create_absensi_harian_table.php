<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')->constrained('jurnal_harian')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->date('tanggal');
            
            // Langsung masukkan ke-6 status sekaligus
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha', 'terlambat', 'bolos'])->default('hadir');
            
            $table->string('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk performa query yang lebih cepat
            $table->index(['jurnal_id', 'user_id']);
            $table->index(['tanggal', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_harian');
    }
};