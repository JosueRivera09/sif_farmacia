<?php
/*
 * Archivo: views/productos/catalogo.php
 * Propósito: Muestra el listado de productos disponibles en el inventario.
 * Qué muestra: Tabla con el catálogo de productos, sus precios y stock, con búsqueda en tiempo real.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$es_admin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador';
?>
<div class="custom-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-secondary pb-3 mb-4 gap-3">
        <div>
            <h6 class="card-title-custom mb-0">Catálogo de Productos y Existencias</h6>
            <p class="text-muted mb-0" style="font-size: 12px;">Visualiza y gestiona los medicamentos del catálogo.</p>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div style="width: 250px;">
                <div class="input-group bg-slate border border-secondary" style="border-radius: 8px; overflow:hidden;">
                    <span class="input-group-text bg-transparent border-0 text-muted"><span class="material-symbols-outlined">search</span></span>
                    <input type="text" id="inventario-search" class="form-control bg-transparent border-0 text-light py-2" placeholder="Buscar medicamento o código..." autocomplete="off">
                </div>
            </div>
            <?php if ($es_admin): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarProductoModal">
                    <span class="material-symbols-outlined me-1" style="font-size: 18px;">add_circle</span>
                    Nuevo Producto
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre Comercial</th>
                    <th>Categoría / Laboratorio</th>
                    <th style="width: 100px; text-align: center;">Receta</th>
                    <th style="width: 120px; text-align: right;">Precio</th>
                    <th style="width: 120px; text-align: center;">Stock Disponible</th>
                    <?php if ($es_admin): ?>
                        <th style="width: 100px; text-align: center;">Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody id="inventario-table-body">
                <tr>
                    <td colspan="<?php echo $es_admin ? '7' : '6'; ?>" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                        Cargando catálogo...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php if ($es_admin): ?>
<!-- Modal Agregar Producto Placeholder / Gestión -->
<div class="modal fade" id="agregarProductoModal" tabindex="-1" aria-labelledby="agregarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #1e293b; color: #f8fafc; border: 1px solid #334155;">
            <div class="modal-header" style="border-bottom: 1px solid #334155;">
                <h5 class="modal-title" id="agregarProductoModalLabel">
                    <span class="material-symbols-outlined me-2 text-primary-custom">inventory_2</span>Nuevo Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" style="background-color: rgba(59, 130, 246, 0.15); border: 1px solid #3b82f6; color: #93c5fd;">
                    <span class="material-symbols-outlined me-2 align-middle">info</span>
                    Para agregar nuevos productos con su stock correspondiente, por favor dirígete a la sección de **Bodega y Lotes** y utiliza la opción de **Nuevo Ingreso**.
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #334155;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Entendido</button>
                <a href="../bodega/bodega_lotes.php" class="btn btn-primary text-decoration-none">
                    <span class="material-symbols-outlined me-1" style="font-size: 18px;">warehouse</span>Ir a Bodega
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
{
    let inventoryData = [];
    const esAdmin = <?php echo $es_admin ? 'true' : 'false'; ?>;
    const searchInput = document.getElementById('inventario-search');
    const tbody = document.getElementById('inventario-table-body');

    function cargarInventarioShared() {
        fetch('../../controllers/vendedor/VentaController.php?action=listar')
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    inventoryData = response.data;
                    renderInventory(inventoryData);
                } else {
                    tbody.innerHTML = `<tr><td colspan="${esAdmin ? 7 : 6}" class="text-center text-danger py-4">Error: ${response.message}</td></tr>`;
                }
            })
            .catch(err => {
                console.error("Error al cargar inventario:", err);
                tbody.innerHTML = `<tr><td colspan="${esAdmin ? 7 : 6}" class="text-center text-danger py-4">Error de conexión al servidor.</td></tr>`;
            });
    }

    function formatStockDesglosado(p) {
        const totalUnidades = parseInt(p.stock_actual) || 0;
        const stockMinimo = parseInt(p.stock_minimo) || 10;
        const factorPrincipal = parseInt(p.unidades_totales_por_empaque_principal) || 1;
        const factorMedio = parseInt(p.unidades_por_empaque_medio) || 1;

        if (totalUnidades <= 0) {
            return `
                <div>
                    <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-0.5 font-bold mb-1" style="font-size: 10px;">Agotado</span>
                    <div class="text-danger font-bold" style="font-size: 13px;">0 ${p.unidad_minima}s</div>
                </div>
            `;
        }

        let partes = [];
        let resto = totalUnidades;

        if (factorPrincipal > 1 && p.empaque_principal) {
            const cajas = Math.floor(resto / factorPrincipal);
            if (cajas > 0) {
                partes.push(`<strong>${cajas}</strong> ${p.empaque_principal}${cajas > 1 ? 's' : ''}`);
                resto = resto % factorPrincipal;
            }
        }

        if (p.empaque_medio && factorMedio > 1 && factorMedio < factorPrincipal) {
            const medio = Math.floor(resto / factorMedio);
            if (medio > 0) {
                partes.push(`<strong>${medio}</strong> ${p.empaque_medio}${medio > 1 ? 's' : ''}`);
                resto = resto % factorMedio;
            }
        }

        if (resto > 0 || partes.length === 0) {
            partes.push(`<strong>${resto}</strong> ${p.unidad_minima}${resto > 1 ? 's' : ''}`);
        }

        const textoDesglose = partes.join(', ');
        const isCritical = totalUnidades <= stockMinimo;
        const badgeClass = isCritical ? 'bg-danger-subtle text-danger border border-danger' : 'bg-success-subtle text-success border border-success';
        const estadoTexto = isCritical ? 'Crítico' : 'Disponible';

        return `
            <div>
                <span class="badge ${badgeClass} px-2 py-0.5 font-bold mb-1" style="font-size: 10px;">${estadoTexto}</span>
                <div class="text-light font-bold" style="font-size: 13px;">${textoDesglose}</div>
                <small class="text-muted font-monospace" style="font-size: 11px;">(${totalUnidades.toLocaleString()} ${p.unidad_minima}s tot.)</small>
            </div>
        `;
    }

    function renderInventory(products) {
        if (products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${esAdmin ? 7 : 6}" class="text-center text-muted py-4">No se encontraron productos coincidentes.</td></tr>`;
            return;
        }

        let html = '';
        products.forEach(p => {
            const recetaBadge = p.requiere_receta 
                ? '<span class="badge bg-danger-subtle text-danger px-2 py-1 font-bold" style="font-size: 10px;">Requiere Receta</span>' 
                : '<span class="badge bg-success-subtle text-success px-2 py-1 font-bold" style="font-size: 10px;">Libre Venta</span>';
            
            const stockHtml = formatStockDesglosado(p);

            let actionColumn = '';
            if (esAdmin) {
                actionColumn = `
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-info me-1" onclick="alert('Funcionalidad de edición disponible en la sección de Bodega y Lotes para este producto: ${p.nombre_commercial}')" title="Editar Producto">
                            <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">edit</span>
                        </button>
                    </td>
                `;
            }

            // Crear desglose de precios según niveles disponibles
            let preciosHtml = `<div><strong>C$ ${p.precio_empaque_principal.toFixed(2)}</strong> <small class="text-muted">(${p.empaque_principal})</small></div>`;
            if (p.empaque_medio && p.precio_empaque_medio !== null && p.precio_empaque_medio > 0) {
                preciosHtml += `<div>C$ ${p.precio_empaque_medio.toFixed(2)} <small class="text-muted">(${p.empaque_medio})</small></div>`;
            }
            if (p.es_fraccionable && p.precio_unidad_minima > 0) {
                preciosHtml += `<div>C$ ${p.precio_unidad_minima.toFixed(2)} <small class="text-muted">(${p.unidad_minima})</small></div>`;
            }

            let empaquesDesc = `${p.empaque_principal} de ${p.unidades_totales_por_empaque_principal} ${p.unidad_minima}`;
            if (p.empaque_medio) {
                empaquesDesc += ` (Blíster: ${p.unidades_por_empaque_medio} ${p.unidad_minima})`;
            }

            html += `
                <tr>
                    <td><code class="text-secondary">${p.codigo_barras}</code></td>
                    <td class="font-bold text-light">${p.nombre_commercial} <small class="text-muted d-block">${p.miligramos ? p.miligramos + 'mg' : ''} / ${empaquesDesc}</small></td>
                    <td class="text-light">${p.nombre_categoria} <small class="text-muted d-block">${p.nombre_laboratorio}</small></td>
                    <td class="text-center">${recetaBadge}</td>
                    <td class="text-end text-light" style="font-size: 12.5px;">${preciosHtml}</td>
                    <td class="text-center">${stockHtml}</td>
                    ${actionColumn}
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase().trim();
        const filtered = inventoryData.filter(p => 
            p.nombre_commercial.toLowerCase().includes(query) || 
            p.codigo_barras.includes(query) ||
            p.nombre_categoria.toLowerCase().includes(query) ||
            p.nombre_laboratorio.toLowerCase().includes(query)
        );
        renderInventory(filtered);
    });

    cargarInventarioShared();
}
</script>
