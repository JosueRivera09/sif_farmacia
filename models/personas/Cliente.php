<?php
/*
 * Archivo: models/Cliente.php
 * Propósito: Modelo de datos para gestionar clientes.
 * Qué muestra: No muestra nada. Provee métodos estáticos.
 */

class Cliente {

    public static function inicializarTablaClientes(mysqli $conexion) {
        $query = "CREATE TABLE IF NOT EXISTS `clientes` (
            `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
            `cedula` varchar(50) DEFAULT NULL,
            `nombre_completo` varchar(150) NOT NULL,
            `telefono` varchar(50) DEFAULT NULL,
            `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id_cliente`),
            UNIQUE KEY `cedula` (`cedula`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        mysqli_query($conexion, $query);

        // Modificar tabla tickets para agregar id_cliente si no existe
        $checkCol = "SHOW COLUMNS FROM `tickets` LIKE 'id_cliente'";
        $resCol = mysqli_query($conexion, $checkCol);
        if (mysqli_num_rows($resCol) == 0) {
            $alter = "ALTER TABLE `tickets` ADD COLUMN `id_cliente` int(11) DEFAULT NULL,
                      ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL";
            mysqli_query($conexion, $alter);
        }
    }

    public static function crearCliente(mysqli $conexion, ?string $cedula, string $nombre, ?string $telefono) {
        self::inicializarTablaClientes($conexion);
        $cedula = mysqli_real_escape_string($conexion, trim($cedula));
        $nombre = mysqli_real_escape_string($conexion, trim($nombre));
        $telefono = mysqli_real_escape_string($conexion, trim($telefono));

        $valCedula = empty($cedula) ? "NULL" : "'$cedula'";
        $valTelefono = empty($telefono) ? "NULL" : "'$telefono'";

        if (!empty($cedula)) {
            $check = "SELECT id_cliente FROM clientes WHERE cedula = '$cedula' LIMIT 1";
            $res = mysqli_query($conexion, $check);
            if ($res && mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                return [
                    'status' => 'exists',
                    'id_cliente' => $row['id_cliente'],
                    'message' => 'El cliente ya se encuentra registrado con este número de cédula.'
                ];
            }
        }

        $query = "INSERT INTO clientes (cedula, nombre_completo, telefono) VALUES ($valCedula, '$nombre', $valTelefono)";
        if (mysqli_query($conexion, $query)) {
            return [
                'status' => 'success',
                'id_cliente' => mysqli_insert_id($conexion),
                'message' => 'Cliente registrado exitosamente.'
            ];
        }
        return [
            'status' => 'error',
            'message' => mysqli_error($conexion)
        ];
    }

    public static function buscarClientePorFiltro(mysqli $conexion, string $filtro) {
        self::inicializarTablaClientes($conexion);
        $filtro = mysqli_real_escape_string($conexion, trim($filtro));

        $query = "SELECT * FROM clientes 
                  WHERE cedula LIKE '%$filtro%' OR telefono LIKE '%$filtro%' OR nombre_completo LIKE '%$filtro%'
                  LIMIT 15";
        $res = mysqli_query($conexion, $query);
        $clientes = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $clientes[] = $row;
            }
        }
        return $clientes;
    }

    public static function obtenerClientePorId(mysqli $conexion, int $id_cliente) {
        self::inicializarTablaClientes($conexion);
        $id_cliente = intval($id_cliente);
        $query = "SELECT * FROM clientes WHERE id_cliente = $id_cliente LIMIT 1";
        $res = mysqli_query($conexion, $query);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            return $row;
        }
        return null;
    }
}
?>
