<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../models/ventas/TicketModel.php';

$id_usuario_actual = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
$turnoAbierto = obtenerTurnoCajaAbierto($conexion, $id_usuario_actual);
$aperturaCaja = ($turnoAbierto) ? floatval($turnoAbierto['monto_inicial']) : 1000.00;

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
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 5.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="5" min="0" value="0" style="width: 120px;">
                </div>

                <!-- Monedas -->
                <div class="col-12 mt-4"><span class="badge bg-slate border border-secondary text-secondary w-100 py-2 fs-6">MONEDAS</span></div>
                
                <div class="col-md-6 d-flex align-items-center justify-content-between mb-2">
                    <label class="text-light m-0 font-monospace" style="width: 100px;">C$ 5.00</label>
                    <input type="number" class="form-control form-control-sif text-end input-denom" data-value="5" min="0" value="0" style="width: 120px;">
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
                    <span style="color: #000000 !important; font-weight: 600;">Apertura de Caja:</span>
                    <span class="font-monospace" style="color: #000000 !important; font-weight: 700;">C$ <?php echo number_format($aperturaCaja, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2.5">
                    <span style="color: #000000 !important; font-weight: 600;">Ventas Recaudadas (Hoy):</span>
                    <span class="font-monospace" style="color: #000000 !important; font-weight: 700;">C$ <?php echo number_format($totalVentasHoy, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span style="color: #000000 !important; font-weight: 700;">Total Esperado en Caja:</span>
                    <span class="font-monospace" style="color: #000000 !important; font-weight: 700;">C$ <?php echo number_format($totalEsperado, 2); ?></span>
                </div>

                <hr class="border-secondary my-4">

                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="fw-bold text-success" style="font-size: 15px;">TOTAL FÍSICO ARQUEADO:</span>
                    <span class="fw-bold text-success fs-3 font-monospace" id="arqueo-total-fisico">C$ 0.00</span>
                </div>

                <div class="d-flex justify-content-between mb-4 align-items-center p-3 rounded bg-slate border border-secondary">
                    <span class="fw-semibold" style="color: #000000 !important; font-size: 13.5px; font-weight: 700;">Diferencia (Cuadre):</span>
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

<div id="banner-arqueo-registrado" class="alert alert-success d-none mb-3 border-0 shadow-sm" style="background-color: #10b981; color: #ffffff;">
    <div class="d-flex align-items-center gap-2">
        <span class="material-symbols-outlined">check_circle</span>
        <div>
            <strong>Arqueo de caja ya registrado hoy</strong>
            <div id="banner-arqueo-texto-detalle" style="font-size: 12px;">El cierre del turno fue guardado en la base de datos de caja.</div>
        </div>
    </div>
</div>

<script>
{
    const totalEsperado = <?php echo $totalEsperado; ?>;
    const aperturaCaja = <?php echo $aperturaCaja; ?>;
    const inputDenoms = document.querySelectorAll('.input-denom');
    const totalFisicoEl = document.getElementById('arqueo-total-fisico');
    const diferenciaEl = document.getElementById('arqueo-diferencia');
    const btnGuardar = document.getElementById('btn-guardar-arqueo');
    const bannerRegistrado = document.getElementById('banner-arqueo-registrado');
    const bannerDetalle = document.getElementById('banner-arqueo-texto-detalle');

    let currentTotalFisico = 0.0;
    let currentDiferencia = 0.0;

    function recalcularArqueo() {
        let totalFisico = 0.0;
        
        inputDenoms.forEach(input => {
            const denominacion = parseFloat(input.getAttribute('data-value'));
            const cantidad = parseInt(input.value) || 0;
            totalFisico += denominacion * cantidad;
        });

        currentTotalFisico = totalFisico;
        totalFisicoEl.innerText = 'C$ ' + totalFisico.toFixed(2);

        const diferencia = totalFisico - totalEsperado;
        currentDiferencia = diferencia;
        
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

    // Cargar si ya se registro un arqueo hoy en la base de datos
    fetch('../../controllers/caja/CajaController.php?action=obtener_arqueo_hoy')
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success' && response.registrado && response.data) {
                const c = response.data;
                if (bannerRegistrado) {
                    bannerRegistrado.classList.remove('d-none');
                    if (bannerDetalle) {
                        bannerDetalle.innerText = `Cierre guardado el ${new Date(c.fecha_cierre).toLocaleString()} | Arqueado: C$ ${parseFloat(c.monto_final).toFixed(2)}`;
                    }
                }

                if (c.denominaciones) {
                    try {
                        const denomsObj = JSON.parse(c.denominaciones);
                        inputDenoms.forEach(input => {
                            const valKey = input.getAttribute('data-value');
                            if (denomsObj[valKey] !== undefined) {
                                input.value = denomsObj[valKey];
                            }
                        });
                        recalcularArqueo();
                    } catch(e) {
                        console.error("Error al parsear denominaciones:", e);
                    }
                }
            }
        })
        .catch(err => console.error("Error al consultar arqueo guardado:", err));

    btnGuardar.addEventListener('click', function() {
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Guardando...`;

        const denominacionesMap = {};
        inputDenoms.forEach(input => {
            const denom = input.getAttribute('data-value');
            const cant = parseInt(input.value) || 0;
            denominacionesMap[denom] = cant;
        });

        const formData = new FormData();
        formData.append('monto_inicial', aperturaCaja);
        formData.append('monto_esperado', totalEsperado);
        formData.append('monto_fisico', currentTotalFisico);
        formData.append('diferencia', currentDiferencia);
        formData.append('denominaciones', JSON.stringify(denominacionesMap));

        fetch('../../controllers/caja/CajaController.php?action=guardar_arqueo', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success') {
                alert("¡Arqueo Registrado y Guardado con Éxito!\nEl registro se guardó correctamente en la tabla de Cierres de Caja.");
                window.location.href = '../../controllers/auth/logout.php';
            } else {
                alert('Error al guardar arqueo: ' + response.message);
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = `<span class="material-symbols-outlined">save</span> Registrar Arqueo de Caja`;
            }
        })
        .catch(err => {
            console.error("Error al registrar arqueo:", err);
            alert("Ocurrió un error al intentar guardar el arqueo de caja.");
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = `<span class="material-symbols-outlined">save</span> Registrar Arqueo de Caja`;
        });
    });

    recalcularArqueo();
}
</script>
