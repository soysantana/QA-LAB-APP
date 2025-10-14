<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

// ========= Parámetro =========
$type = isset($_GET['type']) ? strtoupper(trim($_GET['type'])) : '';

// ========= Utilitarios =========
function normalize($v){ return strtoupper(trim((string)$v)); }

function explode_tests_normalized($testStr){
  if(!$testStr) return [];
  // separa por comas, limpia espacios y vacíos
  $parts = array_map('trim', explode(',', $testStr));
  $parts = array_filter($parts, fn($s)=>$s!=='');
  return array_map(fn($s)=>normalize($s), $parts);
}

// ========= Carga de datos =========
$data = [
  "Requisition" => find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Sample_Date FROM lab_test_requisition_form"),
  // Usa las mismas tablas que la vista web:
  "Preparation" => find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_preparation"),
  "Delivery"    => find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_delivery"),
  "Realization" => find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_realization"),
  "Repeat"      => find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_repeat"),
  "Review"      => find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_review"),
  "Reviewed"    => find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_reviewed"),
];

// ========= Índice de “ya realizado/en curso” =========
$indexedStatus = [];
$follow_tables = ["Preparation","Delivery","Realization","Repeat","Review","Reviewed"];

foreach ($follow_tables as $category) {
  foreach ($data[$category] as $entry) {
    $k = normalize($entry["Sample_ID"])
        ."|".normalize($entry["Sample_Number"])
        ."|".normalize($entry["Test_Type"]);
    $indexedStatus[$k] = true;
  }
}

// ========= Construcción de pendientes SOLO del tipo solicitado =========
$testTypes = [];
$seen = [];
$type_norm = $type; // ya viene en upper+trim desde arriba

foreach ($data["Requisition"] as $r) {
  if (empty($r["Test_Type"])) continue;

  $sampleID     = normalize($r["Sample_ID"]);
  $sampleNumber = normalize($r["Sample_Number"]);
  $date         = $r["Sample_Date"];

  // tokeniza igual que en la vista
  $tokens = explode_tests_normalized($r["Test_Type"]);

  // filtra por el tipo solicitado, coincidencia EXACTA
  if (!in_array($type_norm, $tokens, true)) continue;

  $key = $sampleID."|".$sampleNumber."|".$type_norm;

  // pendiente = no está en índice y no repetido
  if (!isset($indexedStatus[$key]) && !isset($seen[$key])) {
    $testTypes[] = [
      "Sample_ID"     => $r["Sample_ID"],
      "Sample_Number" => $r["Sample_Number"],
      "Sample_Date"   => $date,
      "Test_Type"     => $type_norm,
    ];
    $seen[$key] = true;
  }
}

// ========= PDF =========
class PDF extends Fpdi {
  function Header() {}
  function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', array(8.5 * 25.4, 11 * 25.4));

// Importar template
$pdf->setSourceFile('template/Pendings-List.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Centro de página
$centerX = $pdf->GetPageWidth() / 2;

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetXY($centerX - 50, 40);
$pdf->Cell(100, 10, 'Prioridad de ' . $type_norm, 0, 1, 'C');

// Tabla
$tableWidth = 180;
$tableX = $centerX - ($tableWidth / 2);
$tableY = 50;
$pdf->SetXY($tableX, $tableY);

// Encabezados
$pdf->SetFillColor(200, 220, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(45, 10, 'Sample Date', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Sample ID',   1, 0, 'C', true);
$pdf->Cell(45, 10, 'Test Type',    1, 0, 'C', true);
$pdf->Cell(45, 10, 'Metodo',       1, 1, 'C', true);

// Cuerpo
$pdf->SetFont('Arial', '', 10);

// Escapes
$db = $GLOBALS['db']; // según tu framework

foreach ($testTypes as $sample) {
  $pdf->SetX($tableX);

  $sampleDate = $sample['Sample_Date'];
  $sid        = $sample['Sample_ID'];
  $snum       = $sample['Sample_Number'];
  $testType   = $sample['Test_Type'];

  if ($testType === 'SP') {
    // Seguridad: escape para la consulta
    $sidEsc  = $db->escape($sid);
    $snumEsc = $db->escape($snum);

    $GrainResults = find_by_sql("
      SELECT CumRet11, CumRet13, CumRet14
      FROM grain_size_general
      WHERE Sample_ID = '{$sidEsc}' AND Sample_Number = '{$snumEsc}'
      LIMIT 1
    ");

    if (!empty($GrainResults)) {
      $G = $GrainResults[0];
      $T3p4 = (float)$G['CumRet11']; // 3/4"
      $T3p8 = (float)$G['CumRet13']; // 3/8"
      $TNo4 = (float)$G['CumRet14']; // No.4
      $resultado = '';

      if ($T3p4 > 0) {
        $resultado = "C";
      } elseif ($T3p8 > 0 && $T3p4 == 0) {
        $resultado = "B";
      } elseif ($TNo4 > 0 && $T3p4 == 0 && $T3p8 == 0) {
        $resultado = "A";
      } else {
        $resultado = "No data";
      }

      $pdf->Cell(45, 10, $sampleDate,            1, 0, 'C');
      $pdf->Cell(45, 10, $sid . '-' . $snum,     1, 0, 'C');
      $pdf->Cell(45, 10, $testType,              1, 0, 'C');
      $pdf->Cell(45, 10, $resultado,             1, 1, 'C');

    } else {
      $pdf->Cell(45, 10, $sampleDate,            1, 0, 'C');
      $pdf->Cell(45, 10, $sid . '-' . $snum,     1, 0, 'C');
      $pdf->Cell(45, 10, $testType,              1, 0, 'C');
      $pdf->Cell(45, 10, 'No data',              1, 1, 'C');
    }

  } else {
    $pdf->Cell(45, 10, $sampleDate,              1, 0, 'C');
    $pdf->Cell(45, 10, $sid . '-' . $snum,       1, 0, 'C');
    $pdf->Cell(45, 10, $testType,                1, 0, 'C');
    $pdf->Cell(45, 10, '',                       1, 1, 'C');
  }
}

$pdf->Output();
