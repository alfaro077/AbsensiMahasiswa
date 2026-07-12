<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jadwal_mata_kuliah')) return;

        Schema::create('jadwal_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')
                  ->constrained('mata_kuliah')
                  ->onDelete('cascade');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('gedung', 100);
            $table->string('lantai', 50);
            $table->string('ruangan', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mata_kuliah');
    }
};
