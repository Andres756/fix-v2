DELIMITER $$

CREATE TRIGGER after_delete_salida_producto
AFTER DELETE ON salidas_producto
FOR EACH ROW
BEGIN
    DECLARE v_stock_actual INT;
    DECLARE v_nuevo_stock INT;
    DECLARE v_stock_minimo INT;
    DECLARE v_nuevo_estado INT UNSIGNED;
    
    -- Obtener valores actuales
    SELECT stock, stock_minimo 
    INTO v_stock_actual, v_stock_minimo
    FROM inventarios 
    WHERE id = OLD.inventario_id;
    
    -- Restaurar stock
    SET v_nuevo_stock = v_stock_actual + OLD.cantidad;
    
    -- Determinar nuevo estado
    IF v_nuevo_stock = 0 THEN
        SET v_nuevo_estado = 2; -- SIN STOCK
    ELSEIF v_nuevo_stock > 0 AND v_nuevo_stock <= v_stock_minimo THEN
        SET v_nuevo_estado = 3; -- STOCK BAJO
    ELSE
        SET v_nuevo_estado = 1; -- DISPONIBLE
    END IF;
    
    -- Actualizar inventario
    UPDATE inventarios 
    SET 
        stock = v_nuevo_stock,
        estado_inventario_id = v_nuevo_estado
    WHERE id = OLD.inventario_id;
END$$

DELIMITER ;