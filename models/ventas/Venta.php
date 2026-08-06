<?php
/*
 * Archivo: models/Venta.php
 * Propósito: Modelo de datos para las métricas y operaciones de ventas de los vendedores.
 * Qué muestra: No muestra nada. Provee métodos estáticos.
 */

class Venta {
    public static function obtenerMetricasVendedor(mysqli $conexion, int $id_vendedor = 0) {
        $metricas = [
            'total_stock' => 0,
            'alertas_bajo_stock' => 0,
            'ventas_dia' => 0,
            'monto_dia' => 0.00
        ];

        // Total de productos en stock
        $resStock = mysqli_query($conexion, "SELECT SUM(stock_actual) as total FROM productos");
        if ($resStock && $row = mysqli_fetch_assoc($resStock)) {
            $metricas['total_stock'] = isset($row['total']) ? intval($row['total']) : 0;
        }

        // Productos con stock igual o inferior al mínimo
        $resAlertas = mysqli_query($conexion, "SELECT COUNT(*) as alertas FROM productos WHERE stock_actual <= stock_minimo");
        if ($resAlertas && $row = mysqli_fetch_assoc($resAlertas)) {
            $metricas['alertas_bajo_stock'] = isset($row['alertas']) ? intval($row['alertas']) : 0;
        }

        // Ventas del día y Monto Total Facturado Hoy (CURDATE())
        $id_vendedor = intval($id_vendedor);
        $whereUser = $id_vendedor > 0 ? " AND id_vendedor = $id_vendedor" : "";
        $queryDia = "SELECT COUNT(*) as cant, SUM(total) as monto 
                     FROM tickets 
                     WHERE DATE(fecha_creacion) = CURDATE() $whereUser";
        $resDia = mysqli_query($conexion, $queryDia);
        if ($resDia && $rowDia = mysqli_fetch_assoc($resDia)) {
            $metricas['ventas_dia'] = isset($rowDia['cant']) ? intval($rowDia['cant']) : 0;
            $metricas['monto_dia'] = isset($rowDia['monto']) ? floatval($rowDia['monto']) : 0.00;
        }

        return $metricas;
    }
}
?>
