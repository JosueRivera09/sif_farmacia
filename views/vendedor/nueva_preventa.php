<?php
require_once __DIR__ . "/../../config/conexion.php";

$clientes = [];
$productos = [];

$r1 = mysqli_query($conexion, "SELECT id_cliente, nombre_completo, cedula FROM clientes ORDER BY nombre_completo ASC");
if ($r1) {
    while ($row = mysqli_fetch_assoc($r1)) $clientes[] = $row;
}

$r2 = mysqli_query($conexion, "
    SELECT p.id_producto, p.codigo_barras, p.nombre_commercial, p.stock_actual, p.precio_venta_actual
    FROM productos p
    ORDER BY p.nombre_commercial ASC
");
if ($r2) {
    while ($row = mysqli_fetch_assoc($r2)) $productos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Preventa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/sistema_sif/assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . "/partials/sidebar.php"; ?>

<div class="main-content">
    <div class="header">
        <h2>NUEVA PREVENTA</h2>
        <div class="user-profile">
            <i class="fa-solid fa-user-tie"></i>
            <span>César (Vendedor)</span>
        </div>
    </div>

    <form method="post" action="guardar_preventa.php">
        <div class="panel-izquierdo" style="margin: 30px;">
            <div class="panel-titulo">
                <i class="fa-solid fa-user"></i> Cliente
            </div>
            <select name="id_cliente" class="input-sif" style="width:100%; margin-bottom:20px;">
                <option value="">Consumidor final</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?php echo (int)$c['id_cliente']; ?>">
                        <?php echo htmlspecialchars($c['nombre_completo']); ?><?php echo !empty($c['cedula']) ? " - " . htmlspecialchars($c['cedula']) : ""; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="panel-titulo">
                <i class="fa-solid fa-box"></i> Productos
            </div>

            <table class="tabla-sif">
                <thead>
                    <tr>
                        <th>Sel.</th>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td><input type="checkbox" name="items[<?php echo (int)$p['id_producto']; ?>][seleccionado]" value="1"></td>
                            <td><?php echo htmlspecialchars($p['codigo_barras']); ?></td>
                            <td><?php echo htmlspecialchars($p['nombre_commercial']); ?></td>
                            <td><?php echo (int)$p['stock_actual']; ?></td>
                            <td>C$ <?php echo number_format((float)$p['precio_venta_actual'], 2); ?></td>
                            <td style="max-width:120px;">
                                <input type="number" min="1" name="items[<?php echo (int)$p['id_producto']; ?>][cantidad]" class="input-sif" value="1">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button class="btn-cobrar" type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar preventa</button>
        </div>
    </form>
</div>

</body>
</html>