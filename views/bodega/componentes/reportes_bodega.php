<?php
/*
 * Archivo: views/bodega/componentes/reportes_bodega.php
 * Propósito: Vista para la generación de reportes de bodega e inventario.
 */

$resumen_reportes = isset($resumen_reportes) ? $resumen_reportes : ['total_lotes' => 0, 'total_vencidos' => 0, 'total_por_vencer' => 0, 'total_bajo_stock' => 0];
$lotes_vencidos = isset($lotes_vencidos) ? $lotes_vencidos : [];
$productos_bajo_stock = isset($productos_bajo_stock) ? $productos_bajo_stock : [];
?>
<div class="row g-4 mb-4">
    <!-- Card 1: Total Lotes -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="metric-card bg-slate border border-secondary" style="border-radius: 12px; padding: 20px;">
            <p class="metric-title text-muted mb-1" style="font-size: 12px; text-transform: uppercase;">Lotes Registrados</p>
            <h3 class="metric-value mb-0 font-bold text-light"><?php echo $resumen_reportes['total_lotes']; ?></h3>
            <span class="text-muted" style="font-size: 11px;">Total histórico en bodega</span>
        </div>
    </div>
    <!-- Card 2: Vencidos -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="metric-card bg-slate border <?php echo ($resumen_reportes['total_vencidos'] > 0) ? 'border-danger' : 'border-secondary'; ?>" style="border-radius: 12px; padding: 20px;">
            <p class="metric-title text-muted mb-1" style="font-size: 12px; text-transform: uppercase;">Lotes Vencidos</p>
            <h3 class="metric-value mb-0 font-bold <?php echo ($resumen_reportes['total_vencidos'] > 0) ? 'text-danger' : 'text-light'; ?>"><?php echo $resumen_reportes['total_vencidos']; ?></h3>
            <span class="text-muted" style="font-size: 11px;">Requieren desecho urgente</span>
        </div>
    </div>
    <!-- Card 3: Por Vencer -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="metric-card bg-slate border <?php echo ($resumen_reportes['total_por_vencer'] > 0) ? 'border-warning' : 'border-secondary'; ?>" style="border-radius: 12px; padding: 20px;">
            <p class="metric-title text-muted mb-1" style="font-size: 12px; text-transform: uppercase;">Próximos a Vencer</p>
            <h3 class="metric-value mb-0 font-bold <?php echo ($resumen_reportes['total_por_vencer'] > 0) ? 'text-warning' : 'text-light'; ?>"><?php echo $resumen_reportes['total_por_vencer']; ?></h3>
            <span class="text-muted" style="font-size: 11px;">Vencimiento en < 30 días</span>
        </div>
    </div>
    <!-- Card 4: Bajo Stock -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="metric-card bg-slate border <?php echo ($resumen_reportes['total_bajo_stock'] > 0) ? 'border-danger' : 'border-secondary'; ?>" style="border-radius: 12px; padding: 20px;">
            <p class="metric-title text-muted mb-1" style="font-size: 12px; text-transform: uppercase;">Bajo Stock Mínimo</p>
            <h3 class="metric-value mb-0 font-bold <?php echo ($resumen_reportes['total_bajo_stock'] > 0) ? 'text-danger' : 'text-light'; ?>"><?php echo $resumen_reportes['total_bajo_stock']; ?></h3>
            <span class="text-muted" style="font-size: 11px;">Necesitan reabastecimiento</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Lotes Vencidos / Próximos a Vencer Table -->
    <div class="col-12 col-lg-6">
        <div class="data-section" style="border-radius: 12px; padding: 24px; height: 100%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-light font-bold" style="font-size: 15px;">
                    <span class="material-symbols-outlined text-danger me-2 align-middle">gpp_maybe</span>Lotes Críticos / Vencidos
                </h6>
                <span class="badge bg-danger-subtle text-danger px-2 py-1" style="font-size: 11px;"><?php echo count($lotes_vencidos); ?> críticos</span>
            </div>
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-custom table-borderless align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Vencimiento</th>
                            <th class="text-end">Cant.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($lotes_vencidos) > 0): ?>
                            <?php foreach ($lotes_vencidos as $lv): ?>
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <td class="font-mono-custom text-primary-custom" style="font-size: 13px;"><?php echo htmlspecialchars($lv['numero_lote']); ?></td>
                                    <td>
                                        <span class="font-semibold text-light" style="font-size: 13px;"><?php echo htmlspecialchars($lv['nombre_commercial']); ?></span>
                                    </td>
                                    <td class="text-danger font-bold" style="font-size: 13px;"><?php echo date('d M Y', strtotime($lv['fecha_vencimiento'])); ?></td>
                                    <td class="text-end text-light" style="font-size: 13px;">
                                        <?php 
                                            $cantLV = isset($lv['cantidad_unidades_recibidas']) ? $lv['cantidad_unidades_recibidas'] : (isset($lv['cantidad_recibida']) ? $lv['cantidad_recibida'] : 0);
                                            $uniLV = isset($lv['unidad_minima']) ? $lv['unidad_minima'] : (isset($lv['unidad_medida']) ? $lv['unidad_medida'] : '');
                                            echo $cantLV . ' ' . $uniLV;
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No hay lotes vencidos registrados en este momento.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Productos Bajo Stock Mínimo Table -->
    <div class="col-12 col-lg-6">
        <div class="data-section" style="border-radius: 12px; padding: 24px; height: 100%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-light font-bold" style="font-size: 15px;">
                    <span class="material-symbols-outlined text-warning me-2 align-middle">warning</span>Productos Bajo Stock Mínimo
                </h6>
                <span class="badge bg-warning-subtle text-warning px-2 py-1" style="font-size: 11px;"><?php echo count($productos_bajo_stock); ?> alertas</span>
            </div>
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-custom table-borderless align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">
                            <th>Código Barras</th>
                            <th>Producto</th>
                            <th class="text-center">Stock Actual</th>
                            <th class="text-center">Stock Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($productos_bajo_stock) > 0): ?>
                            <?php foreach ($productos_bajo_stock as $pb): ?>
                                <?php 
                                    $uniPB = isset($pb['unidad_minima']) ? $pb['unidad_minima'] : (isset($pb['unidad_medida']) ? $pb['unidad_medida'] : '');
                                ?>
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <td class="font-mono-custom text-secondary" style="font-size: 13px;"><?php echo htmlspecialchars($pb['codigo_barras']); ?></td>
                                    <td>
                                        <span class="font-semibold text-light" style="font-size: 13px;"><?php echo htmlspecialchars($pb['nombre_commercial']); ?></span>
                                    </td>
                                    <td class="text-center text-danger font-bold" style="font-size: 13px;"><?php echo $pb['stock_actual'] . ' ' . $uniPB; ?></td>
                                    <td class="text-center text-muted" style="font-size: 13px;"><?php echo $pb['stock_minimo'] . ' ' . $uniPB; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No hay productos por debajo del stock mínimo.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 p-4 rounded-4 bg-slate border border-secondary d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <div>
        <h6 class="mb-1 text-light font-bold">Descargar Reporte Completo de Existencias</h6>
        <p class="text-muted mb-0" style="font-size: 13px;">Genera un reporte consolidado con todos los lotes, sus fechas de vencimiento y estado actual.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="../../controllers/bodega/exportar.php?format=excel" class="btn btn-secondary text-decoration-none">
            <i class="fa-solid fa-file-excel me-1"></i>
            Excel (.csv)
        </a>
        <a href="../../controllers/bodega/exportar.php?format=pdf" target="_blank" class="btn btn-primary text-decoration-none">
            <i class="fa-solid fa-file-pdf me-1"></i>
            PDF (.pdf)
        </a>
    </div>
</div>
