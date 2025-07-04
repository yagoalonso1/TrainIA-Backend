<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('muscle_groups')->comment('piernas, core, brazos, etc.');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced']);
            $table->string('video_url')->nullable();
            $table->unsignedBigInteger('pose_model_id')->nullable()->comment('ID del modelo IA específico');
            $table->string('category')->comment('Boxeo, Cardio, Fuerza, etc.');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
