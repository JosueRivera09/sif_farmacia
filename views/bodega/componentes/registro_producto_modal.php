<?php
/*
 * Archivo: views/bodega/componentes/registro_producto_modal.php
 * Propósito: Modal para registrar un nuevo producto en bodega.
 */

$products_list = isset($products_list) ? $products_list : [];
$categories_list = isset($categories_list) ? $categories_list : [];
$laboratories_list = isset($laboratories_list) ? $laboratories_list : [];
?>

<!-- Modal para Nuevo Lote -->
<div class="modal fade" id="nuevoLoteModal" tabindex="-1" aria-labelledby="nuevoLoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background-color: #ffffff !important; color: #0f172a !important; border: 2px solid #10b981 !important; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <div class="modal-header py-3" style="border-bottom: 2px solid #10b981 !important; background-color: #f8fafc !important;">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="nuevoLoteModalLabel">
                    <span class="material-symbols-outlined text-success fs-4">add_box</span>Nuevo Ingreso de Lote
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controllers/bodega/lote_process.php" method="POST">
                <div class="modal-body p-4" style="background-color: #ffffff !important;">
                    <!-- Paso 1 -->
                    <div class="mb-4 p-3 rounded-4" style="background-color: #f8fafc !important; border: 1px solid #10b981 !important;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-success">widgets</span>
                            <h6 class="mb-0 filter-label fw-bold" style="font-size: 13px; color: #059669 !important;">Paso 1 · Elige cómo registrar</h6>
                        </div>
                        <p class="mb-3 text-secondary" style="font-size: 13px;">Selecciona la opción que mejor se adapte al producto que vas a ingresar.</p>
                        <div class="d-flex flex-column flex-md-row gap-3">
                            <label for="tipo_existente" class="w-100" style="cursor: pointer;">
                                <div class="p-3 rounded-3 h-100" style="background-color: #ffffff !important; border: 1.5px solid #10b981 !important;">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="tipo_ingreso" id="tipo_existente" value="existente" checked>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 14px;">Producto existente</div>
                                            <small class="text-secondary d-block" style="font-size: 12px;">Agrega stock a un producto que ya está en el catálogo.</small>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label for="tipo_nuevo" class="w-100" style="cursor: pointer;">
                                <div class="p-3 rounded-3 h-100" style="background-color: #ffffff !important; border: 1.5px solid #10b981 !important;">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="tipo_ingreso" id="tipo_nuevo" value="nuevo">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 14px;">Producto nuevo</div>
                                            <small class="text-secondary d-block" style="font-size: 12px;">Crea un nuevo producto y registra su primer lote en un solo paso.</small>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Paso 2 Existente -->
                    <div id="group_existente" class="mb-4 p-3 rounded-4" style="background-color: #f8fafc !important; border: 1px solid #cbd5e1 !important;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-success">inventory_2</span>
                            <h6 class="mb-0 filter-label fw-bold" style="font-size: 13px; color: #059669 !important;">Paso 2 · Selecciona el producto</h6>
                        </div>
                        <label for="id_producto_select" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Producto disponible</label>
                        <select class="filter-input text-dark bg-white" name="id_producto" id="id_producto_select" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;">
                            <option value="">-- Seleccione un Producto --</option>
                            <?php foreach ($products_list as $prod): ?>
                                <option value="<?php echo $prod['id_producto']; ?>">
                                    <?php echo htmlspecialchars($prod['nombre_commercial']); ?> (<?php echo htmlspecialchars($prod['codigo_barras']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Paso 2 Nuevo -->
                    <div id="group_nuevo" style="display: none;">
                        <div class="p-3 rounded-4 mb-4" style="background-color: #f8fafc !important; border: 1px solid #10b981 !important;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-success">add_circle</span>
                                <h6 class="mb-0 filter-label fw-bold" style="font-size: 13px; color: #059669 !important;">Paso 2 · Datos del producto</h6>
                            </div>
                            <p class="mb-3 text-secondary" style="font-size: 13px;">Completa los datos básicos del catálogo para crear el producto y registrarlo con su primer lote.</p>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="codigo_barras" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Código de Barras</label>
                                    <input type="text" class="filter-input text-dark bg-white" name="codigo_barras" id="codigo_barras" placeholder="Ej: 7701234567890" style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="nombre_commercial" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Nombre Comercial</label>
                                    <input type="text" class="filter-input text-dark bg-white" name="nombre_commercial" id="nombre_commercial" placeholder="Ej: Ibuprofeno 400mg" style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="id_categoria_new" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Categoría del Producto</label>
                                    <select class="filter-input text-dark bg-white" name="id_categoria" id="id_categoria_new" style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;">
                                        <option value="">-- Seleccione una Categoría --</option>
                                        <?php foreach ($categories_list as $cat): ?>
                                            <option value="<?php echo $cat['id_categoria']; ?>">
                                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="nueva_categoria">-- Registrar Nueva Categoría --</option>
                                    </select>
                                    <div id="group_nueva_categoria" class="mt-2" style="display: none;">
                                        <input type="text" class="filter-input text-dark bg-white" name="nueva_categoria_nombre" id="nueva_categoria_nombre" placeholder="Nombre de la nueva categoría..." style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="id_laboratorio_new" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Laboratorio Maestro</label>
                                    <select class="filter-input text-dark bg-white" name="id_laboratorio" id="id_laboratorio_new" style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;">
                                        <option value="">-- Seleccione un Laboratorio --</option>
                                        <?php foreach ($laboratories_list as $lab): ?>
                                            <option value="<?php echo $lab['id_laboratorio']; ?>">
                                                <?php echo htmlspecialchars($lab['nombre_laboratorio']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="nuevo_laboratorio">-- Registrar Nuevo Laboratorio --</option>
                                    </select>
                                    <div id="group_nuevo_laboratorio" class="mt-2" style="display: none;">
                                        <input type="text" class="filter-input text-dark bg-white" name="nuevo_laboratorio_nombre" id="nuevo_laboratorio_nombre" placeholder="Nombre del nuevo laboratorio..." style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="tipo_producto" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Tipo de Producto</label>
                                    <input type="text" class="filter-input text-dark bg-white" name="tipo_producto" id="tipo_producto" placeholder="Ej: Analgésico, Antibiótico, Inyectable..." style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="miligramos" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Miligramos (mg)</label>
                                    <input type="number" class="filter-input text-dark bg-white" name="miligramos" id="miligramos" placeholder="Ej: 500 (Opcional)" style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="empaque_principal" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Empaque Principal</label>
                                    <input type="text" class="filter-input text-dark bg-white" name="empaque_principal" id="empaque_principal" placeholder="Ej: Caja, Bote" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="empaque_medio" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Empaque Medio (Opcional)</label>
                                    <input type="text" class="filter-input text-dark bg-white" name="empaque_medio" id="empaque_medio" placeholder="Ej: Blister, Sobre" style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="unidad_minima" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Unidad Mínima</label>
                                    <input type="text" class="filter-input text-dark bg-white" name="unidad_minima" id="unidad_minima" placeholder="Ej: Tableta, Cápsula" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="unidades_totales_por_empaque_principal" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Unidades Totales por Empaque Principal</label>
                                    <input type="number" min="1" class="filter-input text-dark bg-white" name="unidades_totales_por_empaque_principal" id="unidades_totales_por_empaque_principal" placeholder="Ej: 100" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="unidades_por_empaque_medio" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Unidades por Empaque Medio</label>
                                    <input type="number" min="1" class="filter-input text-dark bg-white" name="unidades_por_empaque_medio" id="unidades_por_empaque_medio" placeholder="Ej: 10 (Si usa blister)" value="1" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="precio_empaque_principal" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Precio Empaque Principal</label>
                                    <input type="number" step="0.01" min="0" class="filter-input text-dark bg-white" name="precio_empaque_principal" id="precio_empaque_principal" placeholder="Ej: 500.00" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="precio_empaque_medio" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Precio Empaque Medio</label>
                                    <input type="number" step="0.01" min="0" class="filter-input text-dark bg-white" name="precio_empaque_medio" id="precio_empaque_medio" placeholder="Ej: 50.00" style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="precio_unidad_minima" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Precio Unidad Mínima</label>
                                    <input type="number" step="0.01" min="0" class="filter-input text-dark bg-white" name="precio_unidad_minima" id="precio_unidad_minima" placeholder="Ej: 5.00" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="stock_minimo" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Stock Mínimo (En unidades mínimas)</label>
                                    <input type="number" min="0" class="filter-input text-dark bg-white" name="stock_minimo" id="stock_minimo" placeholder="Ej: 50" value="0" style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                                </div>
                                <div class="col-12 col-md-6 mb-3 d-flex align-items-end pb-2">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input m-0" name="es_fraccionable" id="es_fraccionable" value="1">
                                        <label class="form-check-label filter-label m-0 fw-bold" for="es_fraccionable" style="text-transform:none; font-size:14px; color: #0f172a !important;">¿Se puede fraccionar/vender suelto?</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 mb-3">
                                    <label for="descripcion" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Descripción</label>
                                    <textarea class="filter-input text-dark bg-white" name="descripcion" id="descripcion" rows="2" placeholder="Detalles u observaciones del producto..." style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;"></textarea>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 mb-3">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input m-0" name="requiere_receta" id="requiere_receta" value="1">
                                        <label class="form-check-label filter-label m-0 fw-bold" for="requiere_receta" style="text-transform:none; font-size:14px; color: #0f172a !important;">Requiere Receta Médica</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3 -->
                    <div class="p-3 rounded-4" style="background-color: #f8fafc !important; border: 1px solid #10b981 !important;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-success">warehouse</span>
                            <h6 class="mb-0 filter-label fw-bold" style="font-size: 13px; color: #059669 !important;">Paso 3 · Datos del lote</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-4 mb-3">
                                <label for="numero_lote" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Número de Lote</label>
                                <input type="text" class="filter-input text-dark bg-white" name="numero_lote" id="numero_lote" placeholder="Ej: LOT-55291" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="empaque_ingreso" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Empaque de Ingreso</label>
                                <select class="filter-input text-dark bg-white" name="empaque_ingreso" id="empaque_ingreso" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;">
                                    <option value="Principal">Empaque Principal (Caja, etc)</option>
                                    <option value="Medio">Empaque Medio (Blíster, etc)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="cantidad_empaques_recibidos" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Cantidad de Empaques Recibidos</label>
                                <input type="number" min="1" class="filter-input text-dark bg-white" name="cantidad_empaques_recibidos" id="cantidad_empaques_recibidos" placeholder="Ej: 100" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="fecha_vencimiento" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Fecha de Vencimiento</label>
                                <input type="date" class="filter-input text-dark bg-white" name="fecha_vencimiento" id="fecha_vencimiento" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;" />
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="bodega" class="filter-label text-dark fw-bold" style="color: #0f172a !important;">Seleccionar Bodega de Destino</label>
                                <select class="filter-input text-dark bg-white" name="bodega" id="bodega" required style="color: #000000 !important; background-color: #ffffff !important; border: 1px solid #cbd5e1 !important;">
                                    <option value="Bodega Principal - Managua">Bodega Principal - Managua</option>
                                    <option value="Depósito Norte">Depósito Norte</option>
                                    <option value="Bodega Externa C-4">Bodega Externa C-4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3" style="border-top: 1px solid #e2e8f0 !important; background-color: #f8fafc !important;">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="background-color: #64748b; border: none;">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size: 18px;">close</span> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold" style="background-color: #10b981; border: none;">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size: 18px;">save</span> Guardar Lote
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
