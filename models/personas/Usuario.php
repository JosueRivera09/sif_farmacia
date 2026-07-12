<?php
/*
 * Archivo: models/Usuario.php
 * Propósito: Modelo de datos para la tabla de usuarios.
 * Qué muestra: No muestra nada. Provee métodos estáticos (o de instancia) para interactuar con los datos de usuarios (CRUD y autenticación).
 */

class Usuario {
    
    /**
     * Obtiene todos los usuarios de la base de datos
     */
    public static function obtenerUsuarios(mysqli $conexion) {
        $usuarios = [];
        $query = "SELECT id_usuario, nombre_usuario, rol, fecha_creacion FROM usuarios ORDER BY id_usuario DESC";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
    }

    /**
     * Obtiene un usuario específico por su ID
     */
    public static function obtenerUsuarioPorId(mysqli $conexion, int $id_usuario) {
        $stmt = mysqli_prepare($conexion, "SELECT id_usuario, nombre_usuario, rol, fecha_creacion FROM usuarios WHERE id_usuario = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($resultado);
            mysqli_stmt_close($stmt);
            return $usuario;
        }
        return null;
    }
    
    /**
     * Obtiene un usuario por nombre de usuario (útil para login)
     */
    public static function obtenerUsuarioPorNombre(mysqli $conexion, string $nombre_usuario) {
        $stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE nombre_usuario = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $nombre_usuario);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($resultado);
            mysqli_stmt_close($stmt);
            return $usuario;
        }
        return null;
    }

    /**
     * Verifica si un nombre de usuario ya está registrado, permitiendo excluir un ID (para actualizaciones)
     */
    public static function nombreUsuarioExiste(mysqli $conexion, string $nombre_usuario, ?int $id_usuario_excluir = null) {
        if ($id_usuario_excluir !== null) {
            $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM usuarios WHERE nombre_usuario = ? AND id_usuario != ?");
            mysqli_stmt_bind_param($stmt, "si", $nombre_usuario, $id_usuario_excluir);
        } else {
            $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM usuarios WHERE nombre_usuario = ?");
            mysqli_stmt_bind_param($stmt, "s", $nombre_usuario);
        }

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($resultado);
            mysqli_stmt_close($stmt);
            return intval($row['total']) > 0;
        }
        return false;
    }

    /**
     * Crea un nuevo usuario en el sistema
     */
    public static function crearUsuario(mysqli $conexion, string $nombre_usuario, string $clave_acceso, string $rol) {
        // Hashear la contraseña con bcrypt para mayor seguridad
        $hash_clave = password_hash($clave_acceso, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre_usuario, clave_acceso, rol) VALUES (?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $nombre_usuario, $hash_clave, $rol);
            $exito = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $exito;
        }
        return false;
    }

    /**
     * Actualiza los datos de un usuario
     */
    public static function actualizarUsuario(mysqli $conexion, int $id_usuario, string $nombre_usuario, string $rol, ?string $clave_acceso = null) {
        if (!empty($clave_acceso)) {
            // Si se proporciona una contraseña, se actualiza también
            $hash_clave = password_hash($clave_acceso, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET nombre_usuario = ?, rol = ?, clave_acceso = ? WHERE id_usuario = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $nombre_usuario, $rol, $hash_clave, $id_usuario);
        } else {
            // Si no se proporciona contraseña, se conservan los datos de acceso anteriores
            $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET nombre_usuario = ?, rol = ? WHERE id_usuario = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $nombre_usuario, $rol, $id_usuario);
        }

        if ($stmt) {
            $exito = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $exito;
        }
        return false;
    }

    /**
     * Elimina un usuario por su ID
     */
    public static function eliminarUsuario(mysqli $conexion, int $id_usuario) {
        $stmt = mysqli_prepare($conexion, "DELETE FROM usuarios WHERE id_usuario = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario);
            $exito = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $exito;
        }
        return false;
    }

    /**
     * Obtiene los permisos extra de un usuario
     */
    public static function obtenerPermisosExtra(mysqli $conexion, int $id_usuario) {
        $permisos = [];
        $stmt = mysqli_prepare($conexion, "SELECT modulo FROM permisos_extra WHERE id_usuario = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($resultado)) {
                $permisos[] = $row['modulo'];
            }
            mysqli_stmt_close($stmt);
        }
        return $permisos;
    }

    /**
     * Actualiza los permisos extra de un usuario
     */
    public static function actualizarPermisosExtra(mysqli $conexion, int $id_usuario, array $permisos) {
        // Eliminar permisos actuales
        $stmt = mysqli_prepare($conexion, "DELETE FROM permisos_extra WHERE id_usuario = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Insertar nuevos permisos
        if (!empty($permisos)) {
            $stmt = mysqli_prepare($conexion, "INSERT INTO permisos_extra (id_usuario, modulo) VALUES (?, ?)");
            if ($stmt) {
                foreach ($permisos as $modulo) {
                    mysqli_stmt_bind_param($stmt, "is", $id_usuario, $modulo);
                    mysqli_stmt_execute($stmt);
                }
                mysqli_stmt_close($stmt);
            }
        }
        return true;
    }
}
?>
