<?php

// Este modelo gestiona la creación, consulta y procesamiento de cobro de los tickets de pre-venta generados por los vendedores.

function inicializarTablasTickets(mysqli $conexion) {
    // Crear tabla de tickets
    $queryTickets = "CREATE TABLE IF NOT EXISTS `tickets` (
        `id_ticket` int(11) NOT NULL AUTO_INCREMENT,
        `codigo_ticket` varchar(20) NOT NULL,
        `total` decimal(10,2) NOT NULL,
        `estado` varchar(20) NOT NULL DEFAULT 'Pendiente',
        `id_vendedor` int(11) NOT NULL,
        `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id_ticket`),
        UNIQUE KEY `codigo_ticket` (`codigo_ticket`),
        KEY `id_vendedor` (`id_vendedor`),
        CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_vendedor`) REFERENCES `usuarios` (`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($conexion, $queryTickets);

    // Crear tabla de detalles de ticket
    $queryDetalles = "CREATE TABLE IF NOT EXISTS `ticket_detalles` (
        `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
        `id_ticket` int(11) NOT NULL,
        `id_producto` int(11) NOT NULL,
        `cantidad` int(11) NOT NULL,
        `precio_unitario` decimal(10,2) NOT NULL,
        PRIMARY KEY (`id_detalle`),
        KEY `id_ticket` (`id_ticket`),
        KEY `id_producto` (`id_producto`),
        CONSTRAINT `ticket_detalles_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`) ON DELETE CASCADE,
        CONSTRAINT `ticket_detalles_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($conexion, $queryDetalles);
}

function crearTicket(mysqli $conexion, int $id_vendedor, array $items, float $total, ?int $id_cliente = null) {
    inicializarTablasTickets($conexion);
    
    // Generar código único aleatorio: TK-XXXXXX
    $codigo = 'TK-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    $id_vendedor = intval($id_vendedor);
    $total = floatval($total);
    
    $valCliente = is_null($id_cliente) || intval($id_cliente) <= 0 ? "NULL" : intval($id_cliente);

    $query = "INSERT INTO tickets (codigo_ticket, total, estado, id_vendedor, id_cliente) VALUES ('$codigo', $total, 'Pendiente', $id_vendedor, $valCliente)";
    if (mysqli_query($conexion, $query)) {
        $id_ticket = mysqli_insert_id($conexion);
        
        foreach ($items as $item) {
            $id_producto = intval($item['id_producto']);
            $cantidad = intval($item['cantidad']);
            $precio = floatval($item['precio']);
            $nivel_empaque = isset($item['nivel_empaque']) ? mysqli_real_escape_string($conexion, $item['nivel_empaque']) : 'Principal';
            $nombre_empaque = isset($item['nombre_empaque']) ? mysqli_real_escape_string($conexion, $item['nombre_empaque']) : 'Caja';
            
            $queryDetalle = "INSERT INTO ticket_detalles (id_ticket, id_producto, cantidad, precio_unitario, nivel_empaque, nombre_empaque) 
                             VALUES ($id_ticket, $id_producto, $cantidad, $precio, '$nivel_empaque', '$nombre_empaque')";
            mysqli_query($conexion, $queryDetalle);
        }
        
        return $codigo;
    }
    return false;
}

function obtenerTicketPorCodigo(mysqli $conexion, string $codigo) {
    inicializarTablasTickets($conexion);
    $codigo = mysqli_real_escape_string($conexion, trim($codigo));

    $query = "SELECT t.*, u.nombre_usuario as nombre_vendedor, c.nombre_completo as nombre_cliente, c.cedula as cedula_cliente, c.telefono as telefono_cliente 
              FROM tickets t 
              LEFT JOIN usuarios u ON t.id_vendedor = u.id_usuario 
              LEFT JOIN clientes c ON t.id_cliente = c.id_cliente
              WHERE t.codigo_ticket = '$codigo' AND t.estado = 'Pendiente' LIMIT 1";
    $res = mysqli_query($conexion, $query);

    if ($res && mysqli_num_rows($res) > 0) {
        $ticket = mysqli_fetch_assoc($res);
        $id_ticket = intval($ticket['id_ticket']);
        
        // Obtener detalles del ticket
        $queryDetalles = "SELECT td.*, p.nombre_commercial, p.codigo_barras 
                          FROM ticket_detalles td 
                          LEFT JOIN productos p ON td.id_producto = p.id_producto 
                          WHERE td.id_ticket = $id_ticket";
        $resDetalles = mysqli_query($conexion, $queryDetalles);
        
        $detalles = [];
        if ($resDetalles) {
            while ($row = mysqli_fetch_assoc($resDetalles)) {
                $detalles[] = $row;
            }
        }
        $ticket['items'] = $detalles;
        return $ticket;
    }
    return null;
}

function procesarPagoTicket(mysqli $conexion, int $id_ticket) {
    $id_ticket = intval($id_ticket);

    // Obtener items para verificar stock
    $queryItems = "SELECT id_producto, cantidad, nivel_empaque FROM ticket_detalles WHERE id_ticket = $id_ticket";
    $resItems = mysqli_query($conexion, $queryItems);
    
    if (!$resItems) return false;

    $deducciones = [];

    // Verificar existencias calculando unidades reales
    while ($row = mysqli_fetch_assoc($resItems)) {
        $id_prod = intval($row['id_producto']);
        $cant = intval($row['cantidad']);
        $nivel = $row['nivel_empaque'];
        
        $resStock = mysqli_query($conexion, "SELECT stock_actual, nombre_commercial, unidades_totales_por_empaque_principal, unidades_por_empaque_medio FROM productos WHERE id_producto = $id_prod LIMIT 1");
        if ($resStock && mysqli_num_rows($resStock) > 0) {
            $prod = mysqli_fetch_assoc($resStock);
            
            // Calcular factor de conversión a unidades mínimas
            $factor = 1;
            if ($nivel === 'Principal') {
                $factor = intval($prod['unidades_totales_por_empaque_principal']);
            } elseif ($nivel === 'Medio') {
                $factor = intval($prod['unidades_por_empaque_medio']);
            }
            
            $unidades_a_descontar = $cant * $factor;
            
            if (intval($prod['stock_actual']) < $unidades_a_descontar) {
                throw new Exception("Stock insuficiente en caja para: " . $prod['nombre_commercial']);
            }
            
            $deducciones[] = [
                'id_producto' => $id_prod,
                'unidades' => $unidades_a_descontar
            ];
        } else {
            throw new Exception("Producto no encontrado durante el cobro.");
        }
    }

    // Descontar stock real e ir marcando
    foreach ($deducciones as $deduccion) {
        $id_prod = $deduccion['id_producto'];
        $unidades = $deduccion['unidades'];
        
        $updateStock = "UPDATE productos SET stock_actual = stock_actual - $unidades WHERE id_producto = $id_prod";
        if (!mysqli_query($conexion, $updateStock)) {
            throw new Exception("Error al actualizar existencias de producto.");
        }
    }

    // Marcar ticket como pagado
    $updateTicket = "UPDATE tickets SET estado = 'Pagado' WHERE id_ticket = $id_ticket";
    return mysqli_query($conexion, $updateTicket);
}

function obtenerMetricasCajaReal(mysqli $conexion) {
    inicializarTablasTickets($conexion);
    $metricas = [
        'recaudacion_hoy' => 0.00,
        'tickets_pagados' => 0,
        'tickets_pendientes' => 0
    ];

    // Recaudación y pagados
    $query = "SELECT SUM(total) as recaudacion, COUNT(*) as pagados 
              FROM tickets 
              WHERE estado = 'Pagado' AND DATE(fecha_creacion) = CURDATE()";
    $res = mysqli_query($conexion, $query);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $metricas['recaudacion_hoy'] = isset($row['recaudacion']) ? floatval($row['recaudacion']) : 0.00;
        $metricas['tickets_pagados'] = isset($row['pagados']) ? intval($row['pagados']) : 0;
    }

    // Pendientes
    $queryPend = "SELECT COUNT(*) as pendientes 
                  FROM tickets 
                  WHERE estado = 'Pendiente' AND DATE(fecha_creacion) = CURDATE()";
    $resPend = mysqli_query($conexion, $queryPend);
    if ($resPend && $rowPend = mysqli_fetch_assoc($resPend)) {
        $metricas['tickets_pendientes'] = isset($rowPend['pendientes']) ? intval($rowPend['pendientes']) : 0;
    }

    return $metricas;
}

function obtenerTicketsPendientesHoy(mysqli $conexion) {
    inicializarTablasTickets($conexion);
    $query = "SELECT t.*, u.nombre_usuario as nombre_vendedor 
              FROM tickets t 
              LEFT JOIN usuarios u ON t.id_vendedor = u.id_usuario 
              WHERE t.estado = 'Pendiente' AND DATE(t.fecha_creacion) = CURDATE()
              ORDER BY t.id_ticket DESC";
    $res = mysqli_query($conexion, $query);
    $tickets = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $tickets[] = $row;
        }
    }
    return $tickets;
}
?>
