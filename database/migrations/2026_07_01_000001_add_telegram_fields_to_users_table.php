<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'telegram_chat_id')) {
                $table->string('telegram_chat_id', 50)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'telegram_token')) {
                $table->string('telegram_token', 100)->nullable()->unique()->after('telegram_chat_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telegram_chat_id', 'telegram_token']);
        });
    }
};
