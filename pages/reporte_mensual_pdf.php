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

/* ===== TEST TYPE EXPANDIDO (INDIVIDUAL) ===== */
function tests_registered_expanded($start,$end){
    $reqs = find_by_sql("
        SELECT Test_Type
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $out = [];
    foreach($reqs as $r){
        $types = explode(',', strtoupper(trim($r['Test_Type'])));
        foreach($types as $t){
            $t = trim($t);
            if ($t=='') continue;
            if (!isset($out[$t])) $out[$t] = 0;
            $out[$t]++;
        }
    }
    return $out;
}

function tests_delivered_expanded($start,$end){
    $rows = find_by_sql("
        SELECT Test_Type
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $out = [];
    foreach($rows as $r){
        $t = strtoupper(trim($r['Test_Type']));
        if ($t=='') continue;
        if (!isset($out[$t])) $out[$t] = 0;
        $out[$t]++;
    }
    return $out;
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
   AUXILIARES
====================================================== */

function table_row_multiline($pdf,$data,$w){

    $pdf->SetFont('Arial','',9);
    $maxH = 5;

    foreach($data as $i=>$txt){
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i]-2,1);
        $h  = max(ceil($nb)*5, 7);
        if($h > $maxH) $maxH = $h;
    }

    if($pdf->GetY() + $maxH > $pdf->getPageBreakTrigger()){
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
    $pdf->Ln($maxH);
}

function ensure_space_for_graph($pdf,$h=80){
    if ($pdf->GetY() + $h > 250){
        $pdf->AddPage();
    }
}

/* ======================================================
   PDF CLASS
====================================================== */

class PDF_MONTHLY extends FPDF {

    public $current_table_header=null;

    function getPageBreakTrigger(){
        return $this->PageBreakTrigger;
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
        $this->current_table_header=['cols'=>$cols,'widths'=>$w];
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',10);
        foreach($data as $i=>$txt){
            $this->Cell($w[$i],7,utf8_decode($txt),1,0,'C');
        }
        $this->Ln();
    }

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
}

/* ======================================================
   PORTADA
====================================================== */

$pdf = new PDF_MONTHLY();
$pdf->AddPage();

if (file_exists('../assets/img/Pueblo-Viejo.jpg')){
    $pdf->Image('../assets/img/Pueblo-Viejo.jpg',10,10,55);
}

$pdf->SetY(40);
$pdf->SetFont('Arial','B',24);
$pdf->Cell(0,20,'MONTHLY LABORATORY REPORT',0,1,'C');

$pdf->SetFont('Arial','',16);
$pdf->Cell(0,10,date("F Y", strtotime($start_str)),0,1,'C');

$pdf->Ln(18);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Pueblo Viejo Mine - TSF Laboratory',0,1,'C');
$pdf->Cell(0,8,'Prepared by: '.utf8_decode($responsable),0,1,'C');

$pdf->Ln(12);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,8,'(Monthly report generated by CQA Laboratory)',0,1,'C');

$pdf->AddPage();
/* ======================================================
   2. EXECUTIVE SUMMARY
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
$pdf->Ln(6);

/* ======================================================
   3. KPIs
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

$pdf->Ln(12);

/* ======================================================
   4. PROCESS BOTTLENECKS
====================================================== */

$pdf->section_title("4. Process Bottlenecks (Monthly)");

$prep_delay = find_by_sql("
    SELECT Test_Type, COUNT(*) total
    FROM test_preparation
    WHERE TIMESTAMPDIFF(HOUR, Register_Date, NOW()) > 48
      AND Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type
");

$real_delay = find_by_sql("
    SELECT Test_Type, COUNT(*) total
    FROM test_realization
    WHERE TIMESTAMPDIFF(HOUR, Register_Date, NOW()) > 48
      AND Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type
");

$pdf->table_header(["Stage","Test Type","Delayed Qty (>48h)"], [50,70,40]);

foreach ($prep_delay as $d){
    $pdf->table_row(["Preparation",$d['Test_Type'],$d['total']], [50,70,40]);
}

foreach ($real_delay as $d){
    $pdf->table_row(["Realization",$d['Test_Type'],$d['total']], [50,70,40]);
}

$pdf->Ln(12);

/* ======================================================
   5. TURNAROUND TIME (TAT)
====================================================== */

$pdf->section_title("5. Turnaround Time (TAT) Summary");

$tat_data = find_by_sql("
    SELECT 
        d.Test_Type,
        AVG(TIMESTAMPDIFF(HOUR, r.Registed_Date, d.Register_Date)) AS avg_hours,
        COUNT(*) total
    FROM test_delivery d
    JOIN lab_test_requisition_form r
      ON r.Sample_ID=d.Sample_ID
     AND r.Sample_Number=d.Sample_Number
     AND r.Test_Type=d.Test_Type
    WHERE d.Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY d.Test_Type
    ORDER BY avg_hours ASC
");

$pdf->table_header(["Test Type","Avg TAT (hrs)","Completed"], [60,40,30]);

foreach($tat_data as $t){
    $pdf->table_row([
        $t['Test_Type'],
        number_format($t['avg_hours'],1),
        $t['total']
    ], [60,40,30]);
}

$pdf->Ln(12);

/* ======================================================
   6. TREND (LAST 6 MONTHS)
====================================================== */

$pdf->section_title("6. Trend – Last 6 Months");

ensure_space_for_graph($pdf, 85);

$trend = last_6_months();

$chartX = 20;
$chartY = $pdf->GetY()+10;
$chartW = 170;
$chartH = 45;

/* Ejes */
$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

if (!empty($trend)){
    $months = count($trend);
    $step   = ($chartW-20)/max($months-1,1);
    $x      = $chartX+10;

    $maxVal = max(array_column($trend,'total'));
    $maxVal = max($maxVal,1);

    foreach($trend as $i=>$t){
        $h = ($t['total']/$maxVal) * ($chartH-5);
        $y = $chartY + ($chartH - $h);

        $pdf->SetFillColor(70,130,180);
        $pdf->Rect($x-2,$y-2,4,4,'F');

        if ($i>0){ $pdf->Line($prev_x,$prev_y,$x,$y); }

        $prev_x = $x;
        $prev_y = $y;

        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($x-10,$chartY+$chartH+2);
        $pdf->MultiCell(20,3,$t['mes'],0,'C');

        $x += $step;
    }
}

$pdf->Ln(20);

/* ======================================================
   7. TEST TYPE PERFORMANCE (Gráfico vertical doble + Tabla)
====================================================== */

$pdf->section_title("7. Test Type Performance (Registered vs Delivered)");

ensure_space_for_graph($pdf, 110);

/* Expandidos */
$registered = tests_registered_expanded($start_str, $end_str);
$delivered  = tests_delivered_expanded($start_str, $end_str);

/* Unificar */
$allTests = array_unique(array_merge(array_keys($registered), array_keys($delivered)));
sort($allTests);

/* Max */
$maxVal = 1;
foreach($allTests as $t){
    $maxVal = max($maxVal, $registered[$t] ?? 0, $delivered[$t] ?? 0);
}

/* GRID */
$chartX = 20;
$chartY = $pdf->GetY()+10;
$chartW = 170;
$chartH = 70;

$barWidth = 6;
$gap      = 8;

$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

$x = $chartX + 5;

/* Dibujar barras */
foreach($allTests as $t){

    $reg = $registered[$t] ?? 0;
    $del = $delivered[$t] ?? 0;

    /* REG */
    $regH = ($reg/$maxVal) * ($chartH-5);
    $regY = $chartY + ($chartH - $regH);

    $pdf->SetFillColor(40,180,90);
    $pdf->Rect($x,$regY,$barWidth,$regH,'F');

    /* DEL */
    $delH = ($del/$maxVal) * ($chartH-5);
    $delY = $chartY + ($chartH - $delH);

    $pdf->SetFillColor(60,130,230);
    $pdf->Rect($x+$barWidth+2,$delY,$barWidth,$delH,'F');

    /* Label */
    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($x-3,$chartY+$chartH+3);
    $pdf->MultiCell($barWidth*2+4,3,$t,0,'C');

    $x += ($barWidth*2) + $gap;
}

$pdf->Ln($chartH + 20);

/* Tabla */
$pdf->table_header(["Test Type", "Registered", "Delivered", "Pending"], [70,30,30,30]);

foreach($allTests as $t){
    $r = $registered[$t] ?? 0;
    $d = $delivered[$t] ?? 0;
    $p = $r - $d;

    $pdf->table_row([$t,$r,$d,$p],[70,30,30,30]);
}

$pdf->Ln(12);
/* ======================================================
   8. MONTHLY CLIENT SUMMARY
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

$pdf->Ln(15);

/* ======================================================
   9. NCR FULL LIST
====================================================== */

$pdf->section_title("9. Non-Conformities / Observations");

$ncrs = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observation"],[50,140]);

foreach($ncrs as $n){
    table_row_multiline($pdf,[
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ],[50,140]);
}

$pdf->Ln(12);

/* ======================================================
   10. RESPONSIBLE
====================================================== */

$pdf->section_title("10. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,8,"Report prepared by",1);
$pdf->Cell(130,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Monthly_Lab_Report_{$year}_{$month}.pdf");
?>
