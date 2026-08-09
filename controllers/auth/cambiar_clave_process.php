<?php
/*
 * Archivo: controllers/auth/cambiar_clave_process.php
 * Propósito: Procesar la solicitud de cambio de contraseña del usuario con sesión activa.
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario']) || intval($_SESSION['id_usuario']) <= 0) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No ha iniciado sesión.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/personas/Usuario.php';

$id_usuario = intval($_SESSION['id_usuario']);
$clave_actual = isset($_POST['clave_actual']) ? trim($_POST['clave_actual']) : '';
$clave_nueva = isset($_POST['clave_nueva']) ? trim($_POST['clave_nueva']) : '';
$clave_confirmar = isset($_POST['clave_confirmar']) ? trim($_POST['clave_confirmar']) : '';

if (empty($clave_actual) || empty($clave_nueva) || empty($clave_confirmar)) {
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

if ($clave_nueva !== $clave_confirmar) {
    echo json_encode(['status' => 'error', 'message' => 'La nueva contraseña y su confirmación no coinciden.']);
    exit;
}

$resultado = Usuario::cambiarClavePropia($conexion, $id_usuario, $clave_actual, $clave_nueva);
echo json_encode($resultado);
exit;
