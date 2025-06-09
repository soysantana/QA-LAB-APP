<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

if (!isset($_GET['fecha'])) {
  die('Fecha no especificada.');
}

$fecha = $_GET['fecha'];
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('F d, Y') : 'Invalid Date';

// --------- CONSULTAS ---------
$requisitioned = (int) find_by_sql("SELECT COUNT(*) as total FROM lab_test_requisition_form WHERE Registed_Date = '{$fecha}'")[0]['total'];
$preparation = (int) find_by_sql("SELECT COUNT(*) as total FROM test_preparation WHERE DATE(Register_Date) = '{$fecha}'")[0]['total'];
$realization = (int) find_by_sql("SELECT COUNT(*) as total FROM test_realization WHERE DATE(Register_Date) = '{$fecha}'")[0]['total'];
$delivery = (int) find_by_sql("SELECT COUNT(*) as total FROM test_delivery WHERE DATE(Register_Date) = '{$fecha}'")[0]['total'];
$reviewed = (int) find_by_sql("SELECT COUNT(*) as total FROM test_reviewed WHERE DATE(Start_Date) = '{$fecha}'")[0]['total'];

// --------- CLASE PDF ---------
class PDF extends FPDF {
  public $fecha_en;

  function Header() {
    // Logo
    if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
      $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 30);
    } else {
      $this->SetFillColor(200, 200, 200);
      $this->Rect(10, 10, 30, 20, 'F');
    }

    // Título a la derecha
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

// --------- GENERAR PDF ---------
$pdf = new PDF();
$pdf->fecha_en = $fecha_en;
$pdf->AddPage();

// Subtítulo
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Summary of Activities', 0, 1);

// Tabla
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

// Salida del PDF
$pdf->Output("I", "Daily_Report_{$fecha}.pdf");
