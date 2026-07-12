<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_mata_kuliah', 'lantai')) {
                $table->dropColumn('lantai');
            }
            if (Schema::hasColumn('jadwal_mata_kuliah', 'ruangan')) {
                $table->dropColumn('ruangan');
            }
        });

        Schema::table('sesi_kuliah', function (Blueprint $table) {
            if (Schema::hasColumn('sesi_kuliah', 'gedung')) {
                $table->dropColumn('gedung');
            }
            if (Schema::hasColumn('sesi_kuliah', 'lantai')) {
                $table->dropColumn('lantai');
            }
            if (Schema::hasColumn('sesi_kuliah', 'ruangan')) {
                $table->dropColumn('ruangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            $table->string('lantai', 50)->nullable();
            $table->string('ruangan', 100)->nullable();
        });

        Schema::table('sesi_kuliah', function (Blueprint $table) {
            $table->string('gedung', 100)->nullable();
            $table->string('lantai', 50)->nullable();
            $table->string('ruangan', 100)->nullable();
        });
    }
};
