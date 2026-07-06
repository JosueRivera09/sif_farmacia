<?php
require_once __DIR__ . "/../../config/conexion.php";

$ventas = [];
$r1 = mysqli_query($conexion, "
    SELECT v.id_venta, v.fecha_venta, v.total_neto, v.estado_venta, c.nombre_completo
    FROM ventas v
    LEFT JOIN clientes c ON c.id_cliente = v.id_cliente
    ORDER BY v.id_venta DESC
");
if ($r1) {
    while ($row = mysqli_fetch_assoc($r1)) $ventas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Ventas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . "/partials/sidebar.php"; ?>

<div class="main-content">
    <div class="header">
        <h2>HISTORIAL DE VENTAS</h2>
        <div class="user-profile">
            <i class="fa-solid fa-user-tie"></i>
            <span>César (Vendedor)</span>
        </div>
    </div>

    <div class="panel-izquierdo" style="margin:30px;">
        <div class="panel-titulo">
            <i class="fa-solid fa-receipt"></i> Ventas registradas
        </div>

        <table class="tabla-sif">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($ventas) > 0): ?>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td><?php echo (int)$v['id_venta']; ?></td>
                            <td><?php echo htmlspecialchars($v['nombre_completo'] ?? 'Consumidor final'); ?></td>
                            <td><?php echo htmlspecialchars($v['fecha_venta']); ?></td>
                            <td>C$ <?php echo number_format((float)$v['total_neto'], 2); ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower(htmlspecialchars($v['estado_venta'])); ?>">
                                    <?php echo htmlspecialchars($v['estado_venta']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No hay ventas registradas</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>