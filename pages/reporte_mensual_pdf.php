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
   SQL FUNCTIONS
============================================ */

function get_count($table,$field,$start,$end){
    $r = find_by_sql("
        SELECT COUNT(*) total
        FROM {$table}
        WHERE {$field} BETWEEN '{$start}' AND '{$end}'
    ");
    return (int)$r[0]['total'];
}

function tests_expanded($rows){
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

function tests_registered_expanded($start,$end){
    $rows = find_by_sql("
        SELECT Test_Type
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");
    return tests_expanded($rows);
}

function tests_delivered_expanded($start,$end){
    $rows = find_by_sql("
        SELECT Test_Type
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
    ");
    return tests_expanded($rows);
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

function get_pending_tests($start,$end){

    $reqs = find_by_sql("
        SELECT Sample_ID,Sample_Number,Client,Structure,Test_Type,Registed_Date
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $pend=[];

    foreach($reqs as $r){
        $types = explode(',', strtoupper($r['Test_Type']));
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

/* ============================================
   CHART ENGINE — PROFESIONAL + FIXED
============================================ */

function ensure_dir($filename){
    if(!file_exists(dirname($filename))){
        mkdir(dirname($filename),0777,true);
    }
}

function bar_chart_simple($labels,$series1,$series2,$filename){

    ensure_dir($filename);

    $labels = array_values($labels);
    $series1 = array_values($series1);
    $series2 = array_values($series2);

    $count = count($labels);
    if($count==0){
        $labels=["No Data"];
        $series1=[0];
        $series2=[0];
        $count=1;
    }

    $W = max(500, 80 + $count*60);
    $H = 340;

    $im = imagecreatetruecolor($W,$H);
    $white=imagecolorallocate($im,255,255,255);
    $black=imagecolorallocate($im,0,0,0);

    $green=imagecolorallocate($im,50,200,120);
    $blue =imagecolorallocate($im,50,100,230);

    imagefill($im,0,0,$white);

    $maxY = max(1, max($series1), max($series2));

    $x=60;
    for($i=0;$i<$count;$i++){
        $h1 = ($series1[$i] / $maxY) * 220;
        $h2 = ($series2[$i] / $maxY) * 220;

        imagefilledrectangle($im,$x,280-$h1,$x+22,280,$green);
        imagefilledrectangle($im,$x+28,280-$h2,$x+50,280,$blue);

        imagestring($im,3,$x,285,$labels[$i],$black);

        $x += 75;
    }

    imagepng($im,$filename);
    imagedestroy($im);
}

function pie_chart($labels,$values,$filename){

    ensure_dir($filename);

    if(empty($labels)){
        $labels=["No Data"];
        $values=[1];
    }

    $W=500; $H=500;
    $cx=250; $cy=250; $r=180;

    $im=imagecreatetruecolor($W,$H);
    $white=imagecolorallocate($im,255,255,255);
    imagefill($im,0,0,$white);

    $palette=[
        [220,50,50],[50,160,50],[50,70,200],[210,140,0],
        [160,40,140],[80,180,60],[60,60,160],[40,180,200]
    ];

    $total = array_sum($values);
    if($total<=0) $total=1;

    $start=0; $i=0;

    foreach($values as $v){
        $angle = ($v/$total)*360;
        $end = $start + $angle;

        $c = $palette[$i % count($palette)];
        $col=imagecolorallocate($im,$c[0],$c[1],$c[2]);

        imagefilledarc($im,$cx,$cy,$r*2,$r*2,$start,$end,$col,IMG_ARC_PIE);

        $start=$end;
        $i++;
    }

    imagepng($im,$filename);
    imagedestroy($im);
}
class PDF_MONTHLY extends FPDF{

    function section($t){
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(220,230,255);
        $this->Cell(0,10,utf8_decode($t),0,1,'L',true);
        $this->Ln(3);
    }

    function header_row($cols,$w){
        $this->SetFont('Arial','B',10);
        foreach($cols as $i=>$c){
            $this->Cell($w[$i],8,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
    }

    function row($data,$w){
        $this->SetFont('Arial','',9);
        foreach($data as $i=>$txt){
            $this->Cell($w[$i],7,utf8_decode($txt),1,0,'C');
        }
        $this->Ln();
    }

    function multirow($data,$w){
        $this->SetFont('Arial','',9);

        // altura calculada para multilinea
        $maxH = 7;
        foreach($data as $i=>$t){
            $len = strlen($t);
            $h = ceil($len/25)*5;
            if($h > $maxH) $maxH = $h;
        }

        if($this->GetY() + $maxH > 260){
            $this->AddPage();
        }

        foreach($data as $i=>$t){
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($w[$i],5,utf8_decode($t),1,'L');
            $this->SetXY($x + $w[$i], $y);
        }

        $this->Ln($maxH);
    }
}

/************************************************
 * PDF START – PORTADA
 ************************************************/

$pdf = new PDF_MONTHLY();
$pdf->AddPage();

if(file_exists("../assets/img/Pueblo-Viejo.jpg")){
    $pdf->Image("../assets/img/Pueblo-Viejo.jpg",10,10,55);
}

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

/************************************************
 * 1. EXECUTIVE SUMMARY
 ************************************************/

$pdf->section("1. Executive Summary");

$reg = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$prep = get_count("test_preparation","Register_Date",$start_str,$end_str);
$real = get_count("test_realization","Register_Date",$start_str,$end_str);
$del  = get_count("test_delivery","Register_Date",$start_str,$end_str);
$ncr  = count(ncr_month($start_str,$end_str));

$pdf->SetFont('Arial','',11);

$summary = "
During this month, the laboratory maintained a stable operational workflow with balanced activity across registration, preparation, realization, and delivery phases.

A total of {$reg} tests were registered, {$prep} entered preparation, {$real} were executed, and {$del} were completed and delivered.  
A total of {$ncr} non-conformities were recorded and addressed.

The overall workload distribution remained consistent among clients and test types, with minimal operational bottlenecks.
";

$pdf->MultiCell(0,7,utf8_decode($summary));
$pdf->Ln(5);

/************************************************
 * 2. KPIs
 ************************************************/

$pdf->section("2. Monthly KPIs");

$pdf->header_row(["Metric","Total"],[70,40]);
$pdf->row(["Registered",$reg],[70,40]);
$pdf->row(["Preparation",$prep],[70,40]);
$pdf->row(["Realization",$real],[70,40]);
$pdf->row(["Delivered",$del],[70,40]);
$pdf->row(["Pending",max(0,$reg-$del)],[70,40]);

$pdf->Ln(10);

/************************************************
 * 3. WORKLOAD – WEEKLY
 ************************************************/

$pdf->section("3. Workload Overview (Weekly)");

$R = find_by_sql("
    SELECT WEEK(Registed_Date,1) w, COUNT(*) total
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY w ORDER BY w
");
$D = find_by_sql("
    SELECT WEEK(Register_Date,1) w, COUNT(*) total
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY w ORDER BY w
");

$labels=[]; $dr=[]; $dd=[];

foreach($R as $r){
    $labels[]="W".$r['w'];
    $dr[]=$r['total'];
    $dd[]=0;
}
foreach($D as $d){
    $idx=array_search("W".$d['w'],$labels);
    if($idx!==false){
        $dd[$idx]=$d['total'];
    }
}

$tmp1="../uploads/chart_workload.png";
bar_chart_simple($labels,$dr,$dd,$tmp1);
$pdf->Image($tmp1,15,$pdf->GetY(),180);
$pdf->Ln(120);

/************************************************
 * 4. TEST TYPE DISTRIBUTION PER CLIENT
 ************************************************/

$pdf->section("4. Test Type Distribution by Client");

$rows = find_by_sql("
    SELECT Client, Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$map=[];

foreach($rows as $r){
    $client = ($r['Client'] ?: "UNKNOWN");
    if(!isset($map[$client])) $map[$client]=[];

    $types = explode(",",strtoupper($r['Test_Type']));
    foreach($types as $t){
        $t=trim($t);
        if($t==="") continue;
        if(!isset($map[$client][$t])) $map[$client][$t]=0;
        $map[$client][$t]++;
    }
}

$all_tests=[];
foreach($map as $c=>$arr){
    foreach(array_keys($arr) as $tt){
        $all_tests[$tt]=true;
    }
}
$all_tests=array_keys($all_tests);
sort($all_tests);

/************* TABLE *************/
$colW = array_merge([40], array_fill(0,count($all_tests),18),[20]);
$header = array_merge(["Client"],$all_tests,["Total"]);

$pdf->header_row($header,$colW);

foreach($map as $client=>$arr){
    $row=[];
    $row[]=$client;
    $sum=0;

    foreach($all_tests as $t){
        $v = $arr[$t] ?? 0;
        $sum += $v;
        $row[]=$v;
    }

    $row[]=$sum;
    $pdf->row($row,$colW);
}

$pdf->Ln(10);

/************* PIE CHART *************/
$client_names = array_keys($map);
$client_totals = [];

foreach($map as $client=>$tarr){
    $client_totals[] = array_sum($tarr);
}

$tmp2="../uploads/chart_clients_pie.png";
pie_chart($client_names,$client_totals,$tmp2);

$pdf->Image($tmp2,30,$pdf->GetY(),150);
$pdf->Ln(170);
/************************************************
 * 5. PENDING TESTS
 ************************************************/

$pdf->section("5. Pending Tests");

$pend = get_pending_tests($start_str,$end_str);

$pdf->header_row(["Sample","Test","Client","Date"],[55,40,50,30]);

foreach($pend as $p){
    $pdf->multirow([
        $p['Sample_ID']."-".$p['Sample_Number'],
        $p['Test_Type'],
        $p['Client'],
        date("d-M",strtotime($p['Date']))
    ],[55,40,50,30]);
}

$pdf->Ln(10);

/************************************************
 * 6. NCR
 ************************************************/

$pdf->section("6. Non-Conformities / Observations");

$ncr = ncr_month($start_str,$end_str);

$pdf->header_row(["Sample","Observation"],[50,140]);

foreach($ncr as $n){
    $pdf->multirow([
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ],[50,140]);
}

$pdf->Ln(10);

/************************************************
 * OUTPUT
 ************************************************/

ob_end_clean();
$pdf->Output("I","Monthly_Report_{$year}_{$month}.pdf");
?>
