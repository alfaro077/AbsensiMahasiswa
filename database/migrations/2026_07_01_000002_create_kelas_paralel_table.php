<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_paralel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_kuliah_id');
            $table->string('nama_kelas', 20);
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->string('tahun_ajaran', 20)->nullable();
            $table->timestamps();

            $table->foreign('mata_kuliah_id')
                  ->references('id')->on('mata_kuliah')
                  ->onDelete('cascade');
            $table->foreign('dosen_id')
                  ->references('id')->on('dosen')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_paralel');
    }
};
