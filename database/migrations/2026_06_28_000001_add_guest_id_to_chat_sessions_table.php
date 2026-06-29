<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_id', 255)->nullable()->index()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn('guest_id');
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
