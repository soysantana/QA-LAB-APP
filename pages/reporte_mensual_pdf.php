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

/* Últimos 6 meses */
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

/* Tests por tipo */
function tests_completed_by_type($start,$end){
    return find_by_sql("
        SELECT Test_Type, COUNT(*) total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type
        ORDER BY total DESC
    ");
}

function tests_registered_by_type($start,$end){
    return find_by_sql("
        SELECT Test_Type, COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type
    ");
}

/* Clientes del mes */
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

/* ------------------------------------------------------
   EXPANSIÓN DE PENDING TESTS
------------------------------------------------------ */
function pending_tests_expanded($start,$end){

    $reqs = find_by_sql("
        SELECT *
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $pendientes = [];

    foreach ($reqs as $r){

        $tests = explode(',', strtoupper(trim($r['Test_Type'])));

        foreach ($tests as $test){

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

            if (empty($exists)){
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

/* NCR */
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
   3. FUNCIONES AUXILIARES
====================================================== */

function table_row_multiline($pdf,$data,$w){
    $pdf->SetFont('Arial','',9);
    $maxHeight = 5;

    foreach($data as $i=>$txt){
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i]-2,1);
        $h  = max(ceil($nb)*5, 7);
        if($h > $maxHeight) $maxHeight = $h;
    }

    if($pdf->GetY() + $maxHeight > $pdf->getPageBreakTrigger()){
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

/* ======================================================
   4. PDF CLASS
====================================================== */

class PDF_MONTHLY extends FPDF {

    public $current_table_header = null;

    /* PARA PERMITIR QUE MULTILINE EVALÚE PAGE BREAK */
    function getPageBreakTrigger() {
        return $this->PageBreakTrigger;
    }

    function Header(){ }

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

$pdf->AddPage();

/* ======================================================
   EXECUTIVE SUMMARY
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
   MONTHLY KPIs
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
   4. SECTION 4 — PROCESS BOTTLENECKS (NEW)
====================================================== */

$pdf->section_title("4. Process Bottlenecks (Monthly)");

$prep_delay = find_by_sql("
    SELECT Test_Type, COUNT(*) total
    FROM test_preparation
    WHERE TIMESTAMPDIFF(HOUR, Register_Date, NOW()) > 48
      AND Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type
    ORDER BY total DESC
");

$real_delay = find_by_sql("
    SELECT Test_Type, COUNT(*) total
    FROM test_realization
    WHERE TIMESTAMPDIFF(HOUR, Register_Date, NOW()) > 48
      AND Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type
    ORDER BY total DESC
");

$pdf->table_header(
    ["Stage","Test Type","Delayed Qty (>48h)"],
    [50,70,40]
);

foreach ($prep_delay as $p){
    $pdf->table_row(["Preparation", $p['Test_Type'], $p['total']], [50,70,40]);
}
foreach ($real_delay as $r){
    $pdf->table_row(["Realization", $r['Test_Type'], $r['total']], [50,70,40]);
}

$pdf->Ln(12);

/* ======================================================
   5. SECTION 5 — TURNAROUND TIME (TAT)
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

$pdf->table_header(
    ["Test Type","Avg TAT (hrs)","Completed"],
    [60,40,30]
);

foreach($tat_data as $t){
    $pdf->table_row([
        $t['Test_Type'],
        number_format($t['avg_hours'],1),
        $t['total']
    ], [60,40,30]);
}

$pdf->Ln(12);

/* ======================================================
   6. SECTION 6 — TREND (LAST 6 MONTHS)
====================================================== */

$pdf->section_title("6. Trend – Last 6 Months");

$trend = last_6_months();

$chartX = 20;
$chartY = $pdf->GetY()+10;
$chartW = 170;
$chartH = 45;

$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

if (!empty($trend)){

    $months = count($trend);
    $step = ($chartW-20)/max($months-1,1);
    $x = $chartX+10;

    $max = max(array_column($trend,'total'));
    $max = max($max,1);

    foreach($trend as $i=>$t){
        $h = ($t['total']/$max) * ($chartH-5);
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

$pdf->Ln(10);

/* ======================================================
   7. SECTION 7 — SUMMARY BY TEST TYPE (STACKED BAR)
====================================================== */

$pdf->section_title("7. Summary by Test Type (Registered vs Delivered)");

$reg_type  = tests_registered_by_type($start_str,$end_str);
$comp_type = tests_completed_by_type($start_str,$end_str);

$regMap  = [];
foreach($reg_type as $r){ $regMap[strtoupper($r['Test_Type'])] = $r['total']; }
$compMap = [];
foreach($comp_type as $c){ $compMap[strtoupper($c['Test_Type'])] = $c['total']; }

$allTypes = array_unique(array_merge(array_keys($regMap), array_keys($compMap)));
sort($allTypes);

$chartX = 20;
$chartY = $pdf->GetY()+8;
$chartW = 170;
$barH = 5;
$spacing = 4;

$maxVal = 1;
foreach($allTypes as $t){
    $maxVal = max($maxVal, $regMap[$t] ?? 0);
}

$y = $chartY;

foreach($allTypes as $t){

    $reg = $regMap[$t] ?? 0;
    $del = $compMap[$t] ?? 0;

    $regW = ($reg / $maxVal) * $chartW;
    $delW = ($del / $maxVal) * $chartW;

    /* REGISTERED = VERDE */
    $pdf->SetFillColor(60,160,60);
    $pdf->Rect($chartX, $y, $regW, $barH, "F");

    /* DELIVERED = AZUL */
    $pdf->SetFillColor(70,130,180);
    $pdf->Rect($chartX, $y, $delW, $barH, "F");

    $pdf->SetFont('Arial','',8);
    $pdf->SetXY($chartX,$y);
    $pdf->Cell(0,$barH,utf8_decode($t),0,0);

    $y += $barH + $spacing;
}

$pdf->Ln(count($allTypes)*($barH+$spacing) + 10);

$pdf->table_header(
    ["Test Type","Registered","Delivered","Pending"],
    [80,30,30,30]
);

foreach($allTypes as $t){
    $r = $regMap[$t] ?? 0;
    $c = $compMap[$t] ?? 0;
    $p = $r - $c;
    $pdf->table_row([$t,$r,$c,$p],[80,30,30,30]);
}

$pdf->Ln(12);
/* ======================================================
   8. SECTION 8 — CLIENT SUMMARY (Top Clients)
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
        [$c['Client'], $c['requested'], $c['delivered'], $p],
        [70,30,30,30]
    );
}

$pdf->Ln(12);

/* ======================================================
   9. SECTION 9 — NCR (FULL LIST)
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
