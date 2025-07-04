<?php

namespace App\Console\Commands;

use App\Services\FileUploadService;
use Illuminate\Console\Command;

class CleanOrphanFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:clean-orphans {--dry-run : Solo mostrar qué archivos se limpiarían sin ejecutar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia archivos huérfanos (registros de DB sin archivo físico)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileUploadService = new FileUploadService();
        
        $this->info('🔍 Buscando archivos huérfanos...');
        
        if ($this->option('dry-run')) {
            $this->warn('⚠️  Modo DRY-RUN: No se realizarán cambios');
        }
        
        $cleanedCount = $this->option('dry-run') 
            ? $this->dryRunClean($fileUploadService)
            : $fileUploadService->cleanOrphanFiles();
        
        if ($cleanedCount > 0) {
            $action = $this->option('dry-run') ? 'se limpiarían' : 'se limpiaron';
            $this->info("✅ {$cleanedCount} archivos huérfanos {$action}");
        } else {
            $this->info('✨ No se encontraron archivos huérfanos');
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Simula la limpieza sin realizar cambios
     */
    private function dryRunClean(FileUploadService $service): int
    {
        $orphanFiles = \App\Models\FileUpload::active()->get()->filter(function ($fileUpload) {
            return !$fileUpload->fileExists();
        });
        
        foreach ($orphanFiles as $orphan) {
            $this->line("🗑️  Archivo huérfano: {$orphan->file_path} (ID: {$orphan->id})");
        }
        
        return $orphanFiles->count();
    }
}
