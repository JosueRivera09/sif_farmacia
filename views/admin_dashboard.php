<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema SIF - Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* PALETA DE COLORES ADAPTADA DEL LOGIN DEL SIF */
        :root {
            --bg-main: #0f1c26;         /* Fondo ultra oscuro general */
            --bg-card: #172a3a;         /* Color azul/verde oscuro de los contenedores */
            --bg-hover: #20374c;        /* Hover para las filas y enlaces */
            --teal-primary: #0db38e;    /* Turquesa brillante */
            --text-white: #ffffff;
            --text-muted: #8fa3b5;      /* Color grisáceo secundario */
            --alert-red: #ef4444;       /* Rojo alertas críticas */
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-white);
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        /* BARRA LATERAL STICKY */
        .sidebar {
            background-color: var(--bg-card);
            min-height: 100vh;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand i {
            color: var(--teal-primary);
        }

        .menu-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 10px 15px 5px 15px;
            display: block;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: var(--text-muted);
            padding: 12px 20px;
            border-radius: 6px;
            margin: 2px 10px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none; 
            display: block;
        }

        .nav-link:hover {
            background-color: var(--bg-hover);
            color: var(--text-white);
        }

        .nav-link.active {
            background-color: rgba(13, 179, 142, 0.15) !important;
            color: var(--teal-primary) !important;
            font-weight: 600;
            border-left: 4px solid var(--teal-primary);
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        /* COMPONENTES DE INTERFAZ DEL PANEL */
        .custom-header {
            background-color: var(--bg-card);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 15px 30px;
        }

        .custom-card {
            background-color: var(--bg-card);
            border: 1px solid rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-verdecita { background-color: #0e2e28 !important; border: 1px solid rgba(13, 179, 142, 0.15) !important; }
        .card-azulita { background-color: #0f243a !important; border: 1px solid rgba(59, 130, 246, 0.15) !important; }
        .card-rojita { background-color: #2d161a !important; border: 1px solid rgba(239, 68, 68, 0.15) !important; }
        .card-moradita { background-color: #211635 !important; border: 1px solid rgba(139, 92, 246, 0.15) !important; }

        .card-title-custom { color: #ffffff !important; font-weight: 600; font-size: 0.75rem; letter-spacing: 0.3px; }
        .text-wait-custom { color: #cbd5e1 !important; font-weight: 500; }
        .text-sub-wait { color: #94a3b8 !important; font-size: 0.8rem; }

        .height-historial { min-height: 340px; }
        .height-alertas { min-height: 525px; }

        .card-icon-box { width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .icon-recaudado { background-color: rgba(13, 179, 142, 0.2); color: #0db38e; }
        .icon-tickets { background-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .icon-alertas { background-color: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .icon-bodega { background-color: rgba(139, 92, 246, 0.2); color: #8b5cf6; }

        .table { color: #e2e8f0; margin-bottom: 0; }
        .table th { color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--bg-main); background-color: transparent; }
        .table td { border-bottom: 1px solid rgba(255, 255, 255, 0.03); background-color: transparent; vertical-align: middle; }
        .table tr:hover td { background-color: var(--bg-hover); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-3 col-lg-2 px-0 sidebar d-flex flex-column justify-content-between">
            <div>
                <div class="sidebar-brand d-flex align-items-center gap-2">
                    <i class="fa-solid fa-square-h fs-4"></i>
                    <span class="fw-bold h5 mb-0">SISTEMA SIF</span>
                </div>
                
                <div class="nav flex-column nav-pills mt-3">
                    <span class="menu-label">Menú Principal</span>
                    <a class="nav-link active" id="btn-inicio"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</a>
                    <a class="nav-link" id="btn-usuarios"><i class="fa-solid fa-users me-2"></i> Gestión Usuarios</a>
                    <a class="nav-link" id="btn-productos"><i class="fa-solid fa-box-archive me-2"></i> Catálogo Productos</a>
                    <a class="nav-link" id="btn-bodega"><i class="fa-solid fa-warehouse me-2"></i> Bodega y Lotes</a>
                    <a class="nav-link" id="btn-compras"><i class="fa-solid fa-file-contract me-2"></i> Auditoría de Ingresos</a>
                    <a class="nav-link" id="btn-reportes"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Reportes Diarios</a>
                </div>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 px-0 d-flex flex-column">
            
            <div class="custom-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-uppercase tracking-wide" style="font-size: 0.95rem;">
                    SISTEMA SIF - PANEL DE ADMINISTRACIÓN
                </span>
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-2" style="cursor: pointer;">
                        <i class="fa-solid fa-user-shield text-success"></i>
                        <span class="fw-medium small">Joel (Admin)</span>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.7rem;"></i>
                    </div>
                </div>
            </div>

            <div class="p-4" id="main-content-area">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="custom-card card-verdecita d-flex justify-content-between align-items-center h-100">
                            <div>
                                <p class="text-uppercase small card-title-custom mb-1">Recaudación</p>
                                <h4 class="fw-bold mb-0 text-white">C$ 0.00</h4>
                            </div>
                            <div class="card-icon-box icon-recaudado"><i class="fa-solid fa-money-bill-wave"></i></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-xl-3">
                        <div class="custom-card card-azulita d-flex justify-content-between align-items-center h-100">
                            <div>
                                <p class="text-uppercase small card-title-custom mb-1">Facturas Pagadas de Hoy</p>
                                <h4 class="fw-bold mb-0 text-white">0 Tickets</h4>
                            </div>
                            <div class="card-icon-box icon-tickets"><i class="fa-solid fa-receipt"></i></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-xl-3">
                        <div class="custom-card card-rojita d-flex justify-content-between align-items-center h-100">
                            <div>
                                <p class="text-uppercase small card-title-custom mb-1">Alertas de Stock Crítico</p>
                                <h4 class="fw-bold mb-0 text-danger">0</h4>
                            </div>
                            <div class="card-icon-box icon-alertas"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-xl-3">
                        <div class="custom-card card-moradita d-flex justify-content-between align-items-center h-100">
                            <div>
                                <p class="text-uppercase small card-title-custom mb-1">Ingresos de Bodega de Hoy</p>
                                <h4 class="fw-bold mb-0 text-white">0 Lotes</h4>
                            </div>
                            <div class="card-icon-box icon-bodega"><i class="fa-solid fa-box"></i></div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8 d-flex flex-column gap-4">
                        <div class="custom-card height-historial">
                            <div class="border-bottom border-secondary pb-2 mb-3">
                                <h6 class="text-uppercase fw-semibold mb-0" style="font-size: 0.85rem;">
                                    Historial de Ventas del Sistema <span class="text-muted fw-normal text-none">(Tiempo Real)</span>
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-dark table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Hora</th>
                                            <th>Ticket</th>
                                            <th>Cliente</th>
                                            <th>Monto</th>
                                            <th>Detalles / Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center text-wait-custom py-4">No hay ventas registradas el día de hoy.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="custom-card">
                            <div class="border-bottom border-secondary pb-2 mb-3">
                                <h6 class="text-uppercase fw-semibold mb-0" style="font-size: 0.85rem;">Usuarios Activos en Sistema</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-dark table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Rol</th>
                                            <th>Última Actividad</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" class="text-center text-wait-custom py-2">Esperando conexión de sesiones...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="custom-card height-alertas">
                            <div class="border-bottom border-danger pb-2 mb-3">
                                <h6 class="text-danger text-uppercase fw-semibold mb-0" style="font-size: 0.85rem;">
                                    Alertas de Stock Bajo <span class="fw-normal small text-white-50">(Urgente)</span>
                                </h6>
                            </div>
                            
                            <div class="d-flex flex-column justify-content-center align-items-center h-75 text-center">
                                <i class="fa-solid fa-boxes-stacked fs-2 mb-2 d-block text-secondary"></i>
                                <span class="text-wait-custom">Sin alertas de inventario</span>
                                <span class="text-sub-wait mt-1">Todo el stock se encuentra estable</span>
                            </div>
                        </div>
                    </div>
                </div> 

            </div> 
        </div> 
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const contentArea = document.getElementById('main-content-area');
    const btnInicio = document.getElementById('btn-inicio');
    const btnUsuarios = document.getElementById('btn-usuarios');
    const btnProductos = document.getElementById('btn-productos');
    const btnBodega = document.getElementById('btn-bodega');
    const btnCompras = document.getElementById('btn-compras');

    const vistaInicioHTML = contentArea.innerHTML;

    function manejarCarga(url, btnClicado, nombreModulo) {
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        btnClicado.classList.add('active');
        
        contentArea.innerHTML = `
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
            .then(data => { contentArea.innerHTML = data; })
            .catch(error => {
                contentArea.innerHTML = `<div class="alert alert-danger m-3">Error: ${error.message}</div>`;
            });
    }

    btnInicio.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        btnInicio.classList.add('active');
        contentArea.innerHTML = vistaInicioHTML;
    });

    btnUsuarios.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Gestion_usuarios.php', btnUsuarios, 'Gestión de Usuarios');
    });

    btnProductos.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Gestion_productos.php', btnProductos, 'Catálogo de Productos');
    });

    btnBodega.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Gestion_BodLotes.php', btnBodega, 'Monitoreo de Existencias');
    });

    btnCompras.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Auditoria_ingresos.php', btnCompras, 'Auditoría de Ingresos');
    });

</script>
</body>
</html>
