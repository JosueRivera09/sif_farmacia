<?php
/*
 * Archivo: views/Interfaz_admin/admin_dashboard.php
 * Propósito: Panel de control principal para el rol de Administrador.
 */

session_start();

// Esta es la pantalla del dashboard del Administrador, que contiene accesos rápidos a la gestión de usuarios, productos, reportes y configuraciones.

// Verificar si hay sesión iniciada y si el rol es Administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: ../login.php");
    exit;
}

// Obtener nombre del usuario de la sesión
$nombre_usuario = isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Administrador';
$rol_usuario = isset($_SESSION['rol']) ? htmlspecialchars($_SESSION['rol']) : 'Administrador';
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema SIF - Panel de Administración</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter & JetBrains Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet" />
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- User Management custom CSS -->
    <link rel="stylesheet" href="../../assets/css/admin/usuarios.css">
    
    <link rel="stylesheet" href="../../assets/css/admin/admin_dashboard.css">
</head>
<body class="h-100">

<div class="app-container">
    
    <!-- BEGIN: Sidebar -->
    <?php include_once __DIR__ . '/../sidebar.php'; ?>
    <!-- END: Sidebar -->

    <!-- Main Content Area -->
    <div class="main-content">
        
        <!-- BEGIN: TopHeader -->
        <header class="top-header">
            <h2 class="header-title">Panel de Administración</h2>

            <div class="d-flex align-items-center gap-4">
                <!--Perfil del usuario logueado -->
                <div class="profile-container d-flex align-items-center gap-3 border-start border-secondary-subtle ps-4">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 font-bold" style="font-size: 14px; font-weight: 700; color: #000000 !important;"><?php echo $nombre_usuario; ?></p>
                        <p class="mb-0 text-uppercase" style="font-size: 10px; font-weight: 600; color: #64748b !important; text-transform: uppercase;"><?php echo $rol_usuario; ?></p>
                    </div>
                    <div class="avatar-box d-flex align-items-center justify-content-center text-white fw-bold" style="background-color: #10b981; font-size: 16px;">
                        <?php echo strtoupper(substr($nombre_usuario, 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>
        <!-- END: TopHeader -->

        <!-- Content Body -->
        <main class="content-body custom-scrollbar" id="main-content-area">
            
            <!-- Metrics Section -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4 col-xl-4">
                    <div class="metric-card card-primary">
                        <div>
                            <p class="metric-title">Recaudación</p>
                            <h3 class="metric-value" id="admin-metric-recaudacion">C$ 0.00</h3>
                        </div>
                        <div class="metric-icon-box bg-primary-box">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-4 col-xl-4">
                    <div class="metric-card card-secondary">
                        <div>
                            <p class="metric-title">Facturas de Hoy</p>
                            <h3 class="metric-value" id="admin-metric-facturas">0 Tickets</h3>
                        </div>
                        <div class="metric-icon-box bg-secondary-box">
                            <span class="material-symbols-outlined">receipt</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-4 col-xl-4">
                    <div class="metric-card card-purple">
                        <div>
                            <p class="metric-title">Ingresos Bodega de Hoy</p>
                            <h3 class="metric-value" id="admin-metric-bodega">0 Lotes</h3>
                        </div>
                        <div class="metric-icon-box bg-purple-box">
                            <span class="material-symbols-outlined">move_to_inbox</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Lists -->
            <div class="row g-4">
                <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                    <div class="custom-card height-historial">
                        <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3 gap-2">
                            <h6 class="card-title-custom mb-0">
                                Historial de Ventas del Sistema
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <select id="filtro-ventas-periodo" class="form-select form-select-sm border-secondary text-dark bg-white" style="font-size: 12px; width: 130px; cursor: pointer;">
                                    <option value="todos" selected>Todos</option>
                                    <option value="dia">Hoy (Día)</option>
                                    <option value="semana">Esta Semana</option>
                                    <option value="mes">Este Mes</option>
                                    <option value="anio">Este Año</option>
                                </select>
                                <button id="btn-filtrar-ventas" class="btn btn-sm btn-primary d-flex align-items-center gap-1 px-3" style="font-size: 12px;">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">filter_alt</span> Filtrar
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive custom-scrollbar" id="container-historial-ventas">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Ticket</th>
                                        <th>Cliente</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-table-ventas">
                                    <tr>
                                        <td colspan="5" class="text-center text-wait-custom py-4">Cargando ventas...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="custom-card">
                        <div class="border-bottom border-secondary pb-2 mb-3">
                            <h6 class="card-title-custom mb-0">Usuarios en Sistema</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Rol</th>
                                        <th>Fecha Registro</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-table-usuarios">
                                    <tr>
                                        <td colspan="4" class="text-center text-wait-custom py-2">Cargando usuarios...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="custom-card height-alertas">
                        <div class="border-bottom border-warning pb-2 mb-3">
                            <h6 class="card-title-custom text-warning mb-0 d-flex align-items-center gap-2">
                                <span class="material-symbols-outlined text-warning" style="font-size: 18px;">notifications_active</span> Alertas
                            </h6>
                        </div>
                        
                        <div id="admin-list-alertas" class="d-flex flex-column justify-content-center align-items-center h-75 text-center">
                            <span class="material-symbols-outlined text-secondary fs-1 mb-2">inventory_2</span>
                            <span class="text-wait-custom d-block">Cargando alertas...</span>
                        </div>
                    </div>
                </div>
            </div> 

        </main> 
    </div> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const contentArea = document.getElementById('main-content-area');
    const btnInicio = document.getElementById('btn-inicio');
    const btnUsuarios = document.getElementById('btn-usuarios');
    const btnProductos = document.getElementById('btn-productos');
    const btnBodega = document.getElementById('btn-bodega');
    const btnReportes = document.getElementById('btn-reportes');

    // Almacenar el HTML inicial limpio para restaurarlo
    let vistaInicioHTML = '';

    let ventasOffset = 0;
    let ventasFiltro = 'todos';
    let loadingVentas = false;
    let hasMoreVentas = true;

    function cargarVentasFiltradas(reset = false) {
        const tbodyVentas = document.getElementById('admin-table-ventas');
        if (!tbodyVentas) return;

        if (reset) {
            ventasOffset = 0;
            hasMoreVentas = true;
            tbodyVentas.innerHTML = '<tr><td colspan="5" class="text-center text-wait-custom py-4"><div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>Cargando ventas...</td></tr>';
        }

        if (loadingVentas || (!hasMoreVentas && !reset)) return;
        loadingVentas = true;

        const rowLoadingMore = document.getElementById('row-loading-more');
        if (!reset && rowLoadingMore) {
            rowLoadingMore.classList.remove('d-none');
        }

        fetch(`../../controllers/admin/AdminDashboardController.php?action=ventas_filtradas&filtro=${ventasFiltro}&offset=${ventasOffset}&limit=10`)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const ventas = response.ventas || [];
                    hasMoreVentas = response.has_more;

                    const rowOldLoader = document.getElementById('row-loading-more');
                    if (rowOldLoader) {
                        rowOldLoader.remove();
                    }

                    if (reset) {
                        tbodyVentas.innerHTML = '';
                        if (ventas.length === 0) {
                            tbodyVentas.innerHTML = '<tr><td colspan="5" class="text-center text-wait-custom py-4">No hay ventas registradas para el período seleccionado.</td></tr>';
                            return;
                        }
                    }

                    if (ventas.length > 0) {
                        let htmlVentas = '';
                        ventas.forEach(v => {
                            let fechaHora = 'N/A';
                            if (v.fecha_creacion) {
                                const d = new Date(v.fecha_creacion.replace(' ', 'T'));
                                fechaHora = isNaN(d.getTime()) ? v.fecha_creacion : d.toLocaleString('es-NI', { dateStyle: 'short', timeStyle: 'short' });
                            }
                            htmlVentas += `
                                <tr>
                                    <td style="white-space: nowrap;">${fechaHora}</td>
                                    <td><code class="text-success font-bold">${v.codigo_ticket}</code></td>
                                    <td>${v.cliente ? v.cliente : 'Cliente Final'}</td>
                                    <td class="font-monospace text-success fw-bold">C$ ${parseFloat(v.total).toFixed(2)}</td>
                                    <td><span class="badge px-2 py-1 bg-success-box text-success">Pagado</span></td>
                                </tr>
                            `;
                        });
                        tbodyVentas.insertAdjacentHTML('beforeend', htmlVentas);
                        ventasOffset += ventas.length;
                    }

                    if (hasMoreVentas) {
                        tbodyVentas.insertAdjacentHTML('beforeend', `
                            <tr id="row-loading-more" class="d-none">
                                <td colspan="5" class="text-center py-2 text-muted small">
                                    <div class="spinner-border spinner-border-sm text-success me-1"></div> Cargando más ventas...
                                </td>
                            </tr>
                        `);
                    }
                } else {
                    if (reset) {
                        tbodyVentas.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">${response.message || 'No se pudieron cargar las ventas.'}</td></tr>`;
                    }
                }
            })
            .catch(err => {
                console.error("Error al cargar ventas filtradas:", err);
                if (reset && tbodyVentas) {
                    tbodyVentas.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error de conexión al cargar ventas.</td></tr>';
                }
            })
            .finally(() => {
                loadingVentas = false;
            });
    }

    function cargarDatosDashboard() {
        const selectFiltro = document.getElementById('filtro-ventas-periodo');
        if (selectFiltro) {
            ventasFiltro = selectFiltro.value;
        }

        const tbodyVentas = document.getElementById('admin-table-ventas');
        const tbodyUsuarios = document.getElementById('admin-table-usuarios');
        const listAlertas = document.getElementById('admin-list-alertas');

        if (tbodyVentas) {
            tbodyVentas.innerHTML = '<tr><td colspan="5" class="text-center text-wait-custom py-4"><div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>Cargando ventas...</td></tr>';
        }
        if (tbodyUsuarios) {
            tbodyUsuarios.innerHTML = '<tr><td colspan="4" class="text-center text-wait-custom py-2"><div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>Cargando usuarios...</td></tr>';
        }
        if (listAlertas) {
            listAlertas.innerHTML = '<div class="d-flex flex-column align-items-center justify-content-center h-100 text-center py-4"><div class="spinner-border spinner-border-sm text-warning mb-2"></div><span class="text-wait-custom d-block fw-bold">Cargando alertas...</span></div>';
        }

        fetch(`../../controllers/admin/AdminDashboardController.php?action=todo&filtro=${ventasFiltro}&offset=0&limit=10`)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const data = response.data;
                    
                    // 1. Métricas
                    if (data.metricas) {
                        if (document.getElementById('admin-metric-recaudacion')) document.getElementById('admin-metric-recaudacion').innerText = 'C$ ' + (data.metricas.recaudacion_hoy || 0).toFixed(2);
                        if (document.getElementById('admin-metric-facturas')) document.getElementById('admin-metric-facturas').innerText = (data.metricas.facturas_hoy || 0) + ' Tickets';
                        if (document.getElementById('admin-metric-bodega')) document.getElementById('admin-metric-bodega').innerText = (data.metricas.ingresos_bodega_hoy || 0) + ' Lotes';
                    }

                    // 2. Historial de Ventas
                    if (tbodyVentas) {
                        const ventas = data.ventas || [];
                        ventasOffset = ventas.length;
                        hasMoreVentas = data.has_more;
                        tbodyVentas.innerHTML = '';

                        if (ventas.length === 0) {
                            tbodyVentas.innerHTML = '<tr><td colspan="5" class="text-center text-wait-custom py-4">No hay ventas registradas para el período seleccionado.</td></tr>';
                        } else {
                            let htmlVentas = '';
                            ventas.forEach(v => {
                                let fechaHora = 'N/A';
                                if (v.fecha_creacion) {
                                    const d = new Date(v.fecha_creacion.replace(' ', 'T'));
                                    fechaHora = isNaN(d.getTime()) ? v.fecha_creacion : d.toLocaleString('es-NI', { dateStyle: 'short', timeStyle: 'short' });
                                }
                                htmlVentas += `
                                    <tr>
                                        <td style="white-space: nowrap;">${fechaHora}</td>
                                        <td><code class="text-success font-bold">${v.codigo_ticket}</code></td>
                                        <td>${v.cliente ? v.cliente : 'Cliente Final'}</td>
                                        <td class="font-monospace text-success fw-bold">C$ ${parseFloat(v.total).toFixed(2)}</td>
                                        <td><span class="badge px-2 py-1 bg-success-box text-success">Pagado</span></td>
                                    </tr>
                                `;
                            });
                            tbodyVentas.innerHTML = htmlVentas;

                            if (hasMoreVentas) {
                                tbodyVentas.insertAdjacentHTML('beforeend', `
                                    <tr id="row-loading-more" class="d-none">
                                        <td colspan="5" class="text-center py-2 text-muted small">
                                            <div class="spinner-border spinner-border-sm text-success me-1"></div> Cargando más ventas...
                                        </td>
                                    </tr>
                                `);
                            }
                        }
                    }

                    // 3. Usuarios
                    if (tbodyUsuarios) {
                        const usuarios = data.usuarios || [];
                        if (usuarios.length === 0) {
                            tbodyUsuarios.innerHTML = '<tr><td colspan="4" class="text-center text-wait-custom py-2">No hay usuarios registrados.</td></tr>';
                        } else {
                            let htmlUsuarios = '';
                            usuarios.forEach(u => {
                                let fechaReg = 'N/A';
                                if (u.fecha_creacion) {
                                    const d = new Date(u.fecha_creacion.replace(' ', 'T'));
                                    fechaReg = isNaN(d.getTime()) ? u.fecha_creacion : d.toLocaleDateString('es-NI');
                                }
                                htmlUsuarios += `
                                    <tr>
                                        <td class="fw-bold text-light">${u.nombre_usuario}</td>
                                        <td><span class="badge px-2 py-1" style="background-color: #334155;">${u.rol}</span></td>
                                        <td>${fechaReg}</td>
                                        <td><span class="text-success d-flex align-items-center gap-1"><span class="material-symbols-outlined" style="font-size:14px;">check_circle</span> Activo</span></td>
                                    </tr>
                                `;
                            });
                            tbodyUsuarios.innerHTML = htmlUsuarios;
                        }
                    }

                    // 4. Alertas
                    if (listAlertas) {
                        const alertas = data.alertas || [];
                        if (alertas.length === 0) {
                            listAlertas.innerHTML = `
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center py-4">
                                    <span class="material-symbols-outlined text-success fs-1 mb-2">check_circle</span>
                                    <span class="text-wait-custom d-block fw-bold" style="color: #cbd5e1;">Sin alertas del sistema</span>
                                    <span class="text-sub-wait mt-1" style="font-size: 11px; color: #94a3b8;">Todo el stock y los vencimientos están estables</span>
                                </div>
                            `;
                        } else {
                            let htmlAlertas = '<div class="w-100 text-start px-1 mt-1" style="max-height: 280px; overflow-y: auto;">';
                            alertas.forEach(a => {
                                let borderColor = a.tipo === 'warning' ? '#f59e0b' : '#ef4444';
                                let badgeBg = a.tipo === 'warning' ? 'bg-warning text-dark' : 'bg-danger text-white';
                                
                                htmlAlertas += `
                                    <div class="d-flex justify-content-between align-items-center p-2 mb-2 shadow-sm" style="background-color: #ffffff; border: 1px solid ${borderColor}; border-left: 5px solid ${borderColor}; border-radius: 8px;">
                                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 65%;">
                                            <span class="d-block fw-bold" style="font-size: 13px; color: #0f172a;">${a.nombre_commercial}</span>
                                            <span class="d-block text-truncate" style="font-size: 11px; color: #64748b; font-weight: 500;">${a.detalle}</span>
                                        </div>
                                        <span class="badge ${badgeBg} rounded-pill px-2 py-1 ms-1 fw-bold" style="font-size: 10px; box-shadow: 0 1px 2px rgba(0,0,0,0.08);">${a.etiqueta}</span>
                                    </div>
                                `;
                            });
                            htmlAlertas += '</div>';
                            listAlertas.innerHTML = htmlAlertas;
                        }
                    }
                } else {
                    if (tbodyVentas) tbodyVentas.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">${response.message || 'Error al cargar ventas.'}</td></tr>`;
                    if (tbodyUsuarios) tbodyUsuarios.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-2">${response.message || 'Error al cargar usuarios.'}</td></tr>`;
                    if (listAlertas) listAlertas.innerHTML = `<div class="p-3 text-danger text-center">${response.message || 'Error al cargar alertas.'}</div>`;
                }
            })
            .catch(err => {
                console.error("Error al cargar datos del dashboard:", err);
                if (tbodyVentas) tbodyVentas.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error de conexión al cargar ventas.</td></tr>';
                if (tbodyUsuarios) tbodyUsuarios.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-2">Error de conexión al cargar usuarios.</td></tr>';
                if (listAlertas) listAlertas.innerHTML = '<div class="p-3 text-danger text-center">Error de conexión al cargar alertas.</div>';
            });
    }

    // Event listeners para el botón de filtrado y el scroll del contenedor de ventas
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('#btn-filtrar-ventas');
        if (btn) {
            e.preventDefault();
            const select = document.getElementById('filtro-ventas-periodo');
            if (select) {
                ventasFiltro = select.value;
                cargarDatosDashboard();
            }
        }
    });

    document.addEventListener('scroll', function(e) {
        if (e.target && e.target.id === 'container-historial-ventas') {
            const container = e.target;
            if (loadingVentas || !hasMoreVentas) return;
            if (container.scrollTop + container.clientHeight >= container.scrollHeight - 40) {
                cargarVentasFiltradas(false);
            }
        }
    }, true);

    const viewCache = {};

    // Envolver el contenido inicial del dashboard de admin como una instancia cacheada
    const defaultDashboardContainer = document.createElement('div');
    defaultDashboardContainer.className = 'cached-view-instance w-100 h-100';
    while (contentArea.firstChild) {
        defaultDashboardContainer.appendChild(contentArea.firstChild);
    }
    contentArea.appendChild(defaultDashboardContainer);
    viewCache['dashboard'] = defaultDashboardContainer;

    function manejarCarga(url, btnClicado, nombreModulo) {
        document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
        if (btnClicado && btnClicado.classList) btnClicado.classList.add('active');

        // Ocultar todas las instancias de vistas cargadas anteriormente en el DOM
        Object.keys(viewCache).forEach(key => {
            if (viewCache[key]) {
                viewCache[key].style.display = 'none';
            }
        });

        // Si la vista ya existe en el caché del DOM, mostrar la instancia activa conservando todo su estado
        if (viewCache[url]) {
            viewCache[url].style.display = 'block';
            return;
        }

        // Si no existe, crear el contenedor para la nueva vista e instalar la instancia
        const container = document.createElement('div');
        container.className = 'cached-view-instance w-100 h-100';
        contentArea.appendChild(container);
        viewCache[url] = container;

        container.innerHTML = `
            <div class="d-flex flex-column justify-content-center align-items-center flex-grow-1 py-5">
                <div class="spinner-border text-success mb-3" role="status"></div>
                <span class="text-wait-custom">Cargando ${nombreModulo}...</span>
            </div>
        `;

        fetch(url)
            .then(response => {
                if(!response.ok) throw new Error('Error al cargar archivo');
                return response.text();
            })
            .then(data => { 
                container.innerHTML = data; 
                // Ejecutar scripts cargados por ajax
                const scripts = container.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            })
            .catch(error => {
                container.innerHTML = `<div class="alert alert-danger m-3">Error: ${error.message}</div>`;
            });
    }

    btnInicio.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
        btnInicio.classList.add('active');
        
        Object.keys(viewCache).forEach(key => {
            if (viewCache[key]) {
                viewCache[key].style.display = 'none';
            }
        });
        if (viewCache['dashboard']) {
            viewCache['dashboard'].style.display = 'block';
            cargarDatosDashboard();
        }
    });

    btnUsuarios.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Gestion_usuarios.php', btnUsuarios, 'Gestión de Usuarios');
    });

    btnProductos.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('../productos/catalogo.php', btnProductos, 'Catálogo de Productos');
    });

    btnBodega.addEventListener('click', (e) => {
        e.preventDefault();
        window.location.href = '../bodega/bodega_lotes.php';
    });

    btnReportes.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Reportes.php', btnReportes, 'Reportes Contables');
    });

    // Enrutamiento basado en parámetros de consulta de la URL (Query Parameters)
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    if (page === 'usuarios') {
        manejarCarga('botones_menu/Gestion_usuarios.php', btnUsuarios, 'Gestión de Usuarios');
    } else if (page === 'productos') {
        manejarCarga('../productos/catalogo.php', btnProductos, 'Catálogo de Productos');
    } else if (page === 'reportes') {
        manejarCarga('botones_menu/Reportes.php', btnReportes, 'Reportes Contables');
    } else {
        // Cargar métricas del dashboard por defecto si no hay página seleccionada
        cargarDatosDashboard();
    }

    const profileContainer = document.querySelector('.profile-container');
    if (profileContainer) {
        profileContainer.style.cursor = 'pointer';
        profileContainer.addEventListener('click', () => {
            manejarCarga('../perfil/ver_perfil.php', { classList: { remove: () => {}, add: () => {} } }, 'Mi Perfil');
        });
    }
</script>
</body>
</html>
