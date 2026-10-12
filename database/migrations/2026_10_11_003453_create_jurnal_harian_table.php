<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('id_kelas')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('id_pengisi')->constrained('users')->onDelete('cascade');
            $table->string('peran_pengisi')->default('siswa');
            
            // Data kehadiran (disimpan sebagai JSON)
            $table->json('kehadiran')->nullable(); 
            // Format: {"hadir": 30, "sakit": 2, "izin": 1, "alpha": 0}
            
            $table->string('kebersihan')->nullable();
            $table->text('suasana_kelas')->nullable();
            $table->text('catatan_fasilitas')->nullable();
            $table->text('catatan_pribadi_guru')->nullable();
            
            // Data pelanggaran (disimpan sebagai JSON)
            $table->json('pelanggaran')->nullable();
            // Format: [{"nama_siswa": "Budi", "jenis_pelanggaran": "Terlambat"}]
            
            // Status verifikasi
            $table->enum('status', ['menunggu_verifikasi', 'disetujui', 'ditolak'])->default('menunggu_verifikasi');
            $table->foreignId('id_penyetuju')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->text('catatan_penolakan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_harian');
    }
};