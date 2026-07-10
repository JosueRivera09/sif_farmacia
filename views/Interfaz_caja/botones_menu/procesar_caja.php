<!--
/*
 * Archivo: views/Interfaz_caja/botones_menu/procesar_caja.php
 * Propósito: Módulo de procesamiento de caja para cobrar pedidos.
 * Qué muestra: Buscador de tickets, listado de tickets pendientes, detalle de facturación.
 */
-->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="metric-card card-primary">
            <div>
                <p class="metric-title">Recaudación Total (Hoy)</p>
                <h3 class="metric-value" id="caja-total-dia">C$ 0.00</h3>
            </div>
            <div class="metric-icon-box bg-primary-box">
                <span class="material-symbols-outlined">real_estate_agent</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="metric-card card-secondary">
            <div>
                <p class="metric-title">Facturas Pagadas</p>
                <h3 class="metric-value" id="caja-tickets-pagados">0 Facturas</h3>
            </div>
            <div class="metric-icon-box bg-secondary-box">
                <span class="material-symbols-outlined">confirmation_number</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="metric-card card-danger">
            <div>
                <p class="metric-title">Pedidos por Cobrar</p>
                <h3 class="metric-value" id="caja-preventas-pendientes">0 Pedidos</h3>
            </div>
            <div class="metric-icon-box bg-danger-box">
                <span class="material-symbols-outlined">schedule</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="custom-card mb-4 p-4">
            <div class="border-bottom border-secondary pb-2 mb-3">
                <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Buscar Pedido de Venta</h6>
            </div>
            <form method="POST" action="" class="d-flex gap-3" id="form-buscar-ticket">
                <input type="text" name="n_ticket" id="buscar-n-ticket" class="form-control form-control-sif flex-grow-1" placeholder="Ingrese el número de pedido o preventa (ej. TK-XXXX)..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-4" id="btn-buscar">
                    <span class="material-symbols-outlined">search</span> Buscar
                </button>
            </form>
        </div>
        <div class="custom-card mb-4 p-4">
            <div class="border-bottom border-secondary pb-2 mb-3">
                <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Pedidos Pendientes de Cobro (Hoy)</h6>
            </div>
            <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="color: #94a3b8;">Código</th>
                            <th style="color: #94a3b8;">Vendedor</th>
                            <th style="color: #94a3b8; text-align: right;">Total</th>
                            <th style="color: #94a3b8; text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-pendientes-tbody">
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">
                                Cargando pedidos pendientes...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="custom-card p-4">
            <div class="border-bottom border-secondary pb-2 mb-3">
                <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Artículos en el Pedido</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="color: #94a3b8;">Medicamento</th>
                            <th style="color: #94a3b8;">Cant.</th>
                            <th style="color: #94a3b8; text-align: right;">P. Unitario</th>
                            <th style="color: #94a3b8; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="orden-items-tbody">
                        <tr>
                            <td colspan="4" class="text-center py-5" style="color: #94a3b8; font-size: 14px;">
                                <span class="material-symbols-outlined d-block fs-1 mb-2" style="opacity: 0.4; color: #94a3b8;">folder_open</span>
                                Ningún pedido cargado en caja actualmente.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="custom-card p-4">
            <div class="border-bottom border-secondary pb-2 mb-4">
                <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Detalle de Liquidación</h6>
            </div>
            
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <span style="color: #94a3b8; font-size: 13.5px; font-weight: 500;">Estado del Ticket:</span>
                <span class="badge px-2.5 py-1.5 font-monospace" id="caja-estado-badge" style="background-color: #475569 !important; color: #ffffff;">Ninguno</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span style="color: #94a3b8; font-size: 13.5px; font-weight: 500;">Vendedor:</span>
                <span class="fw-semibold" id="caja-vendedor" style="color: #f1f5f9;">--</span>
            </div>

            <hr class="border-secondary my-4">

            <div class="d-flex justify-content-between mb-2.5">
                <span style="color: #94a3b8; font-size: 13.5px;">Monto Gravado:</span>
                <span class="font-monospace" id="caja-subtotal" style="color: #f1f5f9;">C$ 0.00</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span style="color: #94a3b8; font-size: 13.5px;">I.V.A (15%):</span>
                <span class="font-monospace" id="caja-iva" style="color: #f1f5f9;">C$ 0.00</span>
            </div>
            
            <div class="d-flex justify-content-between mb-4 pt-3 border-top border-secondary border-dashed align-items-center">
                <span class="fw-bold text-success fs-6">TOTAL NETO:</span>
                <span class="fw-bold text-success fs-4 font-monospace" id="caja-total">C$ 0.00</span>
            </div>

            <div class="mb-4">
                <label class="d-block mb-2" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #cbd5e1;">Paga Con (C$):</label>
                <input type="number" id="input-paga-con" class="form-control form-control-sif w-100 text-end fw-bold fs-5 text-success font-monospace" placeholder="0.00" disabled>
            </div>

            <div class="d-flex justify-content-between mb-4 align-items-center">
                <span style="color: #94a3b8; font-size: 13.5px;">Cambio a devolver:</span>
                <span class="fw-bold text-warning fs-5 font-monospace" id="caja-cambio">C$ 0.00</span>
            </div>

            <button class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2" id="btn-cobrar" disabled>
                <span class="material-symbols-outlined">point_of_sale</span> Procesar Cobro
            </button>
        </div>
    </div>
</div>

<script>
{
    let loadedTicket = null;

    const formBuscar = document.getElementById('form-buscar-ticket');
    const inputBuscar = document.getElementById('buscar-n-ticket');
    const tbodyItems = document.getElementById('orden-items-tbody');
    const tbodyPendientes = document.getElementById('tickets-pendientes-tbody');

    const stateBadge = document.getElementById('caja-estado-badge');
    const vendedorEl = document.getElementById('caja-vendedor');
    const subtotalEl = document.getElementById('caja-subtotal');
    const ivaEl = document.getElementById('caja-iva');
    const totalEl = document.getElementById('caja-total');
    const inputPaga = document.getElementById('input-paga-con');
    const cambioEl = document.getElementById('caja-cambio');
    const btnCobrar = document.getElementById('btn-cobrar');

    // Métricas del cajero (Hoy)
    const metricRecaudado = document.getElementById('caja-total-dia');
    const metricPagados = document.getElementById('caja-tickets-pagados');
    const metricPendientes = document.getElementById('caja-preventas-pendientes');

    function cargarMetricasCaja() {
        fetch('../../controllers/caja/CajaController.php?action=metricas')
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    metricRecaudado.innerText = 'C$ ' + parseFloat(response.data.recaudacion_hoy).toFixed(2);
                    metricPagados.innerText = response.data.tickets_pagados + ' Tickets';
                    metricPendientes.innerText = response.data.tickets_pendientes + ' Pendientes';
                }
            })
            .catch(err => console.error("Error al cargar métricas de caja:", err));
    }

    function cargarTicketsPendientesListado() {
        fetch('../../controllers/caja/CajaController.php?action=listar_pendientes')
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    renderTicketsPendientes(response.data);
                }
            })
            .catch(err => {
                console.error("Error al cargar listado de tickets:", err);
                tbodyPendientes.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Error al conectar con el servidor.</td></tr>';
            });
    }

    function renderTicketsPendientes(tickets) {
        if (tickets.length === 0) {
            tbodyPendientes.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No hay pedidos pendientes de cobro hoy.</td></tr>';
            return;
        }

        let html = '';
        tickets.forEach(t => {
            html += `
                <tr>
                    <td><code class="text-success font-bold" style="font-size:13px;">${t.codigo_ticket}</code></td>
                    <td class="text-light">${t.nombre_vendedor}</td>
                    <td class="text-end text-light font-monospace">C$ ${parseFloat(t.total).toFixed(2)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-success py-1 px-3 d-inline-flex align-items-center gap-1" style="font-size: 11px; background-color: #10b981; border: none; border-radius: 4px; font-weight:600;" onclick="window.cobrarTicketDesdeLista('${t.codigo_ticket}')">
                            <span class="material-symbols-outlined" style="font-size:13px;">point_of_sale</span> Cobrar
                        </button>
                    </td>
                </tr>
            `;
        });
        tbodyPendientes.innerHTML = html;
    }

    window.cobrarTicketDesdeLista = function(codigo) {
        inputBuscar.value = codigo;
        // Lanzar consulta de búsqueda
        buscarTicketPorCodigo(codigo);
    };

    formBuscar.addEventListener('submit', function(e) {
        e.preventDefault();
        const codigo = inputBuscar.value.toUpperCase().trim();
        if (!codigo) return;
        buscarTicketPorCodigo(codigo);
    });

    function buscarTicketPorCodigo(codigo) {
        fetch(`../../controllers/caja/CajaController.php?action=buscar&codigo=${codigo}`)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    loadedTicket = response.data;
                    mostrarTicketEnCaja(loadedTicket);
                } else {
                    alert('Error: ' + response.message);
                    resetDetallesCaja();
                }
            })
            .catch(err => {
                console.error("Error al buscar pedido:", err);
                alert("Ocurrió un error al buscar el pedido.");
            });
    }

    function mostrarTicketEnCaja(ticket) {
        // Renderizar items
        let html = '';
        ticket.items.forEach(item => {
            const sub = parseFloat(item.precio_unitario) * parseInt(item.cantidad);
            html += `
                <tr>
                    <td class="text-light">${item.nombre_commercial}</td>
                    <td class="text-center text-light">${item.cantidad}</td>
                    <td class="text-end text-light">C$ ${parseFloat(item.precio_unitario).toFixed(2)}</td>
                    <td class="text-end text-light">C$ ${sub.toFixed(2)}</td>
                </tr>
            `;
        });
        tbodyItems.innerHTML = html;

        // Estado, Vendedor y totales
        stateBadge.innerText = ticket.estado;
        stateBadge.style.backgroundColor = '#f59e0b'; // Amarillo
        vendedorEl.innerText = ticket.nombre_vendedor;
        
        const total = parseFloat(ticket.total);
        const subtotal = total / 1.15;
        const iva = total - subtotal;

        subtotalEl.innerText = 'C$ ' + subtotal.toFixed(2);
        ivaEl.innerText = 'C$ ' + iva.toFixed(2);
        totalEl.innerText = 'C$ ' + total.toFixed(2);

        // Habilitar pago
        inputPaga.disabled = false;
        inputPaga.value = '';
        cambioEl.innerText = 'C$ 0.00';
        btnCobrar.disabled = false;
    }

    // Calcular cambio
    inputPaga.addEventListener('input', function() {
        if (!loadedTicket) return;
        const total = parseFloat(loadedTicket.total);
        const pagaCon = parseFloat(inputPaga.value);
        if (isNaN(pagaCon) || pagaCon < total) {
            cambioEl.innerText = 'C$ 0.00';
            btnCobrar.disabled = true;
            return;
        }
        const cambio = pagaCon - total;
        cambioEl.innerText = 'C$ ' + cambio.toFixed(2);
        btnCobrar.disabled = false;
    });

    // Procesar Cobro
    btnCobrar.addEventListener('click', function() {
        if (!loadedTicket) return;

        btnCobrar.disabled = true;
        btnCobrar.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Cobrando...`;

        const formData = new FormData();
        formData.append('id_ticket', loadedTicket.id_ticket);

        fetch('../../controllers/caja/CajaController.php?action=cobrar', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    alert('¡Cobro Procesado Exitosamente!\nStock actualizado en el inventario.');
                    resetDetallesCaja();
                    cargarMetricasCaja();
                    cargarTicketsPendientesListado();
                } else {
                    alert('Error: ' + response.message);
                    btnCobrar.disabled = false;
                }
            })
            .catch(err => {
                console.error("Error al procesar cobro:", err);
                alert("Ocurrió un error al registrar el pago.");
                btnCobrar.disabled = false;
            })
            .finally(() => {
                btnCobrar.innerHTML = `<span class="material-symbols-outlined">point_of_sale</span> Procesar Cobro`;
            });
    });

    function resetDetallesCaja() {
        loadedTicket = null;
        tbodyItems.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5" style="color: #94a3b8; font-size: 14px;">
                    <span class="material-symbols-outlined d-block fs-1 mb-2" style="opacity: 0.4; color: #94a3b8;">folder_open</span>
                    Ningún pedido cargado en caja actualmente.
                </td>
            </tr>
        `;
        stateBadge.innerText = 'Ninguno';
        stateBadge.style.backgroundColor = '#475569';
        vendedorEl.innerText = '--';
        subtotalEl.innerText = 'C$ 0.00';
        ivaEl.innerText = 'C$ 0.00';
        totalEl.innerText = 'C$ 0.00';
        inputPaga.value = '';
        inputPaga.disabled = true;
        cambioEl.innerText = 'C$ 0.00';
        btnCobrar.disabled = true;
        inputBuscar.value = '';
    }

    cargarMetricasCaja();
    cargarTicketsPendientesListado();
}
</script>