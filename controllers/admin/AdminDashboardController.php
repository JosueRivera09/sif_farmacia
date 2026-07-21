<?php
/*
 * Archivo: controllers/admin/AdminDashboardController.php
 * Propósito: Proveer datos para el panel principal del Administrador mediante AJAX.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. No autorizado.']);
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/admin/DashboardModel.php';

header('Content-Type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : 'todo';

if ($action === 'todo') {
    // Obtener todas las métricas para cargar el dashboard completo
    $datos = [
        'metricas' => DashboardModel::obtenerMetricasGenerales($conexion),
        'ventas' => DashboardModel::obtenerHistorialVentas($conexion),
        'usuarios' => DashboardModel::obtenerUsuarios($conexion),
        'alertas' => DashboardModel::obtenerAlertasStock($conexion)
    ];

    echo json_encode(['status' => 'success', 'data' => $datos]);
    exit;
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
    exit;
}
