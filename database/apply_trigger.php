<?php
require_once __DIR__ . '/../config/conexion.php';

// Eliminar el trigger si existe para evitar conflictos
mysqli_query($conexion, "DROP TRIGGER IF EXISTS actualizar_stock_nuevo_lote");

// Consulta de creación del trigger
$sql = "
CREATE TRIGGER actualizar_stock_nuevo_lote
AFTER INSERT ON lotes
FOR EACH ROW
BEGIN
    UPDATE productos 
    SET stock_actual = stock_actual + NEW.cantidad_recibida
    WHERE id_producto = NEW.id_producto;
END
";

if (mysqli_query($conexion, $sql)) {
    echo "Disparador (Trigger) 'actualizar_stock_nuevo_lote' creado con éxito.\n";
} else {
    echo "Error al crear el disparador: " . mysqli_error($conexion) . "\n";
}
?>
