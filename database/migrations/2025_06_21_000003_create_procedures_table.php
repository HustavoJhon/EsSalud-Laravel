<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('procedure_type_id')->constrained('procedure_types');
            $table->foreignId('procedure_status_id')->constrained('procedure_statuses');
            $table->foreignId('current_assignee_id')->nullable()->constrained('users');
            $table->json('data')->nullable();
            $table->string('idempotency_key', 64)->nullable()->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
