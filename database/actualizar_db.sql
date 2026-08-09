-- =========================================================================
-- ARCHIVO DE ACTUALIZACIÓN DE BASE DE DATOS PARA PHPMyADMIN
-- Propósito: Aplicar solo los cambios y nuevas tablas/vistas en una BD existente.
-- =========================================================================
USE `sistema_sif`;
SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------------------
-- 1. Crear tabla 'cierres_caja' (si no existe)
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

-- -------------------------------------------------------------------------
-- 2. Vistas SQL para mapear compatibilidad de 'ventas' y 'detalle_ventas' desde los 'tickets'
-- -------------------------------------------------------------------------
CREATE OR REPLACE VIEW `ventas` AS
SELECT `id_ticket` AS `id_venta`,
    `id_vendedor` AS `id_usuario`,
    `id_cliente`,
    `fecha_creacion` AS `fecha_venta`,
    `total` AS `total_neto`,
    `estado` AS `estado_pago`
FROM `tickets`;

CREATE OR REPLACE VIEW `detalle_ventas` AS
SELECT `id_detalle`,
    `id_ticket` AS `id_venta`,
    `id_producto`,
    `cantidad`,
    `nivel_empaque`,
    `nombre_empaque`,
    `precio_unitario`
FROM `ticket_detalles`;

-- -------------------------------------------------------------------------
-- 3. Sincronizar stock_actual de productos con la suma real de sus lotes disponibles
-- -------------------------------------------------------------------------
UPDATE `productos` p
LEFT JOIN (
    SELECT `id_producto`, IFNULL(SUM(`cantidad_unidades_recibidas`), 0) AS `total_lotes`
    FROM `lotes`
    WHERE `cantidad_unidades_recibidas` > 0
    GROUP BY `id_producto`
) l ON p.`id_producto` = l.`id_producto`
SET p.`stock_actual` = IFNULL(l.`total_lotes`, 0);

-- -------------------------------------------------------------------------
-- 4. Agregar columna 'id_cajero' a la tabla 'tickets' si no existe
-- -------------------------------------------------------------------------
SET @dbname = DATABASE();
SET @tablename = "tickets";
SET @columnname = "id_cajero";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `tickets` ADD COLUMN `id_cajero` INT(11) DEFAULT NULL AFTER `id_vendedor`;"
));
PREPARE addColumnIfNotExist FROM @preparedStatement;
EXECUTE addColumnIfNotExist;
DEALLOCATE PREPARE addColumnIfNotExist;

SET FOREIGN_KEY_CHECKS = 1;