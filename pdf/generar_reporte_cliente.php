<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

$anio = $_GET['anio'] ?? date('Y');
$trimestre = $_GET['trimestre'] ?? 'Q1';

switch ($trimestre) {
  case 'Q1': $mes_inicio = 1; $mes_fin = 3; break;
  case 'Q2': $mes_inicio = 4; $mes_fin = 6; break;
  case 'Q3': $mes_inicio = 7; $mes_fin = 9; break;
  case 'Q4': $mes_inicio = 10; $mes_fin = 12; break;
  default: $mes_inicio = 1; $mes_fin = 3;
}

$inicio = "{$anio}-" . str_pad($mes_inicio, 2, "0", STR_PAD_LEFT) . "-01";
$fin = "{$anio}-" . str_pad($mes_fin, 2, "0", STR_PAD_LEFT) . "-31";

// Obtener solicitudes
$solicitudes = find_by_sql("
  SELECT Client, Sample_ID, Sample_Number, Test_Type
  FROM lab_test_requisition_form
  WHERE Sample_Date BETWEEN '{$inicio}' AND '{$fin}'
");

// Obtener entregas desde `ensayos_reporte`
$entregas = find_by_sql("SELECT Sample_Name, Sample_Number, Test_Type FROM test_delivery");

// Organizar entregas
$entregado_map = [];
foreach ($entregas as $e) {
  $key = $e['Sample_Name'] . '|' . $e['Sample_Number'] . '|' . $e['Test_Type'];
  $entregado_map[$key] = true;
}

// Procesar progreso por cliente
$stats = [];

foreach ($solicitudes as $s) {
  $cliente = $s['Client'] ?: 'SIN CLIENTE';
  $key = $s['Sample_ID'] . '|' . $s['Sample_Number'] . '|' . $s['Test_Type'];

  if (!isset($stats[$cliente])) {
    $stats[$cliente] = ['solicitados' => 0, 'entregados' => 0];
  }

  $stats[$cliente]['solicitados']++;
  if (isset($entregado_map[$key])) {
    $stats[$cliente]['entregados']++;
  }
}

// Crear PDF
class PDF extends FPDF {
  function Header() {
    $this->SetFont('Arial','B',12);
    $this->Cell(0,10,'Reporte de Ensayos por Cliente',0,1,'C');
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
$pdf->SetFont('Arial','B',10);

$pdf->Cell(60,10,'Client',1,0,'C');
$pdf->Cell(30,10,'Requisioned',1,0,'C');
$pdf->Cell(30,10,'Completed',1,0,'C');
$pdf->Cell(70,10,'Status',1,1,'C');

$pdf->SetFont('Arial','',10);

foreach ($stats as $cliente => $data) {
  $porcentaje = ($data['solicitados'] > 0) ? round($data['entregados'] / $data['solicitados'] * 100) : 0;
  $pdf->Cell(60,8,$cliente,1);
  $pdf->Cell(30,8,$data['solicitados'],1,0,'C');
  $pdf->Cell(30,8,$data['entregados'],1,0,'C');

  // Barra de progreso textual
  $barra = str_repeat('|', floor($porcentaje / 5));
  $pdf->Cell(70,8,"{$porcentaje}% {$barra}",1,1);
}

$pdf->Output('I', 'Laboratory_Clients_Report.pdf');
?>
