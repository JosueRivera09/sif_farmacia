CREATE DATABASE IF NOT EXISTS sistema_sif CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE sistema_sif;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ticket_detalles;

DROP TABLE IF EXISTS tickets;

DROP TABLE IF EXISTS detalle_ventas;

DROP TABLE IF EXISTS ventas;

DROP TABLE IF EXISTS lotes;

DROP TABLE IF EXISTS productos;

DROP TABLE IF EXISTS cierres_caja;

DROP TABLE IF EXISTS usuarios;

DROP TABLE IF EXISTS clientes;

DROP TABLE IF EXISTS laboratorios;

DROP TABLE IF EXISTS categorias;

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE laboratorios (
    id_laboratorio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_laboratorio VARCHAR(100) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    clave_acceso VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) UNIQUE,
    nombre_completo VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo_barras VARCHAR(50) NOT NULL UNIQUE,
    nombre_commercial VARCHAR(100) NOT NULL,
    descripcion TEXT,
    id_categoria INT NOT NULL,
    id_laboratorio INT DEFAULT NULL,
    miligramos INT DEFAULT NULL,
    unidad_medida VARCHAR(50) NOT NULL,
    requiere_receta BIT(1) DEFAULT b'0',
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 0,
    precio_venta_actual DECIMAL(10, 2) NOT NULL,
    tipo_producto VARCHAR(50) DEFAULT 'General',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria),
    FOREIGN KEY (id_laboratorio) REFERENCES laboratorios (id_laboratorio)
);

CREATE TABLE lotes (
    id_lote INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    numero_lote VARCHAR(50) NOT NULL,
    cantidad_recibida INT NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bodega VARCHAR(100) DEFAULT 'Bodega Principal - Managua',
    FOREIGN KEY (id_producto) REFERENCES productos (id_producto) ON DELETE CASCADE
);

CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cliente INT DEFAULT NULL,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_neto DECIMAL(10, 2) DEFAULT 0,
    estado_pago VARCHAR(20) DEFAULT 'Pendiente',
    FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario),
    FOREIGN KEY (id_cliente) REFERENCES clientes (id_cliente)
);

CREATE TABLE detalle_ventas (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas (id_venta) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos (id_producto)
);

CREATE TABLE tickets (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    codigo_ticket VARCHAR(20) NOT NULL UNIQUE,
    total DECIMAL(10, 2) NOT NULL,
    estado VARCHAR(20) DEFAULT 'Pendiente',
    id_vendedor INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_cliente INT DEFAULT NULL,
    FOREIGN KEY (id_vendedor) REFERENCES usuarios (id_usuario),
    FOREIGN KEY (id_cliente) REFERENCES clientes (id_cliente) ON DELETE SET NULL
);

CREATE TABLE ticket_detalles (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_ticket) REFERENCES tickets (id_ticket) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos (id_producto) ON DELETE CASCADE
);

CREATE TABLE cierres_caja (
    id_cierre INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP NULL,
    monto_inicial DECIMAL(10, 2) DEFAULT 0,
    monto_final DECIMAL(10, 2),
    estado VARCHAR(20) DEFAULT 'Abierto',
    FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
);

-- =========================================================
-- CAMBIOS RECIENTES: CONTROL DE ACCESO DINÁMICO
-- =========================================================

-- CÓDIGO NORMAL (Para ejecutar si es la primera vez que se crea la BD)
CREATE TABLE IF NOT EXISTS permisos_extra (
    id_permiso INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    fecha_otorgado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
);

/*
-- =========================================================
-- CÓDIGO DE ACTUALIZACIÓN (Si ya tenías la BD creada antes)
-- =========================================================
-- Si tu base de datos ya existe y sólo necesitas aplicar los cambios posterior a domingo 12 de julio,
-- copia y ejecuta este bloque en tu gestor de base de datos (phpMyAdmin, MySQL, etc.)

CREATE TABLE IF NOT EXISTS permisos_extra (
id_permiso INT AUTO_INCREMENT PRIMARY KEY,
id_usuario INT NOT NULL,
modulo VARCHAR(50) NOT NULL,
fecha_otorgado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
);
*/

INSERT INTO categorias (nombre_categoria) VALUES ('analgesico');

INSERT INTO laboratorios (nombre_laboratorio) VALUES ('bayer');

INSERT INTO
    usuarios (
        nombre_usuario,
        clave_acceso,
        rol
    )
VALUES (
        'joel_admin',
        'J48_adm!',
        'Administrador'
    ),
    (
        'darllely_caja',
        'D72_caj#',
        'Cajero'
    ),
    (
        'cesar_ventas',
        'C58_vnt$',
        'Vendedor'
    ),
    (
        'lleral_bodega',
        'J22_bod%',
        'Bodega'
    );

INSERT INTO
    clientes (cedula, nombre_completo)
VALUES (
        '521-230206-1002f',
        'joel juniel'
    ),
    ('3131312312412412', 'kevin');

INSERT INTO
    tickets (
        codigo_ticket,
        total,
        estado,
        id_vendedor,
        id_cliente
    )
VALUES (
        'TK-25F6C0',
        17.50,
        'Pendiente',
        3,
        1
    );

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;