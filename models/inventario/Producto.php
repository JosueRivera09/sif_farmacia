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
     * Obtiene todos los campos de un producto por su ID.
     */
    public static function obtenerProductoPorId(mysqli $conexion, int $id_producto) {
        $id = intval($id_producto);
        if ($id <= 0) return null;

        $query = "SELECT * FROM productos WHERE id_producto = $id LIMIT 1";
        $res = mysqli_query($conexion, $query);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $row['id_producto'] = intval($row['id_producto']);
            $row['id_categoria'] = intval($row['id_categoria']);
            $row['id_laboratorio'] = intval($row['id_laboratorio']);
            $row['stock_actual'] = intval($row['stock_actual']);
            $row['stock_minimo'] = intval($row['stock_minimo']);
            $row['miligramos'] = $row['miligramos'] !== null ? intval($row['miligramos']) : null;
            $row['unidades_por_empaque_medio'] = intval($row['unidades_por_empaque_medio']);
            $row['unidades_totales_por_empaque_principal'] = intval($row['unidades_totales_por_empaque_principal']);
            $row['precio_empaque_principal'] = floatval($row['precio_empaque_principal']);
            $row['precio_empaque_medio'] = $row['precio_empaque_medio'] !== null ? floatval($row['precio_empaque_medio']) : null;
            $row['precio_unidad_minima'] = floatval($row['precio_unidad_minima']);
            $row['es_fraccionable'] = ($row['es_fraccionable'] == 1 || $row['es_fraccionable'] == "\x01") ? 1 : 0;
            $row['requiere_receta'] = ($row['requiere_receta'] == 1 || $row['requiere_receta'] == "\x01") ? 1 : 0;
            return $row;
        }
        return null;
    }

    /**
    /**
     * Recalcula y sincroniza exactamente el stock_actual de un producto basándose en la suma real de sus lotes disponibles.
     */
    public static function sincronizarStockProducto(mysqli $conexion, int $id_producto) {
        $id_producto = intval($id_producto);
        if ($id_producto <= 0) return false;
        
        $query = "UPDATE productos p
                  SET p.stock_actual = IFNULL((
                      SELECT SUM(l.cantidad_unidades_recibidas) 
                      FROM lotes l 
                      WHERE l.id_producto = $id_producto AND l.cantidad_unidades_recibidas > 0
                  ), 0)
                  WHERE p.id_producto = $id_producto";
        return mysqli_query($conexion, $query);
    }

    /**
     * Actualiza (reduce) el stock de un producto y sus lotes FIFO tras una venta.
     */
    public static function actualizarStockProducto(mysqli $conexion, int $id_producto, int $cantidad) {
        $id_producto = intval($id_producto);
        $cantidad = intval($cantidad);
        if ($cantidad <= 0) return true;

        // 1. Reducir stock FIFO de los lotes activos (vencimiento más cercano primero)
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

        // 2. Sincronizar el stock_actual del producto con el total de sus lotes
        return self::sincronizarStockProducto($conexion, $id_producto);
    }

    /**
     * Restablece (suma) el stock de un producto y de sus lotes tras cancelar o borrar un ticket.
     */
    public static function restablecerStockProducto(mysqli $conexion, int $id_producto, int $cantidad) {
        $id_producto = intval($id_producto);
        $cantidad = intval($cantidad);
        if ($cantidad <= 0) return true;

        // 1. Devolver unidades al lote activo más reciente
        $queryLote = "SELECT id_lote FROM lotes WHERE id_producto = $id_producto ORDER BY fecha_vencimiento DESC, id_lote DESC LIMIT 1";
        $resLote = mysqli_query($conexion, $queryLote);
        if ($resLote && mysqli_num_rows($resLote) > 0) {
            $loteRow = mysqli_fetch_assoc($resLote);
            $id_lote = intval($loteRow['id_lote']);
            mysqli_query($conexion, "UPDATE lotes SET cantidad_unidades_recibidas = cantidad_unidades_recibidas + $cantidad WHERE id_lote = $id_lote");
        }

        // 2. Sincronizar el stock_actual del producto con el total de sus lotes
        return self::sincronizarStockProducto($conexion, $id_producto);
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
    public static function editarProducto($conexion, $id_producto, $datos) {
        $id = intval($id_producto);
        $cod = mysqli_real_escape_string($conexion, $datos['codigo_barras']);
        $nom = mysqli_real_escape_string($conexion, $datos['nombre_commercial']);
        $desc = mysqli_real_escape_string($conexion, $datos['descripcion']);
        $desc_val = $desc === '' ? 'NULL' : "'$desc'";
        $id_cat = intval($datos['id_categoria']);
        $id_lab = intval($datos['id_laboratorio']);
        $mg = $datos['miligramos'];
        $mg_val = ($mg === '' || $mg === 'NULL') ? 'NULL' : intval($mg);
        $emp_prin = mysqli_real_escape_string($conexion, $datos['empaque_principal']);
        $emp_med = $datos['empaque_medio'];
        $emp_med_val = $emp_med ? "'" . mysqli_real_escape_string($conexion, $emp_med) . "'" : 'NULL';
        $uni_min = mysqli_real_escape_string($conexion, $datos['unidad_minima']);
        
        $u_emp_med = intval($datos['unidades_por_empaque_medio']);
        $u_tot_emp_prin = intval($datos['unidades_totales_por_empaque_principal']);
        $fracc = intval($datos['es_fraccionable']);
        
        $precio_prin = floatval($datos['precio_empaque_principal']);
        $precio_med = $datos['precio_empaque_medio'];
        $precio_med_val = ($precio_med !== null && $precio_med !== '') ? floatval($precio_med) : 'NULL';
        $precio_min = floatval($datos['precio_unidad_minima']);
        
        $receta = intval($datos['requiere_receta']);
        $stock_min = intval($datos['stock_minimo']);
        $tipo = mysqli_real_escape_string($conexion, $datos['tipo_producto']);

        $query = "UPDATE productos SET 
                    codigo_barras = '$cod', nombre_commercial = '$nom', descripcion = $desc_val,
                    id_categoria = $id_cat, id_laboratorio = $id_lab, miligramos = $mg_val,
                    empaque_principal = '$emp_prin', empaque_medio = $emp_med_val, unidad_minima = '$uni_min',
                    unidades_por_empaque_medio = $u_emp_med, unidades_totales_por_empaque_principal = $u_tot_emp_prin,
                    es_fraccionable = $fracc, precio_empaque_principal = $precio_prin, precio_empaque_medio = $precio_med_val,
                    precio_unidad_minima = $precio_min, requiere_receta = $receta, stock_minimo = $stock_min,
                    tipo_producto = '$tipo'
                  WHERE id_producto = $id";
                  
        return mysqli_query($conexion, $query);
    }

    public static function crearProducto($conexion, $datos) {
        $cod = mysqli_real_escape_string($conexion, $datos['codigo_barras']);
        $nom = mysqli_real_escape_string($conexion, $datos['nombre_commercial']);
        $desc = mysqli_real_escape_string($conexion, $datos['descripcion']);
        $desc_val = $desc === '' ? 'NULL' : "'$desc'";
        $id_cat = intval($datos['id_categoria']);
        $id_lab = intval($datos['id_laboratorio']);
        $mg = $datos['miligramos'];
        $mg_val = ($mg === '' || $mg === 'NULL') ? 'NULL' : intval($mg);
        $emp_prin = mysqli_real_escape_string($conexion, $datos['empaque_principal']);
        $emp_med = $datos['empaque_medio'];
        $emp_med_val = $emp_med ? "'" . mysqli_real_escape_string($conexion, $emp_med) . "'" : 'NULL';
        $uni_min = mysqli_real_escape_string($conexion, $datos['unidad_minima']);
        
        $u_emp_med = intval($datos['unidades_por_empaque_medio']);
        $u_tot_emp_prin = intval($datos['unidades_totales_por_empaque_principal']);
        $fracc = intval($datos['es_fraccionable']);
        
        $precio_prin = floatval($datos['precio_empaque_principal']);
        $precio_med = $datos['precio_empaque_medio'];
        $precio_med_val = ($precio_med !== null && $precio_med !== '') ? floatval($precio_med) : 'NULL';
        $precio_min = floatval($datos['precio_unidad_minima']);
        
        $receta = intval($datos['requiere_receta']);
        $stock_min = intval($datos['stock_minimo']);
        $tipo = mysqli_real_escape_string($conexion, $datos['tipo_producto']);

        $query = "INSERT INTO productos (
                    codigo_barras, nombre_commercial, descripcion, id_categoria, id_laboratorio,
                    miligramos, empaque_principal, empaque_medio, unidad_minima, unidades_por_empaque_medio,
                    unidades_totales_por_empaque_principal, es_fraccionable, precio_empaque_principal,
                    precio_empaque_medio, precio_unidad_minima, requiere_receta, stock_minimo,
                    stock_actual, tipo_producto
                  ) VALUES (
                    '$cod', '$nom', $desc_val, $id_cat, $id_lab,
                    $mg_val, '$emp_prin', $emp_med_val, '$uni_min', $u_emp_med,
                    $u_tot_emp_prin, $fracc, $precio_prin,
                    $precio_med_val, $precio_min, $receta, $stock_min,
                    0, '$tipo'
                  )";
        
        if (mysqli_query($conexion, $query)) {
            return mysqli_insert_id($conexion);
        }
        return false;
    }

}
?>