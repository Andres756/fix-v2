<?php
// Reemplaza el contenido de tu migration: 2025_09_20_195633_add_triggers_estado_inventario_automatico.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Primero verificar la estructura de la tabla estados_inventario
        $columns = DB::select("SHOW COLUMNS FROM estados_inventario");
        $columnNames = array_column($columns, 'Field');
        
        // 2. Crear el estado faltante (ya tienes DISPONIBLE y SIN STOCK)
        // Solo crear STOCK BAJO si no existe
        DB::statement("
            INSERT IGNORE INTO estados_inventario (id, nombre, mostrar_en_stock) VALUES
            (3, 'STOCK BAJO', 1)
        ");

        // 3. Actualizar registros existentes para que tengan estado correcto
        DB::statement("
            UPDATE inventarios 
            SET estado_inventario_id = CASE 
                WHEN stock = 0 THEN 2
                WHEN stock_minimo > 0 AND stock <= stock_minimo THEN 3
                ELSE 1
            END
            WHERE estado_inventario_id IS NULL 
               OR estado_inventario_id = 0
               OR estado_inventario_id NOT IN (1, 2, 3)
        ");

        // 5. Crear trigger para INSERT (cuando se crea un inventario)
        DB::unprepared("
            CREATE TRIGGER actualizar_estado_inventario_insert
                BEFORE INSERT ON inventarios
                FOR EACH ROW
            BEGIN
                IF NEW.stock = 0 THEN
                    SET NEW.estado_inventario_id = 2;  -- SIN STOCK
                ELSEIF NEW.stock_minimo > 0 AND NEW.stock <= NEW.stock_minimo THEN
                    SET NEW.estado_inventario_id = 3;  -- STOCK BAJO
                ELSE
                    SET NEW.estado_inventario_id = 1;  -- DISPONIBLE
                END IF;
            END
        ");

        // 6. Crear trigger para UPDATE (cuando se actualiza el stock)
        DB::unprepared("
            CREATE TRIGGER actualizar_estado_inventario_update
                BEFORE UPDATE ON inventarios
                FOR EACH ROW
            BEGIN
                -- Solo actualizar si cambió el stock o stock_minimo
                IF NEW.stock != OLD.stock OR NEW.stock_minimo != OLD.stock_minimo THEN
                    IF NEW.stock = 0 THEN
                        SET NEW.estado_inventario_id = 2;  -- SIN STOCK
                    ELSEIF NEW.stock_minimo > 0 AND NEW.stock <= NEW.stock_minimo THEN
                        SET NEW.estado_inventario_id = 3;  -- STOCK BAJO
                    ELSE
                        SET NEW.estado_inventario_id = 1;  -- DISPONIBLE
                    END IF;
                END IF;
            END
        ");
    }

    public function down()
    {
        // Eliminar triggers
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_inventario_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_inventario_update');
    }
};