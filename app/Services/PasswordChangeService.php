<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PasswordChangeService
{
    /**
     * Change user password
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('La contraseña actual es incorrecta');
        }

        // Verificar que la nueva contraseña no sea igual a la actual
        if (Hash::check($newPassword, $user->password)) {
            throw new \Exception('La nueva contraseña debe ser diferente a la actual');
        }

        // Verificar que la nueva contraseña no sea una de las últimas 3 contraseñas
        // (esto requeriría una tabla adicional para historial de contraseñas)
        // Por ahora, solo verificamos que no sea igual a la actual

        // Actualizar la contraseña
        $user->password = Hash::make($newPassword);
        $user->save();

        // Log del cambio de contraseña para auditoría
        Log::info('Usuario cambió contraseña', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'changed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return [
            'success' => true,
            'message' => 'Contraseña cambiada exitosamente',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'changed_at' => now()->toISOString(),
            ]
        ];
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        
        // Verificar longitud mínima
        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres';
        }

        // Verificar que contenga al menos una letra mayúscula
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una letra mayúscula';
        }

        // Verificar que contenga al menos una letra minúscula
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una letra minúscula';
        }

        // Verificar que contenga al menos un número
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un número';
        }

        // Verificar que contenga al menos un carácter especial
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un carácter especial';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'strength' => $this->calculatePasswordStrength($password),
        ];
    }

    /**
     * Calculate password strength (0-100)
     */
    private function calculatePasswordStrength(string $password): int
    {
        $score = 0;
        
        // Longitud
        $score += min(strlen($password) * 4, 40);
        
        // Complejidad
        if (preg_match('/[a-z]/', $password)) $score += 10;
        if (preg_match('/[A-Z]/', $password)) $score += 10;
        if (preg_match('/[0-9]/', $password)) $score += 10;
        if (preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) $score += 10;
        
        // Variedad de caracteres
        $uniqueChars = count(array_unique(str_split($password)));
        $score += min($uniqueChars * 2, 20);
        
        return min($score, 100);
    }

    /**
     * Get password strength label
     */
    public function getPasswordStrengthLabel(int $strength): string
    {
        if ($strength >= 80) return 'Muy Fuerte';
        if ($strength >= 60) return 'Fuerte';
        if ($strength >= 40) return 'Media';
        if ($strength >= 20) return 'Débil';
        return 'Muy Débil';
    }
} 