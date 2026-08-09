<?php
session_start();

// Este controlador gestiona las operaciones de cobro de tickets realizadas por el Cajero.

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/ventas/TicketModel.php';

header('Content-Type: application/json');

// Verificar sesión, rol y permisos extra (Cajero, Administrador o permiso extra a caja)
$permisos_extra = isset($_SESSION['permisos_extra']) ? $_SESSION['permisos_extra'] : [];
$tiene_acceso_caja = (
    isset($_SESSION['rol']) && (
        $_SESSION['rol'] === 'Cajero' ||
        $_SESSION['rol'] === 'Administrador' ||
        in_array('caja', $permisos_extra) ||
        in_array('cajero', $permisos_extra)
    )
);

if (!isset($_SESSION['id_usuario']) || !$tiene_acceso_caja) {
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
        $id_cajero = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
        $exito = procesarPagoTicket($conexion, $id_ticket, $id_cajero);
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

elseif ($action === 'cancelar_ticket') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        exit;
    }

    $id_ticket = isset($_POST['id_ticket']) ? intval($_POST['id_ticket']) : 0;
    if ($id_ticket <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de ticket inválido']);
        exit;
    }

    mysqli_begin_transaction($conexion);

    try {
        $exito = cancelarTicket($conexion, $id_ticket);
        if ($exito) {
            mysqli_commit($conexion);
            echo json_encode(['status' => 'success', 'message' => 'Ticket borrado exitosamente. Los productos fueron devueltos al stock del inventario.']);
        } else {
            throw new Exception("Error al borrar el ticket.");
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
    $es_admin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador');
    $id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
    
    $pagados = listarTicketsPagadosHoy($conexion, $id_usuario, $es_admin);
    echo json_encode([
        'status' => 'success',
        'es_admin' => $es_admin,
        'id_usuario_actual' => $id_usuario,
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

elseif ($action === 'guardar_arqueo') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        exit;
    }

    $id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
    $monto_inicial = isset($_POST['monto_inicial']) ? floatval($_POST['monto_inicial']) : 1000.00;
    $monto_esperado = isset($_POST['monto_esperado']) ? floatval($_POST['monto_esperado']) : 0.0;
    $monto_fisico = isset($_POST['monto_fisico']) ? floatval($_POST['monto_fisico']) : 0.0;
    $diferencia = isset($_POST['diferencia']) ? floatval($_POST['diferencia']) : 0.0;
    
    $denominaciones_raw = isset($_POST['denominaciones']) ? $_POST['denominaciones'] : '{}';
    $denominaciones = json_decode($denominaciones_raw, true);
    if (!is_array($denominaciones)) $denominaciones = [];

    $exito = registrarCierreCaja($conexion, $id_usuario, $monto_inicial, $monto_fisico, $monto_esperado, $diferencia, $denominaciones);

    if ($exito) {
        echo json_encode(['status' => 'success', 'message' => 'Arqueo de caja registrado correctamente en el sistema.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar el arqueo de caja en la base de datos.']);
    }
    exit;
}

elseif ($action === 'consultar_apertura') {
    $id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
    $abierto = obtenerTurnoCajaAbierto($conexion, $id_usuario);
    if ($abierto) {
        echo json_encode([
            'status' => 'success',
            'tiene_apertura' => true,
            'monto_inicial' => floatval($abierto['monto_inicial']),
            'fecha_apertura' => $abierto['fecha_apertura']
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'tiene_apertura' => false
        ]);
    }
    exit;
}

elseif ($action === 'abrir_caja') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        exit;
    }

    $id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
    $monto_inicial = isset($_POST['monto_inicial']) ? floatval($_POST['monto_inicial']) : 0.0;

    if ($monto_inicial < 0) {
        echo json_encode(['status' => 'error', 'message' => 'El monto de apertura no puede ser negativo']);
        exit;
    }

    $id_cierre = abrirTurnoCaja($conexion, $id_usuario, $monto_inicial);
    if ($id_cierre) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Apertura de caja iniciada con éxito',
            'monto_inicial' => $monto_inicial
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar la apertura de caja']);
    }
    exit;
}

elseif ($action === 'obtener_arqueo_hoy') {
    $id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
    $abierto = obtenerTurnoCajaAbierto($conexion, $id_usuario);
    if ($abierto) {
        echo json_encode([
            'status' => 'success',
            'registrado' => false,
            'monto_inicial' => floatval($abierto['monto_inicial'])
        ]);
    } else {
        $cierre = obtenerUltimoCierreCajaHoy($conexion, $id_usuario);
        if ($cierre) {
            echo json_encode([
                'status' => 'success',
                'registrado' => true,
                'data' => $cierre,
                'monto_inicial' => floatval($cierre['monto_inicial'])
            ]);
        } else {
            echo json_encode(['status' => 'success', 'registrado' => false, 'monto_inicial' => 1000.00]);
        }
    }
    exit;
}

elseif ($action === 'listar_cierres') {
    $es_admin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador');
    $id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
    
    $cierres = listarUltimosCierresCaja($conexion, $id_usuario, $es_admin);
    echo json_encode(['status' => 'success', 'data' => $cierres]);
    exit;
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    exit;
}
?>
