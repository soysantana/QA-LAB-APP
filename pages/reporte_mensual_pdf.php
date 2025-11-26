<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

/********************************************
 * SAFE HELPERS
 ********************************************/
function safeVal($v){
    return ($v === null || $v === "") ? "" : $v;
}
function getCount($db,$sql){
    $q=$db->query($sql);
    $r=$q->fetch_assoc();
    return isset($r["c"])?(int)$r["c"]:0;
}

/********************************************
 * PDF CLASS
 ********************************************/
class PDF_Report extends FPDF {

    public $logoPath="../uploads/pv_logo.png";

    function Header(){
        if(file_exists($this->logoPath)){
            $this->Image($this->logoPath,10,6,25);
        }
        if($this->PageNo()==1) return;

        $this->SetFont('Arial','B',10);
        $this->Cell(0,8,"Monthly Laboratory Report - ".date("F Y"),0,1,'C');
        $this->Ln(2);

        $this->SetDrawColor(180,180,180);
        $this->Line(10,20,200,20);
        $this->Ln(5);
    }

    function Footer(){
        if($this->PageNo()==1) return;
        $this->SetY(-13);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(120,120,120);
        $this->Cell(0,10,"Page ".$this->PageNo()."/{nb}",0,0,'C');
    }

    function SectionTitle($txt){
        $this->SetFont('Arial','B',14);
        $this->Cell(0,12,utf8_decode($txt),0,1);
        $this->SetDrawColor(160,160,160);
        $this->Line(10,$this->GetY(),200,$this->GetY());
        $this->Ln(3);
    }

    function SubTitle($txt){
        $this->SetFont('Arial','B',11);
        $this->Cell(0,7,utf8_decode($txt),0,1);
    }

    function BodyText($txt){
        $this->SetFont('Arial','',10);
        $this->MultiCell(0,5,utf8_decode($txt));
        $this->Ln(2);
    }

    function TableHeader($cols){
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(230,230,230);
        foreach($cols as $w=>$t){
            $this->Cell($w,8,utf8_decode(safeVal($t)),1,0,'C',true);
        }
        $this->Ln();
    }

    function TableRow($cols){
        $this->SetFont('Arial','',10);
        foreach($cols as $w=>$t){
            $this->Cell($w,8,utf8_decode(safeVal($t)),1,0,'C');
        }
        $this->Ln();
    }

    function FixPageForGraph(){
        if($this->GetY()>160){
            $this->AddPage();
            $this->Ln(3);
        }
    }

    function Cover($month,$year){
        $this->AddPage();
        $this->Ln(42);

        $this->SetFont('Arial','B',24);
        $this->Cell(0,10,"MONTHLY LABORATORY REPORT",0,1,'C');

        $this->SetFont('Arial','',14);
        $this->Cell(0,10,utf8_decode("$month $year"),0,1,'C');

        $this->Ln(20);

        $this->SetFont('Arial','B',12);
        $this->Cell(0,8,"Pueblo Viejo - TSF Laboratory Department",0,1,'C');

        $this->Ln(20);
        $this->SetFont('Arial','',12);
        $this->Cell(0,7,"Prepared by: Wendin De Jesus - Chief Laboratory",0,1,'C');
        $this->Cell(0,7,"Issued on: ".date("Y-m-d"),0,1,'C');

        $this->AddPage();
    }
}

/********************************************
 * COLORS AND AXIS
 ********************************************/
function pickColor($i){
    $c=[
        [66,133,244],
        [219,68,55],
        [244,180,0],
        [15,157,88],
        [171,71,188],
        [0,172,193],
        [255,112,67]
    ];
    return $c[$i%count($c)];
}

function drawAxis($p,$x,$y,$w,$h){
    $p->SetDrawColor(0,0,0);
    $p->Line($x,$y+$h,$x+$w,$y+$h);
    $p->Line($x,$y,$x,$y+$h);
}

/********************************************
 * SQL EXTRACTION
 ********************************************/
$anio = isset($_GET["anio"])?(int)$_GET["anio"]:date("Y");
$mes  = isset($_GET["mes"])?(int)$_GET["mes"]:date("m");

$start="$anio-$mes-01";
$end=date("Y-m-t",strtotime($start));

/************* REGISTRADOS *************/
$q1=$db->query("
    SELECT Sample_ID,Sample_Number,Client,Test_Type
    FROM lab_test_requisition_form
    WHERE Sample_Date BETWEEN '$start' AND '$end'
");
$registered=$q1->fetch_all(MYSQLI_ASSOC);
$registered_count=count($registered);

$expanded=[];
foreach($registered as $r){
    $types=explode(",",$r["Test_Type"]);
    foreach($types as $tp){
        $expanded[]=[
            "Sample_ID"=>trim($r["Sample_ID"]),
            "Sample_Number"=>trim($r["Sample_Number"]),
            "Client"=>trim($r["Client"]),
            "Test_Type"=>trim($tp)
        ];
    }
}

/************* PREP / REAL / DEL *************/
$prep = $db->query("
    SELECT Sample_ID,Sample_Number,Test_Type
    FROM test_preparation
    WHERE Start_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);

$real = $db->query("
    SELECT Sample_ID,Sample_Number,Test_Type
    FROM test_realization
    WHERE Start_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);

$delv = $db->query("
    SELECT Sample_ID,Sample_Number,Test_Type
    FROM test_delivery
    WHERE Start_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);

$prep_count=count($prep);
$real_count=count($real);
$del_count =count($delv);

/********************************************
 * SEMANAS ISO DEL MES
 ********************************************/
$weeksInMonth = [];
$firstDay = strtotime($start);
$lastDay  = strtotime($end);

for($d = $firstDay; $d <= $lastDay; $d += 86400){
    $w = (int) date("W", $d);
    if(!in_array($w, $weeksInMonth)){
        $weeksInMonth[] = $w;
    }
}

/********************************************
 * WEEKLY MATRIX — FINAL REAL FIX
 ********************************************/
$weeklyMatrix = [];
$weeksInMonth = [];

$firstDay = strtotime($start);
$lastDay  = strtotime($end);

for($d = $firstDay; $d <= $lastDay; $d += 86400){

    $week = (int)date("W",$d);
    $dateStr = date("Y-m-d",$d);
    $from = "$dateStr 00:00:00";
    $to   = "$dateStr 23:59:59";

    if(!in_array($week,$weeksInMonth)) $weeksInMonth[] = $week;

    if(!isset($weeklyMatrix[$week])){
        $weeklyMatrix[$week] = [
            "Registered"=>0,
            "Preparation"=>0,
            "Execution"=>0,
            "Delivered"=>0
        ];
    }

    // REGISTERED
    $weeklyMatrix[$week]["Registered"] += getCount($db,"
        SELECT COUNT(*) c
        FROM lab_test_requisition_form
        WHERE Sample_Date = '$dateStr'
           OR (Registed_Date BETWEEN '$from' AND '$to')
    ");

    // PREPARATION
    $weeklyMatrix[$week]["Preparation"] += getCount($db,"
        SELECT COUNT(*) c
        FROM test_preparation
        WHERE Start_Date BETWEEN '$from' AND '$to'
           OR DATE(Start_Date) = '$dateStr'
    ");

    // EXECUTION
    $weeklyMatrix[$week]["Execution"] += getCount($db,"
        SELECT COUNT(*) c
        FROM test_realization
        WHERE Start_Date BETWEEN '$from' AND '$to'
           OR DATE(Start_Date) = '$dateStr'
    ");

    // DELIVERED
    $weeklyMatrix[$week]["Delivered"] += getCount($db,"
        SELECT COUNT(*) c
        FROM test_delivery
        WHERE Start_Date BETWEEN '$from' AND '$to'
           OR DATE(Start_Date) = '$dateStr'
    ");
}



/************* NCR — ensayos_reporte *************/
$ncr=$db->query("
    SELECT Report_Date AS NCR_Date,
           Sample_ID,
           Sample_Number,
           Test_Type,
           Noconformidad
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);
/********************************************
 * PENDING TESTS
 ********************************************/
$pending=[];
foreach($expanded as $t){

    $sid=$db->escape($t["Sample_ID"]);
    $num=$db->escape($t["Sample_Number"]);
    $tp =$db->escape($t["Test_Type"]);

    $p = $db->query("
        SELECT 1 FROM test_preparation
        WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'
    ")->num_rows;

    $r = $db->query("
        SELECT 1 FROM test_realization
        WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'
    ")->num_rows;

    $d = $db->query("
        SELECT 1 FROM test_delivery
        WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'
    ")->num_rows;

    if($d==0){
        $pending[]=[
            "Sample_ID"=>$t["Sample_ID"],
            "Sample_Number"=>$t["Sample_Number"],
            "Client"=>$t["Client"],
            "Test_Type"=>$t["Test_Type"],
            "Stage"=>($r?"Realization":($p?"Preparation":"Registered"))
        ];
    }
}

/********************************************
 * CLIENT TOTALS
 ********************************************/
$clientTotals=[];
foreach($expanded as $t){
    $c=$t["Client"];
    if(!isset($clientTotals[$c])) $clientTotals[$c]=0;
    $clientTotals[$c]++;
}

/********************************************
 * TEST TYPE TOTALS
 ********************************************/
$testTotals=[];
foreach($expanded as $t){
    $tp=$t["Test_Type"];
    if(!isset($testTotals[$tp])) $testTotals[$tp]=0;
    $testTotals[$tp]++;
}
arsort($testTotals);

/********************************************
 * DISTRIBUTION (TYPE × CLIENT)
 ********************************************/
$dist=[];
foreach($expanded as $t){
    $tp=$t["Test_Type"];
    $cl=$t["Client"];
    if(!isset($dist[$tp])) $dist[$tp]=[];
    if(!isset($dist[$tp][$cl])) $dist[$tp][$cl]=0;
    $dist[$tp][$cl]++;
}

$uniqueClients=array_keys($clientTotals);
sort($uniqueClients);

/********************************************
 * CHART VERTICAL
 ********************************************/
function chartVertical($p,$x,$y,$labels,$values){
    $p->FixPageForGraph();

    $w=140;$h=60;
    drawAxis($p,$x,$y,$w,$h);

    $max=max($values); if($max==0)$max=1;

    $barW=floor($w/count($values))-8;

    for($i=0;$i<count($values);$i++){
        $v=$values[$i];
        $bh=($v/$max)*($h-5);

        list($r,$g,$b)=pickColor($i);
        $p->SetFillColor($r,$g,$b);

        $p->Rect($x+5+$i*($barW+8),$y+$h-$bh,$barW,$bh,'F');

        $p->SetXY($x+5+$i*($barW+8),$y+$h+2);
        $p->SetFont('Arial','',8);
        $p->Cell($barW,4,utf8_decode($labels[$i]),0,0,'C');
    }

    $p->Ln(75);
}

/********************************************
 * CHART HORIZONTAL
 ********************************************/
function chartHorizontal($p,$x,$y,$labels,$values){
    $p->FixPageForGraph();

    $max=max($values); if($max==0)$max=1;
    $barH=8;

    for($i=0;$i<count($labels);$i++){

        list($r,$g,$b)=pickColor($i);
        $w=($values[$i]/$max)*100;

        $p->SetXY($x,$y+$i*12);
        $p->Cell(30,8,utf8_decode($labels[$i]));

        $p->SetFillColor($r,$g,$b);
        $p->Rect($x+35,$y+$i*12+2,$w,$barH,'F');

        $p->SetXY($x+145,$y+$i*12);
        $p->Cell(15,8,$values[$i]);
    }

    $p->Ln(count($labels)*12+10);
}

/********************************************
 * CHART STACKED (TYPE × CLIENT)
 ********************************************/
function chartStacked($p,$x,$y,$types,$clients,$data){
    $p->FixPageForGraph();

    $barH=10;
    $p->SetFont('Arial','',9);

    foreach($types as $i=>$tp){

        $p->SetXY($x,$y+$i*15);
        $p->Cell(28,8,utf8_decode($tp));

        $pos=$x+35;

        foreach($clients as $ci=>$cl){

            $v= isset($data[$tp][$cl])?$data[$tp][$cl]:0;
            $w=$v*1.3;

            list($r,$g,$b)=pickColor($ci);
            $p->SetFillColor($r,$g,$b);
            $p->Rect($pos,$y+$i*15+2,$w,$barH,'F');

            $pos+=$w;
        }
    }

    $p->Ln(count($types)*15+8);

    $p->SetFont('Arial','B',9);
    $p->Cell(0,5,"Legend:",0,1);

    foreach($clients as $i=>$cl){
        list($r,$g,$b)=pickColor($i);
        $p->SetFillColor($r,$g,$b);
        $p->Rect($p->GetX(),$p->GetY(),5,5,'F');
        $p->SetXY($p->GetX()+8,$p->GetY()-1);
        $p->Cell(40,5,$cl);
        $p->Ln(6);
    }

    $p->Ln(3);
}

/********************************************
 * CHART GROUPED
 ********************************************/
function chartGrouped($p,$x,$y,$labels,$series,$data){
    $p->FixPageForGraph();

    $w=140;$h=60;
    drawAxis($p,$x,$y,$w,$h);

    $max=0;
    foreach($data as $r){
        foreach($r as $v){ if($v>$max)$max=$v; }
    }
    if($max==0)$max=1;

    $groups=count($labels);
    $seriesCount=count($series);

    $groupW=floor($w/$groups)-5;
    $barW=floor($groupW/$seriesCount)-4;

    for($g=0;$g<$groups;$g++){

        for($s=0;$s<$seriesCount;$s++){
            $v=$data[$g][$s];
            $bh=($v/$max)*($h-5);

            list($r,$gC,$b)=pickColor($s);
            $p->SetFillColor($r,$gC,$b);

            $p->Rect(
                $x+$g*$groupW+$s*$barW+5,
                $y+$h-$bh,
                $barW,
                $bh,
                "F"
            );
        }

        $p->SetFont('Arial','',8);
        $p->SetXY($x+$g*$groupW+5,$y+$h+2);
        $p->Cell($groupW,4,utf8_decode($labels[$g]),0,0,'C');
    }

    $p->Ln(70);

    $p->SetFont('Arial','B',9);
    $p->Cell(0,5,"Legend:",0,1);

    foreach($series as $i=>$txt){
        list($r,$g,$b)=pickColor($i);
        $p->SetFillColor($r,$g,$b);
        $p->Rect($p->GetX(),$p->GetY(),5,5,'F');
        $p->SetXY($p->GetX()+8,$p->GetY()-1);
        $p->Cell(40,5,$txt);
        $p->Ln(6);
    }

    $p->Ln(3);
}
/********************************************
 * INIT PDF
 ********************************************/
$pdf=new PDF_Report("P","mm","A4");
$pdf->AliasNbPages();

$monthName=date("F",strtotime($start));
$pdf->Cover($monthName,$anio);


/// =============================================================
//  SECCIÓN 1 — EXECUTIVE SUMMARY
// =============================================================
$pdf->SectionTitle("1. Executive Summary");

$pdf->BodyText("
During this month, the laboratory maintained a stable operational workflow with balanced activity across 
registration, preparation, realization, and delivery phases.

A total of $registered_count tests were registered, 
$prep_count entered preparation, 
$real_count were executed, 
and $del_count were completed and delivered.

A total of ".count($ncr)." non-conformities were recorded and addressed.

The overall workload distribution remained consistent among clients and test types, 
with minimal operational bottlenecks.
");

/* ============================================
 * 2 — MONTHLY KPIs (ULTRA COMPACTO)
 ============================================ */

$pdf->SectionTitle("2. Monthly KPIs");

/* ---------- TABLA MUY COMPACTA ---------- */
$pdf->SubTitle("KPI Summary");

$pdf->TableHeader([40=>"KPI", 30=>"Value"]);
$pdf->TableRow([40=>"Registered",   30=>$registered_count]);
$pdf->TableRow([40=>"Preparation",  30=>$prep_count]);
$pdf->TableRow([40=>"Execution",    30=>$real_count]);
$pdf->TableRow([40=>"Delivered",    30=>$del_count]);

/* ---------- 1 mm EXACTO BAJO LA TABLA ---------- */
$pdf->SetY($pdf->GetY() + 8);

/* ==================================================
 * GRÁFICO KPI — ULTRA COMPACTO, PEGADO, CON EJES
 ================================================== */

$labels = ["Reg","Prep","Exec","Del"];
$values = [$registered_count,$prep_count,$real_count,$del_count];

$chartX = 20;
$chartY = $pdf->GetY();
$chartW = 110;     // Más estrecho
$chartH = 35;      // Mucho más bajo (ideal para pegarlo a tabla)

/* ---------- EJE X / Y ---------- */
drawAxis($pdf, $chartX, $chartY, $chartW, $chartH);

/* ---------- ESCALA EJE Y ---------- */
$maxVal = max($values);
if($maxVal == 0) $maxVal = 1;
$steps = 3;

$pdf->SetFont("Arial","",6);
for($i=0; $i <= $steps; $i++){
    $yPos = $chartY + $chartH - ($chartH / $steps * $i);
    $val = round($maxVal / $steps * $i);
    $pdf->SetXY($chartX - 8, $yPos - 2);
    $pdf->Cell(7,3,$val,0,0,'R');
}

/* ---------- BARRAS COMPACTAS ---------- */
$barWidth = floor($chartW / count($values)) - 15;

for($i=0; $i<count($values); $i++)
{
    list($r,$g,$b) = pickColor($i);
    $pdf->SetFillColor($r,$g,$b);

    $barHeight = ($values[$i] / $maxVal) * ($chartH - 5);
    $barX = $chartX + 10 + $i * ($barWidth + 12);
    $barY = $chartY + $chartH - $barHeight;

    // Barra
    $pdf->Rect($barX, $barY, $barWidth, $barHeight, "F");

    // Valor encima
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY($barX, $barY - 3);
    $pdf->Cell($barWidth,3,$values[$i],0,0,'C');

    // Label eje X
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY($barX, $chartY + $chartH + 1);
    $pdf->Cell($barWidth,3, utf8_decode($labels[$i]),0,0,'C');
}

/* ---------- LEYENDA MINI A LA DERECHA ---------- */
$legendX = $chartX + $chartW + 3;
$legendY = $chartY + 1;

$pdf->SetXY($legendX, $legendY);
$pdf->SetFont("Arial","B",7);
$pdf->Cell(20,4,"Legend",0,1);

foreach($labels as $i=>$lbl){
    list($r,$g,$b)=pickColor($i);
    $pdf->SetFillColor($r,$g,$b);

    $pdf->Rect($legendX, $legendY + 5 + ($i*5), 4, 4, "F");
    $pdf->SetXY($legendX + 6, $legendY + 4 + ($i*5));
    $pdf->SetFont("Arial","",6);
    $pdf->Cell(20,4,$lbl);
}

/* ---------- SOLO 2 mm DE ESPACIO FINAL ---------- */
$pdf->SetY($chartY + $chartH + 6);
 
/* ============================================
 * 3 — WORKLOAD OVERVIEW (ISO Weeks)
 ============================================ */
$pdf->SectionTitle("3. Workload Overview (ISO Weeks)");
$pdf->SubTitle("Weekly Load Summary");

$pdf->TableHeader([
    25=>"ISO Week",
    35=>"Registered",
    35=>"Preparation",
    35=>"Execution",
    35=>"Delivered"
]);

foreach ($weeksInMonth as $weekNum) {

    $row = isset($weeklyMatrix[$weekNum]) ? $weeklyMatrix[$weekNum] : [
        "Registered"  => 0,
        "Preparation" => 0,
        "Execution"   => 0,
        "Delivered"   => 0
    ];

    $pdf->TableRow([
        25=>"W$weekNum",
        35=>$row["Registered"],
        35=>$row["Preparation"],
        35=>$row["Execution"],
        35=>$row["Delivered"]
    ]);
}

$pdf->Ln(5);
$pdf->SubTitle("Weekly Load Chart");

/* ---------- DATOS PARA EL GRÁFICO ---------- */
$labels = [];
$data   = [];

foreach ($weeklyMatrix as $weekNum => $row) {
    $labels[] = "W$weekNum";
    $data[] = [
        $row["Registered"]  ?? 0,
        $row["Preparation"] ?? 0,
        $row["Execution"]   ?? 0,
        $row["Delivered"]   ?? 0
    ];
}

/* ---------- COORDENADAS DEL GRÁFICO ---------- */
$originX = 25;
$originY = $pdf->GetY() + 4;
$chartW  = 140;
$chartH  = 45;

/* ---------- EJE X/Y ---------- */
drawAxis($pdf, $originX, $originY, $chartW, $chartH);

/* ---------- MAX VALUE ---------- */
$maxVal = 0;
foreach ($data as $row) {
    foreach ($row as $v) {
        if ($v > $maxVal) $maxVal = $v;
    }
}
if ($maxVal == 0) $maxVal = 1;

/* ---------- CONFIG ---------- */
$groups = count($labels);       // semanas
$series = 4;                    // Reg, Prep, Exec, Del
$groupWidth = floor($chartW / $groups) - 5;
$barWidth   = floor($groupWidth / $series) - 2;

/* ---------- DIBUJAR BARRAS ---------- */
foreach ($data as $g => $row) {

    for ($s = 0; $s < $series; $s++) {

        $v = $row[$s];
        $barH = ($v / $maxVal) * ($chartH - 5);

        list($r,$gC,$b) = pickColor($s);
        $pdf->SetFillColor($r,$gC,$b);

        $x = $originX + ($g * $groupWidth) + ($s * $barWidth) + 5;
        $y = $originY + $chartH - $barH;

        $pdf->Rect($x, $y, $barWidth, $barH, "F");
    }

    // Label X (semana)
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY($originX + $g * $groupWidth + 2, $originY + $chartH + 1);
    $pdf->Cell($groupWidth, 4, $labels[$g], 0, 0, 'C');
}

/* ---------- LEYENDA COMPACTA ---------- */
$pdf->SetFont("Arial","B",7);
$pdf->SetXY($originX + $chartW + 3, $originY + 2);
$pdf->Cell(20,4,"Legend");

$serieNames = ["Reg","Prep","Exec","Del"];

foreach ($serieNames as $i => $txt) {

    list($r,$g,$b) = pickColor($i);
    $pdf->SetFillColor($r,$g,$b);

    $pdf->Rect($originX + $chartW + 3, $originY + 8 + ($i * 5), 4, 4, "F");

    $pdf->SetXY($originX + $chartW + 9, $originY + 7 + ($i * 5));
    $pdf->SetFont("Arial","",6);
    $pdf->Cell(20, 4, $txt);
}

$pdf->SetY($originY + $chartH + 10);





/************* 4 — TYPE × CLIENT *************/
$pdf->SectionTitle("4. Test Type Distribution by Client");
$pdf->SubTitle("Test Types × Clients Table");

$header=[35=>"Test Type"];
foreach($uniqueClients as $cl){
    $header[25]=$cl;
}
$pdf->TableHeader($header);

foreach($dist as $tp=>$arr){
    $row=[35=>$tp];
    foreach($uniqueClients as $cl){
        $row[25]=isset($arr[$cl])?$arr[$cl]:0;
    }
    $pdf->TableRow($row);
}

$pdf->Ln(3);
$pdf->SubTitle("Stacked Distribution Chart");

chartStacked(
    $pdf,
    25,
    $pdf->GetY()+5,
    array_keys($dist),
    $uniqueClients,
    $dist
);

/************* 5 — PENDING *************/
$pdf->SectionTitle("5. Pending Tests");

$pdf->TableHeader([
    30=>"Sample ID",
    30=>"Number",
    30=>"Client",
    30=>"Test",
    30=>"Stage"
]);

foreach($pending as $p){
    $pdf->TableRow([
        30=>$p["Sample_ID"],
        30=>$p["Sample_Number"],
        30=>$p["Client"],
        30=>$p["Test_Type"],
        30=>$p["Stage"]
    ]);
}

$pdf->Ln(5);
$pdf->SubTitle("Pending Tests Chart");

$seriesNames=array_unique(array_column($pending,"Test_Type"));
$labels=$uniqueClients;

$data=[];
foreach($labels as $cl){
    $row=[];
    foreach($seriesNames as $tp){
        $c=0;
        foreach($pending as $p){
            if($p["Client"]==$cl && $p["Test_Type"]==$tp){
                $c++;
            }
        }
        $row[]=$c;
    }
    $data[]=$row;
}

chartGrouped(
    $pdf,
    25,
    $pdf->GetY()+5,
    $labels,
    $seriesNames,
    $data
);

/************* 6 — SAMPLES PER CLIENT *************/
$pdf->SectionTitle("6. Samples per Client");

$pdf->TableHeader([60=>"Client",40=>"Samples",40=>"Percentage (%)"]);

$total=array_sum($clientTotals);
if($total==0)$total=1;

foreach($clientTotals as $cl=>$q){
    $pct=round(($q/$total)*100,2)."%";
    $pdf->TableRow([60=>$cl,40=>$q,40=>$pct]);
}

$pdf->Ln(4);
$pdf->SubTitle("Samples Per Client Chart");

chartVertical(
    $pdf,
    25,
    $pdf->GetY()+5,
    array_keys($clientTotals),
    array_values($clientTotals)
);

/************* 7 — KPI PREVIOUS MONTH *************/
$pdf->SectionTitle("7. KPI Comparison (vs Last Month)");

$lastStart=date("Y-m-01",strtotime("-1 month",strtotime($start)));
$lastEnd  =date("Y-m-t",strtotime($lastStart));

$prevReg =getCount($db,"SELECT COUNT(*) c FROM lab_test_requisition_form WHERE Sample_Date BETWEEN '$lastStart' AND '$lastEnd'");
$prevPrep=getCount($db,"SELECT COUNT(*) c FROM test_preparation WHERE Start_Date BETWEEN '$lastStart' AND '$lastEnd'");
$prevReal=getCount($db,"SELECT COUNT(*) c FROM test_realization WHERE Start_Date BETWEEN '$lastStart' AND '$lastEnd'");
$prevDel =getCount($db,"SELECT COUNT(*) c FROM test_delivery WHERE Start_Date BETWEEN '$lastStart' AND '$lastEnd'");

$pdf->SubTitle("KPI Comparison Table");

$pdf->TableHeader([40=>"KPI",30=>"Prev",30=>"Current",30=>"Change (%)"]);

function pct($o,$n){ return ($o==0?"∞":round((($n-$o)/$o)*100,1)."%"); }

$pdf->TableRow([40=>"Registered",30=>$prevReg,30=>$registered_count,30=>pct($prevReg,$registered_count)]);
$pdf->TableRow([40=>"Preparation",30=>$prevPrep,30=>$prep_count,30=>pct($prevPrep,$prep_count)]);
$pdf->TableRow([40=>"Execution",30=>$prevReal,30=>$real_count,30=>pct($prevReal,$real_count)]);
$pdf->TableRow([40=>"Delivered",30=>$prevDel,30=>$del_count,30=>pct($prevDel,$del_count)]);

$pdf->Ln(4);
$pdf->SubTitle("KPI Comparison Chart");

chartGrouped(
    $pdf,
    25,
    $pdf->GetY()+5,
    ["Reg","Prep","Exec","Del"],
    ["Prev","Current"],
    [
        [$prevReg,$registered_count],
        [$prevPrep,$prep_count],
        [$prevReal,$real_count],
        [$prevDel,$del_count]
    ]
);

/************* 8 — PRODUCTION MIX *************/
$pdf->SectionTitle("8. Production Mix — Test Types");
$pdf->SubTitle("Test Type Breakdown Table");

$pdf->TableHeader([70=>"Test Type",40=>"Total",40=>"Percentage (%)"]);

$ttTot=array_sum($testTotals);
if($ttTot==0)$ttTot=1;

foreach($testTotals as $tp=>$q){
    $pct=round(($q/$ttTot)*100,1)."%";
    $pdf->TableRow([70=>$tp,40=>$q,40=>$pct]);
}

$pdf->Ln(4);
$pdf->SubTitle("Production Mix Chart");

chartHorizontal(
    $pdf,
    25,
    $pdf->GetY()+5,
    array_keys($testTotals),
    array_values($testTotals)
);

/************* 9 — NCR *************/
$pdf->SectionTitle("9. Non-Conformities");

$pdf->TableHeader([
    30=>"Date",
    60=>"NCR ID",
    90=>"Description"
]);

foreach($ncr as $n){
    $id=$n["Sample_ID"]."-".$n["Sample_Number"]."-".$n["Test_Type"];
    $pdf->TableRow([
        30=>$n["NCR_Date"],
        60=>$id,
        90=>$n["Noconformidad"]
    ]);
}

$pdf->Ln(6);
/************* 10 — PERSONNEL *************/
$pdf->SectionTitle("10. Responsible Personnel");

$pdf->SubTitle("Chief Laboratory");
$pdf->BodyText("• Wendin De Jesús");

$pdf->SubTitle("Document Control");
$pdf->BodyText("
• Yamilexi Mejía
• Arturo Santana – Support
• Frandy Espinal – Support
");

$pdf->SubTitle("Supervisors");
$pdf->BodyText("
• Diana Vázquez – PV Supervisor
• Víctor Mercedes – PV Supervisor
");

$pdf->SubTitle("Contractor Technicians");
$pdf->BodyText("
• Wilson Martínez
• Rafy Leocadio
• Rony Vargas
• Jonathan Vargas
• Rafael Reyes
• Darielvy Félix
• Jordany Almonte
• Melvin Castillo
");

/********************************************
 * OUTPUT
 ********************************************/
$pdf->Output("I","Monthly_Report_$anio-$mes.pdf");
exit;
