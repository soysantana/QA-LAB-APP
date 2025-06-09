<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

if (!isset($_GET['fecha'])) {
  die('Fecha no especificada.');
}

$fecha = $_GET['fecha'];
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('F d, Y') : 'Invalid Date';

// Rango de 4 PM del día anterior a 3:59:59 PM del día actual
$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 16:00:00"));
$end = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

// Resumen de actividades
$requisitioned = (int) find_by_sql("SELECT COUNT(*) as total FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$preparation = (int) find_by_sql("SELECT COUNT(*) as total FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$realization = (int) find_by_sql("SELECT COUNT(*) as total FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$delivery = (int) find_by_sql("SELECT COUNT(*) as total FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$reviewed = (int) find_by_sql("SELECT COUNT(*) as total FROM test_reviewed WHERE Start_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];

// Detalles de ensayos
$test_details = [];
$tablas = [
  'test_preparation' => 'Register_Date',
  'test_realization' => 'Register_Date',
  'test_delivery' => 'Register_Date',
  'test_reviewed' => 'Start_Date'
];

foreach ($tablas as $tabla => $col_fecha) {
  $results = find_by_sql("SELECT Sample_Name, Sample_Number, Test_Type, Technician, Status FROM {$tabla} WHERE {$col_fecha} BETWEEN '{$start}' AND '{$end}'");
  foreach ($results as $row) {
    $test_details[] = [
      'sample' => trim($row['Sample_Name'] . ' ' . $row['Sample_Number']),
      'type' => $row['Test_Type'],
      'tech' => $row['Technician'],
      'status' => $row['Status']
    ];
  }
}

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

// Generar PDF
$pdf = new PDF();
$pdf->fecha_en = $fecha_en;
$pdf->AddPage();

// Título: Summary
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

$pdf->Cell(90, 8, 'Delivered', 1, 0);
$pdf->Cell(30, 8, $delivery, 1, 1);

$pdf->Cell(90, 8, 'Reviewed', 1, 0);
$pdf->Cell(30, 8, $reviewed, 1, 1);

// Línea de separación
$pdf->Ln(10);

// Título: Test Details
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

// Salida del PDF
$pdf->Output("I", "Daily_Report_{$fecha}.pdf");

