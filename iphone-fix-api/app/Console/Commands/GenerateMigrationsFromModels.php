<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateMigrationsFromModels extends Command
{
    /**
     * Nombre del comando Artisan.
     */
    protected $signature = 'generate:migrations';

    /**
     * Descripción del comando.
     */
    protected $description = 'Genera archivos de migración automáticamente desde los modelos existentes en app/Models (recursivo)';

    public function handle()
    {
        $modelsPath = app_path('Models');

        if (!File::exists($modelsPath)) {
            $this->error("❌ No existe la carpeta: {$modelsPath}");
            return Command::FAILURE;
        }

        // 🔍 Buscar todos los archivos dentro de app/Models y subcarpetas
        $files = File::allFiles($modelsPath);

        if (empty($files)) {
            $this->warn("⚠️ No se encontraron modelos dentro de app/Models ni sus subcarpetas");
            return Command::SUCCESS;
        }

        $this->info("🔍 Analizando modelos en {$modelsPath}...");
        sleep(1);

        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $content = File::get($file->getPathname());

            // Buscar $fillable
            $fillable = [];
            if (preg_match("/protected\s+\$fillable\s*=\s*\[([^\]]*)\]/", $content, $matches)) {
                $fillable = array_filter(array_map(function ($f) {
                    return trim(str_replace(["'", '"'], '', $f));
                }, explode(',', $matches[1])));
            }

            // Buscar $guarded = []
            $guardedAll = false;
            if (preg_match("/protected\s+\$guarded\s*=\s*\[\s*\]/", $content)) {
                $guardedAll = true;
            }

            // Si no hay campos, emitir advertencia
            if (empty($fillable) && !$guardedAll) {
                $this->warn("⚠️ No se encontró \$fillable en {$className}");
                continue;
            }

            // Si $guarded = [], no sabemos los campos, pero generamos la tabla
            if ($guardedAll && empty($fillable)) {
                $this->warn("⚠️ El modelo {$className} usa \$guarded = [], se generará la tabla vacía.");
                $fillable = ['id']; // crear al menos la columna id
            }

            // Construir el esquema tipo "campo:string"
            $schema = implode(', ', array_map(fn($f) => "$f:string", $fillable));
            $tableName = Str::snake(Str::pluralStudly($className));

            // Crear la migración
            $this->callSilent('make:migration', [
                'name' => "create_{$tableName}_table",
                '--create' => $tableName,
            ]);

            $this->info("✅ Migración generada para el modelo: {$className}");
            $this->line("   → Campos: {$schema}");
        }

        $this->info("\n🎉 Proceso completado. Revisa la carpeta database/migrations/");
        return Command::SUCCESS;
    }
}
