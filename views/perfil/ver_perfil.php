<?php
/*
 * Archivo: views/perfil/ver_perfil.php
 * Propósito: Módulo de visualización del perfil del usuario logueado.
 * Qué muestra: Inicial del nombre de usuario y botón de acciones (cambiar contraseña).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/conexion.php';

$id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
$nombre_usuario = 'Desconocido';
$rol_usuario = 'Ninguno';
$fecha_creacion = 'No disponible';

if ($id_usuario > 0) {
    $query = "SELECT nombre_usuario, rol, fecha_creacion FROM usuarios WHERE id_usuario = $id_usuario LIMIT 1";
    $res = mysqli_query($conexion, $query);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $nombre_usuario = htmlspecialchars($row['nombre_usuario']);
        $rol_usuario = htmlspecialchars($row['rol']);
        $fecha_creacion = date('d M Y, h:i A', strtotime($row['fecha_creacion']));
    }
}

// Configurar badges de rol de forma premium
$badge_class = 'bg-secondary-subtle text-secondary';
$icon = 'person';
if ($rol_usuario === 'Administrador') {
    $badge_class = 'bg-warning-subtle text-warning border border-warning-subtle';
    $icon = 'shield_person';
} elseif ($rol_usuario === 'Cajero') {
    $badge_class = 'bg-primary-subtle text-primary border border-primary-subtle';
    $icon = 'payments';
} elseif ($rol_usuario === 'Vendedor') {
    $badge_class = 'bg-success-subtle text-success border border-success-subtle';
    $icon = 'point_of_sale';
} elseif ($rol_usuario === 'Bodega') {
    $badge_class = 'bg-info-subtle text-info border border-info-subtle';
    $icon = 'warehouse';
}
?>
<div class="custom-card shadow-sm mx-auto" style="max-width: 600px; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; background-color: #ffffff;">
    <!-- Encabezado del perfil con gradiente sutil -->
    <div class="p-4 text-center border-bottom border-secondary-subtle" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(59, 130, 246, 0.08) 100%);">
        <div class="position-relative d-inline-block mb-3">
            <div class="avatar-box shadow d-flex align-items-center justify-content-center text-white fw-bold" style="width: 100px; height: 100px; border-radius: 50%; border: 4px solid #ffffff; background-color: #10b981; margin: 0 auto; font-size: 40px;">
                <?php echo strtoupper(substr($nombre_usuario, 0, 1)); ?>
            </div>
            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2" title="Usuario Conectado" style="transform: translate(-10px, -5px);"></span>
        </div>
        <h4 class="mb-1 text-light" style="font-weight: 700; color: #0f172a !important;"><?php echo $nombre_usuario; ?></h4>
        <span class="badge rounded-pill px-3 py-1 font-bold <?php echo $badge_class; ?>" style="font-size: 11px;">
            <span class="material-symbols-outlined align-middle me-1" style="font-size: 14px;"><?php echo $icon; ?></span>
            <?php echo $rol_usuario; ?>
        </span>
    </div>

    <!-- Detalles del Perfil -->
    <div class="p-4">
        <div class="row g-3">
            <!-- Nombre de usuario -->
            <div class="col-12 d-flex align-items-center border-bottom border-light-subtle pb-3">
                <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px;">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <div>
                    <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Nombre de Usuario</small>
                    <span class="fw-semibold text-light" style="color: #1e293b !important;"><?php echo $nombre_usuario; ?></span>
                </div>
            </div>
            <!-- Rol -->
            <div class="col-12 d-flex align-items-center border-bottom border-light-subtle pb-3">
                <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px;">
                    <span class="material-symbols-outlined">shield</span>
                </div>
                <div>
                    <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Nivel de Permisos</small>
                    <span class="fw-semibold text-light" style="color: #1e293b !important;"><?php echo $rol_usuario; ?></span>
                </div>
            </div>
            <!-- Fecha de creación -->
            <div class="col-12 d-flex align-items-center border-bottom border-light-subtle pb-3">
                <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px;">
                    <span class="material-symbols-outlined">calendar_today</span>
                </div>
                <div>
                    <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Fecha de Registro</small>
                    <span class="fw-semibold text-light" style="color: #1e293b !important;"><?php echo $fecha_creacion; ?></span>
                </div>
            </div>
            <!-- Estado de cuenta -->
            <div class="col-12 d-flex align-items-center">
                <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px;">
                    <span class="material-symbols-outlined">verified_user</span>
                </div>
                <div>
                    <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Estado de Cuenta</small>
                    <span class="fw-semibold text-success">Activa y Segura</span>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-2 text-center">
            <button id="btn-abrir-modal-clave" class="btn btn-outline-success btn-sm w-100" style="border-radius: 8px; font-weight: 600; padding: 10px;">
                <span class="material-symbols-outlined align-middle me-1" style="font-size: 16px;">lock_reset</span>
                Cambiar Contraseña
            </button>
        </div>
    </div>
</div>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="modalCambiarClave" tabindex="-1" aria-labelledby="modalCambiarClaveLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom border-light-subtle bg-light" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalCambiarClaveLabel">
                    <span class="material-symbols-outlined text-success">lock_reset</span>
                    Cambiar Mi Contraseña
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-cambiar-clave">
                <div class="modal-body p-4">
                    <div id="alert-modal-clave" class="alert d-none mb-3" role="alert"></div>

                    <div class="mb-3">
                        <label for="clave_actual" class="form-label font-bold text-dark" style="font-size: 13px;">Contraseña Actual</label>
                        <input type="password" class="form-control" id="clave_actual" name="clave_actual" placeholder="Ingresa tu contraseña actual" required>
                    </div>

                    <div class="mb-3">
                        <label for="clave_nueva" class="form-label font-bold text-dark" style="font-size: 13px;">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="clave_nueva" name="clave_nueva" placeholder="Ingresa la nueva contraseña (mín. 4 caracteres)" required minlength="4">
                    </div>

                    <div class="mb-3">
                        <label for="clave_confirmar" class="form-label font-bold text-dark" style="font-size: 13px;">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" id="clave_confirmar" name="clave_confirmar" placeholder="Repite la nueva contraseña" required minlength="4">
                    </div>
                </div>
                <div class="modal-footer border-top border-light-subtle bg-light p-3" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 d-flex align-items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size: 18px;">save</span> Guardar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const btnAbrir = document.getElementById('btn-abrir-modal-clave');
    const formClave = document.getElementById('form-cambiar-clave');
    const alertBox = document.getElementById('alert-modal-clave');
    let modalBs = null;

    if (btnAbrir) {
        btnAbrir.addEventListener('click', function() {
            const modalEl = document.getElementById('modalCambiarClave');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined') {
                    modalBs = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modalBs.show();
                } else if (typeof $ !== 'undefined') {
                    $(modalEl).modal('show');
                }
            }
        });
    }

    if (formClave) {
        formClave.addEventListener('submit', function(e) {
            e.preventDefault();
            alertBox.classList.add('d-none');
            alertBox.className = 'alert d-none mb-3';

            const claveActual = document.getElementById('clave_actual').value;
            const claveNueva = document.getElementById('clave_nueva').value;
            const claveConfirmar = document.getElementById('clave_confirmar').value;

            if (claveNueva !== claveConfirmar) {
                alertBox.className = 'alert alert-danger mb-3';
                alertBox.innerText = 'La nueva contraseña y su confirmación no coinciden.';
                alertBox.classList.remove('d-none');
                return;
            }

            const formData = new FormData();
            formData.append('clave_actual', claveActual);
            formData.append('clave_nueva', claveNueva);
            formData.append('clave_confirmar', claveConfirmar);

            fetch('../../controllers/auth/cambiar_clave_process.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alertBox.className = 'alert alert-success mb-3';
                    alertBox.innerText = data.message;
                    alertBox.classList.remove('d-none');
                    formClave.reset();

                    setTimeout(() => {
                        if (modalBs) modalBs.hide();
                    }, 1800);
                } else {
                    alertBox.className = 'alert alert-danger mb-3';
                    alertBox.innerText = data.message || 'Error al cambiar contraseña.';
                    alertBox.classList.remove('d-none');
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alertBox.className = 'alert alert-danger mb-3';
                alertBox.innerText = 'Ocurrió un error al procesar la solicitud.';
                alertBox.classList.remove('d-none');
            });
        });
    }
})();
</script>
