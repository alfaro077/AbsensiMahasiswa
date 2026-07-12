<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dosen')) return;

        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
            $table->string('nip', 20)->unique();
            $table->string('jabatan', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
