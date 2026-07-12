<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('jadwal_mata_kuliah', 'gedung')) return;

        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            $table->dropColumn('gedung');
        });

        if (!Schema::hasColumn('jadwal_mata_kuliah', 'gedung_id')) {
            Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
                $table->foreignId('gedung_id')
                      ->after('jam_selesai')
                      ->constrained('gedung')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('jadwal_mata_kuliah', 'gedung_id')) {
            Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
                $table->dropForeign(['gedung_id']);
                $table->dropColumn('gedung_id');
            });
        }
        if (!Schema::hasColumn('jadwal_mata_kuliah', 'gedung')) {
            Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
                $table->string('gedung', 100)->after('jam_selesai');
            });
        }
    }
};
