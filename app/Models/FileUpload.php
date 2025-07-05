<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class FileUpload extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'original_name',
        'file_name',
        'file_path',
        'public_url',
        'mime_type',
        'file_size',
        'disk',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Genera un nombre único para el archivo
     */
    public static function generateUniqueFileName($originalName, $userId): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return time() . '_' . $userId . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Obtiene la URL completa del archivo
     */
    public function getFullUrlAttribute(): string
    {
        if (str_starts_with($this->public_url, 'http')) {
            return $this->public_url;
        }
        
        return url($this->public_url);
    }

    /**
     * Verifica si el archivo existe físicamente
     */
    public function fileExists(): bool
    {
        return Storage::disk($this->disk)->exists($this->file_path);
    }

    /**
     * Elimina el archivo físico y marca el registro como inactivo
     */
    public function deleteFile(): bool
    {
        $deleted = true;
        
        if ($this->fileExists()) {
            $deleted = Storage::disk($this->disk)->delete($this->file_path);
        }
        
        if ($deleted) {
            $this->update(['is_active' => false]);
        }
        
        return $deleted;
    }

    /**
     * Obtiene el tamaño formateado legible por humanos
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope para obtener solo archivos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Obtiene el avatar activo del usuario
     */
    public static function getUserAvatar($userId): ?self
    {
        return self::where('user_id', $userId)
            ->where('type', 'avatar')
            ->where('is_active', true)
            ->latest()
            ->first();
    }
}
