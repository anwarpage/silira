<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('sender_type', ['admin', 'reporter', 'system'])->default('admin');
            $table->string('sender_name', 120);
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('status_from', 30)->nullable();
            $table->string('status_to', 30)->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamps();

            $table->index(['report_id', 'is_internal', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_responses');
    }
};
