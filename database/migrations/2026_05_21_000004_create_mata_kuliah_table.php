<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mata_kuliah')) return;

        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
            $table->string('kode', 20)->unique();
            $table->string('nama', 255);
            $table->integer('sks');
            $table->integer('semester');
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
