<div class="custom-card mb-4 p-4">
    <div class="border-bottom border-secondary pb-2 mb-3">
        <h6 class="card-title-custom mb-0" style="color: #cbd5e1;">Historial de Cobros (Hoy)</h6>
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

<script>
    {
        const tbodyHistorial = document.getElementById('historial-cobros-tbody');

        function cargarHistorialCobros() {
            fetch('../../controllers/caja/CajaController.php?action=listar_pagados')
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        renderHistorial(response.data);
                    } else {
                        tbodyHistorial.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Error: ${response.message}</td></tr>`;
                    }
                })
                .catch(err => {
                    console.error("Error al cargar historial de cobros:", err);
                    tbodyHistorial.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Ocurrió un error al cargar los datos.</td></tr>`;
                });
        }

        function renderHistorial(tickets) {
            if (tickets.length === 0) {
                tbodyHistorial.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-5"><span class="material-symbols-outlined d-block fs-1 mb-2" style="opacity: 0.4;">receipt_long</span>No hay cobros registrados el día de hoy.</td></tr>`;
                return;
            }

            let html = '';
            tickets.forEach(t => {
                const hora = new Date(t.fecha_creacion).toLocaleTimeString();
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
                </tr>
            `;
            });
            tbodyHistorial.innerHTML = html;
        }

        // Inicializar carga
        cargarHistorialCobros();
    }
</script>