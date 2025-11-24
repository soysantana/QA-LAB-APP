<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
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
   2. FUNCIONES SQL
============================================ */

function get_count($table,$field,$start,$end){
    $r = find_by_sql("
        SELECT COUNT(*) total
        FROM {$table}
        WHERE {$field} BETWEEN '{$start}' AND '{$end}'
    ");
    return (int)$r[0]['total'];
}

function monthly_top_client($start,$end){
    $r = find_by_sql("
        SELECT Client, COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Client
        ORDER BY total DESC LIMIT 1
    ");
    return $r ? $r[0] : ['Client'=>'N/A','total'=>0];
}

function last_6_months(){
    return find_by_sql("
        SELECT DATE_FORMAT(Registed_Date,'%Y-%m') AS mes,
               COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date >= DATE_SUB(CURDATE(),INTERVAL 6 MONTH)
        GROUP BY mes
        ORDER BY mes ASC
    ");
}

/* TEST TYPE EXPANDIDO */
function tests_registered_expanded($start,$end){
    $rows = find_by_sql("
        SELECT Test_Type
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $map = [];
    foreach($rows as $r){
        foreach(explode(",",strtoupper($r["Test_Type"])) as $t){
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
        $t=strtoupper(trim($r['Test_Type']));
        if(!$t) continue;
        if(!isset($map[$t])) $map[$t]=0;
        $map[$t]++;
    }
    return $map;
}

/* PENDING REAL FINAL */
function get_pending_tests($start,$end){

    $reqs = find_by_sql("
        SELECT Sample_ID,Sample_Number,Client,Structure,Test_Type,Registed_Date
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $pend = [];

    foreach($reqs as $r){
        $tests = explode(",", strtoupper(trim($r['Test_Type'])));

        foreach($tests as $t){
            $t = trim($t);
            if(!$t) continue;

            $exists = find_by_sql("
                SELECT 1 FROM (
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_preparation
                    UNION ALL
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_realization
                    UNION ALL
                    SELECT Sample_ID,Sample_Number,Test_Type FROM test_delivery
                ) x
                WHERE x.Sample_ID='{$r['Sample_ID']}'
                  AND x.Sample_Number='{$r['Sample_Number']}'
                  AND x.Test_Type='{$t}'
                LIMIT 1
            ");

            if(empty($exists)){
                $pend[]=[
                    'Sample_ID'=>$r['Sample_ID'],
                    'Sample_Number'=>$r['Sample_Number'],
                    'Client'=>$r['Client'],
                    'Structure'=>$r['Structure'],
                    'Test_Type'=>$t,
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
   3. FUNC AUX
============================================ */

function multiline($pdf,$data,$w){
    $pdf->SetFont('Arial','',9);
    $maxH = 6;

    foreach($data as $i=>$txt){
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i]-2,1);
        $h = max(ceil($nb)*5,7);
        if($h>$maxH) $maxH=$h;
    }

    if($pdf->GetY()+$maxH > $pdf->PageBreakTrigger){
        $pdf->AddPage();
    }

    foreach($data as $i=>$txt){
        $x=$pdf->GetX();
        $y=$pdf->GetY();
        $pdf->MultiCell($w[$i],5,utf8_decode($txt),1,'L');
        $pdf->SetXY($x+$w[$i],$y);
    }

    $pdf->Ln($maxH);
}

function graph_page_break($pdf,$need=120){
    if($pdf->GetY()+$need > 250){
        $pdf->AddPage();
    }
}

function graph_legend($pdf,$items){
    $pdf->SetFont('Arial','',8);
    foreach($items as $it){
        $pdf->SetFillColor($it['r'],$it['g'],$it['b']);
        $pdf->Rect($pdf->GetX(),$pdf->GetY(),4,4,'F');
        $pdf->Cell(25,4," ".$it['label'],0,1);
    }
    $pdf->Ln(3);
}

/* ============================================
   4. CLASS PDF
============================================ */
class PDF_MONTHLY extends FPDF {

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
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',9);
        foreach($data as $i=>$txt){
            $this->Cell($w[$i],7,utf8_decode($txt),1,0,'C');
        }
        $this->Ln();
    }
}


/* ============================================
   5. PORTADA
============================================ */
$pdf = new PDF_MONTHLY();
$pdf->AddPage();

if(file_exists("../assets/img/Pueblo-Viejo.jpg")){
    $pdf->Image("../assets/img/Pueblo-Viejo.jpg",10,10,55);
}

$pdf->SetY(45);
$pdf->SetFont('Arial','B',22);
$pdf->Cell(0,12,"MONTHLY LABORATORY REPORT",0,1,'C');

$pdf->SetFont('Arial','',14);
$pdf->Cell(0,10,date("F Y",strtotime($start_str)),0,1,'C');

$pdf->Ln(15);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Pueblo Viejo Mine – TSF Laboratory",0,1,'C');
$pdf->Cell(0,8,"Prepared by: ".utf8_decode($responsable),0,1,'C');


/* ============================================
   6. EXEC SUMMARY
============================================ */
$pdf->AddPage();
$pdf->section_title("1. Executive Summary");

$total_reg = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$total_del = get_count("test_delivery","Register_Date",$start_str,$end_str);
$total_ncr = count(ncr_month($start_str,$end_str));
$top_client = monthly_top_client($start_str,$end_str);

$pdf->SetFont('Arial','',11);

$pdf->MultiCell(0,7,utf8_decode("
During this month, the laboratory maintained a steady operational flow across all stages. 
A total of {$total_reg} tests were registered and {$total_del} were delivered. 
Non-conformities reported: {$total_ncr}.  
The client with the highest sample demand was {$top_client['Client']} with {$top_client['total']} samples.
"));

/* ============================================
   7. KPIs
============================================ */
$pdf->section_title("2. Monthly KPIs");

$pdf->SetFont('Arial','',10);
$pdf->Cell(60,8,"Registered",1);
$pdf->Cell(30,8,$total_reg,1,1);

$pdf->Cell(60,8,"Delivered",1);
$pdf->Cell(30,8,$total_del,1,1);

$pdf->Cell(60,8,"Pending",1);
$pdf->Cell(30,8,$total_reg - $total_del,1,1);

$pdf->Ln(10);
?>
<?php
/* ============================================
   8. WORKLOAD OVERVIEW (Sección 3)
============================================ */

$pdf->section_title("3. Workload & Performance");

/* Obtener datos */
$weeks = find_by_sql("
    SELECT 
      WEEK(Registed_Date,1) AS w,
      COUNT(*) AS total
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY w
    ORDER BY w
");

$weeks_del = find_by_sql("
    SELECT 
      WEEK(Register_Date,1) AS w,
      COUNT(*) AS total
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY w
    ORDER BY w
");

/* Convertir a mapas */
$weekR=[]; foreach($weeks as $r) $weekR[$r['w']]=$r['total'];
$weekD=[]; foreach($weeks_del as $r) $weekD[$r['w']]=$r['total'];

/* Merge weeks */
$allW = array_unique(array_merge(array_keys($weekR),array_keys($weekD)));
sort($allW);

/* GRAFICO */
graph_page_break($pdf,120);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,"Weekly Workload (Registered vs Delivered)",0,1);

$chartX=20; $chartY=$pdf->GetY()+8;
$chartH=60; $barW=12; $space=10;

/* encontrar max */
$maxVal=1;
foreach($allW as $w){
    $maxVal=max($maxVal,$weekR[$w]??0,$weekD[$w]??0);
}

/* Ejes */
$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+160,$chartY+$chartH);

/* Dibujar */
$x=$chartX+5;

foreach($allW as $w){

    // verde registered
    $reg=$weekR[$w]??0;
    $h1=($reg/$maxVal)*($chartH-5);
    $y1=$chartY+($chartH-$h1);

    $pdf->SetFillColor(40,180,90);
    $pdf->Rect($x,$y1,$barW,$h1,"F");

    // azul delivered
    $del=$weekD[$w]??0;
    $h2=($del/$maxVal)*($chartH-5);
    $y2=$chartY+($chartH-$h2);

    $pdf->SetFillColor(50,100,220);
    $pdf->Rect($x+$barW+2,$y2,$barW,$h2,"F");

    // etiqueta abajo
    $pdf->SetFont('Arial','',8);
    $pdf->SetXY($x-2,$chartY+$chartH+2);
    $pdf->MultiCell($barW*2+2,4,"W".$w,0,'C');

    $x+=($barW*2+$space);
}

$pdf->Ln($chartH+10);

/* LEYENDA */
graph_legend($pdf,[
    ['label'=>'Registered','r'=>40,'g'=>180,'b'=>90],
    ['label'=>'Delivered','r'=>50,'g'=>100,'b'=>220],
]);

/* ============================================
   SECTION 4 – TEST TYPE DISTRIBUTION (Pie Chart)
============================================ */

$pdf->section_title("4. Test Type Distribution");

$regMap = tests_registered_expanded($start_str,$end_str);

/* Sumar total */
$totalReg = array_sum($regMap);
if($totalReg==0) $totalReg=1;

/* PIE CHART */
graph_page_break($pdf,110);

$cx = 105;
$cy = $pdf->GetY()+50;
$r  = 35;

$start_angle = 0;

$colors = [
    [200,30,30],[30,140,30],[30,30,200],[200,120,0],[130,30,150],
    [20,160,160],[160,20,120],[80,80,255],[50,200,50]
];
$ci=0;

foreach($regMap as $type=>$val){

    $angle = ($val/$totalReg)*360;
    $end_angle = $start_angle + $angle;

    $col = $colors[$ci % count($colors)];

    // draw sector
    $pdf->SetFillColor($col[0],$col[1],$col[2]);
    for($i=$start_angle;$i<$end_angle;$i+=2){
        $x=$cx + $r*cos(deg2rad($i));
        $y=$cy + $r*sin(deg2rad($i));
        $pdf->Line($cx,$cy,$x,$y);
    }

    // leyenda
    $pdf->SetXY($cx+50,$cy-40+($ci*6));
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(4,4,'',1,0,'',false);
    $pdf->SetFillColor($col[0],$col[1],$col[2]);
    $pdf->Rect($cx+50,$cy-40+($ci*6),4,4,'F');
    $pdf->Cell(0,4," ".$type,0,1);

    $start_angle = $end_angle;
    $ci++;
}

$pdf->Ln(20);

/* TABLA */
$pdf->table_header(["Test Type","Registered"],[80,40]);
foreach($regMap as $t=>$v){
    $pdf->table_row([$t,$v],[80,40]);
}

$pdf->Ln(10);
?>
<?php
/* ============================================
   SECTION 5 — Pending Tests
============================================ */

$pdf->section_title("5. Pending Tests");

$pend = get_pending_tests($start_str,$end_str);

$pdf->table_header(["Sample","Test","Client","Date"],[55,40,50,30]);

foreach($pend as $p){
    multiline($pdf,[
        $p['Sample_ID']."-".$p['Sample_Number'],
        $p['Test_Type'],
        $p['Client'],
        date("d-M",strtotime($p['Date']))
    ],[55,40,50,30]);
}

$pdf->Ln(10);


/* ============================================
   SECTION 6 — NCR
============================================ */

$pdf->section_title("6. Non-Conformities / Observations");

$ncr = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observation"],[50,140]);

foreach($ncr as $n){
    multiline($pdf,[
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ],[50,140]);
}

$pdf->Ln(10);


/* ============================================
   SECTION 7 — Responsible
============================================ */

$pdf->section_title("7. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,8,"Report prepared by",1);
$pdf->Cell(130,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Monthly_Report_{$year}_{$month}.pdf");
?>
