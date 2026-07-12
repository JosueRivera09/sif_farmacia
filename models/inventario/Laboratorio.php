<?php
/*
 * Archivo: models/Laboratorio.php
 * Propósito: Modelo de datos para laboratorios.
 * Qué muestra: No muestra nada. Provee métodos para listar laboratorios.
 */

class Laboratorio {
    public static function obtenerLaboratoriosParaIngreso(mysqli $conexion) {
        $laboratorios = [];
        $query = "SELECT id_laboratorio, nombre_laboratorio FROM laboratorios ORDER BY nombre_laboratorio ASC";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $laboratorios[] = $row;
            }
        }
        return $laboratorios;
    }
}
?>
