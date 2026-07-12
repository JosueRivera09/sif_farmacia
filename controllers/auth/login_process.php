<?php
session_start();

// Este controlador se encarga de procesar el inicio de sesión de los usuarios del sistema SIF, validando sus credenciales y redirigiéndolos al dashboard correspondiente según su rol.

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/personas/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';

    if (empty($usuario) || empty($clave)) {
        header("Location: ../../views/login.php?error=vacio");
        exit;
    }

    $user_data = Usuario::obtenerUsuarioPorNombre($conexion, $usuario);

    if ($user_data) {

        // Verificar la clave con hash bcrypt (o texto plano como fallback)
        if (password_verify($clave, $user_data['clave_acceso']) || $clave === $user_data['clave_acceso']) {
            // Regenerar ID de sesión para prevenir sesión fixation
            session_regenerate_id(true);

            // Almacenar datos en la sesión
            $_SESSION['id_usuario'] = $user_data['id_usuario'];
            $_SESSION['nombre_usuario'] = $user_data['nombre_usuario'];
            $_SESSION['rol'] = $user_data['rol'];
            $_SESSION['permisos_extra'] = Usuario::obtenerPermisosExtra($conexion, $user_data['id_usuario']);

            // Redirigir según el rol del usuario
            if ($user_data['rol'] === 'Administrador') {
                header("Location: ../../views/Interfaz_admin/admin_dashboard.php");
            } elseif ($user_data['rol'] === 'Cajero') {
                header("Location: ../../views/Interfaz_caja/cajero_dashboard.php");
            } elseif ($user_data['rol'] === 'Vendedor') {
                header("Location: ../../views/Interfaz_vendedor/vendedor_dashboard.php");
            } elseif ($user_data['rol'] === 'Bodega') {
                header("Location: ../../views/bodega/bodega_lotes.php");
            } else {
                // Rol no identificado, redirigir al login por seguridad
                header("Location: ../../views/login.php?error=rol_no_autorizado");
            }
            exit;
        }
    }

    // Si falló el usuario o la contraseña
    header("Location: ../../views/login.php?error=incorrecto");
    exit;
} else {
    // Si no es petición POST, redirigir al login
    header("Location: ../../views/login.php");
    exit;
}
?>
