<div class="custom-card mb-4 p-4">
    <div class="border-bottom border-secondary pb-2 mb-3">
        <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Filtro de Cobros Realizados</h6>
    </div>
    <div class="d-flex gap-3">
        <input type="text" class="input-sif-dark flex-grow-1" placeholder="Buscar por nombre del cliente o N° factura cobrada...">
        <button class="btn btn-secondary d-flex align-items-center gap-2 px-4" style="background-color: #334155; border:none; border-radius: 8px; font-weight: 600;">
            <span class="material-symbols-outlined">filter_list</span> Filtrar
        </button>
    </div>
</div>

<div class="custom-card p-4">
    <div class="border-bottom border-secondary pb-2 mb-3">
        <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Bitácora de Ventas del Turno</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="color: #94a3b8;">Hora Cobro</th>
                    <th style="color: #94a3b8;">N° Factura</th>
                    <th style="color: #94a3b8;">Cliente</th>
                    <th style="color: #94a3b8;">Monto</th>
                    <th style="color: #94a3b8;">Estado</th>
                    <th style="color: #94a3b8;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="text-center py-5" style="color: #94a3b8; font-size: 14px;">
                        <span class="material-symbols-outlined d-block fs-1 mb-2" style="opacity: 0.4; color: #94a3b8;">tray</span>
                        No se encontraron transacciones registradas en el turno actual.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>