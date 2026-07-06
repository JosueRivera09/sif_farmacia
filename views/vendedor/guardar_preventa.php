<?php
require_once __DIR__ . "/../../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nueva_preventa.php");
    exit;
}

$id_cliente = !empty($_POST["id_cliente"]) ? (int)$_POST["id_cliente"] : null;
$items = $_POST["items"] ?? [];

mysqli_begin_transaction($conexion);

try {
    $stmt = mysqli_prepare($conexion, "INSERT INTO ventas (id_usuario, id_cliente, total_neto, estado_venta) VALUES (?, ?, 0.00, 'Pendiente')");
    $id_usuario = 1;
    mysqli_stmt_bind_param($stmt, "ii", $id_usuario, $id_cliente);
    mysqli_stmt_execute($stmt);
    $id_venta = mysqli_insert_id($conexion);

    $total = 0;

    foreach ($items as $id_producto => $item) {
        if (!isset($item["seleccionado"])) continue;

        $cantidad = (int)($item["cantidad"] ?? 0);
        if ($cantidad <= 0) continue;

        $stmtProd = mysqli_prepare($conexion, "SELECT precio_venta_actual, stock_actual FROM productos WHERE id_producto = ?");
        mysqli_stmt_bind_param($stmtProd, "i", $id_producto);
        mysqli_stmt_execute($stmtProd);
        $resProd = mysqli_stmt_get_result($stmtProd);
        $prod = mysqli_fetch_assoc($resProd);

        if (!$prod) {
            throw new Exception("Producto no encontrado.");
        }

        if ($cantidad > (int)$prod["stock_actual"]) {
            throw new Exception("Stock insuficiente para el producto ID $id_producto.");
        }

        $precio = (float)$prod["precio_venta_actual"];
        $subtotal = $precio * $cantidad;
        $total += $subtotal;

        $stmtDet = mysqli_prepare($conexion, "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmtDet, "iiid", $id_venta, $id_producto, $cantidad, $precio);
        mysqli_stmt_execute($stmtDet);
    }

    $stmtUp = mysqli_prepare($conexion, "UPDATE ventas SET total_neto = ? WHERE id_venta = ?");
    mysqli_stmt_bind_param($stmtUp, "di", $total, $id_venta);
    mysqli_stmt_execute($stmtUp);

    mysqli_commit($conexion);
    header("Location: inicio_vendedor.php");
    exit;
} catch (Exception $e) {
    mysqli_rollback($conexion);
    die("Error al guardar preventa: " . $e->getMessage());
}
?>