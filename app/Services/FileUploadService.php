<?php

namespace App\Services;

use App\Models\FileUpload;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class FileUploadService
{
    /**
     * Sube un archivo y registra su información en la base de datos
     *
     * @param UploadedFile $file
     * @param User $user
     * @param string $type
     * @param string $disk
     * @return FileUpload
     * @throws Exception
     */
    public function uploadFile(UploadedFile $file, User $user, string $type = 'avatar', string $disk = 'public'): FileUpload
    {
        DB::beginTransaction();
        
        try {
            // Generar nombre único para el archivo
            $fileName = FileUpload::generateUniqueFileName($file->getClientOriginalName(), $user->id);
            
            // Definir la carpeta según el tipo
            $folder = $this->getFolderByType($type);
            $filePath = $folder . '/' . $fileName;
            
            // Subir el archivo
            $uploadedPath = $file->storeAs($folder, $fileName, $disk);
            
            if (!$uploadedPath) {
                throw new Exception('Error al subir el archivo');
            }
            
            // Obtener metadata del archivo
            $metadata = $this->extractFileMetadata($file);
            
            // Crear registro en la base de datos
            $fileUpload = FileUpload::create([
                'user_id' => $user->id,
                'type' => $type,
                'original_name' => $file->getClientOriginalName(),
                'file_name' => $fileName,
                'file_path' => $uploadedPath,
                'public_url' => $this->generatePublicUrl($uploadedPath, $disk),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'disk' => $disk,
                'metadata' => $metadata,
                'is_active' => true,
            ]);
            
            // Si es un avatar, desactivar avatares anteriores
            if ($type === 'avatar') {
                $this->deactivatePreviousFiles($user, 'avatar', $fileUpload->id);
            }
            
            DB::commit();
            
            return $fileUpload;
            
        } catch (Exception $e) {
            DB::rollBack();
            
            // Limpiar archivo si se subió pero falló el registro
            if (isset($uploadedPath) && Storage::disk($disk)->exists($uploadedPath)) {
                Storage::disk($disk)->delete($uploadedPath);
            }
            
            throw $e;
        }
    }
    
    /**
     * Elimina un archivo y marca el registro como inactivo
     *
     * @param FileUpload $fileUpload
     * @return bool
     */
    public function deleteFile(FileUpload $fileUpload): bool
    {
        DB::beginTransaction();
        
        try {
            // Eliminar archivo físico
            if ($fileUpload->fileExists()) {
                Storage::disk($fileUpload->disk)->delete($fileUpload->file_path);
            }
            
            // Marcar como inactivo
            $fileUpload->update(['is_active' => false]);
            
            DB::commit();
            
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    
    /**
     * Obtiene la carpeta de destino según el tipo de archivo
     *
     * @param string $type
     * @return string
     */
    private function getFolderByType(string $type): string
    {
        return match ($type) {
            'avatar' => 'avatars',
            'document' => 'documents',
            'exercise_video' => 'exercise_videos',
            'exercise_image' => 'exercise_images',
            default => 'uploads',
        };
    }
    
    /**
     * Genera la URL pública para acceder al archivo
     *
     * @param string $filePath
     * @param string $disk
     * @return string
     */
    private function generatePublicUrl(string $filePath, string $disk): string
    {
        if ($disk === 'public') {
            return '/storage/' . $filePath;
        }
        
        // Para otros discos (S3, etc.) usar la URL del storage
        return Storage::disk($disk)->url($filePath);
    }
    
    /**
     * Extrae metadata del archivo (dimensiones para imágenes, duración para videos, etc.)
     *
     * @param UploadedFile $file
     * @return array
     */
    private function extractFileMetadata(UploadedFile $file): array
    {
        $metadata = [];
        
        // Si es una imagen, obtener dimensiones
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $imagePath = $file->getRealPath();
            $imageSize = getimagesize($imagePath);
            
            if ($imageSize) {
                $metadata['width'] = $imageSize[0];
                $metadata['height'] = $imageSize[1];
                $metadata['aspect_ratio'] = round($imageSize[0] / $imageSize[1], 2);
            }
        }
        
        // Agregar información general
        $metadata['original_extension'] = $file->getClientOriginalExtension();
        $metadata['uploaded_at'] = now()->toISOString();
        
        return $metadata;
    }
    
    /**
     * Desactiva archivos anteriores del mismo tipo
     *
     * @param User $user
     * @param string $type
     * @param int $exceptId
     * @return void
     */
    private function deactivatePreviousFiles(User $user, string $type, int $exceptId): void
    {
        $user->fileUploads()
            ->where('type', $type)
            ->where('is_active', true)
            ->where('id', '!=', $exceptId)
            ->update(['is_active' => false]);
    }
    
    /**
     * Obtiene el avatar actual del usuario
     *
     * @param User $user
     * @return string
     */
    public function getUserAvatarUrl(User $user): string
    {
        $uploadedAvatar = FileUpload::getUserAvatar($user->id);
        
        if ($uploadedAvatar) {
            return $uploadedAvatar->full_url;
        }
        
        // Si no tiene avatar subido, generar uno automático
        return $user->generateAvatarUrl();
    }
    
    /**
     * Limpia archivos huérfanos (registros sin archivo físico)
     *
     * @return int Número de archivos limpiados
     */
    public function cleanOrphanFiles(): int
    {
        $orphanFiles = FileUpload::active()->get()->filter(function ($fileUpload) {
            return !$fileUpload->fileExists();
        });
        
        $cleaned = 0;
        foreach ($orphanFiles as $orphan) {
            $orphan->update(['is_active' => false]);
            $cleaned++;
        }
        
        return $cleaned;
    }
} 