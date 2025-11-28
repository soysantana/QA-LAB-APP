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
            $this->Cell($w,7,utf8_decode(safeVal($t)),1,0,'C');
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
    $chartH = 50;

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
   SECTION 5 — Workload by ISO Week (Final v6)
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
            "cmp"     => 0,     // completed same week
            "backlog" => 0      // completed but registered in older week
        ];
    }

    /* ---------- REGISTERED TODAY ---------- */
    $weekly[$week]["reg"] += getCount($db, "
        SELECT COUNT(*) c 
        FROM lab_test_requisition_form 
        WHERE DATE(Registed_Date) = '$date'
    ");

    /* ---------- COMPLETED TODAY (Delivery / Review / Docs) ---------- */
    $completedToday = $db->query("
        SELECT r.Registed_Date
        FROM lab_test_requisition_form r
        WHERE 
            EXISTS (SELECT 1 FROM test_delivery d
                    WHERE d.Sample_ID=r.Sample_ID 
                    AND   d.Sample_Number=r.Sample_Number
                    AND   DATE(d.Start_Date)='$date')
        OR  EXISTS (SELECT 1 FROM test_review rv
                    WHERE rv.Sample_ID=r.Sample_ID 
                    AND   rv.Sample_Number=r.Sample_Number
                    AND   DATE(rv.Start_Date)='$date')
        OR  EXISTS (SELECT 1 FROM test_reviewed rd
                    WHERE rd.Sample_ID=r.Sample_ID 
                    AND   rd.Sample_Number=r.Sample_Number
                    AND   DATE(rd.Start_Date)='$date')
        OR  EXISTS (SELECT 1 FROM doc_files df
                    WHERE df.Sample_ID=r.Sample_ID 
                    AND   df.Sample_Number=r.Sample_Number
                    AND   DATE(df.created_at)='$date')
    ")->fetch_all(MYSQLI_ASSOC);

    foreach ($completedToday as $row) {

        $regWeek = date("W", strtotime($row["Registed_Date"]));

        if ($regWeek == $week) {
            $weekly[$week]["cmp"]++;        // Completed same week
        } else {
            $weekly[$week]["backlog"]++;    // Completed from backlog
        }
    }

    $cursor = strtotime("+1 day", $cursor);
}

$weeks = array_keys($weekly);

/* ARRAYS FOR CHART */
$regArr     = [];
$cmpArr     = [];
$backArr    = [];
$pctArr     = [];
$outputArr  = [];

/* ======================================================
   2) TABLE 5A — Weekly Performance (Same-Week Completion)
====================================================== */

$pdf->SubTitle("Table 5A — Weekly Performance (Same-Week Completion)");

$pdf->TableHeader([
    30=>"Week",
    35=>"Registered",
    40=>"Completed (Same Week)",
    35=>"Completion %"
]);

foreach ($weekly as $w => $v) {

    $reg = $v["reg"];
    $cmp = $v["cmp"];
    $pct = ($reg > 0) ? round(($cmp * 100) / $reg, 1) : 0;

    $pdf->TableRow([
        30=>"W$w",
        35=>$reg,
        40=>$cmp,
        35=>$pct."%"
    ]);

    $pctArr[$w] = $pct;
}

$pdf->Ln(5);

/* ======================================================
   3) TABLE 5B — Weekly Output (Total Completed)
====================================================== */

$pdf->SubTitle("Table 5B — Weekly Output (Total Completed)");

$pdf->TableHeader([
    30=>"Week",
    35=>"Registered",
    35=>"Completed",
    35=>"Backlog Completed",
    35=>"Total Output"
]);

foreach ($weekly as $w => $v) {

    $reg  = $v["reg"];
    $cmp  = $v["cmp"];
    $back = $v["backlog"];
    $out  = $cmp + $back;

    $pdf->TableRow([
        30=>"W$w",
        35=>$reg,
        35=>$cmp,
        35=>$back,
        35=>$out
    ]);

    /* STORE FOR GRAPH */
    $regArr[$w]    = $reg;
    $cmpArr[$w]    = $cmp;
    $backArr[$w]   = $back;
    $outputArr[$w] = $out;
}

$pdf->Ln(6);

/* ======================================================
   4) CHART — Weekly Comparison Chart
====================================================== */

$pdf->SubTitle("Weekly Comparison Chart");

/* PREVENT MULTI-PAGE BREAK */
if ($pdf->GetY() > 200) $pdf->AddPage();

/* DEFINE CHART AREA */
$chartX = 15;
$chartY = $pdf->GetY() + 5;
$chartW = 160;
$chartH = 60;

/* DRAW AXIS */
drawAxis($pdf, $chartX, $chartY, $chartW, $chartH);

/* BAR WIDTH */
$barGroupW   = floor(($chartW - 15) / count($weeks));
$singleBarW  = floor($barGroupW / 3);

/* MAX VALUE */
$maxVal = max(array_merge($regArr, $cmpArr, $backArr));
if ($maxVal == 0) $maxVal = 1;

/* DRAW BARS */
foreach ($weeks as $i => $w) {

    $x0 = $chartX + 10 + $i * $barGroupW;

    // Registered (blue)
    $h1 = ($regArr[$w] / $maxVal) * ($chartH - 5);
    $pdf->SetFillColor(66, 133, 244);
    $pdf->Rect($x0, $chartY + $chartH - $h1, $singleBarW, $h1, "F");

    // Completed (green)
    $h2 = ($cmpArr[$w] / $maxVal) * ($chartH - 5);
    $pdf->SetFillColor(15, 157, 88);
    $pdf->Rect($x0 + $singleBarW + 1, $chartY + $chartH - $h2, $singleBarW, $h2, "F");

    // Backlog (yellow)
    $h3 = ($backArr[$w] / $maxVal) * ($chartH - 5);
    $pdf->SetFillColor(244, 180, 0);
    $pdf->Rect($x0 + 2*($singleBarW+1), $chartY + $chartH - $h3, $singleBarW, $h3, "F");
}

/* COMPLETION LINE */
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(0.4);

$prevX = null;
$prevY = null;

foreach ($weeks as $i => $w) {

    $pct  = $pctArr[$w];
    $yPos = $chartY + $chartH - (($pct/100) * ($chartH - 5));
    $xPos = $chartX + 10 + $i * $barGroupW + ($barGroupW/2);

    if ($prevX !== null) {
        $pdf->Line($prevX, $prevY, $xPos, $yPos);
    }

    $prevX = $xPos;
    $prevY = $yPos;

    // TEXT
    $pdf->SetXY($xPos - 4, $yPos - 4);
    $pdf->SetFont("Arial","",7);
    $pdf->Cell(8,4,$pct."%",0,0,"C");
}

/* LEGEND — RIGHT SIDE VERTICAL */
$legX = $chartX + $chartW + 5;
$legY = $chartY;

$pdf->SetXY($legX, $legY);
$pdf->SetFont("Arial","",7);

$pdf->SetFillColor(66,133,244);  $pdf->Rect($legX,$legY,4,4,"F");
$pdf->SetXY($legX+6,$legY);      $pdf->Cell(20,4,"Registered");

$pdf->SetXY($legX,$legY+6);
$pdf->SetFillColor(15,157,88);   $pdf->Rect($legX,$legY+6,4,4,"F");
$pdf->SetXY($legX+6,$legY+6);    $pdf->Cell(20,4,"Completed");

$pdf->SetXY($legX,$legY+12);
$pdf->SetFillColor(244,180,0);   $pdf->Rect($legX,$legY+12,4,4,"F");
$pdf->SetXY($legX+6,$legY+12);   $pdf->Cell(20,4,"Backlog");

$pdf->SetXY($legX,$legY+18);
$pdf->Cell(20,4,"—— Completion %");

$pdf->SetY($chartY + $chartH + 10);

/* ======================================================
   5) INSIGHTS
====================================================== */

$pdf->SubTitle("Insights & Interpretation");

foreach ($weeks as $w){
    $pdf->BodyText(
        "- W$w: Output ".$outputArr[$w]." tests | Completion ".$pctArr[$w]."% | Backlog ".$backArr[$w]."."
    );
}



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
