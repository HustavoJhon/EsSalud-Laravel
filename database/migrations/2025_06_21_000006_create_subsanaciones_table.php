<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subsanaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedure_id')->constrained('procedures')->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->foreignId('requested_by')->constrained('users');
            $table->text('requested_comment');
            $table->timestamp('responded_at')->nullable();
            $table->text('response_comment')->nullable();
            $table->timestamp('deadline');
            $table->boolean('is_fulfilled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subsanaciones');
    }
};
