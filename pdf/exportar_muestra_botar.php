<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

class PDF extends FPDF {
  function Header() {
    $this->SetFont('Arial', 'B', 14);
    $this->Cell(0, 10, 'Listado de Muestras a Botar', 0, 1, 'C');
    $this->Ln(5);
    $this->SetFont('Arial', 'B', 10);
    $this->SetFillColor(200, 200, 200);
    $this->Cell(40, 8, 'Muestra', 1, 0, 'C', true);
    $this->Cell(30, 8, 'Numero', 1, 0, 'C', true);
    $this->Cell(40, 8, 'Material', 1, 0, 'C', true);
    $this->Cell(40, 8, 'Ensayo', 1, 0, 'C', true);
    $this->Cell(40, 8, 'Bandeja', 1, 1, 'C', true);
  }

  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
  }
}

$fechaLimite = date('Y-m-d H:i:s', strtotime('-7 days'));
$muestras = [];

$result = $db->query("SHOW TABLES");
$tablas = [];
while ($row = $result->fetch_row()) {
  $tablas[] = $row[0];
}

foreach ($tablas as $tabla) {
  $columns = "id, Sample_ID, Sample_Number, test_type";

  $checkTare = $db->query("SHOW COLUMNS FROM `$tabla` LIKE 'Tare_Name'");
  if ($checkTare && $checkTare->num_rows > 0) $columns .= ", Tare_Name";

  $checkMat = $db->query("SHOW COLUMNS FROM `$tabla` LIKE 'Material_Type'");
  if ($checkMat && $checkMat->num_rows > 0) $columns .= ", Material_Type";

  $checkDate = $db->query("SHOW COLUMNS FROM `$tabla` LIKE 'Test_Start_Date'");
  if ($checkDate && $checkDate->num_rows > 0) {
    $query = "SELECT $columns FROM `$tabla` WHERE Test_Start_Date >= '$fechaLimite'";
    $res = $db->query($query);
    while ($row = $res->fetch_assoc()) {
      $muestras[] = $row;
    }
  }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

foreach ($muestras as $m) {
  $pdf->Cell(40, 8, $m['Sample_ID'] ?? '-', 1);
  $pdf->Cell(30, 8, $m['Sample_Number'] ?? '-', 1);
  $pdf->Cell(40, 8, $m['Material_Type'] ?? 'N/A', 1);
  $pdf->Cell(40, 8, $m['test_type'] ?? '-', 1);
  $pdf->Cell(40, 8, $m['Tare_Name'] ?? 'N/A', 1);
  $pdf->Ln();
}

$pdf->Output('I', 'Muestras_a_Botar.pdf');
exit;
