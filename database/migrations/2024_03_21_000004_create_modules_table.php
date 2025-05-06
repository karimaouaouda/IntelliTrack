<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('credits');
            $table->timestamps();
        });

        // Create pivot table for module-teacher relationship
        Schema::create('module_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['module_id', 'user_id']);
        });

        // Create pivot table for module-classroom relationship
        Schema::create('module_classroom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['module_id', 'classroom_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_classroom');
        Schema::dropIfExists('module_teacher');
        Schema::dropIfExists('modules');
    }
}; 