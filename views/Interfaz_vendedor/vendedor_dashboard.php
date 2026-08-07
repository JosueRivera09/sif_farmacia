<?php
session_start();

// Esta es la pantalla del dashboard del Vendedor, que permite registrar ventas, consultar stock de productos y ver el historial de ventas.

// Verificar si hay sesión iniciada y si el rol tiene acceso
$permisos_extra = isset($_SESSION['permisos_extra']) ? $_SESSION['permisos_extra'] : [];
$tiene_acceso = ($_SESSION['rol'] === 'Vendedor' || $_SESSION['rol'] === 'Administrador' || in_array('ventas', $permisos_extra));

if (!isset($_SESSION['id_usuario']) || !$tiene_acceso) {
    header("Location: ../login.php");
    exit;
}

// Obtener nombre del usuario de la sesión
$nombre_usuario = isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Vendedor';
$rol_usuario = isset($_SESSION['rol']) ? htmlspecialchars($_SESSION['rol']) : 'Vendedor';
?>
<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>SISTEMA SIF - Panel de Ventas</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Google Fonts - Inter & JetBrains Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet" />
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="../../assets/css/vendedor/vendedor_dashboard.css">
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
                <h2 class="header-title" id="module-title">Panel de Ventas</h2>

                <div class="d-flex align-items-center gap-4">

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
            <main class="content-body" id="main-content-area">
                <!-- Se cargará dinámicamente con AJAX -->
            </main>
        </div>

    </div>

    <!-- Bootstrap 5 Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const contentArea = document.getElementById('main-content-area');
        const moduleTitle = document.getElementById('module-title');

        const btnInicio = document.getElementById('btn-inicio-ventas') || document.getElementById('btn-inicio');
        const btnNuevaVenta = document.getElementById('btn-nueva-venta');
        const btnInventario = document.getElementById('btn-inventario');

        const viewCache = {};

        function manejarCarga(url, btnClicado, nombreModulo) {
            document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
            if (btnClicado && btnClicado.classList) btnClicado.classList.add('active');
            if (moduleTitle) moduleTitle.innerText = nombreModulo;

            // Ocultar todas las instancias de vistas cargadas anteriormente en el DOM
            Object.keys(viewCache).forEach(key => {
                if (viewCache[key]) {
                    viewCache[key].style.display = 'none';
                }
            });

            // Si la vista ya existe en el caché del DOM, mostrar la instancia activa conservando todo su estado
            if (viewCache[url]) {
                viewCache[url].style.display = 'block';
                if (url.includes('resumen.php')) {
                    if (typeof window.cargarMetricasResumen === 'function') window.cargarMetricasResumen();
                    if (typeof window.cargarMisTickets === 'function') window.cargarMisTickets();
                }
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
                    if (!response.ok) throw new Error('Error al cargar archivo');
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

        if (btnInicio) {
            btnInicio.addEventListener('click', (e) => {
                e.preventDefault();
                manejarCarga('botones_menu/resumen.php', btnInicio, 'Resumen de Ventas');
            });
        }

        if (btnNuevaVenta) {
            btnNuevaVenta.addEventListener('click', (e) => {
                e.preventDefault();
                manejarCarga('botones_menu/nueva_venta.php', btnNuevaVenta, 'Registrar Nueva Venta');
            });
        }

        if (btnInventario) {
            btnInventario.addEventListener('click', (e) => {
                e.preventDefault();
                manejarCarga('../productos/catalogo.php', btnInventario, 'Catálogo de Inventario');
            });
        }

        // Exponer globalmente para usarse desde el Resumen
        window.cargarModuloNuevaVenta = function() {
            manejarCarga('botones_menu/nueva_venta.php', btnNuevaVenta, 'Registrar Nueva Venta');
        };

        window.cargarModuloInventario = function() {
            manejarCarga('../productos/catalogo.php', btnInventario, 'Catálogo de Inventario');
        };

        // Carga inicial según parámetro en URL (?sub=nueva_venta, ?sub=inventario, etc.)
        const urlParams = new URLSearchParams(window.location.search);
        const subParam = urlParams.get('sub');

        if (subParam === 'nueva_venta' && btnNuevaVenta) {
            manejarCarga('botones_menu/nueva_venta.php', btnNuevaVenta, 'Registrar Nueva Venta');
        } else if (subParam === 'inventario' && btnInventario) {
            manejarCarga('../productos/catalogo.php', btnInventario, 'Catálogo de Inventario');
        } else {
            manejarCarga('botones_menu/resumen.php', btnInicio, 'Resumen de Ventas');
        }

        const profileContainer = document.querySelector('.profile-container');
        if (profileContainer) {
            profileContainer.style.cursor = 'pointer';
            profileContainer.addEventListener('click', () => {
                manejarCarga('../perfil/ver_perfil.php', {
                    classList: {
                        remove: () => {},
                        add: () => {}
                    }
                }, 'Mi Perfil');
            });
        }
    </script>
</body>

</html>