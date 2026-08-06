<?php
/*
 * Archivo: models/Lote.php
 * Propósito: Modelo de datos para la tabla de lotes y métricas de bodega.
 * Qué muestra: No muestra nada. Provee métodos estáticos para interactuar con lotes y bodegas.
 */

class Lote {

    public static function obtenerStockTotal(mysqli $conexion) {
        $query = "SELECT SUM(cantidad_unidades_recibidas) as total_stock FROM lotes";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado && $fila = mysqli_fetch_assoc($resultado)) {
            return intval($fila['total_stock']);
        }
        return 0;
    }

    public static function obtenerLotesPorVencer(mysqli $conexion) {
        $query = "SELECT COUNT(*) as total_vencer FROM lotes WHERE fecha_vencimiento > CURDATE() AND fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado && $fila = mysqli_fetch_assoc($resultado)) {
            return intval($fila['total_vencer']);
        }
        return 0;
    }

    public static function contarLotes(mysqli $conexion) {
        $query = "SELECT COUNT(*) as total_filas FROM lotes";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado && $fila = mysqli_fetch_assoc($resultado)) {
            return intval($fila['total_filas']);
        }
        return 0;
    }

    public static function contarBodegasActivas(mysqli $conexion) {
        $query = "SELECT COUNT(DISTINCT bodega) as total_bodegas FROM lotes WHERE cantidad_unidades_recibidas > 0 AND bodega IS NOT NULL AND bodega != ''";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado && $fila = mysqli_fetch_assoc($resultado)) {
            return intval($fila['total_bodegas']);
        }
        return 0;
    }

    public static function obtenerLotesPaginados(mysqli $conexion, int $limit, int $offset) {
        $lotes = [];
        $query = "SELECT l.numero_lote, l.bodega, p.nombre_commercial, c.nombre_categoria, l.cantidad_unidades_recibidas, l.fecha_vencimiento, l.fecha_creacion, p.unidad_minima, la.nombre_laboratorio
                  FROM lotes l
                  JOIN productos p ON l.id_producto = p.id_producto
                  JOIN categorias c ON p.id_categoria = c.id_categoria
                  LEFT JOIN laboratorios la ON p.id_laboratorio = la.id_laboratorio
                  ORDER BY l.fecha_vencimiento ASC
                  LIMIT $limit OFFSET $offset";

        $resultado = mysqli_query($conexion, $query);
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $lotes[] = $row;
            }
        }
        return $lotes;
    }

    public static function obtenerTodosLosLotes(mysqli $conexion) {
        $lotes = [];
        $query = "SELECT l.numero_lote, l.bodega, p.nombre_commercial, c.nombre_categoria, l.cantidad_unidades_recibidas, l.fecha_vencimiento, l.fecha_creacion, p.unidad_minima, la.nombre_laboratorio
                  FROM lotes l
                  JOIN productos p ON l.id_producto = p.id_producto
                  JOIN categorias c ON p.id_categoria = c.id_categoria
                  LEFT JOIN laboratorios la ON p.id_laboratorio = la.id_laboratorio
                  ORDER BY l.fecha_vencimiento ASC";

        $resultado = mysqli_query($conexion, $query);
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $lotes[] = $row;
            }
        }
        return $lotes;
    }

    public static function obtenerLotesVencidos(mysqli $conexion) {
        $lotes = [];
        $query = "SELECT l.numero_lote, l.bodega, p.nombre_commercial, l.cantidad_unidades_recibidas, l.cantidad_unidades_recibidas AS cantidad_recibida, l.fecha_vencimiento, p.unidad_minima, p.unidad_minima AS unidad_medida
                  FROM lotes l
                  JOIN productos p ON l.id_producto = p.id_producto
                  WHERE l.fecha_vencimiento <= CURDATE()
                  ORDER BY l.fecha_vencimiento ASC";
        $res = mysqli_query($conexion, $query);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $lotes[] = $row;
            }
        }
        return $lotes;
    }

    public static function obtenerResumenReporte(mysqli $conexion) {
        $resumen = [
            'total_lotes' => 0,
            'total_vencidos' => 0,
            'total_por_vencer' => 0,
            'total_bajo_stock' => 0,
        ];

        $res = mysqli_query($conexion, "SELECT COUNT(*) as total FROM lotes");
        if ($res && $row = mysqli_fetch_assoc($res)) $resumen['total_lotes'] = intval($row['total']);

        $res = mysqli_query($conexion, "SELECT COUNT(*) as total FROM lotes WHERE fecha_vencimiento <= CURDATE()");
        if ($res && $row = mysqli_fetch_assoc($res)) $resumen['total_vencidos'] = intval($row['total']);

        $res = mysqli_query($conexion, "SELECT COUNT(*) as total FROM lotes WHERE fecha_vencimiento > CURDATE() AND fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
        if ($res && $row = mysqli_fetch_assoc($res)) $resumen['total_por_vencer'] = intval($row['total']);

        $res = mysqli_query($conexion, "SELECT COUNT(*) as total FROM productos WHERE stock_actual <= stock_minimo");
        if ($res && $row = mysqli_fetch_assoc($res)) $resumen['total_bajo_stock'] = intval($row['total']);

        return $resumen;
    }
}
?>
