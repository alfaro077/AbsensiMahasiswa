<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('presensi')) return;

        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi_kuliah')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->dateTime('waktu_absen');
            $table->string('metode', 50);
            $table->string('status', 20);
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
