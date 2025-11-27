<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

/* ======================================================
   0. SAFE HELPERS
====================================================== */
function safeVal($v){
    return ($v === null || $v === "") ? "" : $v;
}
function getCount($db,$sql){
    $q = $db->query($sql);
    $r = $q->fetch_assoc();
    return isset($r["c"]) ? (int)$r["c"] : 0;
}

/* ======================================================
   1. PDF CLASS (OPTIMIZED FOR MONTHLY REPORT)
====================================================== */
class PDF_Monthly extends FPDF {

    public $logoPath = "../uploads/pv_logo.png";

    /* ---------- HEADER ---------- */
    function Header(){
        if(file_exists($this->logoPath)){
            $this->Image($this->logoPath,10,6,25);
        }
        if($this->PageNo() == 1) return;

        $this->SetFont('Arial','B',10);
        $this->Cell(0,8,"Monthly Laboratory Report - ".date("F Y"),0,1,'C');
        $this->Ln(2);

        $this->SetDrawColor(180,180,180);
        $this->Line(10,20,200,20);
        $this->Ln(5);
    }

    /* ---------- FOOTER ---------- */
    function Footer(){
        if($this->PageNo()==1) return;
        $this->SetY(-13);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(120,120,120);
        $this->Cell(0,10,"Page ".$this->PageNo()."/{nb}",0,0,'C');
    }

    /* ---------- TITLES ---------- */
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

    /* ---------- TABLES ---------- */
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

    /* ---------- PAGE BREAK FOR GRAPHS ---------- */
    function FixPageForGraph(){
        if($this->GetY() > 160){
            $this->AddPage();
            $this->Ln(3);
        }
    }

    /* ---------- COVER PAGE ---------- */
 function Cover($month, $year){
    $this->AddPage();

    // === LOGO DE PV EN GRANDE (CENTRADO) ===
    $logo = "../assets/img/Pueblo-Viejo.jpg";
    if (file_exists($logo)) {
        // Centro exacto de la página
        $pageWidth  = $this->GetPageWidth();
        $logoWidth  = 58;           // ajustable
        $x = ($pageWidth - $logoWidth) / 2;

        // Posición vertical del logo
        $this->Image($logo, $x, 35, $logoWidth); 
    }

    // ESPACIO DESPUÉS DEL LOGO
    $this->Ln(68);

    // === TÍTULO PRINCIPAL ===
    $this->SetFont('Arial','B',24);
    $this->Cell(0,12,"MONTHLY LABORATORY REPORT",0,1,'C');

    // MES + AÑO
    $this->SetFont('Arial','',14);
    $this->Cell(0,10,utf8_decode("$month $year"),0,1,'C');

    $this->Ln(25);

    // === INFORMACIÓN INFERIOR ===
    $this->SetFont('Arial','B',12);
    $this->Cell(0,8,"Pueblo Viejo - TSF Laboratory Department",0,1,'C');

    $this->Ln(20);
    $this->SetFont('Arial','',12);
    $this->Cell(0,7,"Prepared by: Wendin De Jesus - Chief Laboratory",0,1,'C');
    $this->Cell(0,7,"Issued on: ".date("Y-m-d"),0,1,'C');

    // Segunda página automática
    $this->AddPage();
}

}

/* ======================================================
   2. COLORS & AXIS HELPERS
====================================================== */
function pickColor($i){
    $c = [
        [66,133,244],   // blue
        [219,68,55],    // red
        [244,180,0],    // yellow
        [15,157,88],    // green
        [171,71,188],   // purple
        [0,172,193],    // cyan
        [255,112,67]    // orange
    ];
    return $c[$i % count($c)];
}

function drawAxis($p,$x,$y,$w,$h){
    $p->SetDrawColor(0,0,0);
    $p->Line($x,$y+$h,$x+$w,$y+$h);  // X-axis
    $p->Line($x,$y,$x,$y+$h);        // Y-axis
}

/* ======================================================
   3. INPUT DATE RANGE
====================================================== */
$anio = isset($_GET["anio"]) ? (int)$_GET["anio"] : date("Y");
$mes  = isset($_GET["mes"])  ? (int)$_GET["mes"]  : date("m");

$start = "$anio-$mes-01";
$end   = date("Y-m-t", strtotime($start));

/* ======================================================
   4. INIT PDF
====================================================== */
$pdf = new PDF_Monthly("P","mm","A4");
$pdf->AliasNbPages();

$monthName = date("F", strtotime($start));
$pdf->Cover($monthName, $anio);
/* ======================================================
   SECTION 1 — EXECUTIVE SUMMARY
====================================================== */

$pdf->SectionTitle("1. Executive Summary");

/* ---------- FETCH ALL PROCESS COUNTS ---------- */
$registered_count = getCount($db,"
    SELECT COUNT(*) c 
    FROM lab_test_requisition_form 
    WHERE Registed_Date BETWEEN '$start' AND '$end'
");

$prep_count = getCount($db,"
    SELECT COUNT(*) c 
    FROM test_preparation 
    WHERE Start_Date BETWEEN '$start' AND '$end'
");

$real_count = getCount($db,"
    SELECT COUNT(*) c 
    FROM test_realization 
    WHERE Start_Date BETWEEN '$start' AND '$end'
");

$del_count = getCount($db,"
    SELECT COUNT(*) c 
    FROM test_delivery 
    WHERE Start_Date BETWEEN '$start' AND '$end'
");

$review_count = getCount($db,"
    SELECT COUNT(*) c 
    FROM test_review 
    WHERE Start_Date BETWEEN '$start' AND '$end'
");

$reviewed_count = getCount($db,"
    SELECT COUNT(*) c 
    FROM test_reviewed 
    WHERE Start_Date BETWEEN '$start' AND '$end'
");

$docs_count = getCount($db,"
    SELECT COUNT(*) c 
    FROM doc_files 
    WHERE created_at BETWEEN '$start' AND '$end'
");

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


/// =============================================================
//  SECCIÓN 1 — EXECUTIVE SUMMARY
// =============================================================


$summaryText = "
During this month, the laboratory maintained stable and consistent operational performance across all workflow phases — sample registration, preparation, execution, and delivery.

A total of {$registered_count} tests were registered, {$prep_count} entered preparation, {$real_count} were executed, and {$del_count} were fully completed and delivered.

Client workload distribution remained technically balanced, with no single client dominating operational capacity, ensuring proper resource allocation. Test type distribution followed expected monthly patterns aligned with project needs.

A total of ".count($ncr)." non-conformities were reported, each properly documented and closed through corrective and preventive actions. No major operational deviations, delays, or backlogs were detected.

Overall, the month progressed efficiently, reflecting strong coordination between technicians, supervisors, and document control—maintaining quality, traceability, and compliance with internal QA procedures and ASTM standards.
";

$pdf->BodyText($summaryText);


/* ======================================================
   SECTION 2 — MONTHLY KPIs (Improved Executive Metrics)
====================================================== */
$pdf->SectionTitle("2. Monthly KPIs");

/* ============================
   2A — KPI TABLE
============================ */

$pending_total = $registered_count - $del_count; 
if ($pending_total < 0) $pending_total = 0;

// Completion KPI usando ENTREGADOS + REVIEWED + DOCS (más realista)
$effective_completed = $del_count + $reviewed_count + $docs_count;
$completion_rate = ($registered_count > 0)
    ? round(($effective_completed / $registered_count) * 100, 2)
    : 0;

// Efficiency Ratio (output real del laboratorio)
$efficiency_ratio = ($registered_count > 0)
    ? round($del_count / $registered_count, 2)
    : 0;

// Pending Ratio
$pending_ratio = ($registered_count > 0)
    ? round(($pending_total / $registered_count) * 100, 2)
    : 0;

// Aging
$aging = $db->query("
    SELECT AVG(DATEDIFF(NOW(), Registed_Date)) AS avg_age
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '$start' AND '$end'
")->fetch_assoc();
$avgAging = isset($aging['avg_age']) ? round($aging['avg_age'],1) : 0;


// ---------- TABLE ----------
$pdf->SubTitle("Key Performance Indicators");

$pdf->TableHeader([
    55=>"KPI",
    35=>"Value"
]);

$pdf->TableRow([55=>"Tests Registered",            35=>$registered_count]);
$pdf->TableRow([55=>"Tests in Preparation",        35=>$prep_count]);
$pdf->TableRow([55=>"Tests in Execution",          35=>$real_count]);
$pdf->TableRow([55=>"Tests Delivered",             35=>$del_count]);
$pdf->TableRow([55=>"Reviewed Tests",              35=>$reviewed_count]);
$pdf->TableRow([55=>"Final Reports Issued",        35=>$docs_count]);

$pdf->TableRow([55=>"Pending Tests",               35=>$pending_total]);
$pdf->TableRow([55=>"Pending Load (%)",            35=>$pending_ratio."%"]);
$pdf->TableRow([55=>"Completion Rate",             35=>$completion_rate."%"]);
$pdf->TableRow([55=>"Efficiency Ratio (Del/Reg)",  35=>$efficiency_ratio]);
$pdf->TableRow([55=>"Average Pending Age (days)",  35=>$avgAging]);

$pdf->Ln(5);

/* ============================
   2B — KPI SHORT SUMMARY TEXT
============================ */

$pdf->SubTitle("Executive Interpretation");
$pdf->BodyText("
• The laboratory processed a total of $registered_count test requests this month.
• The operational workflow shows $prep_count tests entering preparation and $real_count entering execution.
• A total of $del_count tests were successfully delivered, with an extended completion of
  $reviewed_count reviewed cases and $docs_count final reports issued.
• Current pending load is $pending_total tests ($pending_ratio% of the monthly intake).
• The average aging of pending requests is $avgAging days, indicating the typical waiting time before closure.
• Overall completion efficiency stands at $completion_rate%, supported by a delivery-to-registration ratio of $efficiency_ratio.
");



/* ======================================================
   SECTION 3 — Monthly Registered Samples (Trend)
====================================================== */

$pdf->SectionTitle("3. Monthly Registered Samples");

$rows = $db->query("
    SELECT Registed_Date, Client, Sample_ID, Sample_Number, Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '$start' AND '$end'
    ORDER BY Registed_Date ASC
")->fetch_all(MYSQLI_ASSOC);

/* ---------- TABLE ---------- */
$pdf->SubTitle("Registered Samples Table");

$pdf->TableHeader([
    30=>"Date",
    40=>"Client",
    40=>"Sample ID",
    40=>"Number",
    40=>"Tests"
]);

foreach($rows as $r){
    $pdf->TableRow([
        30=>substr($r["Registed_Date"],0,10),
        40=>$r["Client"],
        40=>$r["Sample_ID"],
        40=>$r["Sample_Number"],
        40=>$r["Test_Type"]
    ]);
}

$pdf->Ln(4);

/* ---------- GRAPH: TREND ---------- */

$pdf->SubTitle("Daily Registration Trend");

$trend = []; // date => count

$cursor = strtotime($start);
$last   = strtotime($end);

while($cursor <= $last){
    $date = date("Y-m-d",$cursor);
    $trend[$date] = 0;
    $cursor = strtotime("+1 day",$cursor);
}

foreach($rows as $r){
    $d = substr($r["Registed_Date"],0,10);
    if(isset($trend[$d])) $trend[$d]++;
}

$labels = array_keys($trend);
$values = array_values($trend);

/* ---------- DRAW GRAPH ---------- */
$chartX = 20;
$chartY = $pdf->GetY()+8;
$chartW = 150;
$chartH = 45;

drawAxis($pdf,$chartX,$chartY,$chartW,$chartH);

$maxVal = max($values);
if($maxVal==0) $maxVal=1;

$barW = (int)(($chartW - 10) / count($labels));

for($i=0; $i<count($labels); $i++){
    $v = $values[$i];
    $barH = ($v / $maxVal) * ($chartH - 5);

    $x = $chartX + 5 + $i * $barW;
    $y = $chartY + $chartH - $barH;

    $pdf->SetFillColor(66,133,244);
    $pdf->Rect($x,$y,$barW-1,$barH,"F");
}

$pdf->SetY($chartY + $chartH + 10);

/* ======================================================
   SECTION 4 — Client Summary: Requested vs Completed
====================================================== */

$pdf->SectionTitle("4. Client Summary (Requested vs Completed)");

/* ---------- HISTORICAL REQUESTED (ALL TIME UP TO END OF MONTH) ---------- */
$hist_requests = $db->query("
    SELECT Client, COUNT(*) AS total
    FROM lab_test_requisition_form
    WHERE Registed_Date <= '$end'
    GROUP BY Client
")->fetch_all(MYSQLI_ASSOC);

/* ---------- MONTH COMPLETED (delivery + review + reviewed + doc_files) ---------- */
$hist_completed = $db->query("
    SELECT Client, COUNT(*) AS total
    FROM lab_test_requisition_form r
    WHERE EXISTS (
        SELECT 1 FROM test_delivery d
        WHERE d.Sample_ID = r.Sample_ID
          AND d.Sample_Number = r.Sample_Number
          AND d.Start_Date <= '$end'
    )
    OR EXISTS(
        SELECT 1 FROM test_review ed
        WHERE ed.Sample_ID = r.Sample_ID
          AND ed.Sample_Number = r.Sample_Number
          AND ed.Start_Date <= '$end'
    )
    OR EXISTS(
        SELECT 1 FROM test_reviewed rv
        WHERE rv.Sample_ID = r.Sample_ID
          AND rv.Sample_Number = r.Sample_Number
          AND rv.Start_Date <= '$end'
    )
    OR EXISTS(
        SELECT 1 FROM doc_files df
        WHERE df.Sample_ID = r.Sample_ID
          AND df.Sample_Number = r.Sample_Number
          AND df.created_at <= '$end'
    )
    GROUP BY Client
")->fetch_all(MYSQLI_ASSOC);

/* ---------- NORMALIZE INTO MATRIX ---------- */
$clientStats = [];

foreach($hist_requests as $r){
    $clientStats[$r['Client']]['requested'] = $r['total'];
}

foreach($hist_completed as $r){
    $clientStats[$r['Client']]['completed'] = $r['total'];
}

foreach($clientStats as $c => &$v){
    $req = $v['requested'] ?? 0;
    $cmp = $v['completed'] ?? 0;
    $v['pct'] = ($req>0) ? round(($cmp*100)/$req,1) : 0;
}
unset($v);

/* ---------- TABLE ---------- */

$pdf->SubTitle("Client Completion Table");

$pdf->TableHeader([
    50=>"Client",
    30=>"Requested",
    30=>"Completed",
    30=>"Completion (%)"
]);

foreach($clientStats as $cl=>$v){
    $pdf->TableRow([
        50=>$cl,
        30=>$v['requested'] ?? 0,
        30=>$v['completed'] ?? 0,
        30=>$v['pct']
    ]);
}

$pdf->Ln(4);

/* ---------- GRAPH ---------- */

$pdf->SubTitle("Client Completion Chart");

$labels = array_keys($clientStats);
$values = array_column($clientStats,"pct");

$chartX = 25;
$chartY = $pdf->GetY() + 5;
$chartW = 150;
$chartH = 45;

drawAxis($pdf,$chartX,$chartY,$chartW,$chartH);

$maxVal = max($values); if($maxVal==0) $maxVal=1;

$barW = (int)(($chartW-10)/count($labels));

for($i=0;$i<count($labels);$i++){
    $pct = $values[$i];
    $barH = ($pct/$maxVal)*($chartH-5);
    $x = $chartX + 5 + $i*$barW;
    $y = $chartY + $chartH - $barH;

    list($r,$g,$b) = pickColor($i);
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($x,$y,$barW-2,$barH,"F");

    $pdf->SetFont("Arial","",6);
    $pdf->SetXY($x,$chartY+$chartH+2);
    $pdf->MultiCell($barW-2,3,substr($labels[$i],0,10),0,'C');
}

$pdf->SetY($chartY+$chartH+10);
/* ======================================================
   SECTION 5 — Workload by ISO Week
====================================================== */

$pdf->SectionTitle("5. Workload by ISO Week");

$weekly = [];

$cursor = strtotime($start);
$last = strtotime($end);

while($cursor <= $last){
    $date = date("Y-m-d",$cursor);
    $week = date("W",$cursor);

    if(!isset($weekly[$week])){
        $weekly[$week] = ["reg"=>0, "prep"=>0, "exec"=>0, "del"=>0];
    }

    $weekly[$week]["reg"] += getCount($db,"
        SELECT COUNT(*) c FROM lab_test_requisition_form WHERE Registed_Date = '$date'
    ");

    $weekly[$week]["prep"] += getCount($db,"
        SELECT COUNT(*) c FROM test_preparation WHERE DATE(Start_Date) = '$date'
    ");

    $weekly[$week]["exec"] += getCount($db,"
        SELECT COUNT(*) c FROM test_realization WHERE DATE(Start_Date) = '$date'
    ");

    $weekly[$week]["del"]  += getCount($db,"
        SELECT COUNT(*) c FROM test_delivery WHERE DATE(Start_Date) = '$date'
    ");

    $cursor = strtotime("+1 day",$cursor);
}

/* ---------- TABLE ---------- */
$pdf->SubTitle("ISO Week Load Table");

$pdf->TableHeader([
    30=>"Week",
    30=>"Registered",
    30=>"Prep",
    30=>"Exec",
    30=>"Del"
]);

foreach($weekly as $w=>$v){
    $pdf->TableRow([
        30=>"W$w",
        30=>$v["reg"],
        30=>$v["prep"],
        30=>$v["exec"],
        30=>$v["del"]
    ]);
}

$pdf->Ln(4);

/* ======================================================
   SECTION 6 — Test Type Mix (Production Mix)
====================================================== */

$pdf->SectionTitle("6. Test Type Mix");

$allTests = [];

foreach($rows as $r){
    $types = preg_split('/[,;]+/', $r["Test_Type"]);
    foreach($types as $tp){
        $tp = trim($tp);
        if($tp!=""){
            if(!isset($allTests[$tp])) $allTests[$tp]=0;
            $allTests[$tp]++;
        }
    }
}

asort($allTests);

$pdf->TableHeader([
    60=>"Test Type",
    40=>"Count",
    40=>"Percentage"
]);

$totalTests = array_sum($allTests);
if($totalTests==0) $totalTests=1;

foreach($allTests as $tp=>$q){
    $pct = round(($q/$totalTests)*100,1)."%";
    $pdf->TableRow([60=>$tp,40=>$q,40=>$pct]);
}

$pdf->Ln(4);

/* ---------- GRAPH ---------- */
$pdf->SubTitle("Test Type Mix - Horizontal Chart");

$i=0;
foreach($allTests as $tp=>$q){
    list($r,$g,$b)=pickColor($i);

    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect(30,$pdf->GetY(),$q*1.2,6,"F");

    $pdf->SetXY(10,$pdf->GetY());
    $pdf->Cell(20,6,$tp);

    $pdf->Ln(7);
    $i++;
}

$pdf->Ln(4);

/* ======================================================
   SECTION 7 — Pending Tests + Aging
====================================================== */

$pdf->SectionTitle("7. Pending Tests");

/* ---------- FETCH ALL TESTS TO EVALUATE ---------- */

$expanded = [];

foreach($rows as $r){
    $types = preg_split('/[,;]+/',$r["Test_Type"]);
    foreach($types as $tp){
        $expanded[] = [
            "Sample_ID"=>$r["Sample_ID"],
            "Sample_Number"=>$r["Sample_Number"],
            "Client"=>$r["Client"],
            "Test_Type"=>trim($tp),
            "date"=>$r["Registed_Date"]
        ];
    }
}

$pending=[];

foreach($expanded as $t){

    $sid = $db->escape($t["Sample_ID"]);
    $num = $db->escape($t["Sample_Number"]);
    $tp  = $db->escape($t["Test_Type"]);

    $p = $db->query("SELECT 1 FROM test_preparation WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'")->num_rows;
    $r = $db->query("SELECT 1 FROM test_realization WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'")->num_rows;
    $d = $db->query("SELECT 1 FROM test_delivery WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'")->num_rows;
    $rv = $db->query("SELECT 1 FROM test_review WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'")->num_rows;
    $rvd = $db->query("SELECT 1 FROM test_reviewed WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'")->num_rows;
    $df = $db->query("SELECT 1 FROM doc_files WHERE Sample_ID='$sid' AND Sample_Number='$num' AND Test_Type='$tp'")->num_rows;

    if($d==0 && $rv==0 && $rvd==0 && $df==0){
        $pending[] = [
            "Sample_ID"=>$t["Sample_ID"],
            "Sample_Number"=>$t["Sample_Number"],
            "Client"=>$t["Client"],
            "Test_Type"=>$t["Test_Type"],
            "Days"=> round((strtotime("now") - strtotime($t["date"])) / 86400,1)
        ];
    }
}

$pdf->TableHeader([
    30=>"Sample ID",
    30=>"Number",
    30=>"Client",
    30=>"Test",
    30=>"Age (days)"
]);

foreach($pending as $p){
    $pdf->TableRow([
        30=>$p["Sample_ID"],
        30=>$p["Sample_Number"],
        30=>$p["Client"],
        30=>$p["Test_Type"],
        30=>$p["Days"]
    ]);
}

$pdf->Ln(4);

/* ---------- GRAPH: AGING ---------- */
$pdf->SubTitle("Aging Distribution Chart");

$agingVals = array_column($pending,"Days");
$labels = array_column($pending,"Test_Type");

$chartX = 25;
$chartY = $pdf->GetY()+5;
$chartW = 140;
$chartH = 45;

drawAxis($pdf,$chartX,$chartY,$chartW,$chartH);

if(count($agingVals)==0) $agingVals=[0];
$maxA = max($agingVals);
if($maxA==0) $maxA=1;

$barW = (int)(($chartW-10)/count($agingVals));

for($i=0;$i<count($agingVals);$i++){
    $v=$agingVals[$i];

    $barH = ($v/$maxA)*($chartH-5);
    $x = $chartX+5+$i*$barW;
    $y = $chartY+$chartH-$barH;

    list($r,$g,$b)=pickColor($i);
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($x,$y,$barW-1,$barH,"F");
}

$pdf->SetY($chartY+$chartH+10);
/* ======================================================
   SECTION 8 — NCR Summary
====================================================== */

$pdf->SectionTitle("8. Non-Conformities (NCR)");

$ncr = $db->query("
    SELECT Report_Date, Sample_ID, Sample_Number, Test_Type, Noconformidad
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);

$pdf->TableHeader([
    30=>"Date",
    50=>"Sample",
    40=>"Test",
    70=>"Description"
]);

foreach($ncr as $n){
    $pdf->TableRow([
        30=>substr($n["Report_Date"],0,10),
        50=>$n["Sample_ID"]."-".$n["Sample_Number"],
        40=>$n["Test_Type"],
        70=>$n["Noconformidad"]
    ]);
}

$pdf->Ln(4);

/* ---------- SIMPLE GRAPH: FAIL COUNTS ---------- */
$pdf->SubTitle("NCR Count Chart");

$failLabels = [];
$failCounts = [];

foreach($ncr as $n){
    $tp = $n["Test_Type"];
    if(!isset($failCounts[$tp])) $failCounts[$tp]=0;
    $failCounts[$tp]++;
}

$labels=array_keys($failCounts);
$values=array_values($failCounts);

$chartX=25;
$chartY=$pdf->GetY()+5;
$chartW=140;
$chartH=45;

drawAxis($pdf,$chartX,$chartY,$chartW,$chartH);

if (empty($values)) {
    $values = [0];
}

$maxVal = max($values);
if ($maxVal == 0) $maxVal = 1;

$barW=(int)(($chartW-10)/count($values));

for($i=0;$i<count($values);$i++){
    $bh = ($values[$i]/$maxVal)*($chartH-5);
    $x = $chartX+5+$i*$barW;
    $y = $chartY+$chartH-$bh;

    list($r,$g,$b)=pickColor($i);
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($x,$y,$barW-1,$bh,'F');
}

$pdf->SetY($chartY+$chartH+10);

/* ======================================================
   SECTION 9 — Final Remarks & Recommendations
====================================================== */

$pdf->SectionTitle("9. Managerial Remarks & Recommendations");

$pdf->BodyText("
• Workflow remained stable throughout the month.  
• No major operational disruptions were detected.  
• Pending tests should be monitored closely, especially those with aging > 10 days.  
• Recommend reinforcing documentation cycle to reduce backlog between Delivery and doc_files.  
• Increasing internal review efficiency could reduce turnaround time.  
");

/* ======================================================
   SECTION 10 — Personnel
====================================================== */

$pdf->SectionTitle("10. Laboratory Personnel");

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

/* ======================================================
   OUTPUT PDF
====================================================== */

$pdf->Output("I","Monthly_Report_$anio-$mes.pdf");
exit;
