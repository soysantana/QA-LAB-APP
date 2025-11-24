<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ============================
   PARAMETROS FECHA
============================ */
$year  = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');
$month = isset($_GET['mes'])  ? (int)$_GET['mes']  : date('n');

$start_str = "{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01 00:00:00";
$end_str   = date("Y-m-t 23:59:59", strtotime($start_str));

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* ============================
   FUNCIONES SQL
============================ */

function sql_val($q){
    $r = find_by_sql($q);
    return $r ? $r : [];
}

function get_count($table,$field,$start,$end){
    $r = sql_val("SELECT COUNT(*) total FROM {$table}
                  WHERE {$field} BETWEEN '{$start}' AND '{$end}'");
    return (int)$r[0]['total'];
}

function monthly_top_client($start,$end){
    $r = sql_val("
        SELECT Client, COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Client ORDER BY total DESC LIMIT 1
    ");
    return $r ? $r[0] : ['Client'=>'N/A','total'=>0];
}

function last_6_months(){
    return sql_val("
        SELECT DATE_FORMAT(Registed_Date,'%Y-%m') AS mes,
               COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date >= DATE_SUB(CURDATE(),INTERVAL 6 MONTH)
        GROUP BY mes ORDER BY mes ASC
    ");
}

function tests_registered_expanded($start,$end){
    $rows = sql_val("
        SELECT Test_Type
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $map=[];
    foreach($rows as $r){
        foreach(explode(",", strtoupper($r['Test_Type'])) as $t){
            $t = trim($t);
            if(!$t) continue;
            if(!isset($map[$t])) $map[$t]=0;
            $map[$t]++;
        }
    }
    return $map;
}

function tests_delivered_expanded($start,$end){
    $rows = sql_val("
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

function get_pending_tests($start,$end){

    $reqs = sql_val("
        SELECT Sample_ID,Sample_Number,Client,Structure,Test_Type,Registed_Date
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $pend=[];

    foreach($reqs as $r){
        $tests = explode(",", strtoupper(trim($r['Test_Type'])));

        foreach($tests as $t){
            $t = trim($t);
            if(!$t) continue;

            $exists = sql_val("
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
                    'Sample_ID'     => $r['Sample_ID'],
                    'Sample_Number' => $r['Sample_Number'],
                    'Client'        => $r['Client'],
                    'Structure'     => $r['Structure'],
                    'Test_Type'     => $t,
                    'Date'          => $r['Registed_Date']
                ];
            }
        }
    }

    return $pend;
}

function ncr_month($start,$end){
    return sql_val("
        SELECT *
        FROM ensayos_reporte
        WHERE Noconformidad IS NOT NULL
          AND TRIM(Noconformidad)<>'' 
          AND Report_Date BETWEEN '{$start}' AND '{$end}'
    ");
}

/* ============================
   FUNC AUX
============================ */

function multiline($pdf,$data,$w){
    $pdf->SetFont('Arial','',9);
    $maxH = 6;

    foreach($data as $i=>$txt){
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i]-2,1);
        $h  = max(ceil($nb)*5,7);
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

/* ============================
   CLASS PDF
============================ */
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

/* ============================
   PORTADA
============================ */
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

/* ============================
   EXECUTIVE SUMMARY
============================ */
$pdf->AddPage();
$pdf->section_title("1. Executive Summary");

$total_reg = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$total_del = get_count("test_delivery","Register_Date",$start_str,$end_str);
$total_ncr = count(ncr_month($start_str,$end_str));
$top_client = monthly_top_client($start_str,$end_str);

$pdf->SetFont('Arial','',11);

$pdf->MultiCell(0,7,utf8_decode("
During this reporting period, the laboratory maintained a stable workflow and consistent operational performance.

• Total samples registered: {$total_reg}
• Total tests delivered: {$total_del}
• Total pending tests: ".($total_reg - $total_del)."
• Non-conformities reported: {$total_ncr}
• Client with highest demand: {$top_client['Client']} ({$top_client['total']} samples)

Overall, laboratory operations remained balanced across sample reception and test execution stages.
"));

$pdf->Ln(8);

/* ============================
   KPIs
============================ */
$pdf->section_title("2. Monthly KPIs");

$pdf->table_header(["Metric","Total"],[90,40]);
$pdf->table_row(["Samples Registered",$total_reg],[90,40]);
$pdf->table_row(["Tests Delivered",$total_del],[90,40]);
$pdf->table_row(["Pending Tests",$total_reg - $total_del],[90,40]);

$pdf->Ln(12);
?>
<?php
/* ============================
   SECTION 3 — WORKLOAD
============================ */

$pdf->section_title("3. Workload Overview (Weekly)");

$weeksR = sql_val("
    SELECT WEEK(Registed_Date,1) AS w, COUNT(*) total
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY w ORDER BY w
");

$weeksD = sql_val("
    SELECT WEEK(Register_Date,1) AS w, COUNT(*) total
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY w ORDER BY w
");

$mapR=[]; foreach($weeksR as $r) $mapR[$r['w']]=$r['total'];
$mapD=[]; foreach($weeksD as $r) $mapD[$r['w']]=$r['total'];

$weeks = array_unique(array_merge(array_keys($mapR),array_keys($mapD)));
sort($weeks);

graph_page_break($pdf,120);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,"Weekly Registered vs Delivered",0,1);

$chartX=20; $chartY=$pdf->GetY()+5;
$chartH=60; $barW=12; $space=10;

$maxVal=1;
foreach($weeks as $w){
    $maxVal=max($maxVal,$mapR[$w]??0,$mapD[$w]??0);
}

$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+160,$chartY+$chartH);

$x=$chartX+5;

foreach($weeks as $w){
    $reg=$mapR[$w]??0;
    $h1=($reg/$maxVal)*($chartH-5);
    $y1=$chartY+($chartH-$h1);

    $pdf->SetFillColor(40,180,90); 
    $pdf->Rect($x,$y1,$barW,$h1,"F");

    $del=$mapD[$w]??0;
    $h2=($del/$maxVal)*($chartH-5);
    $y2=$chartY+($chartH-$h2);

    $pdf->SetFillColor(50,100,220);
    $pdf->Rect($x+$barW+2,$y2,$barW,$h2,"F");

    $pdf->SetFont('Arial','',8);
    $pdf->SetXY($x-2,$chartY+$chartH+2);
    $pdf->MultiCell($barW*2+2,4,"W".$w,0,'C');

    $x+=($barW*2+$space);
}

$pdf->Ln($chartH+10);

$pdf->SetFont('Arial','',9);
$pdf->SetFillColor(40,180,90);
$pdf->Rect(20,$pdf->GetY(),4,4,'F');
$pdf->Cell(20,4," Registered",0,1);

$pdf->SetFillColor(50,100,220);
$pdf->Rect(20,$pdf->GetY(),4,4,'F');
$pdf->Cell(20,4," Delivered",0,1);

$pdf->Ln(10);

/* ============================
   SECTION 4 — TEST TYPE DISTRIBUTION (PIE)
============================ */

$pdf->section_title("4. Test Type Distribution");

$regMap = tests_registered_expanded($start_str,$end_str);
$totalReg = array_sum($regMap);
if($totalReg==0) $totalReg=1;

graph_page_break($pdf,130);

$cx = 105;
$cy = $pdf->GetY()+55;
$r  = 38;

$colors = [
    [200,30,30],[30,140,30],[30,30,200],[200,120,0],[130,30,150],
    [20,160,160],[160,20,120],[80,80,255],[50,200,50]
];

$start_angle=0; $i=0;

foreach($regMap as $type=>$val){

    $angle = ($val/$totalReg)*360;
    $end_angle = $start_angle + $angle;

    $col=$colors[$i % count($colors)];
    $pdf->SetDrawColor($col[0],$col[1],$col[2]);

    for($a=$start_angle;$a<$end_angle;$a+=1){
        $x=$cx + $r*cos(deg2rad($a));
        $y=$cy + $r*sin(deg2rad($a));
        $pdf->Line($cx,$cy,$x,$y);
    }

    $pdf->SetFillColor($col[0],$col[1],$col[2]);
    $pdf->Rect($cx+48,$cy-35+($i*6),5,4,'F');

    $pdf->SetXY($cx+55,$cy-36+($i*6));
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(0,4,utf8_decode($type),0,1);

    $start_angle=$end_angle;
    $i++;
}

$pdf->Ln(12);

$pdf->table_header(["Test Type","Registered"],[90,40]);
foreach($regMap as $t=>$v){
    $pdf->table_row([$t,$v],[90,40]);
}

$pdf->Ln(12);
?>
<?php
/* ============================
   SECTION 5 — CLIENT SUMMARY
============================ */

$pdf->section_title("5. Client Summary");

$clients = sql_val("
    SELECT r.Client,
           COUNT(*) AS requested,
           SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) delivered
    FROM lab_test_requisition_form r
    LEFT JOIN test_delivery d
    ON r.Sample_ID=d.Sample_ID
    AND r.Sample_Number=d.Sample_Number
    AND r.Test_Type=d.Test_Type
    WHERE r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY r.Client
    ORDER BY requested DESC
");

$pdf->table_header(["Client","Requested","Delivered","Pending"],[70,30,30,30]);

foreach($clients as $c){
    $p = $c['requested'] - $c['delivered'];
    $pdf->table_row([$c['Client'],$c['requested'],$c['delivered'],$p],[70,30,30,30]);
}

$pdf->Ln(12);

/* ============================
   SECTION 6 — PENDING TESTS
============================ */

$pdf->section_title("6. Pending Tests");

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

$pdf->Ln(12);

/* ============================
   SECTION 7 — NCR
============================ */

$pdf->section_title("7. Non-Conformities");

$ncr = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observation"],[50,140]);

foreach($ncr as $n){
    multiline($pdf,[
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ],[50,140]);
}

$pdf->Ln(12);

/* ============================
   OUTPUT
============================ */
ob_end_clean();
$pdf->Output("I","Monthly_Report_{$year}_{$month}.pdf");
?>
