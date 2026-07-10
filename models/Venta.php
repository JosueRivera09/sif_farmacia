<?php
/*
 * Archivo: models/Venta.php
 * Propósito: Modelo de datos para las métricas y operaciones de ventas de los vendedores.
 * Qué muestra: No muestra nada. Provee métodos estáticos.
 */

class Venta {
    public static function obtenerMetricasVendedor(mysqli $conexion) {
        $metricas = [
            'total_stock' => 0,
            'alertas_bajo_stock' => 0
        ];

        // Total de productos en stock
        $resStock = mysqli_query($conexion, "SELECT SUM(stock_actual) as total FROM productos");
        if ($resStock) {
            $row = mysqli_fetch_assoc($resStock);
            $metricas['total_stock'] = isset($row['total']) ? intval($row['total']) : 0;
        }

        // Productos con stock igual o inferior al mínimo
        $resAlertas = mysqli_query($conexion, "SELECT COUNT(*) as alertas FROM productos WHERE stock_actual <= stock_minimo");
        if ($resAlertas) {
            $row = mysqli_fetch_assoc($resAlertas);
            $metricas['alertas_bajo_stock'] = isset($row['alertas']) ? intval($row['alertas']) : 0;
        }

        return $metricas;
    }
}
?>
