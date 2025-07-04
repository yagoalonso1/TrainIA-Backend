<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'role',
        'subscription_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con archivos subidos
     */
    public function fileUploads(): HasMany
    {
        return $this->hasMany(FileUpload::class);
    }

    /**
     * Obtiene archivos activos del usuario
     */
    public function activeFiles(): HasMany
    {
        return $this->fileUploads()->active();
    }

    /**
     * Obtiene el avatar actual del usuario (archivo subido o generado)
     */
    public function getCurrentAvatarAttribute(): string
    {
        // Primero buscar si tiene un avatar subido
        $uploadedAvatar = FileUpload::getUserAvatar($this->id);
        
        if ($uploadedAvatar) {
            return $uploadedAvatar->public_url;
        }
        
        // Si no tiene avatar subido, retornar el generado automáticamente
        return $this->avatar_url ?: $this->generateAvatarUrl();
    }

    /**
     * Genera una URL de avatar automático basada en el nombre del usuario
     */
    public function generateAvatarUrl(): string
    {
        $name = urlencode($this->name);
        $colors = ['FF6B6B', '4ECDC4', 'FFD93D', 'FF8C42', 'C44569', '6C5CE7', 'FD79A8', 'FDCB6E'];
        $background = $colors[abs(crc32($this->email)) % count($colors)];
        
        return "https://ui-avatars.com/api/?name={$name}&background={$background}&color=FFFFFF&size=200&rounded=true";
    }

    /**
     * Desactiva avatares anteriores cuando se sube uno nuevo
     */
    public function deactivatePreviousAvatars(): void
    {
        $this->fileUploads()
            ->where('type', 'avatar')
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
