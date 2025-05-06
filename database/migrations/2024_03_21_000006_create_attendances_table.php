<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->morphs('attendable');
            $table->enum('type', ['in', 'out']);
            $table->string('device_id')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            // Add index for faster queries
            $table->index(['attendable_type', 'attendable_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
}; 