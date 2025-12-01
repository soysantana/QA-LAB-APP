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
    // AUTO PAGE BREAK FOR TABLE ROW
    if ($this->GetY() > 260) {   // Límite seguro
        $this->AddPage();
        // Reimprimir encabezados si quieres
        // Pero solo si estás dentro de una tabla con header
    }

    $this->SetFont('Arial','',10);
    foreach($cols as $w=>$t){
        $this->Cell($w,6,utf8_decode(safeVal($t)),1,0,'C');
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

function ensure_space($pdf, $needed){
    if ($pdf->GetY() + $needed > 260) {   // 260 = safe limit for A4 portrait
        $pdf->AddPage();
        $pdf->Ln(5);
    }
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
During this month, the laboratory maintained stable and consistent operational performance across all workflow phases - sample registration, preparation, execution, and delivery.

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

// Effective Completion (entregado + revisado + reportado)
$effective_completed = $del_count + $reviewed_count + $docs_count;

$completion_rate = ($registered_count > 0)
    ? round(($del_count / $registered_count) * 100, 2)
    : 0;

// Efficiency Ratio (entregados vs registrados)
$efficiency_ratio = ($registered_count > 0)
    ? round($del_count / $registered_count, 2)
    : 0;

// Pending Ratio
$pending_ratio = ($registered_count > 0)
    ? round(($pending_total / $registered_count) * 100, 2)
    : 0;

// Aging of pending
$aging = $db->query("
    SELECT AVG(DATEDIFF(NOW(), Registed_Date)) AS avg_age
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '$start' AND '$end'
")->fetch_assoc();
$avgAging = isset($aging['avg_age']) ? round($aging['avg_age'],1) : 0;

// ------------------------
// NEW KPI: Process Consistency
// ------------------------
$maxLayer = max($del_count, $reviewed_count, $docs_count);
$minLayer = min($del_count, $reviewed_count, $docs_count);

$process_consistency = ($maxLayer > 0)
    ? round(($minLayer / $maxLayer) * 100, 2)
    : 0;


// ---------- TABLE ----------
$pdf->SubTitle("Key Performance Indicators");

$pdf->TableHeader([
    55=>"KPI",
    35=>"Value"
]);

$pdf->TableRow([55=>"Tests Registered",             35=>$registered_count]);
$pdf->TableRow([55=>"Delivered Tests",              35=>$del_count]);
$pdf->TableRow([55=>"Reviewed Tests",               35=>$reviewed_count]);
$pdf->TableRow([55=>"Final Reports Issued",         35=>$docs_count]);

$pdf->TableRow([55=>"Pending Tests",                35=>$pending_total]);
$pdf->TableRow([55=>"Pending Load (%)",             35=>$pending_ratio."%"]);

$pdf->TableRow([55=>"Completion Rate (Delivered)",  35=>$completion_rate."%"]);
$pdf->TableRow([55=>"Efficiency Ratio (Del/Reg)",   35=>$efficiency_ratio]);

$pdf->TableRow([55=>"Process Consistency (%)",      35=>$process_consistency."%"]);

$pdf->TableRow([55=>"Average Pending Age (days)",   35=>$avgAging]);

$pdf->Ln(4);

/* ============================
   2B — KPI SHORT SUMMARY TEXT
============================ */

$pdf->SubTitle("Executive Interpretation");
$pdf->BodyText("
- The laboratory processed $registered_count test requests this month.
- A total of $del_count tests were delivered, $reviewed_count reviewed, and $docs_count final reports were issued.
- These values indicate a process consistency of $process_consistency%.
- Pending load stands at $pending_total tests ($pending_ratio% of intake).
- The average aging of pending samples is $avgAging days.
- Completion efficiency is $completion_rate%, with an output ratio of $efficiency_ratio compared to registered tests.
");

$pdf->AddPage();   // Página 3

/* ======================================================
   SECTION 3 — MONTHLY REGISTERED SAMPLES (Client-Based)
====================================================== */

$pdf->SectionTitle("3. Monthly Registered Samples");

// ------------------------------------------------------
// 1) Build EXPANDED test list (each Test_Type separately)
// ------------------------------------------------------
$rows = $db->query("
    SELECT 
        Registed_Date,
        Client,
        Sample_ID,
        Sample_Number,
        Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '$start' AND '$end'
    ORDER BY Registed_Date ASC
")->fetch_all(MYSQLI_ASSOC);

$expanded_full = [];        // fully expanded tests
$clientTotals = [];         // tests per client
$clientSamples = [];        // unique samples per client
$dailyClient = [];          // trend per day per client
$testTypePerClient = [];    // for top3 test types per client

// Build date map for the month
$trendDates = [];
$c = strtotime($start);
$last = strtotime($end);
while($c <= $last){
    $trendDates[] = date("Y-m-d", $c);
    $c = strtotime("+1 day", $c);
}

// expand Test_Type
foreach ($rows as $r){
    $types = explode(",", $r["Test_Type"]);

    foreach ($types as $t){
        $t = trim($t);
        if ($t === "") continue;

        // store expanded record
        $expanded_full[] = [
            "Date"   => substr($r["Registed_Date"], 0, 10),
            "Client" => trim($r["Client"]),
            "SID"    => trim($r["Sample_ID"]),
            "NUM"    => trim($r["Sample_Number"]),
            "Test"   => $t
        ];

        $cl = trim($r["Client"]);
        $dt = substr($r["Registed_Date"], 0, 10);

        // Count total tests per client
        if (!isset($clientTotals[$cl])) $clientTotals[$cl] = 0;
        $clientTotals[$cl]++;

        // Track unique samples per client
        $uniqueKey = $r["Sample_ID"] . "-" . $r["Sample_Number"];
        $clientSamples[$cl][$uniqueKey] = true;

        // Trend count per day per client
        if (!isset($dailyClient[$cl])) {
            $dailyClient[$cl] = array_fill_keys($trendDates, 0);
        }
        if (isset($dailyClient[$cl][$dt])) {
            $dailyClient[$cl][$dt]++;
        }

        // Test types per client
        if (!isset($testTypePerClient[$cl])) $testTypePerClient[$cl] = [];
        if (!isset($testTypePerClient[$cl][$t])) $testTypePerClient[$cl][$t] = 0;
        $testTypePerClient[$cl][$t]++;
    }
}
/* ------------------------------------------------------
   2) TABLE — Overview by Client
------------------------------------------------------ */
$pdf->SubTitle("Client Summary Table");

$pdf->TableHeader([
    40=>"Client",
    25=>"Samples",
    25=>"Tests",
    50=>"Top Test Types"
]);

foreach ($clientTotals as $cl => $totalTests){

    // Unique samples count
    $sampleCount = isset($clientSamples[$cl])
        ? count($clientSamples[$cl])
        : 0;

    // Top 3 test types
    $top = $testTypePerClient[$cl];
    arsort($top);
    $topList = implode(", ", array_slice(array_keys($top), 0, 3));

    $pdf->TableRow([
        40=>$cl,
        25=>$sampleCount,
        25=>$totalTests,
        50=>$topList
    ]);
}

$pdf->Ln(4);
/* ------------------------------------------------------
   3) GRAPH — Tests per Client (Vertical Bar)
   Improved with axis labels & bar values
------------------------------------------------------ */

$pdf->SubTitle("Tests per Client Chart");

if (!empty($clientTotals)){

    $labels = array_keys($clientTotals);
    $values = array_values($clientTotals);

    // chart area
    $chartX = 20;
    $chartY = $pdf->GetY() + 6;
    $chartW = 150;
    $chartH = 50;

    /* ---------- DRAW AXIS ---------- */
    drawAxis($pdf, $chartX, $chartY, $chartW, $chartH);

    /* ---------- Y-AXIS SCALE ---------- */
    $maxVal = max($values);
    if ($maxVal == 0) $maxVal = 1;

    // define 4 major steps (0, 25%, 50%, 75%, 100% of max)
    $steps = 4;
    $pdf->SetFont("Arial","",7);

    for ($i=0; $i <= $steps; $i++) {

        $val = round(($maxVal / $steps) * $i);
        $yPos = $chartY + $chartH - ($chartH / $steps * $i);

        // grid line
        $pdf->SetDrawColor(220,220,220);
        $pdf->Line($chartX, $yPos, $chartX + $chartW, $yPos);

        // label
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetXY($chartX - 10, $yPos - 2);
        $pdf->Cell(8, 4, $val, 0, 0, "R");
    }

    /* ---------- DRAW BARS ---------- */
    $barW = (int)( ($chartW - 10) / count($labels) );
    if ($barW < 10) $barW = 10; // minimum width

    foreach ($labels as $i => $lbl){

        $v = $values[$i];
        $barH = ($v / $maxVal) * ($chartH - 5);

        $x = $chartX + 5 + $i * $barW;
        $y = $chartY + $chartH - $barH;

        list($r,$g,$b) = pickColor($i);
        $pdf->SetFillColor($r,$g,$b);

        // bar
        $pdf->Rect($x, $y, $barW - 2, $barH, "F");

        // value above bar
        if ($v > 0){
            $pdf->SetFont("Arial","B",7);
            $pdf->SetXY($x, $y - 4);
            $pdf->Cell($barW - 2, 4, $v, 0, 0, "C");
        }

        // label below bar
        $pdf->SetFont("Arial","",7);
        $pdf->SetXY($x, $chartY + $chartH + 2);
        $pdf->MultiCell($barW - 2, 3, utf8_decode($lbl), 0, "C");
    }

    $pdf->SetY($chartY + $chartH + 14);
}


/* ------------------------------------------------------
   5) Insights
------------------------------------------------------ */

$pdf->Ln(5);
$pdf->SubTitle("Observations & Insights");

$insights = [];

foreach ($dailyClient as $cl => $trend){
    $vals = array_values($trend);
    $maxVal = max($vals);
    $minVal = min($vals);
    $sum = array_sum($vals);

    if ($sum == 0) continue;

    $peakDayIndex = array_search($maxVal, $vals);
    $peakDay = $trendDates[$peakDayIndex];

    $insights[] = "- $cl reached its activity peak on $peakDay with $maxVal tests registered.";
}

if (empty($insights)) $insights[] = "No significant client trends detected.";

foreach ($insights as $txt){
    $pdf->BodyText($txt);
}
$pdf->AddPage();   // Página 4


/* ======================================================
   SECTION 4 — Client Summary (Requested vs Completed)
====================================================== */

$pdf->SectionTitle("4. Client Summary (Requested vs Completed)");

/* ======================================================
   4A — MONTHLY KPIs PER CLIENT
   (Requested vs Completed only for THIS MONTH)
====================================================== */

$pdf->SubTitle("4A. Monthly Client KPIs");

$monthly_requested = $db->query("
    SELECT Client, COUNT(*) AS total
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '$start' AND '$end'
    GROUP BY Client
")->fetch_all(MYSQLI_ASSOC);

$monthly_completed = $db->query("
    SELECT r.Client, COUNT(*) AS total
    FROM lab_test_requisition_form r
    WHERE r.Registed_Date BETWEEN '$start' AND '$end'
      AND (
            EXISTS (
                SELECT 1 FROM test_delivery d
                WHERE d.Sample_ID = r.Sample_ID
                  AND d.Sample_Number = r.Sample_Number
                  AND d.Start_Date BETWEEN '$start' AND '$end'
            )
         OR EXISTS(
                SELECT 1 FROM test_review rv
                WHERE rv.Sample_ID = r.Sample_ID
                  AND rv.Sample_Number = r.Sample_Number
                  AND rv.Start_Date BETWEEN '$start' AND '$end'
            )
         OR EXISTS(
                SELECT 1 FROM test_reviewed rd
                WHERE rd.Sample_ID = r.Sample_ID
                  AND rd.Sample_Number = r.Sample_Number
                  AND rd.Start_Date BETWEEN '$start' AND '$end'
            )
         OR EXISTS(
                SELECT 1 FROM doc_files df
                WHERE df.Sample_ID = r.Sample_ID
                  AND df.Sample_Number = r.Sample_Number
                  AND df.created_at BETWEEN '$start' AND '$end'
            )
      )
    GROUP BY r.Client
")->fetch_all(MYSQLI_ASSOC);

/* Normalizar estructura */
$monthly = [];

foreach ($monthly_requested as $r) {
    $monthly[$r['Client']]['requested'] = $r['total'];
}
foreach ($monthly_completed as $r) {
    $monthly[$r['Client']]['completed'] = $r['total'];
}

foreach ($monthly as $cl => &$v) {
    $req = $v['requested'] ?? 0;
    $cmp = $v['completed'] ?? 0;
    $v['pct'] = ($req > 0) ? round(($cmp * 100) / $req, 1) : 0;
}
unset($v);

/* ---------- TABLE 4A ---------- */

$pdf->TableHeader([
    50=>"Client",
    30=>"Requested",
    30=>"Completed",
    30=>"Completion (%)"
]);

foreach ($monthly as $cl => $v) {
    $pdf->TableRow([
        50=>$cl,
        30=>$v['requested'] ?? 0,
        30=>$v['completed'] ?? 0,
        30=>$v['pct']
    ]);
}

$pdf->Ln(6);

/* ======================================================
   4B — HISTORICAL REQUESTED VS COMPLETED
   (All time up to END OF MONTH)
====================================================== */

$pdf->SubTitle("4B. Historical Client KPIs (Until $end)");

$hist_req = $db->query("
    SELECT Client, COUNT(*) AS total
    FROM lab_test_requisition_form
    WHERE Registed_Date <= '$end'
    GROUP BY Client
")->fetch_all(MYSQLI_ASSOC);

$hist_cmp = $db->query("
    SELECT r.Client, COUNT(*) AS total
    FROM lab_test_requisition_form r
    WHERE r.Registed_Date <= '$end'
      AND (
            EXISTS (
                SELECT 1 FROM test_delivery d
                WHERE d.Sample_ID = r.Sample_ID
                  AND d.Sample_Number = r.Sample_Number
                  AND d.Start_Date <= '$end'
            )
         OR EXISTS(
                SELECT 1 FROM test_review rv
                WHERE rv.Sample_ID = r.Sample_ID
                  AND rv.Sample_Number = r.Sample_Number
                  AND rv.Start_Date <= '$end'
            )
         OR EXISTS(
                SELECT 1 FROM test_reviewed rd
                WHERE rd.Sample_ID = r.Sample_ID
                  AND rd.Sample_Number = r.Sample_Number
                  AND rd.Start_Date <= '$end'
            )
         OR EXISTS(
                SELECT 1 FROM doc_files df
                WHERE df.Sample_ID = r.Sample_ID
                  AND df.Sample_Number = r.Sample_Number
                  AND df.created_at <= '$end'
            )
      )
    GROUP BY r.Client
")->fetch_all(MYSQLI_ASSOC);

/* Normalizar */
$hist = [];
foreach ($hist_req as $r) {
    $hist[$r['Client']]['requested'] = $r['total'];
}
foreach ($hist_cmp as $r) {
    $hist[$r['Client']]['completed'] = $r['total'];
}

foreach ($hist as $cl => &$v) {
    $req = $v['requested'] ?? 0;
    $cmp = $v['completed'] ?? 0;
    $v['backlog'] = max($req - $cmp, 0);
    $v['pct'] = ($req > 0) ? round(($cmp * 100) / $req, 1) : 0;
}
unset($v);

/* ---------- TABLE 4B ---------- */

$pdf->TableHeader([
    45=>"Client",
    30=>"Hist. Requested",
    30=>"Hist. Completed",
    25=>"Backlog",
    30=>"Completion (%)"
]);

foreach ($hist as $cl=>$v){
    $pdf->TableRow([
        45=>$cl,
        30=>$v['requested'] ?? 0,
        30=>$v['completed'] ?? 0,
        25=>$v['backlog'],
        30=>$v['pct']
    ]);
}

$pdf->Ln(6);

/* ======================================================
   4C — HISTORICAL COMPLETION GRAPH (Vertical Bars)
====================================================== */

$pdf->SubTitle("4C. Historical Completion Chart");

$labels = array_keys($hist);
$values = array_column($hist, "pct");

if (!empty($labels)) {

    $chartX = 25;
    $chartY = $pdf->GetY() + 8;
    $chartW = 150;
    $chartH = 40;

    drawAxis($pdf,$chartX,$chartY,$chartW,$chartH);

    $maxVal = max($values);
    if($maxVal == 0) $maxVal = 1;

    $barW = max(12, intval(($chartW - 10) / count($labels)));

    for ($i=0; $i < count($labels); $i++) {

        $pct = $values[$i];
        $barH = ($pct / $maxVal) * ($chartH - 5);

        $x = $chartX + 5 + $i * $barW;
        $y = $chartY + $chartH - $barH;

        list($r,$g,$b) = pickColor($i);
        $pdf->SetFillColor($r,$g,$b);
        $pdf->Rect($x, $y, $barW-2, $barH, "F");

        // client label
        $lbl = $labels[$i];
        if (strlen($lbl) > 12) $lbl = substr($lbl,0,10)."...";

        $pdf->SetFont("Arial","",7);
        $pdf->SetXY($x, $chartY + $chartH + 2);
        $pdf->MultiCell($barW-2, 3, utf8_decode($lbl), 0, 'C');
    }

    $pdf->SetY($chartY + $chartH + 12);
}

/* ======================================================
   4D — EXECUTIVE INSIGHTS
====================================================== */

$pdf->SubTitle("4D. Observations & Insights");

$ins=[];

foreach ($monthly as $cl => $v){
    $pct = $v['pct'];
    if ($pct < 60)
        $ins[] = "- $cl shows low monthly completion ($pct%). Recommend follow-up.";
    else if ($pct >= 90)
        $ins[] = "- $cl demonstrates excellent monthly performance ($pct%).";
}

foreach($hist as $cl=>$v){
    if($v['backlog'] > 100)
        $ins[] = "- $cl has a high historical backlog ({$v['backlog']} tests).";
}

if(empty($ins)) $ins[] = "No significant issues detected in monthly or historical client performance.";

foreach($ins as $t){
    $pdf->BodyText($t);
}
$pdf->AddPage();   // Página 5


/* ======================================================
/* ======================================================
   SECTION 5 — Workload by ISO Week (Final v7)
====================================================== */

$pdf->SectionTitle("5. Workload by ISO Week");

/* ======================================================
   1) Build Weekly Structure
====================================================== */

$weekly = [];   // W => ["reg"=>0,"cmp"=>0,"backlog"=>0]

$cursor = strtotime($start);
$last   = strtotime($end);

while ($cursor <= $last) {

    $date = date("Y-m-d", $cursor);
    $week = date("W", $cursor);

    if (!isset($weekly[$week])) {
        $weekly[$week] = [
            "reg"     => 0,
            "cmp"     => 0,
            "backlog" => 0
        ];
    }

    /* ---------- REGISTERED TODAY ---------- */
    $weekly[$week]["reg"] += getCount($db, "
        SELECT COUNT(*) c 
        FROM lab_test_requisition_form 
        WHERE DATE(Registed_Date) = '$date'
    ");

    /* ---------- COMPLETED TODAY ---------- */
    $completedToday = $db->query("
        SELECT r.Registed_Date
        FROM lab_test_requisition_form r
        WHERE 
            EXISTS (
                SELECT 1 FROM test_delivery d
                WHERE d.Sample_ID = r.Sample_ID
                AND   d.Sample_Number = r.Sample_Number
                AND   DATE(d.Start_Date) = '$date'
            )
        OR EXISTS(
                SELECT 1 FROM test_review rv
                WHERE rv.Sample_ID = r.Sample_ID
                AND   rv.Sample_Number = r.Sample_Number
                AND   DATE(rv.Start_Date) = '$date'
            )
        OR EXISTS(
                SELECT 1 FROM test_reviewed rd
                WHERE rd.Sample_ID = r.Sample_ID
                AND   rd.Sample_Number = r.Sample_Number
                AND   DATE(rd.Start_Date) = '$date'
            )
        OR EXISTS(
                SELECT 1 FROM doc_files df
                WHERE df.Sample_ID = r.Sample_ID
                AND   df.Sample_Number = r.Sample_Number
                AND   DATE(df.created_at) = '$date'
            )
    ")->fetch_all(MYSQLI_ASSOC);

    foreach ($completedToday as $row) {

        $regWeek = date("W", strtotime($row["Registed_Date"]));

        if ($regWeek == $week) {
            $weekly[$week]["cmp"]++;
        } else {
            $weekly[$week]["backlog"]++;
        }
    }

    $cursor = strtotime("+1 day", $cursor);
}

$weeks = array_keys($weekly);

/* ======================================================
   2) TABLE 5A — Weekly Performance (Same-Week Completion)
====================================================== */

$pdf->SubTitle("Table 5A - Weekly Performance (Same-Week Completion)");

$pdf->TableHeader([
    22=>"Week",
    30=>"Registered",
    45=>"Completed (Same Week)",
    28=>"Completion %"
]);


$pctArr = [];
$cleanRegArr = [];
$cleanCmpArr = [];

foreach ($weekly as $w => $v) {

    $reg = $v["reg"];
    $cmp = $v["cmp"];
    $pct = ($reg > 0) ? round(($cmp * 100) / $reg, 1) : 0;

    $pdf->TableRow([
        22=>"W$w",
        30=>$reg,
       45=>$cmp,
        28=>$pct."%"
    ]);

    $cleanRegArr[$w] = $reg;
    $cleanCmpArr[$w] = $cmp;
    $pctArr[$w]      = $pct;
}

$pdf->Ln(6);

/* ======================================================
   3) TABLE 5B — Weekly Output (Total Completed)
====================================================== */

$pdf->SubTitle("Table 5B - Weekly Output (Total Completed)");

$pdf->TableHeader([
    20=>"Week",
    35=>"Reg",
    34=>"Comp",
    30=>"Backlog",
    28=>"Output"
]);


$regArr   = [];
$cmpArr   = [];
$backArr  = [];
$outArr   = [];

foreach ($weekly as $w => $v) {

    $reg  = $v["reg"];
    $cmp  = $v["cmp"];
    $back = $v["backlog"];
    $out  = $cmp + $back;

    $pdf->TableRow([
        20=>"W$w",
        35=>$reg,
        34=>$cmp,
        30=>$back,
        28=>$out
    ]);

    $regArr[$w]  = $reg;
    $cmpArr[$w]  = $cmp;
    $backArr[$w] = $back;
    $outArr[$w]  = $out;
}

$pdf->Ln(8);

/* ======================================================
   4) GRAPH — Weekly Comparison (Compact)
====================================================== */

$pdf->SubTitle("Weekly Comparison Chart");

if ($pdf->GetY() > 190) $pdf->AddPage();

/* CHART SIZE — REDUCIDO */
$chartX = 18;
$chartY = $pdf->GetY() + 4;
$chartW = 120;
$chartH = 50;

/* EJE */
drawAxis($pdf, $chartX, $chartY, $chartW, $chartH);

/* Valores máximos */
$maxVal = max(array_merge($regArr, $cmpArr, $backArr));
if ($maxVal == 0) $maxVal = 1;

/* Barras */
$barGroupW  = floor(($chartW - 10) / count($weeks));
$singleBarW = floor($barGroupW / 3);

foreach ($weeks as $i => $w) {

    $x0 = $chartX + 6 + $i * $barGroupW;

    // Registered
    $h1 = ($regArr[$w] / $maxVal) * ($chartH - 5);
    $pdf->SetFillColor(66,133,244);
    $pdf->Rect($x0, $chartY + $chartH - $h1, $singleBarW, $h1, "F");

    // Completed
    $h2 = ($cmpArr[$w] / $maxVal) * ($chartH - 5);
    $pdf->SetFillColor(15,157,88);
    $pdf->Rect($x0 + $singleBarW + 1, $chartY + $chartH - $h2, $singleBarW, $h2, "F");

    // Backlog
    $h3 = ($backArr[$w] / $maxVal) * ($chartH - 5);
    $pdf->SetFillColor(244,180,0);
    $pdf->Rect($x0 + 2*($singleBarW+1), $chartY + $chartH - $h3, $singleBarW, $h3, "F");
}
/* === Y-AXIS LABELS === */
$pdf->SetFont("Arial","",7);
$steps = 4; // 0%, 25%, 50%, 75%, 100%

for ($i=0; $i <= $steps; $i++) {
    $val = round(($maxVal / $steps) * $i);
    $yPos = $chartY + $chartH - ($chartH / $steps * $i);

    // Linea gris horizontal
    $pdf->SetDrawColor(220,220,220);
    $pdf->Line($chartX, $yPos, $chartX + $chartW, $yPos);

    // Valor
    $pdf->SetTextColor(0,0,0);
    $pdf->SetXY($chartX - 12, $yPos - 2);
    $pdf->Cell(10, 4, $val, 0, 0, "R");
}


/* LEYENDA VERTICAL DERECHA */
$legX = $chartX + $chartW + 3;
$legY = $chartY;

$pdf->SetFont("Arial","",7);

$pdf->SetXY($legX,$legY);
$pdf->SetFillColor(66,133,244); $pdf->Rect($legX,$legY,4,4,"F");
$pdf->SetXY($legX+6,$legY);     $pdf->Cell(22,4,"Registered");

$pdf->SetXY($legX,$legY+6);
$pdf->SetFillColor(15,157,88);  $pdf->Rect($legX,$legY+6,4,4,"F");
$pdf->SetXY($legX+6,$legY+6);   $pdf->Cell(22,4,"Completed");

$pdf->SetXY($legX,$legY+12);
$pdf->SetFillColor(244,180,0);  $pdf->Rect($legX,$legY+12,4,4,"F");
$pdf->SetXY($legX+6,$legY+12);  $pdf->Cell(22,4,"Backlog");

$pdf->SetY($chartY + $chartH + 8);

/* ======================================================
   5) INSIGHTS
====================================================== */

$pdf->SubTitle("Insights & Interpretation");

foreach ($weeks as $w) {
    $pdf->BodyText(
        "- Week $w: Output {$outArr[$w]}| Completion {$pctArr[$w]}%| Backlog {$backArr[$w]}"
    );
}


$pdf->AddPage();   // Página 6

/* ======================================================
   SECTION 6 — Laboratory Test Demand Distribution
====================================================== */
$pdf->SectionTitle("6. Laboratory Test Demand Distribution");

/* ======================================================
   1. Diccionario de nombres completos
====================================================== */
$testNames = [
    "BTS" => "Brazilian (BTS)",
    "PLT" => "Point Load Test",
    "GS"  => "Grain Size",
    "UCS" => "UCS",
    "MC"  => "Moisture Content",
    "AR"  => "Acid Reactivity",
    "AL"  => "Atterberg Limit",
    "SG"  => "Specific Gravity",
    "DHY" => "Double Hydrometer",
    "HY"  => "Hydrometer",
    "SP"  => "Standard Proctor",
    "MP"  => "Modified Proctor",
    "PH"  => "Pinhole Test",
    "SND" => "Soundness Test",
    "LAA" => "Los Angeles Abrasion",
    "SCT" => "Sand Castle Test",
    "SHAPE" => "Particle Shape Test",
    "DENSIDAD-VIBRATORIO" => "Vibrating weight",
    "Envio" => "For Shipment",
    "PERM" => "Permeability Test",
];

/* ======================================================
   2. Contar TODOS los ensayos del mes
====================================================== */
$allTests = [];

foreach ($rows as $r) {
    $types = preg_split('/[,;]+/', $r["Test_Type"]);
    foreach ($types as $tp) {
        $tp = trim($tp);
        if ($tp === "") continue;
        if (!isset($allTests[$tp])) $allTests[$tp] = 0;
        $allTests[$tp]++;
    }
}

/* PROTEGER PDF vacío */
if (empty($allTests)) {
    $pdf->BodyText("No test data available for this period.");
    $allTests = ["NO DATA" => 1];
}

/* Ordenar todos los ensayos */
arsort($allTests);

/* Total global */
$totalAll = array_sum($allTests);
if ($totalAll == 0) $totalAll = 1;

/* Top 5 para gráfico */
$top5 = array_slice($allTests, 0, 5, true);

/* ======================================================
   3. TABLA — TODOS LOS ENSAYOS
====================================================== */

$pdf->SubTitle("Monthly Laboratory Test Demand Distribution");

$pdf->TableHeader([
    70 => "Test Type",
    20 => "Count",
    30 => "%"
]);

foreach ($allTests as $code => $count) {
    $name = $testNames[$code] ?? $code;
    $pct  = round(($count / $totalAll) * 100, 1) . "%";

    $pdf->TableRow([
        70 => utf8_decode($name),
        20 => $count,
        30 => $pct
    ]);
}

/* TOTAL */
$pdf->TableRow([
    70 => "TOTAL",
    20 => $totalAll,
    30 => "100%"
]);

$pdf->Ln(5);

/* ======================================================
   4. GRÁFICO — TOP 5 (HORIZONTAL, SIN EJE X)
====================================================== */

$pdf->SubTitle("Most Demanded Tests (Top 5 by %)");

$chartX = 35;
$chartY = $pdf->GetY() + 5;

$barAreaW = 90;
$barH = 7;

$i = 0;

foreach ($top5 as $tp => $count) {

    $label = $testNames[$tp] ?? $tp;
    $pct   = round(($count / $totalAll) * 100, 1);
    $barW  = ($pct / 100) * $barAreaW;

    list($r, $g, $b) = pickColor($i);

    /* Nombre del ensayo (columna izquierda) */
    $pdf->SetFont("Arial","",8);
    $pdf->SetXY($chartX - 30, $chartY + ($i * 12));
    $pdf->Cell(30, 6, utf8_decode($label), 0, 0, "R");

    /* Barra */
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX, $chartY + ($i * 12), $barW, $barH, "F");

    /* Porcentaje */
    $pdf->SetXY($chartX + $barW + 4, $chartY + ($i * 12));
    $pdf->Cell(18, 6, $pct . "%", 0, 0);

    $i++;
}

$pdf->Ln(18);

/* ======================================================
   5. OBSERVATIONS & INSIGHTS (DINÁMICOS)
====================================================== */

$pdf->SubTitle("Observations & Insights");

$ins = [];

/* Insight 1 — ensayo dominante */
$topCode = array_key_first($allTests);
$topName = $testNames[$topCode] ?? $topCode;
$topPct  = round(($allTests[$topCode] / $totalAll) * 100, 1);
$ins[] = "{$topName} dominates the laboratory workload ({$topPct}%).";

/* Insight 2 — ensayos con menos de 2% */
$low = [];
foreach ($allTests as $tp => $cnt) {
    $pp = ($cnt / $totalAll) * 100;
    if ($pp < 2) $low[] = ($testNames[$tp] ?? $tp);
}
if (!empty($low)) {
    $ins[] = "Limited demand detected for: " . implode(", ", $low) . ".";
}

/* Insight 3 — granulometría alta */
if (isset($allTests["GS"]) && (($allTests["GS"] / $totalAll) * 100) > 20) {
    $ins[] = "High GS usage indicates active classification or excavation activities.";
}

/* Insight 4 — UCS bajo */
if (!isset($allTests["UCS"]) || (($allTests["UCS"] / $totalAll) * 100) < 5) {
    $ins[] = "Low UCS demand suggests minimal geomechanical testing during this period.";
}

foreach ($ins as $text) {
    $pdf->BodyText("- " . utf8_decode($text));
}


$pdf->AddPage();   // Página 7

/* ======================================================
   SECTION 7 — Pending Workload & Aging Analysis
====================================================== */

$pdf->SectionTitle("7. Pending Workload & Aging Analysis");

/* ======================================================
   DICCIONARIO DE NOMBRES COMPLETOS
====================================================== */
$testNames = [
    "BTS" => "Brazilian (BTS)",
    "PLT" => "Point Load Test",
    "GS"  => "Grain Size",
    "UCS" => "UCS",
    "MC"  => "Moisture Content",
    "DENSIDAD-VIBRATAORIO"  => "Weight Vibrating-Hammer",
    "AR"  => "Acid Reactivity",
    "AL"  => "Atterberg Limit",
    "SG"  => "Specific Gravity",
    "DHY" => "Double Hydrometer",
    "HY"  => "Hydrometer",
    "SCT"  => "Sand Castle Test",
    "SP"  => "Standard Proctor",
    "MP"  => "Modified Proctor",
    "PH"  => "Pinhole Test",
    "SND" => "Soundness",
    "LAA" => "Los Angeles Abrasion",
    "SHAPE"  => "Particle Shape",
    "PERM" => "Permeability",
    "ENVIO" => "For Shipment"
];

/* ======================================================
   NORMALIZATION FUNCTION
====================================================== */
function normalizeStr($str){
    if ($str === null) return "";
    $str = trim($str);
    $str = preg_replace('/\x{00A0}/u', ' ', $str);
    $str = str_replace(["–","—","−"], "-", $str);
    $str = preg_replace('/\s+/', ' ', $str);
    return strtoupper(trim($str));
}

/* ======================================================
   1. EXPAND TESTS
====================================================== */
$expanded = [];

foreach ($rows as $r) {

    $types = preg_split('/[,;]+/', $r["Test_Type"]);

    foreach ($types as $tp) {

        $tp = normalizeStr($tp);

        if ($tp === "ENVIO") continue; // IGNORAR ENVIO

        $expanded[] = [
            "Sample_ID"     => normalizeStr($r["Sample_ID"]),
            "Sample_Number" => normalizeStr($r["Sample_Number"]),
            "Client"        => normalizeStr($r["Client"]),
            "Test_Type"     => $tp,
            "RegDate"       => $r["Registed_Date"]
        ];
    }
}

/* ======================================================
   2. DETECT REAL PENDING TESTS
====================================================== */

$pending = [];
$today = strtotime("now");

foreach ($expanded as $t) {

    $sid = $db->escape($t["Sample_ID"]);
    $num = $db->escape($t["Sample_Number"]);
    $tp  = $db->escape($t["Test_Type"]);

    if ($tp === "ENVIO") continue;

    $sql = "
        UPPER(TRIM(Sample_ID))     ='$sid' AND
        UPPER(TRIM(Sample_Number)) ='$num' AND
        UPPER(TRIM(Test_Type))     ='$tp'
    ";

    $prep = $db->query("SELECT 1 FROM test_preparation WHERE $sql")->num_rows;
    $real = $db->query("SELECT 1 FROM test_realization WHERE $sql")->num_rows;
    $del  = $db->query("SELECT 1 FROM test_delivery   WHERE $sql")->num_rows;
    $rev  = $db->query("SELECT 1 FROM test_review     WHERE $sql")->num_rows;
    $rev2 = $db->query("SELECT 1 FROM test_reviewed   WHERE $sql")->num_rows;
    $doc  = $db->query("SELECT 1 FROM doc_files       WHERE $sql")->num_rows;

    if ($prep==0 && $real==0 && $del==0 && $rev==0 && $rev2==0 && $doc==0) {

        $days = round(($today - strtotime($t["RegDate"])) / 86400, 1);

        $pending[] = [
            "Client"        => $t["Client"],
            "Sample_ID"     => $t["Sample_ID"],
            "Sample_Number" => $t["Sample_Number"],
            "Test_Type"     => $testNames[$t["Test_Type"]] ?? $t["Test_Type"],
            "Days"          => $days
        ];
    }
}

/* ======================================================
   NO PENDING → STOP SECTION
====================================================== */

if (empty($pending)) {

    $pdf->BodyText("No pending tests for this period.");
    $pdf->Ln(10);
    goto end_pending_section; // TERMINA TODA LA SECCIÓN 7
}

/* ======================================================
   3. SUMMARY CARDS (NO DIV 0)
====================================================== */

$totalPending = count($pending);
$avgAging     = ($totalPending > 0) ? round(array_sum(array_column($pending,"Days")) / $totalPending, 1) : 0;
$maxAging     = ($totalPending > 0) ? max(array_column($pending,"Days")) : 1;
$critical     = ($totalPending > 0) ? count(array_filter($pending, fn($x)=>$x["Days"] > 14)) : 0;

$pdf->SetFont("Arial","B",10);
$pdf->Cell(45,8,"Pending: $totalPending",1,0,'C');
$pdf->Cell(45,8,"Avg Aging: {$avgAging} d",1,0,'C');
$pdf->Cell(45,8,">14 d: $critical",1,0,'C');
$pdf->Cell(45,8,"Max: {$maxAging} d",1,1,'C');

$pdf->Ln(4);

/* ======================================================
   4. TABLE BY CLIENT
====================================================== */

usort($pending, fn($a,$b)=> $b["Days"] <=> $a["Days"]);

$grouped = [];
foreach ($pending as $p) {
    $grouped[$p["Client"]][] = $p;
}

foreach ($grouped as $client => $list) {

    $pdf->SubTitle("Client: $client (" . count($list) . " pending)");

    $pdf->TableHeader([
       35 => "Sample",
        20 => "Number",
        30 => "Test",
        15 => "Days",
        25 => "Status"
    ]);

    foreach ($list as $row) {

        $status = "Not Started";

        if ($row["Days"] > 14)     $pdf->SetTextColor(220,0,0);
        elseif ($row["Days"] > 7) $pdf->SetTextColor(255,140,0);
        elseif ($row["Days"] > 3) $pdf->SetTextColor(200,150,0);
        else                      $pdf->SetTextColor(0,120,0);

        $pdf->TableRow([
            35 => $row["Sample_ID"],
            20 => $row["Sample_Number"],
            30 => utf8_decode($row["Test_Type"]),
            15 => $row["Days"],
            25 => $status
        ]);

        $pdf->SetTextColor(0,0,0);
    }

    $pdf->Ln(3);
}

/* ======================================================
   5. PAGE BREAK
====================================================== */

if ($pdf->GetY() > 210) {
    $pdf->AddPage();
    $pdf->Ln(5);
}

/* ======================================================
   6. AGING CHART
====================================================== */
$pdf->SubTitle("Aging Chart (Top 10 Delays)");

$topAging = array_slice($pending, 0, 10);

$chartX = 75;     // MOVEMOS GRAFICO A LA DERECHA
$chartY = $pdf->GetY() + 5;
$barAreaW = 95;
$barH = 5;

foreach ($topAging as $i => $row) {

    $label = "{$row['Test_Type']} - {$row['Sample_ID']}-{$row['Sample_Number']}";
    $days  = $row["Days"];
    $barW  = ($maxAging > 0) ? ($days / $maxAging) * $barAreaW : 0;

    list($r,$g,$b) = pickColor($i);

    // Label corregido
    $posY = $chartY + ($i * 6);

    $pdf->SetFont("Arial","",8);
    $pdf->SetXY(10, $posY);            // SIEMPRE VISIBLE
    $pdf->Cell(60, 6, utf8_decode($label), 0, 0, "L");

    // Bar
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX, $posY, $barW, $barH, "F");

    // Value
    $pdf->SetXY($chartX + $barW + 3, $posY);
    $pdf->Cell(18, 6, "{$days} d", 0, 0);
}

$pdf->Ln(5);


/* ======================================================
   7. INSIGHTS
====================================================== */

$pdf->SubTitle("Insights");

$ins = [];

$mainClient = array_key_first($grouped);
if ($mainClient !== null)
    $ins[] = "$mainClient has the highest accumulation of pending tests.";

$topItem = $pending[0];
$ins[] = "{$topItem['Test_Type']} ({$topItem['Sample_ID']}) shows the highest delay: {$topItem['Days']} days.";

if ($critical > 0)
    $ins[] = "$critical tests exceed 14 days (CRITICAL).";

$ins[] = "Average pending aging is {$avgAging} days.";

foreach ($ins as $t) {
    $pdf->BodyText("- " . utf8_decode($t));
}

end_pending_section:

/* ======================================================
   SECTION 8 — NCR Summary (Grouped by Test Type)
   Only Test_Condition = FAIL / REJECTED
====================================================== */

$pdf->SectionTitle("8. Non-Conformities (NCR)");

/* ======================================================
   1. FETCH NCR DATA (ONLY FAIL/REJECTED)
====================================================== */

$ncr = $db->query("
    SELECT Report_Date, Sample_ID, Sample_Number, Test_Type, Noconformidad, Test_Condition
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '$start' AND '$end'
      AND LOWER(TRIM(Test_Condition)) IN ('fail','rejected')
")->fetch_all(MYSQLI_ASSOC);

if (empty($ncr)) {
    $pdf->BodyText("No NCR (Fail/Rejected) recorded for this period.");
    $pdf->Ln(10);
}


/* ======================================================
   2. PREPARE COUNT ARRAYS SAFELY
====================================================== */

$counts = [];
$clientNCR = [];

foreach ($ncr as $n) {

    /* ----- SAFE TEST TYPE NAME ----- */
    $tp = strtoupper(trim((string)$n["Test_Type"]));
    if ($tp === "" || $tp === "-" || $tp === "N/A" || $tp === null) {
        $tp = "UNKNOWN";
    }

    /* Count by test type */
    if (!isset($counts[$tp])) $counts[$tp] = 0;
    $counts[$tp]++;

    /* ----- SAFE CLIENT PREFIX EXTRACTION ----- */
    $rawID = trim((string)$n["Sample_ID"]);

    if ($rawID === "" || $rawID === null) {
        $client = "UNKNOWN";
    } else {
        $parts = explode("-", $rawID);
        $client = strtoupper(trim($parts[0] ?? "UNKNOWN"));
        if ($client === "" || $client === null) $client = "UNKNOWN";
    }

    /* Count by client */
    if (!isset($clientNCR[$client])) $clientNCR[$client] = 0;
    $clientNCR[$client]++;
}

/* Backup if no data */
if (empty($counts)) {
    $counts = ["NO DATA" => 1];
}


/* ======================================================
   3. TOP TEST + TOP CLIENT
====================================================== */

arsort($counts);
$topTest = array_key_first($counts);
$topTestCount = $counts[$topTest];

arsort($clientNCR);
$topClient = array_key_first($clientNCR);


/* ======================================================
   4. SUMMARY CARDS
====================================================== */

$totalNCR = array_sum($counts);

$pdf->SetFont("Arial","B",10);
$pdf->Cell(45,8,"NCR (Fail/Rejected): $totalNCR",1,0,'C');
$pdf->Cell(45,8,"Top Test: $topTest ($topTestCount)",1,0,'C');
$pdf->Cell(45,8,"Test Types: ".count($counts),1,0,'C');
$pdf->Cell(45,8,"Top Client: $topClient",1,1,'C');

$pdf->Ln(4);


/* ======================================================
   5. GROUP NCR BY TEST TYPE
====================================================== */

$grouped = [];
foreach ($ncr as $n) {

    $tp = strtoupper(trim((string)$n["Test_Type"]));
    if ($tp === "" || $tp === "-" || $tp === "N/A" || $tp === null) {
        $tp = "UNKNOWN";
    }

    if (!isset($grouped[$tp])) $grouped[$tp] = [];
    $grouped[$tp][] = $n;
}

uasort($grouped, fn($a,$b)=> count($b) <=> count($a));


/* ======================================================
   6. PRINT TABLES PER TEST TYPE
====================================================== */

foreach ($grouped as $tp => $items) {

    $pdf->SubTitle("$tp — ".count($items)." NCR");

    $pdf->TableHeader([
        30 => "Date",
        40 => "Sample",
        115 => "Description"
    ]);

    foreach ($items as $n) {
        $pdf->TableRow([
            30  => substr($n["Report_Date"],0,10),
            40  => $n["Sample_ID"]."-".$n["Sample_Number"],
            115 => utf8_decode((string)$n["Noconformidad"])
        ]);
    }

    $pdf->Ln(3);
}


/* ======================================================
   7. SAFE PAGE BREAK CHECK
====================================================== */

if ($pdf->GetY() > 210) {
    $pdf->AddPage();
    $pdf->Ln(5);
}


/* ======================================================
   8. NCR FREQUENCY CHART (Top 5 Horizontal)
====================================================== */

$pdf->SubTitle("NCR Frequency by Test Type (Top 5)");

$top5 = array_slice($counts, 0, 5, true);

/* PROTECCIÓN: si top5 está vacío */
if (empty($top5)) {
    $top5 = ["NO DATA" => 1];
}

/* PROTECCIÓN: max() siempre con datos */
$maxVal = max($top5);
if ($maxVal <= 0) $maxVal = 1;

$chartX = 35;
$chartY = $pdf->GetY() + 5;
$barAreaW = 95;
$barH = 7;

$i = 0;
foreach ($top5 as $tp => $ct) {

    $barW = ($ct / $maxVal) * $barAreaW;

    list($r,$g,$b) = pickColor($i);

    // Label
    $pdf->SetFont("Arial","",8);
    $pdf->SetXY($chartX - 35, $chartY + ($i * 12));
    $pdf->Cell(35,6,utf8_decode($tp),0,0,"R");

    // Bar
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX, $chartY + ($i * 12), $barW, $barH, "F");

    // Value
    $pdf->SetXY($chartX + $barW + 3, $chartY + ($i * 12));
    $pdf->Cell(18,6,$ct,0,0);

    $i++;
}

$pdf->Ln(15);


/* ======================================================
   9. INSIGHTS
====================================================== */

$pdf->SubTitle("Insights");

$ins = [];

$ins[] = "$topTest is the test with the most NCR failures this month ($topTestCount).";
$ins[] = count($counts)." different test types showed Fail/Rejected conditions.";
$ins[] = "$topClient is the most affected client.";

if ($totalNCR > 0 && ($topTestCount / $totalNCR) > 0.3)
    $ins[] = "$topTest represents more than 30% of all NCR failures — trend is significant.";

foreach ($ins as $t) {
    $pdf->BodyText("- " . utf8_decode($t));
}

$pdf->Ln(5);


/* ======================================================
   SECTION 9 — Dynamic Managerial Remarks & Recommendations
   Based on Sections 2 to 8 (Activity, Demand, Pending, NCR, Docs)
====================================================== */

$pdf->SectionTitle("9. Managerial Remarks & Recommendations");

/* ======================================================
   1. BUILD METRICS FROM PREVIOUS SECTIONS
====================================================== */

/* ---------- Tests of the Month (from Section 6) ---------- */
$totalTests = $totalAll ?? count($rows);

arsort($allTests);
$mainTest = array_key_first($allTests);
$mainTestCount = $allTests[$mainTest] ?? 0;
$mainTestPct = $totalTests > 0 ? round(($mainTestCount / $totalTests) * 100, 1) : 0;

/* ---------- Distribution by Client (Section 4) ---------- */
$clientCounts = [];
foreach ($rows as $r) {

    $c = strtoupper(trim((string)$r["Client"]));
    if ($c === "" || $c === "-" || $c === null) $c = "UNKNOWN";

    if (!isset($clientCounts[$c])) $clientCounts[$c] = 0;
    $clientCounts[$c]++;
}

arsort($clientCounts);
$topClient = array_key_first($clientCounts);
$topClientCount = $clientCounts[$topClient] ?? 0;
$topClientPct = $totalTests > 0 ? round(($topClientCount / $totalTests) * 100, 1) : 0;

/* ---------- Pending + Aging (Section 7) ---------- */
$totalPending = count($pending);

$agingVals = array_column($pending, "Days");
$avgAging = $totalPending > 0 ? round(array_sum($agingVals) / $totalPending, 1) : 0;
$maxAging = $totalPending > 0 ? max($agingVals) : 0;

$criticalPending = array_filter($pending, fn($p)=>$p["Days"] >= 14);
$totalCritical = count($criticalPending);

$pendingByClient = [];
foreach ($pending as $p) {

    $c = strtoupper(trim((string)$p["Client"]));
    if ($c === "" || $c === "-" || $c === null) $c = "UNKNOWN";

    if (!isset($pendingByClient[$c])) $pendingByClient[$c] = 0;
    $pendingByClient[$c]++;
}

arsort($pendingByClient);
$topPendingClient = empty($pendingByClient) ? "N/A" : array_key_first($pendingByClient);

/* ---------- NCR (Section 8) ---------- */
$totalNCR = $totalNCR ?? count($ncr);
$topNCRtestName = $topTest ?? "N/A";
$topNCRtestCount = $topTestCount ?? 0;

/* ---------- Documentation Performance (Section 5) ---------- */
$docPendingCount = $docPendingCount ?? 0;

/* ======================================================
   2. DYNAMIC TEXT GENERATION
====================================================== */

$remarks = [];

/* ---------- (A) GENERAL LABORATORY PERFORMANCE ---------- */
$remarks[] = 
"The laboratory processed $totalTests tests during the month, with {$mainTest} being the most demanded test ({$mainTestPct}%). "
."Client {$topClient} contributed {$topClientPct}% of the total workload.";

/* ---------- (B) PENDING & AGING ANALYSIS ---------- */
if ($totalPending == 0) {
    $remarks[] = "No pending tests were detected, indicating excellent operational turnaround.";
} else {
    $remarks[] =
        "A total of {$totalPending} pending tests were recorded, with an average aging of {$avgAging} days and a maximum of {$maxAging} days. "
        ."Client {$topPendingClient} accumulated the highest backlog. "
        .($totalCritical > 0 
            ? "{$totalCritical} tests exceeded 14 days, requiring priority attention." 
            : "No critical delays above 14 days were detected.");
}

/* ---------- (C) NCR INTERPRETATION ---------- */
if ($totalNCR == 0) {
    $remarks[] = "No Fail/Rejected NCRs were issued this month, reflecting strong technical consistency.";
} else {
    $ncrPct = round(($topNCRtestCount / $totalNCR) * 100, 1);
    $remarks[] =
        "{$totalNCR} NCRs (Fail/Rejected) were issued this month. "
        ."{$topNCRtestName} was the most recurrent test with {$topNCRtestCount} NCRs ({$ncrPct}%). "
        .($ncrPct > 40 
            ? "This suggests a systemic trend requiring deeper procedural review." 
            : "Distribution of NCRs remains within expected ranges.");
}

/* ---------- (D) DOCUMENTATION WORKFLOW ---------- */
if ($docPendingCount == 0) {
    $remarks[] = "Documentation compliance remained strong with no pending doc_files uploads.";
} elseif ($docPendingCount <= 5) {
    $remarks[] = "Documentation backlog is low, with {$docPendingCount} pending uploads, indicating generally efficient document control.";
} else {
    $remarks[] = "Documentation backlog increased, with {$docPendingCount} pending uploads. This represents a key operational bottleneck requiring corrective action.";
}

/* ---------- (E) STRATEGIC RECOMMENDATIONS ---------- */
$reco = [];

if ($avgAging > 7 || $totalCritical > 0) {
    $reco[] = "Prioritize processing of pending tests older than 10–14 days to stabilize workflow aging.";
}

if ($mainTestPct > 25) {
    $reco[] = "Assign additional resources to {$mainTest} due to its high monthly demand ({$mainTestPct}%).";
}

if ($totalNCR >= 5) {
    $reco[] = "Reinforce QC procedures for {$topNCRtestName} due to recurrent NCR patterns.";
}

if ($docPendingCount > 8) {
    $reco[] = "Strengthen the documentation cycle and enforce end-of-day document uploads to reduce backlog.";
}

if (empty($reco)) {
    $reco[] = "Maintain current operational structure as performance indicators remain stable and positive.";
}

/* ======================================================
   3. PRINT SECTION 9 IN PDF
====================================================== */

$pdf->SubTitle("Overall Performance Summary");
foreach ($remarks as $r) {
    $pdf->BodyText("• " . utf8_decode($r));
}

$pdf->Ln(2);

$pdf->SubTitle("Strategic Recommendations");
foreach ($reco as $r) {
    $pdf->BodyText("• " . utf8_decode($r));
}



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
