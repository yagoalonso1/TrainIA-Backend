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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->comment('avatar, document, etc.'); // Tipo de archivo
            $table->string('original_name'); // Nombre original del archivo
            $table->string('file_name'); // Nombre único generado
            $table->string('file_path'); // Ruta completa del archivo
            $table->string('public_url'); // URL pública para acceder
            $table->string('mime_type'); // image/jpeg, image/png, etc.
            $table->unsignedInteger('file_size'); // Tamaño en bytes
            $table->string('disk')->default('public'); // Disco de almacenamiento
            $table->json('metadata')->nullable(); // Metadata adicional (dimensiones, etc.)
            $table->boolean('is_active')->default(true); // Para soft delete
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['user_id', 'type']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};
