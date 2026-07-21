<?php
/*
 * Archivo: controllers/admin/ReportesController.php
 * Propósito: Gestionar peticiones AJAX de reportes contables.
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
require_once __DIR__ . '/../../models/admin/ReportesModel.php';

header('Content-Type: application/json; charset=utf-8');

$periodo = isset($_GET['periodo']) ? trim($_GET['periodo']) : 'diario';
$fecha_inicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
$fecha_fin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;

try {
    $datos = ReportesModel::obtenerDatosReporte($conexion, $periodo, $fecha_inicio, $fecha_fin);
    echo json_encode(['status' => 'success', 'data' => $datos]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al generar reporte: ' . $e->getMessage()]);
    exit;
}
?>
