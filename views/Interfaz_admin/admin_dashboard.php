<?php
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
            <a class="nav-link-custom text-error-custom" href="../../controllers/auth/logout.php">
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
                
                <div class="profile-container d-flex align-items-center gap-3 border-start border-secondary-subtle ps-4">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 font-bold text-light" style="font-size: 14px; font-weight: 700;"><?php echo $nombre_usuario; ?></p>
                        <p class="mb-0 text-muted uppercase tracking-wider" style="font-size: 10px;"><?php echo $rol_usuario; ?></p>
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
            .then(data => { 
                contentArea.innerHTML = data; 
                // Ejecutar scripts cargados por ajax
                const scripts = contentArea.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            })
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
        manejarCarga('../productos/catalogo.php', btnProductos, 'Catálogo de Productos');
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

    // Enrutamiento basado en parámetros de consulta de la URL (Query Parameters)
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    if (page === 'usuarios') {
        manejarCarga('botones_menu/Gestion_usuarios.php', btnUsuarios, 'Gestión de Usuarios');
    } else if (page === 'productos') {
        manejarCarga('../productos/catalogo.php', btnProductos, 'Catálogo de Productos');
    } else if (page === 'auditoria') {
        manejarCarga('botones_menu/Auditoria_ingresos.php', btnCompras, 'Auditoría de Ingresos');
    } else if (page === 'reportes') {
        manejarCarga('botones_menu/Reportes_diarios.php', btnReportes, 'Reportes Diarios');
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
