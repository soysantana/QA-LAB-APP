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

function normNum($v){
  $s = strtoupper(trim((string)$v));
  if ($s === '') return '';

  // 0078 -> 78
  if (preg_match('/^\d+$/', $s)) return (string)intval($s);

  // G001 -> G1
  if (preg_match('/^G0*\d+$/', $s)) return 'G'.(string)intval(substr($s,1));

  return $s;
}

function makeKey($sid, $sno, $tt){
  return N($sid) . "|" . normNum($sno) . "|" . N($tt);
}

function daysSince($date){
  if(!$date) return 0;
  $t = strtotime($date);
  if(!$t) return 0;
  return (time() - $t) / 86400;
}

/* ============================
   PARÁMETROS
============================ */
$type_raw  = $_GET['type']  ?? '';
$stage_raw = $_GET['stage'] ?? '';

$type  = N($type_raw);   // GS, MC, AL, etc
$stage = N($stage_raw);  // SIN / PREP / PREP_EST / REAL / REAL_EST / ENT / REV / REP

$from = $_GET['from'] ?? date('Y-m-d', strtotime('-90 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-90 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

$db = $GLOBALS['db'];
$fromEsc = $db->escape($from);
$toEsc   = $db->escape($to);

/* ============================
   1) REQUISICIONES (MISMO FILTRO QUE PÁGINA)
============================ */
$req = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
  FROM lab_test_requisition_form
  WHERE Registed_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($req)) $req = [];

/* ============================
   2) WORKFLOW (FUENTE DE VERDAD)
============================ */
$wf = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Status, Updated_At, Process_Started
  FROM test_workflow
");
if (!is_array($wf)) $wf = [];

$wfIndex = [];
foreach ($wf as $w) {
  $k = makeKey($w['Sample_ID'] ?? '', $w['Sample_Number'] ?? '', $w['Test_Type'] ?? '');
  if ($k === "||") continue;

  $sd = $w['Updated_At'] ?? $w['Process_Started'] ?? null;

  $wfIndex[$k] = [
    'status' => (string)($w['Status'] ?? 'Registrado'),
    'sd'     => $sd
  ];
}

/* ============================
   3) CONSTRUIR SALIDA (MISMA LÓGICA QUE LISTADO)
============================ */
$out = [];

foreach ($req as $row) {

  if (empty($row['Test_Type'])) continue;

  $tests = array_map('trim', explode(',', (string)$row['Test_Type']));

  foreach ($tests as $t) {

    $T = N($t);
    if ($T === '') continue;

    // filtrar por type si viene en URL
    if ($type !== '' && $T !== $type) continue;

    $key = makeKey($row['Sample_ID'] ?? '', $row['Sample_Number'] ?? '', $T);

    $wfRow = $wfIndex[$key] ?? null;

    // status y fecha base (para estancados)
    $status = $wfRow['status'] ?? 'Registrado';
    $SD     = $wfRow['sd']     ?? ($row['Registed_Date'] ?? null);

    // map status workflow -> stage PDF
    $ST = 'SIN';
    if ($status === 'Registrado')   $ST = 'SIN';
    if ($status === 'Preparación')  $ST = 'PREP';
    if ($status === 'Realización')  $ST = 'REAL';
    if ($status === 'Repetición')   $ST = 'REP';
    if ($status === 'Entrega')      $ST = 'ENT';
    if ($status === 'Revisado')     $ST = 'REV';

    // Estancados (basado en Updated_At / Process_Started del workflow)
    if ($ST === 'PREP' && daysSince($SD) >= 3) $ST = 'PREP_EST';
    if ($ST === 'REAL' && daysSince($SD) >= 4) $ST = 'REAL_EST';

    // filtrar por stage si viene en URL
    if ($stage !== '' && $stage !== $ST) continue;

    $out[] = [
      'sid'   => $row['Sample_ID'] ?? '',
      'num'   => $row['Sample_Number'] ?? '',
      'type'  => $T,
      'reg'   => $row['Registed_Date'] ?? '',
      'sd'    => (string)($SD ?? ''),
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
