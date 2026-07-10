<?php
session_start();

// Validar que el usuario tenga sesión activa y sea Administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. No autorizado.']);
    exit;
}

// Cargar la conexión y el modelo de usuario (rutas actualizadas para subcarpeta controllers/admin/)
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/Usuario.php';

header('Content-Type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'listar':
        $usuarios = Usuario::obtenerUsuarios($conexion);
        echo json_encode(['status' => 'success', 'data' => $usuarios]);
        break;

    case 'obtener':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID de usuario inválido.']);
            exit;
        }
        $usuario = Usuario::obtenerUsuarioPorId($conexion, $id);
        if ($usuario) {
            echo json_encode(['status' => 'success', 'data' => $usuario]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }
        break;

    case 'guardar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            exit;
        }

        // Obtener datos del cuerpo o POST normal
        $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
        $nombre_usuario = isset($_POST['nombre_usuario']) ? trim($_POST['nombre_usuario']) : '';
        $rol = isset($_POST['rol']) ? trim($_POST['rol']) : '';
        $clave_acceso = isset($_POST['clave_acceso']) ? $_POST['clave_acceso'] : '';

        // Validaciones básicas
        if (empty($nombre_usuario) || empty($rol)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario y el rol son obligatorios.']);
            exit;
        }

        // Validar que el rol sea uno permitido
        $roles_permitidos = ['Administrador', 'Cajero', 'Vendedor', 'Bodega'];
        if (!in_array($rol, $roles_permitidos)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'El rol seleccionado no es válido.']);
            exit;
        }

        // Validar si el usuario ya existe
        if (Usuario::nombreUsuarioExiste($conexion, $nombre_usuario, $id_usuario > 0 ? $id_usuario : null)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario ya está registrado en el sistema.']);
            exit;
        }

        if ($id_usuario > 0) {
            // Edición de usuario existente
            $exito = Usuario::actualizarUsuario($conexion, $id_usuario, $nombre_usuario, $rol, $clave_acceso);
            if ($exito) {
                echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado correctamente.']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el usuario.']);
            }
        } else {
            // Creación de nuevo usuario (clave obligatoria)
            if (empty($clave_acceso)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'La contraseña es obligatoria para nuevos usuarios.']);
                exit;
            }

            $exito = Usuario::crearUsuario($conexion, $nombre_usuario, $clave_acceso, $rol);
            if ($exito) {
                echo json_encode(['status' => 'success', 'message' => 'Usuario creado correctamente.']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error al crear el usuario.']);
            }
        }
        break;

    case 'eliminar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            exit;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID de usuario inválido.']);
            exit;
        }

        // Evitar que el administrador se elimine a sí mismo
        if ($id === intval($_SESSION['id_usuario'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No puedes eliminar tu propio usuario activo.']);
            exit;
        }

        $exito = Usuario::eliminarUsuario($conexion, $id);
        if ($exito) {
            echo json_encode(['status' => 'success', 'message' => 'Usuario eliminado correctamente.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el usuario.']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
        break;
}
