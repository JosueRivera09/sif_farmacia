<?php
session_start();

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
    
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }

        /* Contenedor Principal Flex */
        .app-container {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* Barra Lateral (Sidebar) */
        .sidebar {
            width: 280px;
            background-color: #1e293b;
            border-right: 1px solid #334155;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            padding: 24px 16px;
        }

        .sidebar-header {
            margin-bottom: 32px;
            padding: 0 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-box {
            background-color: #10b981;
            padding: 6px 12px;
            border-radius: 8px;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .sidebar-brand {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .sidebar-subtitle {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            opacity: 0.7;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex-grow: 1;
            overflow-y: auto;
        }

        /* Scrollbar personalizado */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .nav-link-custom:hover {
            background-color: #0f172a;
            color: #f8fafc;
        }

        .nav-link-custom.active {
            background-color: rgba(15, 23, 42, 0.6);
            color: #10b981;
            border-left: 4px solid #10b981;
            border-radius: 0 8px 8px 0;
            padding-left: 12px;
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid #334155;
            padding-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .text-error-custom {
            color: #ef4444 !important;
        }
        .text-error-custom:hover {
            background-color: rgba(239, 68, 68, 0.1) !important;
        }

        /* Área de Contenido */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-color: #0f172a;
        }

        /* Cabecera (Header) */
        .top-header {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            background-color: #0f172a;
            border-bottom: 1px solid #334155;
        }

        .header-title {
            font-size: 20px;
            font-weight: 700;
            color: #f8fafc;
            margin: 0;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #94a3b8;
        }

        .header-icon-btn {
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s ease;
        }
        .header-icon-btn:hover {
            color: #10b981;
        }

        .profile-container {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 24px;
            border-left: 1px solid #334155;
        }

        .avatar-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid rgba(16, 185, 129, 0.2);
            overflow: hidden;
            background-color: #1e293b;
        }

        /* Contenido Central Scrollable */
        .content-body {
            padding: 24px;
            overflow-y: auto;
            flex-grow: 1;
        }

        /* Tarjetas de Métricas */
        .metric-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .metric-card.card-primary::before { background-color: #10b981; }
        .metric-card.card-secondary::before { background-color: #3b82f6; }
        .metric-card.card-danger::before { background-color: #ef4444; }
        .metric-card.card-purple::before { background-color: #8b5cf6; }

        .metric-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin: 0;
        }

        .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: #f8fafc;
            margin-top: 4px;
            margin-bottom: 0;
        }

        .metric-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .bg-primary-box { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
        .bg-secondary-box { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .bg-danger-box { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .bg-purple-box { background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

        /* Contenedores de Tablas y Tarjetas Personalizadas */
        .custom-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .height-historial { min-height: 340px; }
        .height-alertas { min-height: 525px; }

        .card-title-custom {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #f8fafc;
        }

        .text-wait-custom {
            color: #94a3b8;
            font-size: 14px;
        }

        .text-sub-wait {
            color: #64748b;
            font-size: 12px;
        }

        .table-custom {
            margin-bottom: 0;
            color: #f8fafc;
        }

        .table-custom th {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            border-bottom: 2px solid #334155;
            background-color: transparent;
            padding: 12px 16px;
        }

        .table-custom td {
            border-bottom: 1px solid #334155;
            background-color: transparent;
            vertical-align: middle;
            padding: 12px 16px;
            font-size: 14px;
        }

        .table-custom tbody tr:hover td {
            background-color: rgba(15, 23, 42, 0.3);
        }

        /* Spinner de carga */
        .spinner-border.text-success {
            color: #10b981 !important;
        }
    </style>
</head>
<body class="h-100">

<div class="app-container">
    
    <!-- BEGIN: Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div>
                <img src="../../assets/img/logo.png" alt="Logo SISTEMA SIF" style="width: 50px; height: 50px; object-fit: contain;">
            </div>
            <div>
                <h1 class="sidebar-brand">SISTEMA SIF</h1>
                <p class="sidebar-subtitle mb-0">Panel Admin</p>
            </div>
        </div>
        
        <nav class="sidebar-menu custom-scrollbar">
            <a class="nav-link-custom active" id="btn-inicio">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a class="nav-link-custom" id="btn-usuarios">
                <span class="material-symbols-outlined">group</span>
                <span>Gestión Usuarios</span>
            </a>
            <a class="nav-link-custom" id="btn-productos">
                <span class="material-symbols-outlined">inventory_2</span>
                <span>Catálogo Productos</span>
            </a>
            <a class="nav-link-custom" id="btn-bodega" href="../bodega/bodega_lotes.php">
                <span class="material-symbols-outlined">warehouse</span>
                <span>Bodega y Lotes</span>
            </a>
            <a class="nav-link-custom" id="btn-compras">
                <span class="material-symbols-outlined">receipt_long</span>
                <span>Auditoría de Ingresos</span>
            </a>
            <a class="nav-link-custom" id="btn-reportes">
                <span class="material-symbols-outlined">analytics</span>
                <span>Reportes Diarios</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a class="nav-link-custom" href="#">
                <span class="material-symbols-outlined">settings</span>
                <span>Ajustes</span>
            </a>
            <a class="nav-link-custom text-error-custom" href="../../controllers/logout.php">
                <span class="material-symbols-outlined">logout</span>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>
    <!-- END: Sidebar -->

    <!-- Main Content Area -->
    <div class="main-content">
        
        <!-- BEGIN: TopHeader -->
        <header class="top-header">
            <h2 class="header-title">Panel de Administración</h2>
            
            <div class="d-flex align-items-center gap-4">
                <div class="header-icons d-flex gap-3">
                    <span class="material-symbols-outlined header-icon-btn">notifications</span>
                </div>
                
                <div class="profile-container d-flex align-items-center gap-3 border-start border-secondary-subtle ps-4">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 font-bold text-light" style="font-size: 14px; font-weight: 700;"><?php echo $nombre_usuario; ?></p>
                        <p class="mb-0 text-muted uppercase tracking-wider" style="font-size: 10px;"><?php echo $rol_usuario; ?></p>
                    </div>
                    <div class="avatar-box">
                        <img class="w-100 h-100 object-fit-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCuF-iQpxxKVH4dx-vZoHx-5U8lhGCDmzgsTwm3oZr6Na276hBSHqhkmbpEqZdgV1meyGb_jKZQlTsIPbhhSStuy4CY5cBn0ZURf2TnyzatF-TXxpYbHwBbdzJcuE6R88T4pu1bFmdA3zi1r9QcbaFPNPK0_kpPBuRf8inZ-puuthBNSfQxLQz3UBbryi9bwzMNtmR9ZjD-4oVqVDN5ThrbQ9duX9qx6FlXxQYiE1TKg6nhb8n9m3-BaIZAjJr5qu1JFI6LRMLAcAVw" alt="Perfil"/>
                    </div>
                </div>
            </div>
        </header>
        <!-- END: TopHeader -->

        <!-- Content Body -->
        <main class="content-body custom-scrollbar" id="main-content-area">
            
            <!-- Metrics Section -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="metric-card card-primary">
                        <div>
                            <p class="metric-title">Recaudación</p>
                            <h3 class="metric-value">C$ 0.00</h3>
                        </div>
                        <div class="metric-icon-box bg-primary-box">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="metric-card card-secondary">
                        <div>
                            <p class="metric-title">Facturas de Hoy</p>
                            <h3 class="metric-value">0 Tickets</h3>
                        </div>
                        <div class="metric-icon-box bg-secondary-box">
                            <span class="material-symbols-outlined">receipt</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="metric-card card-danger">
                        <div>
                            <p class="metric-title">Stock Crítico</p>
                            <h3 class="metric-value">0</h3>
                        </div>
                        <div class="metric-icon-box bg-danger-box">
                            <span class="material-symbols-outlined">error</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="metric-card card-purple">
                        <div>
                            <p class="metric-title">Ingresos Bodega de Hoy</p>
                            <h3 class="metric-value">0 Lotes</h3>
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
                        <div class="border-bottom border-secondary pb-2 mb-3">
                            <h6 class="card-title-custom mb-0">
                                Historial de Ventas del Sistema <span class="text-muted fw-normal lowercase">(tiempo real)</span>
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom">
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
                            <h6 class="card-title-custom mb-0">Usuarios Activos en Sistema</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom">
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

                <div class="col-12 col-lg-4">
                    <div class="custom-card height-alertas">
                        <div class="border-bottom border-danger pb-2 mb-3">
                            <h6 class="card-title-custom text-danger mb-0">
                                Alertas de Stock Bajo <span class="fw-normal lowercase text-muted">(urgente)</span>
                            </h6>
                        </div>
                        
                        <div class="d-flex flex-column justify-content-center align-items-center h-75 text-center">
                            <span class="material-symbols-outlined text-secondary fs-1 mb-2">inventory_2</span>
                            <span class="text-wait-custom d-block">Sin alertas de inventario</span>
                            <span class="text-sub-wait mt-1">Todo el stock se encuentra estable</span>
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
    const btnCompras = document.getElementById('btn-compras');
    const btnReportes = document.getElementById('btn-reportes');

    const vistaInicioHTML = contentArea.innerHTML;

    function manejarCarga(url, btnClicado, nombreModulo) {
        document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
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
        document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
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
        window.location.href = '../bodega/bodega_lotes.php';
    });

    btnCompras.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Auditoria_ingresos.php', btnCompras, 'Auditoría de Ingresos');
    });

    btnReportes.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Reportes_diarios.php', btnReportes, 'Reportes Diarios');
    });

</script>
</body>
</html>
