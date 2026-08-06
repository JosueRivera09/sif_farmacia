<div class="custom-card mb-4 p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3 gap-2">
        <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Historial de Cobros (Hoy)</h6>

        <div class="d-flex align-items-center gap-2">
            <div id="wrapper-filtro-admin-historial" class="d-none">
                <select id="select-filtro-admin-historial" class="form-select form-select-sm border-secondary text-dark bg-white" style="font-size: 12px; width: 180px; cursor: pointer;">
                    <option value="todos" selected>Todo lo Vendido (Admin)</option>
                    <option value="mis_ventas">Solo Mis Ventas</option>
                </select>
            </div>

            <button id="btn-imprimir-reporte-cobros" class="btn btn-sm btn-primary d-flex align-items-center gap-1 px-3" style="font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">print</span> Imprimir Historial
            </button>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="color: #94a3b8;">Hora</th>
                    <th style="color: #94a3b8;">Código Ticket</th>
                    <th style="color: #94a3b8;">Vendedor</th>
                    <th style="color: #94a3b8;">Cliente</th>
                    <th style="color: #94a3b8; text-align: right;">Total Cobrado</th>
                    <th style="color: #94a3b8; text-align: center;">Estado</th>
                    <th style="color: #94a3b8; text-align: center;">Acción</th>
                </tr>
            </thead>
            <tbody id="historial-cobros-tbody">
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="spinner-border text-success mb-3" role="status"></div><br>
                        Cargando historial de cobros...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    {
        const tbodyHistorial = document.getElementById('historial-cobros-tbody');
        const wrapperAdminFiltro = document.getElementById('wrapper-filtro-admin-historial');
        const selectAdminFiltro = document.getElementById('select-filtro-admin-historial');
        const btnImprimirReporte = document.getElementById('btn-imprimir-reporte-cobros');

        let datosHistorialRaw = null;

        function cargarHistorialCobros() {
            fetch('../../controllers/caja/CajaController.php?action=listar_pagados')
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        datosHistorialRaw = response;

                        // Mostrar u ocultar selector de filtro según si es admin
                        if (response.es_admin) {
                            wrapperAdminFiltro.classList.remove('d-none');
                        } else {
                            wrapperAdminFiltro.classList.add('d-none');
                        }

                        renderHistorial();
                    } else {
                        tbodyHistorial.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error: ${response.message}</td></tr>`;
                    }
                })
                .catch(err => {
                    console.error("Error al cargar historial de cobros:", err);
                    tbodyHistorial.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Ocurrió un error al cargar los datos.</td></tr>`;
                });
        }

        function obtenerVentasFiltradas() {
            if (!datosHistorialRaw || !datosHistorialRaw.data) return [];

            const tickets = datosHistorialRaw.data;
            const esAdmin = datosHistorialRaw.es_admin;
            const idUsuarioActual = datosHistorialRaw.id_usuario_actual;
            const filtroVal = selectAdminFiltro ? selectAdminFiltro.value : 'todos';

            if (esAdmin && filtroVal === 'todos') {
                return tickets;
            } else {
                // Si no es admin o seleccionó "mis_ventas", filtrar solo las ventas hechas por este usuario
                return tickets.filter(t => parseInt(t.id_vendedor) === parseInt(idUsuarioActual));
            }
        }

        function renderHistorial() {
            const ticketsFiltrados = obtenerVentasFiltradas();

            if (ticketsFiltrados.length === 0) {
                tbodyHistorial.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-5"><span class="material-symbols-outlined d-block fs-1 mb-2" style="opacity: 0.4;">receipt_long</span>No hay cobros registrados para mostrar con el filtro actual.</td></tr>`;
                return;
            }

            let html = '';
            ticketsFiltrados.forEach(t => {
                const hora = new Date(t.fecha_creacion).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const cliente = t.nombre_cliente ? t.nombre_cliente : 'Cliente Ocasional';

                html += `
                <tr>
                    <td class="text-light">${hora}</td>
                    <td><code class="text-success font-bold" style="font-size:13px;">${t.codigo_ticket}</code></td>
                    <td class="text-light">${t.nombre_vendedor}</td>
                    <td class="text-light">${cliente}</td>
                    <td class="text-end font-monospace text-success fw-bold">C$ ${parseFloat(t.total).toFixed(2)}</td>
                    <td class="text-center">
                        <span class="badge bg-success-box text-success px-3 py-1">Pagado</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-success p-1 px-2 d-inline-flex align-items-center gap-1" onclick="window.imprimirTicketIndividualCobros('${t.codigo_ticket}')" title="Imprimir Ticket">
                            <span class="material-symbols-outlined" style="font-size:16px;">print</span> Ticket
                        </button>
                    </td>
                </tr>
            `;
            });
            tbodyHistorial.innerHTML = html;
        }

        if (selectAdminFiltro) {
            selectAdminFiltro.addEventListener('change', renderHistorial);
        }

        if (btnImprimirReporte) {
            btnImprimirReporte.addEventListener('click', function() {
                if (!datosHistorialRaw) return;

                const ventasAImprimir = obtenerVentasFiltradas();
                if (ventasAImprimir.length === 0) {
                    alert('No hay ventas cobradas registradas para imprimir con el filtro actual.');
                    return;
                }

                const esAdmin = datosHistorialRaw.es_admin;
                const filtroVal = selectAdminFiltro ? selectAdminFiltro.value : 'todos';
                const usuarioActual = datosHistorialRaw.nombre_usuario_actual;
                const rolActual = datosHistorialRaw.rol_usuario_actual;
                const fechaActual = new Date().toLocaleString();

                let tituloReporte = "Reporte de Ventas Realizadas (Mis Ventas)";
                let alcanceReporte = `Solo ventas procesadas por ${usuarioActual}`;
                if (esAdmin && filtroVal === 'todos') {
                    tituloReporte = "Reporte General de Ventas Cobradas (Todo lo Vendido)";
                    alcanceReporte = "Todo lo vendido en el sistema (Consolidado Administrador)";
                }

                let totalMonto = 0;
                let filasHTML = '';
                ventasAImprimir.forEach(v => {
                    const hora = new Date(v.fecha_creacion).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const monto = parseFloat(v.total);
                    totalMonto += monto;
                    filasHTML += `
                        <tr>
                            <td>${hora}</td>
                            <td><strong>${v.codigo_ticket}</strong></td>
                            <td>${v.nombre_vendedor || 'Vendedor'}</td>
                            <td>${v.nombre_cliente || 'Cliente Final'}</td>
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
                                <p class="mb-1"><strong>Fecha emisión:</strong> ${fechaActual}</p>
                                <p class="mb-1"><strong>Generado por:</strong> ${usuarioActual} (${rolActual})</p>
                                <p class="mb-0"><strong>Alcance:</strong> ${alcanceReporte}</p>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle table-reporte mb-0">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Código Ticket</th>
                                    <th>Vendedor / Usuario</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Total Cobrado</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${filasHTML}
                            </tbody>
                        </table>

                        <div class="resumen-card d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 fw-bold">Total Transacciones: ${ventasAImprimir.length} venta(s)</p>
                                <p class="mb-0 text-muted" style="font-size: 11px;">Documento oficial generado desde la Interfaz de Caja SIF</p>
                            </div>
                            <div class="text-end">
                                <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Recaudación Total</span>
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

        // Función global para imprimir un ticket individual desde la tabla de cobros
        window.imprimirTicketIndividualCobros = function(codigo) {
            fetch(`../../controllers/caja/CajaController.php?action=ver_ticket&codigo=${codigo}`)
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        const data = response.data;
                        const win = window.open('', '_blank', 'width=500,height=700');
                        
                        let itemsHtml = '';
                        if (data.items && data.items.length > 0) {
                            data.items.forEach(item => {
                                const subt = parseFloat(item.precio_unitario) * parseInt(item.cantidad);
                                itemsHtml += `
                                    <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                                        <span>${item.nombre_commercial || 'Producto'} x${item.cantidad} (${item.nombre_empaque || 'Unidad'})</span>
                                        <span>C$ ${subt.toFixed(2)}</span>
                                    </div>
                                `;
                            });
                        } else {
                            itemsHtml = '<div class="text-muted text-center py-2">Sin detalles de productos</div>';
                        }

                        win.document.write(`
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <title>Factura - ${data.codigo_ticket}</title>
                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                                <style>
                                    body { font-family: monospace; font-size: 12px; margin: 0 auto; padding: 15px; max-width: 80mm; }
                                    hr { border-top: 1px dashed #000; }
                                    @media print {
                                        body { max-width: 80mm; padding: 0; }
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="text-center mb-2">
                                    <h5 class="m-0 fw-bold">SISTEMA SIF - FARMACIA</h5>
                                    <p class="text-muted m-0" style="font-size: 10px;">COMPROBANTE DE PAGO / FACTURA</p>
                                    <h4 class="m-0 fw-bold mt-1">${data.codigo_ticket}</h4>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                                    <span>Fecha:</span> <span>${new Date(data.fecha_creacion).toLocaleString()}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                                    <span>Vendedor:</span> <span>${data.nombre_vendedor || 'Vendedor'}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                                    <span>Cliente:</span> <span>${data.nombre_cliente || 'Cliente Final'}</span>
                                </div>
                                <hr>
                                <div>
                                    ${itemsHtml}
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold fs-6">
                                    <span>TOTAL:</span> <span>C$ ${parseFloat(data.total).toFixed(2)}</span>
                                </div>
                                <hr class="my-3">
                                <div class="text-center py-2" style="font-size: 10px; border: 1px dashed #333; border-radius: 4px;">
                                    <p class="mb-3 text-muted">SELLO / FIRMA DE CAJA</p>
                                    <div style="border-top: 1px solid #aaa; width: 60%; margin: 0 auto;"></div>
                                    <p class="m-0 mt-1 text-dark fw-bold" style="font-size: 9px;">PAGADO / CAJERO AUTORIZADO</p>
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
                    } else {
                        alert('Error al obtener los detalles del ticket: ' + response.message);
                    }
                })
                .catch(err => {
                    console.error("Error al imprimir ticket:", err);
                    alert("No se pudo cargar la información del ticket.");
                });
        };

        // Inicializar carga
        cargarHistorialCobros();
    }
</script>