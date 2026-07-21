<?php
$products_list = isset($products_list) ? $products_list : [];
$categories_list = isset($categories_list) ? $categories_list : [];
$laboratories_list = isset($laboratories_list) ? $laboratories_list : [];
?>

<!-- Modal para Editar Producto -->
<div class="modal fade" id="editarProductoModal" tabindex="-1" aria-labelledby="editarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background-color: #1e293b; color: #f8fafc; border: 1px solid #334155; border-radius: 12px;">
            <div class="modal-header" style="border-bottom: 1px solid #334155;">
                <h5 class="modal-title" id="editarProductoModalLabel">
                    <span class="material-symbols-outlined me-2 text-info" style="vertical-align: middle;">edit</span>Editar Información de Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controllers/bodega/lote_process.php" method="POST">
                <input type="hidden" name="action" value="editar_producto">
                <input type="hidden" name="id_producto" id="edit_id_producto">

                <div class="modal-body">
                    <!-- Paso 1: Seleccionar Producto -->
                    <div class="mb-4 p-3 rounded-4" style="background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.25);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-info">search</span>
                            <h6 class="mb-0 filter-label" style="font-size: 13px; color: #0ea5e9;">Selecciona el producto a editar</h6>
                        </div>
                        <select class="filter-input" id="edit_producto_selector" required>
                            <option value="">-- Eliga un Producto --</option>
                            <?php foreach ($products_list as $prod): ?>
                                <option value="<?php echo $prod['id_producto']; ?>">
                                    <?php echo htmlspecialchars($prod['nombre_commercial']); ?> (<?php echo htmlspecialchars($prod['codigo_barras']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campos del producto (inicialmente deshabilitados hasta seleccionar producto) -->
                    <div id="edit_fields_container" style="opacity: 0.5; pointer-events: none;">
                        <div class="p-3 rounded-4 mb-3" style="background: rgba(15, 23, 42, 0.45); border: 1px solid rgba(148, 163, 184, 0.2);">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-info">inventory_2</span>
                                <h6 class="mb-0 filter-label" style="font-size: 13px; color: #f8fafc;">Datos del catálogo</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_codigo_barras" class="filter-label">Código de Barras</label>
                                    <input type="text" class="filter-input" name="codigo_barras" id="edit_codigo_barras" required placeholder="Ej: 7701234567890" />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_nombre_commercial" class="filter-label">Nombre Comercial</label>
                                    <input type="text" class="filter-input" name="nombre_commercial" id="edit_nombre_commercial" required placeholder="Ej: Ibuprofeno 400mg" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_id_categoria" class="filter-label">Categoría del Producto</label>
                                    <select class="filter-input" name="id_categoria" id="edit_id_categoria" required>
                                        <option value="">-- Seleccione una Categoría --</option>
                                        <?php foreach ($categories_list as $cat): ?>
                                            <option value="<?php echo $cat['id_categoria']; ?>">
                                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_id_laboratorio" class="filter-label">Laboratorio Maestro</label>
                                    <select class="filter-input" name="id_laboratorio" id="edit_id_laboratorio" required>
                                        <option value="">-- Seleccione un Laboratorio --</option>
                                        <?php foreach ($laboratories_list as $lab): ?>
                                            <option value="<?php echo $lab['id_laboratorio']; ?>">
                                                <?php echo htmlspecialchars($lab['nombre_laboratorio']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_tipo_producto" class="filter-label">Tipo de Producto</label>
                                    <input type="text" class="filter-input" name="tipo_producto" id="edit_tipo_producto" required placeholder="Ej: Analgésico, Antibiótico..." />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_miligramos" class="filter-label">Miligramos (mg)</label>
                                    <input type="number" class="filter-input" name="miligramos" id="edit_miligramos" placeholder="Ej: 500 (Opcional)" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="edit_empaque_principal" class="filter-label">Empaque Principal</label>
                                    <input type="text" class="filter-input" name="empaque_principal" id="edit_empaque_principal" placeholder="Ej: Caja, Bote" required />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="edit_empaque_medio" class="filter-label">Empaque Medio (Opcional)</label>
                                    <input type="text" class="filter-input" name="empaque_medio" id="edit_empaque_medio" placeholder="Ej: Blister, Sobre" />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="edit_unidad_minima" class="filter-label">Unidad Mínima</label>
                                    <input type="text" class="filter-input" name="unidad_minima" id="edit_unidad_minima" placeholder="Ej: Tableta, Cápsula" required />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_unidades_totales_por_empaque_principal" class="filter-label">Unidades Totales por Empaque Principal</label>
                                    <input type="number" min="1" class="filter-input" name="unidades_totales_por_empaque_principal" id="edit_unidades_totales_por_empaque_principal" placeholder="Ej: 100" required />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_unidades_por_empaque_medio" class="filter-label">Unidades por Empaque Medio</label>
                                    <input type="number" min="1" class="filter-input" name="unidades_por_empaque_medio" id="edit_unidades_por_empaque_medio" placeholder="Ej: 10" value="1" required />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="edit_precio_empaque_principal" class="filter-label">Precio Empaque Principal</label>
                                    <input type="number" step="0.01" min="0" class="filter-input" name="precio_empaque_principal" id="edit_precio_empaque_principal" placeholder="Ej: 500.00" required />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="edit_precio_empaque_medio" class="filter-label">Precio Empaque Medio</label>
                                    <input type="number" step="0.01" min="0" class="filter-input" name="precio_empaque_medio" id="edit_precio_empaque_medio" placeholder="Ej: 50.00" />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="edit_precio_unidad_minima" class="filter-label">Precio Unidad Mínima</label>
                                    <input type="number" step="0.01" min="0" class="filter-input" name="precio_unidad_minima" id="edit_precio_unidad_minima" placeholder="Ej: 5.00" required />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_stock_minimo" class="filter-label">Stock Mínimo (En unidades mínimas)</label>
                                    <input type="number" min="0" class="filter-input" name="stock_minimo" id="edit_stock_minimo" placeholder="Ej: 50" value="0" />
                                </div>
                                <div class="col-12 col-md-6 mb-3 d-flex align-items-end pb-2">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input m-0" name="es_fraccionable" id="edit_es_fraccionable" value="1">
                                        <label class="form-check-label filter-label m-0" for="edit_es_fraccionable" style="text-transform:none; font-size:14px; color: #f8fafc;">¿Se puede fraccionar/vender suelto?</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 mb-3">
                                    <label for="edit_descripcion" class="filter-label">Descripción</label>
                                    <textarea class="filter-input" name="descripcion" id="edit_descripcion" rows="2" placeholder="Detalles u observaciones del producto..."></textarea>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 mb-3">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input m-0" name="requiere_receta" id="edit_requiere_receta" value="1">
                                        <label class="form-check-label filter-label m-0" for="edit_requiere_receta" style="text-transform:none; font-size:14px; color: #f8fafc;">Requiere Receta Médica</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #334155; padding-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">close</span> Cancelar
                    </button>
                    <button type="submit" class="btn btn-info text-white" id="btn_save_edit" disabled>
                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">save</span> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
{
    const selector = document.getElementById('edit_producto_selector');
    const fieldsContainer = document.getElementById('edit_fields_container');
    const btnSave = document.getElementById('btn_save_edit');

    selector.addEventListener('change', function() {
        const id_producto = selector.value;
        if (!id_producto) {
            // Deshabilitar formulario si no hay selección
            fieldsContainer.style.opacity = '0.5';
            fieldsContainer.style.pointerEvents = 'none';
            btnSave.disabled = true;
            document.getElementById('edit_id_producto').value = '';
            return;
        }

        // Cargar datos vía AJAX
        fetch(`../../controllers/bodega/lote_process.php?action=obtener_producto&id=${id_producto}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const p = res.data;
                    document.getElementById('edit_id_producto').value = p.id_producto;
                    document.getElementById('edit_codigo_barras').value = p.codigo_barras;
                    document.getElementById('edit_nombre_commercial').value = p.nombre_commercial;
                    document.getElementById('edit_id_categoria').value = p.id_categoria;
                    document.getElementById('edit_id_laboratorio').value = p.id_laboratorio;
                    document.getElementById('edit_tipo_producto').value = p.tipo_producto;
                    document.getElementById('edit_miligramos').value = p.miligramos !== null ? p.miligramos : '';
                    document.getElementById('edit_empaque_principal').value = p.empaque_principal;
                    document.getElementById('edit_empaque_medio').value = p.empaque_medio !== null ? p.empaque_medio : '';
                    document.getElementById('edit_unidad_minima').value = p.unidad_minima;
                    document.getElementById('edit_unidades_totales_por_empaque_principal').value = p.unidades_totales_por_empaque_principal;
                    document.getElementById('edit_unidades_por_empaque_medio').value = p.unidades_por_empaque_medio;
                    document.getElementById('edit_precio_empaque_principal').value = p.precio_empaque_principal;
                    document.getElementById('edit_precio_empaque_medio').value = p.precio_empaque_medio !== null ? p.precio_empaque_medio : '';
                    document.getElementById('edit_precio_unidad_minima').value = p.precio_unidad_minima;
                    document.getElementById('edit_stock_minimo').value = p.stock_minimo;
                    document.getElementById('edit_descripcion').value = p.descripcion !== null ? p.descripcion : '';
                    
                    document.getElementById('edit_es_fraccionable').checked = (p.es_fraccionable == 1);
                    document.getElementById('edit_requiere_receta').checked = (p.requiere_receta == 1);

                    // Habilitar campos
                    fieldsContainer.style.opacity = '1';
                    fieldsContainer.style.pointerEvents = 'auto';
                    btnSave.disabled = false;
                } else {
                    alert("Error: " + res.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Ocurrió un error al cargar el producto");
            });
    });
}
</script>
