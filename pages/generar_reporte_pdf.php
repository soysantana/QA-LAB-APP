<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

if (!isset($_GET['fecha'])) {
  die('Fecha no especificada.');
}

$fecha = $_GET['fecha'];
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('F d, Y') : 'Invalid Date';

class PDF extends FPDF {
  public $logo_path = '../assets/img/Pueblo-Viejo.jpg'; // cambia esto si tu logo está en otro lugar

  function Header() {
    // Logo en la esquina superior izquierda
    if (file_exists($this->logo_path)) { 
      $this->Image($this->logo_path, 10, 10, 30); // ancho 30 mm
    }

    // Título a la derecha
    $this->SetFont('Arial', 'B', 14);
    $this->SetXY(150, 10);
    $this->Cell(50, 10, 'Daily Laboratory Report', 0, 1, 'R');

    global $fecha_en;
    $this->SetFont('Arial', '', 12);
    $this->SetXY(150, 20);
    $this->Cell(50, 10, "Date: $fecha_en", 0, 1, 'R');

    $this->Ln(15);
  }

  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
  }
}

// Crear PDF
$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// (Aquí luego puedes hacer un query con datos de la fecha y llenar el PDF)

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'This is a sample template. Data loading coming soon...', 0, 1);

// Output PDF
$pdf->Output("I", "Daily_Report_$fecha.pdf");
