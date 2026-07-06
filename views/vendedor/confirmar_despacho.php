<?php
require_once __DIR__ . "/../../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: despachar.php");
    exit;
}

$id_venta = (int)$_POST["id_venta"];

mysqli_begin_transaction($conexion);

try {
    $stmt = mysqli_prepare($conexion, "SELECT estado_venta FROM ventas WHERE id_venta = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_venta);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $venta = mysqli_fetch_assoc($res);

    if (!$venta) {
        throw new Exception("La venta no existe.");
    }

    if ($venta["estado_venta"] !== "Pagado") {
        throw new Exception("No se puede despachar si la venta no está Pagada.");
    }

    $stmtDet = mysqli_prepare($conexion, "SELECT id_producto, cantidad FROM detalle_ventas WHERE id_venta = ?");
    mysqli_stmt_bind_param($stmtDet, "i", $id_venta);
    mysqli_stmt_execute($stmtDet);
    $resDet = mysqli_stmt_get_result($stmtDet);

    while ($d = mysqli_fetch_assoc($resDet)) {
        $stmtProd = mysqli_prepare($conexion, "SELECT stock_actual FROM productos WHERE id_producto = ?");
        mysqli_stmt_bind_param($stmtProd, "i", $d["id_producto"]);
        mysqli_stmt_execute($stmtProd);
        $resProd = mysqli_stmt_get_result($stmtProd);
        $prod = mysqli_fetch_assoc($resProd);

        if (!$prod) {
            throw new Exception("Producto no encontrado.");
        }

        if ((int)$d["cantidad"] > (int)$prod["stock_actual"]) {
            throw new Exception("Stock insuficiente para el producto ID " . $d["id_producto"]);
        }

        $upd = mysqli_prepare($conexion, "UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ?");
        mysqli_stmt_bind_param($upd, "ii", $d["cantidad"], $d["id_producto"]);
        mysqli_stmt_execute($upd);
    }

    $updVenta = mysqli_prepare($conexion, "UPDATE ventas SET estado_venta = 'Entregado' WHERE id_venta = ?");
    mysqli_stmt_bind_param($updVenta, "i", $id_venta);
    mysqli_stmt_execute($updVenta);

    mysqli_commit($conexion);
    header("Location: despachar.php");
    exit;
} catch (Exception $e) {
    mysqli_rollback($conexion);
    die("Error al confirmar despacho: " . $e->getMessage());
}
?>