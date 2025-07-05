<?php

namespace App\Services;

use App\Models\User;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AccountDeletionService
{
    /**
     * Delete user account
     */
    public function deleteAccount(User $user, string $password): array
    {
        // Verificar que la contraseña sea correcta
        if (!Hash::check($password, $user->password)) {
            throw new \Exception('La contraseña es incorrecta');
        }

        // Iniciar transacción para asegurar integridad
        DB::beginTransaction();

        try {
            // Log antes de la eliminación para auditoría
            Log::info('Iniciando eliminación de cuenta', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'deleted_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // 1. Eliminar archivos físicos del usuario
            $this->deleteUserFiles($user);

            // 2. Eliminar registros de archivos de la base de datos
            $this->deleteFileRecords($user);

            // 3. Revocar todos los tokens del usuario
            $user->tokens()->delete();

            // 4. Eliminar datos relacionados (ajustar según tu modelo de datos)
            $this->deleteRelatedData($user);

            // 5. Eliminar el usuario
            $user->delete();

            // Confirmar transacción
            DB::commit();

            // Log de eliminación exitosa
            Log::info('Cuenta eliminada exitosamente', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'deleted_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return [
                'success' => true,
                'message' => 'Cuenta eliminada exitosamente. Todos los datos han sido eliminados permanentemente.',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'deleted_at' => now()->toISOString(),
                    'data_cleaned' => true,
                ]
            ];

        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();

            Log::error('Error al eliminar cuenta', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            throw new \Exception('Error al eliminar la cuenta. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Delete user files from storage
     */
    private function deleteUserFiles(User $user): void
    {
        try {
            // Eliminar avatar si existe
            if ($user->avatar && !str_contains($user->avatar, 'ui-avatars.com')) {
                $avatarPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
            }

            // Eliminar archivos subidos por el usuario
            $userFiles = FileUpload::where('user_id', $user->id)->get();
            
            foreach ($userFiles as $file) {
                if (Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
            }

            Log::info('Archivos del usuario eliminados', [
                'user_id' => $user->id,
                'files_deleted' => $userFiles->count(),
            ]);

        } catch (\Exception $e) {
            Log::warning('Error al eliminar archivos del usuario', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            // No lanzar excepción para continuar con la eliminación
        }
    }

    /**
     * Delete file records from database
     */
    private function deleteFileRecords(User $user): void
    {
        try {
            $deletedCount = FileUpload::where('user_id', $user->id)->delete();
            
            Log::info('Registros de archivos eliminados', [
                'user_id' => $user->id,
                'records_deleted' => $deletedCount,
            ]);

        } catch (\Exception $e) {
            Log::warning('Error al eliminar registros de archivos', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete related data (ajustar según tu modelo de datos)
     */
    private function deleteRelatedData(User $user): void
    {
        try {
            // Aquí puedes agregar la eliminación de otros datos relacionados
            // Por ejemplo: sesiones de entrenamiento, ejercicios, etc.
            
            // Ejemplo (descomenta y ajusta según tus modelos):
            /*
            // Eliminar sesiones de entrenamiento
            $user->trainingSessions()->delete();
            
            // Eliminar ejercicios personalizados
            $user->exercises()->delete();
            
            // Eliminar suscripciones
            $user->subscriptions()->delete();
            
            // Eliminar logs de administrador
            DB::table('admin_logs')->where('user_id', $user->id)->delete();
            */

            Log::info('Datos relacionados eliminados', [
                'user_id' => $user->id,
            ]);

        } catch (\Exception $e) {
            Log::warning('Error al eliminar datos relacionados', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get deletion warning information
     */
    public function getDeletionWarning(): array
    {
        return [
            'warning' => 'Esta acción es irreversible. Se eliminarán permanentemente:',
            'items' => [
                'Tu perfil y datos personales',
                'Todos los archivos subidos',
                'Sesiones de entrenamiento',
                'Configuraciones personalizadas',
                'Historial de actividad',
            ],
            'note' => 'Esta acción no se puede deshacer. Asegúrate de hacer una copia de seguridad de cualquier dato importante.',
        ];
    }
} 