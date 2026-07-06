<?php

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/bodega/CategoriaModel.php';
require_once __DIR__ . '/../../models/bodega/LoteModel.php';

function obtenerDatosBodega($conexion) {
    $lotes_por_pagina = 5;
    $pagina_actual = isset($_GET['page']) ? intval($_GET['page']) : 1;
    if ($pagina_actual < 1) {
        $pagina_actual = 1;
    }

    $total_lotes = contarLotes($conexion);
    $total_paginas = max(1, (int) ceil($total_lotes / $lotes_por_pagina));

    if ($pagina_actual > $total_paginas) {
        $pagina_actual = $total_paginas;
    }

    $offset = ($pagina_actual - 1) * $lotes_por_pagina;

    return [
        'total_stock' => number_format(obtenerStockTotal($conexion)),
        'lotes_por_vencer' => obtenerLotesPorVencer($conexion),
        'pagina_actual' => $pagina_actual,
        'total_paginas' => $total_paginas,
        'offset' => $offset,
        'lotes_por_pagina' => $lotes_por_pagina,
        'total_lotes' => $total_lotes,
        'lotes_list' => obtenerLotesPaginados($conexion, $lotes_por_pagina, $offset),
        'products_list' => obtenerProductosParaIngreso($conexion),
        'laboratories_list' => obtenerLaboratoriosParaIngreso($conexion),
        'categories_list' => obtenerCategorias($conexion),
    ];
}
