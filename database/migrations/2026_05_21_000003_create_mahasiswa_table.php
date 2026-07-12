<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mahasiswa')) return;

        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
            $table->string('nim', 20)->unique();
            $table->integer('angkatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
