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
            <button class="btn btn-outline-success btn-sm w-100" style="border-radius: 8px; font-weight: 600; padding: 10px;" onclick="alert('Funcionalidad de cambio de contraseña en desarrollo.')">
                <span class="material-symbols-outlined align-middle me-1" style="font-size: 16px;">lock_reset</span>
                Cambiar Contraseña
            </button>
        </div>
    </div>
</div>
