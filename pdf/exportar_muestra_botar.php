<?php
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

/* ==============================================================
   CONFIG GLOBAL — columnas de bandejas
============================================================== */
$POSIBLES_BANDEJAS = [
  'Tare_Name',
  'Container','Container1','Container2','Container3','Container4','Container5','Container6',
  'LL_Container_1','LL_Container_2','LL_Container_3',
  'PL_Container_1','PL_Container_2','PL_Container_3',
  'TareMc','Tare_Name_MC_Before','Tare_Name_MC_After'
];

/* ==============================================================
   PDF CLASS
============================================================== */
class PDF extends FPDF {
  function Header() {
    $this->SetFont('Arial', 'B', 14);
    $this->Cell(0, 10, utf8_decode('Listado de Muestras a Botar'), 0, 1, 'C');
    $this->Ln(4);

    $this->SetFont('Arial', 'B', 10);
    $this->SetFillColor(220, 220, 220);

    $this->Cell(35, 8, 'Sample', 1, 0, 'C', true);
    $this->Cell(25, 8, '#', 1, 0, 'C', true);
    $this->Cell(30, 8, 'Material', 1, 0, 'C', true);
    $this->Cell(40, 8, 'Test', 1, 0, 'C', true);
    $this->Cell(30, 8, 'Bandeja', 1, 0, 'C', true);
    $this->Cell(35, 8, 'Origen', 1, 1, 'C', true);
  }

  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Pagina '.$this->PageNo(), 0, 0, 'C');
  }
}

/* ==============================================================
   RANGO DE FECHAS — últimos 7 días
============================================================== */
$fechaLimite = date('Y-m-d H:i:s', strtotime('-7 days'));

/* ==============================================================
   BUSCAR TODAS LAS TABLAS
============================================================== */
$result = $db->query("SHOW TABLES");
$tablas = [];
while ($row = $result->fetch_row()) {
  $tablas[] = $row[0];
}

$muestras = [];

/* ==============================================================
   RECORRER TABLA POR TABLA
============================================================== */
foreach ($tablas as $tabla) {

  // Detectar si tiene fecha
  $checkDate = $db->query("SHOW COLUMNS FROM `$tabla` LIKE 'Test_Start_Date'");
  if (!$checkDate || $checkDate->num_rows == 0) continue;

  // columnas mínimas que intentaremos leer
  $cols = [
    'id','Sample_ID','Sample_Number','test_type','Material_Type'
  ];

  // detectar columnas existentes
  $existingCols = [];
  foreach ($cols as $c) {
    $has = $db->query("SHOW COLUMNS FROM `$tabla` LIKE '$c'");
    if ($has && $has->num_rows > 0) $existingCols[] = $c;
  }

  // añadir columnas de bandejas si existen
  $bandejaCols = [];
  foreach ($POSIBLES_BANDEJAS as $c) {
    $has = $db->query("SHOW COLUMNS FROM `$tabla` LIKE '$c'");
    if ($has && $has->num_rows > 0) {
      $existingCols[] = $c;
      $bandejaCols[]  = $c;
    }
  }

  if (empty($bandejaCols)) continue; // si no tiene ninguna columna de bandeja, ignorar

  // construir select
  $select = implode(',', array_map(function($c){ return "`$c`"; }, $existingCols));

  $sql = "SELECT $select FROM `$tabla` WHERE Test_Start_Date >= '$fechaLimite'";
  $res = $db->query($sql);

  while ($row = $res->fetch_assoc()) {

    // detectar bandeja real
    $bandeja = 'N/A';
    foreach ($bandejaCols as $c) {
      if (!empty($row[$c])) {
        $bandeja = $row[$c];
        break;
      }
    }

    $muestras[] = [
      'Sample_ID'     => $row['Sample_ID']     ?? '-',
      'Sample_Number' => $row['Sample_Number'] ?? '-',
      'Material_Type' => $row['Material_Type'] ?? 'N/D',
      'test_type'     => $row['test_type']     ?? '-',
      'Bandeja'       => $bandeja,
      'Origen'        => $tabla,
    ];
  }
}

/* ==============================================================
   GENERAR PDF
============================================================== */
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

foreach ($muestras as $m) {

  $pdf->Cell(35, 7, utf8_decode($m['Sample_ID']), 1);
  $pdf->Cell(25, 7, utf8_decode($m['Sample_Number']), 1);
  $pdf->Cell(30, 7, utf8_decode($m['Material_Type']), 1);
  $pdf->Cell(40, 7, utf8_decode($m['test_type']), 1);
  $pdf->Cell(30, 7, utf8_decode($m['Bandeja']), 1);
  $pdf->Cell(35, 7, utf8_decode($m['Origen']), 1);
  $pdf->Ln();
}

$pdf->Output('I', 'Muestras_a_Botar.pdf');
exit;
