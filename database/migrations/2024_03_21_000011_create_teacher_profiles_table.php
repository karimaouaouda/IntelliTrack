<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->string('qualification');
            $table->integer('experience_years');
            $table->string('specialization')->nullable();
            $table->text('bio')->nullable();
            $table->string('phone');
            $table->text('address');
            $table->string('emergency_contact');
            $table->json('certifications')->nullable();
            $table->json('skills')->nullable();
            $table->string('profile_photo')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('employment_status')->default('active'); // active, on_leave, terminated
            $table->decimal('salary', 10, 2)->nullable();
            $table->json('working_hours')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure one profile per teacher
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};
