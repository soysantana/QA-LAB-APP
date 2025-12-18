<?php
ob_start();

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');
use setasign\Fpdi\Fpdi;

/* ============================
   NORMALIZADORES (CLAVE)
============================ */
function N($v){
  $s = (string)$v;
  $s = str_replace("\xC2\xA0", " ", $s);
  $s = trim($s);
  $s = preg_replace('/\s+/', ' ', $s);
  return strtoupper($s);
}
function normNum($v){
  $s = N($v);
  if ($s !== '' && ctype_digit($s)) return (string)intval($s); // 0078 -> 78
  return $s;
}
function normTest($v){
  $s = N($v);
  return preg_replace('/[\s\-\_\.\/]+/', '', $s);
}
function daysSince($date){
  if(!$date) return 0;
  $d1 = strtotime($date);
  if(!$d1) return 0;
  return (time() - $d1) / 86400;
}

/* ============================
   PARÁMETROS
============================ */
$type_raw  = $_GET['type']  ?? '';
$stage_raw = $_GET['stage'] ?? '';

$type  = normTest($type_raw);
$stage = N($stage_raw);

$from = $_GET['from'] ?? '';
$to   = $_GET['to']   ?? '';

$db = $GLOBALS['db'];

/* validar fechas si vienen */
$useRange = false;
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
  $useRange = true;
  $fromEsc = $db->escape($from);
  $toEsc   = $db->escape($to);
}

/* ============================
   CARGA BASE (REQUISICIÓN)
   - Si viene from/to => filtra igual que la vista
============================ */
if ($useRange) {
  $req = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'
  ");
} else {
  $req = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
    FROM lab_test_requisition_form
  ");
}
if (!is_array($req)) $req = [];

/* ============================
   ESTADOS (SIN FILTRO FECHA)
============================ */
$prep = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_preparation WHERE Start_Date IS NOT NULL");
$real = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_realization WHERE Start_Date IS NOT NULL");
$ent  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_delivery   WHERE Start_Date IS NOT NULL");
$rev  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_review     WHERE Start_Date IS NOT NULL");
$rep  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_repeat     WHERE Start_Date IS NOT NULL");
$rev2 = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_reviewed  WHERE Start_Date IS NOT NULL");

if (!is_array($prep)) $prep = [];
if (!is_array($real)) $real = [];
if (!is_array($ent))  $ent  = [];
if (!is_array($rev))  $rev  = [];
if (!is_array($rep))  $rep  = [];
if (!is_array($rev2)) $rev2 = [];

/* ============================
   INDEX ESTADO (split por coma)
============================ */
$index = [];

$addIndex = function(array $r, string $stage) use (&$index) {
  $sid = N($r['Sample_ID'] ?? '');
  $num = normNum($r['Sample_Number'] ?? '');
  $sd  = $r['Start_Date'] ?? null;

  $tests = array_map('trim', explode(',', (string)($r['Test_Type'] ?? '')));
  foreach ($tests as $tt){
    $T = normTest($tt);
    if ($T === '') continue;
    $index[$sid."|".$num."|".$T] = ['stage'=>$stage, 'SD'=>$sd];
  }
};

foreach ($prep as $r) $addIndex($r, 'PREP');
foreach ($real as $r) $addIndex($r, 'REAL');
foreach ($ent  as $r) $addIndex($r, 'ENT');
foreach ($rev  as $r) $addIndex($r, 'REV');
foreach ($rep  as $r) $addIndex($r, 'REP');
foreach ($rev2 as $r) $addIndex($r, 'REV');

/* ============================
   ARMAR SALIDA
============================ */
$out = [];

foreach ($req as $row){

  if (empty($row['Test_Type'])) continue;

  $sid = N($row['Sample_ID'] ?? '');
  $num = normNum($row['Sample_Number'] ?? '');
  $reg = $row['Registed_Date'] ?? '';

  $tests = array_map('trim', explode(',', (string)$row['Test_Type']));

  foreach ($tests as $t){
    $T = normTest($t);
    if ($T === '') continue;

    // filtro por type requerido
    if ($type !== '' && $T !== $type) continue;

    $key = $sid."|".$num."|".$T;
    $stData = $index[$key] ?? null;

    $ST = $stData['stage'] ?? 'SIN';
    $SD = $stData['SD'] ?? $reg;

    // estancados
    if ($ST === 'PREP' && daysSince($SD) >= 3) $ST = 'PREP_EST';
    if ($ST === 'REAL' && daysSince($SD) >= 4) $ST = 'REAL_EST';

    // filtro por stage (si viene)
    if ($stage !== '' && $stage !== $ST) continue;

    $out[] = [
      'sid'   => $row['Sample_ID'],
      'num'   => $row['Sample_Number'],
      'type'  => $T,
      'reg'   => $reg,
      'sd'    => $SD,
      'stage' => $ST
    ];
  }
}

/* ORDENAR */
usort($out, function($a,$b){
  return strcmp((string)($a['reg'] ?? ''), (string)($b['reg'] ?? ''));
});

/* ============================
   PDF
============================ */
class PDF extends Fpdi {}
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);

$header = "PENDIENTES  ".$type;
if ($stage !== "") $header .= " - STAGE: ".$stage;
if ($useRange) $header .= "  ($from to $to)";

$pdf->Cell(190,10,$header,0,1,'C');

$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,8,'Sample ID',1);
$pdf->Cell(30,8,'Number',1);
$pdf->Cell(30,8,'Type',1);
$pdf->Cell(40,8,'Req. Date',1);
$pdf->Cell(40,8,'Stage Date',1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);

foreach ($out as $p){
  $pdf->Cell(40,8,(string)$p['sid'],1);
  $pdf->Cell(30,8,(string)$p['num'],1);
  $pdf->Cell(30,8,(string)$p['type'],1);
  $pdf->Cell(40,8,(string)$p['reg'],1);
  $pdf->Cell(40,8,(string)$p['sd'],1);
  $pdf->Ln();
}

ob_end_clean();
$pdf->Output();
