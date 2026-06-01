<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sesi_kuliah', function (Blueprint $table) {
            $table->string('gedung', 100)->nullable()->after('topik');
            $table->string('lantai', 50)->nullable()->after('gedung');
            $table->string('ruangan', 100)->nullable()->after('lantai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_kuliah', function (Blueprint $table) {
            $table->dropColumn(['gedung', 'lantai', 'ruangan']);
        });
    }
};
