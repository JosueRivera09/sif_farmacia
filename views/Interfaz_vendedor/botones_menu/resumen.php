<!--
/*
 * Archivo: views/Interfaz_vendedor/botones_menu/resumen.php
 * Propósito: Módulo de resumen del turno del vendedor.
 * Qué muestra: Tarjetas con métricas de stock y ventas, y botones de acceso rápido.
 */
-->
<div class="custom-card mb-4">
    <div class="border-bottom border-secondary pb-2 mb-3">
        <h6 class="card-title-custom mb-0">Resumen del Turno</h6>
    </div>
    
    <div class="row g-4">
        <!-- Stock Total -->
        <div class="col-md-3">
            <div class="metric-card card-primary">
                <div>
                    <h5 class="metric-title">Stock en Tienda</h5>
                    <h3 class="metric-value" id="resumen-stock">0</h3>
                </div>
                <div class="metric-icon-box bg-primary-box">
                    <span class="material-symbols-outlined">inventory</span>
                </div>
            </div>
        </div>

        <!-- Alertas de Stock Bajo -->
        <div class="col-md-3">
            <div class="metric-card card-danger">
                <div>
                    <h5 class="metric-title">Stock Bajo</h5>
                    <h3 class="metric-value" id="resumen-alertas">0</h3>
                </div>
                <div class="metric-icon-box bg-danger-box">
                    <span class="material-symbols-outlined">warning</span>
                </div>
            </div>
        </div>

        <!-- Cantidad Ventas -->
        <div class="col-md-3">
            <div class="metric-card card-secondary">
                <div>
                    <h5 class="metric-title">Ventas del Día</h5>
                    <h3 class="metric-value" id="resumen-ventas-count">0</h3>
                </div>
                <div class="metric-icon-box bg-secondary-box">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
            </div>
        </div>

        <!-- Monto Vendido -->
        <div class="col-md-3">
            <div class="metric-card card-purple">
                <div>
                    <h5 class="metric-title">Total Facturado (Día)</h5>
                    <h3 class="metric-value" id="resumen-ventas-monto">C$ 0.00</h3>
                </div>
                <div class="metric-icon-box bg-purple-box">
                    <span class="material-symbols-outlined">attach_money</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="custom-card">
    <div class="border-bottom border-secondary pb-2 mb-3">
        <h6 class="card-title-custom mb-0">Acceso Rápido a Operaciones</h6>
    </div>
    <div class="d-flex flex-wrap gap-3">
        <button class="btn btn-primary d-flex align-items-center gap-2" onclick="window.cargarModuloNuevaVenta()">
            <span class="material-symbols-outlined">point_of_sale</span>
            Registrar Nueva Venta
        </button>
        <button class="btn btn-secondary d-flex align-items-center gap-2" onclick="window.cargarModuloInventario()">
            <span class="material-symbols-outlined">search</span>
            Consultar Catálogo
        </button>
    </div>
</div>

<div class="custom-card mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3 gap-2">
        <h6 class="card-title-custom mb-0" id="titulo-seccion-tickets">Mis Tickets de Venta (Últimos 20)</h6>
        <button id="btn-imprimir-todos-tickets" class="btn btn-sm btn-primary d-flex align-items-center gap-1 px-3" style="font-size: 12px;">
            <span class="material-symbols-outlined" style="font-size: 16px;">print</span> Imprimir Lista de Tickets
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="color: #94a3b8;">N° Ticket</th>
                    <th style="color: #94a3b8;">Fecha y Hora</th>
                    <th style="color: #94a3b8;">Vendedor / Usuario</th>
                    <th style="color: #94a3b8; text-align: right;">Total</th>
                    <th style="color: #94a3b8; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody id="vendedor-tickets-tbody">
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <div class="spinner-border text-success spinner-border-sm mb-2" role="status"></div><br>
                        Cargando tickets...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal / Vista de Ticket Impreso en Resumen -->
<div id="ticket-modal-resumen-overlay" class="d-none" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(15, 23, 42, 0.85); display: flex; align-items: center; justify-content: center; z-index: 1050; padding: 20px;">
    <div class="bg-white text-dark p-4 shadow" style="border-radius: 12px; width: 100%; max-width: 400px; font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.5;">
        <div class="text-center mb-3">
            <h5 class="m-0 font-bold" style="color: #061e33;">SISTEMA SIF - FARMACIA</h5>
            <p class="text-muted m-0" style="font-size: 11px;">DETALLE DE TICKET / FACTURA</p>
            <p class="m-0 text-dark font-bold mt-2" style="font-size: 18px;" id="ticket-resumen-code">TK-XXXXXX</p>
        </div>
        <hr style="border-top: 1px dashed #333;" class="my-2">

        <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
            <span>Fecha:</span>
            <span id="ticket-resumen-date">09/07/2026</span>
        </div>
        <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
            <span>Vendedor:</span>
            <span id="ticket-resumen-vendedor">--</span>
        </div>
        <div class="d-flex justify-content-between mb-1" style="font-size: 11px;" id="ticket-resumen-client-row">
            <span>Cliente:</span>
            <span id="ticket-resumen-client-name">Cliente Final</span>
        </div>
        <hr style="border-top: 1px dashed #333;" class="my-2">

        <div id="ticket-resumen-items-list" style="max-height: 150px; overflow-y: auto; font-size: 11px;">
            <!-- Lista de productos -->
        </div>

        <hr style="border-top: 1px dashed #333;" class="my-2">
        <div class="d-flex justify-content-between font-bold" style="font-size: 14px; color: #000;">
            <span>TOTAL:</span>
            <span id="ticket-resumen-total">C$ 0.00</span>
        </div>
        <hr style="border-top: 1px dashed #333;" class="my-3">

        <div class="text-center mb-3 py-3" style="font-size: 11px; border: 1px dashed #333; border-radius: 6px; background-color: #f8fafc;">
            <p class="mb-4 text-muted" style="font-size: 10px;">ESPACIO PARA SELLO / FIRMA DE CAJA</p>
            <div style="border-top: 1px solid #aaa; width: 60%; margin: 0 auto;"></div>
            <p class="m-0 mt-1 text-dark font-bold" style="font-size: 9px;">CAJERO AUTORIZADO</p>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-danger flex-grow-1 py-2 font-bold d-flex align-items-center justify-content-center gap-1" id="btn-imprimir-ticket-resumen" style="border-radius: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span> Guardar (PDF)
            </button>
            <button class="btn btn-dark py-2 font-bold" id="btn-close-ticket-resumen-modal" style="border-radius: 8px;">Cerrar</button>
        </div>
    </div>
</div>

<script>
{
    const modalResumen = document.getElementById('ticket-modal-resumen-overlay');
    const btnCloseModalResumen = document.getElementById('btn-close-ticket-resumen-modal');
    const btnPrintModalResumen = document.getElementById('btn-imprimir-ticket-resumen');

    let ticketActualDatos = null;

    function cargarMetricasResumen() {
        fetch('../../controllers/vendedor/VentaController.php?action=metricas')
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const data = response.data;
                    document.getElementById('resumen-stock').innerText = data.total_stock;
                    document.getElementById('resumen-alertas').innerText = data.alertas_bajo_stock;
                    document.getElementById('resumen-ventas-count').innerText = data.ventas_sesion;
                    document.getElementById('resumen-ventas-monto').innerText = 'C$ ' + data.monto_ventas;
                }
            })
            .catch(err => console.error("Error al cargar métricas de resumen:", err));
    }

    let rawTicketsResponse = null;

    function cargarMisTickets() {
        fetch('../../controllers/vendedor/VentaController.php?action=mis_tickets')
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    rawTicketsResponse = response;
                    const tickets = response.data || [];
                    const esAdmin = response.es_admin;
                    
                    const tituloEl = document.getElementById('titulo-seccion-tickets');
                    if (tituloEl) {
                        tituloEl.innerText = esAdmin ? 'Tickets de Venta del Sistema (Todos)' : 'Mis Tickets de Venta (Últimos 20)';
                    }

                    const tbody = document.getElementById('vendedor-tickets-tbody');
                    if (tickets.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No hay tickets registrados para mostrar.</td></tr>`;
                        return;
                    }
                    
                    let html = '';
                    tickets.forEach(t => {
                        const dateObj = new Date(t.fecha_creacion);
                        const fecha = dateObj.toLocaleDateString();
                        const hora = dateObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        
                        let badgeClass = 'bg-warning-box text-warning';
                        let estadoTexto = 'Pendiente';
                        if (t.estado === 'Pagado') {
                            badgeClass = 'bg-success-box text-success';
                            estadoTexto = 'Pagado';
                        }
                        
                        html += `
                            <tr>
                                <td><code class="text-success font-bold" style="font-size:13px;">${t.codigo_ticket}</code></td>
                                <td class="text-light">${fecha} ${hora}</td>
                                <td class="text-light">${t.nombre_vendedor || response.nombre_usuario_actual}</td>
                                <td class="text-end font-monospace text-success fw-bold">C$ ${parseFloat(t.total).toFixed(2)}</td>
                                <td class="text-center">
                                    <span class="badge ${badgeClass} px-3 py-1">${estadoTexto}</span>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                }
            })
            .catch(err => {
                console.error("Error al cargar tickets del vendedor:", err);
                document.getElementById('vendedor-tickets-tbody').innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar tickets</td></tr>`;
            });
    }

    const btnImprimirTodos = document.getElementById('btn-imprimir-todos-tickets');
    if (btnImprimirTodos) {
        btnImprimirTodos.addEventListener('click', function() {
            if (!rawTicketsResponse || !rawTicketsResponse.data || rawTicketsResponse.data.length === 0) {
                alert("No hay tickets registrados para imprimir.");
                return;
            }

            const tickets = rawTicketsResponse.data;
            const esAdmin = rawTicketsResponse.es_admin;
            const usuarioActual = rawTicketsResponse.nombre_usuario_actual || 'Usuario';
            const rolActual = rawTicketsResponse.rol_usuario_actual || 'Vendedor';
            const fechaEmision = new Date().toLocaleString();

            let tituloReporte = "Lista de Tickets de Venta Generados (Mis Tickets)";
            let alcanceReporte = `Tickets generados por el vendedor: ${usuarioActual}`;
            if (esAdmin) {
                tituloReporte = "Lista General de Todo lo Vendido en el Sistema (Todos los Vendedores)";
                alcanceReporte = "Consolidado total de tickets generados por todos los usuarios del sistema";
            }

            let totalMonto = 0;
            let filasHTML = '';
            tickets.forEach(t => {
                const dateObj = new Date(t.fecha_creacion);
                const fechaHora = dateObj.toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });
                const monto = parseFloat(t.total);
                totalMonto += monto;
                filasHTML += `
                    <tr>
                        <td>${fechaHora}</td>
                        <td><strong>${t.codigo_ticket}</strong></td>
                        <td>${t.nombre_vendedor || usuarioActual}</td>
                        <td>${t.nombre_cliente || 'Cliente Final'}</td>
                        <td class="text-center">
                            <span class="badge ${t.estado === 'Pagado' ? 'bg-success' : 'bg-warning text-dark'}">${t.estado}</span>
                        </td>
                        <td class="text-end fw-bold">C$ ${monto.toFixed(2)}</td>
                    </tr>
                `;
            });

            const win = window.open('', '_blank', 'width=800,height=750');
            win.document.write(`
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="utf-8">
                    <title>${tituloReporte}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { font-family: sans-serif; font-size: 13px; padding: 25px; color: #1e293b; }
                        .header-reporte { border-bottom: 2px solid #10b981; padding-bottom: 12px; margin-bottom: 20px; }
                        .table-reporte th { background-color: #f1f5f9; text-transform: uppercase; font-size: 11px; font-weight: 700; color: #475569; }
                        .resumen-card { background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-top: 20px; }
                        @media print {
                            body { padding: 0; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header-reporte d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-1" style="color: #0f172a;">SISTEMA SIF - FARMACIA</h3>
                            <h5 class="text-success fw-bold mb-0">${tituloReporte}</h5>
                        </div>
                        <div class="text-end" style="font-size: 12px; color: #64748b;">
                            <p class="mb-1"><strong>Fecha emisión:</strong> ${fechaEmision}</p>
                            <p class="mb-1"><strong>Generado por:</strong> ${usuarioActual} (${rolActual})</p>
                            <p class="mb-0"><strong>Alcance:</strong> ${alcanceReporte}</p>
                        </div>
                    </div>

                    <table class="table table-bordered align-middle table-reporte mb-0">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Código Ticket</th>
                                <th>Vendedor / Usuario</th>
                                <th>Cliente</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filasHTML}
                        </tbody>
                    </table>

                    <div class="resumen-card d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 fw-bold">Total Transacciones: ${tickets.length} ticket(s)</p>
                            <p class="mb-0 text-muted" style="font-size: 11px;">Documento oficial impreso desde el Panel de Ventas SIF</p>
                        </div>
                        <div class="text-end">
                            <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Monto Total de Ventas</span>
                            <h4 class="fw-bold text-success mb-0">C$ ${totalMonto.toFixed(2)}</h4>
                        </div>
                    </div>

                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() { window.close(); }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            win.document.close();
        });
    }

    window.verTicketResumen = function(codigo) {
        fetch(`../../controllers/vendedor/VentaController.php?action=ver_ticket&codigo=${codigo}`)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const data = response.data;
                    ticketActualDatos = data;

                    document.getElementById('ticket-resumen-code').innerText = data.codigo_ticket;
                    document.getElementById('ticket-resumen-date').innerText = new Date(data.fecha_creacion).toLocaleString();
                    document.getElementById('ticket-resumen-vendedor').innerText = data.nombre_vendedor || 'Vendedor';
                    document.getElementById('ticket-resumen-client-name').innerText = data.nombre_cliente ? data.nombre_cliente : 'Cliente Final';
                    document.getElementById('ticket-resumen-total').innerText = 'C$ ' + parseFloat(data.total).toFixed(2);

                    let itemsHtml = '';
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            const subt = parseFloat(item.precio_unitario) * parseInt(item.cantidad);
                            itemsHtml += `
                                <div class="d-flex justify-content-between mb-1">
                                    <span>${item.nombre_commercial || 'Producto'} x${item.cantidad} (${item.nombre_empaque || 'Caja'})</span>
                                    <span>C$ ${subt.toFixed(2)}</span>
                                </div>
                            `;
                        });
                    } else {
                        itemsHtml = '<div class="text-muted text-center py-2">Sin detalles registrados</div>';
                    }
                    document.getElementById('ticket-resumen-items-list').innerHTML = itemsHtml;

                    modalResumen.classList.remove('d-none');
                    modalResumen.style.setProperty('display', 'flex', 'important');
                } else {
                    alert('Error: ' + response.message);
                }
            })
            .catch(err => {
                console.error("Error al ver ticket:", err);
                alert("Error al cargar los datos del ticket.");
            });
    };

    if (btnCloseModalResumen) {
        btnCloseModalResumen.addEventListener('click', function() {
            modalResumen.classList.add('d-none');
            modalResumen.style.setProperty('display', 'none', 'important');
        });
    }

    if (btnPrintModalResumen) {
        btnPrintModalResumen.addEventListener('click', function() {
            if (!ticketActualDatos) return;

            const tipoImpresora = document.getElementById('select-impresora-resumen').value;
            const codigoTicket = ticketActualDatos.codigo_ticket;
            const fechaTicket = new Date(ticketActualDatos.fecha_creacion).toLocaleString();
            const totalTicket = 'C$ ' + parseFloat(ticketActualDatos.total).toFixed(2);
            const vendedorTicket = ticketActualDatos.nombre_vendedor || 'Vendedor';
            const clienteTicket = ticketActualDatos.nombre_cliente ? ticketActualDatos.nombre_cliente : 'Cliente Final';
            const itemsHtml = document.getElementById('ticket-resumen-items-list').innerHTML;

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
                        <span>Vendedor:</span> <span>${vendedorTicket}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                        <span>Cliente:</span> <span>${clienteTicket}</span>
                    </div>
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
        });
    }

    cargarMetricasResumen();
    cargarMisTickets();
}
</script>
