<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesi_kuliah', function (Blueprint $table) {
            if (!Schema::hasColumn('sesi_kuliah', 'gedung')) {
                $table->string('gedung', 100)->nullable()->after('mata_kuliah_id');
            }
            if (!Schema::hasColumn('sesi_kuliah', 'lantai')) {
                $table->string('lantai', 50)->nullable()->after('gedung');
            }
            if (!Schema::hasColumn('sesi_kuliah', 'ruangan')) {
                $table->string('ruangan', 100)->nullable()->after('lantai');
            }
        });
    }

    public function down(): void
    {
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
};
