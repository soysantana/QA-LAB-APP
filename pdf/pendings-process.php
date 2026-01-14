<?php
ob_start();

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');
use setasign\Fpdi\Fpdi;

/* ============================
   UTIL
============================ */
function N($v){ return strtoupper(trim((string)$v)); }

function daysSince($date){
  if(!$date) return 0;
  $t = strtotime($date);
  if(!$t) return 0;
  return (time() - $t) / 86400;
}

/* ============================
   PARÁMETROS
   - type: GS, MC, AL, etc
   - stage: SIN / PREP / PREP_EST / REAL / REAL_EST / ENT / REV / REP
   - from/to: YYYY-MM-DD (igual que tu página)
============================ */
$type_raw  = $_GET['type']  ?? '';
$stage_raw = $_GET['stage'] ?? '';

$type  = N($type_raw);
$stage = N($stage_raw);

$from = $_GET['from'] ?? date('Y-m-d', strtotime('-90 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-90 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

$db = $GLOBALS['db'];
$fromEsc = $db->escape($from);
$toEsc   = $db->escape($to);

/* ============================
   CARGA BASE (MISMA que página)
============================ */
$req = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
  FROM lab_test_requisition_form
  WHERE Registed_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($req)) $req = [];

/* ============================
   INDEX DE ESTADO (MISMA que página)
============================ */
$index = [];

/* PREPARATION */
$prep = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
  FROM test_preparation
  WHERE Start_Date IS NOT NULL
    AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($prep)) $prep = [];
foreach ($prep as $r) {
  $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
    'stage' => 'PREP',
    'SD'    => $r['Start_Date'] ?? null
  ];
}

/* REALIZATION */
$real = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
  FROM test_realization
  WHERE Start_Date IS NOT NULL
    AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($real)) $real = [];
foreach ($real as $r) {
  $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
    'stage' => 'REAL',
    'SD'    => $r['Start_Date'] ?? null
  ];
}

/* DELIVERY */
$ent = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
  FROM test_delivery
  WHERE Start_Date IS NOT NULL
    AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($ent)) $ent = [];
foreach ($ent as $r) {
  $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
    'stage' => 'ENT',
    'SD'    => $r['Start_Date'] ?? null
  ];
}

/* REVIEW */
$rev = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
  FROM test_review
  WHERE Start_Date IS NOT NULL
    AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($rev)) $rev = [];
foreach ($rev as $r) {
  $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
    'stage' => 'REV',
    'SD'    => $r['Start_Date'] ?? null
  ];
}

/* REPEAT */
$rep = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
  FROM test_repeat
  WHERE Start_Date IS NOT NULL
    AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($rep)) $rep = [];
foreach ($rep as $r) {
  $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
    'stage' => 'REP',
    'SD'    => $r['Start_Date'] ?? null
  ];
}

/* REVIEWED */
$rev2 = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
  FROM test_reviewed
  WHERE Start_Date IS NOT NULL
    AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($rev2)) $rev2 = [];
foreach ($rev2 as $r) {
  $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
    'stage' => 'REV',
    'SD'    => $r['Start_Date'] ?? null
  ];
}

/* ============================
   ARMAR SALIDA (MISMA lógica)
============================ */
$out = [];

foreach ($req as $row) {

  if (empty($row['Test_Type'])) continue;

  $tests = array_map('trim', explode(',', (string)$row['Test_Type']));

  foreach ($tests as $t) {

    $T = N($t);
    if ($T === '') continue;

    // Si viene type, filtra por ese ensayo (igual que tu PDF actual)
    if ($type !== '' && $T !== $type) continue;

    $key = N($row['Sample_ID'] ?? '')."|".N($row['Sample_Number'] ?? '')."|".$T;
    $stData = $index[$key] ?? null;

    $ST = $stData['stage'] ?? 'SIN';
    $SD = $stData['SD'] ?? ($row['Registed_Date'] ?? null);

    // Estancados: igual que página
    if ($ST === 'PREP' && daysSince($SD) >= 3) $ST = 'PREP_EST';
    if ($ST === 'REAL' && daysSince($SD) >= 4) $ST = 'REAL_EST';

    // Si viene stage, filtra por stage
    if ($stage !== '' && $stage !== $ST) continue;

    $out[] = [
      'sid'   => $row['Sample_ID'] ?? '',
      'num'   => $row['Sample_Number'] ?? '',
      'type'  => $T,
      'reg'   => $row['Registed_Date'] ?? '',
      'sd'    => $SD ?? '',
      'stage' => $ST
    ];
  }
}

/* Orden */
usort($out, function($a,$b){
  return strcmp((string)($a['reg'] ?? ''), (string)($b['reg'] ?? ''));
});

/* ============================
   PDF
============================ */
class PDF extends Fpdi {}
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',13);

$header = "PENDIENTES";
if ($type !== '')  $header .= "  $type";
if ($stage !== '') $header .= "  - STAGE: $stage";
$header .= "  ($from a $to)";

$pdf->Cell(190,9,$header,0,1,'C');
$pdf->Ln(1);

$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,7,'Sample ID',1);
$pdf->Cell(20,7,'Number',1);
$pdf->Cell(15,7,'Type',1);
$pdf->Cell(35,7,'Req. Date',1);
$pdf->Cell(35,7,'Stage Date',1);
$pdf->Cell(40,7,'Stage',1);
$pdf->Ln();

$pdf->SetFont('Arial','',9);

foreach ($out as $p){
  $pdf->Cell(45,7,(string)$p['sid'],1);
  $pdf->Cell(20,7,(string)$p['num'],1);
  $pdf->Cell(15,7,(string)$p['type'],1);
  $pdf->Cell(35,7,(string)$p['reg'],1);
  $pdf->Cell(35,7,(string)$p['sd'],1);
  $pdf->Cell(40,7,(string)$p['stage'],1);
  $pdf->Ln();
}

ob_end_clean();
$pdf->Output();
