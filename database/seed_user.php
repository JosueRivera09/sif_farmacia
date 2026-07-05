<?php
require_once __DIR__ . '/../config/conexion.php';

// Verificar si ya existen usuarios
$query = "SELECT COUNT(*) as total FROM usuarios";
$result = mysqli_query($conexion, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row['total'] > 0) {
        echo "La tabla 'usuarios' ya contiene registros. No se realizó ninguna inserción.\n";
        exit;
    }
} else {
    die("Error al consultar la tabla usuarios: " . mysqli_error($conexion));
}

// Usuarios por defecto
$usuarios = [
    [
        'nombre_usuario' => 'admin',
        'clave_acceso' => password_hash('admin123', PASSWORD_DEFAULT),
        'rol' => 'Administrador'
    ],
    [
        'nombre_usuario' => 'vendedor',
        'clave_acceso' => password_hash('vendedor123', PASSWORD_DEFAULT),
        'rol' => 'Vendedor'
    ]
];

// Insertar usuarios
foreach ($usuarios as $usr) {
    $nombre = $usr['nombre_usuario'];
    $clave = $usr['clave_acceso'];
    $rol = $usr['rol'];
    
    $insertQuery = "INSERT INTO usuarios (nombre_usuario, clave_acceso, rol) VALUES ('$nombre', '$clave', '$rol')";
    
    if (mysqli_query($conexion, $insertQuery)) {
        echo "Usuario '{$nombre}' creado correctamente con el rol '{$rol}'.\n";
    } else {
        echo "Error al crear el usuario '{$nombre}': " . mysqli_error($conexion) . "\n";
    }
}
?>
