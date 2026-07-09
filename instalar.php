<?php
/**
 * SISTEMA SIF - Instalador y Configurador Automático de Base de Datos
 */

$is_cli = (php_sapi_name() === 'cli');

// Mostrar diseño HTML si se ejecuta en navegador
if (!$is_cli) { ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador del Sistema SIF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .install-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        .btn-install {
            background-color: #008B74;
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-install:hover {
            background-color: #00705d;
            color: white;
        }
        .log-box {
            background-color: #1e1e1e;
            color: #39ff14;
            font-family: 'Courier New', Courier, monospace;
            padding: 15px;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 20px;
            font-size: 0.9rem;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container text-center">
    <div class="install-card mx-auto">
        <h2 class="fw-bold mb-3 text-dark">Instalador Base de Datos - SIF</h2>
        <p class="text-muted">Este script configurará la base de datos, tablas, disparadores (triggers) y usuarios de prueba por defecto para que el sistema funcione de inmediato.</p>

        <?php if (!isset($_GET['ejecutar'])) { ?>
            <div class="alert alert-info" role="alert">
                Asegúrate de tener <strong>XAMPP</strong> iniciado (Apache y MySQL).
            </div>
            <a href="?ejecutar=1" class="btn btn-install btn-lg shadow-sm">Instalar y Configurar Ahora</a>
        <?php } else { ?>
            <div class="log-box">
<?php } }

if (isset($_GET['ejecutar']) || $is_cli) {
    // 1. Cargar Configuración de conexión básica
    $host = "localhost";
    $user = "root";
    $password = "";

    echo "Conectando al servidor MySQL local...\n";
    $conexion_inicial = @mysqli_connect($host, $user, $password);

    if (!$conexion_inicial) {
        die("❌ ERROR: No se pudo conectar a MySQL. Asegúrate de que XAMPP y MySQL estén encendidos.\nDetalles del error: " . mysqli_connect_error() . "\n");
    }
    echo "✔ Conectado a MySQL con éxito.\n\n";

    // 2. Ejecutar el script SQL
    echo "Cargando esquema database/schema.sql...\n";
    $schema_file = __DIR__ . '/database/schema.sql';
    if (!file_exists($schema_file)) {
        die("❌ ERROR: No se encontró el archivo de esquema en $schema_file\n");
    }

    $sql_schema = file_get_contents($schema_file);

    // Ejecutar múltiples consultas
    if (mysqli_multi_query($conexion_inicial, $sql_schema)) {
        do {
            // Vaciar resultados intermedios
            if ($result = mysqli_store_result($conexion_inicial)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($conexion_inicial));
        echo "✔ Base de datos y tablas creadas exitosamente.\n\n";
    } else {
        die("❌ ERROR al importar el esquema: " . mysqli_error($conexion_inicial) . "\n");
    }

    // Cerrar y conectar a la nueva BD
    mysqli_close($conexion_inicial);
    $conexion = mysqli_connect($host, $user, $password, 'sistema_sif');

    // 3. Crear Usuarios por defecto (Encriptados)
    echo "Creando usuarios de prueba por defecto...\n";
    $usuarios = [
        [
            'nombre_usuario' => 'admin',
            'clave' => 'admin123',
            'rol' => 'Administrador'
        ],
        [
            'nombre_usuario' => 'vendedor',
            'clave' => 'vendedor123',
            'rol' => 'Vendedor'
        ]
    ];

    foreach ($usuarios as $usr) {
        $nombre = $usr['nombre_usuario'];
        $hash = password_hash($usr['clave'], PASSWORD_BCRYPT, ['cost' => 12]);
        $rol = $usr['rol'];

        // Verificar si existe antes de insertar
        $check = mysqli_query($conexion, "SELECT id_usuario FROM usuarios WHERE nombre_usuario = '$nombre'");
        if (mysqli_num_rows($check) == 0) {
            $insert = "INSERT INTO usuarios (nombre_usuario, clave_acceso, rol) VALUES ('$nombre', '$hash', '$rol')";
            if (mysqli_query($conexion, $insert)) {
                echo "✔ Usuario '{$nombre}' creado. Acceso -> Clave: '{$usr['clave']}' | Rol: '{$rol}'\n";
            } else {
                echo "❌ Error al crear usuario '{$nombre}': " . mysqli_error($conexion) . "\n";
            }
        } else {
            echo "ℹ El usuario '{$nombre}' ya existe.\n";
        }
    }
    echo "\n";

    // 4. Crear Trigger (Disparador)
    echo "Configurando disparadores (triggers)...\n";
    mysqli_query($conexion, "DROP TRIGGER IF EXISTS actualizar_stock_nuevo_lote");
    $trigger_sql = "
    CREATE TRIGGER actualizar_stock_nuevo_lote
    AFTER INSERT ON lotes
    FOR EACH ROW
    BEGIN
        UPDATE productos 
        SET stock_actual = stock_actual + NEW.cantidad_recibida
        WHERE id_producto = NEW.id_producto;
    END
    ";
    if (mysqli_query($conexion, $trigger_sql)) {
        echo "✔ Disparador 'actualizar_stock_nuevo_lote' configurado correctamente.\n\n";
    } else {
        echo "❌ Error al configurar disparador: " . mysqli_error($conexion) . "\n\n";
    }

    // 5. Cargar Semillas de inventario de prueba
    echo "Sembrando datos de prueba en inventario...\n";
    
    // Categorías
    $checkCat = mysqli_query($conexion, "SELECT COUNT(*) as total FROM categorias");
    $rowCat = mysqli_fetch_assoc($checkCat);
    if ($rowCat['total'] == 0) {
        mysqli_query($conexion, "INSERT INTO categorias (id_categoria, nombre_categoria) VALUES (1, 'Analgésicos'), (2, 'Antibióticos')");
        echo "✔ Categorías sembradas.\n";
    }

    // Laboratorios
    $checkLab = mysqli_query($conexion, "SELECT COUNT(*) as total FROM laboratorios");
    $rowLab = mysqli_fetch_assoc($checkLab);
    if ($rowLab['total'] == 0) {
        mysqli_query($conexion, "INSERT INTO laboratorios (id_laboratorio, nombre_laboratorio) VALUES (1, 'Bayer'), (2, 'Pfizer')");
        echo "✔ Laboratorios sembrados.\n";
    }

    // Productos
    $checkProd = mysqli_query($conexion, "SELECT COUNT(*) as total FROM productos");
    $rowProd = mysqli_fetch_assoc($checkProd);
    if ($rowProd['total'] == 0) {
        $insertProd1 = "INSERT INTO productos (id_producto, codigo_barras, nombre_commercial, descripcion, id_categoria, id_laboratorio, miligramos, unidad_medida, precio_venta_actual, stock_actual) 
                        VALUES (1, '7701234567890', 'Paracetamol 500mg', 'Alivio del dolor y la fiebre', 1, 1, 500, 'Cajas', 5.00, 500)";
        $insertProd2 = "INSERT INTO productos (id_producto, codigo_barras, nombre_commercial, descripcion, id_categoria, id_laboratorio, miligramos, unidad_medida, precio_venta_actual, stock_actual) 
                        VALUES (2, '7709876543210', 'Amoxicilina 500mg', 'Antibiótico bactericida', 2, 2, 500, 'Frascos', 12.00, 300)";
        mysqli_query($conexion, $insertProd1);
        mysqli_query($conexion, $insertProd2);
        echo "✔ Productos sembrados.\n";
    }

    // Lotes
    $checkLotes = mysqli_query($conexion, "SELECT COUNT(*) as total FROM lotes");
    $rowLotes = mysqli_fetch_assoc($checkLotes);
    if ($rowLotes['total'] == 0) {
        $fechaProximo = date('Y-m-d', strtotime('+15 days'));
        $fechaDisponible = date('Y-m-d', strtotime('+500 days'));
        $insertLote1 = "INSERT INTO lotes (id_producto, numero_lote, cantidad_recibida, fecha_vencimiento) 
                        VALUES (1, 'LOT-20451', 500, '$fechaDisponible')";
        $insertLote2 = "INSERT INTO lotes (id_producto, numero_lote, cantidad_recibida, fecha_vencimiento) 
                        VALUES (2, 'LOT-30812', 300, '$fechaProximo')";
        mysqli_query($conexion, $insertLote1);
        mysqli_query($conexion, $insertLote2);
        echo "✔ Lotes sembrados.\n";
    }

    echo "\n🚀 ¡INSTALACIÓN COMPLETADA CON ÉXITO! El sistema está listo para usar.\n";
    mysqli_close($conexion);
}

if (!$is_cli) { ?>
            <a href="index.php" class="btn btn-success btn-lg mt-4 shadow-sm w-100">Ir al Sistema (Login)</a>
    </div>
</div>
</body>
</html>
        <?php } ?>


