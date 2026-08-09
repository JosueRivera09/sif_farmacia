<?php
/*
 * Archivo: views/Interfaz_caja/botones_menu/historial_cobros.php
 * Propósito: Vista que muestra el historial de cobros realizados en caja.
 */
?>
<div class="custom-card mb-4 p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3 gap-2">
        <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Historial de Cobros (Hoy)</h6>

        <button id="btn-imprimir-reporte-cobros" class="btn btn-sm btn-primary d-flex align-items-center gap-1 px-3" style="font-size: 12px;">
            <span class="material-symbols-outlined" style="font-size: 16px;">print</span> Imprimir Lista de Ventas
        </button>
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
                </tr>
            </thead>
            <tbody id="historial-cobros-tbody">
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="spinner-border text-success mb-3" role="status"></div><br>
                        Cargando historial de cobros...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="custom-card mb-4 p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3 gap-2">
        <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Historial de Cierres de Caja</h6>
        <span class="badge bg-slate border border-secondary text-secondary">Últimos Arqueos Registrados</span>
    </div>

    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th style="color: #94a3b8;">ID</th>
                    <th style="color: #94a3b8;">Cajero</th>
                    <th style="color: #94a3b8;">Fecha Cierre</th>
                    <th style="color: #94a3b8; text-align: right;">Fondo Inicial</th>
                    <th style="color: #94a3b8; text-align: right;">Esperado</th>
                    <th style="color: #94a3b8; text-align: right;">Físico Arqueado</th>
                    <th style="color: #94a3b8; text-align: right;">Diferencia</th>
                    <th style="color: #94a3b8; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody id="historial-cierres-tbody">
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <div class="spinner-border text-success spinner-border-sm mb-2" role="status"></div><br>
                        Cargando registros de cierres_caja...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    {
        const tbodyHistorial = document.getElementById('historial-cobros-tbody');
        const tbodyCierres = document.getElementById('historial-cierres-tbody');
        const btnImprimirReporte = document.getElementById('btn-imprimir-reporte-cobros');

        let datosHistorialRaw = null;

        function cargarHistorialCobros() {
            fetch('../../controllers/caja/CajaController.php?action=listar_pagados')
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        datosHistorialRaw = response;
                        renderHistorial();
                    } else {
                        tbodyHistorial.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Error: ${response.message}</td></tr>`;
                    }
                })
                .catch(err => {
                    console.error("Error al cargar historial de cobros:", err);
                    tbodyHistorial.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Ocurrió un error al cargar los datos.</td></tr>`;
                });
        }

        function cargarHistorialCierres() {
            fetch('../../controllers/caja/CajaController.php?action=listar_cierres')
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success' && response.data) {
                        renderCierres(response.data);
                    } else {
                        tbodyCierres.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-3">No fue posible cargar los registros de cierre.</td></tr>`;
                    }
                })
                .catch(err => {
                    console.error("Error al cargar cierres:", err);
                    tbodyCierres.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-3">Error al consultar cierres_caja.</td></tr>`;
                });
        }

        function renderCierres(cierres) {
            if (cierres.length === 0) {
                tbodyCierres.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">No hay cierres de caja registrados en la tabla.</td></tr>`;
                return;
            }

            let html = '';
            cierres.forEach(c => {
                const fecha = new Date(c.fecha_cierre).toLocaleString([], {
                    dateStyle: 'short',
                    timeStyle: 'short'
                });
                const inicial = parseFloat(c.monto_inicial || 0).toFixed(2);
                const esperado = parseFloat(c.monto_esperado || 0).toFixed(2);
                const fisico = parseFloat(c.monto_final || 0).toFixed(2);
                const diff = parseFloat(c.diferencia || 0);

                let diffBadge = `<span class="badge bg-success font-monospace">C$ 0.00</span>`;
                if (diff < 0) {
                    diffBadge = `<span class="badge bg-danger font-monospace">C$ ${diff.toFixed(2)} (Faltante)</span>`;
                } else if (diff > 0) {
                    diffBadge = `<span class="badge bg-warning text-dark font-monospace">C$ +${diff.toFixed(2)} (Sobrante)</span>`;
                }

                html += `
                    <tr>
                        <td><code class="text-success font-bold">#${c.id_cierre}</code></td>
                        <td class="text-light"><strong>${c.nombre_usuario || 'Cajero'}</strong></td>
                        <td class="text-light font-monospace" style="font-size:12px;">${fecha}</td>
                        <td class="text-end text-light font-monospace">C$ ${inicial}</td>
                        <td class="text-end text-light font-monospace">C$ ${esperado}</td>
                        <td class="text-end text-light font-monospace fw-bold">C$ ${fisico}</td>
                        <td class="text-end">${diffBadge}</td>
                        <td class="text-center"><span class="badge bg-primary px-2 py-1">${c.estado}</span></td>
                    </tr>
                `;
            });
            tbodyCierres.innerHTML = html;
        }

        function obtenerVentasSegunRol() {
            if (!datosHistorialRaw || !datosHistorialRaw.data) return [];
            return datosHistorialRaw.data;
        }

        function renderHistorial() {
            const tickets = obtenerVentasSegunRol();

            if (tickets.length === 0) {
                tbodyHistorial.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-5"><span class="material-symbols-outlined d-block fs-1 mb-2" style="opacity: 0.4;">receipt_long</span>No hay cobros registrados para mostrar.</td></tr>`;
                return;
            }

            let html = '';
            tickets.forEach(v => {
                const hora = new Date(v.fecha_creacion).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                html += `
                    <tr>
                        <td class="text-light font-monospace" style="font-size:12px;">${hora}</td>
                        <td><code class="text-success font-bold" style="font-size:13px;">${v.codigo_ticket}</code></td>
                        <td class="text-light">${v.nombre_vendedor || 'Vendedor'}</td>
                        <td class="text-light">${v.nombre_cliente || 'Cliente Final'}</td>
                        <td class="text-end text-light font-monospace font-bold">C$ ${parseFloat(v.total).toFixed(2)}</td>
                        <td class="text-center"><span class="badge bg-success px-2 py-1">Pagado</span></td>
                    </tr>
                `;
            });
            tbodyHistorial.innerHTML = html;
        }

        if (btnImprimirReporte) {
            btnImprimirReporte.addEventListener('click', function() {
                if (!datosHistorialRaw) return;

                const ventasAImprimir = obtenerVentasSegunRol();
                if (ventasAImprimir.length === 0) {
                    alert('No hay ventas registradas para imprimir.');
                    return;
                }

                const esAdmin = datosHistorialRaw.es_admin;
                const usuarioActual = datosHistorialRaw.nombre_usuario_actual;
                const rolActual = datosHistorialRaw.rol_usuario_actual;
                const fechaActual = new Date().toLocaleString();

                let tituloReporte = "Lista de Ventas Realizadas por Usuario";
                let alcanceReporte = `Ventas procesadas por el usuario: ${usuarioActual}`;

                if (esAdmin) {
                    tituloReporte = "Lista General de Todo lo Vendido en el Sistema";
                    alcanceReporte = "Consolidado total de ventas realizadas por todos los usuarios";
                }

                let totalMonto = 0;
                let filasHTML = '';
                ventasAImprimir.forEach(v => {
                    const hora = new Date(v.fecha_creacion).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
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
                                <p class="mb-1 fw-bold">Total Registros: ${ventasAImprimir.length} ticket(s)</p>
                                <p class="mb-0 text-muted" style="font-size: 11px;">Documento oficial generado desde el Historial de Cobros</p>
                            </div>
                            <div class="text-end">
                                <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Monto Total Recaudado</span>
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

        // Inicializar carga
        cargarHistorialCobros();
        cargarHistorialCierres();
    }
</script>