<?php
$products_list = isset($products_list) ? $products_list : [];
$categories_list = isset($categories_list) ? $categories_list : [];
$laboratories_list = isset($laboratories_list) ? $laboratories_list : [];
?>

<!-- Modal para Nuevo Lote -->
<div class="modal fade" id="nuevoLoteModal" tabindex="-1" aria-labelledby="nuevoLoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background-color: #1e293b; color: #f8fafc; border: 1px solid #334155;">
            <div class="modal-header" style="border-bottom: 1px solid #334155;">
                <h5 class="modal-title" id="nuevoLoteModalLabel"><span class="material-symbols-outlined me-2 text-primary-custom">add_box</span>Nuevo Ingreso de Lote</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controllers/bodega/lote_process.php" method="POST">
                <div class="modal-body">
                    <div class="mb-4 p-3 rounded-4" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-primary-custom">widgets</span>
                            <h6 class="mb-0 filter-label" style="font-size: 13px; color: #10b981;">Paso 1 · Elige cómo registrar</h6>
                        </div>
                        <p class="mb-3 text-muted" style="font-size: 13px;">Selecciona la opción que mejor se adapte al producto que vas a ingresar.</p>
                        <div class="d-flex flex-column flex-md-row gap-3">
                            <label for="tipo_existente" class="w-100" style="cursor: pointer;">
                                <div class="p-3 rounded-3 h-100" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(59, 130, 246, 0.35);">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="tipo_ingreso" id="tipo_existente" value="existente" checked>
                                        <div>
                                            <div class="fw-semibold text-light">Producto existente</div>
                                            <small class="text-muted d-block">Agrega stock a un producto que ya está en el catálogo.</small>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label for="tipo_nuevo" class="w-100" style="cursor: pointer;">
                                <div class="p-3 rounded-3 h-100" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(16, 185, 129, 0.35);">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="tipo_ingreso" id="tipo_nuevo" value="nuevo">
                                        <div>
                                            <div class="fw-semibold text-light">Producto nuevo</div>
                                            <small class="text-muted d-block">Crea un nuevo producto y registra su primer lote en un solo paso.</small>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="group_existente" class="mb-4 p-3 rounded-4" style="background: rgba(15, 23, 42, 0.45); border: 1px solid rgba(148, 163, 184, 0.2);">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-primary-custom">inventory_2</span>
                            <h6 class="mb-0 filter-label" style="font-size: 13px; color: #f8fafc;">Paso 2 · Selecciona el producto</h6>
                        </div>
                        <label for="id_producto_select" class="filter-label">Producto disponible</label>
                        <select class="filter-input" name="id_producto" id="id_producto_select" required>
                            <option value="">-- Seleccione un Producto --</option>
                            <?php foreach ($products_list as $prod): ?>
                                <option value="<?php echo $prod['id_producto']; ?>">
                                    <?php echo htmlspecialchars($prod['nombre_commercial']); ?> (<?php echo htmlspecialchars($prod['codigo_barras']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="group_nuevo" style="display: none;">
                        <div class="p-3 rounded-4 mb-4" style="background: rgba(15, 23, 42, 0.45); border: 1px solid rgba(148, 163, 184, 0.2);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-primary-custom">add_circle</span>
                                <h6 class="mb-0 filter-label" style="font-size: 13px; color: #10b981;">Paso 2 · Datos del producto</h6>
                            </div>
                            <p class="mb-3 text-muted" style="font-size: 13px;">Completa los datos básicos del catálogo para crear el producto y registrarlo con su primer lote.</p>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="codigo_barras" class="filter-label">Código de Barras</label>
                                    <input type="text" class="filter-input" name="codigo_barras" id="codigo_barras" placeholder="Ej: 7701234567890" />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="nombre_commercial" class="filter-label">Nombre Comercial</label>
                                    <input type="text" class="filter-input" name="nombre_commercial" id="nombre_commercial" placeholder="Ej: Ibuprofeno 400mg" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="id_categoria_new" class="filter-label">Categoría del Producto</label>
                                    <select class="filter-input" name="id_categoria" id="id_categoria_new">
                                        <option value="">-- Seleccione una Categoría --</option>
                                        <?php foreach ($categories_list as $cat): ?>
                                            <option value="<?php echo $cat['id_categoria']; ?>">
                                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="nueva_categoria">-- Registrar Nueva Categoría --</option>
                                    </select>
                                    <div id="group_nueva_categoria" class="mt-2" style="display: none;">
                                        <input type="text" class="filter-input" name="nueva_categoria_nombre" id="nueva_categoria_nombre" placeholder="Nombre de la nueva categoría..." />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="id_laboratorio_new" class="filter-label">Laboratorio Maestro</label>
                                    <select class="filter-input" name="id_laboratorio" id="id_laboratorio_new">
                                        <option value="">-- Seleccione un Laboratorio --</option>
                                        <?php foreach ($laboratories_list as $lab): ?>
                                            <option value="<?php echo $lab['id_laboratorio']; ?>">
                                                <?php echo htmlspecialchars($lab['nombre_laboratorio']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="nuevo_laboratorio">-- Registrar Nuevo Laboratorio --</option>
                                    </select>
                                    <div id="group_nuevo_laboratorio" class="mt-2" style="display: none;">
                                        <input type="text" class="filter-input" name="nuevo_laboratorio_nombre" id="nuevo_laboratorio_nombre" placeholder="Nombre del nuevo laboratorio..." />
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="tipo_producto" class="filter-label">Tipo de Producto</label>
                                    <input type="text" class="filter-input" name="tipo_producto" id="tipo_producto" placeholder="Ej: Analgésico, Antibiótico, Inyectable..." />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="miligramos" class="filter-label">Miligramos (mg)</label>
                                    <input type="number" class="filter-input" name="miligramos" id="miligramos" placeholder="Ej: 500 (Opcional)" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="empaque_principal" class="filter-label">Empaque Principal</label>
                                    <input type="text" class="filter-input" name="empaque_principal" id="empaque_principal" placeholder="Ej: Caja, Bote" required />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="empaque_medio" class="filter-label">Empaque Medio (Opcional)</label>
                                    <input type="text" class="filter-input" name="empaque_medio" id="empaque_medio" placeholder="Ej: Blister, Sobre" />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="unidad_minima" class="filter-label">Unidad Mínima</label>
                                    <input type="text" class="filter-input" name="unidad_minima" id="unidad_minima" placeholder="Ej: Tableta, Cápsula" required />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="unidades_totales_por_empaque_principal" class="filter-label">Unidades Totales por Empaque Principal</label>
                                    <input type="number" min="1" class="filter-input" name="unidades_totales_por_empaque_principal" id="unidades_totales_por_empaque_principal" placeholder="Ej: 100" required />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="unidades_por_empaque_medio" class="filter-label">Unidades por Empaque Medio</label>
                                    <input type="number" min="1" class="filter-input" name="unidades_por_empaque_medio" id="unidades_por_empaque_medio" placeholder="Ej: 10 (Si usa blister)" value="1" required />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="precio_empaque_principal" class="filter-label">Precio Empaque Principal</label>
                                    <input type="number" step="0.01" min="0" class="filter-input" name="precio_empaque_principal" id="precio_empaque_principal" placeholder="Ej: 500.00" required />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="precio_empaque_medio" class="filter-label">Precio Empaque Medio</label>
                                    <input type="number" step="0.01" min="0" class="filter-input" name="precio_empaque_medio" id="precio_empaque_medio" placeholder="Ej: 50.00" />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="precio_unidad_minima" class="filter-label">Precio Unidad Mínima</label>
                                    <input type="number" step="0.01" min="0" class="filter-input" name="precio_unidad_minima" id="precio_unidad_minima" placeholder="Ej: 5.00" required />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="stock_minimo" class="filter-label">Stock Mínimo (En unidades mínimas)</label>
                                    <input type="number" min="0" class="filter-input" name="stock_minimo" id="stock_minimo" placeholder="Ej: 50" value="0" />
                                </div>
                                <div class="col-12 col-md-6 mb-3 d-flex align-items-end pb-2">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input m-0" name="es_fraccionable" id="es_fraccionable" value="1">
                                        <label class="form-check-label filter-label m-0" for="es_fraccionable" style="text-transform:none; font-size:14px; color: #f8fafc;">¿Se puede fraccionar/vender suelto?</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 mb-3">
                                    <label for="descripcion" class="filter-label">Descripción</label>
                                    <textarea class="filter-input" name="descripcion" id="descripcion" rows="2" placeholder="Detalles u observaciones del producto..."></textarea>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 mb-3">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input m-0" name="requiere_receta" id="requiere_receta" value="1">
                                        <label class="form-check-label filter-label m-0" for="requiere_receta" style="text-transform:none; font-size:14px; color: #f8fafc;">Requiere Receta Médica</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-4" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.22);">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-primary-custom">warehouse</span>
                            <h6 class="mb-0 filter-label" style="font-size: 13px; color: #10b981;">Paso 3 · Datos del lote</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-4 mb-3">
                                <label for="numero_lote" class="filter-label">Número de Lote</label>
                                <input type="text" class="filter-input" name="numero_lote" id="numero_lote" placeholder="Ej: LOT-55291" required />
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="empaque_ingreso" class="filter-label">Empaque de Ingreso</label>
                                <select class="filter-input" name="empaque_ingreso" id="empaque_ingreso" required>
                                    <option value="Principal">Empaque Principal (Caja, etc)</option>
                                    <option value="Medio">Empaque Medio (Blíster, etc)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="cantidad_empaques_recibidos" class="filter-label">Cantidad de Empaques Recibidos</label>
                                <input type="number" min="1" class="filter-input" name="cantidad_empaques_recibidos" id="cantidad_empaques_recibidos" placeholder="Ej: 100" required />
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="fecha_vencimiento" class="filter-label">Fecha de Vencimiento</label>
                                <input type="date" class="filter-input" name="fecha_vencimiento" id="fecha_vencimiento" required />
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="bodega" class="filter-label">Seleccionar Bodega de Destino</label>
                                <select class="filter-input" name="bodega" id="bodega" required>
                                    <option value="Bodega Principal - Managua">Bodega Principal - Managua</option>
                                    <option value="Depósito Norte">Depósito Norte</option>
                                    <option value="Bodega Externa C-4">Bodega Externa C-4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #334155; padding-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">close</span> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">save</span> Guardar Lote
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
