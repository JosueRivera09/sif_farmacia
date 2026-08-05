<?php
/*
 * Archivo: controllers/admin/GenerarReportePDF.php
 * Propósito: Generar informe contable en PDF mediante la librería FPDF.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión y rol de administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    die("Acceso denegado. No autorizado.");
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/admin/ReportesModel.php';
require_once __DIR__ . '/../../libs/fpdf/fpdf.php';

$periodo = isset($_GET['periodo']) ? trim($_GET['periodo']) : 'diario';
$fecha_inicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
$fecha_fin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;

$datos = ReportesModel::obtenerDatosReporte($conexion, $periodo, $fecha_inicio, $fecha_fin);

class PDFReporteContable extends FPDF {
    public $periodoTexto = '';

    function Header() {
        // Título del sistema y encabezado institucional
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(15, 23, 42); // slate-900
        $this->Cell(120, 8, iconv('UTF-8', 'windows-1252', 'SISTEMA SIF - FARMACIA'), 0, 0, 'L');
        
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(16, 185, 129); // emerald-500
        $this->Cell(70, 8, iconv('UTF-8', 'windows-1252', 'REPORTE CONTABLE'), 0, 1, 'R');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(120, 5, iconv('UTF-8', 'windows-1252', 'RUC: J0310000001234 | San José de Bocay, Jinotega'), 0, 0, 'L');
        $this->Cell(70, 5, iconv('UTF-8', 'windows-1252', 'Período: ' . $this->periodoTexto), 0, 1, 'R');
        
        $this->Cell(120, 5, iconv('UTF-8', 'windows-1252', 'Emisión: ' . date('d/m/Y H:i:s')), 0, 1, 'L');

        $this->Ln(3);
        $this->SetDrawColor(203, 213, 225);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(6);
    }

    function Footer() {
        $this->SetY(-18);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(100, 5, iconv('UTF-8', 'windows-1252', 'Sistema SIF Farmacia - Reporte para Contabilidad y Auditoría'), 0, 0, 'L');
        $this->Cell(90, 5, iconv('UTF-8', 'windows-1252', 'Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }
}

// Determinar texto del período
$periodoTexto = 'Diario (Hoy)';
if ($periodo === 'semanal') $periodoTexto = 'Semanal (Últimos 7 días)';
if ($periodo === 'mensual') $periodoTexto = 'Mensual (Mes Actual)';
if ($periodo === 'personalizado') $periodoTexto = "Rango: $fecha_inicio al $fecha_fin";

$pdf = new PDFReporteContable('P', 'mm', 'A4');
$pdf->periodoTexto = $periodoTexto;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 25);

// 1. MÉTRICAS CLAVE CONTABLES
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(190, 6, iconv('UTF-8', 'windows-1252', '1. Resumen de Indicadores Financieros'), 0, 1, 'L');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(241, 245, 249);
$pdf->SetDrawColor(226, 232, 240);

$pdf->Cell(47.5, 7, iconv('UTF-8', 'windows-1252', 'RECAUDACIÓN TOTAL'), 1, 0, 'C', true);
$pdf->Cell(47.5, 7, iconv('UTF-8', 'windows-1252', 'VENTAS REALIZADAS'), 1, 0, 'C', true);
$pdf->Cell(47.5, 7, iconv('UTF-8', 'windows-1252', 'TICKET PROMEDIO'), 1, 0, 'C', true);
$pdf->Cell(47.5, 7, iconv('UTF-8', 'windows-1252', 'PRODUCTO ESTRELLA'), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(16, 185, 129); // Verde
$pdf->Cell(47.5, 8, 'C$ ' . number_format($datos['metricas']['total_recaudado'], 2), 1, 0, 'C');
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(47.5, 8, $datos['metricas']['total_tickets'] . ' Facturas', 1, 0, 'C');
$pdf->Cell(47.5, 8, 'C$ ' . number_format($datos['metricas']['ticket_promedio'], 2), 1, 0, 'C');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(47.5, 8, iconv('UTF-8', 'windows-1252', substr($datos['metricas']['producto_estrella'], 0, 24)), 1, 1, 'C');

$pdf->Ln(6);

// 2. VENTAS POR CATEGORÍA Y RENDIMIENTO DE VENDEDORES
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(92, 6, iconv('UTF-8', 'windows-1252', '2. Ventas por Categoría'), 0, 0, 'L');
$pdf->Cell(6, 6, '', 0, 0);
$pdf->Cell(92, 6, iconv('UTF-8', 'windows-1252', '3. Rendimiento por Vendedor'), 0, 1, 'L');
$pdf->Ln(2);

// Tabla Categorías
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetFillColor(241, 245, 249);
$pdf->Cell(60, 6, iconv('UTF-8', 'windows-1252', 'Categoría'), 1, 0, 'L', true);
$pdf->Cell(32, 6, iconv('UTF-8', 'windows-1252', 'Total (C$)'), 1, 0, 'R', true);

$pdf->Cell(6, 6, '', 0, 0);

// Tabla Vendedores
$pdf->Cell(45, 6, iconv('UTF-8', 'windows-1252', 'Vendedor'), 1, 0, 'L', true);
$pdf->Cell(20, 6, iconv('UTF-8', 'windows-1252', 'Cant. Vnt'), 1, 0, 'C', true);
$pdf->Cell(27, 6, iconv('UTF-8', 'windows-1252', 'Total (C$)'), 1, 1, 'R', true);

$pdf->SetFont('Arial', '', 8.5);
$maxRows = max(count($datos['categorias']), count($datos['vendedores']));
if ($maxRows == 0) {
    $pdf->Cell(92, 6, iconv('UTF-8', 'windows-1252', 'Sin datos en este período'), 1, 0, 'C');
    $pdf->Cell(6, 6, '', 0, 0);
    $pdf->Cell(92, 6, iconv('UTF-8', 'windows-1252', 'Sin datos en este período'), 1, 1, 'C');
} else {
    for ($i = 0; $i < $maxRows; $i++) {
        // Fila Categoría
        if (isset($datos['categorias'][$i])) {
            $cat = $datos['categorias'][$i];
            $pdf->Cell(60, 5.5, iconv('UTF-8', 'windows-1252', substr($cat['nombre_categoria'], 0, 32)), 1, 0, 'L');
            $pdf->Cell(32, 5.5, 'C$ ' . number_format($cat['total_monto'], 2), 1, 0, 'R');
        } else {
            $pdf->Cell(60, 5.5, '', 1, 0, 'L');
            $pdf->Cell(32, 5.5, '', 1, 0, 'R');
        }

        $pdf->Cell(6, 5.5, '', 0, 0);

        // Fila Vendedor
        if (isset($datos['vendedores'][$i])) {
            $vend = $datos['vendedores'][$i];
            $pdf->Cell(45, 5.5, iconv('UTF-8', 'windows-1252', substr($vend['vendedor'], 0, 24)), 1, 0, 'L');
            $pdf->Cell(20, 5.5, $vend['total_tickets'], 1, 0, 'C');
            $pdf->Cell(27, 5.5, 'C$ ' . number_format($vend['total_monto'], 2), 1, 1, 'R');
        } else {
            $pdf->Cell(45, 5.5, '', 1, 0, 'L');
            $pdf->Cell(20, 5.5, '', 1, 0, 'C');
            $pdf->Cell(27, 5.5, '', 1, 1, 'R');
        }
    }
}

$pdf->Ln(6);

// 3. REGISTRO DETALLADO DE TRANSACCIONES
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(190, 6, iconv('UTF-8', 'windows-1252', '4. Detalle de Transacciones Contables'), 0, 1, 'L');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetFillColor(241, 245, 249);
$pdf->Cell(30, 6, iconv('UTF-8', 'windows-1252', 'Código Ticket'), 1, 0, 'C', true);
$pdf->Cell(40, 6, iconv('UTF-8', 'windows-1252', 'Fecha y Hora'), 1, 0, 'C', true);
$pdf->Cell(45, 6, iconv('UTF-8', 'windows-1252', 'Vendedor'), 1, 0, 'L', true);
$pdf->Cell(45, 6, iconv('UTF-8', 'windows-1252', 'Cliente'), 1, 0, 'L', true);
$pdf->Cell(30, 6, iconv('UTF-8', 'windows-1252', 'Monto Total'), 1, 1, 'R', true);

$pdf->SetFont('Arial', '', 8);
if (count($datos['transacciones']) == 0) {
    $pdf->Cell(190, 6, iconv('UTF-8', 'windows-1252', 'No se registraron transacciones en el período seleccionado.'), 1, 1, 'C');
} else {
    foreach ($datos['transacciones'] as $t) {
        $pdf->Cell(30, 5.5, $t['codigo_ticket'], 1, 0, 'C');
        $pdf->Cell(40, 5.5, date('d/m/Y H:i', strtotime($t['fecha_creacion'])), 1, 0, 'C');
        $pdf->Cell(45, 5.5, iconv('UTF-8', 'windows-1252', substr($t['vendedor'], 0, 24)), 1, 0, 'L');
        $pdf->Cell(45, 5.5, iconv('UTF-8', 'windows-1252', substr($t['cliente'] ? $t['cliente'] : 'Cliente Final', 0, 24)), 1, 0, 'L');
        $pdf->Cell(30, 5.5, 'C$ ' . number_format($t['total'], 2), 1, 1, 'R');
    }
}

// 4. FIRMAS Y SELLOS DE AUTORIZACIÓN CONTABLE
$pdf->Ln(15);
if ($pdf->GetY() > 230) {
    $pdf->AddPage();
}

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(85, 5, '__________________________________', 0, 0, 'C');
$pdf->Cell(20, 5, '', 0, 0);
$pdf->Cell(85, 5, '__________________________________', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(85, 5, iconv('UTF-8', 'windows-1252', 'Elaborado por / Administrador'), 0, 0, 'C');
$pdf->Cell(20, 5, '', 0, 0);
$pdf->Cell(85, 5, iconv('UTF-8', 'windows-1252', 'Revisado por / Contabilidad'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 7.5);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(85, 4, iconv('UTF-8', 'windows-1252', 'Nombre y Firma'), 0, 0, 'C');
$pdf->Cell(20, 4, '', 0, 0);
$pdf->Cell(85, 4, iconv('UTF-8', 'windows-1252', 'Sello y Firma Auditoría'), 0, 1, 'C');

$pdf->Output('I', 'Reporte_Contable_' . date('Ymd_His') . '.pdf');
exit;
?>
