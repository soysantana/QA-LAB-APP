<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validar fecha
if (!isset($_GET['fecha'])) {
  die('Fecha no especificada.');
}

$fecha = $_GET['fecha'];
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('F d, Y') : 'Invalid Date';

$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 16:00:00"));
$end   = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

// Resumen de actividades
$requisitioned = (int) find_by_sql("SELECT COUNT(*) as total FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$preparation   = (int) find_by_sql("SELECT COUNT(*) as total FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$realization   = (int) find_by_sql("SELECT COUNT(*) as total FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$delivery      = (int) find_by_sql("SELECT COUNT(*) as total FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];

// Detalles de ensayos
$test_details = [];
$tablas = [
  'test_preparation' => 'Register_Date',
  'test_realization' => 'Register_Date',
  'test_delivery'    => 'Register_Date',
  'test_reviewed'    => 'Start_Date'
];

foreach ($tablas as $tabla => $col_fecha) {
  $query = "SELECT Sample_Name, Sample_Number, Test_Type, Status";
  $has_tech = in_array($tabla, ['test_preparation', 'test_realization', 'test_delivery']);
  if ($has_tech) $query .= ", Technician";
  $query .= " FROM {$tabla} WHERE {$col_fecha} BETWEEN '{$start}' AND '{$end}'";
  $results = find_by_sql($query);

  foreach ($results as $row) {
    $test_details[] = [
      'sample' => trim($row['Sample_Name'] . ' ' . $row['Sample_Number']),
      'type'   => $row['Test_Type'],
      'tech'   => $has_tech ? $row['Technician'] : 'N/A',
      'status' => $row['Status']
    ];
  }
}

// --- Sección de ensayos pendientes ---
function normalize($v) {
  return strtoupper(trim($v));
}

$requisitions = find_all("lab_test_requisition_form");
$tables_to_check = ['test_preparation', 'test_delivery', 'test_realization', 'test_repeat', 'test_review', 'test_reviewed'];

$indexed_status = [];
foreach ($tables_to_check as $table) {
  $data = find_all($table);
  foreach ($data as $row) {
    $key = normalize($row['Sample_Name']) . "|" . normalize($row['Sample_Number']) . "|" . normalize($row['Test_Type']);
    $indexed_status[$key] = true;
  }
}

$pending_summary = [];
foreach ($requisitions as $req) {
  for ($i = 1; $i <= 20; $i++) {
    $tt_key = "Test_Type$i";
    if (empty($req[$tt_key])) continue;

    $sample_id = normalize($req['Sample_ID']);
    $sample_num = normalize($req['Sample_Number']);
    $test_type = normalize($req[$tt_key]);
    $key = $sample_id . "|" . $sample_num . "|" . $test_type;

    if (!isset($indexed_status[$key])) {
      if (!isset($pending_summary[$test_type])) $pending_summary[$test_type] = 0;
      $pending_summary[$test_type]++;
    }
  }
}
ksort($pending_summary);

// Clase PDF
class PDF extends FPDF {
  public $fecha_en;

  function Header() {
    if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
      $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 30);
    }
    $this->SetFont('Arial', 'B', 14);
    $this->SetXY(150, 10);
    $this->Cell(50, 10, 'Daily Laboratory Report', 0, 1, 'R');
    $this->SetFont('Arial', '', 12);
    $this->SetXY(150, 20);
    $this->Cell(50, 10, "Date: {$this->fecha_en}", 0, 1, 'R');
    $this->Ln(10);
  }

  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
  }
}

$pdf = new PDF();
$pdf->fecha_en = $fecha_en;
$pdf->AddPage();

// Resumen
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Summary of Activities', 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(90, 8, 'Test Process', 1, 0, 'C');
$pdf->Cell(30, 8, 'Quantity', 1, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(90, 8, 'Requisitioned', 1, 0);
$pdf->Cell(30, 8, $requisitioned, 1, 1);
$pdf->Cell(90, 8, 'In Preparation', 1, 0);
$pdf->Cell(30, 8, $preparation, 1, 1);
$pdf->Cell(90, 8, 'In Realization', 1, 0);
$pdf->Cell(30, 8, $realization, 1, 1);
$pdf->Cell(90, 8, 'Completed', 1, 0);
$pdf->Cell(30, 8, $delivery, 1, 1);

// Detalles
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Test Details', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Sample Number', 1, 0, 'C');
$pdf->Cell(40, 8, 'Test Type', 1, 0, 'C');
$pdf->Cell(45, 8, 'Technician', 1, 0, 'C');
$pdf->Cell(35, 8, 'Status', 1, 1, 'C');

$pdf->SetFont('Arial', '', 9);
foreach ($test_details as $detail) {
  $pdf->Cell(60, 8, $detail['sample'], 1);
  $pdf->Cell(40, 8, $detail['type'], 1);
  $pdf->Cell(45, 8, $detail['tech'], 1);
  $pdf->Cell(35, 8, $detail['status'], 1);
  $pdf->Ln();
}

// Ensayos pendientes
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Pending Tests', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(100, 8, 'Test Type', 1, 0, 'C');
$pdf->Cell(30, 8, 'Pending Count', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);
foreach ($pending_summary as $type => $count) {
  $pdf->Cell(100, 8, $type, 1);
  $pdf->Cell(30, 8, $count, 1, 1);
}

$pdf->Output("I", "Daily_Report_{$fecha}.pdf");
