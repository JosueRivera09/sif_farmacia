-- =========================================================================
-- ARCHIVO DE ACTUALIZACIÓN DE BASE DE DATOS PARA PHPMyADMIN
-- Propósito: Aplicar solo los cambios y nuevas tablas/vistas en una BD existente.
-- =========================================================================

USE `sistema_sif`;

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------------------
-- 1. Crear tabla 'cierres_caja' (si no existe en la otra PC)
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
SELECT 
    `id_ticket` AS `id_venta`,
    `id_vendedor` AS `id_usuario`,
    `id_cliente`,
    `fecha_creacion` AS `fecha_venta`,
    `total` AS `total_neto`,
    `estado` AS `estado_pago`
FROM `tickets`;

CREATE OR REPLACE VIEW `detalle_ventas` AS 
SELECT 
    `id_detalle`,
    `id_ticket` AS `id_venta`,
    `id_producto`,
    `cantidad`,
    `nivel_empaque`,
    `nombre_empaque`,
    `precio_unitario`
FROM `ticket_detalles`;

SET FOREIGN_KEY_CHECKS = 1;
