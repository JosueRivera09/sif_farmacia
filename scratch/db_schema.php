<?php
/*
 * Archivo: scratch/db_schema.php
 * Propósito: Esquema de la base de datos (copia/scratch).
 */

require_once __DIR__ . '/../config/conexion.php';

$res = mysqli_query($conexion, "SHOW TABLES");
echo "=== TABLES ===\n";
while($row = mysqli_fetch_row($res)) {
    echo "- " . $row[0] . "\n";
    $columns = mysqli_query($conexion, "DESCRIBE " . $row[0]);
    while($col = mysqli_fetch_assoc($columns)) {
        echo "  * " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}
