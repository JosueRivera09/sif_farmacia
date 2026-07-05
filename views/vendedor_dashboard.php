<?php
session_start();

// Verificar si hay sesión iniciada y si el rol es Vendedor
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Vendedor') {
    header("Location: login.php");
    exit;
}

// Obtener nombre del usuario de la sesión
$nombre_usuario = isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Vendedor';
$rol_usuario = isset($_SESSION['rol']) ? htmlspecialchars($_SESSION['rol']) : 'Vendedor';
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>SISTEMA SIF - Panel de Ventas</title>
    
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
            background-color: #0f172a; /* Fondo principal de la indicación anterior */
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
            background-color: #1e293b; /* Sidebar bg de la indicación anterior */
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
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .sidebar-subtitle {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            opacity: 0.7;
            margin-top: 4px;
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
            font-size: 24px;
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

        /* Tarjeta */
        .content-section {
            background-color: #1e293b;
            border-radius: 8px;
            border: 1px solid #334155;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 32px;
            text-align: center;
        }

        /* Footer */
        .footer-meta {
            margin-top: auto;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            opacity: 0.5;
            border-top: 1px solid #334155;
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
                    <p class="sidebar-subtitle mb-0">Sales Panel</p>
                </div>
            </div>
            
            <nav class="sidebar-menu custom-scrollbar">
                <a class="nav-link-custom active" href="vendedor_dashboard.php">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Ventas</span>
                </a>
                <a class="nav-link-custom" href="bodega_lotes.php">
                    <span class="material-symbols-outlined">warehouse</span>
                    <span>Bodega y Lotes</span>
                </a>
                <a class="nav-link-custom" href="#">
                    <span class="material-symbols-outlined">analytics</span>
                    <span>Mis Reportes</span>
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
                <h2 class="header-title">Panel de Ventas</h2>
                
                <div class="d-flex align-items-center gap-4">
                    <div class="header-icons d-flex gap-3">
                        <span class="material-symbols-outlined header-icon-btn">notifications</span>
                        <span class="material-symbols-outlined header-icon-btn">settings</span>
                    </div>
                    
                    <div class="profile-container d-flex align-items-center gap-3 border-start border-secondary-subtle ps-4">
                        <div class="text-end d-none d-sm-block">
                            <p class="mb-0 font-bold text-light" style="font-size: 14px; font-weight: 700;"><?php echo $nombre_usuario; ?> (Vendedor)</p>
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
            <main class="content-body d-flex flex-column justify-content-between">
                
                <div class="content-section my-auto">
                    <h1 class="mb-4">Panel de Operaciones de Ventas</h1>
                    <p class="lead">Has accedido correctamente con el rol de <strong><?php echo htmlspecialchars($_SESSION['rol']); ?></strong>.</p>
                    <hr class="my-4" style="border-color: #334155;">
                    <p class="text-muted">Esta pantalla está disponible únicamente para el personal del área de ventas. Desde aquí se gestionará el registro de facturas, cotizaciones y cierres de caja.</p>
                </div>

                <!-- Footer Meta -->
                <div class="footer-meta">
                    <p class="mb-0">© 2023 SISTEMA SIF - PANEL DE VENTAS</p>
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
