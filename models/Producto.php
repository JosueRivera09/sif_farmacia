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
        $query = "SELECT p.id_producto, p.nombre_commercial, p.codigo_barras, p.stock_actual, p.precio_venta_actual, 
                         p.requiere_receta, p.miligramos, p.unidad_medida, c.nombre_categoria, l.nombre_laboratorio 
                  FROM productos p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                  LEFT JOIN laboratorios l ON p.id_laboratorio = l.id_laboratorio 
                  ORDER BY p.nombre_commercial ASC";
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $row['id_producto'] = intval($row['id_producto']);
                $row['stock_actual'] = intval($row['stock_actual']);
                $row['precio_venta_actual'] = floatval($row['precio_venta_actual']);
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
        $query = "SELECT p.id_producto, p.codigo_barras, p.nombre_commercial, p.stock_actual, p.stock_minimo, p.unidad_medida
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
