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
                    <h5 class="metric-title">Ventas del Turno</h5>
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
                    <h5 class="metric-title">Total Facturado</h5>
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

<script>
{
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

    cargarMetricasResumen();
}
</script>
