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
                        <p class="mb-0 font-bold" style="font-size: 14px; font-weight: 700; color: #000000 !important;"><?php echo $nombre_usuario; ?></p>
                        <p class="mb-0 text-uppercase" style="font-size: 10px; font-weight: 600; color: #64748b !important; text-transform: uppercase;"><?php echo $rol_usuario; ?></p>
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

<!-- Modal Apertura de Caja -->
<div class="modal fade" id="modalAperturaCaja" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalAperturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #1e293b; color: #f8fafc; border: 1px solid #334155;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2" id="modalAperturaLabel">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                    Apertura de Turno de Caja (Fondo Inicial)
                </h5>
            </div>
            <form id="form-apertura-caja">
                <div class="modal-body py-4">
                    <p class="text-light mb-3" style="font-size: 14px;">
                        Por favor ingrese el monto inicial en efectivo con el que iniciará la caja para este turno:
                    </p>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text border-secondary text-success fw-bold" style="background-color: #f1f5f9;">C$</span>
                        <input type="number" step="0.01" min="0" class="form-control border-secondary fw-bold font-monospace" id="input-monto-apertura-inicial" placeholder="1000.00" value="1000.00" required style="color: #000000 !important; background-color: #ffffff !important; font-size: 20px;">
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 11px;">
                        * Este monto será la base en efectivo utilizada para el cálculo del total esperado al realizar el arqueo final.
                    </small>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <span class="material-symbols-outlined">play_circle</span> Iniciar Turno de Caja
                    </button>
                </div>
            </form>
        </div>
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
        if (btnClicado && btnClicado.classList) btnClicado.classList.add('active');
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

    // Carga inicial según parámetro en URL (?sub=historial, ?sub=arqueo, etc.)
    const urlParams = new URLSearchParams(window.location.search);
    const subParam = urlParams.get('sub');

    if (subParam === 'historial' && btnHistorial) {
        manejarCarga('botones_menu/historial_cobros.php', btnHistorial, 'Historial de Cobros');
    } else if (subParam === 'arqueo' && btnArqueo) {
        manejarCarga('botones_menu/arqueo_caja.php', btnArqueo, 'Arqueo de Caja y Turno');
    } else {
        manejarCarga('botones_menu/procesar_caja.php', btnModuloCaja, 'Pedidos por Cobrar');
    }

    const profileContainer = document.querySelector('.profile-container');
    if (profileContainer) {
        profileContainer.style.cursor = 'pointer';
        profileContainer.addEventListener('click', () => {
            manejarCarga('../perfil/ver_perfil.php', { classList: { remove: () => {}, add: () => {} } }, 'Mi Perfil');
        });
    }

    // Verificar si la caja requiere apertura al iniciar turno
    function verificarAperturaCaja() {
        fetch('../../controllers/caja/CajaController.php?action=consultar_apertura')
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success' && !response.tiene_apertura) {
                    const modalEl = document.getElementById('modalAperturaCaja');
                    if (modalEl) {
                        const modalBs = new bootstrap.Modal(modalEl);
                        modalBs.show();
                    }
                }
            })
            .catch(err => console.error("Error al consultar apertura:", err));
    }

    const formApertura = document.getElementById('form-apertura-caja');
    if (formApertura) {
        formApertura.addEventListener('submit', function(e) {
            e.preventDefault();
            const monto = parseFloat(document.getElementById('input-monto-apertura-inicial').value) || 0;
            
            const formData = new FormData();
            formData.append('monto_inicial', monto);

            fetch('../../controllers/caja/CajaController.php?action=abrir_caja', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const modalEl = document.getElementById('modalAperturaCaja');
                    const modalBs = bootstrap.Modal.getInstance(modalEl);
                    if (modalBs) modalBs.hide();
                    // Refrescar modulo actual
                    const activeLink = document.querySelector('.nav-link-custom.active');
                    if (activeLink) activeLink.click();
                } else {
                    alert("Error al iniciar turno: " + response.message);
                }
            })
            .catch(err => alert("Ocurrió un error al registrar la apertura de caja."));
        });
    }

    verificarAperturaCaja();
</script>
</body>
</html>