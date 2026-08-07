<?php
/*
 * Archivo: views/Interfaz_admin/botones_menu/Reportes.php
 * Propósito: Vista para la visualización y generación de reportes administrativos.
 */
?>
<link rel="stylesheet" href="../../assets/css/admin/reportes.css">

<div class="container-fluid p-0">
    <!-- FILTROS DE REPORTE -->
    <div class="report-filter-container">
        <div class="report-filter-title">
            <span class="material-symbols-outlined align-middle me-1" style="font-size: 18px;">filter_alt</span>
            Configurar Reporte Contable
        </div>
        
        <form id="form-reporte">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="filter-label mb-2" style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">Período</label>
                    <select id="select-periodo" class="report-select">
                        <option value="diario" selected>Diario (Hoy)</option>
                        <option value="semanal">Semanal (Últimos 7 días)</option>
                        <option value="mensual">Mensual (Mes Actual)</option>
                        <option value="personalizado">Rango Personalizado</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-3 date-range-group d-none">
                    <label class="filter-label mb-2" style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">Desde</label>
                    <input type="date" id="reporte-fecha-inicio" class="report-input-date">
                </div>
                
                <div class="col-12 col-md-3 date-range-group d-none">
                    <label class="filter-label mb-2" style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">Hasta</label>
                    <input type="date" id="reporte-fecha-fin" class="report-input-date">
                </div>
                
                <div class="col-12 col-md-4 ms-md-auto d-flex gap-2">
                    <button type="submit" class="btn-report-generate flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                        <span class="material-symbols-outlined" style="font-size: 20px;">analytics</span>
                        Generar Reporte
                    </button>
                    <button type="button" id="btn-exportar-pdf" class="btn btn-danger d-flex align-items-center justify-content-center gap-2 px-3 fw-bold" style="border-radius: 8px;">
                        <span class="material-symbols-outlined" style="font-size: 20px;">picture_as_pdf</span>
                        Imprimir PDF
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TARJETAS DE MÉTRICAS -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="report-metric-card recaudado">
                <div class="report-metric-title">Recaudación Total</div>
                <div class="report-metric-value" id="val-recaudado">C$ 0.00</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="report-metric-card tickets">
                <div class="report-metric-title">Ventas Totales</div>
                <div class="report-metric-value" id="val-tickets">0 Tickets</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="report-metric-card promedio">
                <div class="report-metric-title">Ticket Promedio</div>
                <div class="report-metric-value" id="val-promedio">C$ 0.00</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="report-metric-card estrella">
                <div class="report-metric-title">Producto Estrella</div>
                <div class="report-metric-value" id="val-estrella" style="font-size: 16px; min-height: 36px; display: flex; align-items: center;">Ninguno</div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE GRÁFICOS SENCILLOS -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="chart-container-box">
                <div class="chart-title d-flex align-items-center justify-content-between">
                    <span>Ventas por Categoría (Monto)</span>
                    <span class="material-symbols-outlined text-success" style="font-size: 20px;">category</span>
                </div>
                <div id="chart-categorias" class="d-flex flex-column gap-2" style="min-height: 200px; justify-content: center;">
                    <!-- Se carga mediante JS -->
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="chart-container-box">
                <div class="chart-title d-flex align-items-center justify-content-between">
                    <span>Rendimiento de Vendedores</span>
                    <span class="material-symbols-outlined text-primary" style="font-size: 20px;">badge</span>
                </div>
                <div id="chart-vendedores" class="d-flex flex-column gap-2" style="min-height: 200px; justify-content: center;">
                    <!-- Se carga mediante JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA DE TRANSACCIONES DETALLADAS -->
    <div class="report-table-card">
        <div class="border-bottom border-secondary pb-2 mb-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title-custom mb-0">Detalle de Transacciones del Periodo</h6>
            <span class="badge bg-secondary-box text-light" id="total-registros">0 registros</span>
        </div>
        
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Código Ticket</th>
                        <th>Fecha y Hora</th>
                        <th>Vendedor</th>
                        <th>Cliente</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody id="report-table-body">
                    <tr>
                        <td colspan="5" class="text-center text-wait-custom py-4">Haz clic en Generar Reporte para ver los datos.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    const selectPeriodo = document.getElementById('select-periodo');
    const dateRangeGroups = document.querySelectorAll('.date-range-group');
    const formReporte = document.getElementById('form-reporte');
    
    // Configurar fechas por defecto para el rango personalizado (hoy y hace una semana)
    const hoy = new Date().toISOString().split('T')[0];
    const haceUnaSemana = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    document.getElementById('reporte-fecha-inicio').value = haceUnaSemana;
    document.getElementById('reporte-fecha-fin').value = hoy;

    // Mostrar/ocultar rango de fechas según período
    selectPeriodo.addEventListener('change', () => {
        if (selectPeriodo.value === 'personalizado') {
            dateRangeGroups.forEach(el => el.classList.remove('d-none'));
        } else {
            dateRangeGroups.forEach(el => el.classList.add('d-none'));
        }
    });

    // Enviar formulario y cargar reporte
    formReporte.addEventListener('submit', (e) => {
        e.preventDefault();
        cargarDatosReporte();
    });

    // Imprimir o Exportar a PDF
    const btnExportarPdf = document.getElementById('btn-exportar-pdf');
    if (btnExportarPdf) {
        btnExportarPdf.addEventListener('click', () => {
            const periodo = selectPeriodo.value;
            const fechaInicio = document.getElementById('reporte-fecha-inicio').value;
            const fechaFin = document.getElementById('reporte-fecha-fin').value;

            let url = `../../controllers/admin/GenerarReportePDF.php?periodo=${periodo}`;
            if (periodo === 'personalizado') {
                url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
            }
            window.open(url, '_blank');
        });
    }

    function cargarDatosReporte() {
        const periodo = selectPeriodo.value;
        const fechaInicio = document.getElementById('reporte-fecha-inicio').value;
        const fechaFin = document.getElementById('reporte-fecha-fin').value;

        let url = `../../controllers/admin/ReportesController.php?periodo=${periodo}`;
        if (periodo === 'personalizado') {
            url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        }

        // Mostrar estados de carga
        document.getElementById('val-recaudado').innerText = '...';
        document.getElementById('val-tickets').innerText = '...';
        document.getElementById('val-promedio').innerText = '...';
        document.getElementById('val-estrella').innerText = 'Cargando...';
        document.getElementById('chart-categorias').innerHTML = '<div class="text-center text-muted"><div class="spinner-border spinner-border-sm text-success" role="status"></div></div>';
        document.getElementById('chart-vendedores').innerHTML = '<div class="text-center text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
        document.getElementById('report-table-body').innerHTML = '<tr><td colspan="5" class="text-center text-wait-custom py-4">Generando reporte contable...</td></tr>';

        fetch(url)
            .then(res => {
                if(!res.ok) throw new Error('Error al conectar con el servidor.');
                return res.json();
            })
            .then(response => {
                if (response.status === 'success') {
                    renderizarReporte(response.data);
                } else {
                    alert('Error: ' + response.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Ocurrió un error al procesar el reporte.');
            });
    }

    function renderizarReporte(data) {
        // 1. Renderizar Métricas
        document.getElementById('val-recaudado').innerText = 'C$ ' + data.metricas.total_recaudado.toFixed(2);
        document.getElementById('val-tickets').innerText = data.metricas.total_tickets + ' Tickets';
        document.getElementById('val-promedio').innerText = 'C$ ' + data.metricas.ticket_promedio.toFixed(2);
        document.getElementById('val-estrella').innerText = data.metricas.producto_estrella;

        // 2. Renderizar Gráfico de Categorías
        const chartCategorias = document.getElementById('chart-categorias');
        if (data.categorias.length === 0) {
            chartCategorias.innerHTML = '<span class="text-center text-sub-wait">Sin datos para el período seleccionado.</span>';
        } else {
            // Obtener el valor máximo para calcular proporciones
            const maxMonto = Math.max(...data.categorias.map(c => c.total_monto));
            let htmlCats = '';
            data.categorias.forEach((c, idx) => {
                const porcentaje = maxMonto > 0 ? (c.total_monto / maxMonto) * 100 : 0;
                const fillClass = idx % 3 === 0 ? 'bar-fill-green' : (idx % 3 === 1 ? 'bar-fill-blue' : 'bar-fill-purple');
                htmlCats += `
                    <div class="custom-bar-row">
                        <div class="bar-info">
                            <span class="bar-label">${c.nombre_categoria}</span>
                            <span class="bar-value">C$ ${c.total_monto.toFixed(2)}</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill ${fillClass}" style="width: ${porcentaje}%"></div>
                        </div>
                    </div>
                `;
            });
            chartCategorias.innerHTML = htmlCats;
        }

        // 3. Renderizar Gráfico de Vendedores
        const chartVendedores = document.getElementById('chart-vendedores');
        if (data.vendedores.length === 0) {
            chartVendedores.innerHTML = '<span class="text-center text-sub-wait">Sin datos para el período seleccionado.</span>';
        } else {
            const maxMontoVend = Math.max(...data.vendedores.map(v => v.total_monto));
            let htmlVends = '';
            data.vendedores.forEach((v, idx) => {
                const porcentaje = maxMontoVend > 0 ? (v.total_monto / maxMontoVend) * 100 : 0;
                const fillClass = idx % 2 === 0 ? 'bar-fill-blue' : 'bar-fill-purple';
                htmlVends += `
                    <div class="custom-bar-row">
                        <div class="bar-info">
                            <span class="bar-label">${v.vendedor} <small class="text-muted">(${v.total_tickets} vnt)</small></span>
                            <span class="bar-value">C$ ${v.total_monto.toFixed(2)}</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill ${fillClass}" style="width: ${porcentaje}%"></div>
                        </div>
                    </div>
                `;
            });
            chartVendedores.innerHTML = htmlVends;
        }

        // 4. Renderizar Tabla de transacciones
        const tbody = document.getElementById('report-table-body');
        document.getElementById('total-registros').innerText = `${data.transacciones.length} registros`;
        
        if (data.transacciones.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-wait-custom py-4">No se encontraron transacciones en este periodo.</td></tr>';
        } else {
            let htmlTable = '';
            data.transacciones.forEach(t => {
                const fecha = new Date(t.fecha_creacion).toLocaleString();
                htmlTable += `
                    <tr>
                        <td><code class="text-success font-bold">${t.codigo_ticket}</code></td>
                        <td class="text-light" style="font-size: 13.5px;">${fecha}</td>
                        <td class="text-light" style="font-size: 13.5px;">${t.vendedor}</td>
                        <td class="text-light" style="font-size: 13.5px;">${t.cliente ? t.cliente : '<span class="text-muted">Cliente Final</span>'}</td>
                        <td class="text-end font-monospace text-success fw-bold">C$ ${t.total.toFixed(2)}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = htmlTable;
        }
    }

    // Cargar reporte inicial automáticamente al abrir
    cargarDatosReporte();
})();
</script>
