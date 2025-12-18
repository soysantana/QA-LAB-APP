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
  $d1 = strtotime($date);
  if(!$d1) return 0;
  return (time() - $d1) / 86400;
}

/* ============================
   PARÁMETROS
============================ */
$db = $GLOBALS['db'];

$type_raw  = $_GET['type']  ?? '';
$stage_raw = $_GET['stage'] ?? '';

$type  = N($type_raw);
$stage = N($stage_raw);

$from = $_GET['from'] ?? date('Y-m-d', strtotime('-90 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-90 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

$fromEsc = $db->escape($from);
$toEsc   = $db->escape($to);

/* ============================
   CARGA BASE (MISMO RANGO QUE LA VISTA)
============================ */
$req = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
  FROM lab_test_requisition_form
  WHERE Registed_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($req)) $req = [];

/* ============================
   ESTADOS (SIN FILTRO POR FECHA)
   OJO: si filtras por fecha aquí, vuelves a dañar el SIN
============================ */
$prep = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_preparation WHERE Start_Date IS NOT NULL");
$real = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_realization WHERE Start_Date IS NOT NULL");
$ent  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_delivery WHERE Start_Date IS NOT NULL");
$rev  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_review WHERE Start_Date IS NOT NULL");
$rep  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_repeat WHERE Start_Date IS NOT NULL");
$rev2 = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_reviewed WHERE Start_Date IS NOT NULL");

if (!is_array($prep)) $prep = [];
if (!is_array($real)) $real = [];
if (!is_array($ent))  $ent  = [];
if (!is_array($rev))  $rev  = [];
if (!is_array($rep))  $rep  = [];
if (!is_array($rev2)) $rev2 = [];

/* ============================
   INDEX ESTADO
============================ */
$index = [];

foreach ($prep as $r){
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage' => 'PREP', 'SD' => $r['Start_Date']];
}

foreach ($real as $r){
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage' => 'REAL', 'SD' => $r['Start_Date']];
}

foreach ($ent as $r){
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage' => 'ENT', 'SD' => $r['Start_Date']];
}

foreach ($rev as $r){
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage' => 'REV', 'SD' => $r['Start_Date']];
}

foreach ($rep as $r){
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage' => 'REP', 'SD' => $r['Start_Date']];
}

foreach ($rev2 as $r){
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage' => 'REV', 'SD' => $r['Start_Date']];
}

/* ============================
   ARMAR SALIDA
============================ */
$out = [];

foreach ($req as $row){

  if (empty($row['Test_Type'])) continue;

  $tests = array_map('trim', explode(',', $row['Test_Type']));

  foreach ($tests as $t){

    $T = N($t);
    if ($T === '') continue;

    // Si viene type, filtra por ese; si no viene, imprime todos
    if ($type !== '' && $T !== $type) continue;

    $key = N($row['Sample_ID'])."|".N($row['Sample_Number'])."|".$T;
    $stData = $index[$key] ?? null;

    $ST  = $stData['stage'] ?? 'SIN';
    $SD  = $stData['SD'] ?? $row['Registed_Date'];

    // ESTANCADOS
    if ($ST === 'PREP' && daysSince($SD) >= 3) $ST = 'PREP_EST';
    if ($ST === 'REAL' && daysSince($SD) >= 4) $ST = 'REAL_EST';

    // FILTRO POR STAGE
    if ($stage !== '' && $stage !== $ST) continue;

    $out[] = [
      'sid'   => $row['Sample_ID'],
      'num'   => $row['Sample_Number'],
      'type'  => ($type !== '' ? $type : $T),
      'reg'   => $row['Registed_Date'],
      'sd'    => $SD,
      'stage' => $ST
    ];
  }
}

/* ORDENAR */
usort($out, function($a,$b){
  return strcmp($a['reg'] ?? '', $b['reg'] ?? '');
});

/* ============================
   PDF
============================ */
class PDF extends Fpdi {}
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',13);

$header = "PENDIENTES";
if ($type !== '') $header .= "  $type";
if ($stage !== '') $header .= " - STAGE: $stage";
$header .= "  ($from a $to)";

$pdf->Cell(190,8,$header,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('Arial','B',9);
$pdf->Cell(48,7,'Sample ID',1);
$pdf->Cell(18,7,'Num',1);
$pdf->Cell(22,7,'Type',1);
$pdf->Cell(28,7,'Req. Date',1);
$pdf->Cell(28,7,'Stage Date',1);
$pdf->Cell(30,7,'Stage',1);
$pdf->Ln();

$pdf->SetFont('Arial','',9);

foreach ($out as $p){
  $pdf->Cell(48,7,$p['sid'],1);
  $pdf->Cell(18,7,$p['num'],1);
  $pdf->Cell(22,7,$p['type'],1);
  $pdf->Cell(28,7,substr($p['reg'],0,10),1);
  $pdf->Cell(28,7,substr($p['sd'],0,10),1);
  $pdf->Cell(30,7,$p['stage'],1);
  $pdf->Ln();
}

ob_end_clean();
$pdf->Output();
