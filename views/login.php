<?php
/*
 * Archivo: views/login.php
 * Propósito: Interfaz de inicio de sesión del sistema.
 * Qué muestra: Formulario de autenticación de usuarios.
 */
session_start();

// Si el usuario ya está logueado, redirigir según su rol

// Si el usuario ya está logueado, redirigir según su rol
if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['rol'] === 'Administrador') {
        header("Location: Interfaz_admin/admin_dashboard.php");
    } elseif ($_SESSION['rol'] === 'Cajero') {
        header("Location: Interfaz_caja/cajero_dashboard.php");
    } elseif ($_SESSION['rol'] === 'Vendedor') {
        header("Location: Interfaz_vendedor/vendedor_dashboard.php");
    } else {
        header("Location: login.php?error=rol_no_autorizado");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - SIF</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts - Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/auth/login.css">
</head>

<body>

    <div class="login-container">

        <!-- Sección de Logo -->
        <div class="logo-section">
            <div class="d-flex justify-content-center align-items-center mb-2">
                <img src="../assets/img/logo.png" alt="Logo SIF" style="width: 80px; height: 80px; object-fit: contain;">
                <span class="logo-title ms-2">SIF</span>
            </div>
            <div class="logo-subtitle">Sistema de Inventario de Farmacia</div>
        </div>

        <!-- Tarjeta de Login -->
        <div class="login-card">
            <h2 class="login-card-title">Acceso al Sistema</h2>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-custom d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <?php
                        if ($_GET['error'] == 'vacio') {
                            echo "Por favor, llene todos los campos.";
                        } elseif ($_GET['error'] == 'incorrecto') {
                            echo "Usuario o contraseña incorrectos.";
                        } else {
                            echo "Error de autenticación.";
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="../controllers/auth/login_process.php" method="POST">
                <!-- Nombre de Usuario -->
                <div class="mb-4">
                    <label for="usuario" class="form-label">Nombre de Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Nombre de Usuario" required autocomplete="username">
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label for="clave" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" id="clave" name="clave" placeholder="Contraseña" required autocomplete="current-password">
                    </div>
                </div>

                <!-- Botón de Iniciar Sesión -->
                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>
        </div>

    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>