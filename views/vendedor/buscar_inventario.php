<?php
require_once __DIR__ . "/../../config/conexion.php";

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : "";
$productos = [];

if ($busqueda !== "") {
    $like = "%$busqueda%";
    $stmt = mysqli_prepare($conexion, "
        SELECT p.id_producto, p.codigo_barras, p.nombre_commercial, p.stock_actual, p.precio_venta_actual, p.requiere_receta,
               l.nombre_laboratorio, c.nombre_categoria
        FROM productos p
        LEFT JOIN laboratorios l ON p.id_laboratorio = l.id_laboratorio
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.nombre_commercial LIKE ? OR p.codigo_barras LIKE ?
        ORDER BY p.nombre_commercial ASC
    ");
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/sistema_sif/assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . "/partials/sidebar.php"; ?>

<div class="main-content">
    <div class="header">
        <h2>CONSULTAR INVENTARIO</h2>
        <div class="user-profile">
            <i class="fa-solid fa-user-tie"></i>
            <span>César (Vendedor)</span>
        </div>
    </div>

    <div class="container" style="grid-template-columns: 1fr;">
        <div class="panel-izquierdo">
            <div class="panel-titulo">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar producto
            </div>

            <form method="get" class="search-box">
                <input type="text" name="q" class="input-sif" placeholder="Buscar por nombre o código de barras" value="<?php echo htmlspecialchars($busqueda); ?>">
                <button class="btn-sif">Buscar</button>
            </form>

            <table class="tabla-sif">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Laboratorio</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Receta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($busqueda !== "" && count($productos) > 0): ?>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['codigo_barras']); ?></td>
                                <td><?php echo htmlspecialchars($p['nombre_commercial']); ?></td>
                                <td><?php echo htmlspecialchars($p['nombre_categoria'] ?? 'General'); ?></td>
                                <td><?php echo htmlspecialchars($p['nombre_laboratorio'] ?? 'General'); ?></td>
                                <td><?php echo (int)$p['stock_actual']; ?></td>
                                <td>C$ <?php echo number_format((float)$p['precio_venta_actual'], 2); ?></td>
                                <td><?php echo ((int)$p['requiere_receta'] === 1) ? 'Sí' : 'No'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No hay resultados para mostrar</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>