<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_harian', function (Blueprint $table) {
            // Tambahkan kolom yang dibutuhkan oleh JurnalController
            $table->string('cuaca')->nullable()->after('tanggal');
            $table->integer('terlambat')->default(0)->after('kehadiran');
            $table->text('catatan_ketidakhadiran')->nullable()->after('terlambat');
            $table->string('mata_pelajaran')->nullable()->after('catatan_fasilitas');
            $table->string('topik_materi')->nullable()->after('mata_pelajaran');
            $table->string('metode_pembelajaran')->nullable()->after('topik_materi');
            $table->text('hambatan_belajar')->nullable()->after('metode_pembelajaran');
            $table->text('kejadian_positif')->nullable()->after('hambatan_belajar');
            $table->string('siswa_bermasalah')->nullable()->after('kejadian_positif');
            $table->string('suhu_ruangan')->nullable()->after('kebersihan');
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_harian', function (Blueprint $table) {
            $table->dropColumn([
                'cuaca', 'terlambat', 'catatan_ketidakhadiran', 
                'mata_pelajaran', 'topik_materi', 'metode_pembelajaran', 
                'hambatan_belajar', 'kejadian_positif', 'siswa_bermasalah', 'suhu_ruangan'
            ]);
        });
    }
};