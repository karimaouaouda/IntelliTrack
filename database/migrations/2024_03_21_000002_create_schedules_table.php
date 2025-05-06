<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('day_of_week');
            $table->timestamps();

            // Add a unique constraint to prevent scheduling conflicts
            $table->unique(['classroom_id', 'start_time', 'end_time', 'day_of_week'], 'unique_classroom_schedule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
}; 