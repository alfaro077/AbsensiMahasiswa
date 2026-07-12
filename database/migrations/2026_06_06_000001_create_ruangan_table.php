<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ruangan')) return;

        Schema::create('ruangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gedung_id')->constrained('gedung')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('lantai', 50);
            $table->integer('kapasitas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
