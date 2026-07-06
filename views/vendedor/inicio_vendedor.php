<?php
require_once __DIR__ . "/../../config/conexion.php";

$totalProductos = 0;
$productosBajoStock = 0;
$ventasPendientes = 0;
$productosRecientes = [];

$r1 = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM productos");
if ($r1) $totalProductos = (int)mysqli_fetch_assoc($r1)['total'];

$r2 = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM productos WHERE stock_actual <= stock_minimo");
if ($r2) $productosBajoStock = (int)mysqli_fetch_assoc($r2)['total'];

$r3 = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM ventas WHERE estado_venta = 'Pendiente'");
if ($r3) $ventasPendientes = (int)mysqli_fetch_assoc($r3)['total'];

$r4 = mysqli_query($conexion, "
    SELECT p.nombre_commercial, p.stock_actual, p.precio_venta_actual, p.requiere_receta, l.nombre_laboratorio
    FROM productos p
    LEFT JOIN laboratorios l ON p.id_laboratorio = l.id_laboratorio
    ORDER BY p.fecha_creacion DESC
    LIMIT 5
");
if ($r4) {
    while ($row = mysqli_fetch_assoc($r4)) {
        $productosRecientes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema SIF - Inicio Vendedor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/sistema_sif/assets/css/styles.css">
    <style>
        .container {
            grid-template-columns: 2fr 1fr;
        }
        .bienvenida-box {
            background-color: var(--bg-tarjetas);
            padding: 24px;
            border-radius: 6px;
            border-left: 4px solid var(--verde-sif);
        }
        .bienvenida-box h1 {
            margin-bottom: 8px;
            font-size: 1.7rem;
        }
        .bienvenida-box p {
            color: var(--texto-mutado);
            line-height: 1.5;
        }
        .cards-row {
            grid-column: span 2;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .card-mini.blue { background-color: #1e2942; }
        .card-mini.green { background-color: #143537; }
        .card-mini.yellow { background-color: #3b3520; }
        .productos-lista {
            display: grid;
            gap: 12px;
        }
        .producto-item {
            background-color: var(--bg-principal);
            border: 1px solid #243046;
            padding: 14px;
            border-radius: 6px;
        }
        .producto-item strong {
            display: block;
            margin-bottom: 5px;
        }
        .producto-item small {
            color: var(--texto-mutado);
        }
        .quick-links {
            display: grid;
            gap: 12px;
        }
        .quick-link {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            background-color: #243046;
            padding: 14px;
            border-radius: 6px;
        }
        .quick-link:hover {
            background-color: #2c3952;
        }
    </style>
</head>
<body>

<?php include __DIR__ . "/partials/sidebar.php"; ?>

<div class="main-content">
    <div class="header">
        <h2>SISTEMA SIF - PANEL DE VENDEDOR</h2>
        <div class="user-profile">
            <i class="fa-solid fa-user-tie"></i>
            <span>César (Vendedor)</span>
        </div>
    </div>

    <div class="container">
        <div class="bienvenida-box" style="grid-column: span 2;">
            <h1>¡Hola, César!</h1>
            <p>Desde este panel puedes consultar inventario, crear preventas pendientes y despachar cuando la caja haya confirmado el pago.</p>
        </div>

        <div class="cards-row">
            <div class="card-mini blue">
                <div class="card-mini-info">
                    <h3>Total de productos</h3>
                    <p><?php echo $totalProductos; ?></p>
                </div>
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>

            <div class="card-mini yellow">
                <div class="card-mini-info">
                    <h3>Productos bajo stock</h3>
                    <p><?php echo $productosBajoStock; ?></p>
                </div>
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="card-mini green">
                <div class="card-mini-info">
                    <h3>Ventas pendientes</h3>
                    <p><?php echo $ventasPendientes; ?></p>
                </div>
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <div class="panel-izquierdo">
            <div class="panel-titulo">
                <i class="fa-solid fa-boxes-stacked"></i> Productos recientes
            </div>

            <div class="productos-lista">
                <?php if (count($productosRecientes) > 0): ?>
                    <?php foreach ($productosRecientes as $p): ?>
                        <div class="producto-item">
                            <strong><?php echo htmlspecialchars($p['nombre_commercial']); ?></strong>
                            <small>
                                Laboratorio: <?php echo htmlspecialchars($p['nombre_laboratorio'] ?? 'General'); ?> |
                                Stock: <?php echo (int)$p['stock_actual']; ?> |
                                Precio: C$ <?php echo number_format($p['precio_venta_actual'], 2); ?>
                                <?php if ((int)$p['requiere_receta'] === 1): ?>
                                    | <span class="status-badge pendiente">Receta</span>
                                <?php endif; ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="producto-item">
                        <strong>No hay productos cargados</strong>
                        <small>Primero inserta datos en la tabla productos.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel-derecho">
            <div class="panel-titulo">
                <i class="fa-solid fa-circle-info"></i> Accesos rápidos
            </div>

            <div class="quick-links">
                <a href="modulo_vendedor.php" class="quick-link"><i class="fa-solid fa-cash-register"></i> Ir a Vender</a>
                <a href="historial_vendedor.php" class="quick-link"><i class="fa-solid fa-receipt"></i> Ver Historial</a>
                <a href="buscar_inventario.php" class="quick-link"><i class="fa-solid fa-magnifying-glass"></i> Buscar Inventario</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>