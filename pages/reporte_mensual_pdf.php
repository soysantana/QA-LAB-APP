<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors',1);
error_reporting(E_ALL);

/* ============================================
   PARAMETROS
============================================ */
$year  = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');
$month = isset($_GET['mes'])  ? (int)$_GET['mes']  : date('n');

$start_str = "{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01 00:00:00";
$end_str   = date("Y-m-t 23:59:59", strtotime($start_str));

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';


/* ============================================
   CONSULTAS SQL
============================================ */

function get_count($table,$field,$start,$end){
    $r = find_by_sql("
        SELECT COUNT(*) total
        FROM {$table}
        WHERE {$field} BETWEEN '{$start}' AND '{$end}'
    ");
    return (int)$r[0]['total'];
}

function tests_registered_expanded($start,$end){
    $rows = find_by_sql("
        SELECT Test_Type
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $map=[];
    foreach($rows as $r){
        $types = explode(',', strtoupper($r['Test_Type']));
        foreach($types as $t){
            $t = trim($t);
            if(!$t) continue;
            if(!isset($map[$t])) $map[$t]=0;
            $map[$t]++;
        }
    }
    return $map;
}

function tests_delivered_expanded($start,$end){
    $rows = find_by_sql("
        SELECT Test_Type
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
    ");
    $map=[];
    foreach($rows as $r){
        $t = strtoupper(trim($r['Test_Type']));
        if(!$t) continue;
        if(!isset($map[$t])) $map[$t]=0;
        $map[$t]++;
    }
    return $map;
}

function last_6_months(){
    return find_by_sql("
        SELECT DATE_FORMAT(Registed_Date,'%Y-%m') AS m, COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date >= DATE_SUB(CURDATE(),INTERVAL 6 MONTH)
        GROUP BY m ORDER BY m ASC
    ");
}

function get_pending_tests($start,$end){
    $reqs = find_by_sql("
        SELECT Sample_ID,Sample_Number,Client,Structure,Test_Type,Registed_Date
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $pend=[];

    foreach($reqs as $r){
        $types = explode(',', strtoupper(trim($r['Test_Type'])));
        foreach($types as $t){
            $t=trim($t);
            if(!$t) continue;

            $exists = find_by_sql("
                SELECT 1 FROM(
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_preparation
                    UNION ALL
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_realization
                    UNION ALL
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_delivery
               )x
                WHERE x.Sample_ID='{$r['Sample_ID']}'
                AND x.Sample_Number='{$r['Sample_Number']}'
                AND x.Test_Type='{$t}'
                LIMIT 1
            ");

            if(empty($exists)){
                $pend[]=[
                    'Sample_ID'=>$r['Sample_ID'],
                    'Sample_Number'=>$r['Sample_Number'],
                    'Test_Type'=>$t,
                    'Client'=>$r['Client'],
                    'Date'=>$r['Registed_Date']
                ];
            }
        }
    }
    return $pend;
}

function ncr_month($start,$end){
    return find_by_sql("
        SELECT *
        FROM ensayos_reporte
        WHERE Noconformidad IS NOT NULL
        AND TRIM(Noconformidad)<>'' 
        AND Report_Date BETWEEN '{$start}' AND '{$end}'
    ");
}


function create_bar_chart($data1,$data2,$labels,$filename){

    // --- Prevención total de arrays vacíos ---
    if (empty($labels) || empty($data1) || empty($data2)) {
        // Crear un PNG vacío que diga "No data"
        $im = imagecreatetruecolor(600,200);
        $white = imagecolorallocate($im,255,255,255);
        $black = imagecolorallocate($im,0,0,0);
        imagefilledrectangle($im,0,0,600,200,$white);
        imagestring($im,5,200,90,"NO DATA AVAILABLE",$black);
        imagepng($im,$filename);
        imagedestroy($im);
        return;
    }

    // ---- Gráfico normal ----
    $w=700; 
    $h=400;
    $im=imagecreatetruecolor($w,$h);

    $white=imagecolorallocate($im,255,255,255);
    $black=imagecolorallocate($im,0,0,0);
    $blue=imagecolorallocate($im,50,100,220);
    $green=imagecolorallocate($im,40,180,90);

    imagefilledrectangle($im,0,0,$w,$h,$white);

    // --- Prevención adicional ---
    $max = max(max($data1), max($data2));
    if ($max <= 0) $max = 1;

    $barW = 20;
    $gap  = 40;
    $x = 80;
    $base = $h - 60;

    for($i=0;$i<count($labels);$i++){
        $h1 = ($data1[$i] / $max) * 250;
        $h2 = ($data2[$i] / $max) * 250;

        imagefilledrectangle($im,$x,$base-$h1,$x+$barW,$base,$green);
        imagefilledrectangle($im,$x+$barW+5,$base-$h2,$x+$barW+5+$barW,$base,$blue);

        imagestring($im,3,$x,$base+5,$labels[$i],$black);
        $x += ($barW*2 + $gap);
    }

    imagepng($im,$filename);
    imagedestroy($im);
}


function create_pie_chart($data,$labels,$filename){
    $w=500; $h=500;
    $im=imagecreatetruecolor($w,$h);
    $white=imagecolorallocate($im,255,255,255);
    imagefill($im,0,0,$white);

    $colors=[
        [200,30,30],[30,140,30],[30,30,200],[200,120,0],
        [150,40,140],[100,180,40],[60,60,160],[40,200,180]
    ];

    $total=array_sum($data);
    if($total==0) $total=1;

    $cx=250; $cy=250; $r=150;

    $start=0; $i=0;

    foreach($data as $val){
        $angle = ($val/$total)*360;
        $end = $start + $angle;

        $col=imagecolorallocate($im,
            $colors[$i%count($colors)][0],
            $colors[$i%count($colors)][1],
            $colors[$i%count($colors)][2]
        );

        imagefilledarc($im,$cx,$cy,$r*2,$r*2,$start,$end,$col,IMG_ARC_PIE);

        $start=$end;
        $i++;
    }

    imagepng($im,$filename);
    imagedestroy($im);
}
?>
<?php

/* ============================================
   CLASS PDF
============================================ */

class PDF_MONTHLY extends FPDF {

    function section($title){
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(230,235,255);
        $this->Cell(0,10,utf8_decode($title),0,1,'L',true);
        $this->Ln(3);
    }

    function table_header($cols,$w){
        $this->SetFont('Arial','B',10);
        foreach($cols as $i=>$c){
            $this->Cell($w[$i],8,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',9);
        foreach($data as $i=>$t){
            $this->Cell($w[$i],7,utf8_decode($t),1,0,'C');
        }
        $this->Ln();
    }
}

/* ============================================
   PDF
============================================ */

$pdf = new PDF_MONTHLY();
$pdf->AddPage();

/* PORTADA */
$pdf->Image("../assets/img/Pueblo-Viejo.jpg",10,10,55);
$pdf->SetY(45);

$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,12,"MONTHLY LABORATORY REPORT",0,1,'C');

$pdf->SetFont('Arial','',14);
$pdf->Cell(0,10,date("F Y",strtotime($start_str)),0,1,'C');

$pdf->Ln(15);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Pueblo Viejo Mine – TSF Laboratory",0,1,'C');
$pdf->Cell(0,8,"Prepared by: ".utf8_decode($responsable),0,1,'C');

$pdf->AddPage();

/* EXEC SUMMARY */
$pdf->section("1. Executive Summary");

$total_reg = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$total_del = get_count("test_delivery","Register_Date",$start_str,$end_str);
$total_ncr = count(ncr_month($start_str,$end_str));

$pdf->SetFont('Arial','',11);

$pdf->MultiCell(0,7,utf8_decode("
During this month, laboratory operations showed a consistent workflow across all main stages (registration, preparation, and delivery). 
A total of {$total_reg} tests were registered and {$total_del} were delivered. 
A total of {$total_ncr} non-conformities were reported during the month.
"));

$pdf->Ln(5);

/* KPIs */
$pdf->section("2. Monthly KPIs");

$pdf->table_header(["Metric","Total"],[70,40]);
$pdf->table_row(["Registered",$total_reg],[70,40]);
$pdf->table_row(["Delivered",$total_del],[70,40]);
$pdf->table_row(["Pending",$total_reg - $total_del],[70,40]);

$pdf->Ln(10);
?>
<?php

/* ============================================
   SECTION 3 – Workload Chart
============================================ */

$pdf->section("3. Workload Overview");

$weeksR = find_by_sql("
    SELECT WEEK(Registed_Date,1) w, COUNT(*) total
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY w ORDER BY w
");
$weeksD = find_by_sql("
    SELECT WEEK(Register_Date,1) w, COUNT(*) total
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY w ORDER BY w
");

// --- FIX: garantizar que no estén vacíos ---
if (empty($labels)) {
    $labels = ["No Data"];
    $dr     = [0];
    $dd     = [0];
}


foreach($weeksR as $r){
    $labels[] = "W".$r['w'];
    $dr[]     = $r['total'];
    $dd[]     = 0;
}
foreach($weeksD as $d){
    $idx = array_search("W".$d['w'],$labels);
    if($idx !== false) $dd[$idx] = $d['total'];
}

$tmp1 = "../uploads/chart_workload.png";
create_bar_chart($dr,$dd,$labels,$tmp1);

$pdf->Image($tmp1,20,$pdf->GetY(),170);
$pdf->Ln(120);


/* ============================================
   SECTION 4 – Test Type Distribution
============================================ */

$pdf->section("4. Test Type Distribution");

$regMap = tests_registered_expanded($start_str,$end_str);
$labels = array_keys($regMap);
$vals   = array_values($regMap);

$tmp2 = "../uploads/chart_tests.png";
create_pie_chart($vals,$labels,$tmp2);

$pdf->Image($tmp2,30,$pdf->GetY(),150);
$pdf->Ln(160);

$pdf->table_header(["Test Type","Registered"],[80,40]);
foreach($regMap as $t=>$v){
    $pdf->table_row([$t,$v],[80,40]);
}

$pdf->Ln(10);


/* ============================================
   SECTION 5 – Pending Tests
============================================ */

$pdf->section("5. Pending Tests");

$pend = get_pending_tests($start_str,$end_str);

$pdf->table_header(["Sample","Test","Client","Date"],[55,40,50,30]);

foreach($pend as $p){
    $pdf->table_row([
        $p['Sample_ID']."-".$p['Sample_Number'],
        $p['Test_Type'],
        $p['Client'],
        date("d-M",strtotime($p['Date']))
    ],[55,40,50,30]);
}

$pdf->Ln(10);


/* ============================================
   SECTION 6 – NCR
============================================ */

$pdf->section("6. Non-Conformities / Observations");

$ncr = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observation"],[50,140]);

foreach($ncr as $n){
    $pdf->table_row([
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ],[50,140]);
}

$pdf->Ln(10);


/* ============================================
   OUTPUT
============================================ */
ob_end_clean();
$pdf->Output("I","Monthly_Report_{$year}_{$month}.pdf");
?>
