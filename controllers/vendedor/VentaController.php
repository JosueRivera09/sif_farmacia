<?php
/*
 * Archivo: controllers/vendedor/VentaController.php
 * Propósito: Controlador de la interfaz del vendedor para gestionar ventas, carrito, y clientes.
 * Qué muestra: No muestra nada. Retorna respuestas JSON.
 */
session_start();

// Este controlador gestiona las solicitudes AJAX del Vendedor, como listar productos, obtener estadísticas y procesar la facturación/venta de productos.

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/Producto.php';
require_once __DIR__ . '/../../models/ventas/Venta.php';
require_once __DIR__ . '/../../models/ventas/TicketModel.php';
require_once __DIR__ . '/../../models/personas/Cliente.php';

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
    $nombre_cliente_temporal = isset($data['nombre_cliente_temporal']) ? trim($data['nombre_cliente_temporal']) : '';

    if (!$id_cliente && !empty($nombre_cliente_temporal)) {
        // Create client on the fly or find existing
        $nombre_safe = mysqli_real_escape_string($conexion, $nombre_cliente_temporal);
        $queryBusqueda = "SELECT id_cliente FROM clientes WHERE nombre_completo = '$nombre_safe' LIMIT 1";
        $resBusqueda = mysqli_query($conexion, $queryBusqueda);
        if ($resBusqueda && mysqli_num_rows($resBusqueda) > 0) {
            $row = mysqli_fetch_assoc($resBusqueda);
            $id_cliente = $row['id_cliente'];
        } else {
            // Create new
            $queryInsert = "INSERT INTO clientes (nombre_completo) VALUES ('$nombre_safe')";
            if (mysqli_query($conexion, $queryInsert)) {
                $id_cliente = mysqli_insert_id($conexion);
            }
        }
    }

    // Iniciar transacción de base de datos
    mysqli_begin_transaction($conexion);

    try {
        $total_venta = 0.0;
        foreach ($data['items'] as &$item) { // Note the & for reference to modify the item in place if needed, though we can just read
            $id_producto = intval($item['id_producto']);
            $cantidad = intval($item['cantidad']);
            $nivel = isset($item['nivel_empaque']) ? $item['nivel_empaque'] : 'Principal';
            
            if ($cantidad <= 0) {
                throw new Exception('Cantidad inválida');
            }

            // Consultar producto para comprobar stock y factores
            $query = "SELECT stock_actual, precio_empaque_principal, precio_empaque_medio, precio_unidad_minima, nombre_commercial,
                             unidades_totales_por_empaque_principal, unidades_por_empaque_medio 
                      FROM productos WHERE id_producto = $id_producto LIMIT 1";
            $res = mysqli_query($conexion, $query);
            if (!$res || mysqli_num_rows($res) === 0) {
                throw new Exception('Producto no encontrado');
            }

            $prod = mysqli_fetch_assoc($res);
            
            // Determinar factor y precio basado en el nivel de empaque
            $factor = 1;
            $precio = 0.0;
            
            if ($nivel === 'Principal') {
                $factor = intval($prod['unidades_totales_por_empaque_principal']);
                $precio = floatval($prod['precio_empaque_principal']);
            } elseif ($nivel === 'Medio') {
                $factor = intval($prod['unidades_por_empaque_medio']);
                $precio = floatval($prod['precio_empaque_medio']);
            } else {
                $precio = floatval($prod['precio_unidad_minima']);
            }
            
            // Guardar el precio real determinado por backend para evitar manipulación
            $item['precio'] = $precio;
            
            $unidades_a_descontar = $cantidad * $factor;
            
            if (intval($prod['stock_actual']) < $unidades_a_descontar) {
                throw new Exception("Stock insuficiente para: " . $prod['nombre_commercial']);
            }

            $total_venta += $precio * $cantidad;
        }
          // lo que edite
        // === RESTA DE STOCK AL GENERAR TICKET ===
        // Se resta aquí para que el inventario se actualice inmediatamente
        foreach ($data['items'] as $item) {
            $id_producto = intval($item['id_producto']);
            $cantidad = intval($item['cantidad']);

            $updateStock = "UPDATE productos 
                           SET stock_actual = stock_actual - $cantidad 
                           WHERE id_producto = $id_producto";

            if (!mysqli_query($conexion, $updateStock)) {
                throw new Exception("Error al actualizar el stock del producto ID: $id_producto");
            }
        }
        // =======================================================================================

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

elseif ($action === 'mis_tickets') {
    $id_vendedor = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
    if ($id_vendedor <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Vendedor no identificado']);
        exit;
    }

    $tickets = obtenerTicketsPorVendedor($conexion, $id_vendedor);
    echo json_encode(['status' => 'success', 'data' => $tickets]);
    exit;
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    exit;
}
?>
