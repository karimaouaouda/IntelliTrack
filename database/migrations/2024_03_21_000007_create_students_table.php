<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('ref_id')->unique();
            $table->string('name');
            $table->timestamp('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('medical_conditions')->nullable();
            $table->string('email')->unique()->nullable();
            $table->mediumText('notes')->nullable();
            $table->timestamps();
        });

        // Create pivot table for student-classroom relationship
        Schema::create('classroom_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['classroom_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_student');
        Schema::dropIfExists('students');
    }
};
