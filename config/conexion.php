<?php
/*
 * Archivo: config/conexion.php
 * Propósito: Configuración de la conexión a la base de datos.
 */

// Parámetros por defecto o variables de entorno
$host = getenv('DB_HOST') ?: "127.0.0.1";
$user = getenv('DB_USER') ?: "sif_user";
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "sif12345";
$db = getenv('DB_NAME') ?: "sistema_sif";
$port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

// Cargar configuración local opcional si existe
if (file_exists(__DIR__ . '/config.local.php')) {
    include __DIR__ . '/config.local.php';
}

$conexion = @mysqli_connect($host, $user, $password, $db, $port);

// Fallback inteligente: si falla con sif_user, intentar con root (común en MariaDB local recién instalado)
if (!$conexion && $user === 'sif_user') {
    $conexion_fallback = @mysqli_connect($host, 'root', '', $db, $port);
    if ($conexion_fallback) {
        $conexion = $conexion_fallback;
    }
}

if (!$conexion) {
    die("<h3>Error de conexión con la Base de Datos</h3>" .
        "<p>No se pudo conectar a <code>$host:$port</code> con el usuario <code>$user</code>.</p>" .
        "<p><b>Detalle:</b> " . mysqli_connect_error() . "</p>" .
        "<hr><p><b>Guía de solución en Linux / CachyOS:</b>" .
        "<ul>" .
        "<li>Asegúrate de que MariaDB esté iniciado: <code>sudo systemctl start mariadb</code></li>" .
        "<li>Importa la base de datos: <code>mariadb -u root -p < database/schema.sql</code> o ejecuta <code>./setup_linux.sh</code></li>" .
        "<li>O crea el archivo <code>config/config.local.php</code> con tus credenciales personalizadas.</li>" .
        "</ul></p>");
}

// Configurar codificación utf8mb4 para caracteres en español y símbolos
mysqli_set_charset($conexion, "utf8mb4");
?>
