<?php

/**
 * SISTEMA SIF - Exportador de Reportes de Bodega e Inventario
 * 
 * Permite exportar el listado completo de lotes a formatos:
 * - Excel: Archivo CSV compatible codificado en UTF-8 con delimitadores legibles.
 * - PDF: Documento formal auto-generado con FPDF, incluyendo logo, cabeceras estructuradas y tabla estilizada.
 */

/*
 * Archivo: controllers/bodega/exportar.php
 * Propósito: Controlador para exportar los lotes de bodega a Excel (.csv) o PDF.
 * Qué muestra: No muestra nada directamente (descarga el archivo).
 */

session_start();

// 1. Validar autenticación y permisos
$permisos_extra = isset($_SESSION['permisos_extra']) ? $_SESSION['permisos_extra'] : [];
$tiene_acceso_bodega = (
    isset($_SESSION['rol']) && (
        $_SESSION['rol'] === 'Bodega' ||
        $_SESSION['rol'] === 'Administrador' ||
        in_array('bodega', $permisos_extra)
    )
);

if (!isset($_SESSION['id_usuario']) || !$tiene_acceso_bodega) {
    header("Location: ../../views/login.php");
    exit;
}

// 2. Cargar dependencias
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/Lote.php';
require_once __DIR__ . '/../../models/inventario/Producto.php';

// 3. Obtener formato solicitado
$format = isset($_GET['format']) ? trim($_GET['format']) : 'excel';

// 4. Obtener todos los lotes de la base de datos
$lotes = Lote::obtenerTodosLosLotes($conexion);

if ($format === 'excel') {
    // === EXPORTAR A EXCEL (CSV UTF-8) ===

    // Configurar cabeceras HTTP para forzar la descarga de Excel
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Reporte_Inventario_' . date('Ymd_His') . '.csv"');

    // Abrir salida en PHP
    $output = fopen('php://output', 'w');

    // Añadir BOM UTF-8 para garantizar soporte de caracteres especiales y acentos en MS Excel
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Escribir fila de cabecera (usamos punto y coma ';' como delimitador estándar en español)
    fputcsv($output, [
        'Código de Lote',
        'Producto',
        'Categoría',
        'Laboratorio',
        'Cantidad',
        'Unidad',
        'Bodega',
        'Fecha Ingreso',
        'Fecha Vencimiento',
        'Estado'
    ], ';');

    // Escribir datos de los lotes
    $hoy = date('Y-m-d');
    foreach ($lotes as $lote) {
        // Calcular Estado
        $fecha_vencimiento = $lote['fecha_vencimiento'];
        $diff = strtotime($fecha_vencimiento) - strtotime($hoy);
        $dias = round($diff / (60 * 60 * 24));

        if ($dias <= 0) {
            $estado = 'Vencido';
        } elseif ($dias <= 30) {
            $estado = 'Próximo a Vencer';
        } else {
            $estado = 'Disponible';
        }

        fputcsv($output, [
            $lote['numero_lote'],
            $lote['nombre_commercial'],
            $lote['nombre_categoria'],
            $lote['nombre_laboratorio'] ?? 'No asignado',
            $lote['cantidad_unidades_recibidas'],
            $lote['unidad_minima'],
            $lote['bodega'],
            date('d/m/Y', strtotime($lote['fecha_creacion'])),
            date('d/m/Y', strtotime($lote['fecha_vencimiento'])),
            $estado
        ], ';');
    }

    fclose($output);
    exit;
} elseif ($format === 'pdf') {
    // === EXPORTAR A PDF (Usando FPDF) ===

    require_once __DIR__ . '/../../libs/fpdf/fpdf.php';

    // Clase personalizada para reporte PDF estructurado
    class ReportePDF extends FPDF
    {

        // Cabecera de página
        function Header()
        {
            // Logo de la empresa
            $logoPath = __DIR__ . '/../../assets/img/logo.png';
            if (file_exists($logoPath)) {
                $this->Image($logoPath, 10, 8, 22);
            }

            // Fuente Arial Negrita 14
            $this->SetFont('Arial', 'B', 15);
            $this->SetTextColor(15, 23, 42); // Gris Oscuro Slate-900

            // Títulos del reporte
            $this->Cell(25); // Espaciador para no superponer al logo
            $this->Cell(120, 8, utf8_decode('SISTEMA SIF - CONTROL DE INVENTARIO'), 0, 0, 'L');

            $this->SetFont('Arial', '', 10);
            $this->SetTextColor(100, 116, 139); // Slate-500
            $this->Cell(45, 6, utf8_decode('Fecha: ' . date('d/m/Y H:i')), 0, 1, 'R');

            $this->Cell(25);
            $this->SetFont('Arial', 'B', 11);
            $this->SetTextColor(16, 185, 129); // Accent Verde-10b981
            $this->Cell(120, 6, utf8_decode('REPORTE DE STOCK Y LOTES EN BODEGA'), 0, 0, 'L');

            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(100, 116, 139);
            $this->Cell(45, 6, utf8_decode('Usuario: ' . ($_SESSION['nombre_usuario'] ?? 'Admin')), 0, 1, 'R');

            // Línea divisoria
            $this->Ln(8);
            $this->SetDrawColor(203, 213, 225); // Border Slate-300
            $this->SetLineWidth(0.5);
            $this->Line(10, 36, 200, 36);
            $this->Ln(3);
        }

        // Pie de página
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(148, 163, 184); // Slate-400

            // Número de página
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb} - Generado automáticamente por SISTEMA SIF'), 0, 0, 'C');
        }
    }

    // Instanciar reporte en orientación Vertical, mm, A4 (ancho disponible de 190mm)
    $pdf = new ReportePDF('P', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetMargins(10, 15, 10);
    $pdf->SetAutoPageBreak(true, 20);

    // Espacio después de cabecera
    $pdf->Ln(2);

    // --- DISEÑAR TABLA ---

    // Configurar Fuente para Cabeceras de Tabla
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(15, 23, 42); // Fondo oscuro Slate-900
    $pdf->SetTextColor(255, 255, 255); // Texto Blanco

    // Definir anchos de columna (Suma total = 190)
    // Código (20), Producto (43), Laboratorio (30), Cant. (20), Bodega (32), Vencimiento (25), Estado (20)
    $anchos = [
        'lote' => 20,
        'producto' => 43,
        'laboratorio' => 30,
        'cantidad' => 20,
        'bodega' => 32,
        'vence' => 25,
        'estado' => 20
    ];

    // Dibujar Cabeceras de Tabla
    $pdf->Cell($anchos['lote'], 8, utf8_decode('Lote'), 1, 0, 'C', true);
    $pdf->Cell($anchos['producto'], 8, utf8_decode('Producto'), 1, 0, 'L', true);
    $pdf->Cell($anchos['laboratorio'], 8, utf8_decode('Laboratorio'), 1, 0, 'L', true);
    $pdf->Cell($anchos['cantidad'], 8, utf8_decode('Cant.'), 1, 0, 'C', true);
    $pdf->Cell($anchos['bodega'], 8, utf8_decode('Bodega'), 1, 0, 'L', true);
    $pdf->Cell($anchos['vence'], 8, utf8_decode('Vence'), 1, 0, 'C', true);
    $pdf->Cell($anchos['estado'], 8, utf8_decode('Estado'), 1, 1, 'C', true);

    // Configurar Fuente para Filas de Datos
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(51, 65, 85); // Slate-700

    $hoy = date('Y-m-d');
    $fill = false; // Alternancia de fondo

    foreach ($lotes as $lote) {
        // Calcular Estado y Colores de Alerta
        $fecha_vencimiento = $lote['fecha_vencimiento'];
        $diff = strtotime($fecha_vencimiento) - strtotime($hoy);
        $dias = round($diff / (60 * 60 * 24));

        // Colores para estados
        if ($dias <= 0) {
            $estado = 'Vencido';
            $bgEstado = [254, 226, 226]; // Rojo Claro
            $textEstado = [220, 38, 38]; // Rojo Fuerte
        } elseif ($dias <= 30) {
            $estado = 'P. Vencer';
            $bgEstado = [254, 243, 199]; // Amarillo Claro
            $textEstado = [217, 119, 6]; // Amarillo/Café
        } else {
            $estado = 'Disponible';
            $bgEstado = [209, 250, 229]; // Verde Claro
            $textEstado = [5, 150, 105]; // Verde Fuerte
        }

        // Guardar coordenadas de la celda de inicio para dibujar
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Alternar color de fila (Zebra striping)
        if ($fill) {
            $pdf->SetFillColor(248, 250, 252); // Fondo Slate-50
        } else {
            $pdf->SetFillColor(255, 255, 255); // Fondo Blanco
        }

        // 1. Código
        $pdf->Cell($anchos['lote'], 7, utf8_decode($lote['numero_lote']), 1, 0, 'C', true);

        // 2. Producto (Si es largo se corta)
        $prodName = strlen($lote['nombre_commercial']) > 24 ? substr($lote['nombre_commercial'], 0, 22) . '..' : $lote['nombre_commercial'];
        $pdf->Cell($anchos['producto'], 7, utf8_decode($prodName), 1, 0, 'L', true);

        // 3. Laboratorio
        $labName = $lote['nombre_laboratorio'] ?? 'No asignado';
        $labName = strlen($labName) > 17 ? substr($labName, 0, 15) . '..' : $labName;
        $pdf->Cell($anchos['laboratorio'], 7, utf8_decode($labName), 1, 0, 'L', true);

        // 4. Cantidad
        $cantStr = $lote['cantidad_unidades_recibidas'] . ' ' . $lote['unidad_minima'];
        $pdf->Cell($anchos['cantidad'], 7, utf8_decode($cantStr), 1, 0, 'C', true);

        // 5. Bodega
        $bodegaCortada = str_replace(['Principal - ', 'Externa '], ['', 'Ext. '], $lote['bodega']);
        $pdf->Cell($anchos['bodega'], 7, utf8_decode($bodegaCortada), 1, 0, 'L', true);

        // 6. Fecha Vencimiento
        $pdf->Cell($anchos['vence'], 7, date('d M Y', strtotime($lote['fecha_vencimiento'])), 1, 0, 'C', true);

        // 7. Celda del Estado (Con colores de fondo y texto personalizados)
        $pdf->SetFillColor($bgEstado[0], $bgEstado[1], $bgEstado[2]);
        $pdf->SetTextColor($textEstado[0], $textEstado[1], $textEstado[2]);
        $pdf->SetFont('Arial', 'B', 7.5);
        $pdf->Cell($anchos['estado'], 7, utf8_decode($estado), 1, 1, 'C', true);

        // Reestablecer color de texto y fuente por defecto
        $pdf->SetTextColor(51, 65, 85);
        $pdf->SetFont('Arial', '', 8);

        $fill = !$fill; // Invertir fila
    }

    // Salida al navegador (Vista en pestaña nueva)
    $pdf->Output('I', 'Reporte_Inventario_' . date('Ymd') . '.pdf');
    exit;
}
