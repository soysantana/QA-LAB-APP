<?php

ob_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors',1);
error_reporting(E_ALL);

$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');


/* ============================================================
   CUSTOM PDF CLASS — SAME STYLE AS MONTHLY REPORT
============================================================ */
class PDF extends FPDF {

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
        $this->SetFont('Arial','',9);
        $this->MultiCell(0,5, utf8_decode($txt));
    }

    function TableHeader($cols){
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(230,230,230);
        foreach ($cols as $w=>$t){
            $this->Cell($w,7,utf8_decode($t),1,0,'C',true);
        }
        $this->Ln();
        $this->SetFont('Arial','',9);
    }

    function TableRow($cols){
        foreach ($cols as $w=>$t){
            $this->Cell($w,6,utf8_decode($t),1,0,'L');
        }
        $this->Ln();
    }
}

/* ============================================================
   UTILITIES
============================================================ */
function pickColor($i){
    $colors = [
        [45,95,185],
        [220,70,70],
        [80,170,90],
        [200,160,40],
        [140,75,140],
        [70,150,150]
    ];
    return $colors[$i % count($colors)];
}

function drawAxis($pdf,$x,$y,$w,$h){
    $pdf->SetDrawColor(0,0,0);
    $pdf->Line($x,$y+$h,$x+$w,$y+$h); // X axis
    $pdf->Line($x,$y,$x,$y+$h);        // Y axis
}

/* ============================================================
   PARAMETERS
============================================================ */
$year = isset($_GET['year']) ? (int)$_GET['year'] : date("Y");

$start = "$year-01-01";
$end   = "$year-12-31";
/* ============================================================
   GET LIST OF CLIENTS FOR COVER PAGE
============================================================ */
$clients = $db->query("
    SELECT DISTINCT Client 
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '$start' AND '$end'
      AND Client <> ''
    ORDER BY Client ASC
")->fetch_all(MYSQLI_ASSOC);

$clientList = array_map(fn($c)=>$c['Client'], $clients);
$clientStr = implode(" • ", $clientList);

/* ============================================================
   PDF START
============================================================ */
$pdf = new PDF("P","mm","Letter");
$pdf->AddPage();

/* ======================================================
   PORTADA — ANNUAL LABORATORY REPORT
====================================================== */



// ---------------- LOGO ----------------
$logo = "../assets/img/Pueblo-Viejo.jpg";
if (file_exists($logo)) {
    $pdf->Image($logo, 12, 12, 38); // mismo tamaño que reporte mensual
}

// ---------------- TÍTULO PRINCIPAL ----------------
$pdf->SetFont("Arial", "B", 22);
$pdf->SetXY(0, 55);
$pdf->Cell(210, 12, "ANNUAL LABORATORY REPORT - $anio", 0, 1, "C");

$pdf->Ln(2);

// Línea divisoria
$pdf->SetDrawColor(70, 70, 70);
$pdf->Line(40, $pdf->GetY(), 170, $pdf->GetY());
$pdf->Ln(8);

// ---------------- SUBTÍTULO CORPORATIVO ----------------
$pdf->SetFont("Arial", "B", 12);
$pdf->SetTextColor(50,50,50);
$pdf->Cell(210, 8, "Soil - Aggregates - Rock| CQA Laboratory Unit", 0, 1, "C");

$pdf->SetFont("Arial", "", 11);
$pdf->Cell(210, 6, "PV Project", 0, 1, "C");

$pdf->Ln(10);

// ======================================================
//  CLIENTES DEL AÑO
// ======================================================
$clientes = $db->query("
    SELECT DISTINCT UPPER(TRIM(Client)) AS C
    FROM lab_test_requisition_form
    WHERE YEAR(Registed_Date) = '$anio'
    ORDER BY C ASC
")->fetch_all(MYSQLI_ASSOC);

$clientList = implode(", ", array_column($clientes, "C"));
if ($clientList == "") $clientList = "NO CLIENT DATA";

// título de clientes
$pdf->SetFont("Arial","B",12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(210,7,"Clients Served in $anio:",0,1,"C");

// contenido
$pdf->SetFont("Arial","",10);
$pdf->MultiCell(0,6, utf8_decode($clientList), 0, "C");

$pdf->Ln(15);

// ---------------- BLOQUE DE INFORMACIÓN ----------------
$pdf->SetFont("Arial","",11);
$pdf->SetTextColor(60,60,60);

$pdf->Cell(210,7, utf8_decode("Prepared by: Wendin De Jesús - Chief Laboratory"), 0, 1, "C");
$pdf->Cell(210,7, utf8_decode("Location: Pueblo Viejo - Sánchez Ramírez - Dominican Republic"), 0, 1, "C");
$pdf->Cell(210,7, utf8_decode("Reporting Period: January 1st, $anio - December 31st, $anio"), 0, 1, "C");
$pdf->Cell(210,7, utf8_decode("Document Type: Annual CQA Laboratory Performance Report"), 0, 1, "C");

$pdf->Ln(20);

// ---------------- LÍNEA FINAL ----------------
$pdf->SetDrawColor(120,120,120);
$pdf->Line(40, $pdf->GetY(), 170, $pdf->GetY());



/* ============================================================
   SECTION 2 — GLOBAL YEARLY SUMMARY (FINAL COMPLETE VERSION)
============================================================ */
$pdf->AddPage();
$pdf->SectionTitle("2. Global Yearly Summary");

/* ============================================================
   1. RETRIEVE RAW DATA
============================================================ */
$rows = $db->query("
    SELECT 
        Registed_Date,
        Client,
        Sample_ID,
        Sample_Number,
        Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);

/* Build client list */
$clientList = [];
foreach ($rows as $r){
    $cl = trim((string)$r["Client"]);
    if ($cl !== "" && !in_array($cl, $clientList)){
        $clientList[] = $cl;
    }
}

/* Expand test types */
$expanded = [];
$testCountByType   = [];
$testCountByClient = [];
$testsByMonth      = array_fill(1, 12, 0);

foreach ($rows as $r){

    $types  = explode(",", $r["Test_Type"]);
    $client = trim($r["Client"]);
    $month  = (int)substr($r["Registed_Date"], 5, 2);

    foreach ($types as $t){
        $t = trim($t);
        if ($t === "") continue;

        $expanded[] = [
            "Date"   => substr($r["Registed_Date"], 0, 10),
            "Client" => $client,
            "Test"   => $t
        ];

        /* count test type */
        if (!isset($testCountByType[$t])) $testCountByType[$t] = 0;
        $testCountByType[$t]++;

        /* count per client */
        if (!isset($testCountByClient[$client])) $testCountByClient[$client] = 0;
        $testCountByClient[$client]++;

        /* count per month */
        if ($month >= 1 && $month <= 12){
            $testsByMonth[$month]++;
        }
    }
}

/* Totals */
$totalTests    = count($expanded);
$totalSamples  = count($rows);
$totalClients  = count($clientList);
$avgTestsPerSample = ($totalSamples > 0) ? round($totalTests / $totalSamples, 2) : 0;

/* Fallbacks */
if (empty($testCountByType))   $testCountByType   = ["NO DATA" => 1];
if (empty($testCountByClient)) $testCountByClient = ["NO DATA" => 1];


/* ============================================================
   2. SUMMARY CARDS (KPIs)
============================================================ */
$pdf->Ln(3);
$pdf->SetFont("Arial","B",11);

$cardW = 47;
$cardH = 12;

$pdf->SetFillColor(245,245,245);
$pdf->SetDrawColor(90,90,90);

$pdf->Cell($cardW, $cardH, "Total Tests: $totalTests", 1, 0, 'C', true);
$pdf->Cell($cardW, $cardH, "Total Samples: $totalSamples", 1, 0, 'C', true);
$pdf->Cell($cardW, $cardH, "Clients: $totalClients", 1, 0, 'C', true);
$pdf->Cell($cardW, $cardH, "Avg Tests/Sample: $avgTestsPerSample", 1, 1, 'C', true);

$pdf->Ln(8);


/* ============================================================
   3. QUARTER SUMMARY TABLE
============================================================ */
$pdf->SubTitle("Quarter Summary");

$Q = [1=>0,2=>0,3=>0,4=>0];
$daysQ = [1=>90,2=>91,3=>92,4=>92];

foreach ($expanded as $e){
    $m = (int)substr($e["Date"], 5, 2);
    if ($m <= 3) $Q[1]++;
    elseif ($m <= 6) $Q[2]++;
    elseif ($m <= 9) $Q[3]++;
    else $Q[4]++;
}

$pdf->TableHeader([
    40=>"Quater",
    25=>"Tests",
    30=>"% of Year",
    35=>"Avg/Day",
   
]);

$prevQ = null;
foreach ([1,2,3,4] as $q){

    $pct = ($totalTests > 0) ? round(($Q[$q] / $totalTests) * 100, 1)."%" : "0%";
    $avgDay = round($Q[$q] / $daysQ[$q], 2);
    $delta = "N/A";

    if (!is_null($prevQ)){
        $change = $Q[$q] - $prevQ;
        $delta  = ($change > 0 ? "+$change" : (string)$change);
    }

    $pdf->TableRow([
        40=>"Q$q",
        25=>$Q[$q],
        30=>$pct,
        35=>"$avgDay tests/day",
      
    ]);

    $prevQ = $Q[$q];
}

$pdf->Ln(10);


/* ============================================================
   4. QUARTER COMPARISON CHART (Horizontal Bars)
============================================================ */
$pdf->SubTitle("Quarter Workload Comparison");

$chartX = 35;
$chartY = $pdf->GetY() + 10;
$barAreaW = 115;
$barH = 7;

$maxQ = max($Q);
if ($maxQ <= 0) $maxQ = 1;

$i = 0;
foreach ($Q as $q => $val){

    $barW = ($val / $maxQ) * $barAreaW;
    list($r,$g,$b) = pickColor($i);

    $pdf->SetFont("Arial","",8);
    $posY = $chartY + ($i * 12);

    $pdf->SetXY($chartX - 25, $posY);
    $pdf->Cell(25, 6, "Q$q", 0, 0, "R");

    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX, $posY, $barW, $barH, "F");

    $pdf->SetXY($chartX + $barW + 3, $posY);
    $pdf->Cell(20, 6, $val, 0, 0);

    $i++;
}

$pdf->Ln(20);


/* ============================================================
   5. MONTHLY TREND CHART (Line + Markers)
============================================================ */
$pdf->SubTitle("Monthly Workload Trend (Line Chart)");

$chartX = 30;
$chartY = $pdf->GetY() + 10;
$chartW = 150;
$chartH = 45;

drawAxis($pdf, $chartX, $chartY, $chartW, $chartH);

$maxM = max($testsByMonth);
if ($maxM <= 0) $maxM = 1;

$pdf->SetDrawColor(0,0,0);
$pdf->SetFont("Arial","",7);

/* Lines */
$prevX = null;
$prevY = null;

foreach ($testsByMonth as $m => $v){

    $x = $chartX + ($chartW/12)*($m-0.5);
    $y = $chartY + $chartH - ($v / $maxM) * ($chartH - 5);

    /* line */
    if (!is_null($prevX)){
        $pdf->Line($prevX, $prevY, $x, $y);
    }

    /* marker */
    $pdf->SetFillColor(0,0,0);
    $pdf->Rect($x-1.5, $y-1.5, 3, 3, "F");

    /* label */
    $pdf->SetXY($x-5, $y-10);
    $pdf->Cell(10, 5, $v, 0, 0, "C");

    $prevX = $x;
    $prevY = $y;
}

/* month labels */
$monthNames = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
$pdf->SetFont("Arial","",7);

for ($m=1; $m<=12; $m++){
    $x = $chartX + ($chartW/12)*($m-0.5);
    $pdf->SetXY($x-7, $chartY + $chartH + 2);
    $pdf->Cell(14, 5, $monthNames[$m-1], 0, 0, "C");
}

$pdf->Ln(25);

/* ============================================================
   TEST TYPE DICTIONARY — Full Names
============================================================ */
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

/* ============================================================
   6. TOP TEST TYPES (Table)
============================================================ */
$pdf->SubTitle("Annual Total Test per Test Type");

// Ordenar test types
arsort($testCountByType);

// Extraer los 5 mayores
$topTests = array_slice($testCountByType, 0, 25, true);

// ------- ENCABEZADO -------
$pdf->TableHeader([
    70=>"Test Type",
    40=>"Tests",
    50=>"% of Annual"
]);

$totalTop = 0;

// ------- FILAS -------
foreach ($topTests as $abbr => $cnt){

    // Nombre completo
    $fullName = isset($testNames[$abbr]) ? $testNames[$abbr] : $abbr;

    // % anual
    $pct = ($totalTests > 0)
        ? round(($cnt / $totalTests) * 100, 1) . "%"
        : "0%";

    // Sumar al total
    $totalTop += $cnt;

    $pdf->TableRow([
        70 => utf8_decode($fullName),
        40 => $cnt,
        50 => $pct
    ]);
}

// ------- TOTAL FINAL -------
$pdf->SetFont("Arial","B",9);

$finalPct = ($totalTests > 0)
    ? round(($totalTop / $totalTests) * 100, 1) . "%"
    : "0%";

$pdf->TableRow([
    70 => "TOTAL ",
    40 => $totalTop,
    50 => $finalPct
]);

$pdf->SetFont("Arial","",9);
$pdf->Ln(5);
/* ============================================================
   6B. HORIZONTAL BAR CHART — ONLY TOP 10 TEST TYPES
============================================================ */
$pdf->SubTitle("Top 10 Test Types -  Chart");

// Tomar solo los primeros 10
$top10 = array_slice($topTests, 0, 10, true);

$chartX = 25;
$chartY = $pdf->GetY() + 8;
$barWmax = 120;
$barH = 6;

$maxVal = max($top10);
if ($maxVal <= 0) $maxVal = 1;

$i = 0;

foreach ($top10 as $abbr => $cnt){

    $y = $chartY + ($i * 7.5);  // más espacio para 10 barras

    // Nombre completo
    $label = isset($testNames[$abbr]) ? $testNames[$abbr] : $abbr;

    // Ancho de barra
    $bw = ($cnt / $maxVal) * $barWmax;

    // Color
    list($r,$g,$b) = pickColor($i);

    // Etiqueta
    $pdf->SetXY($chartX, $y);
    $pdf->SetFont("Arial","",7);
    $pdf->Cell(50, 6, utf8_decode($label), 0, 0);

    // Barra
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX + 32, $y, $bw, $barH, "F");

    // Valor numérico
    $pdf->SetXY($chartX + 32 + $bw + 3, $y);
    $pdf->Cell(15, 6, $cnt, 0, 0);

    $i++;
}

$pdf->Ln($i * 8 + 5);



/* ============================================================
   7. TOP CLIENTS (Table) — With Total Row
============================================================ */
$pdf->SubTitle("Annual Total Test per Clients");

// Ordenar por cantidad
arsort($testCountByClient);

// Tomar máximo 20 (como pediste)
$topClients = array_slice($testCountByClient, 0, 25, true);

// Encabezado
$pdf->TableHeader([
    70 => "Client",
    40 => "Tests",
    50 => "% of Annual"
]);

$totalTopClients = 0;

// Filas
foreach ($topClients as $cl => $cnt){

    // Nunca permitir nombre vacío
    if (trim($cl) === "") $cl = "UNKNOWN";

    // % anual
    $pct = ($totalTests > 0)
        ? round(($cnt / $totalTests) * 100, 1) . "%"
        : "0%";

    // Acumulado
    $totalTopClients += $cnt;

    $pdf->TableRow([
        70 => utf8_decode($cl),
        40 => $cnt,
        50 => $pct
    ]);
}

// TOTAL FINAL
$pdf->SetFont("Arial","B",9);

$finalPct = ($totalTests > 0)
    ? round(($totalTopClients / $totalTests) * 100, 1) . "%"
    : "0%";

$pdf->TableRow([
    70 => "TOTAL (Top Clients)",
    40 => $totalTopClients,
    50 => $finalPct
]);

$pdf->SetFont("Arial","",9);
$pdf->Ln(5);

/* ============================================================
   7B. HORIZONTAL BAR CHART — ONLY TOP 5 CLIENTS
============================================================ */
$pdf->SubTitle(" Top 5 Clients -  Chart");

// Limitar a los 5 mayores
$top5 = array_slice($topClients, 0, 5, true);

$chartX = 25;
$chartY = $pdf->GetY() + 8;
$barWmax = 120;
$barH = 7; // un poquito más alto porque son solo 5 barras

$maxClient = max($top5);
if ($maxClient <= 0) $maxClient = 1;

$i = 0;

foreach ($top5 as $cl => $cnt){

    if (trim($cl) === "") $cl = "UNKNOWN";

    // Espaciado vertical optimizado para 5 barras
    $y = $chartY + ($i * 11);

    // Calcular ancho proporcional
    $bw = ($cnt / $maxClient) * $barWmax;

    // Color único por índice
    list($r,$g,$b) = pickColor($i);

    // Etiqueta del cliente
    $pdf->SetXY($chartX, $y);
    $pdf->SetFont("Arial","",8);
    $pdf->Cell(50, 7, utf8_decode($cl), 0, 0);

    // Barra
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX + 32, $y, $bw, $barH, "F");

    // Valor numérico
    $pdf->SetXY($chartX + 32 + $bw + 3, $y);
    $pdf->Cell(15, 7, $cnt, 0, 0);

    $i++;
}

// Espacio después del gráfico
$pdf->Ln($i * 12 + 5);


/* ============================================================
   8. INSIGHTS (Executive English Summary)
============================================================ */
$pdf->SubTitle("Insights");

$ins = [];

/* 1. Top quarter */
$topQ = array_search(max($Q), $Q);
$ins[] = "Q$topQ was the strongest quarter with {$Q[$topQ]} tests.";

/* 2. Top test of the year */
$topTest = array_key_first($testCountByType);
$ins[] = "$topTest was the most demanded test type of the year.";

/* 3. Top client */
$topClient = array_key_first($testCountByClient);
$ins[] = "$topClient generated the highest test volume among all clients.";

/* 4. Growth tendencies */
if ($Q[2] > $Q[1]) $ins[] = "Workload increased from Q1 to Q2.";
if ($Q[3] > $Q[2]) $ins[] = "Workload continued rising from Q2 to Q3.";
if ($Q[4] > $Q[3]) $ins[] = "Q4 closed the year with additional growth.";

/* 5. Monthly stability or variability */
$maxMonth = array_search(max($testsByMonth), $testsByMonth);
$minMonth = array_search(min($testsByMonth), $testsByMonth);
$ins[] = "Peak monthly activity occurred in ".date("F", mktime(0,0,0,$maxMonth,1)).".";
$ins[] = "Lowest monthly activity occurred in ".date("F", mktime(0,0,0,$minMonth,1)).".";

/* PRINT INSIGHTS */
foreach ($ins as $t){
    $pdf->BodyText("- " . utf8_decode($t));
}

$pdf->Ln(5);

/* ============================================================
   SECTION # 3— LABORATORY OPERATIONAL PERFORMANCE
   (Turnaround, Material Overview, Workload Index, Equipment Load)
============================================================ */


$pdf->SectionTitle("3. Laboratory Operational Performance");

/* ============================================================
   0. TEST TYPE FULL NAMES & COMPLEXITY INDEX (TCI)
============================================================ */

$TEST_FULL = [
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


$TCI = [
    "HY"  => 3.0,
    "DHY" => 3.0,
    "SND" => 3.5,
    "UCS" => 2.5,
    "PH"  => 2.5,
    "LAA" => 2.0,
    "AL"  => 1.5,
    "SP"  => 1.2,
    "MP"  => 1.2,
    "SG"  => 1.3,
    "GS"  => 1.0,
    "PLT" => 1.0,
    "MC"  => 0.8,
    "SHAPE" => 0.9,
    "SCT" => 0.8,
    "Envio" => 0.4,
    "PERM" => 1.8
];

/* ============================================================
   1. TURNAROUND PERFORMANCE
============================================================ */
$pdf->SubTitle("1. Turnaround Performance (Registration - Preparation - Realization - Delivery)");

$turnRegPrep = [];
$turnPrepReal = [];
$turnRealDel = [];
$turnTotal = [];

foreach ($rows as $r){

    $id = $r["Sample_ID"];
    $num = $r["Sample_Number"];
    $types = explode(",", $r["Test_Type"]);
    $regDate = $r["Registed_Date"];

    foreach ($types as $t){
        $t = trim($t);
        if ($t=="") continue;

        /* PREPARATION */
        $prep = find_by_sql("SELECT Start_Date FROM test_preparation 
            WHERE Sample_ID='{$id}' AND Sample_Number='{$num}' AND Test_Type='{$t}' LIMIT 1");
        $prepDate = $prep ? $prep[0]["Start_Date"] : null;

        /* REALIZATION */
        $real = find_by_sql("SELECT Start_Date FROM test_realization 
            WHERE Sample_ID='{$id}' AND Sample_Number='{$num}' AND Test_Type='{$t}' LIMIT 1");
        $realDate = $real ? $real[0]["Start_Date"] : null;

        /* DELIVERY */
        $del = find_by_sql("SELECT Start_Date FROM test_delivery 
            WHERE Sample_ID='{$id}' AND Sample_Number='{$num}' AND Test_Type='{$t}' LIMIT 1");
        $delDate = $del ? $del[0]["Start_Date"] : null;

        /* CALCULATE DAYS */
        if ($regDate && $prepDate){
            $turnRegPrep[] = (strtotime($prepDate) - strtotime($regDate)) / 86400;
        }
        if ($prepDate && $realDate){
            $turnPrepReal[] = (strtotime($realDate) - strtotime($prepDate)) / 86400;
        }
        if ($realDate && $delDate){
            $turnRealDel[] = (strtotime($delDate) - strtotime($realDate)) / 86400;
        }
        if ($regDate && $delDate){
            $turnTotal[] = (strtotime($delDate) - strtotime($regDate)) / 86400;
        }
    }
}

function avgOrZero($arr){ return count($arr)>0 ? round(array_sum($arr)/count($arr),2) : 0; }

$avg1 = avgOrZero($turnRegPrep);
$avg2 = avgOrZero($turnPrepReal);
$avg3 = avgOrZero($turnRealDel);
$avg4 = avgOrZero($turnTotal);

/* TABLE */
$pdf->TableHeader([
    70=>"Process",
    50=>"Avg Days"
]);

$pdf->TableRow([70=>"Registration - Preparation", 50=>$avg1]);
$pdf->TableRow([70=>"Preparation -Realization", 50=>$avg2]);
$pdf->TableRow([70=>"Realization -Delivery", 50=>$avg3]);
$pdf->TableRow([70=>"Total Turnaround Time", 50=>$avg4]);

$pdf->Ln(5);

/* BAR CHART TURNAROUND */
$chartX = 30;
$chartY = $pdf->GetY() + 10;
$barW = 100;
$barH = 7;

$vals = [$avg1, $avg2, $avg3, $avg4];
$labels = ["Reg - Prep","Prep - Real","Real - Del","Total"];
$max = max($vals);
if ($max<=0) $max=1;

for ($i=0;$i<4;$i++){
    $bw = ($vals[$i]/$max)*$barW;
    list($r,$g,$b) = pickColor($i);

    $y = $chartY + ($i*10);

    $pdf->SetXY($chartX - 30, $y);
    $pdf->Cell(28, 6, $labels[$i], 0, 0, "R");

    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX, $y, $bw, $barH, "F");

    $pdf->SetXY($chartX + $bw + 3, $y);
    $pdf->Cell(20, 6, $vals[$i], 0, 0);
}

$pdf->Ln(30);

/* ============================================================
   2. MATERIAL OVERVIEW — WITH CUSTOM DICTIONARY
============================================================ */
$pdf->SubTitle("2. Material Overview");

/* ======================
   MATERIAL DICTIONARY
====================== */
function classifyMaterial($mat, $client) {

    $mat = strtolower(trim($mat));
    $client = strtoupper(trim($client));

    /* ====================================================
       PRIORIDAD 1 — CLIENTE MRM → SIEMPRE ROCK
    ==================================================== */
    if ($client === "MRM") return "Rock";

    /* ====================================================
       PRIORIDAD 2 — DETECCIÓN DE ROCA POR MATERIAL
    ==================================================== */
    $rockKeywords = ["rock", "roca", "rockfill", "rf", "trf", "irf", "uff"];

    foreach ($rockKeywords as $k){
        if (strpos($mat, $k) !== false){
            return "Rock";
        }
    }

    /* ====================================================
       PRIORIDAD 3 — SOIL
    ==================================================== */
    if (strpos($mat, "common") !== false) return "Soil";
    if (strpos($mat, "lpf") !== false) return "Soil";

    /* ====================================================
       PRIORIDAD 4 — AGGREGATES
    ==================================================== */
    if (strpos($mat, "ff") !== false) return "Aggregates";   // Fine Filter
    if (strpos($mat, "cf") !== false) return "Aggregates";   // Coarse Filter
    if (strpos($mat, "utf") !== false) return "Aggregates";  // Ultra Filter

    /* ====================================================
       PRIORIDAD 5 — CONCRETE
    ==================================================== */
    if (strpos($mat, "concrete") !== false) return "Concrete";
    if (strpos($mat, "agg") !== false) return "Concrete";

    /* ====================================================
       DEFAULT
    ==================================================== */
    return "Unknown";
}

/* ======================
   PROCESS MATERIALS
====================== */

$materialCountSamples = [];
$materialCountTests = [];
$materialTopClient = [];

foreach ($rows as $r){

    $client = trim($r["Client"]);
    $mat_raw = trim($r["Material_Type"]);

    /* CLASSIFY MATERIAL ACCORDING TO RULES */
    $mat = classifyMaterial($mat_raw, $client);

    $id = $r["Sample_ID"];
    $num = $r["Sample_Number"];
    $types = explode(",", $r["Test_Type"]);

    /* UNIQUE SAMPLES */
    if (!isset($materialCountSamples[$mat])) $materialCountSamples[$mat] = [];
    $materialCountSamples[$mat]["{$id}-{$num}"] = true;

    /* TEST COUNTS */
    foreach ($types as $t){
        $t = trim($t);
        if ($t=="") continue;

        if (!isset($materialCountTests[$mat])) $materialCountTests[$mat] = 0;
        $materialCountTests[$mat]++;

        /* TOP CLIENT PER MATERIAL */
        if (!isset($materialTopClient[$mat])) $materialTopClient[$mat] = [];
        if (!isset($materialTopClient[$mat][$client])) $materialTopClient[$mat][$client] = 0;
        $materialTopClient[$mat][$client]++;
    }
}

/* ======================
   MATERIAL TABLE
====================== */

$pdf->TableHeader([
    40=>"Material",
    20=>"Samples",
    25=>"Tests",
    35=>"Top Client"
]);

foreach ($materialCountTests as $mat=>$cnt){

    $samples = count($materialCountSamples[$mat]);

    arsort($materialTopClient[$mat]);
    $topClient = array_key_first($materialTopClient[$mat]);
    if (trim($topClient)=="") $topClient="Unknown";

    $pdf->TableRow([
        40=>$mat,
        20=>$samples,
        25=>$cnt,
        35=>$topClient
    ]);
}

$pdf->Ln(12);

/* ======================
   MATERIAL HORIZONTAL BAR CHART
====================== */

$chartX = 28;
$chartY = $pdf->GetY();
$barWmax = 110;
$barH = 6;

$maxM = max($materialCountTests);
if ($maxM<=0) $maxM=1;

$i=0;
foreach ($materialCountTests as $mat=>$cnt){
    list($r,$g,$b)=pickColor($i);
    $y = $chartY + ($i*7.2);

    $bw = ($cnt/$maxM)*$barWmax;

    $pdf->SetXY($chartX-20,$y);
    $pdf->Cell(20,6,$mat,0,0);

    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX,$y,$bw,$barH,"F");

    $pdf->SetXY($chartX+$bw+5,$y);
    $pdf->Cell(10,6,$cnt);

    $i++;
}

$pdf->Ln($i*9 + 15);


/* ============================================================
   3. TEST COMPLEXITY — WORKLOAD INDEX
============================================================ */
$pdf->SubTitle("3. Test Complexity Index  - Workload Index");

$workload = [];

foreach ($testCountByType as $abbr=>$cnt){
    $tci = isset($TCI[$abbr]) ? $TCI[$abbr] : 1.0;
    $wi = $cnt * $tci;
    $workload[$abbr] = $wi;
}

arsort($workload);

$pdf->TableHeader([
    35=>"Test Type",
    25=>"Tests",
    20=>"TCI",
    40=>"Workload Index"
]);

foreach ($workload as $abbr=>$wi){
    $full = isset($TEST_FULL[$abbr]) ? $TEST_FULL[$abbr] : $abbr;
    $cnt = $testCountByType[$abbr];
    $tci = isset($TCI[$abbr]) ? $TCI[$abbr] : 1.0;

    $pdf->TableRow([
        35=>$full,
        25=>$cnt,
        20=>$tci,
        40=>$wi
    ]);
}

$pdf->Ln(10);

/* ============================================================
   Horizontal Bars — Workload (TOP 10 ONLY)
============================================================ */

/* Obtener los 10 mayores Workload Index */
$top10WI = array_slice($workload, 0, 10, true);

$chartX = 40;
$chartY = $pdf->GetY();
$barWmax = 110;
$barH = 6;

$maxWI = max($top10WI);
if ($maxWI <= 0) $maxWI = 1;

$i = 0;

foreach ($top10WI as $abbr => $wi) {

    $full = isset($TEST_FULL[$abbr]) ? $TEST_FULL[$abbr] : $abbr;
    list($r, $g, $b) = pickColor($i);

    /* spacing corregido */
    $y = $chartY + ($i * 9);

    /* ancho proporcional */
    $bw = ($wi / $maxWI) * $barWmax;

    /* nombre */
    $pdf->SetXY($chartX - 30, $y);
    $pdf->Cell(28, 6, utf8_decode($full), 0, 0);

    /* barra */
    $pdf->SetFillColor($r, $g, $b);
    $pdf->Rect($chartX, $y, $bw, $barH, "F");

    /* valor */
    $pdf->SetXY($chartX + $bw + 5, $y);
    $pdf->Cell(12, 6, number_format($wi,1));

    $i++;
}

/* espacio final */
$pdf->Ln(($i * 9) + 15);


/* ============================================================
   4. EQUIPMENT UTILIZATION INDEX (Sorted Descending)
============================================================ */
$pdf->SubTitle("4. Equipment Utilization Index");

$equipmentMap = [
    "Sieves"            => ["GS","AL"],
    "Hydrometer Stand"  => ["HY","DHY"],
    "Proctor Mold"      => ["SP","MP"],
    "LAA Drum"          => ["LAA"],
    "Oven / Balance"    => ["MC","SG","AL","GS"],
];

/* ============================
   1. Compute Equipment Load
=============================== */
$equipmentLoad = [];

foreach ($equipmentMap as $equip => $testArr){
    $load = 0;
    foreach ($testArr as $abbr){
        if (isset($testCountByType[$abbr])){
            $load += $testCountByType[$abbr];
        }
    }
    $equipmentLoad[$equip] = $load;
}

/* ============================
   2. ORDER DESCENDING
=============================== */
arsort($equipmentLoad); // ← AQUÍ SE ORDENA DE MAYOR A MENOR

/* ============================
   3. TABLE (Already Sorted)
=============================== */

$pdf->TableHeader([
    70=>"Equipment",
    50=>"Estimated Load"
]);

foreach ($equipmentLoad as $eq => $ld){
    $pdf->TableRow([
        70 => $eq,
        50 => $ld
    ]);
}

$pdf->Ln(12);

/* ============================
   4. BAR CHART (Sorted)
=============================== */

$pdf->SubTitle("Equipment Utilization - Horizontal Bar Chart");

$chartX  = 40;
$chartY  = $pdf->GetY() + 3;
$barWmax = 120;
$barH    = 7;

$maxE = max($equipmentLoad);
if ($maxE <= 0) $maxE = 1;

$i = 0;

foreach ($equipmentLoad as $eq => $ld){

    list($r,$g,$b) = pickColor($i);

    $y  = $chartY + ($i * 11);
    $bw = ($ld / $maxE) * $barWmax;

    /* Equipment name */
    $pdf->SetFont("Arial", "", 8);
    $pdf->SetXY($chartX - 35, $y);
    $pdf->Cell(34, 6, utf8_decode($eq), 0, 0, "R");

    /* Bar */
    $pdf->SetFillColor($r, $g, $b);
    $pdf->Rect($chartX, $y, $bw, $barH, "F");

    /* Value */
    $pdf->SetFont("Arial", "B", 8);
    $pdf->SetXY($chartX + $bw + 5, $y);
    $pdf->Cell(12, 6, $ld);

    $i++;
}

$pdf->Ln(($i * 12) + 10);




/* ============================================================
   SECTION 4 — Test Type Distribution
============================================================ */
$pdf->SectionTitle("4. Test Type Distribution");

$testCount = [];
foreach ($expanded as $e){
    if (!isset($testCount[$e["Test"]])) $testCount[$e["Test"]] = 0;
    $testCount[$e["Test"]]++;
}

arsort($testCount);

$pdf->TableHeader([
    50=>"Test Type",
    30=>"Count",
    100=>"Percentage"
]);

foreach ($testCount as $t=>$v){
    $pct = round(($v/$totalTests)*100,1)."%";
    $pdf->TableRow([
        50=>$t,
        30=>$v,
        100=>$pct
    ]);
}

$pdf->Ln(5);
/* ============================================================
   SECTION 5 — NCR YEARLY SUMMARY (JOIN FROM ensayos_reporte)
============================================================ */
$pdf->SectionTitle("5. NCR Summary");

$ncr = $db->query("
    SELECT r.Client, e.*
    FROM ensayos_reporte e
    LEFT JOIN lab_test_requisition_form r
      ON r.Sample_ID = e.Sample_ID
     AND r.Sample_Number = e.Sample_Number
     AND FIND_IN_SET(e.Test_Type, r.Test_Type)
    WHERE e.Report_Date BETWEEN '$start' AND '$end'
      AND (
           LOWER(Test_Condition) LIKE '%fail%' OR
           LOWER(Test_Condition) LIKE '%reject%' OR
           LOWER(Test_Condition) LIKE '%no pasa%'
      )
")->fetch_all(MYSQLI_ASSOC);

if (empty($ncr)){
    $pdf->BodyText("No NCR registered this year.");
} else {
    $pdf->TableHeader([
        35=>"Client",
        40=>"Sample",
        90=>"Description"
    ]);

    foreach ($ncr as $n){
        $pdf->TableRow([
            35=>$n["Client"],
            40=>$n["Sample_ID"]."-".$n["Sample_Number"],
            90=>utf8_decode($n["Noconformidad"])
        ]);
    }
}

$pdf->Ln(8);

/* ============================================================
   SECTION 6 — Pending Tests (JOIN with requisition)
============================================================ */
$pdf->SectionTitle("6. Pending Tests Overview");

$pending = $db->query("
    SELECT r.Client, r.Sample_ID, r.Sample_Number, r.Test_Type
    FROM lab_test_requisition_form r
    WHERE r.Registed_Date BETWEEN '$start' AND '$end'
      AND NOT EXISTS (
          SELECT 1 FROM test_preparation p
          WHERE p.Sample_ID=r.Sample_ID 
            AND p.Sample_Number=r.Sample_Number
            AND p.Test_Type=r.Test_Type
      )
      AND NOT EXISTS (
          SELECT 1 FROM test_realization z
          WHERE z.Sample_ID=r.Sample_ID 
            AND z.Sample_Number=r.Sample_Number
            AND z.Test_Type=r.Test_Type
      )
      AND NOT EXISTS (
          SELECT 1 FROM test_delivery d
          WHERE d.Sample_ID=r.Sample_ID 
            AND d.Sample_Number=r.Sample_Number
            AND d.Test_Type=r.Test_Type
      )
")->fetch_all(MYSQLI_ASSOC);

if (empty($pending)){
    $pdf->BodyText("No pending tests for this year.");
} else {
    $pdf->TableHeader([
        35=>"Client",
        40=>"Sample",
        90=>"Test Type"
    ]);

    foreach ($pending as $p){
        $pdf->TableRow([
            35=>$p["Client"],
            40=>$p["Sample_ID"]."-".$p["Sample_Number"],
            90=>$p["Test_Type"]
        ]);
    }
}

$pdf->Ln(8);

/* ============================================================
   SECTION 7 — Insights
============================================================ */
$pdf->SectionTitle("7. Insights & Recommendations");

$pdf->BodyText("
• Q1 represented ".round(($Q[1]/$totalTests)*100,1)."% of yearly activity.
• Main client: ".$clientList[0]."
• Most frequent test: ".array_key_first($testCount)."
• Total NCR: ".count($ncr)."
• Pending tests: ".count($pending)."
");

/* ============================================================
   OUTPUT
============================================================ */
ob_end_clean();
$pdf->Output("I","Annual_Report_$year.pdf");
exit;