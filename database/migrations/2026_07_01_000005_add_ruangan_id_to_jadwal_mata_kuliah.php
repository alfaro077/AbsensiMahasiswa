<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_mata_kuliah', 'ruangan_id')) {
                $table->unsignedBigInteger('ruangan_id')->nullable()->after('kelas_paralel_id');
                $table->foreign('ruangan_id')
                      ->references('id')->on('ruangan')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
            $table->dropColumn('ruangan_id');
        });
    }
};
