<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_paralel_id')->nullable()->after('mata_kuliah_id');
            $table->foreign('kelas_paralel_id')
                  ->references('id')->on('kelas_paralel')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['kelas_paralel_id']);
            $table->dropColumn('kelas_paralel_id');
        });
    }
};
