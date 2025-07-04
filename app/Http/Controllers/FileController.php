<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploadService;

class FileController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get all files for the authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $files = $user->activeFiles()->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'files' => $files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'type' => $file->type,
                        'original_name' => $file->original_name,
                        'public_url' => $file->full_url,
                        'file_size' => $file->formatted_size,
                        'uploaded_at' => $file->created_at->diffForHumans(),
                        'metadata' => $file->metadata,
                    ];
                })
            ]
        ]);
    }

    /**
     * Delete a specific file
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $file = $user->fileUploads()->findOrFail($id);
        
        $deleted = $this->fileUploadService->deleteFile($file);
        
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Archivo eliminado exitosamente' : 'Error al eliminar el archivo'
        ]);
    }
} 