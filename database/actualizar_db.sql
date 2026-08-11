-- =========================================================================
-- ARCHIVO DE ACTUALIZACIÓN INCREMENTAL PARA phpMyAdmin (XAMPP / MySQL / MariaDB)
-- Propósito: Actualizar una base de datos 'sistema_sif' existente sin borrar datos.
-- =========================================================================

USE `sistema_sif`;

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------------------
-- 1. Asegurar la existencia de la tabla 'cierres_caja' y sus columnas
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cierres_caja` (
    `id_cierre` INT(11) NOT NULL AUTO_INCREMENT,
    `id_usuario` INT(11) NOT NULL,
    `fecha_apertura` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `fecha_cierre` DATETIME DEFAULT NULL,
    `monto_inicial` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `monto_final` DECIMAL(10, 2) DEFAULT 0.00,
    `monto_esperado` DECIMAL(10, 2) DEFAULT 0.00,
    `diferencia` DECIMAL(10, 2) DEFAULT 0.00,
    `denominaciones` TEXT DEFAULT NULL,
    `estado` ENUM('Abierto', 'Cerrado') NOT NULL DEFAULT 'Abierto',
    PRIMARY KEY (`id_cierre`),
    KEY `id_usuario` (`id_usuario`),
    CONSTRAINT `cierres_caja_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Asegurar columna 'monto_esperado' en 'cierres_caja'
SET @dbname = DATABASE();
SET @tablename = "cierres_caja";
SET @columnname = "monto_esperado";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE `cierres_caja` ADD COLUMN `monto_esperado` DECIMAL(10, 2) DEFAULT 0.00;"
));
PREPARE addCol FROM @preparedStatement; EXECUTE addCol; DEALLOCATE PREPARE addCol;

-- Asegurar columna 'diferencia' en 'cierres_caja'
SET @columnname = "diferencia";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE `cierres_caja` ADD COLUMN `diferencia` DECIMAL(10, 2) DEFAULT 0.00;"
));
PREPARE addCol FROM @preparedStatement; EXECUTE addCol; DEALLOCATE PREPARE addCol;

-- Asegurar columna 'denominaciones' en 'cierres_caja'
SET @columnname = "denominaciones";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE `cierres_caja` ADD COLUMN `denominaciones` TEXT DEFAULT NULL;"
));
PREPARE addCol FROM @preparedStatement; EXECUTE addCol; DEALLOCATE PREPARE addCol;

-- -------------------------------------------------------------------------
-- 2. Agregar columna 'id_cajero' a la tabla 'tickets' si no existe
-- -------------------------------------------------------------------------
SET @tablename = "tickets";
SET @columnname = "id_cajero";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE `tickets` ADD COLUMN `id_cajero` INT(11) DEFAULT NULL AFTER `id_vendedor`;"
));
PREPARE addCol FROM @preparedStatement; EXECUTE addCol; DEALLOCATE PREPARE addCol;

-- -------------------------------------------------------------------------
-- 3. Vistas SQL para mapear compatibilidad de 'ventas' y 'detalle_ventas' desde los 'tickets'
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `ventas`;
DROP VIEW IF EXISTS `ventas`;
CREATE VIEW `ventas` AS
SELECT 
    `id_ticket` AS `id_venta`,
    `id_vendedor` AS `id_usuario`,
    `id_cajero`,
    `id_cliente`,
    `fecha_creacion` AS `fecha_venta`,
    `total` AS `total_neto`,
    `estado` AS `estado_pago`
FROM `tickets`;

DROP TABLE IF EXISTS `detalle_ventas`;
DROP VIEW IF EXISTS `detalle_ventas`;
CREATE VIEW `detalle_ventas` AS
SELECT 
    `id_detalle`,
    `id_ticket` AS `id_venta`,
    `id_producto`,
    `cantidad`,
    `nivel_empaque`,
    `nombre_empaque`,
    `precio_unitario`
FROM `ticket_detalles`;

-- -------------------------------------------------------------------------
-- 4. Sincronizar stock_actual de productos con la suma real de sus lotes disponibles
-- -------------------------------------------------------------------------
UPDATE `productos` p
LEFT JOIN (
    SELECT `id_producto`, IFNULL(SUM(`cantidad_unidades_recibidas`), 0) AS `total_lotes`
    FROM `lotes`
    WHERE `cantidad_unidades_recibidas` > 0
    GROUP BY `id_producto`
) l ON p.`id_producto` = l.`id_producto`
SET p.`stock_actual` = IFNULL(l.`total_lotes`, 0);

SET FOREIGN_KEY_CHECKS = 1;