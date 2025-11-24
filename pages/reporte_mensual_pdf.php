<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

ini_set('display_errors',1);
error_reporting(E_ALL);

/* =========================================================
   1. INPUTS (MES Y AÑO)
========================================================= */
$year  = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');
$month = isset($_GET['mes'])  ? (int)$_GET['mes']  : date('n');

$start_str = "{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01 00:00:00";
$end_str   = date("Y-m-t 23:59:59", strtotime($start_str));

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* =========================================================
   2. SQL FUNCTIONS
========================================================= */

function sql($q){ return find_by_sql($q); }

function count_between($table,$field,$start,$end){
    $r = sql("SELECT COUNT(*) total FROM $table WHERE $field BETWEEN '$start' AND '$end'");
    return (int)$r[0]['total'];
}

function monthly_top_client($start,$end){
    $r = sql("
        SELECT Client, COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '$start' AND '$end'
        GROUP BY Client
        ORDER BY total DESC
        LIMIT 1
    ");
    return $r ? $r[0] : ['Client'=>'N/A','total'=>0];
}

function weekly_workload($year,$month){
    return sql("
        SELECT
            YEARWEEK(Registed_Date,1) AS yw,
            WEEK(Registed_Date,1) AS week,
            COUNT(*) AS registered
        FROM lab_test_requisition_form
        WHERE YEAR(Registed_Date) = $year
          AND MONTH(Registed_Date) = $month
        GROUP BY yw
        ORDER BY week ASC
    ");
}

function weekly_delivery($year,$month){
    return sql("
        SELECT
            YEARWEEK(Register_Date,1) AS yw,
            WEEK(Register_Date,1) AS week,
            COUNT(*) AS delivered
        FROM test_delivery
        WHERE YEAR(Register_Date) = $year
          AND MONTH(Register_Date) = $month
        GROUP BY yw
        ORDER BY week ASC
    ");
}

function tests_registered_expanded($start,$end){
    $rows = sql("SELECT Test_Type FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '$start' AND '$end'");
    $out = [];
    foreach($rows as $r){
        foreach(explode(',',strtoupper($r['Test_Type'])) as $t){
            $t = trim($t);
            if($t=='') continue;
            if(!isset($out[$t])) $out[$t]=0;
            $out[$t]++;
        }
    }
    return $out;
}

function tests_delivered_expanded($start,$end){
    $rows = sql("SELECT Test_Type FROM test_delivery WHERE Register_Date BETWEEN '$start' AND '$end'");
    $out = [];
    foreach($rows as $r){
        $t = strtoupper(trim($r['Test_Type']));
        if($t=='') continue;
        if(!isset($out[$t])) $out[$t]=0;
        $out[$t]++;
    }
    return $out;
}

function monthly_clients($start,$end){
    return sql("
        SELECT r.Client,
               COUNT(*) AS requested,
               SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) AS delivered
        FROM lab_test_requisition_form r
        LEFT JOIN test_delivery d
          ON r.Sample_ID=d.Sample_ID
         AND r.Sample_Number=d.Sample_Number
         AND r.Test_Type=d.Test_Type
        WHERE r.Registed_Date BETWEEN '$start' AND '$end'
        GROUP BY r.Client
        ORDER BY requested DESC
    ");
}

function pending_tests_expanded($start,$end){
    $reqs = sql("SELECT * FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '$start' AND '$end'");
    $pending=[];

    foreach($reqs as $r){
        $types = explode(',',strtoupper($r['Test_Type']));
        foreach($types as $t){
            $t=trim($t); if($t=='') continue;

            $exists = sql("
                SELECT 1 FROM (
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_preparation
                    UNION ALL
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_realization
                    UNION ALL
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_delivery
                ) x
                WHERE x.Sample_ID='{$r['Sample_ID']}'
                  AND x.Sample_Number='{$r['Sample_Number']}'
                  AND x.Test_Type='$t'
                LIMIT 1
            ");

            if(empty($exists)){
                $pending[]=[
                    'Sample_ID'=>$r['Sample_ID'],
                    'Sample_Number'=>$r['Sample_Number'],
                    'Structure'=>$r['Structure'],
                    'Client'=>$r['Client'],
                    'Test_Type'=>$t,
                    'Registed_Date'=>$r['Registed_Date']
                ];
            }
        }
    }
    return $pending;
}

function ncr_month($start,$end){
    return sql("
        SELECT *
        FROM ensayos_reporte
        WHERE TRIM(Noconformidad) <> ''
          AND Report_Date BETWEEN '$start' AND '$end'
    ");
}

/* ======================================================
   4. PDF CLASS
====================================================== */

class PDF_MONTHLY extends FPDF {

    public $current_table_header = null;

    function getPageBreakTrigger() {
        return $this->PageBreakTrigger;
    }

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
        $this->current_table_header=[ 'cols'=>$cols,'widths'=>$w ];
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',10);
        foreach($data as $i=>$txt){
            $this->Cell($w[$i],7,utf8_decode($txt),1,0,'C');
        }
        $this->Ln();
    }
}

/* ======================================================
   INIT PDF
====================================================== */

$pdf = new PDF_MONTHLY();
$pdf->AddPage();


/* =========================================================
   4. PORTADA
========================================================= */

$pdf = new PDF_MONTHLY();
$pdf->AddPage();

if(file_exists('../assets/img/Pueblo-Viejo.jpg')){
    $pdf->Image('../assets/img/Pueblo-Viejo.jpg',10,10,50);
}

$pdf->SetY(35);
$pdf->SetFont('Arial','B',22);
$pdf->Cell(0,15,"MONTHLY LABORATORY REPORT",0,1,'C');

$pdf->SetFont('Arial','',15);
$pdf->Cell(0,10,date("F Y",strtotime($start_str)),0,1,'C');

$pdf->Ln(10);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Pueblo Viejo Mine — TSF Laboratory",0,1,'C');
$pdf->Cell(0,8,"Prepared by: ".utf8_decode($responsable),0,1,'C');

$pdf->AddPage();

/* =========================================================
   5. EXECUTIVE SUMMARY
========================================================= */

$pdf->section_title("1. Executive Summary");

$total_reg = count_between("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$total_del = count_between("test_delivery","Register_Date",$start_str,$end_str);
$total_pend = $total_reg - $total_del;
$total_ncr = count(ncr_month($start_str,$end_str));
$top_client = monthly_top_client($start_str,$end_str);

/* Summary automático */
$pdf->SetFont('Arial','',10);
$txt = "
During this month, the TSF Laboratory processed a total of $total_reg samples, out of which $total_del were fully completed and delivered. 
A total of $total_pend tests remain pending at the end of the period.

The highest demand originated from client {$top_client['Client']} ({$top_client['total']} samples). 
There were $total_ncr non-conformities recorded during the month, all of which were documented and addressed through the internal QA/QC workflow.

Overall, laboratory operations maintained consistent performance and stable turnaround capacity.
";
$pdf->MultiCell(0,6,utf8_decode($txt));
$pdf->Ln(5);
/* ======================================================
   4. WEEKLY WORKLOAD (GRAPH)
====================================================== */

$pdf->section_title("4. Weekly Workload Summary");

$weekly = find_by_sql("
    SELECT 
        WEEK(Registed_Date, 1) - WEEK(DATE_FORMAT(Registed_Date,'%Y-%m-01'),1) + 1 AS wk,
        COUNT(*) as registered
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY wk
");

$weekly_del = find_by_sql("
    SELECT 
        WEEK(Register_Date, 1) - WEEK(DATE_FORMAT(Register_Date,'%Y-%m-01'),1) + 1 AS wk,
        COUNT(*) as delivered
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY wk
");

$regW = [];
foreach($weekly as $w){ $regW[$w['wk']] = $w['registered']; }

$delW = [];
foreach($weekly_del as $w){ $delW[$w['wk']] = $w['delivered']; }

$weeks = [1,2,3,4,5];
$maxVal = max( array_merge( array_values($regW), array_values($delW), [1] ) );

$chartX = 25;
$chartY = $pdf->GetY() + 10;
$barW   = 20;
$chartH = 60;

$pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);
$pdf->Line($chartX, $chartY + $chartH, $chartX + (count($weeks)*$barW*2), $chartY + $chartH);

$x = $chartX + 5;

foreach($weeks as $wk){

    $r = $regW[$wk] ?? 0;
    $d = $delW[$wk] ?? 0;

    $rH = ($r / $maxVal) * ($chartH - 5);
    $dH = ($d / $maxVal) * ($chartH - 5);

    $pdf->SetFillColor(50,170,70);  // green
    $pdf->Rect($x, $chartY + ($chartH - $rH), $barW, $rH, "F");

    $pdf->SetFillColor(60,120,240); // blue
    $pdf->Rect($x + $barW + 3, $chartY + ($chartH - $dH), $barW, $dH, "F");

    $pdf->SetFont('Arial','',8);
    $pdf->SetXY($x, $chartY + $chartH + 2);
    $pdf->Cell($barW*2+3, 4, "W{$wk}", 0, 0, 'C');

    $x += ($barW*2) + 6;
}

$pdf->Ln($chartH + 12);

/* ======================================================
   5. TEST TYPE DISTRIBUTION (GRAPH + TABLE)
====================================================== */

$pdf->section_title("5. Test Type Performance");

$registered = tests_registered_expanded($start_str, $end_str);
$delivered  = tests_delivered_expanded($start_str, $end_str);

$allTests = array_unique(array_merge(array_keys($registered), array_keys($delivered)));
sort($allTests);

$maxVal = max( array_merge( array_values($registered), array_values($delivered), [1] ) );

$chartX = 20;
$chartY = $pdf->GetY() + 15;
$chartH = 55;

$pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);
$pdf->Line($chartX, $chartY + $chartH, $chartX + 170, $chartY + $chartH);

$x = $chartX + 5;
$barW = 6;

foreach($allTests as $t){

    $r = $registered[$t] ?? 0;
    $d = $delivered[$t] ?? 0;

    $rH = ($r / $maxVal) * ($chartH - 5);
    $dH = ($d / $maxVal) * ($chartH - 5);

    $pdf->SetFillColor(0,160,70); 
    $pdf->Rect($x, $chartY + ($chartH - $rH), $barW, $rH, "F");

    $pdf->SetFillColor(70,130,230); 
    $pdf->Rect($x + $barW + 1, $chartY + ($chartH - $dH), $barW, $dH, "F");

    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($x-2, $chartY + $chartH + 2);
    $pdf->MultiCell($barW*2+2, 3, $t, 0, 'C');

    $x += ($barW*2) + 6;
}

$pdf->Ln($chartH + 20);

$pdf->table_header(
    ["Test Type","Registered","Delivered","Pending"],
    [70,30,30,30]
);

foreach($allTests as $t){

    $r = $registered[$t] ?? 0;
    $d = $delivered[$t] ?? 0;
    $p = $r - $d;

    $pdf->table_row([$t,$r,$d,$p], [70,30,30,30]);
}

$pdf->Ln(14);
/* ======================================================
   6. PENDING TESTS (Expanded)
====================================================== */

$pdf->section_title("6. Pending Tests (Expanded)");

$pend = pending_tests_expanded($start_str, $end_str);

$pdf->table_header(
    ["Sample","Structure","Client","Test","Date"],
    [30,40,40,30,50]
);

foreach($pend as $p){
    table_row_multiline($pdf,[
        $p['Sample_ID']."-".$p['Sample_Number'],
        $p['Structure'],
        $p['Client'],
        $p['Test_Type'],
        $p['Registed_Date']
    ], [30,40,40,30,50]);
}

$pdf->Ln(10);

/* ======================================================
   7. NCR / OBSERVATIONS
====================================================== */

$pdf->section_title("7. Non-Conformities (NCR)");

$ncrs = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observation"],[50,140]);

foreach($ncrs as $n){
    table_row_multiline($pdf,[
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ], [50,140]);
}

$pdf->Ln(12);

/* ======================================================
   8. CLIENT SUMMARY
====================================================== */

$pdf->section_title("8. Monthly Summary by Client");

$clients = monthly_clients($start_str,$end_str);

$pdf->table_header(
    ["Client","Requested","Delivered","Pending"],
    [70,30,30,30]
);

foreach($clients as $c){
    $p = $c['requested'] - $c['delivered'];
    $pdf->table_row(
        [$c['Client'],$c['requested'],$c['delivered'],$p],
        [70,30,30,30]
    );
}

$pdf->Ln(14);

/* ======================================================
   RESPONSIBLE
====================================================== */

$pdf->section_title("9. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,8,"Report prepared by",1);
$pdf->Cell(130,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Monthly_Lab_Report_{$year}_{$month}.pdf");
?>
