CREATE DATABASE IF NOT EXISTS `sistema_sif` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sistema_sif`;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- 1. Tablas independientes
-- --------------------------------------------------------

CREATE TABLE `categorias` (
    `id_categoria` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre_categoria` VARCHAR(100) NOT NULL,
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id_categoria`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `fecha_creacion`) VALUES
(1, 'Analgésicos', '2026-07-18 21:45:02'),
(2, 'Antibióticos', '2026-07-18 21:45:02'),
(3, 'Antiácidos', '2026-07-18 21:45:02'),
(4, 'Vitaminas', '2026-07-18 21:45:02'),
(5, 'Antihistamínicos', '2026-07-18 21:45:02');

CREATE TABLE `laboratorios` (
    `id_laboratorio` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre_laboratorio` VARCHAR(100) NOT NULL,
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id_laboratorio`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `laboratorios` (`id_laboratorio`, `nombre_laboratorio`, `fecha_creacion`) VALUES
(1, 'Bayer', '2026-07-18 21:45:02'),
(2, 'Pfizer', '2026-07-18 21:45:02'),
(3, 'Ramos', '2026-07-18 21:45:02'),
(4, 'Farma', '2026-07-18 21:45:02'),
(5, 'Genfar', '2026-07-18 21:45:02');

CREATE TABLE `clientes` (
    `id_cliente` INT(11) NOT NULL AUTO_INCREMENT,
    `cedula` VARCHAR(20) DEFAULT NULL,
    `nombre_completo` VARCHAR(150) NOT NULL,
    `telefono` VARCHAR(20) DEFAULT NULL,
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id_cliente`),
    UNIQUE KEY `cedula` (`cedula`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `clientes` (`id_cliente`, `cedula`, `nombre_completo`, `telefono`, `fecha_creacion`) VALUES
(1, '521-230206-1002f', 'Joel Juniel', '8888-8888', '2026-07-18 21:45:02'),
(2, '3131312312412412', 'Kevin Rodríguez', '7777-7777', '2026-07-18 21:45:02'),
(3, NULL, 'pepe', NULL, '2026-07-18 22:00:33'),
(4, NULL, 'juan', NULL, '2026-07-18 22:03:34'),
(5, NULL, 'xo', NULL, '2026-07-18 22:06:21'),
(6, NULL, 'zz', NULL, '2026-07-18 22:07:21');

CREATE TABLE `usuarios` (
    `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre_usuario` VARCHAR(50) NOT NULL,
    `clave_acceso` VARCHAR(255) NOT NULL,
    `rol` VARCHAR(20) NOT NULL,
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id_usuario`),
    UNIQUE KEY `nombre_usuario` (`nombre_usuario`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `clave_acceso`, `rol`, `fecha_creacion`) VALUES
(1, 'joel_admin', 'J48_adm!', 'Administrador', '2026-07-18 21:45:02'),
(2, 'darllely_caja', 'D72_caj#', 'Cajero', '2026-07-18 21:45:02'),
(3, 'cesar_ventas', 'C58_vnt$', 'Vendedor', '2026-07-18 21:45:02'),
(4, 'lleral_bodega', 'J22_bod%', 'Bodega', '2026-07-18 21:45:02');

-- --------------------------------------------------------
-- 2. Tablas dependientes
-- --------------------------------------------------------

CREATE TABLE `productos` (
    `id_producto` INT(11) NOT NULL AUTO_INCREMENT,
    `codigo_barras` VARCHAR(50) NOT NULL,
    `nombre_commercial` VARCHAR(100) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `id_categoria` INT(11) NOT NULL,
    `id_laboratorio` INT(11) DEFAULT NULL,
    `empaque_principal` VARCHAR(50) NOT NULL,
    `empaque_medio` VARCHAR(50) DEFAULT NULL,
    `unidad_minima` VARCHAR(50) NOT NULL,
    `unidades_por_empaque_medio` INT(11) DEFAULT 1,
    `unidades_totales_por_empaque_principal` INT(11) NOT NULL DEFAULT 1,
    `es_fraccionable` BIT(1) DEFAULT b'0',
    `precio_empaque_principal` DECIMAL(10, 2) NOT NULL,
    `precio_empaque_medio` DECIMAL(10, 2) DEFAULT NULL,
    `precio_unidad_minima` DECIMAL(10, 2) NOT NULL,
    `miligramos` INT(11) DEFAULT NULL,
    `requiere_receta` BIT(1) DEFAULT b'0',
    `stock_actual` INT(11) DEFAULT 0,
    `stock_minimo` INT(11) DEFAULT 0,
    `tipo_producto` VARCHAR(50) DEFAULT 'General',
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id_producto`),
    UNIQUE KEY `codigo_barras` (`codigo_barras`),
    KEY `id_categoria` (`id_categoria`),
    KEY `id_laboratorio` (`id_laboratorio`),
    CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
    CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_laboratorio`) REFERENCES `laboratorios` (`id_laboratorio`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `productos` (`id_producto`, `codigo_barras`, `nombre_commercial`, `descripcion`, `id_categoria`, `id_laboratorio`, `empaque_principal`, `empaque_medio`, `unidad_minima`, `unidades_por_empaque_medio`, `unidades_totales_por_empaque_principal`, `es_fraccionable`, `precio_empaque_principal`, `precio_empaque_medio`, `precio_unidad_minima`, `miligramos`, `requiere_receta`, `stock_actual`, `stock_minimo`, `tipo_producto`, `fecha_creacion`) VALUES
(1, '7701234567891', 'Paracetamol 500mg', 'Alivio dolor y fiebre', 1, 1, 'Caja', 'Blister', 'Tableta', 10, 100, b'1', 150.00, 20.00, 2.50, 500, b'0', 1000, 0, 'General', '2026-07-18 21:45:02'),
(2, '7701234567892', 'Amoxicilina 250mg', 'Antibiótico', 2, 2, 'Frasco', NULL, 'Jarabe', 1, 1, b'0', 120.00, NULL, 120.00, 250, b'0', 50, 0, 'General', '2026-07-18 21:45:02'),
(3, '7701234567893', 'Omeprazol 20mg', 'Antiácido', 3, 3, 'Caja', NULL, 'Cápsula', 1, 30, b'1', 200.00, NULL, 8.00, 20, b'0', 596, 0, 'General', '2026-07-18 21:45:02'),
(4, '7701234567894', 'Vitamina C', 'Suplemento', 4, 1, 'Caja', 'Blister', 'Gragea', 10, 50, b'1', 250.00, 55.00, 6.00, 1000, b'0', 250, 0, 'General', '2026-07-18 21:45:02'),
(5, '7701234567895', 'Diclofenaco 75mg', 'Inyectable', 1, 5, 'Caja', NULL, 'Ampolla', 1, 5, b'0', 80.00, NULL, 80.00, 75, b'0', 150, 0, 'General', '2026-07-18 21:45:02'),
(6, '7701234567896', 'Loratadina 10mg', 'Alergias', 5, 4, 'Caja', 'Blister', 'Tableta', 10, 20, b'1', 90.00, 48.00, 5.00, 10, b'0', 400, 0, 'General', '2026-07-18 21:45:02'),
(7, '7701234567897', 'Suero Oral', 'Rehidratación', 4, 3, 'Bote', NULL, 'Sobre', 1, 1, b'0', 30.00, NULL, 30.00, NULL, b'0', 100, 0, 'General', '2026-07-18 21:45:02'),
(8, '7701234567898', 'Aspirina Forte', 'Migraña', 1, 1, 'Caja', 'Sobre', 'Pastilla', 2, 40, b'1', 180.00, 12.00, 7.00, 500, b'0', 658, 0, 'General', '2026-07-18 21:45:02'),
(9, '7701234567899', 'Clotrimazol 1%', 'Antimicótico', 2, 5, 'Tubo', NULL, 'Crema', 1, 1, b'0', 110.00, NULL, 110.00, NULL, b'0', 60, 0, 'General', '2026-07-18 21:45:02'),
(10, '7701234567890', 'Azitromicina 500mg', 'Antibiótico fuerte', 2, 2, 'Caja', 'Blister', 'Cápsula', 3, 3, b'1', 280.00, 280.00, 100.00, 500, b'0', 90, 0, 'General', '2026-07-18 21:45:02');

CREATE TABLE `cierres_caja` (
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

CREATE TABLE `permisos_extra` (
    `id_permiso` INT(11) NOT NULL AUTO_INCREMENT,
    `id_usuario` INT(11) NOT NULL,
    `modulo` VARCHAR(50) NOT NULL,
    `fecha_otorgado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id_permiso`),
    KEY `id_usuario` (`id_usuario`),
    CONSTRAINT `permisos_extra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `lotes` (
    `id_lote` INT(11) NOT NULL AUTO_INCREMENT,
    `id_producto` INT(11) NOT NULL,
    `numero_lote` VARCHAR(50) NOT NULL,
    `empaque_ingreso` VARCHAR(50) NOT NULL,
    `cantidad_empaques_recibidos` INT(11) NOT NULL,
    `cantidad_unidades_recibidas` INT(11) NOT NULL,
    `fecha_vencimiento` DATE NOT NULL,
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `bodega` VARCHAR(100) DEFAULT 'Bodega Principal - Managua',
    PRIMARY KEY (`id_lote`),
    KEY `id_producto` (`id_producto`),
    CONSTRAINT `lotes_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `lotes` (`id_lote`, `id_producto`, `numero_lote`, `empaque_ingreso`, `cantidad_empaques_recibidos`, `cantidad_unidades_recibidas`, `fecha_vencimiento`, `fecha_creacion`, `bodega`) VALUES
(1, 1, 'L-001', 'Principal', 10, 1000, '2026-07-25', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(2, 2, 'L-002', 'Principal', 50, 50, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(3, 3, 'L-003', 'Principal', 20, 600, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(4, 4, 'L-004', 'Principal', 5, 250, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(5, 5, 'L-005', 'Principal', 30, 150, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(6, 6, 'L-006', 'Principal', 20, 400, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(7, 7, 'L-007', 'Principal', 100, 100, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(8, 8, 'L-008', 'Principal', 20, 800, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(9, 9, 'L-009', 'Principal', 60, 60, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua'),
(10, 10, 'L-010', 'Principal', 30, 90, '2027-07-18', '2026-07-18 21:45:02', 'Bodega Principal - Managua');

CREATE TABLE `tickets` (
    `id_ticket` INT(11) NOT NULL AUTO_INCREMENT,
    `codigo_ticket` VARCHAR(20) NOT NULL,
    `total` DECIMAL(10, 2) NOT NULL,
    `estado` VARCHAR(20) DEFAULT 'Pendiente',
    `id_vendedor` INT(11) NOT NULL,
    `id_cajero` INT(11) DEFAULT NULL,
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `id_cliente` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id_ticket`),
    UNIQUE KEY `codigo_ticket` (`codigo_ticket`),
    KEY `id_vendedor` (`id_vendedor`),
    KEY `id_cajero` (`id_cajero`),
    KEY `id_cliente` (`id_cliente`),
    CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_vendedor`) REFERENCES `usuarios` (`id_usuario`),
    CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL,
    CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`id_cajero`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `tickets` (`id_ticket`, `codigo_ticket`, `total`, `estado`, `id_vendedor`, `fecha_creacion`, `id_cliente`) VALUES
(1, 'TK-D4B7CC', 32.00, 'Pagado', 1, '2026-07-18 22:00:33', 3),
(2, 'TK-211A49', 700.00, 'Pagado', 1, '2026-07-18 22:03:34', 4),
(3, 'TK-E8D309', 180.00, 'Pagado', 1, '2026-07-18 22:06:21', 5),
(4, 'TK-D8E96E', 12.00, 'Pagado', 1, '2026-07-18 22:07:21', 6);

CREATE TABLE `ticket_detalles` (
    `id_detalle` INT(11) NOT NULL AUTO_INCREMENT,
    `id_ticket` INT(11) NOT NULL,
    `id_producto` INT(11) NOT NULL,
    `cantidad` INT(11) NOT NULL,
    `nivel_empaque` VARCHAR(20) NOT NULL DEFAULT 'Principal',
    `nombre_empaque` VARCHAR(50) NOT NULL,
    `precio_unitario` DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (`id_detalle`),
    KEY `id_ticket` (`id_ticket`),
    KEY `id_producto` (`id_producto`),
    CONSTRAINT `ticket_detalles_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`) ON DELETE CASCADE,
    CONSTRAINT `ticket_detalles_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `ticket_detalles` (`id_detalle`, `id_ticket`, `id_producto`, `cantidad`, `nivel_empaque`, `nombre_empaque`, `precio_unitario`) VALUES
(1, 1, 3, 4, 'Minimo', 'Cápsula', 8.00),
(2, 2, 8, 100, 'Minimo', 'Pastilla', 7.00),
(3, 3, 8, 1, 'Principal', 'Caja', 180.00),
(4, 4, 8, 1, 'Medio', 'Sobre', 12.00);

-- --------------------------------------------------------
-- 3. Vistas de compatibilidad (Mapeo desde Tickets)
-- --------------------------------------------------------

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

-- --------------------------------------------------------
-- 4. Disparadores (Triggers)
-- --------------------------------------------------------

DELIMITER $$
CREATE TRIGGER `actualizar_stock_nuevo_lote`
AFTER INSERT ON `lotes` FOR EACH ROW
BEGIN
    UPDATE productos
    SET stock_actual = stock_actual + NEW.cantidad_unidades_recibidas
    WHERE id_producto = NEW.id_producto;
END $$
DELIMITER ;

-- --------------------------------------------------------
-- 5. Sincronización final de Stock por Lotes
-- --------------------------------------------------------

UPDATE `productos` p
LEFT JOIN (
    SELECT `id_producto`, IFNULL(SUM(`cantidad_unidades_recibidas`), 0) AS `total_lotes`
    FROM `lotes`
    WHERE `cantidad_unidades_recibidas` > 0
    GROUP BY `id_producto`
) l ON p.`id_producto` = l.`id_producto`
SET p.`stock_actual` = IFNULL(l.`total_lotes`, 0);

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;