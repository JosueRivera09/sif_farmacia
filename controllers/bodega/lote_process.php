<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Verificar si hay sesión iniciada
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../views/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_ingreso = isset($_POST['tipo_ingreso']) ? trim($_POST['tipo_ingreso']) : 'existente';
    $numero_lote = isset($_POST['numero_lote']) ? trim($_POST['numero_lote']) : '';
    $empaque_ingreso = isset($_POST['empaque_ingreso']) ? trim($_POST['empaque_ingreso']) : 'Principal';
    $cantidad_empaques_recibidos = isset($_POST['cantidad_empaques_recibidos']) ? intval($_POST['cantidad_empaques_recibidos']) : 0;
    $fecha_vencimiento = isset($_POST['fecha_vencimiento']) ? trim($_POST['fecha_vencimiento']) : '';
    $bodega = isset($_POST['bodega']) ? trim($_POST['bodega']) : 'Bodega Principal - Managua';

    if (empty($numero_lote) || $cantidad_empaques_recibidos <= 0 || empty($fecha_vencimiento) || empty($bodega)) {
        header("Location: ../../views/bodega/bodega_lotes.php?error=campos_vacios");
        exit;
    }

    // Iniciar transacción para garantizar atomicidad
    mysqli_begin_transaction($conexion);

    try {
        $id_producto = 0;

        if ($tipo_ingreso === 'nuevo') {
            // Registrar nuevo producto
            $codigo_barras = isset($_POST['codigo_barras']) ? trim($_POST['codigo_barras']) : '';
            $nombre_commercial = isset($_POST['nombre_commercial']) ? trim($_POST['nombre_commercial']) : '';
            $id_categoria_post = isset($_POST['id_categoria']) ? trim($_POST['id_categoria']) : '';
            $id_laboratorio_post = isset($_POST['id_laboratorio']) ? trim($_POST['id_laboratorio']) : '';
            $tipo_producto = isset($_POST['tipo_producto']) ? trim($_POST['tipo_producto']) : 'General';
            $miligramos = isset($_POST['miligramos']) && $_POST['miligramos'] !== '' ? intval($_POST['miligramos']) : 'NULL';
            
            $empaque_principal = isset($_POST['empaque_principal']) ? trim($_POST['empaque_principal']) : 'Caja';
            $empaque_medio = isset($_POST['empaque_medio']) && trim($_POST['empaque_medio']) !== '' ? trim($_POST['empaque_medio']) : null;
            $unidad_minima = isset($_POST['unidad_minima']) ? trim($_POST['unidad_minima']) : 'Unidad';
            
            $unidades_por_empaque_medio = isset($_POST['unidades_por_empaque_medio']) ? intval($_POST['unidades_por_empaque_medio']) : 1;
            $unidades_totales_por_empaque_principal = isset($_POST['unidades_totales_por_empaque_principal']) ? intval($_POST['unidades_totales_por_empaque_principal']) : 1;
            
            $es_fraccionable = isset($_POST['es_fraccionable']) ? 1 : 0;
            
            $precio_empaque_principal = isset($_POST['precio_empaque_principal']) ? floatval($_POST['precio_empaque_principal']) : 0.0;
            $precio_empaque_medio = isset($_POST['precio_empaque_medio']) && trim($_POST['precio_empaque_medio']) !== '' ? floatval($_POST['precio_empaque_medio']) : null;
            $precio_unidad_minima = isset($_POST['precio_unidad_minima']) ? floatval($_POST['precio_unidad_minima']) : 0.0;
            
            $stock_minimo = isset($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0;
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $requiere_receta = isset($_POST['requiere_receta']) ? 1 : 0;

            // 1. Resolver Categoría
            $id_categoria = 0;
            if ($id_categoria_post === 'nueva_categoria') {
                $nueva_cat_nombre = isset($_POST['nueva_categoria_nombre']) ? trim($_POST['nueva_categoria_nombre']) : '';
                if (empty($nueva_cat_nombre)) {
                    throw new Exception("El nombre de la nueva categoría está vacío.");
                }
                $cat_nombre_escapado = mysqli_real_escape_string($conexion, $nueva_cat_nombre);
                
                // Verificar duplicados
                $queryCheckCat = "SELECT id_categoria FROM categorias WHERE nombre_categoria = '$cat_nombre_escapado'";
                $resCheckCat = mysqli_query($conexion, $queryCheckCat);
                if ($resCheckCat && mysqli_num_rows($resCheckCat) > 0) {
                    $rowCat = mysqli_fetch_assoc($resCheckCat);
                    $id_categoria = intval($rowCat['id_categoria']);
                } else {
                    $queryInsertCat = "INSERT INTO categorias (nombre_categoria) VALUES ('$cat_nombre_escapado')";
                    if (!mysqli_query($conexion, $queryInsertCat)) {
                        throw new Exception("Error al crear categoría: " . mysqli_error($conexion));
                    }
                    $id_categoria = mysqli_insert_id($conexion);
                }
            } else {
                $id_categoria = intval($id_categoria_post);
            }

            // 2. Resolver Laboratorio
            $id_laboratorio = 0;
            if ($id_laboratorio_post === 'nuevo_laboratorio') {
                $nuevo_lab_nombre = isset($_POST['nuevo_laboratorio_nombre']) ? trim($_POST['nuevo_laboratorio_nombre']) : '';
                if (empty($nuevo_lab_nombre)) {
                    throw new Exception("El nombre del nuevo laboratorio está vacío.");
                }
                $lab_nombre_escapado = mysqli_real_escape_string($conexion, $nuevo_lab_nombre);
                
                // Verificar duplicados
                $queryCheckLab = "SELECT id_laboratorio FROM laboratorios WHERE nombre_laboratorio = '$lab_nombre_escapado'";
                $resCheckLab = mysqli_query($conexion, $queryCheckLab);
                if ($resCheckLab && mysqli_num_rows($resCheckLab) > 0) {
                    $rowLab = mysqli_fetch_assoc($resCheckLab);
                    $id_laboratorio = intval($rowLab['id_laboratorio']);
                } else {
                    $queryInsertLab = "INSERT INTO laboratorios (nombre_laboratorio) VALUES ('$lab_nombre_escapado')";
                    if (!mysqli_query($conexion, $queryInsertLab)) {
                        throw new Exception("Error al crear laboratorio: " . mysqli_error($conexion));
                    }
                    $id_laboratorio = mysqli_insert_id($conexion);
                }
            } else {
                $id_laboratorio = intval($id_laboratorio_post);
            }

            if (empty($codigo_barras) || empty($nombre_commercial) || $id_categoria <= 0 || $id_laboratorio <= 0 || empty($empaque_principal) || empty($unidad_minima) || $precio_empaque_principal <= 0 || empty($tipo_producto)) {
                throw new Exception("Campos de producto nuevo incompletos o inválidos.");
            }

            // Escapar cadenas de producto
            $cod_escapado = mysqli_real_escape_string($conexion, $codigo_barras);
            $nom_escapado = mysqli_real_escape_string($conexion, $nombre_commercial);
            $tipo_escapado = mysqli_real_escape_string($conexion, $tipo_producto);
            $desc_escapado = mysqli_real_escape_string($conexion, $descripcion);
            
            $empaque_prin_esc = mysqli_real_escape_string($conexion, $empaque_principal);
            $empaque_med_esc = $empaque_medio ? "'" . mysqli_real_escape_string($conexion, $empaque_medio) . "'" : 'NULL';
            $uni_min_esc = mysqli_real_escape_string($conexion, $unidad_minima);
            $precio_med_val = $precio_empaque_medio !== null ? $precio_empaque_medio : 'NULL';

            // Insertar producto con stock_actual = 0. El Trigger sumará automáticamente cuando se inserte el lote.
            $queryInsertProd = "INSERT INTO productos (codigo_barras, nombre_commercial, descripcion, id_categoria, id_laboratorio, miligramos, 
                                empaque_principal, empaque_medio, unidad_minima, unidades_por_empaque_medio, unidades_totales_por_empaque_principal, 
                                es_fraccionable, precio_empaque_principal, precio_empaque_medio, precio_unidad_minima, requiere_receta, stock_actual, stock_minimo, tipo_producto) 
                                VALUES ('$cod_escapado', '$nom_escapado', " . ($desc_escapado === '' ? 'NULL' : "'$desc_escapado'") . ", $id_categoria, $id_laboratorio, $miligramos, 
                                '$empaque_prin_esc', $empaque_med_esc, '$uni_min_esc', $unidades_por_empaque_medio, $unidades_totales_por_empaque_principal, 
                                $es_fraccionable, $precio_empaque_principal, $precio_med_val, $precio_unidad_minima, $requiere_receta, 0, $stock_minimo, '$tipo_escapado')";
            
            if (!mysqli_query($conexion, $queryInsertProd)) {
                throw new Exception("Error al crear producto: " . mysqli_error($conexion));
            }

            $id_producto = mysqli_insert_id($conexion);
        } else {
            // Producto existente
            $id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
            if ($id_producto <= 0) {
                throw new Exception("Producto no seleccionado.");
            }
        }

        // Escapar datos del lote
        $lote_escapado = mysqli_real_escape_string($conexion, $numero_lote);
        $venc_escapado = mysqli_real_escape_string($conexion, $fecha_vencimiento);
        $bodega_escapada = mysqli_real_escape_string($conexion, $bodega);
        $empaque_ingreso_esc = mysqli_real_escape_string($conexion, $empaque_ingreso);
        
        // Calcular la cantidad de unidades reales que entraron
        $queryFactores = "SELECT unidades_totales_por_empaque_principal, unidades_por_empaque_medio FROM productos WHERE id_producto = $id_producto LIMIT 1";
        $resFactores = mysqli_query($conexion, $queryFactores);
        if (!$resFactores || mysqli_num_rows($resFactores) === 0) {
            throw new Exception("Producto no encontrado para calcular unidades de lote.");
        }
        $factores = mysqli_fetch_assoc($resFactores);
        
        $factor_conversion = 1;
        if ($empaque_ingreso === 'Principal') {
            $factor_conversion = intval($factores['unidades_totales_por_empaque_principal']);
        } elseif ($empaque_ingreso === 'Medio') {
            $factor_conversion = intval($factores['unidades_por_empaque_medio']);
        }
        
        $cantidad_unidades_recibidas = $cantidad_empaques_recibidos * $factor_conversion;

        // Insertar lote (esto dispara el trigger de actualización de stock en 'productos')
        $queryInsertLote = "INSERT INTO lotes (id_producto, numero_lote, empaque_ingreso, cantidad_empaques_recibidos, cantidad_unidades_recibidas, fecha_vencimiento, bodega) 
                            VALUES ($id_producto, '$lote_escapado', '$empaque_ingreso_esc', $cantidad_empaques_recibidos, $cantidad_unidades_recibidas, '$venc_escapado', '$bodega_escapada')";
        
        if (!mysqli_query($conexion, $queryInsertLote)) {
            throw new Exception("Error al registrar lote: " . mysqli_error($conexion));
        }

        // Confirmar transacción
        mysqli_commit($conexion);

        header("Location: ../../views/bodega/bodega_lotes.php?success=1");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        header("Location: ../../views/bodega/bodega_lotes.php?error=fallo_registro");
        exit;
    }
} else {
    header("Location: ../../views/bodega/bodega_lotes.php");
    exit;
}
?>
