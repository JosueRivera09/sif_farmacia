<?php
/*
 * Archivo: models/admin/DashboardModel.php
 * Propósito: Proveer datos y métricas para el panel principal del Administrador.
 */

class DashboardModel
{
    /**
     * Obtiene las métricas generales del día (recaudación, facturas, ingresos bodega, stock crítico)
     */
    public static function obtenerMetricasGenerales(mysqli $conexion)
    {
        $metricas = [
            'recaudacion_hoy' => 0.00,
            'facturas_hoy' => 0,
            'stock_critico' => 0,
            'ingresos_bodega_hoy' => 0
        ];


        // 1. Recaudación y Facturas Hoy
        $queryTickets = "SELECT SUM(total) as recaudacion, COUNT(id_ticket) as facturas 
                         FROM tickets 
                         WHERE estado = 'Pagado' AND DATE(fecha_creacion) = CURDATE()";
        $resTickets = mysqli_query($conexion, $queryTickets);
        if ($resTickets && $row = mysqli_fetch_assoc($resTickets)) {
            $metricas['recaudacion_hoy'] = $row['recaudacion'] ? floatval($row['recaudacion']) : 0.00;
            $metricas['facturas_hoy'] = $row['facturas'] ? intval($row['facturas']) : 0;
        }

        // 2. Stock Crítico
        $queryStock = "SELECT COUNT(id_producto) as critico 
                       FROM productos 
                       WHERE stock_actual <= stock_minimo";
        $resStock = mysqli_query($conexion, $queryStock);
        if ($resStock && $row = mysqli_fetch_assoc($resStock)) {
            $metricas['stock_critico'] = intval($row['critico']);
        }

        // 3. Ingresos Bodega Hoy
        $queryBodega = "SELECT COUNT(id_lote) as lotes 
                        FROM lotes 
                        WHERE DATE(fecha_creacion) = CURDATE()";
        $resBodega = mysqli_query($conexion, $queryBodega);
        if ($resBodega && $row = mysqli_fetch_assoc($resBodega)) {
            $metricas['ingresos_bodega_hoy'] = intval($row['lotes']);
        }

        return $metricas;
    }

    /**
     * Obtiene el historial de ventas pagadas con filtro de período y paginación
     */
    public static function obtenerHistorialVentas(mysqli $conexion, string $filtro = 'todos', int $offset = 0, int $limit = 10)
    {
        $ventas = [];
        $whereFecha = "";

        switch ($filtro) {
            case 'dia':
                $whereFecha = " AND DATE(t.fecha_creacion) = CURDATE()";
                break;
            case 'semana':
                $whereFecha = " AND YEARWEEK(t.fecha_creacion, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'mes':
                $whereFecha = " AND YEAR(t.fecha_creacion) = YEAR(CURDATE()) AND MONTH(t.fecha_creacion) = MONTH(CURDATE())";
                break;
            case 'anio':
                $whereFecha = " AND YEAR(t.fecha_creacion) = YEAR(CURDATE())";
                break;
            case 'todos':
            default:
                $whereFecha = "";
                break;
        }

        $offset = max(0, intval($offset));
        $limit = max(1, intval($limit));

        $query = "SELECT t.codigo_ticket, t.total, t.fecha_creacion, c.nombre_completo as cliente
                  FROM tickets t
                  LEFT JOIN clientes c ON t.id_cliente = c.id_cliente
                  WHERE t.estado = 'Pagado' $whereFecha
                  ORDER BY t.id_ticket DESC
                  LIMIT $offset, $limit";
        $res = mysqli_query($conexion, $query);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $ventas[] = $row;
            }
        }
        return $ventas;
    }

    /**
     * Obtiene la lista de usuarios activos en el sistema
     */
    public static function obtenerUsuarios(mysqli $conexion)
    {
        $usuarios = [];
        $query = "SELECT nombre_usuario, rol, fecha_creacion FROM usuarios ORDER BY rol, nombre_usuario";
        $res = mysqli_query($conexion, $query);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
    }

    /**
     * Obtiene las alertas del sistema (productos bajo stock, vencidos y por vencer)
     */
    public static function obtenerAlertasStock(mysqli $conexion)
    {
        $alertas = [];

        // 1. Productos Agotados (0 Stock)
        $queryAgotados = "SELECT nombre_commercial, stock_actual, unidad_minima 
                          FROM productos 
                          WHERE stock_actual <= 0 
                          ORDER BY nombre_commercial ASC 
                          LIMIT 10";
        $resA = mysqli_query($conexion, $queryAgotados);
        if ($resA) {
            while ($row = mysqli_fetch_assoc($resA)) {
                $alertas[] = [
                    'nombre_commercial' => $row['nombre_commercial'],
                    'detalle' => 'Sin existencias (0 ' . $row['unidad_minima'] . 's en inventario)',
                    'etiqueta' => 'Agotado',
                    'tipo' => 'danger'
                ];
            }
        }

        // 2. Productos Próximos a Agotarse (Stock mayor a 0 pero menor o igual al mínimo o a 10 unidades)
        $queryStock = "SELECT nombre_commercial, stock_actual, stock_minimo, unidad_minima 
                       FROM productos 
                       WHERE stock_actual > 0 AND (stock_actual <= stock_minimo OR (stock_minimo = 0 AND stock_actual <= 10)) 
                       ORDER BY stock_actual ASC 
                       LIMIT 10";
        $resS = mysqli_query($conexion, $queryStock);
        if ($resS) {
            while ($row = mysqli_fetch_assoc($resS)) {
                $alertas[] = [
                    'nombre_commercial' => $row['nombre_commercial'],
                    'detalle' => 'Quedan solo ' . intval($row['stock_actual']) . ' ' . $row['unidad_minima'] . 's' . (intval($row['stock_minimo']) > 0 ? ' (Mín: ' . intval($row['stock_minimo']) . ')' : ''),
                    'etiqueta' => 'Próximo a Agotarse',
                    'tipo' => 'warning'
                ];
            }
        }

        // 3. Lotes Vencidos (con unidades disponibles)
        $queryVencidos = "SELECT p.nombre_commercial, l.numero_lote, l.fecha_vencimiento, l.cantidad_unidades_recibidas, p.unidad_minima
                          FROM lotes l
                          JOIN productos p ON l.id_producto = p.id_producto
                          WHERE l.fecha_vencimiento <= CURDATE() AND l.cantidad_unidades_recibidas > 0
                          ORDER BY l.fecha_vencimiento ASC
                          LIMIT 10";
        $resV = mysqli_query($conexion, $queryVencidos);
        if ($resV) {
            while ($row = mysqli_fetch_assoc($resV)) {
                $alertas[] = [
                    'nombre_commercial' => $row['nombre_commercial'],
                    'detalle' => 'Lote ' . $row['numero_lote'] . ' (' . intval($row['cantidad_unidades_recibidas']) . ' ' . $row['unidad_minima'] . 's) - Vencido ' . date('d/m/Y', strtotime($row['fecha_vencimiento'])),
                    'etiqueta' => 'Vencido',
                    'tipo' => 'danger'
                ];
            }
        }

        // 4. Lotes Próximos a Vencer (< 30 días con unidades disponibles)
        $queryPorVencer = "SELECT p.nombre_commercial, l.numero_lote, l.fecha_vencimiento, l.cantidad_unidades_recibidas, p.unidad_minima
                           FROM lotes l
                           JOIN productos p ON l.id_producto = p.id_producto
                           WHERE l.fecha_vencimiento > CURDATE() AND l.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND l.cantidad_unidades_recibidas > 0
                           ORDER BY l.fecha_vencimiento ASC
                           LIMIT 10";
        $resPV = mysqli_query($conexion, $queryPorVencer);
        if ($resPV) {
            while ($row = mysqli_fetch_assoc($resPV)) {
                $alertas[] = [
                    'nombre_commercial' => $row['nombre_commercial'],
                    'detalle' => 'Lote ' . $row['numero_lote'] . ' (' . intval($row['cantidad_unidades_recibidas']) . ' ' . $row['unidad_minima'] . 's) - Vence el ' . date('d/m/Y', strtotime($row['fecha_vencimiento'])),
                    'etiqueta' => 'Próximo a Vencer',
                    'tipo' => 'warning'
                ];
            }
        }

        return $alertas;
    }
}
