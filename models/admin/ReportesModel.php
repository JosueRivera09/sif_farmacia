<?php
/*
 * Archivo: models/admin/ReportesModel.php
 * Propósito: Proveer consultas para la generación de reportes contables y gráficos.
 */

class ReportesModel {

    private static function obtenerFiltroFecha($periodo, $fecha_inicio, $fecha_fin) {
        switch ($periodo) {
            case 'diario':
                return "DATE(t.fecha_creacion) = CURDATE()";
            case 'semanal':
                return "DATE(t.fecha_creacion) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'mensual':
                return "MONTH(t.fecha_creacion) = MONTH(CURDATE()) AND YEAR(t.fecha_creacion) = YEAR(CURDATE())";
            case 'personalizado':
                $inicio = mysqli_real_escape_string($GLOBALS['conexion_global'], $fecha_inicio);
                $fin = mysqli_real_escape_string($GLOBALS['conexion_global'], $fecha_fin);
                return "DATE(t.fecha_creacion) BETWEEN '$inicio' AND '$fin'";
            default:
                return "DATE(t.fecha_creacion) = CURDATE()";
        }
    }

    public static function obtenerDatosReporte(mysqli $conexion, $periodo, $fecha_inicio = null, $fecha_fin = null) {
        $GLOBALS['conexion_global'] = $conexion; // Para usar en escape
        $filtro = self::obtenerFiltroFecha($periodo, $fecha_inicio, $fecha_fin);

        // 1. Métricas generales
        $metricas = [
            'total_recaudado' => 0.00,
            'total_tickets' => 0,
            'ticket_promedio' => 0.00,
            'producto_estrella' => 'Ninguno'
        ];

        $qMetricas = "SELECT SUM(total) as total_recaudado, COUNT(id_ticket) as total_tickets, AVG(total) as ticket_promedio 
                      FROM tickets t 
                      WHERE t.estado = 'Pagado' AND $filtro";
        $resMetricas = mysqli_query($conexion, $qMetricas);
        if ($resMetricas && $row = mysqli_fetch_assoc($resMetricas)) {
            $metricas['total_recaudado'] = $row['total_recaudado'] ? floatval($row['total_recaudado']) : 0.00;
            $metricas['total_tickets'] = $row['total_tickets'] ? intval($row['total_tickets']) : 0;
            $metricas['ticket_promedio'] = $row['ticket_promedio'] ? floatval($row['ticket_promedio']) : 0.00;
        }

        // Producto estrella (más vendido por cantidad)
        $qEstrella = "SELECT p.nombre_commercial 
                      FROM ticket_detalles td 
                      JOIN productos p ON td.id_producto = p.id_producto 
                      JOIN tickets t ON td.id_ticket = t.id_ticket 
                      WHERE t.estado = 'Pagado' AND $filtro 
                      GROUP BY td.id_producto 
                      ORDER BY SUM(td.cantidad) DESC 
                      LIMIT 1";
        $resEstrella = mysqli_query($conexion, $qEstrella);
        if ($resEstrella && $row = mysqli_fetch_assoc($resEstrella)) {
            $metricas['producto_estrella'] = $row['nombre_commercial'];
        }

        // 2. Ventas por categoría
        $categorias = [];
        $qCategorias = "SELECT c.nombre_categoria, SUM(td.cantidad * td.precio_unitario) as total_monto
                        FROM ticket_detalles td
                        JOIN productos p ON td.id_producto = p.id_producto
                        JOIN categorias c ON p.id_categoria = c.id_categoria
                        JOIN tickets t ON td.id_ticket = t.id_ticket
                        WHERE t.estado = 'Pagado' AND $filtro
                        GROUP BY c.id_categoria
                        ORDER BY total_monto DESC";
        $resCategorias = mysqli_query($conexion, $qCategorias);
        if ($resCategorias) {
            while ($row = mysqli_fetch_assoc($resCategorias)) {
                $row['total_monto'] = floatval($row['total_monto']);
                $categorias[] = $row;
            }
        }

        // 3. Ventas por vendedor
        $vendedores = [];
        $qVendedores = "SELECT u.nombre_usuario as vendedor, SUM(t.total) as total_monto, COUNT(t.id_ticket) as total_tickets
                        FROM tickets t
                        JOIN usuarios u ON t.id_vendedor = u.id_usuario
                        WHERE t.estado = 'Pagado' AND $filtro
                        GROUP BY t.id_vendedor
                        ORDER BY total_monto DESC";
        $resVendedores = mysqli_query($conexion, $qVendedores);
        if ($resVendedores) {
            while ($row = mysqli_fetch_assoc($resVendedores)) {
                $row['total_monto'] = floatval($row['total_monto']);
                $row['total_tickets'] = intval($row['total_tickets']);
                $vendedores[] = $row;
            }
        }

        // 4. Detalle de transacciones
        $transacciones = [];
        $qTransacciones = "SELECT t.codigo_ticket, t.total, t.fecha_creacion, u.nombre_usuario as vendedor, c.nombre_completo as cliente
                           FROM tickets t
                           JOIN usuarios u ON t.id_vendedor = u.id_usuario
                           LEFT JOIN clientes c ON t.id_cliente = c.id_cliente
                           WHERE t.estado = 'Pagado' AND $filtro
                           ORDER BY t.fecha_creacion DESC";
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
