<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

function normalize($v) {
  return strtoupper(trim((string)($v ?? '')));
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

$indexed_status = [];
foreach ($tables_to_check as $table) {
  $data = find_all($table);
  foreach ($data as $row) {
    if (!isset($row['Sample_Name'], $row['Sample_Number'], $row['Test_Type'])) continue;
    $key = normalize($row['Sample_Name']) . "|" . normalize($row['Sample_Number']) . "|" . normalize($row['Test_Type']);
    $indexed_status[$key] = true;
  }
}

$pending_tests = [];
foreach ($requisitions as $req) {
  for ($i = 1; $i <= 20; $i++) {
    $testKey = "Test_Type" . $i;
    if (empty($req[$testKey])) continue;

    $sample_name = normalize($req['Sample_ID']); // usamos Sample_ID como nombre
    $sample_num = normalize($req['Sample_Number']);
    $test_type  = normalize($req[$testKey]);
    $date       = $req['Sample_Date'];
    $key        = $sample_name . "|" . $sample_num . "|" . $test_type;

    if (!isset($indexed_status[$key])) {
      $pending_tests[] = [
        'Sample_Name' => $req['Sample_ID'],
        'Sample_Number' => $req['Sample_Number'],
        'Test_Type' => $req[$testKey],
        'Sample_Date' => $date
      ];
    }
  }
}

class PDF extends FPDF {
  function Header() {
    $this->SetFont('Arial', 'B', 14);
    $this->Cell(0, 10, 'Pending Tests Report', 0, 1, 'C');
    $this->Ln(5);
  }

  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
  }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, '#', 1, 0, 'C');
$pdf->Cell(40, 8, 'Sample Name', 1, 0, 'C');
$pdf->Cell(40, 8, 'Sample Number', 1, 0, 'C');
$pdf->Cell(60, 8, 'Test Type', 1, 0, 'C');
$pdf->Cell(40, 8, 'Sample Date', 1, 1, 'C');

$pdf->SetFont('Arial', '', 9);
foreach ($pending_tests as $i => $row) {
  $pdf->Cell(10, 8, $i + 1, 1);
  $pdf->Cell(40, 8, $row['Sample_Name'], 1);
  $pdf->Cell(40, 8, $row['Sample_Number'], 1);
  $pdf->Cell(60, 8, $row['Test_Type'], 1);
  $pdf->Cell(40, 8, $row['Sample_Date'], 1);
  $pdf->Ln();
}

$pdf->Output("I", "pending_tests_report.pdf");
?>
