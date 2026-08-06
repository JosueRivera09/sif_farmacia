<?php
$host = "127.0.0.1";
$user = "sif_user";
$password = "sif12345";
$db = "sistema_sif";

$conexion = mysqli_connect($host, $user, $password, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
