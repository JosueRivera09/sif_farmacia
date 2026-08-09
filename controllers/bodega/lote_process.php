<?php
/*
 * Archivo: controllers/bodega/lote_process.php
 * Propósito: Controlador para gestionar el registro de lotes y productos en bodega (MVC).
 */
session_start();
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/Producto.php';
require_once __DIR__ . '/../../models/inventario/Lote.php';

// Verificar sesión y permisos
$permisos_extra = isset($_SESSION['permisos_extra']) ? $_SESSION['permisos_extra'] : [];
$tiene_acceso_bodega = (
    isset($_SESSION['rol']) && (
        $_SESSION['rol'] === 'Bodega' ||
        $_SESSION['rol'] === 'Administrador' ||
        in_array('bodega', $permisos_extra)
    )
);

if (!isset($_SESSION['id_usuario']) || !$tiene_acceso_bodega) {
    header("Location: ../../views/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtener_producto') {
    header('Content-Type: application/json; charset=utf-8');
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de producto inválido.']);
        exit;
    }

    $prod = Producto::obtenerProductoPorId($conexion, $id);
    if ($prod) {
        echo json_encode(['status' => 'success', 'data' => $prod]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    
    if ($action === 'editar_producto') {
        $id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
        if ($id_producto <= 0) {
            header("Location: ../../views/bodega/bodega_lotes.php?error=producto_invalido");
            exit;
        }

        if (empty($_POST['codigo_barras']) || empty($_POST['nombre_commercial']) || intval($_POST['id_categoria']) <= 0 || intval($_POST['id_laboratorio']) <= 0 || empty($_POST['empaque_principal']) || empty($_POST['unidad_minima']) || floatval($_POST['precio_empaque_principal']) <= 0) {
            header("Location: ../../views/bodega/bodega_lotes.php?error=campos_incompletos_prod");
            exit;
        }

        if (Producto::editarProducto($conexion, $id_producto, $_POST)) {
            header("Location: ../../views/bodega/bodega_lotes.php?success_edit=1");
        } else {
            header("Location: ../../views/bodega/bodega_lotes.php?error=fallo_edit");
        }
        exit;
    }

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

    mysqli_begin_transaction($conexion);

    try {
        $id_producto = 0;

        if ($tipo_ingreso === 'nuevo') {
            $id_categoria_post = isset($_POST['id_categoria']) ? trim($_POST['id_categoria']) : '';
            $id_laboratorio_post = isset($_POST['id_laboratorio']) ? trim($_POST['id_laboratorio']) : '';
            
            // 1. Resolver Categoría
            $id_categoria = 0;
            if ($id_categoria_post === 'nueva_categoria') {
                $nueva_cat_nombre = isset($_POST['nueva_categoria_nombre']) ? trim($_POST['nueva_categoria_nombre']) : '';
                if (empty($nueva_cat_nombre)) throw new Exception("El nombre de la nueva categoría está vacío.");
                $cat_nombre_escapado = mysqli_real_escape_string($conexion, $nueva_cat_nombre);
                
                $resCheckCat = mysqli_query($conexion, "SELECT id_categoria FROM categorias WHERE nombre_categoria = '$cat_nombre_escapado'");
                if ($resCheckCat && mysqli_num_rows($resCheckCat) > 0) {
                    $rowCat = mysqli_fetch_assoc($resCheckCat);
                    $id_categoria = intval($rowCat['id_categoria']);
                } else {
                    mysqli_query($conexion, "INSERT INTO categorias (nombre_categoria) VALUES ('$cat_nombre_escapado')");
                    $id_categoria = mysqli_insert_id($conexion);
                }
            } else {
                $id_categoria = intval($id_categoria_post);
            }
            $_POST['id_categoria'] = $id_categoria;

            // 2. Resolver Laboratorio
            $id_laboratorio = 0;
            if ($id_laboratorio_post === 'nuevo_laboratorio') {
                $nuevo_lab_nombre = isset($_POST['nuevo_laboratorio_nombre']) ? trim($_POST['nuevo_laboratorio_nombre']) : '';
                if (empty($nuevo_lab_nombre)) throw new Exception("El nombre del nuevo laboratorio está vacío.");
                $lab_nombre_escapado = mysqli_real_escape_string($conexion, $nuevo_lab_nombre);
                
                $resCheckLab = mysqli_query($conexion, "SELECT id_laboratorio FROM laboratorios WHERE nombre_laboratorio = '$lab_nombre_escapado'");
                if ($resCheckLab && mysqli_num_rows($resCheckLab) > 0) {
                    $rowLab = mysqli_fetch_assoc($resCheckLab);
                    $id_laboratorio = intval($rowLab['id_laboratorio']);
                } else {
                    mysqli_query($conexion, "INSERT INTO laboratorios (nombre_laboratorio) VALUES ('$lab_nombre_escapado')");
                    $id_laboratorio = mysqli_insert_id($conexion);
                }
            } else {
                $id_laboratorio = intval($id_laboratorio_post);
            }
            $_POST['id_laboratorio'] = $id_laboratorio;

            if (empty($_POST['codigo_barras']) || empty($_POST['nombre_commercial']) || $id_categoria <= 0 || $id_laboratorio <= 0 || empty($_POST['empaque_principal']) || empty($_POST['unidad_minima']) || floatval($_POST['precio_empaque_principal']) <= 0) {
                throw new Exception("Campos de producto nuevo incompletos o inválidos.");
            }

            // 3. Crear Producto
            $id_producto = Producto::crearProducto($conexion, $_POST);
            if (!$id_producto) {
                throw new Exception("Error al crear el producto en la base de datos.");
            }

        } else {
            $id_producto = isset($_POST['id_producto_existente']) ? intval($_POST['id_producto_existente']) : 0;
            if ($id_producto <= 0) {
                throw new Exception("Debe seleccionar un producto existente.");
            }
        }

        // Obtener multiplicador
        $unidades_totales_principal = isset($_POST['unidades_totales_por_empaque_principal']) ? intval($_POST['unidades_totales_por_empaque_principal']) : 1;
        $unidades_medio = isset($_POST['unidades_por_empaque_medio']) ? intval($_POST['unidades_por_empaque_medio']) : 1;
        
        if ($tipo_ingreso === 'existente') {
            $queryProd = "SELECT unidades_totales_por_empaque_principal, unidades_por_empaque_medio FROM productos WHERE id_producto = $id_producto";
            $resProd = mysqli_query($conexion, $queryProd);
            if ($resProd && mysqli_num_rows($resProd) > 0) {
                $rowP = mysqli_fetch_assoc($resProd);
                $unidades_totales_principal = intval($rowP['unidades_totales_por_empaque_principal']);
                $unidades_medio = intval($rowP['unidades_por_empaque_medio']);
            } else {
                throw new Exception("Producto existente no encontrado.");
            }
        }

        $multiplicador = 1;
        if ($empaque_ingreso === 'Principal') {
            $multiplicador = $unidades_totales_principal;
        } elseif ($empaque_ingreso === 'Medio') {
            $multiplicador = $unidades_medio;
        }

        $cantidad_unidades_totales = $cantidad_empaques_recibidos * $multiplicador;

        // Crear Lote
        if (!Lote::crearLote($conexion, $id_producto, $numero_lote, $empaque_ingreso, $cantidad_empaques_recibidos, $cantidad_unidades_totales, $fecha_vencimiento, $bodega)) {
            throw new Exception("Error al registrar el lote.");
        }

        // Actualizar y sincronizar el stock total del producto con sus lotes
        if (!Producto::sincronizarStockProducto($conexion, $id_producto)) {
            throw new Exception("Error al actualizar el stock del producto.");
        }

        mysqli_commit($conexion);
        header("Location: ../../views/bodega/bodega_lotes.php?success_lote=1");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        $error_msg = urlencode($e->getMessage());
        header("Location: ../../views/bodega/bodega_lotes.php?error=exception&msg=$error_msg");
        exit;
    }
} else {
    header("Location: ../../views/bodega/bodega_lotes.php");
    exit;
}
