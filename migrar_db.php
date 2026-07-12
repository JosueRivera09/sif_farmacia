<?php
/*
 * Archivo: migrar_db.php
 * Propósito: Este script se utiliza para ejecutar cambios o actualizaciones estructurales 
 * (migraciones) en la base de datos de forma rápida sin tener que hacerlo manualmente en phpMyAdmin.
 * 
 * Uso: Solo debe ejecutarse cuando se han agregado nuevas tablas o columnas al sistema.
 */
require_once 'config/conexion.php';

// Aquí se coloca la consulta SQL que se desea ejecutar en la base de datos
$sql = "CREATE TABLE IF NOT EXISTS permisos_extra (
    id_permiso INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    fecha_otorgado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
)";

// Se ejecuta la consulta y se notifica el resultado
if (mysqli_query($conexion, $sql)) {
    echo "Migración completada: Tabla permisos_extra creada o ya existe.\n";
} else {
    echo "Error ejecutando migración: " . mysqli_error($conexion) . "\n";
}
