<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="metric-card card-primary">
            <div>
                <p class="metric-title">Recaudación Total (Hoy)</p>
                <h3 class="metric-value">C$ 0.00</h3>
            </div>
            <div class="metric-icon-box bg-primary-box">
                <span class="material-symbols-outlined">real_estate_agent</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="metric-card card-secondary">
            <div>
                <p class="metric-title">Tickets Pagados</p>
                <h3 class="metric-value">0 Tickets</h3>
            </div>
            <div class="metric-icon-box bg-secondary-box">
                <span class="material-symbols-outlined">confirmation_number</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="metric-card card-danger">
            <div>
                <p class="metric-title">Preventas por Cobrar</p>
                <h3 class="metric-value">0 Pendientes</h3>
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
                <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Buscar Orden de Venta</h6>
            </div>
            <form method="POST" action="" class="d-flex gap-3" id="form-buscar-ticket">
                <input type="text" name="n_ticket" class="input-sif-dark flex-grow-1" placeholder="Ingrese el número de ticket o preventa..." autocomplete="off" required>
                <button type="submit" class="btn btn-success d-flex align-items-center gap-2 px-4" style="background-color: #10b981; border: none; font-weight: 600; border-radius: 8px;">
                    <span class="material-symbols-outlined">search</span> Buscar
                </button>
            </form>
        </div>

        <div class="custom-card p-4">
            <div class="border-bottom border-secondary pb-2 mb-3">
                <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Artículos en la Orden</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="color: #94a3b8;">Medicamento</th>
                            <th style="color: #94a3b8;">Laboratorio</th>
                            <th style="color: #94a3b8;">Cant.</th>
                            <th style="color: #94a3b8;">P. Unitario</th>
                            <th style="color: #94a3b8;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-5" style="color: #94a3b8; font-size: 14px;">
                                <span class="material-symbols-outlined d-block fs-1 mb-2" style="opacity: 0.4; color: #94a3b8;">folder_open</span>
                                Ningún ticket cargado en caja actualmente.
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
                <span class="badge px-2.5 py-1.5 font-monospace" style="background-color: #475569 !important; color: #ffffff;">Ninguno</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span style="color: #94a3b8; font-size: 13.5px; font-weight: 500;">Cliente:</span>
                <span class="fw-semibold" style="color: #f1f5f9;">-- / --</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span style="color: #94a3b8; font-size: 13.5px; font-weight: 500;">Vendedor:</span>
                <span class="fw-semibold" style="color: #f1f5f9;">--</span>
            </div>

            <hr class="border-secondary my-4">

            <div class="d-flex justify-content-between mb-2.5">
                <span style="color: #94a3b8; font-size: 13.5px;">Monto Gravado:</span>
                <span class="font-monospace" style="color: #f1f5f9;">C$ 0.00</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span style="color: #94a3b8; font-size: 13.5px;">I.V.A (0%):</span>
                <span class="font-monospace" style="color: #f1f5f9;">C$ 0.00</span>
            </div>
            
            <div class="d-flex justify-content-between mb-4 pt-3 border-top border-secondary border-dashed align-items-center">
                <span class="fw-bold text-success fs-6">TOTAL NETO:</span>
                <span class="fw-bold text-success fs-4 font-monospace">C$ 0.00</span>
            </div>

            <div class="mb-4">
                <label class="d-block mb-2" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #cbd5e1;">Paga Con (C$):</label>
                <input type="number" class="input-sif-dark w-100 text-end fw-bold fs-5 text-success font-monospace" placeholder="0.00" disabled>
            </div>

            <div class="d-flex justify-content-between mb-4 align-items-center">
                <span style="color: #94a3b8; font-size: 13.5px;">Cambio a devolver:</span>
                <span class="fw-bold text-warning fs-5 font-monospace">C$ 0.00</span>
            </div>

            <button class="btn btn-success w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2" style="background-color: #10b981; border: none; border-radius: 8px; font-size: 15px;" disabled>
                <span class="material-symbols-outlined">point_of_sale</span> Procesar Cobro
            </button>
        </div>
    </div>
</div>