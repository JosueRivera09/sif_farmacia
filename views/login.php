<?php
session_start();
// Si el usuario ya está logueado, redirigir según su rol
if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['rol'] === 'Administrador') {
        header("Location: Interfaz_admin/admin_dashboard.php");
    } else {
        header("Location: vendedor_dashboard.php");
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
    
    <style>
        /* ==========================================
           CONFIGURACIÓN DEL FONDO DE PÁGINA
           ------------------------------------------
           Actualmente está configurado con fondo blanco (#ffffff)
           como se solicitó. Si deseas cambiar el fondo (por ejemplo,
           a una imagen, gradiente o color sólido), modifica las
           propiedades dentro de esta regla body.
           ========================================== */
        body {
            background-color: #ffffff; /* FONDO BLANCO CONFIGURABLE */
            /* Ejemplo de fondo con imagen o gradiente:
               background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
               background-size: cover;
               background-attachment: fixed;
            */
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        /* Contenedor del Logo */
        .logo-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-title {
            font-size: 3rem;
            font-weight: 700;
            color: #0d233a;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo-subtitle {
            font-size: 0.95rem;
            color: #6c757d;
            margin-top: 5px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Tarjeta de Login (Estilo maqueta: azul muy oscuro) */
        .login-card {
            background-color: #061e33; /* Azul oscuro de la maqueta */
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .login-card-title {
            color: #ffffff;
            font-size: 1.35rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.4;
        }

        /* Inputs y Labels */
        .form-label {
            color: #a0aec0;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .input-group {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(0, 139, 116, 0.25);
            border-color: #008B74;
        }

        .input-group-text {
            background-color: transparent;
            border: none;
            color: #4a5568;
            padding-left: 15px;
            padding-right: 10px;
        }

        .form-control {
            border: none;
            padding: 12px 12px 12px 5px;
            font-size: 0.95rem;
            color: #1a202c;
            background-color: transparent;
        }

        .form-control:focus {
            box-shadow: none;
            background-color: transparent;
        }

        /* Botón Iniciar Sesión (Estilo verde/turquesa de la maqueta) */
        .btn-login {
            background-color: #008B74; /* Color verde/turquesa */
            border: none;
            color: white;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #00705d;
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        /* Alertas de error */
        .alert-custom {
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #ea868f;
            font-size: 0.9rem;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        
        <!-- Sección de Logo -->
        <div class="logo-section">
            <!-- 
               ====================================================
               IMAGEN O LOGO DEL SISTEMA
               ----------------------------------------------------
               Actualmente renderiza un SVG inline que representa el
               maletín de primeros auxilios verde de la maqueta.
               Si deseas cambiar el logo por una imagen física, reemplaza
               el código <svg> por:
               <img src="../assets/img/tu_logo.png" alt="Logo SIF" width="80">
               ====================================================
            -->
            <div class="d-flex justify-content-center align-items-center mb-2">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="6" width="20" height="15" rx="4" fill="#008B74" />
                    <!-- Asa del maletín -->
                    <path d="M9 6V4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V6" stroke="#008B74" stroke-width="2" stroke-linecap="round"/>
                    <!-- Cruz médica blanca -->
                    <path d="M12 9V18" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
                    <path d="M7.5 13.5H16.5" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <span class="logo-title ms-2">SIF</span>
            </div>
            <div class="logo-subtitle">Sistema de Inventario de Farmacia</div>
        </div>

        <!-- Tarjeta de Login -->
        <div class="login-card">
            <h2 class="login-card-title">Acceso al Sistema de<br>Inventario de Farmacia</h2>

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

            <form action="../controllers/login_process.php" method="POST">
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
                <button type="submit" class="btn btn-login">Iniciar Sesión</button>
            </form>
        </div>

    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
