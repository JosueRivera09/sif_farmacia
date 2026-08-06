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
        $query = "SELECT p.id_producto, p.nombre_commercial, p.codigo_barras, 
                         COALESCE(
                             (SELECT SUM(l_valid.cantidad_unidades_recibidas) 
                              FROM lotes l_valid 
                              WHERE l_valid.id_producto = p.id_producto 
                                AND l_valid.fecha_vencimiento > CURDATE()), 
                             p.stock_actual
                         ) AS stock_actual, 
                         p.empaque_principal, p.empaque_medio, p.unidad_minima,
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
     * Actualiza (reduce) el stock de un producto tras una venta.
     */
    public static function actualizarStockProducto(mysqli $conexion, int $id_producto, int $cantidad) {
        $id_producto = intval($id_producto);
        $cantidad = intval($cantidad);
        $query = "UPDATE productos SET stock_actual = stock_actual - $cantidad WHERE id_producto = $id_producto AND stock_actual >= $cantidad";
        return mysqli_query($conexion, $query) && mysqli_affected_rows($conexion) > 0;
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
