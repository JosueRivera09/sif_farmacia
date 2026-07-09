<?php
session_start();

// Verificar si hay sesión iniciada y si el rol es Cajero
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Cajero') {
    header("Location: ../login.php");
    exit;
}

// Obtener nombre del usuario de la sesión
$nombre_usuario = isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Cajero';
$rol_usuario = isset($_SESSION['rol']) ? htmlspecialchars($_SESSION['rol']) : 'Cajero';
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema SIF - Panel de Caja</title>
    
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

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

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

        .nav-link-custom:hover { background-color: #0f172a; color: #f8fafc; }
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

        .text-error-custom { color: #ef4444 !important; }
        .text-error-custom:hover { background-color: rgba(239, 68, 68, 0.1) !important; }

        /* Área de Contenido */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-color: #0f172a;
        }

        .top-header {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            background-color: #0f172a;
            border-bottom: 1px solid #334155;
        }

        .header-title { font-size: 20px; font-weight: 700; color: #f8fafc; margin: 0; }
        .profile-container { display: flex; align-items: center; gap: 12px; padding-left: 24px; border-left: 1px solid #334155; }
        .avatar-box { width: 40px; height: 40px; border-radius: 50%; border: 2px solid rgba(16, 185, 129, 0.2); overflow: hidden; background-color: #1e293b; }

        .content-body { padding: 24px; overflow-y: auto; flex-grow: 1; }

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
        .metric-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; }
        .metric-card.card-primary::before { background-color: #10b981; }
        .metric-card.card-secondary::before { background-color: #3b82f6; }
        .metric-card.card-danger::before { background-color: #f59e0b; }

        .metric-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin: 0; }
        .metric-value { font-size: 24px; font-weight: 700; color: #f8fafc; margin-top: 4px; margin-bottom: 0; }
        .metric-icon-box { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        
        .bg-primary-box { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
        .bg-secondary-box { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .bg-danger-box { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }

        .custom-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .text-wait-custom { color: #94a3b8; font-size: 14px; }
        .spinner-border.text-success { color: #10b981 !important; }
    </style>
</head>
<body class="h-100">

<div class="app-container">
    
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div>
                <img src="../../assets/img/logo.png" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;">
            </div>
            <div>
                <h1 class="sidebar-brand">SISTEMA SIF</h1>
                <p class="sidebar-subtitle mb-0">Panel Caja</p>
            </div>
        </div>
        
        <nav class="sidebar-menu custom-scrollbar">
            <a class="nav-link-custom active" id="btn-inicio">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a class="nav-link-custom" id="btn-modulo-caja">
                <span class="material-symbols-outlined">payments</span>
                <span>Módulo Caja</span>
            </a>
            <a class="nav-link-custom" id="btn-historial">
                <span class="material-symbols-outlined">receipt_long</span>
                <span>Historial Cobros</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a class="nav-link-custom text-error-custom" href="../../controllers/logout.php">
                <span class="material-symbols-outlined">logout</span>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- ÁREA DE CONTENIDO -->
    <div class="main-content">
        <header class="top-header">
            <h2 class="header-title" id="header-dinamico-titulo">Panel de Caja</h2>
            <div class="d-flex align-items-center gap-4">
                <div class="profile-container d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 text-light" style="font-size: 14px; font-weight: 700;"><?php echo $nombre_usuario; ?></p>
                        <p class="mb-0 text-muted uppercase" style="font-size: 10px;"><?php echo $rol_usuario; ?></p>
                    </div>
                    <div class="avatar-box">
                        <img class="w-100 h-100 object-fit-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCuF-iQpxxKVH4dx-vZoHx-5U8lhGCDmzgsTwm3oZr6Na276hBSHqhkmbpEqZdgV1meyGb_jKZQlTsIPbhhSStuy4CY5cBn0ZURf2TnyzatF-TXxpYbHwBbdzJcuE6R88T4pu1bFmdA3zi1r9QcbaFPNPK0_kpPBuRf8inZ-puuthBNSfQxLQz3UBbryi9bwzMNtmR9ZjD-4oVqVDN5ThrbQ9duX9qx6FlXxQYiE1TKg6nhb8n9m3-BaIZAjJr5qu1JFI6LRMLAcAVw" alt="Perfil"/>
                    </div>
                </div>
            </div>
        </header>

        <!-- CUERPO DE CONTENIDO DINÁMICO -->
        <main class="content-body custom-scrollbar" id="main-content-area">
            
            <!-- VISTA INICIAL (DASHBOARD) -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="metric-card card-primary">
                        <div>
                            <p class="metric-title">Recaudación Total (Hoy)</p>
                            <h3 class="metric-value">C$ 0.00</h3>
                        </div>
                        <div class="metric-icon-box bg-primary-box">
                            <span class="material-symbols-outlined">real_estate_agent</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="metric-card card-secondary">
                        <div>
                            <p class="metric-title">Tickets Pagados</p>
                            <h3 class="metric-value">0 Tickets</h3>
                        </div>
                        <div class="metric-icon-box bg-secondary-box">
                            <span class="material-symbols-outlined">confirmation_number</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="metric-card card-danger">
                        <div>
                            <p class="metric-title">Preventas por Cobrar</p>
                            <h3 class="metric-value">0 Pendientes</h3>
                        </div>
                        <div class="metric-icon-box bg-danger-box">
                            <span class="material-symbols-outlined">schedule</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="custom-card" style="border-left: 5px solid #10b981;">
                        <h5 class="mb-2">¡Bienvenida, <?php echo $nombre_usuario; ?>!</h5>
                        <p class="text-muted small mb-0">Control de apertura activo. Utiliza el menú lateral para navegar en la aplicación o procesar cobros directos.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="custom-card">
                        <div class="border-bottom border-secondary pb-2 mb-3"><h6 class="card-title-custom mb-0">Estado del Turno</h6></div>
                        <p class="mb-0 text-muted small">Caja operativa vinculada a tu ID de usuario correctamente.</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const contentArea = document.getElementById('main-content-area');
    const headerTitulo = document.getElementById('header-dinamico-titulo');
    const btnInicio = document.getElementById('btn-inicio');
    const btnModuloCaja = document.getElementById('btn-modulo-caja');
    const btnHistorial = document.getElementById('btn-historial');

    const vistaInicioHTML = contentArea.innerHTML;

    function manejarCarga(url, btnClicado, nombreModulo) {
        document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
        btnClicado.classList.add('active');
        headerTitulo.innerText = nombreModulo;
        
        contentArea.innerHTML = `
            <div class="d-flex flex-column justify-content-center align-items-center flex-grow-1 py-5 h-75">
                <div class="spinner-border text-success mb-3" role="status"></div>
                <span class="text-wait-custom">Cargando ${nombreModulo}...</span>
            </div>
        `;

        fetch(url)
            .then(response => {
                if(!response.ok) throw new Error('Error al cargar módulo');
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
        headerTitulo.innerText = "Panel de Caja";
        contentArea.innerHTML = vistaInicioHTML;
    });

    btnModuloCaja.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Procesar_caja.php', btnModuloCaja, 'Módulo de Facturación y Caja');
    });

    btnHistorial.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/Historial_cobros.php', btnHistorial, 'Reportes Diarios de Facturación');
    });
</script>
</body>
</html>