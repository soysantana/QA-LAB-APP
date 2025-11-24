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
        SELECT COUNT(*) total FROM {$table}
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

function tests_completed($start,$end){
    return find_by_sql("
        SELECT Test_Type, COUNT(*) total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type
        ORDER BY total DESC
    ");
}

function tests_registered_expanded($start,$end){
    $rows = find_by_sql("
        SELECT Sample_ID, Sample_Number, Test_Type
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $map = [];

    foreach($rows as $r){
        $tests = explode(',', strtoupper(trim($r['Test_Type'])));
        foreach($tests as $t){
            $t = trim($t);
            if($t=='') continue;

            if(!isset($map[$t])) $map[$t]=0;
            $map[$t]++;
        }
    }

    return $map;
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

function pending_tests_expanded($start,$end){

    $reqs = find_by_sql("
        SELECT *
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $pend = [];

    foreach($reqs as $r){

        $tests = explode(',', strtoupper(trim($r['Test_Type'])));

        foreach($tests as $t){
            $t = trim($t);
            if($t=='') continue;

            $exists = find_by_sql("
                SELECT 1 FROM (
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_preparation
                    UNION ALL
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_realization
                    UNION ALL
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_delivery
                ) x
                WHERE x.Sample_ID     = '{$r['Sample_ID']}'
                AND   x.Sample_Number = '{$r['Sample_Number']}'
                AND   x.Test_Type     = '{$t}'
                LIMIT 1
            ");

            if(empty($exists)){
                $pend[] = [
                    'Sample' => $r['Sample_ID']."-".$r['Sample_Number'],
                    'Client' => $r['Client'],
                    'Test'   => $t,
                    'Date'   => $r['Registed_Date'],
                ];
            }
        }
    }

    return $pend;
}

function lead_time_tests($start,$end){
    return find_by_sql("
        SELECT
            p.Test_Type,
            DATEDIFF(d.Register_Date, p.Register_Date) AS lead_days
        FROM test_preparation p
        INNER JOIN test_delivery d
        ON p.Sample_ID = d.Sample_ID
        AND p.Sample_Number = d.Sample_Number
        AND p.Test_Type = d.Test_Type
        WHERE p.Register_Date BETWEEN '{$start}' AND '{$end}'
    ");
}

function ncr_month($start,$end){
    return find_by_sql("
        SELECT *
        FROM ensayos_reporte
        WHERE TRIM(Noconformidad)<>'' 
        AND Report_Date BETWEEN '{$start}' AND '{$end}'
    ");
}

/* ======================================================
   3. AUXILIARES
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

/* ======================================================
   4. PDF CLASS
====================================================== */

class PDF_MONTHLY extends FPDF {

    public $current_table_header = null;

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

/* NUEVA PÁGINA */
$pdf->AddPage();

/* ======================================================
   SECCIÓN 2 — EXECUTIVE SUMMARY
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
"));
$pdf->Ln(5);

/* ======================================================
   SECCIÓN 3 — KPIs GENERALES
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
   SECCIÓN 4 — LAST 6 MONTHS TREND (LINE CHART)
====================================================== */

$pdf->section_title("4. Trend - Last 6 Months");

$trend = last_6_months();

/* Si no hay datos, evitar error */
if (empty($trend)){
    $pdf->SetFont('Arial','I',10);
    $pdf->Cell(0,10,"No data available",0,1,'C');
} else {

    $chartX = 20;
    $chartY = $pdf->GetY()+10;
    $chartW = 170;
    $chartH = 50;

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $months = count($trend);
    $step = ($chartW-20)/max($months-1,1);

    $x = $chartX+10;
    $maxVal = max(array_column($trend,'total'));

    foreach($trend as $i=>$t){
        $h = ($t['total']/$maxVal)*($chartH-5);
        $y = $chartY + ($chartH - $h);

        $pdf->SetFillColor(50,120,240);
        $pdf->Rect($x-1,$y-1,3,3,'F');

        if($i>0){ $pdf->Line($prev_x,$prev_y,$x,$y); }

        $prev_x=$x;
        $prev_y=$y;

        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($x-10,$chartY+$chartH+3);
        $pdf->MultiCell(20,3,$t['mes'],0,'C');

        $x += $step;
    }

    $pdf->Ln($chartH + 20);
}
/* ======================================================
   SECCIÓN 5 — PENDING TESTS (LISTA EXPANDIDA)
====================================================== */

$pdf->section_title("5. Pending Tests (Monthly)");

$pend = pending_tests_expanded($start_str,$end_str);

$pdf->table_header(
    ["Sample","Test","Client","Date"],
    [60,40,50,30]
);

foreach ($pend as $p){
    $pdf->table_row([
        $p['Sample'],
        $p['Test'],
        $p['Client'],
        date("d-M", strtotime($p['Date']))
    ], [60,40,50,30]);
}

$pdf->Ln(12);

/* ======================================================
   SECCIÓN 6 — LEAD TIME BY TEST TYPE (AVG DAYS)
====================================================== */

$pdf->section_title("6. Lead Time by Test Type (Avg Days)");

$lt = lead_time_tests($start_str,$end_str);

$ltMap = [];

foreach($lt as $row){
    $t = strtoupper($row['Test_Type']);
    if(!isset($ltMap[$t])) $ltMap[$t] = ['sum'=>0,'count'=>0];
    $ltMap[$t]['sum'] += $row['lead_days'];
    $ltMap[$t]['count']++;
}

$leadFinal = [];
foreach($ltMap as $type=>$data){
    $leadFinal[] = [
        'type'=>$type,
        'avg'=>round($data['sum']/$data['count'],2)
    ];
}

if(empty($leadFinal)){
    $pdf->SetFont('Arial','I',10);
    $pdf->Cell(0,10,"No data for Lead Time",0,1,'C');
} else {

    /* GRAFICO DE BARRAS */
    $chartX = 20;
    $chartY = $pdf->GetY()+5;
    $chartW = 170;
    $chartH = 55;

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $barWidth = 15;
    $space    = 8;
    $x        = $chartX + 10;

    $maxVal = max(array_column($leadFinal,'avg'));

    foreach($leadFinal as $r){

        $h = ($r['avg']/$maxVal)*($chartH-5);
        $y = $chartY + ($chartH - $h);

        $pdf->SetFillColor(50,150,250);
        $pdf->Rect($x,$y,$barWidth,$h,'F');

        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($x-5,$chartY+$chartH+2);
        $pdf->MultiCell($barWidth+5,3,$r['type'],0,'C');

        $x += $barWidth + $space;
    }

    $pdf->Ln($chartH + 20);

    /* TABLA */
    $pdf->table_header(["Test Type","Avg Lead Time (days)"],[100,40]);
    foreach($leadFinal as $r){
        $pdf->table_row([$r['type'],$r['avg']], [100,40]);
    }
}

$pdf->Ln(12);

/* ======================================================
   SECCIÓN 7 — PENDING TESTS BREAKDOWN BY TYPE
====================================================== */

$pdf->section_title("7. Pending Tests Breakdown by Type");

$typeCount = [];

foreach($pend as $p){
    $t = strtoupper($p['Test']);
    if(!isset($typeCount[$t])) $typeCount[$t]=0;
    $typeCount[$t]++;
}

arsort($typeCount);

/* GRAFICO */

$chartX = 20;
$chartY = $pdf->GetY()+8;
$chartW = 175;
$chartH = 55;

$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

$barW = 12;
$space = 6;
$x = $chartX + 10;

$maxV = empty($typeCount) ? 1 : max($typeCount);

foreach($typeCount as $t=>$v){
    $h = ($v/$maxV)*($chartH-5);
    $y = $chartY + ($chartH-$h);

    $pdf->SetFillColor(240,90,90);
    $pdf->Rect($x,$y,$barW,$h,'F');

    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($x-5,$chartY+$chartH+2);
    $pdf->MultiCell($barW+5,3,$t,0,'C');

    $x += $barW + $space;
}

$pdf->Ln($chartH + 20);

/* TABLA */
$pdf->table_header(["Test Type","Pending"],[80,40]);

foreach($typeCount as $t=>$v){
    $pdf->table_row([$t,$v],[80,40]);
}

$pdf->Ln(12);
/* ======================================================
   SECCIÓN 8 — NCR SUMMARY
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

$pdf->Ln(12);

/* ======================================================
   SECCIÓN 9 — RESPONSIBLE
====================================================== */

$pdf->section_title("9. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(60,8,"Report prepared by",1);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Monthly_Lab_Report_{$year}_{$month}.pdf");
?>
