<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            $table->dropColumn('gedung');
            $table->foreignId('gedung_id')
                  ->after('jam_selesai')
                  ->constrained('gedung')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['gedung_id']);
            $table->dropColumn('gedung_id');
            $table->string('gedung', 100)->after('jam_selesai');
        });
    }
};
