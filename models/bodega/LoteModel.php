<?php

function obtenerStockTotal($conexion) {
    $query = "SELECT SUM(cantidad_recibida) as total_stock FROM lotes";
    $resultado = mysqli_query($conexion, $query);
    $fila = mysqli_fetch_assoc($resultado);

    return isset($fila['total_stock']) ? intval($fila['total_stock']) : 0;
}

function obtenerLotesPorVencer($conexion) {
    $query = "SELECT COUNT(*) as total_vencer FROM lotes WHERE fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
    $resultado = mysqli_query($conexion, $query);
    $fila = mysqli_fetch_assoc($resultado);

    return isset($fila['total_vencer']) ? intval($fila['total_vencer']) : 0;
}

function contarLotes($conexion) {
    $query = "SELECT COUNT(*) as total_filas FROM lotes";
    $resultado = mysqli_query($conexion, $query);
    $fila = mysqli_fetch_assoc($resultado);

    return isset($fila['total_filas']) ? intval($fila['total_filas']) : 0;
}

function obtenerLotesPaginados($conexion, $limit, $offset) {
    $lotes = [];
    $query = "SELECT l.numero_lote, l.bodega, p.nombre_commercial, c.nombre_categoria, l.cantidad_recibida, l.fecha_vencimiento, l.fecha_creacion, p.unidad_medida, la.nombre_laboratorio
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

function obtenerProductosParaIngreso($conexion) {
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

function obtenerLaboratoriosParaIngreso($conexion) {
    $laboratorios = [];
    $query = "SELECT id_laboratorio, nombre_laboratorio FROM laboratorios ORDER BY nombre_laboratorio ASC";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $laboratorios[] = $row;
        }
    }

    return $laboratorios;
}

function contarBodegasActivas($conexion) {
    $query = "SELECT COUNT(DISTINCT bodega) as total_bodegas FROM lotes WHERE cantidad_recibida > 0 AND bodega IS NOT NULL AND bodega != ''";
    $resultado = mysqli_query($conexion, $query);
    $fila = mysqli_fetch_assoc($resultado);

    return isset($fila['total_bodegas']) ? intval($fila['total_bodegas']) : 0;
}

