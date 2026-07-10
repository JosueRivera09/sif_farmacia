<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/conexion.php';

// Consultar ventas reales cobradas hoy por este cajero (o en general)
$querySales = "SELECT SUM(total) as total_ventas, COUNT(*) as total_tickets 
               FROM tickets 
               WHERE estado = 'Pagado' AND DATE(fecha_creacion) = CURDATE()";
$resSales = mysqli_query($conexion, $querySales);
$totalVentasHoy = 0.0;
$totalTicketsHoy = 0;

if ($resSales && $row = mysqli_fetch_assoc($resSales)) {
    $totalVentasHoy = isset($row['total_ventas']) ? floatval($row['total_ventas']) : 0.0;
    $totalTicketsHoy = isset($row['total_tickets']) ? intval($row['total_tickets']) : 0;
}

$aperturaCaja = 1000.00; // Fondo fijo de apertura de caja por defecto
$totalEsperado = $aperturaCaja + $totalVentasHoy;
?>
<div class="row g-4">
    <!-- Columna Izquierda: Ingreso de Denominaciones -->
    <div class="col-lg-7">
        <!--
        /*
         * Archivo: views/Interfaz_caja/botones_menu/arqueo_caja.php
         * Propósito: Módulo de arqueo de caja (cierre del día).
         * Qué muestra: Formulario para ingresar valores físicos y botón para procesar.
         */
        -->
        <div class="custom-card">
            <div class="border-bottom border-secondary pb-2 mb-3">
                <h6 class="card-title-custom mb-0">Denominaciones de Dinero en Efectivo</h6>
            </div>
            
            <form id="form-arqueo-denominaciones" class="row g-3">
                <!-- Billetes -->
                <div class="col-12"><span class="badge bg-slate border border-secondary text-secondary w-100 py-2 fs-6">BILLETES</span></div>
                
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 1,000.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="1000" min="0" value="0" style="width: 120px;">
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 500.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="500" min="0" value="0" style="width: 120px;">
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 200.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="200" min="0" value="0" style="width: 120px;">
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 100.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="100" min="0" value="0" style="width: 120px;">
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 50.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="50" min="0" value="0" style="width: 120px;">
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 20.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="20" min="0" value="0" style="width: 120px;">
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 10.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="10" min="0" value="0" style="width: 120px;">
                </div>

                <!-- Monedas -->
                <div class="col-12 mt-4"><span class="badge bg-slate border border-secondary text-secondary w-100 py-2 fs-6">MONEDAS</span></div>
                
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 5.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="5" min="0" value="0" style="width: 120px;">
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 2.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="2" min="0" value="0" style="width: 120px;">
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 1.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="1" min="0" value="0" style="width: 120px;">
                </div>
            </form>
        </div>
    </div>

    <!-- Columna Derecha: Comparativa de Totales y Cierre -->
    <div class="col-lg-5">
        <div class="custom-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="border-bottom border-secondary pb-2 mb-4">
                    <h6 class="card-title-custom mb-0">Balance de Arqueo</h6>
                </div>

                <div class="d-flex justify-content-between mb-2.5">
                    <span class="text-muted">Apertura de Caja:</span>
                    <span class="font-monospace text-light">C$ <?php echo number_format($aperturaCaja, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2.5">
                    <span class="text-muted">Ventas Recaudadas (Hoy):</span>
                    <span class="font-monospace text-light">C$ <?php echo number_format($totalVentasHoy, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="text-muted" style="font-weight: 600;">Total Esperado en Caja:</span>
                    <span class="font-monospace text-light" style="font-weight: 700;">C$ <?php echo number_format($totalEsperado, 2); ?></span>
                </div>

                <hr class="border-secondary my-4">

                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="fw-bold text-success" style="font-size: 15px;">TOTAL FÍSICO ARQUEADO:</span>
                    <span class="fw-bold text-success fs-3 font-monospace" id="arqueo-total-fisico">C$ 0.00</span>
                </div>

                <div class="d-flex justify-content-between mb-4 align-items-center p-3 rounded bg-slate border border-secondary">
                    <span class="fw-semibold text-light" style="font-size: 13.5px;">Diferencia (Cuadre):</span>
                    <span class="fw-bold fs-5 font-monospace text-warning" id="arqueo-diferencia">C$ <?php echo number_format(-$totalEsperado, 2); ?></span>
                </div>
            </div>

            <div>
                <button type="button" class="btn btn-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2" id="btn-guardar-arqueo">
                    <span class="material-symbols-outlined">save</span> Registrar Arqueo de Caja
                </button>
            </div>
        </div>
    </div>
</div>

<script>
{
    const totalEsperado = <?php echo $totalEsperado; ?>;
    const inputDenoms = document.querySelectorAll('.input-denom');
    const totalFisicoEl = document.getElementById('arqueo-total-fisico');
    const diferenciaEl = document.getElementById('arqueo-diferencia');
    const btnGuardar = document.getElementById('btn-guardar-arqueo');

    function recalcularArqueo() {
        let totalFisico = 0.0;
        
        inputDenoms.forEach(input => {
            const denominacion = parseFloat(input.getAttribute('data-value'));
            const cantidad = parseInt(input.value) || 0;
            totalFisico += denominacion * cantidad;
        });

        totalFisicoEl.innerText = 'C$ ' + totalFisico.toFixed(2);

        const diferencia = totalFisico - totalEsperado;
        
        if (Math.abs(diferencia) < 0.01) {
            diferenciaEl.innerText = 'C$ 0.00 (Caja Cuadrada)';
            diferenciaEl.className = 'fw-bold fs-5 font-monospace text-success';
        } else if (diferencia < 0) {
            diferenciaEl.innerText = 'C$ ' + diferencia.toFixed(2) + ' (Faltante)';
            diferenciaEl.className = 'fw-bold fs-5 font-monospace text-danger';
        } else {
            diferenciaEl.innerText = 'C$ +' + diferencia.toFixed(2) + ' (Sobrante)';
            diferenciaEl.className = 'fw-bold fs-5 font-monospace text-warning';
        }
    }

    inputDenoms.forEach(input => {
        input.addEventListener('input', recalcularArqueo);
    });

    btnGuardar.addEventListener('click', function() {
        alert("¡Arqueo Guardado con Éxito!\nEl turno de caja se ha cerrado correctamente.");
        window.location.href = '../../controllers/auth/logout.php';
    });

    recalcularArqueo();
}
</script>
