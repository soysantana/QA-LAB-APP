<?php
ob_start(); // IMPORTANTÍSIMO PARA EVITAR "Some data has already been output"

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
$type_raw  = $_GET['type']  ?? '';
$stage_raw = $_GET['stage'] ?? '';

$type  = N($type_raw);
$stage = N($stage_raw);

$db = $GLOBALS['db'];

/* ============================
   CARGA BASE
============================ */
$req = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
    FROM lab_test_requisition_form
");

/* ESTADOS */
$prep = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_preparation");
$real = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_realization");
$ent  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_delivery");
$rev  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_review");
$rep  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_repeat");
$rev2 = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date FROM test_reviewed");

/* ============================
   INDEX ESTADO
============================ */
$index = [];

foreach ($prep as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
     = ['stage' => 'PREP', 'SD' => $r['Start_Date']];

foreach ($real as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
     = ['stage' => 'REAL', 'SD' => $r['Start_Date']];

foreach ($ent as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
     = ['stage' => 'ENT', 'SD' => $r['Start_Date']];

foreach ($rev as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
     = ['stage' => 'REV', 'SD' => $r['Start_Date']];

foreach ($rep as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
     = ['stage' => 'REP', 'SD' => $r['Start_Date']];

foreach ($rev2 as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
     = ['stage' => 'REV', 'SD' => $r['Start_Date']];

/* ============================
   ARMAR SALIDA
============================ */
$out = [];

foreach ($req as $row){

    if(empty($row['Test_Type'])) continue;

    $tests = array_map('trim', explode(',', $row['Test_Type']));

    foreach ($tests as $t){

        if(N($t) !== $type) continue;

        $key = N($row['Sample_ID'])."|".N($row['Sample_Number'])."|".N($t);
        $stData = $index[$key] ?? null;

        $ST  = $stData['stage'] ?? 'SIN';
        $SD  = $stData['SD'] ?? $row['Registed_Date'];

        /* ===== ESTANCADOS ===== */
        if($ST === 'PREP' && daysSince($SD) >= 3)   $ST = 'PREP_EST';
        if($ST === 'REAL' && daysSince($SD) >= 4)   $ST = 'REAL_EST';

        /* FILTRO */
        if($stage !== '' && $stage !== $ST) continue;

        $out[] = [
            'sid'   => $row['Sample_ID'],
            'num'   => $row['Sample_Number'],
            'type'  => $type,
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
$pdf->SetFont('Arial','B',14);

$header = "PENDIENTES  $type";
if($stage !== "") $header .= " - STAGE: $stage";

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
    $pdf->Cell(40,8,$p['sid'],1);
    $pdf->Cell(30,8,$p['num'],1);
    $pdf->Cell(30,8,$p['type'],1);
    $pdf->Cell(40,8,$p['reg'],1);
    $pdf->Cell(40,8,$p['sd'],1);
    $pdf->Ln();
}

ob_end_clean();
$pdf->Output();
