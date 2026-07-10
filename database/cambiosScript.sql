USE `sistema_sif`;

-- 1. Agregar la columna 'bodega' a la tabla lotes (soluciona el error de Lote.php:56)
ALTER TABLE `lotes`
ADD COLUMN `bodega` varchar(100) DEFAULT 'Bodega Principal - Managua';

-- 2. Creación de la tabla de 'clientes'
CREATE TABLE IF NOT EXISTS `clientes` (
    `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
    `cedula` varchar(50) DEFAULT NULL,
    `nombre_completo` varchar(150) NOT NULL,
    `telefono` varchar(50) DEFAULT NULL,
    `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id_cliente`),
    UNIQUE KEY `cedula` (`cedula`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- 3. Creación de la tabla de 'tickets'
CREATE TABLE IF NOT EXISTS `tickets` (
    `id_ticket` int(11) NOT NULL AUTO_INCREMENT,
    `codigo_ticket` varchar(20) NOT NULL,
    `total` decimal(10, 2) NOT NULL,
    `estado` varchar(20) NOT NULL DEFAULT 'Pendiente',
    `id_vendedor` int(11) NOT NULL,
    `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id_ticket`),
    UNIQUE KEY `codigo_ticket` (`codigo_ticket`),
    KEY `id_vendedor` (`id_vendedor`),
    CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_vendedor`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- 4. Modificación a 'tickets' para relacionarlo con 'clientes'
ALTER TABLE `tickets`
ADD COLUMN `id_cliente` int(11) DEFAULT NULL,
ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL;

-- 5. Creación de la tabla de detalles de ticket ('ticket_detalles')
CREATE TABLE IF NOT EXISTS `ticket_detalles` (
    `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
    `id_ticket` int(11) NOT NULL,
    `id_producto` int(11) NOT NULL,
    `cantidad` int(11) NOT NULL,
    `precio_unitario` decimal(10, 2) NOT NULL,
    PRIMARY KEY (`id_detalle`),
    KEY `id_ticket` (`id_ticket`),
    KEY `id_producto` (`id_producto`),
    CONSTRAINT `ticket_detalles_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`) ON DELETE CASCADE,
    CONSTRAINT `ticket_detalles_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;