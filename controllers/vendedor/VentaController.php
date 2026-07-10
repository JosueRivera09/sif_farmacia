<?php
/*
 * Archivo: controllers/vendedor/VentaController.php
 * Propósito: Controlador de la interfaz del vendedor para gestionar ventas, carrito, y clientes.
 * Qué muestra: No muestra nada. Retorna respuestas JSON.
 */
session_start();

// Este controlador gestiona las solicitudes AJAX del Vendedor, como listar productos, obtener estadísticas y procesar la facturación/venta de productos.

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/Producto.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/admin/TicketModel.php';
require_once __DIR__ . '/../../models/Cliente.php';

header('Content-Type: application/json');

// Verificar sesión y rol
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['Vendedor', 'Administrador'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'listar') {
    $productos = Producto::obtenerProductosVenta($conexion);
    echo json_encode(['status' => 'success', 'data' => $productos]);
    exit;
} 

elseif ($action === 'metricas') {
    $metricas = Venta::obtenerMetricasVendedor($conexion);
    // Simular total de ventas en la sesión actual
    $metricas['ventas_sesion'] = isset($_SESSION['ventas_simuladas_count']) ? $_SESSION['ventas_simuladas_count'] : 0;
    $metricas['monto_ventas'] = isset($_SESSION['ventas_simuladas_monto']) ? number_format($_SESSION['ventas_simuladas_monto'], 2) : "0.00";
    echo json_encode(['status' => 'success', 'data' => $metricas]);
    exit;
} 

elseif ($action === 'buscar_cliente') {
    $filtro = isset($_GET['query']) ? $_GET['query'] : '';
    $clientes = Cliente::buscarClientePorFiltro($conexion, $filtro);
    echo json_encode(['status' => 'success', 'data' => $clientes]);
    exit;
}

elseif ($action === 'crear_cliente') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        exit;
    }
    
    $cedula = isset($_POST['cedula']) ? $_POST['cedula'] : '';
    $nombre = isset($_POST['nombre_completo']) ? $_POST['nombre_completo'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';

    if (empty($nombre)) {
        echo json_encode(['status' => 'error', 'message' => 'El nombre completo es obligatorio.']);
        exit;
    }

    $res = Cliente::crearCliente($conexion, $cedula, $nombre, $telefono);
    echo json_encode($res);
    exit;
}

elseif ($action === 'vender') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        exit;
    }

    // Leer el JSON recibido
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['items']) || empty($data['items'])) {
        echo json_encode(['status' => 'error', 'message' => 'Carrito de compras vacío']);
        exit;
    }

    $id_cliente = isset($data['id_cliente']) ? intval($data['id_cliente']) : null;

    // Iniciar transacción de base de datos
    mysqli_begin_transaction($conexion);

    try {
        $total_venta = 0.0;
        foreach ($data['items'] as $item) {
            $id_producto = intval($item['id_producto']);
            $cantidad = intval($item['cantidad']);

            if ($cantidad <= 0) {
                throw new Exception('Cantidad inválida');
            }

            // Consultar producto para comprobar stock
            $query = "SELECT stock_actual, precio_venta_actual, nombre_commercial FROM productos WHERE id_producto = $id_producto LIMIT 1";
            $res = mysqli_query($conexion, $query);
            if (!$res || mysqli_num_rows($res) === 0) {
                throw new Exception('Producto no encontrado');
            }

            $prod = mysqli_fetch_assoc($res);
            if (intval($prod['stock_actual']) < $cantidad) {
                throw new Exception("Stock insuficiente para: " . $prod['nombre_commercial']);
            }

            $total_venta += floatval($prod['precio_venta_actual']) * $cantidad;
        }

        // Crear ticket en la base de datos
        $codigo_ticket = crearTicket($conexion, $_SESSION['id_usuario'], $data['items'], $total_venta, $id_cliente);
        if (!$codigo_ticket) {
            throw new Exception("Error al crear el ticket de venta.");
        }

        // Registrar estadísticas en sesión del vendedor
        if (!isset($_SESSION['ventas_simuladas_count'])) {
            $_SESSION['ventas_simuladas_count'] = 0;
            $_SESSION['ventas_simuladas_monto'] = 0.0;
        }
        $_SESSION['ventas_simuladas_count'] += 1;
        $_SESSION['ventas_simuladas_monto'] += $total_venta;

        mysqli_commit($conexion);
        echo json_encode([
            'status' => 'success',
            'message' => 'Ticket de pre-venta generado con éxito',
            'codigo_ticket' => $codigo_ticket,
            'total' => number_format($total_venta, 2)
        ]);

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    exit;
}
?>
