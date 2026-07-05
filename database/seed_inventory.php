<?php
require_once __DIR__ . '/../config/conexion.php';

// 1. Sembrar Categorías
$queryCat = "SELECT COUNT(*) as total FROM categorias";
$resCat = mysqli_query($conexion, $queryCat);
$rowCat = mysqli_fetch_assoc($resCat);

if ($rowCat['total'] == 0) {
    mysqli_query($conexion, "INSERT INTO categorias (id_categoria, nombre_categoria) VALUES (1, 'Analgésicos'), (2, 'Antibióticos')");
    echo "Categorías de prueba sembradas.\n";
}

// 2. Sembrar Laboratorios
$queryLab = "SELECT COUNT(*) as total FROM laboratorios";
$resLab = mysqli_query($conexion, $queryLab);
$rowLab = mysqli_fetch_assoc($resLab);

if ($rowLab['total'] == 0) {
    mysqli_query($conexion, "INSERT INTO laboratorios (id_laboratorio, nombre_laboratorio) VALUES (1, 'Bayer'), (2, 'Pfizer')");
    echo "Laboratorios de prueba sembrados.\n";
}

// 3. Sembrar Productos
$queryProd = "SELECT COUNT(*) as total FROM productos";
$resProd = mysqli_query($conexion, $queryProd);
$rowProd = mysqli_fetch_assoc($resProd);

if ($rowProd['total'] == 0) {
    $insertProd1 = "INSERT INTO productos (id_producto, codigo_barras, nombre_commercial, descripcion, id_categoria, id_laboratorio, miligramos, unidad_medida, precio_venta_actual, stock_actual) 
                    VALUES (1, '7701234567890', 'Paracetamol 500mg', 'Alivio del dolor y la fiebre', 1, 1, 500, 'Cajas', 5.00, 500)";
    $insertProd2 = "INSERT INTO productos (id_producto, codigo_barras, nombre_commercial, descripcion, id_categoria, id_laboratorio, miligramos, unidad_medida, precio_venta_actual, stock_actual) 
                    VALUES (2, '7709876543210', 'Amoxicilina 500mg', 'Antibiótico bactericida', 2, 2, 500, 'Frascos', 12.00, 300)";
    
    mysqli_query($conexion, $insertProd1);
    mysqli_query($conexion, $insertProd2);
    echo "Productos de prueba sembrados.\n";
}

// 4. Sembrar Lotes
$queryLotes = "SELECT COUNT(*) as total FROM lotes";
$resLotes = mysqli_query($conexion, $queryLotes);
$rowLotes = mysqli_fetch_assoc($resLotes);

if ($rowLotes['total'] == 0) {
    // Para que un lote esté próximo a vencer respecto a la fecha actual (Julio 2026),
    // usaremos una fecha dentro de 15 días.
    $fechaProximo = date('Y-m-d', strtotime('+15 days'));
    $fechaDisponible = date('Y-m-d', strtotime('+500 days'));
    
    $insertLote1 = "INSERT INTO lotes (id_producto, numero_lote, cantidad_recibida, fecha_vencimiento) 
                    VALUES (1, 'LOT-20451', 500, '$fechaDisponible')";
    $insertLote2 = "INSERT INTO lotes (id_producto, numero_lote, cantidad_recibida, fecha_vencimiento) 
                    VALUES (2, 'LOT-30812', 300, '$fechaProximo')";
                    
    mysqli_query($conexion, $insertLote1);
    mysqli_query($conexion, $insertLote2);
    echo "Lotes de prueba sembrados.\n";
}

echo "Semilla de inventario completada correctamente.\n";
?>
