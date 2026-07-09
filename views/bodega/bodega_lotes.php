<?php
require_once __DIR__ . '/../../controllers/bodega/BodegaController.php';
?>
<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>SISTEMA SIF - Bodega y Lotes</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Google Fonts - Inter & JetBrains Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet" />
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <!-- Hojas de estilo personalizadas -->
    <link href="../../assets/css/bodega_lotes.css" rel="stylesheet" />
</head>

<body class="h-100">

    <div class="app-container">

        <!-- BEGIN: Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div>
                    <img src="../../assets/img/logo.png" alt="Logo SISTEMA SIF" style="width: 50px; height: 50px; object-fit: contain;">
                </div>
                <div>
                    <h1 class="sidebar-brand">SISTEMA SIF</h1>
                    <p class="sidebar-subtitle mb-0">Panel Bodega</p>
                </div>
            </div>

            <nav class="sidebar-menu custom-scrollbar">
                <?php if ($_SESSION['rol'] === 'Administrador'): ?>
                    <a class="nav-link-custom" href="../Interfaz_admin/admin_dashboard.php">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-link-custom" href="../Interfaz_admin/admin_dashboard.php">
                        <span class="material-symbols-outlined">group</span>
                        <span>Gestión Usuarios</span>
                    </a>
                    <a class="nav-link-custom" href="../Interfaz_admin/admin_dashboard.php">
                        <span class="material-symbols-outlined">inventory_2</span>
                        <span>Catálogo Productos</span>
                    </a>
                    <a class="nav-link-custom active" href="bodega_lotes.php">
                        <span class="material-symbols-outlined">warehouse</span>
                        <span>Bodega y Lotes</span>
                    </a>
                    <a class="nav-link-custom" href="../Interfaz_admin/admin_dashboard.php">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <span>Auditoría de Ingresos</span>
                    </a>
                    <a class="nav-link-custom" href="../Interfaz_admin/admin_dashboard.php">
                        <span class="material-symbols-outlined">analytics</span>
                        <span>Reportes Diarios</span>
                    </a>
                <?php else: ?>
                    <a class="nav-link-custom" href="../vendedor_dashboard.php">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>Inicio</span>
                    </a>
                    <a class="nav-link-custom active" href="bodega_lotes.php">
                        <span class="material-symbols-outlined">warehouse</span>
                        <span>Bodega y Lotes</span>
                    </a>
                    <a class="nav-link-custom" href="#">
                        <span class="material-symbols-outlined">analytics</span>
                        <span>Mis Reportes</span>
                    </a>
                <?php endif; ?>
            </nav>

            <!-- Botón de ajustes y cerrar sesión integrado de forma consistente -->
            <div class="sidebar-footer">
                <a class="nav-link-custom" href="#">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Ajustes</span>
                </a>
                <a class="nav-link-custom text-error-custom" href="../../controllers/logout.php">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </aside>
        <!-- END: Sidebar -->

        <!-- Main Content Area -->
        <div class="main-content">

            <!-- BEGIN: TopHeader -->
            <header class="top-header">
                <h2 class="header-title">Bodega y Lotes</h2>

                <div class="d-flex align-items-center gap-4">
                    <div class="header-icons d-flex gap-3">
                        <span class="material-symbols-outlined header-icon-btn">notifications</span>

                    </div>
                    <!--Perfil del usuario logueado -->
                    <div class="profile-container d-flex align-items-center gap-3 border-start border-secondary-subtle ps-4">
                        <div class="text-end d-none d-sm-block">
                            <p class="mb-0 font-bold text-light" style="font-size: 14px; font-weight: 700;"><?php echo $nombre_usuario; ?></p>
                            <p class="mb-0 text-muted uppercase tracking-wider" style="font-size: 10px;"><?php echo $rol_usuario; ?></p>
                        </div>
                        <div class="avatar-box">
                            <img class="w-100 h-100 object-fit-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCuF-iQpxxKVH4dx-vZoHx-5U8lhGCDmzgsTwm3oZr6Na276hBSHqhkmbpEqZdgV1meyGb_jKZQlTsIPbhhSStuy4CY5cBn0ZURf2TnyzatF-TXxpYbHwBbdzJcuE6R88T4pu1bFmdA3zi1r9QcbaFPNPK0_kpPBuRf8inZ-puuthBNSfQxLQz3UBbryi9bwzMNtmR9ZjD-4oVqVDN5ThrbQ9duX9qx6FlXxQYiE1TKg6nhb8n9m3-BaIZAjJr5qu1JFI6LRMLAcAVw" alt="Perfil" />
                        </div>
                    </div>
                </div>
            </header>
            <!-- END: TopHeader -->

            <!-- Content Body -->
            <main class="content-body custom-scrollbar">

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981;">
                        <i class="fa-solid fa-circle-check me-2"></i><strong>¡Éxito!</strong> Lote registrado e inventario del producto actualizado correctamente.
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444;">
                        <i class="fa-solid fa-circle-xmark me-2"></i><strong>¡Error!</strong> No se pudo registrar el lote. Por favor, intente de nuevo.
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Summary Metrics Bento (Movimientos de hoy omitido) -->
                <div class="row g-4 mb-4">
                    <!-- Metric 1 -->
                    <div class="col-12 col-md-4">
                        <div class="metric-card card-primary" id="cardTotalStock" style="cursor: pointer;">
                            <div>
                                <p class="metric-title">Total Stock en Bodega</p>
                                <h3 class="metric-value"><?php echo $total_stock; ?></h3>
                                <p class="metric-subtitle text-primary-custom">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">trending_up</span> Ver todo el stock
                                </p>
                            </div>
                            <div class="metric-icon-box bg-primary-box">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric 2 -->
                    <div class="col-12 col-md-4">
                        <div class="metric-card card-tertiary" id="cardLotesPorVencer" style="cursor: pointer;">
                            <div>
                                <p class="metric-title">Lotes por Vencer</p>
                                <h3 class="metric-value text-tertiary-custom"><?php echo $lotes_por_vencer; ?></h3>
                                <p class="metric-subtitle text-tertiary-custom">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">warning</span> Filtrar próximos a vencer
                                </p>
                            </div>
                            <div class="metric-icon-box bg-tertiary-box">
                                <span class="material-symbols-outlined">timer</span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric 3 -->
                    <div class="col-12 col-md-4">
                        <div class="metric-card card-secondary">
                            <div>
                                <p class="metric-title">Bodegas Activas</p>
                                <h3 class="metric-value"><?php echo str_pad($bodegas_activas, 2, '0', STR_PAD_LEFT); ?></h3>
                                <p class="metric-subtitle text-muted">Capacidad actual: 45%</p>
                            </div>
                            <div class="metric-icon-box bg-secondary-box">
                                <span class="material-symbols-outlined">location_on</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Layout: Sidebar Filters + Main Table -->
                <div class="row g-4 align-items-start">

                    <!-- Filter Panel -->
                    <div class="col-12 col-lg-3">
                        <aside class="filter-panel">
                            <div class="filter-title-section">
                                <span class="material-symbols-outlined text-primary-custom">filter_list</span>
                                <h4 class="metric-title mb-0">Filtros de Búsqueda</h4>
                            </div>

                            <form class="space-y-4" id="filterForm">
                                <div class="mb-3">
                                    <label class="filter-label">Bodega</label>
                                    <select class="filter-input" id="filterBodega">
                                        <option value="">Todas las bodegas</option>
                                        <option value="Bodega Principal - Managua">Bodega Principal - Managua</option>
                                        <option value="Depósito Norte">Depósito Norte</option>
                                        <option value="Bodega Externa C-4">Bodega Externa C-4</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="filter-label">Categoría</label>
                                    <select class="filter-input" id="filterCategoria">
                                        <option value="">Todas las categorías</option>
                                        <?php if (!empty($categories_list)): ?>
                                            <?php foreach ($categories_list as $cat): ?>
                                                <option value="<?php echo htmlspecialchars($cat['nombre_categoria']); ?>">
                                                    <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="filter-label">Rango de Fechas</label>
                                    <input class="filter-input mb-2" id="filterDesde" placeholder="Desde" type="date" />
                                    <input class="filter-input" id="filterHasta" placeholder="Hasta" type="date" />
                                </div>
                                <button type="button" class="btn-apply mb-2" id="btnApplyFilters">Aplicar Filtros</button>
                                <button type="button" class="btn-clear" id="btnClearFilters">Limpiar Búsqueda</button>
                            </form>
                        </aside>
                    </div>

                    <!-- Data Table Section -->
                    <div class="col-12 col-lg-9">
                        <div class="data-section">

                            <div class="table-controls d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div class="search-container">
                                    <span class="material-symbols-outlined search-icon">search</span>
                                    <input class="search-input" id="searchBar" placeholder="Buscar por código o producto..." type="text" />
                                </div>
                                <div class="d-flex gap-2 w-100 w-md-auto">
                                    <button class="btn-primary-custom flex-grow-1 flex-md-grow-0 justify-content-center" data-bs-toggle="modal" data-bs-target="#nuevoLoteModal">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">add_circle</span>
                                        NUEVO INGRESO
                                    </button>
                                    <div class="dropdown d-flex flex-grow-1 flex-md-grow-0">
                                        <button class="btn-secondary-custom dropdown-toggle w-100 justify-content-center" type="button" id="dropdownExportar" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
                                            EXPORTAR
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="dropdownExportar" style="background-color: #1e293b; border: 1px solid #334155;">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="../../controllers/bodega/exportar.php?format=excel">
                                                    <i class="fa-solid fa-file-excel text-success" style="font-size: 16px;"></i>
                                                    <span style="font-size: 13px; font-weight: 500;">Exportar a Excel</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="../../controllers/bodega/exportar.php?format=pdf" target="_blank">
                                                    <i class="fa-solid fa-file-pdf text-danger" style="font-size: 16px;"></i>
                                                    <span style="font-size: 13px; font-weight: 500;">Exportar a PDF</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-dark table-borderless align-middle mb-0 w-100" id="inventoryTable">
                                    <thead>
                                        <tr class="table-custom-header">
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Laboratorio</th>
                                            <th>Cantidad</th>
                                            <th>Fecha Ingreso</th>
                                            <th>Vencimiento</th>
                                            <th class="text-center">Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($lotes_list) > 0): ?>
                                            <?php foreach ($lotes_list as $lote):
                                                // Calcular estado del lote
                                                $fecha_vencimiento = $lote['fecha_vencimiento'];
                                                $hoy = date('Y-m-d');
                                                $diff = strtotime($fecha_vencimiento) - strtotime($hoy);
                                                $dias = round($diff / (60 * 60 * 24));

                                                if ($dias < 0) {
                                                    $estado = 'Vencido';
                                                    $badge_class = 'badge-vencido';
                                                } elseif ($dias <= 30) {
                                                    $estado = 'Próximo a Vencer';
                                                    $badge_class = 'badge-proximo';
                                                } else {
                                                    $estado = 'Disponible';
                                                    $badge_class = 'badge-disponible';
                                                }

                                                $fecha_venc_formateada = date('d M Y', strtotime($fecha_vencimiento));
                                                $fecha_ingreso_formateada = date('d M Y', strtotime($lote['fecha_creacion']));
                                            ?>
                                                <tr data-bodega="<?php echo htmlspecialchars($lote['bodega']); ?>" data-categoria="<?php echo htmlspecialchars($lote['nombre_categoria']); ?>" data-vencimiento="<?php echo htmlspecialchars($lote['fecha_vencimiento']); ?>">
                                                    <td class="font-mono-custom text-primary-custom"><?php echo htmlspecialchars($lote['numero_lote']); ?></td>
                                                    <td>
                                                        <p class="mb-0 font-semibold text-light" style="font-size: 14px; font-weight: 600;"><?php echo htmlspecialchars($lote['nombre_commercial']); ?></p>
                                                        <p class="mb-0 text-muted" style="font-size: 11px;">Medicamentos / <?php echo htmlspecialchars($lote['nombre_categoria']); ?> | <?php echo htmlspecialchars($lote['bodega']); ?></p>
                                                    </td>
                                                    <td class="text-light" style="font-size: 14px;"><?php echo htmlspecialchars($lote['nombre_laboratorio'] ?? 'No asignado'); ?></td>
                                                    <td class="text-light font-semibold"><?php echo htmlspecialchars($lote['cantidad_recibida']) . ' ' . htmlspecialchars($lote['unidad_medida']); ?></td>
                                                    <td class="text-light" style="font-size: 14px; color: #e2e8f0 !important;"><?php echo $fecha_ingreso_formateada; ?></td>
                                                    <td class="text-light <?php echo ($estado === 'Próximo a Vencer') ? 'text-tertiary-custom font-bold' : (($estado === 'Vencido') ? 'text-error-custom font-bold' : ''); ?>" style="font-size: 14px; <?php echo ($estado === 'Disponible') ? 'color: #e2e8f0 !important;' : ''; ?>"><?php echo $fecha_venc_formateada; ?></td>
                                                    <td class="text-center">
                                                        <span class="badge-status <?php echo $badge_class; ?>"><?php echo $estado; ?></span>
                                                    </td>
                                                    <td class="text-end">
                                                        <button class="btn btn-link text-muted p-0"><span class="material-symbols-outlined">more_vert</span></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">No hay lotes registrados en bodega.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="pagination-container">
                                <?php
                                $desde_item = ($total_lotes > 0) ? $offset + 1 : 0;
                                $hasta_item = min($offset + $lotes_por_pagina, $total_lotes);
                                ?>
                                <p class="mb-0 text-muted" style="font-size: 12px;">Mostrando <?php echo $desde_item; ?> a <?php echo $hasta_item; ?> de <?php echo $total_lotes; ?> lotes</p>
                                <div class="d-flex gap-1">
                                    <a href="?page=<?php echo $pagina_actual - 1; ?>" class="btn-page d-flex align-items-center justify-content-center <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>" style="text-decoration: none;">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">chevron_left</span>
                                    </a>
                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>" class="btn-page d-flex align-items-center justify-content-center <?php echo ($pagina_actual == $i) ? 'active' : ''; ?>" style="text-decoration: none;"><?php echo $i; ?></a>
                                    <?php endfor; ?>
                                    <a href="?page=<?php echo $pagina_actual + 1; ?>" class="btn-page d-flex align-items-center justify-content-center <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>" style="text-decoration: none;">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">chevron_right</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Footer Meta -->
                <div class="footer-meta">
                    <p class="mb-0">© 2026 SISTEMA SIF </p>
                    <div class="d-flex gap-4">
                        <span>ESTADO DEL SERVIDOR: ÓPTIMO</span>
                        <span>VERSIÓN: 1.0</span>
                    </div>
                </div>

            </main>
        </div>

    </div>

    <!-- Bootstrap 5 Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Simulación de búsqueda en tiempo real
            const searchInput = document.getElementById('searchBar');
            searchInput.addEventListener('input', (e) => {
                const val = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#inventoryTable tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(val) ? '' : 'none';
                });
            });

            // Filtrado al hacer clic en "Lotes por Vencer"
            const cardVencer = document.getElementById('cardLotesPorVencer');
            if (cardVencer) {
                cardVencer.addEventListener('click', () => {
                    const rows = document.querySelectorAll('#inventoryTable tbody tr');
                    rows.forEach(row => {
                        const statusBadge = row.querySelector('.badge-status');
                        if (statusBadge) {
                            const statusText = statusBadge.innerText.toLowerCase();
                            const isExpiring = statusText.includes('próximo a vencer') || statusText.includes('vencido');
                            row.style.display = isExpiring ? '' : 'none';
                        }
                    });
                    document.getElementById('searchBar').value = 'Próximo a Vencer';
                });
            }

            // Restaurar filtro al hacer clic en "Total Stock en Bodega"
            const cardStock = document.getElementById('cardTotalStock');
            if (cardStock) {
                cardStock.addEventListener('click', () => {
                    const rows = document.querySelectorAll('#inventoryTable tbody tr');
                    rows.forEach(row => {
                        row.style.display = '';
                    });
                    document.getElementById('searchBar').value = '';
                });
            }

            // Filtrado desde el panel/tarjeta de búsqueda lateral (Aplicar Filtros)
            const btnApply = document.getElementById('btnApplyFilters');
            if (btnApply) {
                btnApply.addEventListener('click', () => {
                    const bodegaVal = document.getElementById('filterBodega').value;
                    const categoriaVal = document.getElementById('filterCategoria').value;
                    const desdeVal = document.getElementById('filterDesde').value;
                    const hastaVal = document.getElementById('filterHasta').value;

                    const rows = document.querySelectorAll('#inventoryTable tbody tr');
                    rows.forEach(row => {
                        const rowBodega = row.getAttribute('data-bodega');
                        const rowCategoria = row.getAttribute('data-categoria');
                        const rowVencimiento = row.getAttribute('data-vencimiento');

                        let show = true;

                        // Filtrar por Bodega
                        if (bodegaVal && rowBodega !== bodegaVal) {
                            show = false;
                        }

                        // Filtrar por Categoría
                        if (categoriaVal && rowCategoria !== categoriaVal) {
                            show = false;
                        }

                        // Filtrar por Rango de Fechas
                        if (rowVencimiento) {
                            const dateVenc = new Date(rowVencimiento);
                            if (desdeVal) {
                                const dateDesde = new Date(desdeVal);
                                if (dateVenc < dateDesde) show = false;
                            }
                            if (hastaVal) {
                                const dateHasta = new Date(hastaVal);
                                if (dateVenc > dateHasta) show = false;
                            }
                        }

                        row.style.display = show ? '' : 'none';
                    });
                });
            }

            // Limpiar filtros desde el panel lateral
            const btnClear = document.getElementById('btnClearFilters');
            if (btnClear) {
                btnClear.addEventListener('click', () => {
                    document.getElementById('filterBodega').value = '';
                    document.getElementById('filterCategoria').value = '';
                    document.getElementById('filterDesde').value = '';
                    document.getElementById('filterHasta').value = '';
                    document.getElementById('searchBar').value = '';

                    const rows = document.querySelectorAll('#inventoryTable tbody tr');
                    rows.forEach(row => {
                        row.style.display = '';
                    });
                });
            }

            // Alternar campos de producto existente vs nuevo en el modal
            const radioExistente = document.getElementById('tipo_existente');
            const radioNuevo = document.getElementById('tipo_nuevo');
            const groupExistente = document.getElementById('group_existente');
            const groupNuevo = document.getElementById('group_nuevo');

            const selectExistente = document.getElementById('id_producto_select');
            const inputCodigo = document.getElementById('codigo_barras');
            const inputNombre = document.getElementById('nombre_commercial');
            const selectCategoria = document.getElementById('id_categoria_new');
            const selectLaboratorio = document.getElementById('id_laboratorio_new');
            const selectUnidad = document.getElementById('unidad_medida');
            const inputPrecio = document.getElementById('precio_venta_actual');

            const groupNuevoLaboratorio = document.getElementById('group_nuevo_laboratorio');
            const inputNuevoLaboratorio = document.getElementById('nuevo_laboratorio_nombre');

            const groupNuevaCategoria = document.getElementById('group_nueva_categoria');
            const inputNuevaCategoria = document.getElementById('nueva_categoria_nombre');

            function toggleLaboratorioField() {
                if (radioNuevo.checked && selectLaboratorio.value === 'nuevo_laboratorio') {
                    groupNuevoLaboratorio.style.display = '';
                    inputNuevoLaboratorio.setAttribute('required', 'required');
                } else {
                    groupNuevoLaboratorio.style.display = 'none';
                    inputNuevoLaboratorio.removeAttribute('required');
                }
            }

            function toggleCategoriaField() {
                if (radioNuevo.checked && selectCategoria.value === 'nueva_categoria') {
                    groupNuevaCategoria.style.display = '';
                    inputNuevaCategoria.setAttribute('required', 'required');
                } else {
                    groupNuevaCategoria.style.display = 'none';
                    inputNuevaCategoria.removeAttribute('required');
                }
            }

            function toggleModalFields() {
                if (radioExistente.checked) {
                    groupExistente.style.display = '';
                    groupNuevo.style.display = 'none';

                    selectExistente.setAttribute('required', 'required');
                    inputCodigo.removeAttribute('required');
                    inputNombre.removeAttribute('required');
                    selectCategoria.removeAttribute('required');
                    selectLaboratorio.removeAttribute('required');
                    selectUnidad.removeAttribute('required');
                    inputPrecio.removeAttribute('required');

                    // Ocultar nuevo laboratorio y nueva categoría si se vuelve a existente
                    groupNuevoLaboratorio.style.display = 'none';
                    inputNuevoLaboratorio.removeAttribute('required');
                    groupNuevaCategoria.style.display = 'none';
                    inputNuevaCategoria.removeAttribute('required');
                } else {
                    groupExistente.style.display = 'none';
                    groupNuevo.style.display = '';

                    selectExistente.removeAttribute('required');
                    inputCodigo.setAttribute('required', 'required');
                    inputNombre.setAttribute('required', 'required');
                    selectCategoria.setAttribute('required', 'required');
                    selectLaboratorio.setAttribute('required', 'required');
                    selectUnidad.setAttribute('required', 'required');
                    inputPrecio.setAttribute('required', 'required');

                    toggleLaboratorioField();
                    toggleCategoriaField();
                }
            }

            if (radioExistente && radioNuevo) {
                radioExistente.addEventListener('change', toggleModalFields);
                radioNuevo.addEventListener('change', toggleModalFields);
            }
            if (selectLaboratorio) {
                selectLaboratorio.addEventListener('change', toggleLaboratorioField);
            }
            if (selectCategoria) {
                selectCategoria.addEventListener('change', toggleCategoriaField);
            }
        });
    </script>

    <?php include __DIR__ . '/partials/registro_producto_modal.php'; ?>
</body>

</html>