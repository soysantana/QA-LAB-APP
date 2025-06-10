<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

function normalize($v) {
  return strtoupper(trim((string)$v));
}

// Cargar todos los requisitions
$requisitions = find_all("lab_test_requisition_form");

// Tablas donde se validará si ya fueron procesados
$tables_to_check = [
  'test_preparation',
  'test_delivery',
  'test_realization',
  'test_repeat',
  'test_review',
  'test_reviewed'
];

// Crear índice de combinaciones ya procesadas
$indexed_status = [];
foreach ($tables_to_check as $table) {
  $data = find_all($table);
  foreach ($data as $row) {
    if (!isset($row['Sample_Name'], $row['Sample_Number'], $row['Test_Type'])) continue;
    $key = normalize($row['Sample_Name']) . "|" . normalize($row['Sample_Number']) . "|" . normalize($row['Test_Type']);
    $indexed_status[$key] = true;
  }
}

// Buscar los ensayos pendientes
$pending_tests = [];
foreach ($requisitions as $requisition) {
  for ($i = 1; $i <= 20; $i++) {
    $testKey = "Test_Type" . $i;
    if (empty($requisition[$testKey])) continue;

    $sample_id = normalize($requisition['Sample_ID']);
    $sample_num = normalize($requisition['Sample_Number']);
    $test_type = normalize($requisition[$testKey]);
    $key = $sample_id . "|" . $sample_num . "|" . $test_type;

    if (!isset($indexed_status[$key])) {
      $pending_tests[] = [
        'Sample_ID' => $requisition['Sample_ID'],
        'Sample_Number' => $requisition['Sample_Number'],
        'Test_Type' => $requisition[$testKey],
        'Sample_Date' => $requisition['Sample_Date']
      ];
    }
  }
}

// Generar PDF
class PDF extends FPDF {
  function Header() {
    $this->SetFont('Arial','B',14);
    $this->Cell(0,10,'Listado de Ensayos Pendientes',0,1,'C');
    $this->Ln(5);
  }

  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
  }
}

ob_clean(); // limpiar cualquier salida previa
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10, 8, '#', 1, 0, 'C');
$pdf->Cell(40, 8, 'Sample ID', 1, 0, 'C');
$pdf->Cell(40, 8, 'Sample Number', 1, 0, 'C');
$pdf->Cell(60, 8, 'Test Type', 1, 0, 'C');
$pdf->Cell(40, 8, 'Sample Date', 1, 1, 'C');

$pdf->SetFont('Arial','',9);
foreach ($pending_tests as $i => $row) {
  $pdf->Cell(10, 8, $i + 1, 1);
  $pdf->Cell(40, 8, $row['Sample_ID'], 1);
  $pdf->Cell(40, 8, $row['Sample_Number'], 1);
  $pdf->Cell(60, 8, $row['Test_Type'], 1);
  $pdf->Cell(40, 8, $row['Sample_Date'], 1);
  $pdf->Ln();
}

$pdf->Output("I", "Ensayos_Pendientes.pdf");
