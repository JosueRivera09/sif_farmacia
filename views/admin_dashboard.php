<?php
session_start();

// Verificar si hay sesión iniciada y si el rol es Administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: login.php");
    exit;
}

// Obtener nombre del usuario de la sesión
$nombre_usuario = isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Admin';
$rol_usuario = isset($_SESSION['rol']) ? htmlspecialchars($_SESSION['rol']) : 'Administrador';
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>SISTEMA SIF - Panel de Administración</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <!-- Google Fonts - Inter & JetBrains Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet"/>
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

    <style>
        body {
            background-color: #0f172a; /* Fondo principal pizarra oscuro */
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
            background-color: #1e293b; /* Sidebar bg pizarra medio */
            border-right: 1px solid #334155; /* border-subtle */
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
            background-color: #10b981; /* accent-green */
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

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: #94a3b8; /* text-muted */
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover {
            background-color: #0f172a;
            color: #f8fafc;
        }

        .nav-link-custom.active {
            background-color: rgba(15, 23, 42, 0.6);
            color: #10b981; /* accent-green */
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
            color: #ef4444 !important; /* error */
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
            font-size: 14px;
            font-weight: 600;
            color: #cbd5e1;
            letter-spacing: 0.05em;
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
        .metric-card.card-tertiary::before { background-color: #f59e0b; }
        .metric-card.card-secondary::before { background-color: #3b82f6; }

        .metric-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin: 0;
        }

        .metric-value {
            font-size: 28px;
            font-weight: 700;
            color: #f8fafc;
            margin-top: 4px;
            margin-bottom: 0;
        }

        .metric-value.text-tertiary-custom {
            color: #f59e0b !important;
        }

        .metric-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-box { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
        .bg-tertiary-box { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .bg-secondary-box { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }

        /* Secciones de Tablas y Alertas */
        .content-section {
            background-color: #1e293b;
            border-radius: 12px;
            border: 1px solid #334155;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-header {
            padding: 16px 24px;
            border-bottom: 1px solid #334155;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #c2c6d6;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }

        .table-custom-header {
            background-color: rgba(15, 23, 42, 0.4);
            border-bottom: 1px solid #334155;
        }

        .table-custom-header th {
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            padding: 16px 24px;
        }

        .empty-placeholder {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px;
            color: #64748b;
        }

        /* Footer */
        .footer-meta {
            margin-top: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            opacity: 0.5;
        }

        /* Scrollbar Personalizado */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }
    </style>
</head>
<body class="h-100">

    <div class="app-container">
        
        <!-- BEGIN: Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-box">S</div>
                <div>
                    <h1 class="sidebar-brand">SISTEMA SIF</h1>
                    <p class="sidebar-subtitle mb-0">Warehouse Management</p>
                </div>
            </div>
            
            <nav class="sidebar-menu custom-scrollbar">
                <a class="nav-link-custom active" href="admin_dashboard.php">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Inicio</span>
                </a>
                <a class="nav-link-custom" href="#">
                    <span class="material-symbols-outlined">group</span>
                    <span>Gestión Usuarios</span>
                </a>
                <a class="nav-link-custom" href="#">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span>Catálogo Productos</span>
                </a>
                <a class="nav-link-custom" href="bodega/bodega_lotes.php">
                    <span class="material-symbols-outlined">warehouse</span>
                    <span>Bodega y Lotes</span>
                </a>
                <a class="nav-link-custom" href="#">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span>Módulo Compras</span>
                </a>
                <a class="nav-link-custom" href="#">
                    <span class="material-symbols-outlined">analytics</span>
                    <span>Reportes</span>
                </a>
            </nav>
            
            <!-- Botón de ajustes y cerrar sesión integrado de forma consistente -->
            <div class="sidebar-footer">
                <a class="nav-link-custom" href="#">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Ajustes</span>
                </a>
                <a class="nav-link-custom text-error-custom" href="../controllers/logout.php">
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
                <h2 class="header-title">SISTEMA SIF - PANEL DE ADMINISTRACIÓN</h2>
                
                <div class="d-flex align-items-center gap-4">
                    <div class="header-icons d-flex gap-3">
                        <span class="material-symbols-outlined header-icon-btn">notifications</span>
                        <span class="material-symbols-outlined header-icon-btn">settings</span>
                    </div>
                    
                    <div class="profile-container d-flex align-items-center gap-3 border-start border-secondary-subtle ps-4">
                        <div class="text-end d-none d-sm-block">
                            <p class="mb-0 font-bold text-light" style="font-size: 14px; font-weight: 700;"><?php echo $nombre_usuario; ?> (Admin)</p>
                            <p class="mb-0 text-muted uppercase tracking-wider" style="font-size: 10px;"><?php echo $rol_usuario; ?></p>
                        </div>
                        <div class="avatar-box">
                            <img class="w-100 h-100 object-fit-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCuF-iQpxxKVH4dx-vZoHx-5U8lhGCDmzgsTwm3oZr6Na276hBSHqhkmbpEqZdgV1meyGb_jKZQlTsIPbhhSStuy4CY5cBn0ZURf2TnyzatF-TXxpYbHwBbdzJcuE6R88T4pu1bFmdA3zi1r9QcbaFPNPK0_kpPBuRf8inZ-puuthBNSfQxLQz3UBbryi9bwzMNtmR9ZjD-4oVqVDN5ThrbQ9duX9qx6FlXxQYiE1TKg6nhb8n9m3-BaIZAjJr5qu1JFI6LRMLAcAVw" alt="Perfil"/>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END: TopHeader -->

            <main class="content-body custom-scrollbar">
                
                <!-- Summary Cards -->
                <div class="row g-4 mb-4">
                    <!-- Recaudación Card -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="metric-card card-primary">
                            <div>
                                <p class="metric-title">Recaudación</p>
                                <p class="metric-value">C$ 0.00</p>
                            </div>
                            <div class="metric-icon-box bg-primary-box">
                                <i class="fa-solid fa-money-bill-wave fa-lg"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Facturas Card -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="metric-card card-secondary">
                            <div>
                                <p class="metric-title">Facturas Pagadas de Hoy</p>
                                <p class="metric-value">0 Tickets</p>
                            </div>
                            <div class="metric-icon-box bg-secondary-box">
                                <i class="fa-solid fa-receipt fa-lg"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Alertas Card -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="metric-card card-tertiary">
                            <div>
                                <p class="metric-title">Alertas de Stock Crítico</p>
                                <p class="metric-value text-tertiary-custom">0</p>
                            </div>
                            <div class="metric-icon-box bg-tertiary-box">
                                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Ingresos Card -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="metric-card card-primary">
                            <div>
                                <p class="metric-title">Ingresos de Bodega de Hoy</p>
                                <p class="metric-value">0 Lotes</p>
                            </div>
                            <div class="metric-icon-box bg-primary-box">
                                <i class="fa-solid fa-box fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: SummaryCards -->

                <div class="row g-4">
                    <!-- Left Main Area -->
                    <div class="col-12 col-lg-8">
                        
                        <!-- BEGIN: SalesHistory -->
                        <section class="content-section mb-4">
                            <div class="section-header">
                                <h3 class="section-title">Historial de Ventas del Sistema</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-dark table-borderless align-middle mb-0">
                                    <thead>
                                        <tr class="table-custom-header">
                                            <th scope="col">Hora</th>
                                            <th scope="col">Ticket</th>
                                            <th scope="col">Cliente</th>
                                            <th scope="col">Monto</th>
                                            <th scope="col">Detalles / Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Placeholder para ventas vacías -->
                                        <tr>
                                            <td colspan="5" class="p-0">
                                                <div class="empty-placeholder">
                                                    <p class="mb-0 text-muted">No hay ventas registradas el día de hoy.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <!-- END: SalesHistory -->

                        <!-- BEGIN: ActiveUsers -->
                        <section class="content-section">
                            <div class="section-header">
                                <h3 class="section-title">Usuarios Activos en Sistema</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-dark table-borderless align-middle mb-0">
                                    <thead>
                                        <tr class="table-custom-header">
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Rol</th>
                                            <th scope="col">Última Actividad</th>
                                            <th scope="col">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Placeholder para usuarios vacíos -->
                                        <tr>
                                            <td colspan="4" class="p-0">
                                                <div class="empty-placeholder" style="min-height: 120px;">
                                                    <p class="mb-0 text-muted italic">Esperando conexión de sesiones...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <!-- END: ActiveUsers -->

                    </div>

                    <!-- Right Alert Area -->
                    <div class="col-12 col-lg-4">
                        <!-- BEGIN: LowStockAlerts -->
                        <section class="content-section h-100 d-flex flex-column">
                            <div class="section-header">
                                <h3 class="section-title text-danger">Alertas de Stock Bajo (Urgente)</h3>
                            </div>
                            <div class="empty-placeholder flex-grow-1 text-center py-5">
                                <div class="mb-3">
                                    <i class="fa-solid fa-boxes-stacked" style="font-size: 3rem; color: rgba(100, 116, 139, 0.3);"></i>
                                </div>
                                <h4 class="text-light fs-6">Sin alertas de inventario</h4>
                                <p class="text-muted small mt-2">Todo el stock se encuentra estable</p>
                            </div>
                        </section>
                        <!-- END: LowStockAlerts -->
                    </div>
                </div>

                <!-- Footer Meta -->
                <div class="footer-meta">
                    <p class="mb-0">© 2023 SISTEMA SIF - GESTIÓN DE ALMACENES E INVENTARIOS</p>
                    <div class="d-flex gap-4">
                        <span>ESTADO DEL SERVIDOR: ÓPTIMO</span>
                        <span>VERSIÓN: 2.4.0-CORE</span>
                    </div>
                </div>

            </main>
        </div>

    </div>

    <!-- Bootstrap 5 Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
