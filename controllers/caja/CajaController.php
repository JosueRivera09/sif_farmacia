<?php
session_start();

// Este controlador gestiona las operaciones de cobro de tickets realizadas por el Cajero.

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/ventas/TicketModel.php';

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

elseif ($action === 'listar_pagados') {
    // Obtener tickets pagados de hoy
    $query = "SELECT t.id_ticket, t.codigo_ticket, t.total, t.fecha_creacion, t.id_vendedor, u.nombre_usuario as nombre_vendedor, c.nombre_completo as nombre_cliente
              FROM tickets t 
              LEFT JOIN usuarios u ON t.id_vendedor = u.id_usuario 
              LEFT JOIN clientes c ON t.id_cliente = c.id_cliente
              WHERE t.estado = 'Pagado' AND DATE(t.fecha_creacion) = CURDATE()
              ORDER BY t.id_ticket DESC";
    $res = mysqli_query($conexion, $query);
    $pagados = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $pagados[] = $row;
        }
    }
    echo json_encode([
        'status' => 'success',
        'es_admin' => (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'),
        'id_usuario_actual' => isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0,
        'nombre_usuario_actual' => isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : 'Usuario',
        'rol_usuario_actual' => isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Cajero',
        'data' => $pagados
    ]);
    exit;
}

elseif ($action === 'ver_ticket') {
    $codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';
    if (empty($codigo)) {
        echo json_encode(['status' => 'error', 'message' => 'Código de ticket no proporcionado']);
        exit;
    }
    $ticket = obtenerTicketDetallePorCodigo($conexion, $codigo);
    if ($ticket) {
        echo json_encode(['status' => 'success', 'data' => $ticket]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ticket no encontrado']);
    }
    exit;
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    exit;
}
?>
