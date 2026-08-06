<?php
/*
 * Archivo: models/Producto.php
 * Propósito: Modelo de datos para la tabla de productos.
 * Qué muestra: No muestra nada. Provee métodos estáticos para interactuar con productos.
 */

class Producto {

    /**
     * Obtiene los productos optimizados para el proceso de venta.
     */
    public static function obtenerProductosVenta(mysqli $conexion) {
        $productos = [];
        $query = "SELECT p.id_producto, p.nombre_commercial, p.codigo_barras, p.stock_actual,
                         p.empaque_principal, p.empaque_medio, p.unidad_minima, p.stock_minimo,
                         p.unidades_por_empaque_medio, p.unidades_totales_por_empaque_principal, p.es_fraccionable,
                         p.precio_empaque_principal, p.precio_empaque_medio, p.precio_unidad_minima,
                         p.requiere_receta, p.miligramos, c.nombre_categoria, l.nombre_laboratorio 
                  FROM productos p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                  LEFT JOIN laboratorios l ON p.id_laboratorio = l.id_laboratorio 
                  WHERE p.id_producto NOT IN (
                      SELECT id_producto 
                      FROM lotes 
                      GROUP BY id_producto 
                      HAVING MAX(fecha_vencimiento) <= CURDATE()
                  )
                  ORDER BY p.nombre_commercial ASC";
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $row['id_producto'] = intval($row['id_producto']);
                $row['stock_actual'] = intval($row['stock_actual']);
                $row['precio_empaque_principal'] = floatval($row['precio_empaque_principal']);
                $row['precio_empaque_medio'] = $row['precio_empaque_medio'] !== null ? floatval($row['precio_empaque_medio']) : null;
                $row['precio_unidad_minima'] = floatval($row['precio_unidad_minima']);
                $row['unidades_por_empaque_medio'] = intval($row['unidades_por_empaque_medio']);
                $row['unidades_totales_por_empaque_principal'] = intval($row['unidades_totales_por_empaque_principal']);
                $row['es_fraccionable'] = ($row['es_fraccionable'] == 1 || $row['es_fraccionable'] == "\x01");
                $row['requiere_receta'] = ($row['requiere_receta'] == 1 || $row['requiere_receta'] == "\x01");
                $productos[] = $row;
            }
        }
        return $productos;
    }

    /**
     * Actualiza (reduce) el stock de un producto y sus lotes FIFO tras una venta.
     */
    public static function actualizarStockProducto(mysqli $conexion, int $id_producto, int $cantidad) {
        $id_producto = intval($id_producto);
        $cantidad = intval($cantidad);
        if ($cantidad <= 0) return true;

        // 1. Reducir stock global del producto
        $query = "UPDATE productos SET stock_actual = stock_actual - $cantidad WHERE id_producto = $id_producto";
        $ok = mysqli_query($conexion, $query);

        // 2. Reducir stock FIFO de los lotes activos (vencimiento más cercano primero)
        $unidades_pendientes = $cantidad;
        $queryLotes = "SELECT id_lote, cantidad_unidades_recibidas 
                       FROM lotes 
                       WHERE id_producto = $id_producto AND cantidad_unidades_recibidas > 0 
                       ORDER BY fecha_vencimiento ASC, id_lote ASC";
        $resLotes = mysqli_query($conexion, $queryLotes);
        if ($resLotes) {
            while ($lote = mysqli_fetch_assoc($resLotes)) {
                if ($unidades_pendientes <= 0) break;

                $id_lote = intval($lote['id_lote']);
                $cantLote = intval($lote['cantidad_unidades_recibidas']);

                if ($cantLote <= $unidades_pendientes) {
                    $restar = $cantLote;
                    $unidades_pendientes -= $cantLote;
                } else {
                    $restar = $unidades_pendientes;
                    $unidades_pendientes = 0;
                }

                mysqli_query($conexion, "UPDATE lotes SET cantidad_unidades_recibidas = cantidad_unidades_recibidas - $restar WHERE id_lote = $id_lote");
            }
        }

        return $ok;
    }

    /**
     * Restablece (suma) el stock de un producto y de sus lotes tras cancelar o borrar un ticket.
     */
    public static function restablecerStockProducto(mysqli $conexion, int $id_producto, int $cantidad) {
        $id_producto = intval($id_producto);
        $cantidad = intval($cantidad);
        if ($cantidad <= 0) return true;

        // 1. Sumar de vuelta en la tabla productos
        $query = "UPDATE productos SET stock_actual = stock_actual + $cantidad WHERE id_producto = $id_producto";
        $ok = mysqli_query($conexion, $query);

        // 2. Devolver unidades al lote activo más reciente
        $queryLote = "SELECT id_lote FROM lotes WHERE id_producto = $id_producto ORDER BY fecha_vencimiento DESC, id_lote DESC LIMIT 1";
        $resLote = mysqli_query($conexion, $queryLote);
        if ($resLote && mysqli_num_rows($resLote) > 0) {
            $loteRow = mysqli_fetch_assoc($resLote);
            $id_lote = intval($loteRow['id_lote']);
            mysqli_query($conexion, "UPDATE lotes SET cantidad_unidades_recibidas = cantidad_unidades_recibidas + $cantidad WHERE id_lote = $id_lote");
        }

        return $ok;
    }

    /**
     * Obtiene productos cuyo stock es igual o inferior al stock mínimo.
     */
    public static function obtenerProductosBajoStock(mysqli $conexion) {
        $productos = [];
        $query = "SELECT p.id_producto, p.codigo_barras, p.nombre_commercial, p.stock_actual, p.stock_minimo, p.unidad_minima, p.unidad_minima AS unidad_medida
                  FROM productos p
                  WHERE p.stock_actual <= p.stock_minimo
                  ORDER BY p.stock_actual ASC";
        $res = mysqli_query($conexion, $query);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    /**
     * Obtiene los productos básicos (ID, nombre, código de barras) para ingreso de lotes.
     */
    public static function obtenerProductosParaIngreso(mysqli $conexion) {
        $productos = [];
        $query = "SELECT id_producto, nombre_commercial, codigo_barras FROM productos ORDER BY nombre_commercial ASC";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $productos[] = $row;
            }
        }
        return $productos;
    }
}
?>
