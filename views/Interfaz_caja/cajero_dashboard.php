<?php
session_start();

// Esta es la pantalla del dashboard del Cajero, que contiene las opciones para realizar cobros, transacciones y consultar el historial de caja.

// Verificar si hay sesión iniciada y si el rol tiene acceso
$permisos_extra = isset($_SESSION['permisos_extra']) ? $_SESSION['permisos_extra'] : [];
$tiene_acceso = ($_SESSION['rol'] === 'Cajero' || $_SESSION['rol'] === 'Administrador' || in_array('caja', $permisos_extra));

if (!isset($_SESSION['id_usuario']) || !$tiene_acceso) {
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
    
    <link rel="stylesheet" href="../../assets/css/cajero/cajero_dashboard.css">
</head>
<body class="h-100">

<div class="app-container">
    
    <!-- SIDEBAR -->
    <?php include_once __DIR__ . '/../sidebar.php'; ?>

    <!-- ÁREA DE CONTENIDO -->
    <div class="main-content">
        <header class="top-header">
            <h2 class="header-title" id="header-dinamico-titulo">Panel de Caja</h2>
            <div class="d-flex align-items-center gap-4">
                <div class="profile-container d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0" style="font-size: 14px; font-weight: 700; color: #000000 !important;"><?php echo $nombre_usuario; ?></p>
                        <p class="mb-0 uppercase" style="font-size: 10px; font-weight: 600; color: #000000 !important;"><?php echo $rol_usuario; ?></p>
                    </div>
                    <div class="avatar-box d-flex align-items-center justify-content-center text-white fw-bold" style="background-color: #10b981; font-size: 16px;">
                        <?php echo strtoupper(substr($nombre_usuario, 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- CUERPO DE CONTENIDO DINÁMICO -->
        <main class="content-body custom-scrollbar" id="main-content-area">
            <!-- Carga dinámica -->
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const contentArea = document.getElementById('main-content-area');
    const headerTitulo = document.getElementById('header-dinamico-titulo');
    const btnModuloCaja = document.getElementById('btn-modulo-caja');
    const btnHistorial = document.getElementById('btn-historial');
    const btnArqueo = document.getElementById('btn-arqueo');

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

    btnModuloCaja.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/procesar_caja.php', btnModuloCaja, 'Pedidos por Cobrar');
    });

    btnHistorial.addEventListener('click', (e) => {
        e.preventDefault();
        manejarCarga('botones_menu/historial_cobros.php', btnHistorial, 'Historial de Cobros');
    });

    if (btnArqueo) {
        btnArqueo.addEventListener('click', (e) => {
            e.preventDefault();
            manejarCarga('botones_menu/arqueo_caja.php', btnArqueo, 'Arqueo de Caja y Turno');
        });
    }

    // Carga inicial por defecto: Pedidos por cobrar
    manejarCarga('botones_menu/procesar_caja.php', btnModuloCaja, 'Pedidos por Cobrar');

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