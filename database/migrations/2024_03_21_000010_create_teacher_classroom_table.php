<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_classroom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->boolean('is_primary_teacher')->default(false);
            $table->json('schedule')->nullable(); // Store teaching schedule
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure a teacher can't be assigned to the same classroom twice
            $table->unique(['user_id', 'classroom_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_classroom');
    }
}; 