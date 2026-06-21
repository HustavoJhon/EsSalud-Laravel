<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('procedure_id')->nullable()->index()->constrained('procedures');
            $table->foreignId('category_id')->nullable()->constrained('document_categories');
            $table->string('original_name', 255);
            $table->string('stored_path', 512);
            $table->string('mime_type', 100);
            $table->bigInteger('file_size');
            $table->integer('version')->default(1);
            $table->text('ocr_text')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->string('minio_path', 512)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
