<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

// Evita errores HTML que interfieran con el PDF
ob_clean();

function normalize($v) {
  return strtoupper(trim((string)$v));
}

$requisitions = find_all("lab_test_requisition_form");
$tables_to_check = [
  'test_preparation',
  'test_delivery',
  'test_realization',
  'test_repeat',
  'test_review',
  'test_reviewed'
];

// Recolectar las combinaciones ya procesadas
$indexed_status = [];
foreach ($tables_to_check as $table) {
  $data = find_all($table);
  foreach ($data as $row) {
    if (!isset($row['Sample_Name'], $row['Sample_Number'], $row['Test_Type'])) continue;
    $key = normalize($row['Sample_Name']) . "|" . normalize($row['Sample_Number']) . "|" . normalize($row['Test_Type']);
    $indexed_status[$key] = true;
  }
}

// Buscar las pendientes
$pending_tests = [];
foreach ($requisitions as $req) {
  for ($i = 1; $i <= 20; $i++) {
    $testKey = "Test_Type{$i}";
    if (empty($req[$testKey])) continue;

    $sample_name = normalize($req['Sample_ID']); // Usamos Sample_ID como Sample_Name
    $sample_number = normalize($req['Sample_Number']);
    $test_type = normalize($req[$testKey]);
    $key = "{$sample_name}|{$sample_number}|{$test_type}";

    if (!isset($indexed_status[$key])) {
      $pending_tests[] = [
        'Sample_ID' => $req['Sample_ID'],
        'Sample_Number' => $req['Sample_Number'],
        'Test_Type' => $req[$testKey],
        'Sample_Date' => $req['Sample_Date']
      ];
    }
  }
}

// Generar PDF
class PDF extends FPDF {
  function Header() {
    $this->SetFont('Arial','B',12);
    $this->Cell(0,10,'Listado de Ensayos Pendientes',0,1,'C');
    $this->Ln(5);
  }
  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
  }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(40, 8, 'Sample ID', 1);
$pdf->Cell(40, 8, 'Sample Number', 1);
$pdf->Cell(60, 8, 'Test Type', 1);
$pdf->Cell(40, 8, 'Sample Date', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
foreach ($pending_tests as $i => $row) {
  $pdf->Cell(10, 8, $i + 1, 1);
  $pdf->Cell(40, 8, $row['Sample_ID'], 1);
  $pdf->Cell(40, 8, $row['Sample_Number'], 1);
  $pdf->Cell(60, 8, $row['Test_Type'], 1);
  $pdf->Cell(40, 8, $row['Sample_Date'], 1);
  $pdf->Ln();
}

$pdf->Output("I", "Lista_Ensayos_Pendientes.pdf");
?>
