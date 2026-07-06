<?php

function obtenerCategorias($conexion) {
    $categorias = [];
    $query = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $categorias[] = $row;
        }
    }

    return $categorias;
}
