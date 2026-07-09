<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';

    if (empty($usuario) || empty($clave)) {
        header("Location: ../views/login.php?error=vacio");
        exit;
    }

    // Escapar caracteres especiales para seguridad básica (aunque se recomienda usar sentencias preparadas)
    $usuario_escapado = mysqli_real_escape_string($conexion, $usuario);

    $query = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario_escapado' LIMIT 1";
    $result = mysqli_query($conexion, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);

        // Verificar la clave en texto plano
        if ($clave === $user_data['clave_acceso']) {
            // Regenerar ID de sesión para prevenir sesión fixation
            session_regenerate_id(true);

            // Almacenar datos en la sesión
            $_SESSION['id_usuario'] = $user_data['id_usuario'];
            $_SESSION['nombre_usuario'] = $user_data['nombre_usuario'];
            $_SESSION['rol'] = $user_data['rol'];

            // Redirigir según el rol del usuario
            if ($user_data['rol'] === 'Administrador') {
                header("Location: ../views/Interfaz_admin/admin_dashboard.php");
            } elseif ($user_data['rol'] === 'Vendedor') {
                header("Location: ../views/vendedor_dashboard.php");
            } else {
                // Rol no identificado, redirigir al login por seguridad
                header("Location: ../views/login.php?error=rol_no_autorizado");
            }
            exit;
        }
    }

    // Si falló el usuario o la contraseña
    header("Location: ../views/login.php?error=incorrecto");
    exit;
} else {
    // Si no es petición POST, redirigir al login
    header("Location: ../views/login.php");
    exit;
}
?>
