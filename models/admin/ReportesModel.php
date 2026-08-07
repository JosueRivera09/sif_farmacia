<?php
/*
 * Archivo: models/admin/ReportesModel.php
 * Propósito: Proveer consultas para la generación de reportes contables y gráficos utilizando las vistas ventas y detalle_ventas.
 */

class ReportesModel {

    private static function obtenerFiltroFecha($periodo, $fecha_inicio, $fecha_fin) {
        switch ($periodo) {
            case 'diario':
                return "DATE(v.fecha_venta) = CURDATE()";
            case 'semanal':
                return "DATE(v.fecha_venta) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'mensual':
                return "MONTH(v.fecha_venta) = MONTH(CURDATE()) AND YEAR(v.fecha_venta) = YEAR(CURDATE())";
            case 'personalizado':
                $inicio = mysqli_real_escape_string($GLOBALS['conexion_global'], $fecha_inicio);
                $fin = mysqli_real_escape_string($GLOBALS['conexion_global'], $fecha_fin);
                return "DATE(v.fecha_venta) BETWEEN '$inicio' AND '$fin'";
            default:
                return "DATE(v.fecha_venta) = CURDATE()";
        }
    }

    public static function obtenerDatosReporte(mysqli $conexion, $periodo, $fecha_inicio = null, $fecha_fin = null) {
        $GLOBALS['conexion_global'] = $conexion; // Para usar en escape
        $filtro = self::obtenerFiltroFecha($periodo, $fecha_inicio, $fecha_fin);

        // 1. Métricas generales desde la vista 'ventas'
        $metricas = [
            'total_recaudado' => 0.00,
            'total_tickets' => 0,
            'ticket_promedio' => 0.00,
            'producto_estrella' => 'Ninguno'
        ];

        $qMetricas = "SELECT SUM(total_neto) as total_recaudado, COUNT(id_venta) as total_tickets, AVG(total_neto) as ticket_promedio 
                      FROM ventas v 
                      WHERE v.estado_pago = 'Pagado' AND $filtro";
        $resMetricas = mysqli_query($conexion, $qMetricas);
        if ($resMetricas && $row = mysqli_fetch_assoc($resMetricas)) {
            $metricas['total_recaudado'] = $row['total_recaudado'] ? floatval($row['total_recaudado']) : 0.00;
            $metricas['total_tickets'] = $row['total_tickets'] ? intval($row['total_tickets']) : 0;
            $metricas['ticket_promedio'] = $row['ticket_promedio'] ? floatval($row['ticket_promedio']) : 0.00;
        }

        // Producto estrella desde las vistas 'detalle_ventas' y 'ventas'
        $qEstrella = "SELECT p.nombre_commercial 
                      FROM detalle_ventas dv 
                      JOIN productos p ON dv.id_producto = p.id_producto 
                      JOIN ventas v ON dv.id_venta = v.id_venta 
                      WHERE v.estado_pago = 'Pagado' AND $filtro 
                      GROUP BY dv.id_producto 
                      ORDER BY SUM(dv.cantidad) DESC 
                      LIMIT 1";
        $resEstrella = mysqli_query($conexion, $qEstrella);
        if ($resEstrella && $row = mysqli_fetch_assoc($resEstrella)) {
            $metricas['producto_estrella'] = $row['nombre_commercial'];
        }

        // 2. Ventas por categoría utilizando 'detalle_ventas' y 'ventas'
        $categorias = [];
        $qCategorias = "SELECT c.nombre_categoria, SUM(dv.cantidad * dv.precio_unitario) as total_monto
                        FROM detalle_ventas dv
                        JOIN productos p ON dv.id_producto = p.id_producto
                        JOIN categorias c ON p.id_categoria = c.id_categoria
                        JOIN ventas v ON dv.id_venta = v.id_venta
                        WHERE v.estado_pago = 'Pagado' AND $filtro
                        GROUP BY c.id_categoria
                        ORDER BY total_monto DESC";
        $resCategorias = mysqli_query($conexion, $qCategorias);
        if ($resCategorias) {
            while ($row = mysqli_fetch_assoc($resCategorias)) {
                $row['total_monto'] = floatval($row['total_monto']);
                $categorias[] = $row;
            }
        }

        // 3. Ventas por vendedor utilizando 'ventas'
        $vendedores = [];
        $qVendedores = "SELECT u.nombre_usuario as vendedor, SUM(v.total_neto) as total_monto, COUNT(v.id_venta) as total_tickets
                        FROM ventas v
                        JOIN usuarios u ON v.id_usuario = u.id_usuario
                        WHERE v.estado_pago = 'Pagado' AND $filtro
                        GROUP BY v.id_usuario
                        ORDER BY total_monto DESC";
        $resVendedores = mysqli_query($conexion, $qVendedores);
        if ($resVendedores) {
            while ($row = mysqli_fetch_assoc($resVendedores)) {
                $row['total_monto'] = floatval($row['total_monto']);
                $row['total_tickets'] = intval($row['total_tickets']);
                $vendedores[] = $row;
            }
        }

        // 4. Detalle de transacciones utilizando 'ventas'
        $transacciones = [];
        $qTransacciones = "SELECT t.codigo_ticket, v.total_neto as total, v.fecha_venta as fecha_creacion, u.nombre_usuario as vendedor, c.nombre_completo as cliente
                           FROM ventas v
                           JOIN tickets t ON v.id_venta = t.id_ticket
                           JOIN usuarios u ON v.id_usuario = u.id_usuario
                           LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                           WHERE v.estado_pago = 'Pagado' AND $filtro
                           ORDER BY v.fecha_venta DESC";
        $resTransacciones = mysqli_query($conexion, $qTransacciones);
        if ($resTransacciones) {
            while ($row = mysqli_fetch_assoc($resTransacciones)) {
                $row['total'] = floatval($row['total']);
                $transacciones[] = $row;
            }
        }

        return [
            'metricas' => $metricas,
            'categorias' => $categorias,
            'vendedores' => $vendedores,
            'transacciones' => $transacciones
        ];
    }
}
?>
