<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->index()->constrained('chat_sessions')->cascadeOnDelete();
            $table->string('role', 10);
            $table->text('content');
            $table->string('message_type', 20)->default('text');
            $table->json('sources')->nullable();
            $table->float('confidence')->nullable();
            $table->integer('latency_ms')->nullable();
            $table->boolean('feedback_helpful')->nullable();
            $table->text('feedback_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
