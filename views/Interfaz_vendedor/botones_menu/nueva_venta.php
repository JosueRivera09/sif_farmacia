<?php
/*
 * Archivo: views/Interfaz_vendedor/botones_menu/nueva_venta.php
 * Propósito: Módulo de punto de venta (creación de pedidos).
 * Qué muestra: Buscador de productos, carrito de compras, gestión de cliente y resumen de factura.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nombre_vendedor = isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Vendedor';
?>
<div class="row g-4" id="venta-modulo-container">
    <!-- Panel de Selección de Productos y Facturación -->
    <div class="col-lg-8">
        <div class="custom-card h-100">
            <div class="border-bottom border-secondary pb-2 mb-3">
                <h6 class="card-title-custom mb-0">Agregar Productos al Pedido</h6>
            </div>

            <!-- Buscador de Productos para el desplegable -->
            <div class="mb-3">
                <label for="input-buscar-producto" class="form-label text-light">Buscar Medicamento</label>
                <div class="input-group bg-slate border border-secondary" style="border-radius: 8px; overflow:hidden;">
                    <span class="input-group-text bg-transparent border-0 text-muted"><span class="material-symbols-outlined" style="font-size:18px;">search</span></span>
                    <input type="text" id="input-buscar-producto" class="form-control bg-transparent border-0 text-light py-2" placeholder="Escribe el nombre o código de barras para filtrar..." autocomplete="off">
                </div>
            </div>

            <form id="form-agregar-item" class="row g-3 align-items-end mb-4">
                <div class="col-md-5">
                    <label for="select-producto" class="form-label text-light">Seleccionar Producto</label>
                    <select id="select-producto" class="form-select form-control-sif bg-slate text-light" required>
                        <option value="" disabled selected>Cargando productos...</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="select-nivel-empaque" class="form-label text-light">Nivel de Empaque</label>
                    <select id="select-nivel-empaque" class="form-select form-control-sif bg-slate text-light" required disabled>
                        <option value="" disabled selected>Seleccione producto...</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="input-cantidad" class="form-label text-light">Cantidad</label>
                    <input type="number" id="input-cantidad" class="form-control form-control-sif text-light" min="1" value="1" required>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th style="width: 120px; text-align: center;">Empaque</th>
                            <th style="width: 100px; text-align: right;">Precio</th>
                            <th style="width: 80px; text-align: center;">Cant.</th>
                            <th style="width: 120px; text-align: right;">Subtotal</th>
                            <th style="width: 80px; text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="cart-table-body">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay productos agregados al pedido.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sección de Cliente / Paciente y Resumen -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- Tarjeta de Cliente -->
        <div class="custom-card">
            <div class="border-bottom border-secondary pb-2 mb-3">
                <h6 class="card-title-custom mb-0">Cliente / Paciente</h6>
            </div>
            
            <!-- Buscador o Nombre de Cliente -->
            <div class="mb-3 position-relative">
                <label for="buscar-cliente-input" class="form-label text-light" style="font-size: 12px;">Cliente (Búsqueda o Nombre Directo)</label>
                <div class="input-group bg-slate border border-secondary" style="border-radius: 8px; overflow:hidden;">
                    <input type="text" id="buscar-cliente-input" class="form-control bg-transparent border-0 text-light py-2" placeholder="Escribe el nombre para omitir registro, o busca..." autocomplete="off">
                    <button class="btn btn-secondary border-0 px-3 d-flex align-items-center" type="button" id="btn-buscar-cliente-trigger" style="background-color: #334155;">
                        <span class="material-symbols-outlined" style="font-size:16px;">search</span>
                    </button>
                </div>
                <!-- Lista de autocompletado de clientes -->
                <div id="clientes-dropdown-suggestions" class="list-group d-none position-absolute w-100 shadow" style="z-index: 1050; max-height: 180px; overflow-y: auto; background-color: #1e293b; border: 1px solid #475569;">
                </div>
            </div>

            <!-- Datos del Cliente Seleccionado -->
            <div id="cliente-seleccionado-info" class="p-3 rounded mb-3 d-none" style="background-color: #0f172a; border: 1px solid #10b981;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-success fw-bold" id="info-cliente-nombre" style="font-size: 13.5px;">Juan Pérez</span>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" id="btn-remover-cliente" style="text-decoration:none; font-size:11px; font-weight:700;">Quitar</button>
                </div>
                <div class="text-muted font-monospace" style="font-size: 11px;">
                    Cédula: <span id="info-cliente-cedula">--</span> <br> Tel: <span id="info-cliente-tel">--</span>
                </div>
            </div>

            <!-- Botón para Registrar Nuevo Cliente -->
            <button class="btn btn-sm btn-outline-success w-100 py-2 d-flex align-items-center justify-content-center gap-1" type="button" id="btn-mostrar-registro-cliente" style="font-weight:600;">
                <span class="material-symbols-outlined" style="font-size:16px;">person_add</span> Registrar Nuevo Cliente
            </button>

            <!-- Formulario de Registro de Cliente -->
            <div id="form-registro-cliente-box" class="d-none mt-3 p-3 border border-secondary rounded" style="background-color: #0f172a;">
                <h6 class="text-light fw-bold d-block mb-3" style="font-size:12.5px;">Registrar Cliente</h6>
                <div class="mb-2">
                    <input type="text" id="reg-cliente-nombre" class="form-control form-control-sif text-light form-control-sm" placeholder="Nombre completo *" required>
                </div>
                <div class="mb-2">
                    <input type="text" id="reg-cliente-cedula" class="form-control form-control-sif text-light form-control-sm" placeholder="Cédula (opcional)">
                </div>
                <div class="mb-2">
                    <input type="text" id="reg-cliente-tel" class="form-control form-control-sif text-light form-control-sm" placeholder="Teléfono (opcional)">
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-sm btn-success flex-grow-1" id="btn-guardar-nuevo-cliente" style="font-weight:600;">Guardar</button>
                    <button type="button" class="btn btn-sm btn-secondary" id="btn-cancelar-registro-cliente" style="background-color: #475569; border:none;">Cancelar</button>
                </div>
            </div>
        </div>

        <!-- Resumen de Cobro -->
        <div class="custom-card d-flex flex-column justify-content-between">
            <div>
                <div class="border-bottom border-secondary pb-2 mb-3">
                    <h6 class="card-title-custom mb-0">Resumen del Pedido</h6>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="text-light" id="invoice-subtotal">C$ 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">I.V.A (15%):</span>
                    <span class="text-light" id="invoice-iva">C$ 0.00</span>
                </div>
                <hr class="border-secondary my-3">
                <div class="d-flex justify-content-between mb-4">
                    <h4 class="font-bold text-success m-0">TOTAL:</h4>
                    <h4 class="font-bold text-success m-0" id="invoice-total">C$ 0.00</h4>
                </div>
            </div>

            <div>
                <button class="btn btn-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2" id="btn-procesar-factura" disabled>
                    <span class="material-symbols-outlined">receipt</span>
                    Generar Pedido de Venta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal / Vista de Ticket Impreso -->
<div id="ticket-modal-overlay" class="d-none" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(15, 23, 42, 0.85); display: flex; align-items: center; justify-content: center; z-index: 1050; padding: 20px;">
    <div class="bg-white text-dark p-4 shadow" style="border-radius: 12px; width: 100%; max-width: 400px; font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.5;">
        <div class="text-center mb-3">
            <h5 class="m-0 font-bold" style="color: #061e33;">SISTEMA SIF - FARMACIA</h5>
            <p class="text-muted m-0" style="font-size: 11px;">PRE-VENTA - PEDIDO DIGITAL</p>
            <p class="m-0 text-dark font-bold mt-2" style="font-size: 18px;" id="ticket-generated-code">TK-XXXXXX</p>
        </div>
        <hr style="border-top: 1px dashed #333;" class="my-2">

        <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
            <span>Fecha:</span>
            <span id="ticket-generated-date">09/07/2026</span>
        </div>
        <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
            <span>Vendedor:</span>
            <span><?php echo $nombre_vendedor; ?></span>
        </div>
        <div class="d-flex justify-content-between mb-1" style="font-size: 11px; display: none;" id="ticket-generated-client-row">
            <span>Cliente:</span>
            <span id="ticket-generated-client-name">--</span>
        </div>
        <hr style="border-top: 1px dashed #333;" class="my-2">

        <div id="ticket-items-list" style="max-height: 150px; overflow-y: auto; font-size: 11px;">
            <!-- Lista de productos -->
        </div>

        <hr style="border-top: 1px dashed #333;" class="my-2">
        <div class="d-flex justify-content-between font-bold" style="font-size: 14px; color: #000;">
            <span>TOTAL:</span>
            <span id="ticket-generated-total">C$ 0.00</span>
        </div>
        <hr style="border-top: 1px dashed #333;" class="my-3">

        <div class="text-center mb-3 py-3" style="font-size: 11px; border: 1px dashed #333; border-radius: 6px; background-color: #f8fafc;">
            <p class="mb-4 text-muted" style="font-size: 10px;">ESPACIO PARA SELLO / FIRMA DE CAJA</p>
            <div style="border-top: 1px solid #aaa; width: 60%; margin: 0 auto;"></div>
            <p class="m-0 mt-1 text-dark font-bold" style="font-size: 9px;">CAJERO AUTORIZADO</p>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-success flex-grow-1 py-2 font-bold d-flex align-items-center justify-content-center gap-1" id="btn-imprimir-ticket-modal" style="border-radius: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">print</span> Imprimir Factura
            </button>
            <button class="btn btn-dark py-2 font-bold" id="btn-close-ticket-modal" style="border-radius: 8px;">Cerrar</button>
        </div>
    </div>
</div>

<script>
    {
        let allProducts = [];
        let cart = [];

        const selectEl = document.getElementById('select-producto');
        const selectNivelEl = document.getElementById('select-nivel-empaque');
        const cantidadEl = document.getElementById('input-cantidad');
        const formEl = document.getElementById('form-agregar-item');
        const tbodyEl = document.getElementById('cart-table-body');
        const searchFilterInput = document.getElementById('input-buscar-producto');

        const subtotalEl = document.getElementById('invoice-subtotal');
        const ivaEl = document.getElementById('invoice-iva');
        const totalEl = document.getElementById('invoice-total');
        const btnProcesar = document.getElementById('btn-procesar-factura');

        // Elementos del Modal
        const ticketOverlay = document.getElementById('ticket-modal-overlay');
        const ticketCodeEl = document.getElementById('ticket-generated-code');
        const ticketDateEl = document.getElementById('ticket-generated-date');
        const ticketTotalEl = document.getElementById('ticket-generated-total');
        const ticketItemsEl = document.getElementById('ticket-items-list');
        const btnCloseModal = document.getElementById('btn-close-ticket-modal');

        // Cargar productos desde el controlador
        function cargarProductosParaVenta() {
            fetch('../../controllers/vendedor/VentaController.php?action=listar')
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        allProducts = response.data;
                        renderProductDropdown(allProducts);
                    }
                })
                .catch(err => console.error("Error al cargar productos:", err));
        }

        // Renderizar dropdown filtrable
        function renderProductDropdown(products) {
            selectEl.innerHTML = '';
            if (products.length === 0) {
                selectEl.innerHTML = '<option value="" disabled selected>No se encontraron productos...</option>';
                return;
            }

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            defaultOption.text = 'Selecciona un medicamento...';
            selectEl.appendChild(defaultOption);

            products.forEach(p => {
                const recetaTag = p.requiere_receta ? ' [RECETA]' : '';
                const option = document.createElement('option');
                option.value = p.id_producto;
                option.text = `${p.nombre_commercial} (Stock Min: ${p.stock_actual})${recetaTag}`;
                selectEl.appendChild(option);
            });
        }
        
        // Actualizar opciones de empaque cuando se selecciona un producto
        selectEl.addEventListener('change', function() {
            const id_producto = parseInt(selectEl.value);
            const product = allProducts.find(p => p.id_producto == id_producto);
            
            selectNivelEl.innerHTML = '';
            
            if (!product) {
                selectNivelEl.disabled = true;
                selectNivelEl.innerHTML = '<option value="" disabled selected>Seleccione producto...</option>';
                return;
            }
            
            selectNivelEl.disabled = false;
            
            // Empaque Principal
            if (product.precio_empaque_principal > 0) {
                const optPrinc = document.createElement('option');
                optPrinc.value = 'Principal';
                optPrinc.dataset.nombre = product.empaque_principal;
                optPrinc.dataset.precio = product.precio_empaque_principal;
                optPrinc.text = `${product.empaque_principal} - C$ ${product.precio_empaque_principal.toFixed(2)}`;
                selectNivelEl.appendChild(optPrinc);
            }
            
            // Empaque Medio
            if (product.empaque_medio && product.precio_empaque_medio !== null && product.precio_empaque_medio > 0) {
                const optMedio = document.createElement('option');
                optMedio.value = 'Medio';
                optMedio.dataset.nombre = product.empaque_medio;
                optMedio.dataset.precio = product.precio_empaque_medio;
                optMedio.text = `${product.empaque_medio} - C$ ${product.precio_empaque_medio.toFixed(2)}`;
                selectNivelEl.appendChild(optMedio);
            }
            
            // Unidad Mínima (Sólo si es fraccionable)
            if (product.es_fraccionable && product.precio_unidad_minima > 0) {
                const optMinimo = document.createElement('option');
                optMinimo.value = 'Minimo';
                optMinimo.dataset.nombre = product.unidad_minima;
                optMinimo.dataset.precio = product.precio_unidad_minima;
                optMinimo.text = `${product.unidad_minima} - C$ ${product.precio_unidad_minima.toFixed(2)}`;
                selectNivelEl.appendChild(optMinimo);
            }
            
            if (selectNivelEl.options.length === 0) {
                selectNivelEl.disabled = true;
                selectNivelEl.innerHTML = '<option value="" disabled selected>Sin empaques válidos...</option>';
            }
        });

        // Filtrar dropdown al escribir en el buscador
        searchFilterInput.addEventListener('input', function() {
            const query = searchFilterInput.value.toLowerCase().trim();
            const filtered = allProducts.filter(p =>
                p.nombre_commercial.toLowerCase().includes(query) ||
                p.codigo_barras.includes(query)
            );
            renderProductDropdown(filtered);
            // Si hay filtrados, seleccionar automáticamente la primera coincidencia
            if (filtered.length > 0) {
                selectEl.value = filtered[0].id_producto;
            }
        });

        // Agregar item al carrito
        formEl.addEventListener('submit', function(e) {
            e.preventDefault();
            const id_producto = parseInt(selectEl.value);
            const cantidad = parseInt(cantidadEl.value);
            const nivelEmpaque = selectNivelEl.value;
            const selectedOption = selectNivelEl.options[selectNivelEl.selectedIndex];

            if (isNaN(id_producto)) {
                alert("Por favor, selecciona un producto de la lista.");
                return;
            }
            if (!nivelEmpaque) {
                alert("Por favor, selecciona un nivel de empaque.");
                return;
            }
            if (cantidad <= 0) {
                alert("La cantidad debe ser mayor a cero.");
                return;
            }

            const product = allProducts.find(p => p.id_producto == id_producto);
            if (!product) {
                alert("Error: El producto seleccionado no existe en el catálogo cargado.");
                return;
            }

            const nombreEmpaque = selectedOption.dataset.nombre;
            const precioEmpaque = parseFloat(selectedOption.dataset.precio);
            
            // Factor de conversión
            let factor = 1;
            if (nivelEmpaque === 'Principal') {
                factor = parseInt(product.unidades_totales_por_empaque_principal);
            } else if (nivelEmpaque === 'Medio') {
                factor = parseInt(product.unidades_por_empaque_medio);
            }
            
            const unidadesRequeridas = cantidad * factor;

            if (product.stock_actual < unidadesRequeridas) {
                alert(`Stock insuficiente para vender ${cantidad} ${nombreEmpaque}. Equivalen a ${unidadesRequeridas} unidades mínimas y sólo hay ${product.stock_actual} disponibles.`);
                return;
            }

            // Revisar si ya existe en el carrito
            const existingItemIndex = cart.findIndex(item => item.id_producto === id_producto && item.nivel_empaque === nivelEmpaque);
            if (existingItemIndex > -1) {
                const nuevaCantidad = cart[existingItemIndex].cantidad + cantidad;
                const nuevasUnidadesRequeridas = nuevaCantidad * factor;
                
                if (product.stock_actual < nuevasUnidadesRequeridas) {
                    alert(`Stock insuficiente. Ya tienes ${cart[existingItemIndex].cantidad} ${nombreEmpaque} en el carrito.`);
                    return;
                }
                cart[existingItemIndex].cantidad = nuevaCantidad;
            } else {
                cart.push({
                    id_producto: product.id_producto,
                    nombre: product.nombre_commercial,
                    precio: precioEmpaque,
                    cantidad: cantidad,
                    nivel_empaque: nivelEmpaque,
                    nombre_empaque: nombreEmpaque
                });
            }

            cantidadEl.value = 1;
            selectEl.selectedIndex = 0;
            searchFilterInput.value = '';
            renderProductDropdown(allProducts);
            
            // Reset dropdown de nivel
            selectNivelEl.innerHTML = '<option value="" disabled selected>Seleccione producto...</option>';
            selectNivelEl.disabled = true;
            
            renderCart();
        });

        // Estado de Cliente seleccionado
        let selectedClientId = null;
        let selectedClientData = null;

        const inputBuscarCliente = document.getElementById('buscar-cliente-input');
        const btnBuscarClienteTrigger = document.getElementById('btn-buscar-cliente-trigger');
        const suggestionsBox = document.getElementById('clientes-dropdown-suggestions');
        const infoBox = document.getElementById('cliente-seleccionado-info');
        const infoNombre = document.getElementById('info-cliente-nombre');
        const infoCedula = document.getElementById('info-cliente-cedula');
        const infoTel = document.getElementById('info-cliente-tel');
        const btnRemoverCliente = document.getElementById('btn-remover-cliente');

        const btnMostrarRegistro = document.getElementById('btn-mostrar-registro-cliente');
        const formRegistroBox = document.getElementById('form-registro-cliente-box');
        const regNombre = document.getElementById('reg-cliente-nombre');
        const regCedula = document.getElementById('reg-cliente-cedula');
        const regTel = document.getElementById('reg-cliente-tel');
        const btnGuardarCliente = document.getElementById('btn-guardar-nuevo-cliente');
        const btnCancelarRegistro = document.getElementById('btn-cancelar-registro-cliente');

        // Búsqueda en tiempo real de clientes
        let clienteTimeout = null;
        inputBuscarCliente.addEventListener('input', function() {
            clearTimeout(clienteTimeout);
            const query = inputBuscarCliente.value.trim();
            if (query.length < 2) {
                suggestionsBox.classList.add('d-none');
                return;
            }

            clienteTimeout = setTimeout(() => {
                fetch(`../../controllers/vendedor/VentaController.php?action=buscar_cliente&query=${query}`)
                    .then(res => res.json())
                    .then(response => {
                        if (response.status === 'success') {
                            renderClienteSuggestions(response.data);
                        }
                    })
                    .catch(err => console.error("Error al buscar clientes:", err));
            }, 300);
        });

        // Trigger de búsqueda manual al hacer clic en lupa
        btnBuscarClienteTrigger.addEventListener('click', function() {
            const query = inputBuscarCliente.value.trim();
            if (!query) return;
            fetch(`../../controllers/vendedor/VentaController.php?action=buscar_cliente&query=${query}`)
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        renderClienteSuggestions(response.data);
                    }
                });
        });

        function renderClienteSuggestions(clientes) {
            if (clientes.length === 0) {
                suggestionsBox.innerHTML = '<div class="list-group-item text-muted p-2" style="font-size:12px; background-color:#1e293b; border-color:#475569;">Ningún cliente coincide.</div>';
                suggestionsBox.classList.remove('d-none');
                return;
            }

            let html = '';
            clientes.forEach(c => {
                const infoExtra = `${c.cedula ? 'Cédula: ' + c.cedula : ''} ${c.telefono ? ' | Tel: ' + c.telefono : ''}`;
                html += `
                    <button type="button" class="list-group-item list-group-item-action text-light text-start p-2 border-0" 
                            style="font-size:12px; background-color:#1e293b; border-bottom:1px solid #475569;"
                            onclick="window.seleccionarClienteDesdeLista(${c.id_cliente}, '${c.nombre_completo.replace(/'/g, "\\'")}', '${c.cedula || '--'}', '${c.telefono || '--'}')">
                        <strong>${c.nombre_completo}</strong><br>
                        <span class="text-muted font-monospace">${infoExtra}</span>
                    </button>
                `;
            });
            suggestionsBox.innerHTML = html;
            suggestionsBox.classList.remove('d-none');
        }

        window.seleccionarClienteDesdeLista = function(id, nombre, cedula, telefono) {
            selectedClientId = id;
            selectedClientData = { nombre, cedula, telefono };

            infoNombre.innerText = nombre;
            infoCedula.innerText = cedula;
            infoTel.innerText = telefono;

            infoBox.classList.remove('d-none');
            suggestionsBox.classList.add('d-none');
            inputBuscarCliente.value = '';
        };

        // Ocultar dropdown de autocompletado si se hace clic fuera
        document.addEventListener('click', function(e) {
            if (e.target !== inputBuscarCliente && e.target !== suggestionsBox) {
                suggestionsBox.classList.add('d-none');
            }
        });

        // Quitar cliente seleccionado
        btnRemoverCliente.addEventListener('click', function() {
            selectedClientId = null;
            selectedClientData = null;
            infoBox.classList.add('d-none');
        });

        // Toggle Formulario Registro
        btnMostrarRegistro.addEventListener('click', function() {
            formRegistroBox.classList.remove('d-none');
            btnMostrarRegistro.classList.add('d-none');
            regNombre.focus();
        });

        btnCancelarRegistro.addEventListener('click', function() {
            formRegistroBox.classList.add('d-none');
            btnMostrarRegistro.classList.remove('d-none');
            regNombre.value = '';
            regCedula.value = '';
            regTel.value = '';
        });

        // Registrar Nuevo Cliente
        btnGuardarCliente.addEventListener('click', function() {
            const nombreVal = regNombre.value.trim();
            const cedulaVal = regCedula.value.trim();
            const telVal = regTel.value.trim();

            if (!nombreVal) {
                alert("El nombre completo del cliente es obligatorio.");
                return;
            }

            btnGuardarCliente.disabled = true;

            const formData = new FormData();
            formData.append('nombre_completo', nombreVal);
            formData.append('cedula', cedulaVal);
            formData.append('telefono', telVal);

            fetch('../../controllers/vendedor/VentaController.php?action=crear_cliente', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success' || response.status === 'exists') {
                        alert(response.message);
                        window.seleccionarClienteDesdeLista(response.id_cliente, nombreVal, cedulaVal || '--', telVal || '--');
                        
                        // Ocultar form
                        formRegistroBox.classList.add('d-none');
                        btnMostrarRegistro.classList.remove('d-none');
                        regNombre.value = '';
                        regCedula.value = '';
                        regTel.value = '';
                    } else {
                        alert("Error: " + response.message);
                    }
                })
                .catch(err => {
                    console.error("Error al registrar cliente:", err);
                    alert("Ocurrió un error al registrar el cliente.");
                })
                .finally(() => {
                    btnGuardarCliente.disabled = false;
                });
        });

        // Renderizar carrito
        function renderCart() {
            if (cart.length === 0) {
                tbodyEl.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay productos agregados al pedido.</td></tr>';
                subtotalEl.innerText = 'C$ 0.00';
                ivaEl.innerText = 'C$ 0.00';
                totalEl.innerText = 'C$ 0.00';
                btnProcesar.disabled = true;
                return;
            }

            let html = '';
            let subtotal = 0;

            cart.forEach((item, index) => {
                const itemSubtotal = item.precio * item.cantidad;
                subtotal += itemSubtotal;
                html += `
                <tr>
                    <td class="text-light">${item.nombre}</td>
                    <td class="text-center text-light">${item.nombre_empaque}</td>
                    <td class="text-end text-light">C$ ${item.precio.toFixed(2)}</td>
                    <td class="text-center text-light">${item.cantidad}</td>
                    <td class="text-end text-light">C$ ${itemSubtotal.toFixed(2)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-danger p-1 d-inline-flex" onclick="window.quitarItemCarrito(${index})">
                            <span class="material-symbols-outlined" style="font-size:16px;">delete</span>
                        </button>
                    </td>
                </tr>
            `;
            });

            tbodyEl.innerHTML = html;

            const iva = subtotal * 0.15;
            const total = subtotal + iva;

            subtotalEl.innerText = `C$ ${subtotal.toFixed(2)}`;
            ivaEl.innerText = `C$ ${iva.toFixed(2)}`;
            totalEl.innerText = `C$ ${total.toFixed(2)}`;
            btnProcesar.disabled = false;
        }

        // Quitar item del carrito
        window.quitarItemCarrito = function(index) {
            cart.splice(index, 1);
            renderCart();
        };

        // Procesar venta y generar ticket
        btnProcesar.addEventListener('click', function() {
            if (cart.length === 0) return;

            btnProcesar.disabled = true;
            btnProcesar.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Generando...`;
            
            const nombreTemporal = (!selectedClientId && inputBuscarCliente.value.trim() !== '') ? inputBuscarCliente.value.trim() : null;

            fetch('../../controllers/vendedor/VentaController.php?action=vender', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        items: cart,
                        id_cliente: selectedClientId,
                        nombre_cliente_temporal: nombreTemporal
                    })
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        // Cargar datos en el ticket impreso/modal
                        ticketCodeEl.innerText = response.codigo_ticket;
                        ticketDateEl.innerText = new Date().toLocaleString();
                        ticketTotalEl.innerText = 'C$ ' + response.total;

                        const ticketClientRow = document.getElementById('ticket-generated-client-row');
                        const ticketClientName = document.getElementById('ticket-generated-client-name');
                        if (selectedClientId && selectedClientData) {
                            ticketClientName.innerText = selectedClientData.nombre;
                            ticketClientRow.style.display = 'flex';
                        } else if (nombreTemporal) {
                            ticketClientName.innerText = nombreTemporal;
                            ticketClientRow.style.display = 'flex';
                        } else {
                            ticketClientRow.style.display = 'none';
                        }

                        let itemsHtml = '';
                        cart.forEach(item => {
                            itemsHtml += `
                            <div class="d-flex justify-content-between">
                                <span>${item.nombre} x${item.cantidad}</span>
                                <span>C$ ${(item.precio * item.cantidad).toFixed(2)}</span>
                            </div>
                        `;
                        });
                        ticketItemsEl.innerHTML = itemsHtml;

                        // Mostrar modal
                        ticketOverlay.classList.remove('d-none');
                        ticketOverlay.style.setProperty('display', 'flex', 'important');

                        // Mandar a imprimir de manera directa al generar el producto/ticket
                        setTimeout(ejecutarImpresionDirectaTicket, 200);

                        cart = [];
                        selectedClientId = null;
                        selectedClientData = null;
                        inputBuscarCliente.value = '';
                        infoBox.classList.add('d-none');
                        renderCart();
                        cargarProductosParaVenta(); // Recargar para actualizar stock
                    } else {
                        alert('Error: ' + response.message);
                    }
                })
                .catch(err => {
                    console.error("Error al generar ticket:", err);
                    alert("Ocurrió un error al conectar con el servidor.");
                })
                .finally(() => {
                    btnProcesar.innerHTML = `<span class="material-symbols-outlined">receipt</span> Generar Pedido de Venta`;
                    btnProcesar.disabled = cart.length === 0;
                });
        });

        function ejecutarImpresionDirectaTicket() {
            const selectImpresora = document.getElementById('select-impresora-ticket');
            const tipoImpresora = selectImpresora ? selectImpresora.value : 'pos80';
            const codigoTicket = ticketCodeEl.innerText;
            const fechaTicket = ticketDateEl.innerText;
            const totalTicket = ticketTotalEl.innerText;
            const itemsHtml = ticketItemsEl.innerHTML;
            const clienteRowHtml = document.getElementById('ticket-generated-client-row').style.display !== 'none' ? document.getElementById('ticket-generated-client-row').outerHTML : '';

            let widthCss = '80mm';
            if (tipoImpresora === 'pos58') widthCss = '58mm';
            if (tipoImpresora === 'laser') widthCss = '100%';

            const win = window.open('', '_blank', 'width=600,height=700');
            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Factura Ticket - ${codigoTicket}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { font-family: monospace; font-size: 12px; margin: 0 auto; padding: 15px; max-width: ${widthCss}; }
                        hr { border-top: 1px dashed #000; }
                        @media print {
                            body { max-width: ${widthCss}; padding: 0; }
                            button { display: none !important; }
                        }
                    </style>
                </head>
                <body>
                    <div class="text-center mb-2">
                        <h5 class="m-0 fw-bold">SISTEMA SIF - FARMACIA</h5>
                        <p class="text-muted m-0" style="font-size: 10px;">PRE-VENTA / FACTURA</p>
                        <h4 class="m-0 fw-bold mt-1">${codigoTicket}</h4>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                        <span>Fecha:</span> <span>${fechaTicket}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                        <span>Vendedor:</span> <span>${'<?php echo $nombre_vendedor; ?>'}</span>
                    </div>
                    ${clienteRowHtml}
                    <hr>
                    <div style="font-size: 11px;">
                        ${itemsHtml}
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-6">
                        <span>TOTAL:</span> <span>${totalTicket}</span>
                    </div>
                    <hr class="my-3">
                    <div class="text-center py-2" style="font-size: 10px; border: 1px dashed #333; border-radius: 4px;">
                        <p class="mb-3 text-muted">SELLO / FIRMA DE CAJA</p>
                        <div style="border-top: 1px solid #aaa; width: 60%; margin: 0 auto;"></div>
                        <p class="m-0 mt-1 text-dark fw-bold" style="font-size: 9px;">CAJERO AUTORIZADO</p>
                    </div>
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function(){ window.close(); }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            win.document.close();
        }

        btnCloseModal.addEventListener('click', function() {
            ticketOverlay.classList.add('d-none');
            ticketOverlay.style.setProperty('display', 'none', 'important');
        });

        const btnImprimirTicket = document.getElementById('btn-imprimir-ticket-modal');
        if (btnImprimirTicket) {
            btnImprimirTicket.addEventListener('click', ejecutarImpresionDirectaTicket);
        }

        cargarProductosParaVenta();
    }
</script>