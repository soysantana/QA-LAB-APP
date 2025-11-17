<?php
// pdf/ensayos_entregados_pdf.php
declare(strict_types=1);

require('../libs/fpdf/fpdf.php');
require_once('../config/load.php');

page_require_level(2);

$anioActualIso = (int)date('o');
$anioActual    = (int)date('Y');
$mesActual     = (int)date('n');
$trActual      = (int)floor(($mesActual - 1) / 3) + 1;
$semanaActual  = (int)date('W');

// Leer parámetros
$modo = isset($_GET['modo']) ? strtolower(trim($_GET['modo'])) : 'semana';
if (!in_array($modo, ['semana','mes','trimestre','anio'], true)) {
  $modo = 'semana';
}

if ($modo === 'semana') {
  $anio = isset($_GET['anio']) && $_GET['anio'] !== '' ? (int)$_GET['anio'] : $anioActualIso;
} else {
  $anio = isset($_GET['anio']) && $_GET['anio'] !== '' ? (int)$_GET['anio'] : $anioActual;
}

$semana    = isset($_GET['semana'])    && $_GET['semana']    !== '' ? (int)$_GET['semana']    : $semanaActual;
$mes       = isset($_GET['mes'])       && $_GET['mes']       !== '' ? (int)$_GET['mes']       : $mesActual;
$trimestre = isset($_GET['trimestre']) && $_GET['trimestre'] !== '' ? (int)$_GET['trimestre'] : $trActual;

$cliente    = isset($_GET['cliente'])    ? trim((string)$_GET['cliente'])    : '';
$estructura = isset($_GET['estructura']) ? trim((string)$_GET['estructura']) : '';
$testType   = isset($_GET['test_type'])  ? trim((string)$_GET['test_type'])  : '';

// =========================
// 1) Calcular rango de fechas
// =========================
$inicioPeriodo      = null;
$finPeriodo         = null;
$descripcionPeriodo = '';

try {
  if ($modo === 'semana') {
    $dt = new DateTime();
    $dt->setISODate($anio, $semana);
    $inicioPeriodo = $dt->format('Y-m-d');
    $dt->modify('+6 days');
    $finPeriodo = $dt->format('Y-m-d');
    $descripcionPeriodo = sprintf('Semana ISO %02d del año %04d', $semana, $anio);

  } elseif ($modo === 'mes') {
    if ($mes < 1 || $mes > 12) $mes = $mesActual;
    $dt = new DateTime(sprintf('%04d-%02d-01', $anio, $mes));
    $inicioPeriodo = $dt->format('Y-m-d');
    $dt->modify('last day of this month');
    $finPeriodo = $dt->format('Y-m-d');
    $descripcionPeriodo = sprintf('Mes %04d-%02d', $anio, $mes);

  } elseif ($modo === 'trimestre') {
    if ($trimestre < 1 || $trimestre > 4) $trimestre = $trActual;
    $mesInicio = ($trimestre - 1) * 3 + 1;
    $mesFin    = $mesInicio + 2;

    $dt = new DateTime(sprintf('%04d-%02d-01', $anio, $mesInicio));
    $inicioPeriodo = $dt->format('Y-m-d');

    $dtFin = new DateTime(sprintf('%04d-%02d-01', $anio, $mesFin));
    $dtFin->modify('last day of this month');
    $finPeriodo = $dtFin->format('Y-m-d');

    $descripcionPeriodo = sprintf('Trimestre Q%d %04d (M%d–M%d)', $trimestre, $anio, $mesInicio, $mesFin);

  } else {
    $inicioPeriodo = sprintf('%04d-01-01', $anio);
    $finPeriodo    = sprintf('%04d-12-31', $anio);
    $descripcionPeriodo = sprintf('Año completo %04d', $anio);
  }
} catch (Exception $e) {
  $inicioPeriodo = sprintf('%04d-01-01', $anio);
  $finPeriodo    = sprintf('%04d-12-31', $anio);
  $descripcionPeriodo = 'Periodo (fallback) ' . $anio;
}

// =========================
// 2) WHERE dinámico
// =========================
$where = [];
$where[] = sprintf(
  "d.Register_Date BETWEEN '%s 00:00:00' AND '%s 23:59:59'",
  $db->escape($inicioPeriodo),
  $db->escape($finPeriodo)
);

if ($cliente !== '') {
  $where[] = "r.Client = '" . $db->escape($cliente) . "'";
}
if ($estructura !== '') {
  $where[] = "r.Structure = '" . $db->escape($estructura) . "'";
}
if ($testType !== '') {
  $where[] = "d.Test_Type = '" . $db->escape($testType) . "'";
}

$whereSql = implode(' AND ', $where);

// =========================
// 3) Consultas
// =========================
$detalle = find_by_sql("
  SELECT
    d.Sample_ID,
    d.Sample_Number,
    d.Test_Type,
    r.Client,
    r.Structure,
    d.Technician,
    d.Register_Date
  FROM test_delivery d
  LEFT JOIN lab_test_requisition_form r
    ON r.Sample_ID     = d.Sample_ID
   AND r.Sample_Number = d.Sample_Number
  WHERE $whereSql
  ORDER BY d.Register_Date ASC, d.Sample_ID, d.Sample_Number, d.Test_Type
");

$resumen_dia = find_by_sql("
  SELECT
    DATE(d.Register_Date) AS fecha,
    COUNT(*) AS total
  FROM test_delivery d
  LEFT JOIN lab_test_requisition_form r
    ON r.Sample_ID     = d.Sample_ID
   AND r.Sample_Number = d.Sample_Number
  WHERE $whereSql
  GROUP BY DATE(d.Register_Date)
  ORDER BY fecha
");

$resumen_test = find_by_sql("
  SELECT
    d.Test_Type,
    COUNT(*) AS total
  FROM test_delivery d
  LEFT JOIN lab_test_requisition_form r
    ON r.Sample_ID     = d.Sample_ID
   AND r.Sample_Number = d.Sample_Number
  WHERE $whereSql
  GROUP BY d.Test_Type
  ORDER BY total DESC
");

$total_entregados = 0;
foreach ($resumen_dia as $r) {
  $total_entregados += (int)$r['total'];
}

// =========================
// 4) Clase PDF
// =========================
class PDF_Entregados_Periodo extends FPDF
{
  public string $titulo;
  public string $subtitulo;

  function Header()
  {
    // Logo si quieres:
    // $this->Image('../assets/img/logo.png', 10, 8, 25);

    $this->SetFont('Arial', 'B', 14);
    $this->Cell(0, 8, utf8_decode($this->titulo), 0, 1, 'C');

    $this->SetFont('Arial', '', 10);
    $this->Cell(0, 6, utf8_decode($this->subtitulo), 0, 1, 'C');

    $this->Ln(2);
    $this->SetDrawColor(200, 200, 200);
    $this->Line(10, $this->GetY(), 200, $this->GetY());
    $this->Ln(4);
  }

  function Footer()
  {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
  }

  function FancyTable($header, $data, $widths, $aligns = [])
  {
    // Header
    $this->SetFont('Arial', 'B', 9);
    $this->SetFillColor(230, 230, 230);
    $this->SetDrawColor(200, 200, 200);

    foreach ($header as $i => $col) {
      $w = $widths[$i] ?? 20;
      $this->Cell($w, 6, utf8_decode($col), 1, 0, 'C', true);
    }
    $this->Ln();

    // Data
    $this->SetFont('Arial', '', 8);
    $this->SetFillColor(245, 245, 245);
    $fill = false;

    foreach ($data as $row) {
      $colIndex = 0;
      foreach ($row as $cell) {
        $w   = $widths[$colIndex] ?? 20;
        $ali = $aligns[$colIndex] ?? 'L';
        $this->Cell($w, 5, utf8_decode((string)$cell), 1, 0, $ali, $fill);
        $colIndex++;
      }
      $this->Ln();
      $fill = !$fill;

      if ($this->GetY() > 270) {
        $this->AddPage();
      }
    }
  }
}

// =========================
// 5) Crear PDF
// =========================
$pdf = new PDF_Entregados_Periodo('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->titulo    = 'Ensayos entregados';
$pdf->subtitulo = $descripcionPeriodo . " ($inicioPeriodo a $finPeriodo)";
$pdf->SetMargins(10, 20, 10);
$pdf->AddPage();

// =========================
// 6) Resumen
// =========================
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode('Resumen del periodo'), 0, 1, 'L');
$pdf->Ln(1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 5, utf8_decode('Total ensayos entregados:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, (string)$total_entregados, 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 5, utf8_decode('Días con entregas:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, (string)count($resumen_dia), 0, 1, 'L');

$pdf->Ln(4);

// =========================
// 7) Tabla resumen por día
// =========================
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, utf8_decode('Ensayos entregados por día'), 0, 1, 'L');
$pdf->Ln(1);

if (empty($resumen_dia)) {
  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(0, 6, utf8_decode('No hay ensayos entregados en este periodo.'), 0, 1, 'L');
} else {
  $headerDia = ['Fecha', 'Cantidad ensayos'];
  $widthsDia = [40, 40];
  $alignDia  = ['L', 'R'];

  $dataDia = [];
  foreach ($resumen_dia as $r) {
    $fechaFmt = date('d/m/Y', strtotime($r['fecha']));
    $dataDia[] = [$fechaFmt, (int)$r['total']];
  }

  $pdf->FancyTable($headerDia, $dataDia, $widthsDia, $alignDia);
}

$pdf->Ln(6);

// =========================
// 8) Tabla resumen por tipo de ensayo
// =========================
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, utf8_decode('Resumen por tipo de ensayo'), 0, 1, 'L');
$pdf->Ln(1);

if (empty($resumen_test)) {
  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(0, 6, utf8_decode('No hay registros de tipos de ensayo en este periodo.'), 0, 1, 'L');
} else {
  $headerTest = ['Tipo de ensayo', 'Cantidad'];
  $widthsTest = [80, 30];
  $alignTest  = ['L', 'R'];

  $dataTest = [];
  foreach ($resumen_test as $rt) {
    $dataTest[] = [(string)$rt['Test_Type'], (int)$rt['total']];
  }

  $pdf->FancyTable($headerTest, $dataTest, $widthsTest, $alignTest);
}

$pdf->Ln(6);

// =========================
// 9) Detalle
// =========================
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, utf8_decode('Detalle de ensayos entregados'), 0, 1, 'L');
$pdf->Ln(1);

if (empty($detalle)) {
  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(0, 6, utf8_decode('No hay ensayos entregados en este periodo.'), 0, 1, 'L');
} else {
  $headerDet = ['Fecha', 'Sample ID', 'Sample Number', 'Test Type', 'Cliente', 'Estructura', 'Técnico'];
  $widthsDet = [28, 25, 25, 22, 40, 30, 25];
  $alignDet  = ['L','L','L','L','L','L','L'];

  $dataDet = [];
  foreach ($detalle as $row) {
    $fecha = substr((string)$row['Register_Date'], 0, 16); // yyyy-mm-dd HH:MM
    $dataDet[] = [
      $fecha,
      (string)$row['Sample_ID'],
      (string)$row['Sample_Number'],
      (string)$row['Test_Type'],
      (string)($row['Client'] ?? ''),
      (string)($row['Structure'] ?? ''),
      (string)$row['Technician'],
    ];
  }

  $pdf->FancyTable($headerDet, $dataDet, $widthsDet, $alignDet);
}

// =========================
// 10) Salida
// =========================
$filename = sprintf('Ensayos_entregados_%s_%04d.pdf', $modo, $anio);
$pdf->Output('I', $filename);
exit;
