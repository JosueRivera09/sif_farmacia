<?php
session_start();

// Este controlador gestiona las operaciones de cobro de tickets realizadas por el Cajero.

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/admin/TicketModel.php';

header('Content-Type: application/json');

// Verificar sesión y rol (Cajero o Administrador pueden cobrar)
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'Cajero' && $_SESSION['rol'] !== 'Administrador')) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'buscar') {
    $codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';
    if (empty($codigo)) {
        echo json_encode(['status' => 'error', 'message' => 'Código de ticket no proporcionado']);
        exit;
    }

    $ticket = obtenerTicketPorCodigo($conexion, $codigo);
    if ($ticket) {
        echo json_encode(['status' => 'success', 'data' => $ticket]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ticket no encontrado o ya pagado']);
    }
    exit;
} 

elseif ($action === 'cobrar') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        exit;
    }

    $id_ticket = isset($_POST['id_ticket']) ? intval($_POST['id_ticket']) : 0;
    if ($id_ticket <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de ticket inválido']);
        exit;
    }

    // Iniciar transacción para proteger inventario
    mysqli_begin_transaction($conexion);

    try {
        $exito = procesarPagoTicket($conexion, $id_ticket);
        if ($exito) {
            mysqli_commit($conexion);
            echo json_encode(['status' => 'success', 'message' => 'Pago del ticket registrado y stock descontado exitosamente']);
        } else {
            throw new Exception("Error al procesar el pago del ticket.");
        }
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
} 

elseif ($action === 'metricas') {
    $metricas = obtenerMetricasCajaReal($conexion);
    echo json_encode(['status' => 'success', 'data' => $metricas]);
    exit;
}

elseif ($action === 'listar_pendientes') {
    $tickets = obtenerTicketsPendientesHoy($conexion);
    echo json_encode(['status' => 'success', 'data' => $tickets]);
    exit;
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    exit;
}
?>
