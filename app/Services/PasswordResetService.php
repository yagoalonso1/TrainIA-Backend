<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;

class PasswordResetService
{
    /**
     * Generate a temporary password
     */
    public function generateTemporaryPassword(): string
    {
        // Generar contraseña temporal con 12 caracteres
        // Incluye mayúsculas, minúsculas, números y símbolos
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $password = '';
        
        // Asegurar al menos un carácter de cada tipo
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Completar con caracteres aleatorios
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < 12; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mezclar los caracteres
        return str_shuffle($password);
    }

    /**
     * Send forgot password request
     */
    public function sendForgotPassword(string $email): array
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            throw new \Exception('Usuario no encontrado');
        }

        // Generar contraseña temporal
        $temporaryPassword = $this->generateTemporaryPassword();
        
        // Generar token único
        $token = Str::random(60);
        
        // Guardar en la base de datos
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );
        
        // Actualizar contraseña del usuario con la temporal
        $user->password = Hash::make($temporaryPassword);
        $user->save();
        
        // Enviar email con la contraseña temporal
        try {
            Mail::to($user->email)->send(new ForgotPasswordMail($user, $temporaryPassword));
        } catch (\Exception $e) {
            // Si falla el envío, solo loguea el error (no interrumpe el flujo)
            \Log::error('Error enviando email de contraseña temporal: ' . $e->getMessage());
        }
        
        return [
            'success' => true,
            'message' => 'Se ha enviado una contraseña temporal a tu email',
            'data' => [
                'email' => $email,
                'temporary_password' => $temporaryPassword, // Solo para desarrollo
                'token' => $token,
                'expires_at' => now()->addHours(1)->toISOString(),
            ]
        ];
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $email, string $token, string $newPassword): array
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            throw new \Exception('Usuario no encontrado');
        }

        // Verificar token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$resetRecord) {
            throw new \Exception('Token inválido o expirado');
        }

        // Verificar que el token no haya expirado (1 hora)
        if (now()->diffInHours($resetRecord->created_at) > 1) {
            // Eliminar token expirado
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            throw new \Exception('Token expirado');
        }

        // Actualizar contraseña
        $user->password = Hash::make($newPassword);
        $user->save();

        // Eliminar token usado
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return [
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente',
            'data' => [
                'email' => $email,
            ]
        ];
    }

    /**
     * Verify token validity
     */
    public function verifyToken(string $email, string $token): bool
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$resetRecord) {
            return false;
        }

        // Verificar que el token no haya expirado (1 hora)
        if (now()->diffInHours($resetRecord->created_at) > 1) {
            // Eliminar token expirado
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return false;
        }

        return true;
    }
} 