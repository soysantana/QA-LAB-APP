<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

if (!isset($_GET['fecha'])) {
  die('Fecha no especificada.');
}

$fecha = $_GET['fecha'];
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('F d, Y') : 'Invalid Date';

// Obtener conteos por etapa
function contar_por_fecha($tabla, $campo_fecha, $fecha) {
  global $db;
  $sql = "SELECT COUNT(*) AS total FROM $tabla WHERE DATE($campo_fecha) = '{$db->escape($fecha)}'";
  $res = $db->query($sql);
  $data = $res->fetch_assoc();
  return (int)$data['total'];
}

$requisitioned = contar_por_fecha('lab_test_requisition_form', 'Registed_Date', $fecha);
$preparation   = contar_por_fecha('test_preparation', 'Register_Date', $fecha);
$realization   = contar_por_fecha('test_realization', 'Register_Date', $fecha);
$delivery      = contar_por_fecha('test_delivery', 'Register_Date', $fecha);
$review        = contar_por_fecha('test_review', 'Start_Date', $fecha);

// PDF
class PDF extends FPDF {
  public $logo_path = '../assets/img/Pueblo-Viejo.jpg';
  public $fecha_en;

  function Header() {
    if (file_exists($this->logo_path)) {
      $this->Image($this->logo_path, 10, 10, 30);
    }
    $this->SetFont('Arial', 'B', 14);
    $this->SetXY(150, 10);
    $this->Cell(50, 10, 'Daily Laboratory Report', 0, 1, 'R');

    $this->SetFont('Arial', '', 12);
    $this->SetXY(150, 20);
    $this->Cell(50, 10, "Date: {$this->fecha_en}", 0, 1, 'R');
    $this->Ln(15);
  }

  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
  }
}

$pdf = new PDF();
$pdf->fecha_en = $fecha_en;
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// Subtítulo
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Summary of Activities', 0, 1, 'L');
$pdf->Ln(4);

// Tabla de resumen
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(90, 8, 'Test Stage', 1, 0, 'C');
$pdf->Cell(30, 8, 'Quantity', 1, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(90, 8, 'Requisitioned', 1);
$pdf->Cell(30, 8, $requisitioned, 1, 1);

$pdf->Cell(90, 8, 'In Preparation', 1);
$pdf->Cell(30, 8, $preparation, 1, 1);

$pdf->Cell(90, 8, 'In Realization', 1);
$pdf->Cell(30, 8, $realization, 1, 1);

$pdf->Cell(90, 8, 'Delivered', 1);
$pdf->Cell(30, 8, $delivery, 1, 1);

$pdf->Cell(90, 8, 'Reviewed', 1);
$pdf->Cell(30, 8, $review, 1, 1);

// Finalizar PDF
$pdf->Ln(5);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'This summary includes tests registered on ' . $fecha_en . '.', 0, 1);

$pdf->Output("I", "Daily_Report_$fecha.pdf");
