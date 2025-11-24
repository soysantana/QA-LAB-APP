<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors',1);
error_reporting(E_ALL);

/* ============================================
   1. PARAMETROS
============================================ */
$year  = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');
$month = isset($_GET['mes'])  ? (int)$_GET['mes']  : date('n');

$start_str = "{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01 00:00:00";
$end_str   = date("Y-m-t 23:59:59", strtotime($start_str));

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* ============================================
   2. CONSULTAS SQL
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

function last_6_months(){
    return find_by_sql("
        SELECT DATE_FORMAT(Registed_Date,'%Y-%m') AS m, COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY m ORDER BY m ASC
    ");
}

/* PENDING FINAL */
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

function ncr_month($start,$end){
    return find_by_sql("
        SELECT *
        FROM ensayos_reporte
        WHERE Noconformidad IS NOT NULL
        AND TRIM(Noconformidad)<>'' 
        AND Report_Date BETWEEN '{$start}' AND '{$end}'
    ");
}

/* ============================================
   3. CHARTS (Bar + Pie)
============================================ */

function safe_chart_image($filename){
    if(!file_exists(dirname($filename))){
        mkdir(dirname($filename),0777,true);
    }
}

function create_bar_chart($data1,$data2,$labels,$filename){
    safe_chart_image($filename);

    if(empty($labels)){
        $labels=["No Data"];
        $data1=[0];
        $data2=[0];
    }

    $w=700; $h=400;
    $im=imagecreatetruecolor($w,$h);

    $white=imagecolorallocate($im,255,255,255);
    $black=imagecolorallocate($im,0,0,0);
    $blue=imagecolorallocate($im,50,100,220);
    $green=imagecolorallocate($im,40,180,90);

    imagefilledrectangle($im,0,0,$w,$h,$white);

    $max=max(1,max($data1),max($data2));
    $barW=22; $gap=45;
    $base=$h-60;
    $x=80;

    for($i=0;$i<count($labels);$i++){
        $h1=($data1[$i]/$max)*260;
        $h2=($data2[$i]/$max)*260;

        imagefilledrectangle($im,$x,$base-$h1,$x+$barW,$base,$green);
        imagefilledrectangle($im,$x+$barW+6,$base-$h2,$x+$barW+6+$barW,$base,$blue);

        imagestring($im,4,$x-4,$base+8,$labels[$i],$black);

        $x += ($barW*2 + $gap);
    }

    imagepng($im,$filename);
    imagedestroy($im);
}

function create_pie_chart($data,$labels,$filename){
    safe_chart_image($filename);

    $w=500;$h=500;
    $im=imagecreatetruecolor($w,$h);
    $white=imagecolorallocate($im,255,255,255);
    imagefill($im,0,0,$white);

    $colors=[
        [200,30,30],[30,140,30],[30,30,200],[200,120,0],
        [150,40,140],[100,180,40],[60,60,160],[40,200,180]
    ];

    $total=array_sum($data);
    if($total<=0) $total=1;

    $cx=250;$cy=250;$r=160;

    $start=0; $i=0;
    foreach($data as $v){
        $angle=($v/$total)*360;
        $end=$start+$angle;

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

/* ============================================
   4. PDF CLASS
============================================ */

class PDF_MONTHLY extends FPDF{

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

    function multiline($data,$w){
        $this->SetFont('Arial','',9);
        $maxH=6;

        foreach($data as $i=>$txt){
            $nb=$this->GetStringWidth(utf8_decode($txt))/max($w[$i]-2,1);
            $h=max(ceil($nb)*5,7);
            if($h>$maxH) $maxH=$h;
        }

        if($this->GetY()+$maxH > 260){
            $this->AddPage();
        }

        foreach($data as $i=>$txt){
            $x=$this->GetX();
            $y=$this->GetY();
            $this->MultiCell($w[$i],5,utf8_decode($txt),1,'L');
            $this->SetXY($x+$w[$i],$y);
        }
        $this->Ln($maxH);
    }
}

/* ============================================
   5. PDF – PORTADA
============================================ */

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

/* ============================================
   6. EXECUTIVE SUMMARY
============================================ */

$pdf->section("1. Executive Summary");

$reg = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$prep = get_count("test_preparation","Register_Date",$start_str,$end_str);
$real = get_count("test_realization","Register_Date",$start_str,$end_str);
$del  = get_count("test_delivery","Register_Date",$start_str,$end_str);
$ncr  = count(ncr_month($start_str,$end_str));

$pdf->SetFont('Arial','',11);
$pdf->MultiCell(0,7,utf8_decode("
During this month, the laboratory sustained a consistent operational workflow across registration, preparation, realization, and delivery stages.

A total of {$reg} tests were registered, {$prep} entered the preparation stage, {$real} were performed, and {$del} were completed and delivered.

A total of {$ncr} non-conformities were recorded and addressed during the period.

Overall, the laboratory maintained stable productivity with balanced distribution of workload across test types and clients.
"));

$pdf->Ln(5);

/* ============================================
   7. MONTHLY KPIs
============================================ */

$pdf->section("2. Monthly KPIs");

$pdf->table_header(["Metric","Total"],[70,40]);
$pdf->table_row(["Registered",$reg],[70,40]);
$pdf->table_row(["Preparation",$prep],[70,40]);
$pdf->table_row(["Realization",$real],[70,40]);
$pdf->table_row(["Delivered",$del],[70,40]);
$pdf->table_row(["Pending",$reg - $del],[70,40]);

$pdf->Ln(10);
?>
<?php
/* ============================================
   8. WORKLOAD – Weekly Registered vs Delivered
============================================ */

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
    if($idx!==false) $dd[$idx]=$d['total'];
}

$tmp1="../uploads/chart_workload.png";
create_bar_chart($dr,$dd,$labels,$tmp1);
$pdf->Image($tmp1,15,$pdf->GetY(),180);
$pdf->Ln(120);

/* ============================================
   9. TEST TYPE DISTRIBUTION BY CLIENT (Stacked)
============================================ */

$pdf->section("4. Test Type Distribution by Client");

$rows = find_by_sql("
    SELECT Client, Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$map=[];

/* Construir matriz: Client → Test_Type → Count */
foreach($rows as $r){
    $client = $r['Client'];
    if(!isset($map[$client])) $map[$client]=[];

    $types = explode(",",strtoupper($r['Test_Type']));
    foreach($types as $t){
        $t=trim($t);
        if(!$t) continue;
        if(!isset($map[$client][$t])) $map[$client][$t]=0;
        $map[$client][$t]++;
    }
}

/* Obtener todos los test types posibles */
$all_tests=[];
foreach($map as $c=>$arr){
    $all_tests = array_merge($all_tests,array_keys($arr));
}
$all_tests = array_unique($all_tests);
sort($all_tests);

/* === TABLA === */
$pdf->table_header(array_merge(["Client"],$all_tests,["Total"]), array_merge([40],array_fill(0,count($all_tests),18),[20]));

foreach($map as $client=>$arr){
    $row=[]; $row[]=$client;
    $sum=0;

    foreach($all_tests as $t){
        $v = $arr[$t] ?? 0;
        $sum += $v;
        $row[] = $v;
    }
    $row[]=$sum;

    $pdf->table_row($row, array_merge([40],array_fill(0,count($all_tests),18),[20]));
}

$pdf->Ln(10);

/* === GRÁFICO STACKED (PNG) === */

$labels2=array_keys($map);
$graphData=[];

foreach($labels2 as $client){
    $graphData[$client]=[];
    foreach($all_tests as $t){
        $graphData[$client][] = $map[$client][$t] ?? 0;
    }
}

/* Conversión para gráfico */
$bars_client=[];  
$bars_test=[];    

foreach($all_tests as $i=>$t){
    foreach($labels2 as $idx=>$c){
        if(!isset($bars_test[$i])) $bars_test[$i]=[];
        $bars_test[$i][] = $map[$c][$t] ?? 0;
    }
}

$tmp2="../uploads/chart_stacked_clients.png";
create_bar_chart(array_sum($bars_test) ? $bars_test[0] : [0], array_sum($bars_test) ? $bars_test[1] : [0], $labels2, $tmp2);

$pdf->Image($tmp2,15,$pdf->GetY(),180);
$pdf->Ln(120);
?>
<?php
/* ============================================
   10. PENDING TESTS
============================================ */

$pdf->section("5. Pending Tests");

$pend = get_pending_tests($start_str,$end_str);

$pdf->table_header(["Sample","Test","Client","Date"],[55,40,50,30]);

foreach($pend as $p){
    $pdf->multiline([
        $p['Sample_ID']."-".$p['Sample_Number'],
        $p['Test_Type'],
        $p['Client'],
        date("d-M",strtotime($p['Date']))
    ],[55,40,50,30]);
}

$pdf->Ln(10);

/* ============================================
   11. NCR
============================================ */

$pdf->section("6. Non-Conformities / Observations");

$ncr = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observation"],[50,140]);

foreach($ncr as $n){
    $pdf->multiline([
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ],[50,140]);
}

$pdf->Ln(10);


/* ============================================
   OUTPUT FINAL
============================================ */

ob_end_clean();
$pdf->Output("I","Monthly_Report_{$year}_{$month}.pdf");
?>
