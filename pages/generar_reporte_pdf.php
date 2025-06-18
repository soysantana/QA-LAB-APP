<?php
ob_start(); // ← Esto habilita el buffer de salida
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');



if (!isset($_GET['fecha'])) {
  die('Fecha no especificada.');
}

$fecha = $_GET['fecha'];
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('F d, Y') : 'Invalid Date';

$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 16:00:00"));
$end = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

$day_of_week = date('w', strtotime($fecha));
$week_number = date('W', strtotime($fecha));

$requisitioned = (int) find_by_sql("SELECT COUNT(*) as total FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$preparation   = (int) find_by_sql("SELECT COUNT(*) as total FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$realization   = (int) find_by_sql("SELECT COUNT(*) as total FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$delivery      = (int) find_by_sql("SELECT COUNT(*) as total FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];
$reviewed      = (int) find_by_sql("SELECT COUNT(*) as total FROM test_reviewed WHERE Start_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'];

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

function normalize($v) {
  return strtoupper(trim($v));
}

$requisitions = find_by_sql("SELECT * FROM lab_test_requisition_form");
$tables_to_check = [
  'test_preparation',
  'test_delivery',
  'test_realization',
  'test_repeat',
  'test_review',
  'test_reviewed'
];

$indexed_status = [];
foreach ($tables_to_check as $table) {
  $data = find_all($table);
  foreach ($data as $row) {
    $key = normalize($row['Sample_Name']) . "|" . normalize($row['Sample_Number']) . "|" . normalize($row['Test_Type']);
    $indexed_status[$key] = true;
  }
}

$pending_tests = [];
foreach ($requisitions as $requisition) {
  $sample_id = normalize($requisition['Sample_ID']);
  $sample_num = normalize($requisition['Sample_Number']);
  $test_types = explode(',', $requisition['Test_Type'] ?? '');
  $date = $requisition['Sample_Date'];

  foreach ($test_types as $test_type_raw) {
    $test_type = normalize($test_type_raw);
    $key = $sample_id . "|" . $sample_num . "|" . $test_type;

    if (!isset($indexed_status[$key])) {
      $pending_tests[] = [
        'Sample_ID' => $requisition['Sample_ID'],
        'Sample_Number' => $requisition['Sample_Number'],
        'Test_Type' => trim($test_type_raw),
        'Sample_Date' => $date
      ];
    }
  }
}

$ensayos_reporte = find_by_sql("SELECT * FROM ensayos_reporte WHERE Report_Date BETWEEN '{$start}' AND '{$end}'");

class PDF extends FPDF {
  public $fecha_en;
  public $day_of_week;
  public $week_number;

  function Header() {
    if ($this->PageNo() > 1) return;
    if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
      $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 30);
    }
    $this->SetFont('Arial', 'B', 14);
    $this->SetXY(150, 10);
    $this->Cell(50, 10, 'Daily Laboratory Report', 0, 1, 'R');
    $this->SetFont('Arial', '', 12);
    $this->SetXY(150, 20);
    $this->Cell(50, 10, "Date: {$this->fecha_en}", 0, 1, 'R');
    $this->Ln(15);

    $this->SetFont('Arial', 'B', 11);
    $this->Cell(0, 8, 'Personnel Assigned', 0, 1);
    $this->SetFont('Arial', '', 10);

    if ($this->day_of_week == 3) {
      $this->MultiCell(0, 6, "Contractor Lab Technicians: Wilson Martinez, Rafy Leocadio, Rony Vargas, Jonathan Vargas, Rafael Reyes, Darielvy Felix, Jordany Almonte, Joel Ledesma", 0, 'L');
      $this->MultiCell(0, 6, "PV Laboratory Supervisors: Diana Vazquez, Laura Sanchez", 0, 'L');
      $this->MultiCell(0, 6, "Lab Document Control: Yamilexi Mejia, Frandy Epsinal, Arturo Santana", 0, 'L');
      $this->MultiCell(0, 6, "Field Supervisor: Adelqui Acosta, Victor Mercedes", 0, 'L');
      $this->MultiCell(0, 6, "Field Technicians: Jordany Amparo, Luis Monegro", 0, 'L');
    } elseif (in_array($this->day_of_week, [0, 1, 2])) {
      $this->MultiCell(0, 6, "Contractor Lab Technicians: Wilson Martinez, Rafy Leocadio, Rony Vargas, Jonathan Vargas", 0, 'L');
      $this->MultiCell(0, 6, "PV Supervisor: Diana Vazquez", 0, 'L');
      $this->MultiCell(0, 6, "Lab Document Control: " . ($this->week_number % 2 === 0 ? "Yamilexi Mejia, Frandy Espinal" : "Frandy Espinal, Yamilexi Mejía"), 0, 'L');
      $this->MultiCell(0, 6, "Field Supervisor: Adelqui Acosta", 0, 'L');
      $this->MultiCell(0, 6, "Field Technicians: Jordany Amparo", 0, 'L');
    } else {
      $this->MultiCell(0, 6, "Contractor Lab Technicians: Rafael Reyes, Darielvy Felix, Jordany Almonte, Joel Ledesma", 0, 'L');
      $this->MultiCell(0, 6, "PV Supervisor: Laura Sanchez", 0, 'L');
      $this->MultiCell(0, 6, "Lab Document Control: " . ($this->week_number % 2 === 0 ? "Yamilexi Mejia, Arturo Santana" : "Arturo Santana, Yamilexi Mejía"), 0, 'L');
      $this->MultiCell(0, 6, "Field Supervisor: Victor Mercedes", 0, 'L');
      $this->MultiCell(0, 6, "Field Technicians: Luis Monegro", 0, 'L');
    }

    $this->Ln(2);
    $this->SetFont('Arial', 'I', 10);
    $this->Cell(0, 8, "Report reviwed by: Wendin De Jesus", 0, 1);
    $this->Ln(5);
  }

  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
  }
}

$pdf = new PDF();
$pdf->fecha_en = $fecha_en;
$pdf->day_of_week = $day_of_week;
$pdf->week_number = $week_number;
$pdf->AddPage();

// Summary Table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Summary of Activities', 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(90, 8, 'Test Process', 1, 0, 'C');
$pdf->Cell(30, 8, 'Quantity', 1, 1, 'C');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(90, 8, 'Requisitioned', 1, 0); $pdf->Cell(30, 8, $requisitioned, 1, 1);
$pdf->Cell(90, 8, 'In Preparation', 1, 0); $pdf->Cell(30, 8, $preparation, 1, 1);
$pdf->Cell(90, 8, 'In Realization', 1, 0); $pdf->Cell(30, 8, $realization, 1, 1);
$pdf->Cell(90, 8, 'Completed', 1, 0); $pdf->Cell(30, 8, $delivery, 1, 1);

$pdf->Ln(10);

// Status of Tests
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Status of Tests', 0, 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 8, 'Sample ID', 1);
$pdf->Cell(25, 8, 'Structure', 1);
$pdf->Cell(20, 8, 'Mat. Type', 1);
$pdf->Cell(25, 8, 'Test Type', 1);
$pdf->Cell(20, 8, 'Condition', 1);
$pdf->Cell(70, 8, 'Comments', 1, 1);
$pdf->SetFont('Arial', '', 9);
foreach ($ensayos_reporte as $row) {
  $pdf->Cell(35, 8, $row['Sample_Name'] . '-' . $row['Sample_Number'], 1);
  $pdf->Cell(25, 8, $row['Structure'], 1);
  $pdf->Cell(20, 8, $row['Material_Type'], 1);
  $pdf->Cell(25, 8, $row['Test_Type'], 1);
  $pdf->Cell(20, 8, $row['Test_Condition'], 1);
  $pdf->MultiCell(70, 8, $row['Comments'], 1);
}

$pdf->Ln(10);

// Test Details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'Test Details', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Test Legend: AR= Acid Reativity, GS= Grain Size, SG= Specific Gravity, SP= Standard Proctor, MP= Modified Proctor, AL= Atterberg Limit,   ', 0, 1);
$pdf->Cell(0, 5, 'HY= Hidrometer, DHY= Double Hydromter, SCT= Sand Castle, SND= Soundness, LAA= Los Angeles Abrasion, MC= Moisture Content, ', 0, 1);
$pdf->Cell(0, 5, 'PLT= Point Load, UCS= Simple Compression, BTS, Brazilian, Shape= Particle Shape,  ', 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Sample Number', 1);
$pdf->Cell(40, 8, 'Test Type', 1);
$pdf->Cell(45, 8, 'Technician', 1);
$pdf->Cell(35, 8, 'Status', 1, 1);
$pdf->SetFont('Arial', '', 9);
foreach ($test_details as $detail) {
  $pdf->Cell(60, 8, $detail['sample'], 1);
  $pdf->Cell(40, 8, $detail['type'], 1);
  $pdf->Cell(45, 8, $detail['tech'], 1);
  $pdf->Cell(35, 8, $detail['status'], 1);
  $pdf->Ln();
}

$pdf->Ln(10);

// Pending Tests
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Pending Tests', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Test Legend: AR= Acid Reativity, GS= Grain Size, SG= Specific Gravity, SP= Standard Proctor, MP= Modified Proctor, AL= Atterberg Limit,   ', 0, 1);
$pdf->Cell(0, 5, 'HY= Hidrometer, DHY= Double Hydromter, SCT= Sand Castle, SND= Soundness, LAA= Los Angeles Abrasion, MC= Moisture Content, ', 0, 1);
$pdf->Cell(0, 5, 'PLT= Point Load, UCS= Simple Compression, BTS, Brazilian, Shape= Particle Shape,  ', 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(40, 8, 'Sample ID', 1);
$pdf->Cell(40, 8, 'Sample Number', 1);
$pdf->Cell(60, 8, 'Test Type', 1);
$pdf->Cell(40, 8, 'Sample Date', 1, 1);
$pdf->SetFont('Arial', '', 9);
foreach ($pending_tests as $i => $row) {
  $pdf->Cell(10, 8, $i + 1, 1);
  $pdf->Cell(40, 8, $row['Sample_ID'], 1);
  $pdf->Cell(40, 8, $row['Sample_Number'], 1);
  $pdf->Cell(60, 8, $row['Test_Type'], 1);
  $pdf->Cell(40, 8, $row['Sample_Date'], 1);
  $pdf->Ln();
}


$pdf->Output("I", "Daily_Laboratory_Report_{$fecha}.pdf");
exit;