<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ======================================================
   1. DEFINIR MES Y AÑO
====================================================== */
$year  = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');
$month = isset($_GET['mes'])  ? (int)$_GET['mes']  : date('n');

$start_str = "{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01 00:00:00";
$end_str   = date("Y-m-t 23:59:59", strtotime($start_str));

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* ======================================================
   2. FUNCIONES SQL
====================================================== */

function get_count($table,$field,$start,$end){
    $r = find_by_sql("
        SELECT COUNT(*) total
        FROM {$table}
        WHERE {$field} BETWEEN '{$start}' AND '{$end}'
    ");
    return (int)$r[0]['total'];
}

function monthly_top_client($start,$end){
    $rows = find_by_sql("
        SELECT Client, COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Client
        ORDER BY total DESC
        LIMIT 1
    ");
    return $rows ? $rows[0] : ['Client'=>'N/A','total'=>0];
}

function last_6_months(){
    return find_by_sql("
        SELECT DATE_FORMAT(Registed_Date,'%Y-%m') AS mes,
               COUNT(*) AS total
        FROM lab_test_requisition_form
        WHERE Registed_Date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes
        ORDER BY mes ASC
    ");
}

/* ======================================================
   FIX: TESTS REGISTERED — EXPANDED
====================================================== */
function tests_registered_by_type($start,$end){

    $rows = find_by_sql("
        SELECT Test_Type
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $map = [];

    foreach($rows as $r){
        $tests = explode(',', strtoupper(trim($r['Test_Type'])));
        foreach($tests as $t){
            $t = trim($t);
            if($t === '') continue;
            if(!isset($map[$t])) $map[$t] = 0;
            $map[$t]++;
        }
    }

    $final = [];
    foreach($map as $type=>$total){
        $final[] = ['Test_Type'=>$type, 'total'=>$total];
    }

    return $final;
}

/* ======================================================
   FIX: TESTS COMPLETED — EXPANDED
====================================================== */
function tests_completed_by_type($start,$end){

    $rows = find_by_sql("
        SELECT Test_Type
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $map = [];

    foreach($rows as $r){
        $tests = explode(',', strtoupper(trim($r['Test_Type'])));
        foreach($tests as $t){
            $t = trim($t);
            if($t === '') continue;
            if(!isset($map[$t])) $map[$t] = 0;
            $map[$t]++;
        }
    }

    $final = [];
    foreach($map as $type=>$total){
        $final[] = ['Test_Type'=>$type, 'total'=>$total];
    }

    return $final;
}


function monthly_clients($start,$end){
    return find_by_sql("
        SELECT r.Client,
               COUNT(*) AS requested,
               SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) delivered
        FROM lab_test_requisition_form r
        LEFT JOIN test_delivery d
        ON r.Sample_ID=d.Sample_ID
        AND r.Sample_Number=d.Sample_Number
        AND r.Test_Type=d.Test_Type
        WHERE r.Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY r.Client
        ORDER BY requested DESC
    ");
}

function pending_tests_expanded($start, $end) {

    $reqs = find_by_sql("
        SELECT *
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $pendientes = [];

    foreach ($reqs as $r) {
        $tests = explode(',', strtoupper(trim($r['Test_Type'])));

        foreach ($tests as $test) {
            $test = trim($test);
            if ($test == '') continue;

            $exists = find_by_sql("
                SELECT 1
                FROM (
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_preparation
                    UNION ALL
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_realization
                    UNION ALL
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_delivery
                ) x
                WHERE x.Sample_ID     = '{$r['Sample_ID']}'
                  AND x.Sample_Number = '{$r['Sample_Number']}'
                  AND x.Test_Type     = '{$test}'
                LIMIT 1
            ");

            if (empty($exists)) {
                $pendientes[] = [
                    'Sample_ID'     => $r['Sample_ID'],
                    'Sample_Number' => $r['Sample_Number'],
                    'Structure'     => $r['Structure'],
                    'Client'        => $r['Client'],
                    'Test_Type'     => $test,
                    'Registed_Date' => $r['Registed_Date'],
                ];
            }
        }
    }

    return $pendientes;
}

function ncr_month($start,$end){
    return find_by_sql("
        SELECT *
        FROM ensayos_reporte
        WHERE Noconformidad IS NOT NULL
        AND TRIM(Noconformidad) <> ''
        AND Report_Date BETWEEN '{$start}' AND '{$end}'
    ");
}

/* ======================================================
   3. AUX MULTILINE
====================================================== */

function table_row_multiline($pdf,$data,$w){
    $pdf->SetFont('Arial','',9);
    $maxHeight = 5;

    foreach($data as $i=>$txt){
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i]-2,1);
        $h  = max(ceil($nb)*5, 7);
        if($h > $maxHeight) $maxHeight = $h;
    }

    if($pdf->GetY() + $maxHeight > $pdf->PageBreakTrigger){
        $pdf->AddPage();
        if($pdf->current_table_header){
            $pdf->table_header(
                $pdf->current_table_header['cols'],
                $pdf->current_table_header['widths']
            );
        }
    }

    foreach($data as $i=>$txt){
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($w[$i],5,utf8_decode($txt),1,'L');
        $pdf->SetXY($x+$w[$i],$y);
    }

    $pdf->Ln($maxHeight);
}
function ensure_space($pdf, $height){
    if ($pdf->GetY() + $height > 260) { 
        $pdf->AddPage();
    }
}



/* ======================================================
   4. PDF CLASS
====================================================== */

class PDF_MONTHLY extends FPDF {

    public $current_table_header = null;

    function Header(){}

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }

    function section_title($txt){
        $this->SetFont('Arial','B',13);
        $this->SetFillColor(200,220,255);
        $this->Cell(0,9,utf8_decode($txt),0,1,'L',true);
        $this->Ln(2);
    }

    function table_header($cols,$w){
        $this->SetFont('Arial','B',10);
        foreach($cols as $i=>$c){
            $this->Cell($w[$i],7,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
        $this->current_table_header=[
            'cols'=>$cols,
            'widths'=>$w
        ];
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',10);
        foreach($data as $i=>$txt){
            $this->Cell($w[$i],7,utf8_decode($txt),1,0,'C');
        }
        $this->Ln();
    }
}

$pdf = new PDF_MONTHLY();

/* ======================================================
   PORTADA
====================================================== */

$pdf->AddPage();

if (file_exists('../assets/img/Pueblo-Viejo.jpg')){
    $pdf->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 55);
}

$pdf->SetY(40);
$pdf->SetFont('Arial','B',24);
$pdf->Cell(0,20,'MONTHLY LABORATORY REPORT',0,1,'C');

$pdf->SetFont('Arial','',16);
$pdf->Cell(0,10,date("F Y", strtotime($start_str)),0,1,'C');

$pdf->Ln(20);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Pueblo Viejo Mine  TSF Laboratory',0,1,'C');
$pdf->Cell(0,8,'Prepared by: '.utf8_decode($responsable),0,1,'C');

$pdf->Ln(15);

$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,8,'(Monthly report generated by CQA Laboratory)',0,1,'C');

/* Nueva página */
$pdf->AddPage();

/* ======================================================
   5. EXECUTIVE SUMMARY
====================================================== */

$pdf->section_title("2. Executive Summary");

$total_tests = get_count("test_delivery","Register_Date",$start_str,$end_str);
$total_req   = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$total_ncr   = count(ncr_month($start_str,$end_str));

$top_client  = monthly_top_client($start_str,$end_str);

$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,6,utf8_decode("
- A total of {$total_tests} laboratory tests were performed this month.
- {$total_req} samples were received.
- {$total_ncr} non-conformities were issued.
- Highest demand came from: {$top_client['Client']} ({$top_client['total']} samples).
- Monthly operational performance remained stable.
"));
$pdf->Ln(5);

/* ======================================================
   6. MONTHLY ACTIVITY SUMMARY
====================================================== */

$pdf->section_title("3. Monthly Activity Summary");

$prep    = get_count("test_preparation","Register_Date",$start_str,$end_str);
$real    = get_count("test_realization","Register_Date",$start_str,$end_str);
$pending = $total_req - $total_tests;

$pdf->table_header(["Activity","Total"],[100,40]);
$pdf->table_row(["Samples Registered",$total_req],[100,40]);
$pdf->table_row(["Tests Prepared",$prep],[100,40]);
$pdf->table_row(["Tests Performed",$real],[100,40]);
$pdf->table_row(["Tests Delivered",$total_tests],[100,40]);
$pdf->table_row(["Pending Tests",$pending],[100,40]);

$pdf->Ln(10);

/* ======================================================
   7. LAST 6 MONTHS TREND (LINE CHART)
====================================================== */

$pdf->section_title("4. Trend - Last 6 Months");

$trend = last_6_months();

/* Coordenadas */
$chartX = 20;
$chartY = $pdf->GetY()+10;
$chartW = 170;
$chartH = 50;

/* Ejes */
$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

$months = count($trend);
$step = ($chartW-20)/max($months-1,1);

$x = $chartX+10;
$max = max(array_column($trend,'total'));

foreach($trend as $i=>$t){

    $h = ($t['total']/$max)*$chartH;
    $y = $chartY + ($chartH - $h);

    $pdf->SetFillColor(50,150,250);
    $pdf->Rect($x-1,$y-1,2,2,'F');

    if($i>0){ $pdf->Line($prev_x,$prev_y,$x,$y); }

    $prev_x = $x;
    $prev_y = $y;

    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($x-10,$chartY+$chartH+2);
    $pdf->MultiCell(20,3,$t['mes'],0,'C');

    $x += $step;
}

$pdf->Ln(20);
/* ======================================================
   8. PENDING TESTS — LIST
====================================================== */

$pdf->section_title("5. Pending Tests (Monthly)");

$pendientes = pending_tests_expanded($start_str,$end_str);

$pdf->table_header(
    ["Sample","Test","Client","Date"],
    [60,65,40,20]
);

foreach ($pendientes as $p){
    $pdf->table_row([
        $p['Sample_ID']."-".$p['Sample_Number'],
        $p['Test_Type'],
        $p['Client'],
        date("d-M", strtotime($p['Registed_Date']))
    ], [60,65,40,20]);
}

$pdf->Ln(10);

/* ======================================================
   6. OPERATIONAL OVERVIEW (4 KPI BOXES)
====================================================== */

$pdf->section_title("6. Operational Overview");

$registered = $total_req;
$completed  = $total_tests;
$pending    = $pending;
$ncr_total  = $total_ncr;

$boxW = 90;
$boxH = 22;
$startX = 10;
$startY = $pdf->GetY();

$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(240, 240, 240);

/* BOX 1 – Registered */
$pdf->SetXY($startX, $startY);
$pdf->Cell($boxW, 8, "Samples Registered", 1, 2, 'L', true);
$pdf->SetFont('Arial','B',14);
$pdf->Cell($boxW, 10, $registered, 1, 2, 'C');
$pdf->SetFillColor(200, 200, 200);
$pdf->Rect($startX+2, $startY+18, $boxW-4, 4, "F");

/* BOX 2 – Completed */
$pdf->SetXY($startX + $boxW + 10, $startY);
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell($boxW, 8, "Tests Completed", 1, 2, 'L', true);
$pdf->SetFont('Arial','B',14);
$pdf->Cell($boxW, 10, $completed, 1, 2, 'C');
$pdf->SetFillColor(200, 200, 200);
$pdf->Rect($startX + $boxW + 12, $startY+18, $boxW-4, 4, "F");

/* NEXT ROW */
$startY += $boxH + 10;

/* BOX 3 – Pending Tests */
$pdf->SetXY($startX, $startY);
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell($boxW, 8, "Pending Tests", 1, 2, 'L', true);
$pdf->SetFont('Arial','B',14);
$pdf->Cell($boxW, 10, $pending, 1, 2, 'C');
$pdf->SetFillColor(200, 200, 200);
$pdf->Rect($startX+2, $startY+18, $boxW-4, 4, "F");

/* BOX 4 – Non-Conformities */
$pdf->SetXY($startX + $boxW + 10, $startY);
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell($boxW, 8, "Non-Conformities", 1, 2, 'L', true);
$pdf->SetFont('Arial','B',14);
$pdf->Cell($boxW, 10, $ncr_total, 1, 2, 'C');
$pdf->SetFillColor(200, 200, 200);
$pdf->Rect($startX + $boxW + 12, $startY+18, $boxW-4, 4, "F");

$pdf->Ln(30);
/* ======================================================
   7. Top 10 Test Types – Requested vs Delivered
====================================================== */

$pdf->section_title("7. Top 10 Test Types – Requested vs Delivered");
ensure_space($pdf, 140);


/* 1) Obtener totales del mes */
$reg_type  = tests_registered_by_type($start_str,$end_str);
$comp_type = tests_completed_by_type($start_str,$end_str);

/* Mapeos */
$regMap  = [];
foreach($reg_type as $r){
    $type = strtoupper(trim($r['Test_Type']));
    $regMap[$type] = $r['total'];
}

$compMap = [];
foreach($comp_type as $c){
    $type = strtoupper(trim($c['Test_Type']));
    $compMap[$type] = $c['total'];
}

/* Unir claves */
$allTypes = array_unique(array_merge(array_keys($regMap), array_keys($compMap)));

/* Ordenar por Registered (desc) */
usort($allTypes, function($a,$b) use ($regMap){
    return ($regMap[$b] ?? 0) - ($regMap[$a] ?? 0);
});

/* Limitar a Top 10 */
$allTypes = array_slice($allTypes, 0, 10);

/* === GRÁFICO DE BARRAS – Requested (verde) vs Delivered (gris) === */

$chartX = 20;
$chartY = $pdf->GetY() + 10;
$chartW = 175;
$chartH = 65;

$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

$barWidth = 16;
$spacing  = 10;
$x = $chartX + 10;

$values = array_map(function($t) use ($regMap){
    return $regMap[$t] ?? 0;
}, $allTypes);

$maxVal = !empty($values) ? max($values) : 1;

foreach($allTypes as $t){

    $reg = $regMap[$t] ?? 0;
    $del = $compMap[$t] ?? 0;
    $pen = max($reg - $del, 0);

    /* Delivered (green) */
    $hDel = ($del / $maxVal) * ($chartH - 5);
    $yDel = $chartY + ($chartH - $hDel);
    $pdf->SetFillColor(60,180,75);
    $pdf->Rect($x, $yDel, $barWidth, $hDel, "F");

    /* Pending (gray) */
    $hPen = ($pen / $maxVal) * ($chartH - 5);
    $yPen = $yDel - $hPen;
    $pdf->SetFillColor(180,180,180);

    if($hPen > 0){
        $pdf->Rect($x, $yPen, $barWidth, $hPen, "F");
    }

    /* Etiqueta */
    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($x-3,$chartY+$chartH+2);
    $pdf->MultiCell($barWidth+5,3,$t,0,'C');

    $x += $barWidth + $spacing;
}

$pdf->Ln($chartH + 20);

/* Tabla */
$pdf->table_header(
    ["Test Type","Registered","Delivered","Pending"],
    [60,30,30,30]
);

foreach($allTypes as $t){
    $r = $regMap[$t] ?? 0;
    $c = $compMap[$t] ?? 0;
    $p = max($r - $c,0);

    $pdf->table_row([$t, $r, $c, $p], [60,30,30,30]);
}

$pdf->Ln(10);

/* ======================================================
   8. NCR SUMMARY (Monthly)
====================================================== */

$pdf->section_title("8. Non-Conformities / Observations");

$ncrs = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observation"],[50,140]);

foreach($ncrs as $n){
    table_row_multiline($pdf,[
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ],[50,140]);
}

$pdf->Ln(10);

/* ======================================================
   9. RESPONSIBLE
====================================================== */

$pdf->section_title("9. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(60,8,"Report prepared by",1);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Monthly_Lab_Report_{$year}_{$month}.pdf");
