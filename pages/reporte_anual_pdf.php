<?php

ob_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

ini_set('memory_limit', '1024M');
set_time_limit(300);

require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
error_reporting(E_ALL);
$user = current_user();
$preparedBy = $user['name'] ?? $user['username'] ?? 'N/A';
$generatedAt = date('Y-m-d H:i');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

/* ============================================================
   SMALL CACHE to avoid thousands of SQL queries
============================================================ */
$clientCache = [];

function findClientCached($id, $num){
    global $clientCache;

    $key = $id."_".$num;

    if (isset($clientCache[$key])) {
        return $clientCache[$key];
    }

    $row = find_by_sql("
        SELECT Client 
        FROM lab_test_requisition_form 
        WHERE Sample_ID='{$id}'
          AND Sample_Number='{$num}'
        LIMIT 1
    ");

    $clientCache[$key] = $row[0]['Client'] ?? "UNKNOWN";
    return $clientCache[$key];
}

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

    foreach ($cols as $w => $t){
        $text = ($t === null) ? '' : utf8_decode((string)$t);
        $this->Cell($w,7,$text,1,0,'C',true);
    }
    $this->Ln();
    $this->SetFont('Arial','',9);
}

function TableRow($cols){
    foreach ($cols as $w => $t){

        // Limpieza total del valor
        if ($t === null || $t === '') {
            $text = '';
        } elseif (is_numeric($t)) {
            $text = $t;  // números se imprimen sin utf8_decode
        } else {
            $text = utf8_decode((string)$t);
        }

        $this->Cell($w,6,$text,1,0,'L');
    }
    $this->Ln();
}

function WriteFormatted($text) {
    $parts = preg_split('/(\[\[b\]\]|\[\[\/b\]\])/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($parts as $part) {
        if ($part === '[[b]]') {
            $this->SetFont('Arial','B',8);
        } elseif ($part === '[[/b]]') {
            $this->SetFont('Arial','',8);
        } else {
            $this->Write(5, utf8_decode($part));
        }
    }

    $this->Ln(6);
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
   EQUIPMENT LOAD — required for Executive Summary
============================================================ */

$equipmentMap = [
    "Sieves"           => ["GS","AL"],
    "Hydrometer Stand" => ["HY","DHY"],
    "Proctor Mold"     => ["SP","MP"],
    "LAA Drum"         => ["LAA"],
    "Oven / Balance"   => ["MC","SG","AL","GS"],
];

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

/* fallback por si está todo vacío */
if (empty($equipmentLoad)) {
    $equipmentLoad = ["No Data" => 1];
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
   ALIAS MAP — Technician Name Resolver
============================================================ */
/* ============================================================
   TECH ALIAS MAP (ROBUST)
============================================================ */

function tech_norm_token($s){
    $s = strtolower(trim((string)$s));
    if ($s === '') return '';
    // quita puntos y caracteres extraños comunes
    $s = str_replace(['.', '·', '+', '-', "\t"], '', $s);
    // normaliza guiones raros a -
    $s = str_replace(["-","+","—","-"], '-', $s);
    // colapsa espacios
    $s = preg_replace('/\s+/', ' ', $s);
    return $s;
}

function tech_split_tokens($raw){
    $raw = tech_norm_token($raw);
    if ($raw === '') return [];
    // separa por / - espacio , \ etc.
    $parts = preg_split('/[\/\-\s\+,\\\\]+/', $raw);
    $out = [];
    foreach ((array)$parts as $p){
        $p = tech_norm_token($p);
        if ($p !== '') $out[] = $p;
    }
    return $out;
}

/* 1) LOAD ALIAS MAP */
function loadTechnicianAliasMap() {

    $rows = find_by_sql("
        SELECT name, alias
        FROM users
        WHERE TRIM(name) <> ''
    ");

    $aliasMap = [];
    $firstLetterCount = [];
    $firstLetterName  = [];

    foreach ((array)$rows as $r){

        $name = trim((string)($r['name'] ?? ''));
        if ($name === "") continue;

        // =========
        // A) Alias manuales desde users.alias
        // =========
        $aliasRaw = trim((string)($r['alias'] ?? ''));
        if ($aliasRaw !== "") {
            $tokens = tech_split_tokens($aliasRaw);
            foreach ($tokens as $t) {
                if ($t !== "") $aliasMap[$t] = $name;
            }
        }

        // =========
        // B) Alias automáticos desde el nombre
        //    - first name, last name
        //    - iniciales (LM)
        // =========
        $nameClean = tech_norm_token($name);
        $nameParts = preg_split('/\s+/', $nameClean);
        $nameParts = array_values(array_filter(array_map('trim',$nameParts)));

        if (!empty($nameParts)){
            // tokens del nombre (luis, monegro, etc.)
            foreach ($nameParts as $np){
                if (strlen($np) >= 2) $aliasMap[$np] = $name;
            }

            // iniciales (primera letra de cada palabra)
            $ini = '';
            foreach ($nameParts as $np){
                $ini .= substr($np,0,1);
            }
            $ini = tech_norm_token($ini); // ej: "lm"
            if (strlen($ini) >= 2) $aliasMap[$ini] = $name;

            // caso común: primera + apellido (l + monegro)
            if (count($nameParts) >= 2){
                $first = substr($nameParts[0],0,1);
                $last  = $nameParts[count($nameParts)-1];
                $aliasMap[$first.$last] = $name; // ej: lmonegro
            }
        }

        // =========
        // C) Conteo inicial de la persona (fallback MUY limitado)
        // =========
        $first = strtolower(substr($name,0,1));
        if (!isset($firstLetterCount[$first])) $firstLetterCount[$first] = 0;
        $firstLetterCount[$first]++;
    }

    // SI SOLO HAY UNA PERSONA POR ESA INICIAL → ASIGNAR DIRECTO
    foreach ((array)$rows as $r){
        $name = trim((string)($r['name'] ?? ''));
        if ($name === "") continue;

        $first = strtolower(substr($name,0,1));
        if (($firstLetterCount[$first] ?? 0) === 1){
            $firstLetterName[$first] = $name;
        }
    }

    return [$aliasMap, $firstLetterName];
}


/* 2) RESOLVE TECH BASED ON ALIAS + INITIALS */
function resolveTechnician($aliasMap, $firstLetterName, $rawTech){

    if ($rawTech === null) return null;

    $clean = tech_norm_token($rawTech);
    if ($clean === "") return null;

    // tokens: soporta LM, L.M., LM/RRH, LM-RRH, LM RRH, etc.
    $parts = tech_split_tokens($clean);
    if (empty($parts)) return null;

    $names = [];

    foreach ($parts as $p){

        if ($p === "") continue;

        // 1) match directo en aliasMap (siglas, alias manual, alias auto)
        if (isset($aliasMap[$p])) {
            $names[] = $aliasMap[$p];
            continue;
        }

        // 2) si viene con puntos pegados (ya los quitamos) pero por si acaso:
        $p2 = str_replace('.', '', $p);
        if ($p2 !== $p && isset($aliasMap[$p2])) {
            $names[] = $aliasMap[$p2];
            continue;
        }

        // 3) fallback por inicial SOLO si está unívoca
        $first = strtolower(substr($p,0,1));
        if (isset($firstLetterName[$first])) {
            $names[] = $firstLetterName[$first];
        }
    }

    $names = array_values(array_unique(array_filter($names)));
    if (empty($names)) return null;

    return implode(", ", $names);
}

function ensureSpace($pdf, $needed = 50){
    if ($pdf->GetY() + $needed > 250){ 
        $pdf->AddPage();
        $pdf->SetY(20);
    }
}

function safeTextUtf($v){
    if (is_array($v)) return utf8_decode(implode(", ", $v));
    if ($v === null) return "";
    return utf8_decode((string)$v);
}
function cleanVal($v){

    if ($v === null) return "";

    // Si es array, convertir a string seguro
    if (is_array($v)) {
        return implode(", ", array_map(fn($x)=> (string)$x, $v));
    }

    // Convertir todo a string antes de trim
    $v = (string)$v;

    return utf8_decode(trim($v));
}


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
$pdf->SetTextColor(60,60,60);

// Info operativa (quién y cuándo) — IZQUIERDA
$pdf->SetFont('Arial','',10);
$pdf->Cell(210,6, safeTextUtf("Prepared by: ".$preparedBy), 0, 1, 'C');
$pdf->Cell(210,6, safeTextUtf("Generated at: ".$generatedAt), 0, 1, 'C');

$pdf->Ln(4);

// Info institucional — CENTRADA
$pdf->SetFont('Arial','',11);
$pdf->Cell(210,7, safeTextUtf("Location: Pueblo Viejo - Sánchez Ramírez - Dominican Republic"), 0, 1, "C");
$pdf->Cell(210,7, safeTextUtf("Reporting Period: January 1st, $anio - December 31st, $anio"), 0, 1, "C");
$pdf->Cell(210,7, safeTextUtf("Document Type: Annual CQA Laboratory Performance Report"), 0, 1, "C");

$pdf->Ln(15);


// ---------------- LÍNEA FINAL ----------------
$pdf->SetDrawColor(120,120,120);
$pdf->Line(40, $pdf->GetY(), 170, $pdf->GetY());

/* ============================================================
   GLOBAL ANNUAL DATA PREPARATION — REQUIRED BEFORE EXEC SUMMARY
============================================================ */

// 1. OBTENER TODAS LAS MUESTRAS DEL AÑO
$rows = $db->query("
    SELECT 
        Registed_Date,
        Client,
        Material_Type,
        Sample_ID,
        Sample_Number,
        Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);

// 2. LISTA DE CLIENTES
$clientList = [];
foreach ($rows as $r){
    $cl = trim((string)$r["Client"]);
    if ($cl !== "" && !in_array($cl, $clientList)){
        $clientList[] = $cl;
    }
}

// 3. EXPANDIR TEST TYPES
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

        // Expand sample
        $expanded[] = [
            "Date"     => substr($r["Registed_Date"], 0, 10),
            "Client"   => $client,
            "Test"     => $t,
            "Material" => trim($r["Material_Type"])
        ];

        // Count by type
        if (!isset($testCountByType[$t])) $testCountByType[$t] = 0;
        $testCountByType[$t]++;

        // Count by client
        if (!isset($testCountByClient[$client])) $testCountByClient[$client] = 0;
        $testCountByClient[$client]++;

        // Count by month
        if ($month >= 1 && $month <= 12){
            $testsByMonth[$month]++;
        }
    }
}

// 4. TOTALES
$totalTests    = count($expanded);
$totalSamples  = count($rows);
$totalClients  = count($clientList);
$avgTestsPerSample = ($totalSamples > 0) ? round($totalTests / $totalSamples, 2) : 0;

// 5. QUARTER SUMMARY
$Q = [1=>0,2=>0,3=>0,4=>0];
foreach ($expanded as $e){
    $m = (int)substr($e["Date"], 5, 2);
    if ($m <= 3) $Q[1]++;
    elseif ($m <= 6) $Q[2]++;
    elseif ($m <= 9) $Q[3]++;
    else $Q[4]++;
}

// 6. MATERIAL DISTRIBUTION (si la usas)
$materialStats = [
    "Soil"=>["samples"=>0],
    "Aggregates"=>["samples"=>0],
    "Rocks"=>["samples"=>0],
    "Concrete"=>["samples"=>0]
];

foreach ($expanded as $e){
    $mat = strtolower($e["Material"]);

    if (strpos($mat,"common")!==false || strpos($mat,"lpf")!==false)
        $materialStats["Soil"]["samples"]++;

    elseif (strpos($mat,"ff")!==false || strpos($mat,"cf")!==false || strpos($mat,"utf")!==false)
        $materialStats["Aggregates"]["samples"]++;

    elseif (strpos($mat,"rock")!==false || strpos($mat,"rf")!==false || strpos($mat,"trf")!==false || strpos($mat,"irf")!==false)
        $materialStats["Rocks"]["samples"]++;

    elseif (strpos($mat,"conc")!==false)
        $materialStats["Concrete"]["samples"]++;
}

// 7. EQUIPMENT LOAD
$equipmentMap = [
    "Sieves" => ["GS","AL"],
    "Hydrometer Stand" => ["HY","DHY"],
    "Proctor Mold" => ["SP","MP"],
    "LAA Drum" => ["LAA"],
    "Oven / Balance" => ["MC","SG","AL","GS"],
];

$equipmentLoad = [];
foreach ($equipmentMap as $equip => $arr){
    $load = 0;
    foreach ($arr as $abbr){
        if (isset($testCountByType[$abbr])){
            $load += $testCountByType[$abbr];
        }
    }
    $equipmentLoad[$equip] = $load;
}
/* ============================================================
   NCR — ANNUAL NON CONFORMITIES
============================================================ */
$ncr = $db->query("
    SELECT Report_Date AS NCR_Date,
           Sample_ID,
           Sample_Number,
           Test_Type,
           Noconformidad
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);

/* fallback */
if (!is_array($ncr)) {
    $ncr = [];
}
$pdf->AddPage();

/* ======================================================
   SECTION 1 — EXECUTIVE SUMMARY (ANNUAL)
====================================================== */

$pdf->SectionTitle("1.0 Executive Summary");

/* ----------- METRICAS CLAVE PARA EL ANIO ----------- */

$totalRegistered = $totalSamples;     // muestras registradas
$totalTestsRun   = $totalTests;       // ensayos ejecutados

$totalClientsYear = $totalClients;    // clientes activos
$topClient        = array_key_first($testCountByClient); 
$topClientTests   = $testCountByClient[$topClient] ?? 0;

$topTest          = array_key_first($testCountByType);
$topTestCount     = $testCountByType[$topTest] ?? 0;

$peakQuarter = array_search(max($Q), $Q);
$minQuarter  = array_search(min($Q), $Q);

$peakMonthIndex = array_search(max($testsByMonth), $testsByMonth);
$minMonthIndex  = array_search(min($testsByMonth), $testsByMonth);

$peakMonthName = date("F", mktime(0,0,0,$peakMonthIndex,1));
$minMonthName  = date("F", mktime(0,0,0,$minMonthIndex,1));

$annualNCR = count($ncr);

/* ----------- TEXTO EJECUTIVO ----------- */

$summaryText = "
The PVDJ Laboratory maintained stable and consistent operational performance throughout the year, supported by a well-structured workflow covering sample registration, preparation, testing, delivery, and documentation.

A total of {$totalRegistered} samples were received and {$totalTestsRun} individual tests were completed. Services were provided to {$totalClientsYear} clients, with demand distributed evenly across projects, ensuring balanced resource allocation and avoiding dependency on any single client.

The most frequently executed test was '{$topTest}' with {$topTestCount} completions, while '{$topClient}' was the client with the highest testing volume ({$topClientTests} tests). Operational load peaked in Q{$peakQuarter} and reached its lowest point in Q{$minQuarter}. Monthly activity followed expected field and construction cycles, with the highest demand in {$peakMonthName} and the lowest in {$minMonthName}.

A total of {$annualNCR} non-conformities were recorded and addressed through corrective and preventive actions, maintaining alignment with internal procedures, audit requirements, and ASTM standards.

Overall, laboratory operations demonstrated strong reliability, effective coordination among technicians, supervisors, and document control, and consistent adherence to quality, traceability, and safety standards. These results confirm a solid operational year and provide a strong basis for continued performance improvement.
";
$pdf->SetFont("Arial","",8);
$pdf->WriteFormatted($summaryText);
$pdf->Ln(5);



/* ============================================================
   SECTION 2 — GLOBAL YEARLY SUMMARY (FINAL COMPLETE VERSION)
============================================================ */

$pdf->SectionTitle("2.0 Global Yearly Summary");

/* ============================================================
   1. RETRIEVE RAW DATA
============================================================ */
$rows = $db->query("
    SELECT 
        Registed_Date,
        Client,
        Sample_ID,
        Material_Type,
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
$pdf->SubTitle("2.1 Quarter Summary");

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

$pdf->Ln(5);


/* ============================================================
   4. QUARTER COMPARISON CHART (Horizontal Bars)
============================================================ */
$pdf->SubTitle(" 2.2 Quarter Workload Comparison");

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
    $posY = $chartY + ($i * 7.2);

    $pdf->SetXY($chartX - 25, $posY);
    $pdf->Cell(25, 6, "Q$q", 0, 0, "R");

    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX, $posY, $barW, $barH, "F");

    $pdf->SetXY($chartX + $barW + 3, $posY);
    $pdf->Cell(20, 6, $val, 0, 0);

    $i++;
}

$pdf->Ln(10);

$pdf->AddPage();
/* ============================================================
   5. MONTHLY TREND CHART (Line + Markers)
============================================================ */
$pdf->SubTitle(" 2.3 Monthly Workload Trend (Line Chart)");

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

$pdf->Ln(20);

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
$pdf->SubTitle("2.4 Annual Total Test per Test Type");

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
$pdf->AddPage();
/* ============================================================
   6B. HORIZONTAL BAR CHART — ONLY TOP 10 TEST TYPES
============================================================ */

$pdf->SubTitle("2.5 Top 5 Test Types -  Chart");

// Tomar solo los primeros 10
$top10 = array_slice($topTests, 0, 5, true);

$chartX = 25;
$chartY = $pdf->GetY() + 5;
$barWmax = 120;
$barH = 5;

$maxVal = max($top10);
if ($maxVal <= 0) $maxVal = 1;

$i = 0;

foreach ($top10 as $abbr => $cnt){

    $y = $chartY + ($i * 6);  // más espacio para 10 barras

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

$pdf->Ln($i * 7.2 + 2);



/* ============================================================
   7. TOP CLIENTS (Table) — With Total Row
============================================================ */
$pdf->SubTitle("2.6 Annual Total Test per Clients");

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
$pdf->AddPage();
/* ============================================================
   7B. HORIZONTAL BAR CHART — ONLY TOP 5 CLIENTS
============================================================ */
$pdf->SubTitle("2.7 Top 5 Clients -  Chart");

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
    $y = $chartY + ($i * 7.2);

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
$pdf->Ln($i * 6 + 5);


/* ============================================================
   8. INSIGHTS (Executive English Summary)
============================================================ */
$pdf->SubTitle("2.8 Insights");

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


$pdf->SectionTitle("3.0 Laboratory Operational Performance");

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
$pdf->SubTitle("3.1 Turnaround Performance (Registration - Preparation - Realization - Delivery)");

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
$pdf->SubTitle("3.2 Material Overview");

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

$pdf->Ln(10);

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

$pdf->Ln($i*6 + 15);


/* ============================================================
   3. TEST COMPLEXITY — WORKLOAD INDEX
============================================================ */
$pdf->SubTitle("3.3 Test Complexity Index (TCI) - Workload Index");

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

$pdf->Ln(8);

/* ============================================================
   Horizontal Bars — Workload (TOP 10 ONLY)
============================================================ */
ensureSpace($pdf, 40);
/* Obtener los 10 mayores Workload Index */
$top10WI = array_slice($workload, 0, 5, true);

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
$pdf->SubTitle("3.4 Equipment Utilization Index");

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

$pdf->SubTitle("3.5 Equipment Utilization - Horizontal Bar Chart");
ensureSpace($pdf, 40);
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

$pdf->Ln(($i * 7.2) + 10);




/* ============================================================
   SECTION 4 — Annual Load Distribution by Day-of-Week (Stable)
=============================================================== */

$pdf->AddPage();
$pdf->SectionTitle("4. Annual Load Distribution by Day-of-Week");

/* ============================================================
   1) SAFE LOCAL DATA SOURCES
=============================================================== */

/* Garantiza que $rows viene del dataset global */
$rows_daily = isset($rows) && is_array($rows) ? $rows : [];

/* Map de días */
$dayMap = [
    1=>"MON", 2=>"TUE", 3=>"WED", 4=>"THU", 5=>"FRI", 6=>"SAT", 0=>"SUN"
];

/* Inicialización segura */
$testsByDay = [ "MON"=>0,"TUE"=>0,"WED"=>0,"THU"=>0,"FRI"=>0,"SAT"=>0,"SUN"=>0 ];
$regByDay   = [ "MON"=>0,"TUE"=>0,"WED"=>0,"THU"=>0,"FRI"=>0,"SAT"=>0,"SUN"=>0 ];

/* Construcción de datos */
foreach ($rows_daily as $r){

    $regDate = $r["Registed_Date"] ?? null;
    if (!$regDate) continue;

    $dow = date("w", strtotime($regDate));
    $day = $dayMap[$dow] ?? "MON";

    /* COUNT REGISTRATIONS */
    $regByDay[$day]++;

    /* COUNT TEST EXECUTIONS */
    $types = explode(",", $r["Test_Type"] ?? "");
    foreach ($types as $t){
        $t = trim($t);
        if ($t !== "") $testsByDay[$day]++;
    }
}

/* Si todo está vacío, evita errores */
if (array_sum($testsByDay) == 0){
    $testsByDay["MON"] = 1;
}
if (array_sum($regByDay) == 0){
    $regByDay["MON"] = 1;
}

/* ============================================================
   2) KEY INDICATORS
=============================================================== */

$pdf->SubTitle("4.1 Key Indicators");

$totalYear = array_sum($testsByDay);
$avgDaily  = round($totalYear / 365, 1);

$maxDay = array_keys($testsByDay, max($testsByDay))[0] ?? "MON";
$minDay = array_keys($testsByDay, min($testsByDay))[0] ?? "MON";

$weekendLoad = $testsByDay["SAT"] + $testsByDay["SUN"];
$weekendPct  = $totalYear > 0 ? round(($weekendLoad / $totalYear) * 100, 1) : 0;

/* Variabilidad */
$mean = $totalYear > 0 ? ($totalYear / 7) : 1;
$sd = 0;
foreach ($testsByDay as $v){
    $sd += pow($v - $mean, 2);
}
$sd = sqrt($sd / 7);
$cv = round(($sd / $mean) * 100, 1);

/* KPI CARDS */
$pdf->SetFillColor(240,240,240);
$pdf->SetFont("Arial","B",10);

$kpiW = 45; $kpiH = 10;

$pdf->Cell($kpiW,$kpiH,"Peak Day: $maxDay",1,0,'C',true);
$pdf->Cell($kpiW,$kpiH,"Lowest Day: $minDay",1,0,'C',true);
$pdf->Cell($kpiW,$kpiH,"Avg/Day: $avgDaily",1,0,'C',true);
$pdf->Cell($kpiW,$kpiH,"Weekend: $weekendPct%",1,1,'C',true);

$pdf->Cell($kpiW,$kpiH,"CV: $cv%",1,0,'C',true);
$pdf->Cell($kpiW,$kpiH,"Total: $totalYear",1,0,'C',true);
$pdf->Cell($kpiW,$kpiH,"SAT+SUN: $weekendLoad",1,0,'C',true);
$pdf->Cell($kpiW,$kpiH,"Days Counted: 365",1,1,'C',true);

$pdf->Ln(8);

/* ============================================================
   3) BAR CHART – TEST EXECUTION
=============================================================== */

$pdf->SubTitle("4.2 Weekly Test Load (Descending)");
ensureSpace($pdf, 40);
$sortedTests = $testsByDay;
arsort($sortedTests);

$chartX = 40;
$chartY = $pdf->GetY() + 3;
$barW   = 120;
$barH   = 7;

$maxVal = max($sortedTests);
if ($maxVal <= 0) $maxVal = 1;

$i = 0;
foreach ($sortedTests as $day => $cnt){

    list($r,$g,$b) = pickColor($i);
    $y = $chartY + ($i * 7.2);

    $bw = ($cnt / $maxVal) * $barW;

    $pdf->SetFont("Arial","",8);
    $pdf->SetXY($chartX - 35, $y);
    $pdf->Cell(30, 6, $day, 0, 0, "R");

    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX, $y, $bw, $barH, "F");

    $pdf->SetXY($chartX + $bw + 5, $y);
    $pdf->Cell(20, 6, $cnt, 0, 0);

    $i++;
}

$pdf->Ln(($i * 7.2) + 5);

/* ============================================================
   4) BAR CHART – REGISTRATION LOAD
=============================================================== */

$pdf->SubTitle("4.3 Sample Registration by Day");
ensureSpace($pdf, 40);
$sortedReg = $regByDay;
arsort($sortedReg);

$chartX = 40;
$chartY = $pdf->GetY() + 3;

$maxVal = max($sortedReg);
if ($maxVal <= 0) $maxVal = 1;

$i = 0;
foreach ($sortedReg as $day=>$cnt){

    list($r,$g,$b) = pickColor($i+3);
    $y = $chartY + ($i * 7.2);

    $bw = ($cnt / $maxVal) * $barW;

    $pdf->SetFont("Arial","",8);
    $pdf->SetXY($chartX - 35, $y);
    $pdf->Cell(30,6,$day,0,0,"R");

    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX,$y,$bw,$barH,"F");

    $pdf->SetXY($chartX+$bw+5,$y);
    $pdf->Cell(20,6,$cnt,0,0);

    $i++;
}

$pdf->Ln(($i * 10) + 5);

/* ============================================================
   5) BACKLOG ANALYSIS
=============================================================== */

$pdf->SubTitle("4.4 Backlog Accumulation");

/* BACKLOG CALCULATION */
$backlog = [];
foreach ($testsByDay as $d => $v) {
    $backlog[$d] = $regByDay[$d] - $testsByDay[$d];
}

/* TABLE WITH 5 COLUMNS (FULL WIDTH 190 mm) */
$pdf->TableHeader([
    15 => "Day",
    35 => "Registered",
    30 => "Executed",
    25 => "Backlog",
    40 => "Interpretation"
]);

foreach ($backlog as $day => $value) {

    if ($value >= 100)      $txt = "High";
    elseif ($value >= 30)   $txt = "Moderate";
    elseif ($value >= 0)    $txt = "Low";
    else                    $txt = "Clearing";

    $pdf->TableRow([
        15 => $day,
       35 => $regByDay[$day],
        30 => $testsByDay[$day],
        25 => $value,
        40 => $txt
    ]);
}
/* ============================================================
   BACKLOG INTERPRETATION NOTE
============================================================ */

$pdf->Ln(2);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(0,6,"Interpretation Guide",0,1);
$pdf->SetFont('Arial','',8);

$textoExplicacion = "
High        : Significant backlog accumulation requiring urgent corrective action.
Moderate    : Manageable backlog - monitor to prevent potential delays.
Low         : Stable workload- no meaningful backlog detected.
Clearing    : Executed more tests than registered - pending load is being reduced.
";

$pdf->MultiCell(0,5, utf8_decode($textoExplicacion));
$pdf->Ln(5);



/* ============================================================
   6) EXECUTIVE INTERPRETATION
=============================================================== */

$pdf->SubTitle("4.5 Interpretation");

$peakDay = $maxDay;
$lowDay  = $minDay;

$highBack = max($backlog);
$lowBack  = min($backlog);

$backHighDay = array_keys($backlog, $highBack)[0] ?? "MON";
$backLowDay  = array_keys($backlog, $lowBack)[0] ?? "MON";

$summary  = "The weekly test distribution remains consistent throughout the year. ";
$summary .= "The peak operational day was {$peakDay}, while {$lowDay} registered the lowest activity.\n\n";

$summary .= "Backlog accumulation was highest on {$backHighDay} and lowest on {$backLowDay}, ";
$summary .= "indicating predictable operational patterns across the week.\n\n";

$summary .= "Weekend activity represented {$weekendPct}% of annual workload, reaffirming ";
$summary .= "that the laboratory operates predominantly during weekdays.\n\n";

$summary .= "Overall variability (CV = {$cv}%) shows the laboratory maintained ";
$summary .= ($cv < 20 ? "a highly stable demand." : ($cv < 40 ? "a moderately consistent workload." : "strong fluctuations in demand."));

$pdf->BodyText($summary);
$pdf->Ln(3);


/* ============================================================
   SECTION 5 — Technician Performance Overview
============================================================ */

$pdf->AddPage();
$pdf->SectionTitle("5. Technician Performance Overview");

/* ============================================================
   5.0 LOAD TECHNICIAN ALIAS MAP
============================================================ */
list($aliasMap, $firstLetterName) = loadTechnicianAliasMap();

/* ============================================================
   5.0A — LOAD LIST OF REAL TECHNICIANS FROM users TABLE
============================================================ */
$realTechRows = find_by_sql("
    SELECT name
    FROM users
    WHERE job = 'Technical'
       OR job LIKE '%Technician%'
       OR job LIKE '%Lab Tech%'
");

$realTechnicians = [];
$realTechSet = []; // para validar rápido

foreach ((array)$realTechRows as $r){
    $nm = trim((string)($r['name'] ?? ''));
    if ($nm === '') continue;
    $realTechnicians[] = $nm;
    $realTechSet[strtoupper($nm)] = $nm; // key normalizada -> nombre original
}

/* ============================================================
   5.1 RAW TECHNICIAN DATA (FAST DATE RANGE)
============================================================ */
$y0 = $db->escape($year.'-01-01');
$y1 = $db->escape(($year+1).'-01-01');

$tecRaw = find_by_sql("
    SELECT Technician, COUNT(*) total, 'Preparation' etapa
    FROM test_preparation
    WHERE Register_Date >= '{$y0}' AND Register_Date < '{$y1}'
      AND TRIM(IFNULL(Technician,'')) <> ''
    GROUP BY Technician

    UNION ALL

    SELECT Technician, COUNT(*) total, 'Realization' etapa
    FROM test_realization
    WHERE Register_Date >= '{$y0}' AND Register_Date < '{$y1}'
      AND TRIM(IFNULL(Technician,'')) <> ''
    GROUP BY Technician

    UNION ALL

    SELECT Technician, COUNT(*) total, 'Delivery' etapa
    FROM test_delivery
    WHERE Register_Date >= '{$y0}' AND Register_Date < '{$y1}'
      AND TRIM(IFNULL(Technician,'')) <> ''
    GROUP BY Technician
");

if (!is_array($tecRaw)) $tecRaw = [];

/* ============================================================
   5.2 NORMALIZATION → techSummary[name][stage]
   RULE:
   - Si resuelve alias → usa nombre completo
   - Si no está en users pero sí en alias → se queda (NO se pierde)
   - Si no se puede resolver → UNMAPPED (raw)
   - NO distribuimos counts inválidos (eso daña los números)
============================================================ */
$techSummary = [];

function initTech(&$techSummary, $name){
    if (!isset($techSummary[$name])) {
        $techSummary[$name] = [
            "Preparation" => 0,
            "Realization" => 0,
            "Delivery"    => 0
        ];
    }
}

function normKey($s){
    return strtoupper(trim((string)$s));
}

function isKnownRealTech($realTechSet, $name){
    $k = normKey($name);
    return isset($realTechSet[$k]);
}

foreach ($tecRaw as $row){

    $rawTech = trim((string)($row["Technician"] ?? ''));
    $stage   = (string)($row["etapa"] ?? '');
    $count   = (float)($row["total"] ?? 0);

    if ($rawTech === '' || $stage === '' || $count <= 0) continue;

    // 1) Resolver alias/iniciales
    $resolved = resolveTechnician($aliasMap, $firstLetterName, $rawTech);

    // Si NO resolvió, lo dejamos como UNMAPPED(raw) (no se pierde)
    if ($resolved === null) {
        $label = "UNMAPPED (" . $rawTech . ")";
        initTech($techSummary, $label);
        $techSummary[$label][$stage] += $count;
        continue;
    }

    // puede venir "Nombre1, Nombre2"
    $names = array_values(array_filter(array_map('trim', explode(',', $resolved))));

    if (empty($names)) {
        $label = "UNMAPPED (" . $rawTech . ")";
        initTech($techSummary, $label);
        $techSummary[$label][$stage] += $count;
        continue;
    }

    // 2) Para cada nombre: si está en users usamos el "oficial"; si no, se queda igual (no se pierde)
    $finalNames = [];
    foreach ($names as $nm){
        $k = normKey($nm);
        if (isset($realTechSet[$k])) {
            $finalNames[] = $realTechSet[$k];
        } else {
            // técnico fuera de users, pero resuelto por alias → lo mantenemos
            $finalNames[] = $nm;
        }
    }

    $finalNames = array_values(array_unique(array_filter($finalNames)));

    if (empty($finalNames)) {
        $label = "UNMAPPED (" . $rawTech . ")";
        initTech($techSummary, $label);
        $techSummary[$label][$stage] += $count;
        continue;
    }

    // 3) Si hay múltiples técnicos, dividir el count entre ellos
    $split = $count / count($finalNames);

    foreach ($finalNames as $nm){
        initTech($techSummary, $nm);
        $techSummary[$nm][$stage] += $split;
    }
}

/* ============================================================
   5.4 SORT TABLE — DESCENDING BY TOTAL
============================================================ */
$totalsForSort = [];

foreach ($techSummary as $name => $stages){
    $totalsForSort[$name] = (float)$stages["Preparation"] + (float)$stages["Realization"] + (float)$stages["Delivery"];
}
arsort($totalsForSort);

$techSummaryOrdered = [];
foreach ($totalsForSort as $name => $v){
    $techSummaryOrdered[$name] = $techSummary[$name];
}

/* ============================================================
   5.5 TABLE — Technician Summary
============================================================ */
$pdf->SubTitle("5.1 Technician Activity Summary");

if (empty($techSummaryOrdered)) {

    $pdf->BodyText("No technician activity recorded for this year.");
    $pdf->Ln(5);

} else {

    $wTech  = 60;
    $wPrep  = 20;
    $wReal  = 30;
    $wDel   = 25;
    $wTotal = 15;

    $pdf->TableHeader([
        $wTech  => "Technician",
        $wPrep  => "Prep",
        $wReal  => "Real",
        $wDel   => "Del",
        $wTotal => "Total"
    ]);

    foreach ($techSummaryOrdered as $name => $stages){

        $prep = round((float)$stages["Preparation"],1);
        $real = round((float)$stages["Realization"],1);
        $del  = round((float)$stages["Delivery"],1);
        $tot  = round($prep + $real + $del,1);

        $pdf->TableRow([
            $wTech  => utf8_decode($name),
            $wPrep  => $prep,
            $wReal  => $real,
            $wDel   => $del,
            $wTotal => $tot
        ]);
    }

    $pdf->Ln(8);
}

/* ============================================================
   5.2 PROCESS-SPECIFIC CHARTS
============================================================ */
$pdf->SubTitle("5.2 Technician Contribution per Process");

/* --- PREP --- */
$pdf->SetFont("Arial","B",10);
$pdf->Cell(0,6,"Preparation Workload",0,1);
ensureSpace($pdf, 40);

$prepData = [];
foreach ($techSummaryOrdered as $name=>$stages){
    $prepData[$name] = (float)$stages["Preparation"];
}
arsort($prepData);

if (array_sum($prepData) == 0){
    $pdf->BodyText("No preparation data recorded.");
    $pdf->Ln(5);
} else {
    $chartX = 45;
    $chartY = $pdf->GetY() + 4;
    $barW   = 110;
    $barH   = 6;

    $maxVal = max($prepData); if ($maxVal <= 0) $maxVal = 1;
    $i = 0;

    foreach ($prepData as $name => $val){
        list($rC,$gC,$bC) = pickColor($i);
        $y  = $chartY + ($i * 8.5);
        $bw = ($val / $maxVal) * $barW;

        $pdf->SetXY($chartX - 40, $y);
        $pdf->SetFont("Arial","",8);
        $pdf->Cell(38, 5, utf8_decode($name), 0, 0, "R");

        $pdf->SetFillColor($rC,$gC,$bC);
        $pdf->Rect($chartX, $y, $bw, $barH, "F");

        $pdf->SetXY($chartX + $barW + 4, $y);
        $pdf->Cell(10, 5, round($val,1));

        $i++;
    }

    $pdf->SetY($chartY + ($i * 8.5) + 6);
}

/* --- REAL --- */
$pdf->SetFont("Arial","B",10);
$pdf->Cell(0,6,"Realization Workload",0,1);
ensureSpace($pdf, 40);

$realData = [];
foreach ($techSummaryOrdered as $name=>$stages){
    $realData[$name] = (float)$stages["Realization"];
}
arsort($realData);

if (array_sum($realData) == 0){
    $pdf->BodyText("No realization data recorded.");
    $pdf->Ln(5);
} else {
    $chartX = 45;
    $chartY = $pdf->GetY() + 4;
    $barW   = 110;
    $barH   = 4;

    $maxVal = max($realData); if ($maxVal <= 0) $maxVal = 1;
    $i = 0;

    foreach ($realData as $name => $val){
        list($rC,$gC,$bC) = pickColor($i+3);
        $y  = $chartY + ($i * 6);
        $bw = ($val / $maxVal) * $barW;

        $pdf->SetXY($chartX - 40, $y);
        $pdf->SetFont("Arial","",8);
        $pdf->Cell(38, 5, utf8_decode($name), 0, 0, "R");

        $pdf->SetFillColor($rC,$gC,$bC);
        $pdf->Rect($chartX, $y, $bw, $barH, "F");

        $pdf->SetXY($chartX + $barW + 4, $y);
        $pdf->Cell(10, 5, round($val,1));

        $i++;
    }

    $pdf->SetY($chartY + ($i * 6) + 6);
}

/* --- DEL --- */
$pdf->SetFont("Arial","B",10);
$pdf->Cell(0,6,"Delivery Workload",0,1);
ensureSpace($pdf, 40);

$delData = [];
foreach ($techSummaryOrdered as $name=>$stages){
    $delData[$name] = (float)$stages["Delivery"];
}
arsort($delData);

if (array_sum($delData) == 0){
    $pdf->BodyText("No delivery data recorded.");
    $pdf->Ln(5);
} else {
    $chartX = 45;
    $chartY = $pdf->GetY() + 4;
    $barW   = 110;
    $barH   = 4;

    $maxVal = max($delData); if ($maxVal <= 0) $maxVal = 1;
    $i = 0;

    foreach ($delData as $name => $val){
        list($rC,$gC,$bC) = pickColor($i+5);
        $y  = $chartY + ($i * 6);
        $bw = ($val / $maxVal) * $barW;

        $pdf->SetXY($chartX - 40, $y);
        $pdf->SetFont("Arial","",8);
        $pdf->Cell(38, 5, utf8_decode($name), 0, 0, "R");

        $pdf->SetFillColor($rC,$gC,$bC);
        $pdf->Rect($chartX, $y, $bw, $barH, "F");

        $pdf->SetXY($chartX + $barW + 4, $y);
        $pdf->Cell(10, 5, round($val,1));

        $i++;
    }

    $pdf->SetY($chartY + ($i * 6) + 6);
}

/* ============================================================
   5.4 Insights (SAFE)
============================================================ */
$pdf->SubTitle("5.4 Insights");

$topPrep = (array_sum($prepData) > 0) ? array_key_first($prepData) : null;
$topReal = (array_sum($realData) > 0) ? array_key_first($realData) : null;
$topDel  = (array_sum($delData)  > 0) ? array_key_first($delData)  : null;

$lines = [];

$lines[] = $topPrep
  ? "- In Preparation, the leading contributor was {$topPrep}."
  : "- In Preparation, no workload was recorded for the selected period.";

$lines[] = $topReal
  ? "- In Realization, the highest execution workload was handled by {$topReal}."
  : "- In Realization, no workload was recorded for the selected period.";

$lines[] = $topDel
  ? "- Delivery activities were dominated by {$topDel}."
  : "- In Delivery, no workload was recorded for the selected period.";

$lines[] = "- Any item shown as UNMAPPED means the raw technician value exists in DB but is not in your alias dictionary.";

$pdf->BodyText(implode("\n\n", $lines));




/* ============================================================
   SECTION 6 — Document Control & Traceability
============================================================ */
/* ============================================================
   6. LOAD DATASETS FOR DOCUMENT CONTROL SECTION
============================================================ */

/* Delivered */
$delivered = $db->query("
    SELECT Sample_ID, Sample_Number, Test_Type, Register_Date
    FROM test_delivery
    WHERE YEAR(Register_Date) = '$year'
")->fetch_all(MYSQLI_ASSOC);

/* Reviewed */
$reviewed = $db->query("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
    FROM test_review
    WHERE YEAR(Start_Date) = '$year'
")->fetch_all(MYSQLI_ASSOC);

/* Approved (Reviewed + Closed) */
$approved = $db->query("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
    FROM test_reviewed
    WHERE YEAR(Start_Date) = '$year'
")->fetch_all(MYSQLI_ASSOC);

/* Uploaded documents (doc_files) */
$fileRows = $db->query("
    SELECT id, Sample_ID, Sample_Number, File_Name, created_at
    FROM doc_files
    WHERE YEAR(created_at) = '$year'
")->fetch_all(MYSQLI_ASSOC);

/* ============================================================
   SAFE COUNTERS (NO 'total' / NO 'qty')
============================================================ */

$totDelivered = is_array($delivered) ? count($delivered) : 0;
$totReviewed  = is_array($reviewed)  ? count($reviewed)  : 0;
$totApproved  = is_array($approved)  ? count($approved)  : 0;
$totFiles     = is_array($fileRows)  ? count($fileRows)  : 0;



/* ============================================================
   SECTION 6 — Document Control Performance
   (Delivery, Review, Approval, Uploaded Docs)
============================================================ */

$pdf->AddPage();
$pdf->SectionTitle("6. Document Control Performance Overview");

/* ============================================================
   1. RAW MONTHLY DATA FROM DATABASE
============================================================ */

$delivered = find_by_sql("
    SELECT MONTH(Register_Date) AS m, COUNT(*) AS total
    FROM test_delivery
    WHERE YEAR(Register_Date) = '{$year}'
    GROUP BY MONTH(Register_Date)
");

$reviewed = find_by_sql("
    SELECT MONTH(Start_Date) AS m, COUNT(*) AS total
    FROM test_review
    WHERE YEAR(Start_Date) = '{$year}'
    GROUP BY MONTH(Start_Date)
");

$approved = find_by_sql("
    SELECT MONTH(Start_Date) AS m, COUNT(*) AS total
    FROM test_reviewed
    WHERE YEAR(Start_Date) = '{$year}'
    GROUP BY MONTH(Start_Date)
");

/* DOCUMENT FILES */
$fileRows = find_by_sql("
    SELECT MONTH(created_at) AS m, COUNT(*) AS qty
    FROM doc_files
    WHERE YEAR(created_at) = '{$year}'
    GROUP BY MONTH(created_at)
");

/* ============================================================
   2. SAFELY BUILD ARRAYS FOR CHARTS
============================================================ */

$months = [1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",
           7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec"];

$delivM = $revM = $apprM = $filesM = array_fill(1,12,0);

foreach ($delivered as $r) $delivM[(int)$r["m"]] = (int)$r["total"];
foreach ($reviewed as $r)  $revM[(int)$r["m"]]   = (int)$r["total"];
foreach ($approved as $r)  $apprM[(int)$r["m"]]  = (int)$r["total"];
foreach ($fileRows as $r)  $filesM[(int)$r["m"]] = (int)$r["qty"];

/* ============================================================
   3. KPIs
============================================================ */

$totalDelivered = array_sum($delivM);
$totalReviewed  = array_sum($revM);
$totalApproved  = array_sum($apprM);
$totalFiles     = array_sum($filesM);

$pdf->SubTitle("6.1 Key Indicators");

$pdf->SetFillColor(240,240,240);
$pdf->SetFont("Arial","B",10);

$kW = 55; $kH = 10;

$pdf->Cell($kW,$kH,"Delivered: $totalDelivered",1,0,'C',true);
$pdf->Cell($kW,$kH,"Reviewed: $totalReviewed",1,0,'C',true);
$pdf->Cell($kW,$kH,"Approved: $totalApproved",1,1,'C',true);

$pdf->Cell($kW,$kH,"Files Uploaded: $totalFiles",1,0,'C',true);
$pdf->Cell($kW,$kH,"Approval Rate: ". ($totalReviewed>0? round(($totalApproved/$totalReviewed)*100,1) : 0) ."%",1,0,'C',true);
$pdf->Cell($kW,$kH,"Delivery/Review Ratio: ". ($totalDelivered>0? round(($totalReviewed/$totalDelivered)*100,1) : 0) ."%",1,1,'C',true);

$pdf->Ln(8);

/* ============================================================
   4. TABLE SUMMARY
============================================================ */

$pdf->SubTitle("6.2 Monthly Document Control Summary");

$pdf->TableHeader([
    35=>"Month",
    15=>"Del",
    25=>"Rev",
    40=>"Appro",
    20=>"Files"
]);

foreach ($months as $num=>$label){
    $pdf->TableRow([
        35=>$label,
        15=>$delivM[$num],
        25=>$revM[$num],
       40=>$apprM[$num],
        20=>$filesM[$num],
    ]);
}

$pdf->Ln(8);

/* ============================================================
   5. CHART 1 — Delivery by Month
============================================================ */

$pdf->SubTitle("6.3 Delivered Tests per Month");
ensureSpace($pdf, 40);
$chartX = 35; 
$chartY = $pdf->GetY()+3;
$barW   = 6;
$barMax = 15;

$maxVal = max($delivM); 
if ($maxVal <= 0) $maxVal = 1;

$i = 0;
foreach ($delivM as $m=>$v){
    list($r,$g,$b)=pickColor($i);
    $pdf->SetFillColor($r,$g,$b);

    $h = ($v/$maxVal)*$barMax;
    $pdf->Rect($chartX + ($i*10), $chartY + ($barMax-$h), $barW, $h, "F");

    $pdf->SetXY($chartX + ($i*10), $chartY + $barMax + 1);
    $pdf->SetFont("Arial","",7);
    $pdf->Cell($barW+2,4,substr($months[$m],0,3),0,0,"C");

    $i++;
}

$pdf->Ln($barMax-2);

/* ============================================================
   6. CHART 2 — Reviews per Month
============================================================ */

$pdf->SubTitle("6.4 Reviewed Tests per Month");
ensureSpace($pdf, 40);
$chartY = $pdf->GetY()+3;
$maxVal = max($revM); if ($maxVal <= 0) $maxVal = 1;

$i = 0;
foreach ($revM as $m=>$v){
    list($r,$g,$b)=pickColor($i+2);
    $pdf->SetFillColor($r,$g,$b);

    $h = ($v/$maxVal)*$barMax;
    $pdf->Rect($chartX + ($i*10), $chartY + ($barMax-$h), $barW, $h, "F");

    $pdf->SetXY($chartX + ($i*10), $chartY + $barMax + 1);
    $pdf->SetFont("Arial","",7);
    $pdf->Cell($barW+2,4,substr($months[$m],0,3),0,0,"C");

    $i++;
}

$pdf->Ln($barMax+15);

/* ============================================================
   7. CHART 3 — Approved per Month
============================================================ */

$pdf->SubTitle("6.5 Approved Tests per Month");
ensureSpace($pdf, 60);
$chartY = $pdf->GetY()+3;
$maxVal = max($apprM); if ($maxVal <= 0) $maxVal = 1;

$i = 0;
foreach ($apprM as $m=>$v){
    list($r,$g,$b)=pickColor($i+4);
    $pdf->SetFillColor($r,$g,$b);

    $h = ($v/$maxVal)*$barMax;
    $pdf->Rect($chartX + ($i*10), $chartY + ($barMax-$h), $barW, $h, "F");

    $pdf->SetXY($chartX + ($i*10), $chartY + $barMax + 1);
    $pdf->SetFont("Arial","",7);
    $pdf->Cell($barW+2,4,substr($months[$m],0,3),0,0,"C");

    $i++;
}

$pdf->Ln($barMax+10);

/* ============================================================
   8. CHART 4 — Uploaded Docs per Month
============================================================ */

$pdf->SubTitle("6.6 Uploaded Files per Month");
ensureSpace($pdf, 60);
$chartY = $pdf->GetY()+3;
$maxVal = max($filesM); if ($maxVal <= 0) $maxVal = 1;

$i = 0;
foreach ($filesM as $m=>$v){
    list($r,$g,$b)=pickColor($i+6);
    $pdf->SetFillColor($r,$g,$b);

    $h = ($v/$maxVal)*$barMax;
    $pdf->Rect($chartX + ($i*7.2), $chartY + ($barMax-$h), $barW, $h, "F");

    $pdf->SetXY($chartX + ($i*7.2), $chartY + $barMax + 1);
    $pdf->SetFont("Arial","",7);
    $pdf->Cell($barW+2,4,substr($months[$m],0,3),0,0,"C");

    $i++;
}

$pdf->Ln($barMax+10);

/* ============================================================
   9. INSIGHTS
============================================================ */

$pdf->SubTitle("6.7 Executive Insights");

$approvalRate = $totalReviewed > 0 ? round(($totalApproved/$totalReviewed)*100,1) : 0;

$peak_month_del  = array_keys($delivM, max($delivM))[0];
$peak_month_rev  = array_keys($revM, max($revM))[0];
$peak_month_file = array_keys($filesM, max($filesM))[0];

$ins = "
- Peak delivery month: {$months[$peak_month_del]}
- Peak review month: {$months[$peak_month_rev]}
- Peak document upload month: {$months[$peak_month_file]}
- Approval Rate: {$approvalRate}% 
- Delivery-to-Review ratio indicates ".($approvalRate>=90?"strong":"moderate")." document quality.
- Monthly behavior shows ".(max($filesM)>0?"active":"minimal")." document management.
";

$pdf->BodyText($ins);
$pdf->Ln(8);

// Polyfill para servidores PHP 7
if (!function_exists("str_contains")) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}


/* ============================================================
   SECTION 7 — REPEAT TEST ANALYSIS (ONE PAGE EXEC SUMMARY)
============================================================ */

$pdf->AddPage();
$pdf->SectionTitle("7. Repeat Test Analysis");

/* ============================================================
   7.1 LOAD RAW REPEAT DATA
============================================================ */

$repeatRaw = $db->query("
    SELECT 
        r.Sample_ID,
        r.Sample_Number,
        r.Test_Type,
        r.Comment,
        rr.Client,
        rr.Registed_Date
    FROM test_repeat r
    LEFT JOIN lab_test_requisition_form rr
      ON rr.Sample_ID = r.Sample_ID
     AND rr.Sample_Number = r.Sample_Number
    WHERE rr.Registed_Date BETWEEN '$start' AND '$end'
")->fetch_all(MYSQLI_ASSOC);

$totalRepeat = count($repeatRaw);

if ($totalRepeat == 0) {
    $pdf->BodyText("No repeat tests were registered during the year.");
    $pdf->Ln(8);
    goto SkipRepeatSection;
}

/* ============================================================
   7.2 BUILD COUNTERS
============================================================ */

$repeatByType   = [];
$repeatByClient = [];
$repeatByMonth  = array_fill(1,12,0);
$repeatReasons  = [];

foreach ($repeatRaw as $r){

    $test   = trim($r["Test_Type"] ?? "");
    $client = trim($r["Client"] ?? "");
    $date   = $r["Registed_Date"] ?? "";

    if ($test !== "") {
        if (!isset($repeatByType[$test])) $repeatByType[$test] = 0;
        $repeatByType[$test]++;
    }

    if ($client === "") $client = "UNKNOWN";
    if (!isset($repeatByClient[$client])) $repeatByClient[$client] = 0;
    $repeatByClient[$client]++;

    if ($date) {
        $m = (int)substr($date,5,2);
        if ($m>=1 && $m<=12) $repeatByMonth[$m]++;
    }

    $cause = strtolower(trim((string)(($r["Comment"] ?? "")." ".($r["Comments"] ?? ""))));

    if ($cause !== ""){

        if (str_contains($cause,"sample") || str_contains($cause,"insufficient"))
            $root = "Sample";

        elseif (str_contains($cause,"tech") || str_contains($cause,"human"))
            $root = "Tech";

        elseif (str_contains($cause,"equip") || str_contains($cause,"machine"))
            $root = "Equipment";

        elseif (str_contains($cause,"fail") || str_contains($cause,"outlier"))
            $root = "Fail/Outlier";

        else
            $root = "Other";

        if (!isset($repeatReasons[$root])) $repeatReasons[$root] = 0;
        $repeatReasons[$root]++;
    }
}

arsort($repeatByType);
arsort($repeatByClient);
arsort($repeatReasons);

$repeatPct = ($totalTests > 0) ? round(($totalRepeat / $totalTests) * 100, 2) : 0;

/* ============================================================
   7.3 KPI (ONE LINE)
============================================================ */

$pdf->SetFillColor(240,240,240);
$pdf->SetFont("Arial","B",10);

$pdf->Cell(95,8,"Total Repeats: $totalRepeat",1,0,'C',true);
$pdf->Cell(95,8,"Repeat Rate: $repeatPct%",1,1,'C',true);
$pdf->Ln(2);

/* ============================================================
   HELPERS: TOP N + OTHER
============================================================ */
function topNPlusOther($arr, $n=8){
    arsort($arr);
    $top = array_slice($arr, 0, $n, true);
    $other = array_sum($arr) - array_sum($top);
    if ($other > 0) $top["OTHER"] = $other;
    return $top;
}

/* ============================================================
   7.4 TABLES (COMPACT, TWO COLUMNS)
   LEFT: Repeat by Type (TOP 8)
   RIGHT: Repeat by Client (TOP 8)
============================================================ */

$topTypes   = topNPlusOther($repeatByType, 8);
$topClients = topNPlusOther($repeatByClient, 8);

$startY = $pdf->GetY();
$leftX  = 12;
$rightX = 110;

/* --- LEFT TABLE: TYPES --- */
$pdf->SetXY($leftX, $startY);
$pdf->SetFont("Arial","B",9);
$pdf->Cell(90,5,"Repeat by Test Type (Top 8)",0,1);

$pdf->SetX($leftX);
$pdf->TableHeader([
    65=>"Type",
    10=>"N",
    15=>"%"
]);

foreach ($topTypes as $t=>$cnt){
    $typeTotal = $testCountByType[$t] ?? 1;
    $pctType   = round(($cnt/$typeTotal)*100,1);

    $name = ($t==="OTHER") ? "OTHER" : ($testNames[$t] ?? $t);

    $pdf->SetX($leftX);
    $pdf->TableRow([
        65=>safeTextUtf($name),
        10=>$cnt,
        15=>$pctType."%"
    ]);
}

/* --- RIGHT TABLE: CLIENTS --- */
$pdf->SetXY($rightX, $startY);
$pdf->SetFont("Arial","B",9);
$pdf->Cell(90,5,"Repeat by Client (Top 8)",0,1);

$pdf->SetX($rightX);
$pdf->TableHeader([
    65=>"Client",
    10=>"N",
    15=>"%"
]);

foreach ($topClients as $cl=>$cnt){
    $pct = round(($cnt / $totalRepeat)*100,1)."%" ;
    $label = ($cl==="") ? "UNKNOWN" : $cl;

    $pdf->SetX($rightX);
    $pdf->TableRow([
        65=>safeTextUtf($label),
        10=>$cnt,
        15=>$pct
    ]);
}

/* move Y below the lower table */
$afterTablesY = max($pdf->GetY(), $startY + 55);
$pdf->SetY($afterTablesY + 2);

/* ============================================================
   7.5 CHARTS (COMPACT)
   A) Horizontal Bars: Type Top 8 + Other
   B) Mini Line: Monthly Trend
============================================================ */

$pdf->SetFont("Arial","B",9);
$pdf->Cell(0,5,"7.2 Repeat by Test Type (Chart - Top 8)",0,1);

ensureSpace($pdf, 40);

$chartX = 60;
$chartY = $pdf->GetY() + 1;
$labelW = 45;
$barW   = 85;
$barH   = 3.5;
$rowGap = 5;

$maxR = max($topTypes);
if ($maxR<=0) $maxR = 1;

$i = 0;
$pdf->SetFont("Arial","",7);

foreach ($topTypes as $t=>$cnt){

    $y = $chartY + ($i * $rowGap);
    list($r,$g,$b) = pickColor($i);

    $bw = ($cnt / $maxR) * $barW;
    $label = ($t==="OTHER") ? "OTHER" : ($testNames[$t] ?? $t);

    $pdf->SetXY($chartX - $labelW - 2, $y);
    $pdf->Cell($labelW, $barH, safeTextUtf($label), 0, 0, "R");

    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect($chartX, $y, $bw, $barH, "F");

    $pdf->SetXY($chartX + $barW + 3, $y);
    $pdf->Cell(10, $barH, (string)$cnt, 0, 0);

    $i++;
}

$pdf->SetY($chartY + ($i*$rowGap) + 2);

/* --- MONTHLY MINI LINE --- */
$pdf->SetFont("Arial","B",9);
$pdf->Cell(0,5,"7.4 Monthly Repeat Trend",0,1);

ensureSpace($pdf, 38);

$cx = 25;
$cy = $pdf->GetY() + 10;
$w  = 160;
$h  = 22;

drawAxis($pdf, $cx, $cy, $w, $h);

$maxM = max($repeatByMonth);
if ($maxM <= 0) $maxM = 1;

$months = ["J","F","M","A","M","J","J","A","S","O","N","D"];
$pdf->SetFont("Arial","",6);
$pdf->SetTextColor(80,80,80);

for ($m=1; $m<=12; $m++){
    $x = $cx + ($w/12)*($m-0.5);
    $pdf->SetXY($x - 2, $cy + $h + 1);
    $pdf->Cell(4, 3, $months[$m-1], 0, 0, "C");
}

$prevX = null; $prevY = null;
foreach ($repeatByMonth as $m=>$v){

    $x = $cx + ($w/12)*($m-0.5);
    $y = $cy + $h - ($v/$maxM)*($h-3);

    if ($prevX !== null) $pdf->Line($prevX, $prevY, $x, $y);

    $pdf->SetFillColor(0,0,0);
    $pdf->Rect($x-0.9, $y-0.9, 1.8, 1.8, "F");

    $prevX=$x; $prevY=$y;
}

$pdf->SetY($cy + $h + 6);

/* ============================================================
   7.6 INSIGHTS (ONE PARAGRAPH)
============================================================ */

$pdf->SetFont("Arial","B",9);
$pdf->Cell(0,5,"7.5 Insights & Recommendations",0,1);

$topTypeCode = array_key_first($repeatByType);
$topTypeName = $testNames[$topTypeCode] ?? $topTypeCode;

$topClient = array_key_first($repeatByClient);

$ins = "- Top repeat test type: {$topTypeName}\n"
     . "- Top repeat client: {$topClient}\n"
     . "- Repeat rate: {$repeatPct}%\n"
     . "- Focus corrective actions on sample handling, technician checks, and equipment verification.";

$pdf->SetFont("Arial","",8);
$pdf->MultiCell(0,4.5,$ins);

SkipRepeatSection:
$pdf->Ln(2);


/* ============================================================
   SECTION 8 — NCR (NON CONFORMITY REPORTS) — FULL ANALYSIS — OPTIMIZED (CONTROLLED)
============================================================ */

ini_set("memory_limit","512M");
gc_enable();

$pdf->AddPage();
$pdf->SectionTitle("8. NCR (Non-Conformity Reports) Analysis");

/* ============================================================
   CLIENT CACHE (PRELOAD CLIENTS FOR ZERO REPEATED QUERIES)
============================================================ */
$clientCacheRaw = find_by_sql("
    SELECT Sample_ID, Sample_Number, Client
    FROM lab_test_requisition_form
");
$clientIndex = [];
foreach ((array)$clientCacheRaw as $cx){
    $clientIndex[($cx["Sample_ID"] ?? '')."-".($cx["Sample_Number"] ?? '')] = $cx["Client"] ?? "UNKNOWN";
}
unset($clientCacheRaw);
gc_collect_cycles();

/* Helper */
function getClientFromCache($sid,$snum,$cache){
    $key = $sid."-".$snum;
    return $cache[$key] ?? "UNKNOWN";
}

/* ============================================================
   1) OFFICIAL NCR FROM ensayos_reporte
============================================================ */
$officialNCR = find_by_sql("
    SELECT 
        r.Client,
        e.Sample_ID,
        e.Sample_Number,
        e.Test_Type,
        e.Test_Condition,
        e.Noconformidad,
        e.Material_Type,
        e.Report_Date
    FROM ensayos_reporte e
    LEFT JOIN lab_test_requisition_form r
        ON r.Sample_ID = e.Sample_ID
       AND r.Sample_Number = e.Sample_Number
       AND FIND_IN_SET(e.Test_Type, r.Test_Type)
    WHERE YEAR(e.Report_Date) = '{$year}'
      AND (
            LOWER(e.Test_Condition) LIKE '%fail%' OR
            LOWER(e.Test_Condition) LIKE '%reject%' OR
            LOWER(e.Test_Condition) LIKE '%no pasa%'
      )
");

/* ============================================================
   2) HIDDEN NCR — FROM ALL TEST TABLES (Comments)
============================================================ */
$testTables = [
    'atterberg_limit','moisture_oven','moisture_constant_mass',
    'moisture_microwave','moisture_scale','grain_size_general',
    'grain_size_coarse','grain_size_fine','soundness',
    'specific_gravity','specific_gravity_coarse','specific_gravity_fine',
    'standard_proctor','hydrometer','double_hydrometer'
];

$keywords = [
    "insufficient", "insuficiente",
    "no valido", "no válido",
    "muestra insuficiente",
    "insufficient material"
];

$hiddenNCR = [];

foreach ($testTables as $tb){

    $rows = find_by_sql("
        SELECT 
            Sample_ID,
            Sample_Number,
            Test_Type,
            Material_Type,
            Comments,
            Registed_Date
        FROM {$tb}
        WHERE YEAR(Registed_Date) = '{$year}'
    ");

    foreach ((array)$rows as $r){

        $c = strtolower(trim((string)($r["Comments"] ?? "")));
        if ($c === "") continue;

        foreach ($keywords as $kw){
            if (str_contains($c,$kw)){

                $client = getClientFromCache(($r["Sample_ID"] ?? ''),($r["Sample_Number"] ?? ''),$clientIndex);

                $hiddenNCR[] = [
                    "Client"        => $client,
                    "Sample_ID"     => $r["Sample_ID"] ?? "",
                    "Sample_Number" => $r["Sample_Number"] ?? "",
                    "Test_Type"     => $r["Test_Type"] ?? "",
                    "Test_Condition"=> "Fail",
                    "Noconformidad" => $r["Comments"] ?? "",
                    "Material_Type" => $r["Material_Type"] ?? "UNKNOWN",
                    "Report_Date"   => $r["Registed_Date"] ?? ""
                ];
                break;
            }
        }
    }

    unset($rows);
    gc_collect_cycles();
}

/* ============================================================
   3) MERGE SOURCES (MEMORY-SAFE MERGE)
============================================================ */
$allNCR = (array)$officialNCR;
foreach ((array)$hiddenNCR as $n) $allNCR[] = $n;
unset($officialNCR, $hiddenNCR);
gc_collect_cycles();

/* Si no hay NCR */
if (empty($allNCR)){
    $pdf->BodyText("No NCR recorded for this year.");
    $pdf->Ln(10);
    goto END_NCR_SECTION;
}

/* ============================================================
   8.1 — NCR Summary by Test Type (FULL NAMES + CONTROLLED)
============================================================ */
$pdf->SubTitle("8.1 NCR Summary by Test Type");

/* Normaliza texto */
function normTest($v){
    $v = (string)$v;
    $v = str_replace(["\xC2\xA0", "\t", "\r", "\n"], " ", $v);
    $v = strtoupper(trim($v));
    $v = preg_replace('/\s+/', ' ', $v);
    return $v;
}

/* Convierte alias/variantes a un código estándar */
function aliasTest($raw){
    $t = normTest($raw);
    if ($t === '' || $t === 'N/A' || $t === 'NA') return '';

    $flat = str_replace(['.', '-', '_', '/', '\\', ' '], '', $t);

   $map = [
        // Grain Size
        'GS'     => 'Grain Size',
        'GS_FF'  => 'Grain Size',
        'GS_Full' => 'Grain Size',
        'GS_UTF'    => 'Grain Size',
        'GS_Soil'   => 'Grain Size',
         'Gradation'   => 'Grain Size',
         'GRADATION'   => 'Grain Size',
        

        // Atterberg
        'ATTERBERG LIMITS'   => 'Atterberg Limit',        
        'ATTERBERG LIMIT'   => 'Atterberg Limit',
        'ATTEMBERG LIMIT'   => 'Atterberg Limit',
        'ATTERBERG LIMIT-PI' => 'Atterberg Limit',
        'ATTERBERG LIMIT-PL' => 'Atterberg Limit',
        'ATTERBERG LIMIT-PI REQUIREMENT'    => 'Atterberg Limit',
        'ATTERBERG LIMIT-LL' => 'Atterberg Limit',
         'AL'              => 'Atterberg Limit',

        // Moisture Content
        'MC'              => 'MC',
        'MOISTURECONTENT' => 'MC',
        'WATERCONTENT'    => 'MC',

        // Proctor
        'SP'              => 'SP',
        'STANDARDPROCTOR' => 'SP',
        'MP'              => 'MP',
        'MODIFIEDPROCTOR' => 'MP',

        // Hydrometer
        'HY'              => 'Hydrometer',
        'HYDROMETER'      => 'HY',
        'DHY'             => 'Double Hydrometer',
        'DOUBLEHYDROMETER'=> 'DHY',

        // UCS / PLT / BTS
        'UCS'             => 'UCS',
        'UNCONFINEDCOMPRESSIVESTRENGTH' => 'UCS',
        'PLT'             => 'PLT',
        'POINTLOAD'       => 'PLT',
        'POINTLOADTEST'   => 'PLT',
        'BTS'             => 'BTS',
        'BRAZILIAN'       => 'BTS',
        'ACID REACTIVITY'   => 'Acid Reactivity',

        // Specific Gravity / Soundness / etc (ejemplos)
        'SG'              => 'Specific Gravity',
        'SG-Fine'         => 'Specific Gravity',
        'SG-Coarse'       => 'Specific Gravity',
        'MC_Oven'           => 'Moisture Content',
        'AR'              => 'Acid Reactivity',
        'MC_MICROWAVE'  => 'Moisture Content',
        'MC_Scale'  => 'Moisture Content',
        'SND'  => 'Soundness',
        'SOUNDNESS'  => 'Soundness',
         'LAA'  => 'Los Angeles Abrasion',
         'PQ'  => 'Poor Quality Rock',
         'Crudo'  => 'Raw Material',
         'Diorite'  => 'Diorite',
         'Common'  => 'Common',
    ];


    if (isset($map[$t])) return $map[$t];
    if (isset($map[$flat])) return $map[$flat];

    if (preg_match('/^[A-Z0-9]{1,10}$/', $t)) return $t;

    return $t;
}

/* Conteo por tipo */
$ncrPerType = [];
foreach ($allNCR as $n){
    $raw = $n["Test_Type"] ?? '';
    $code = aliasTest($raw);
    if ($code === '') continue;

    if (!isset($ncrPerType[$code])) $ncrPerType[$code] = 0;
    $ncrPerType[$code]++;
}

if (empty($ncrPerType)){
    $pdf->BodyText("No NCR recorded for this year.");
    $pdf->Ln(10);
    goto END_NCR_SECTION;
}

arsort($ncrPerType);
$totalNCR = array_sum($ncrPerType);

/* --- TABLE TOP 15 + OTHER --- */
$topNTable = 15;
$topTable = array_slice($ncrPerType, 0, $topNTable, true);
$otherCnt = $totalNCR - array_sum($topTable);
if ($otherCnt > 0) $topTable["OTHER"] = $otherCnt;

$pdf->TableHeader([
    80=>"Test Type",
    35=>"NCR Count",
    30=>"% of Total"
]);

foreach ($topTable as $code=>$cnt){
    $pct  = $totalNCR > 0 ? round(($cnt/$totalNCR)*100,1) : 0;
    $fullName = ($code === "OTHER") ? "OTHER" : ($testNames[$code] ?? $code);

    $pdf->TableRow([
        80=>safeTextUtf($fullName),
        35=>$cnt,
        30=>$pct."%"
    ]);
}
$pdf->TableRow([80=>"TOTAL",35=>$totalNCR,30=>"100%"]);
$pdf->Ln(6);

/* --- CHART TOP 12 + OTHER (NO MULTI-PAGE) --- */
ensureSpace($pdf,55);

$topNChart = 12;
$topChart = array_slice($ncrPerType, 0, $topNChart, true);
$otherCntC = $totalNCR - array_sum($topChart);
if ($otherCntC > 0) $topChart["OTHER"] = $otherCntC;

$maxV = max($topChart); if ($maxV < 1) $maxV = 1;

$x0=25; $y0=$pdf->GetY();
$barH=4; $gap=2;

$pdf->SetFont("Arial","",8);

foreach ($topChart as $code=>$cnt){

    $label = ($code === "OTHER") ? "OTHER" : ($testNames[$code] ?? $code);
    $bw = ($cnt/$maxV)*95;

    $pdf->SetXY($x0,$y0);
    $pdf->Cell(70,$barH,safeTextUtf($label),0,0);

    $pdf->SetFillColor(80,140,210);
    $pdf->Rect($x0+72,$y0,$bw,$barH,"F");

    $pdf->SetXY($x0+172,$y0);
    $pdf->Cell(12,$barH,$cnt,0,0,"R");

    $y0 += ($barH+$gap);
}

$pdf->SetY($y0+6);
gc_collect_cycles();

/* ============================================================
   8.2 — NCR Trend by Month (COMPACT)
============================================================ */
$pdf->SubTitle("8.2 NCR Trend by Month");
ensureSpace($pdf,55);

$perMonthNCR = array_fill(1,12,0);
foreach ($allNCR as $n){
    if (!empty($n["Report_Date"])){
        $m = (int)substr($n["Report_Date"],5,2);
        if ($m>=1 && $m<=12) $perMonthNCR[$m]++;
    }
}

$monthShort=[1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",
             7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec"];

$maxMonth = max($perMonthNCR); if ($maxMonth < 1) $maxMonth = 1;

$x0=25; $y0=$pdf->GetY();
$barH=4; $gap=1.5;

$pdf->SetFont("Arial","",8);

foreach ($perMonthNCR as $i=>$val){

    $bw = ($val/$maxMonth)*95;

    $pdf->SetXY($x0,$y0);
    $pdf->Cell(12,$barH,$monthShort[$i],0,0);

    $pdf->SetFillColor(70,170,120);
    $pdf->Rect($x0+14,$y0,$bw,$barH,"F");

    $pdf->SetXY($x0+115,$y0);
    $pdf->Cell(10,$barH,$val,0,0,"R");

    $y0 += ($barH+$gap);
}

$pdf->SetY($y0+6);
gc_collect_cycles();

/* ============================================================
   8.3 — NCR by Material Type (FULL NAMES + CONTROLLED)
============================================================ */
$pdf->SubTitle("8.3 NCR by Material Type");
ensureSpace($pdf,55);

/* Normalización material */
function normMat($v){
    $v = (string)$v;
    $v = str_replace(["\xC2\xA0", "\t", "\r", "\n"], " ", $v);
    $v = strtoupper(trim($v));
    $v = preg_replace('/\s+/', ' ', $v);
    return $v;
}

/* Mapa materiales (tuyo) */
$materialMap = [
    'LPF' => 'Low Permeability Fill',
    'CF'  => 'Coarse Filter',
    'COARSE FILTER' => 'Coarse Filter',
    'FF'  => 'Fine Filter',
    'FINE FILTER' => 'Fine Filter',
    'IRF' => 'Intermediate Rock Fill',
    'TRF' => 'Transition Rock Fill',
    'UFF' => 'Upstream Facing Fill',
    'UTF' => 'Upstream Transition Fill',
    'RF'  => 'Rock Fill',
    'SOIL'=> 'Soil',
    'FRF' => 'Fine Rock Fill',
];

/* Expandir a nombre completo */
function materialFullName($raw, $map){
    $k = normMat($raw);

    if ($k === '' || $k === 'N/A' || $k === 'NA' || $k === 'NULL') {
        return 'UNKNOWN';
    }

    if (isset($map[$k])) return $map[$k];

    foreach ($map as $abbr => $full){
        if ($abbr === '') continue;
        if (strpos($k, $abbr) !== false) return $full;
    }

    return $k;
}

/* Conteo por material */
$perMaterial = [];
foreach ($allNCR as $n){
    $mRaw  = $n["Material_Type"] ?? "";
    $mFull = materialFullName($mRaw, $materialMap);
    if (!isset($perMaterial[$mFull])) $perMaterial[$mFull] = 0;
    $perMaterial[$mFull]++;
}

arsort($perMaterial);
$totalMT = array_sum($perMaterial);

/* --- TABLE TOP 15 + OTHER --- */
$topNMatTable = 15;
$matTable = array_slice($perMaterial, 0, $topNMatTable, true);
$otherMat = $totalMT - array_sum($matTable);
if ($otherMat > 0) $matTable["OTHER"] = $otherMat;

$pdf->TableHeader([
    80=>"Material Type",
    35=>"NCR Count",
    30=>"% of Total"
]);

foreach ($matTable as $matFull=>$cnt){
    $pct = $totalMT>0 ? round(($cnt/$totalMT)*100,1) : 0;
    $pdf->TableRow([
        80=>safeTextUtf($matFull),
        35=>$cnt,
        30=>$pct."%"
    ]);
}
$pdf->TableRow([80=>"TOTAL",35=>$totalMT,30=>"100%"]);
$pdf->Ln(6);

/* --- CHART TOP 12 + OTHER (NO MULTI-PAGE) --- */
ensureSpace($pdf,55);

$topNMatChart = 12;
$matChart = array_slice($perMaterial, 0, $topNMatChart, true);
$otherMatC = $totalMT - array_sum($matChart);
if ($otherMatC > 0) $matChart["OTHER"] = $otherMatC;

$maxMat = max($matChart); if ($maxMat < 1) $maxMat = 1;

$x0=25; $y0=$pdf->GetY();
$barH=4; $gap=2;

$pdf->SetFont("Arial","",8);

foreach ($matChart as $matFull=>$cnt){

    $bw = ($cnt/$maxMat)*95;

    $pdf->SetXY($x0,$y0);
    $pdf->Cell(70,$barH,safeTextUtf($matFull),0,0);

    $pdf->SetFillColor(200,90,90);
    $pdf->Rect($x0+72,$y0,$bw,$barH,"F");

    $pdf->SetXY($x0+172,$y0);
    $pdf->Cell(12,$barH,$cnt,0,0,"R");

    $y0 += ($barH+$gap);
}

$pdf->SetY($y0+6);
gc_collect_cycles();

/* ============================================================
   8.4 — NCR Insight (FULL NAMES)
============================================================ */
$pdf->SubTitle("8.4 NCR Insight");

/* Top test (por código) */
$topTest = array_key_first($ncrPerType) ?? "N/A";
$topTestCount = $ncrPerType[$topTest] ?? 0;
$topPct = $totalNCR>0 ? round(($topTestCount/$totalNCR)*100,1) : 0;
$topTestName = $testNames[$topTest] ?? $topTest;

/* Top client */
$perClient=[];
foreach ($allNCR as $n){
    $c = trim($n["Client"] ?? "");
    if ($c==="") $c="UNKNOWN";
    if (!isset($perClient[$c])) $perClient[$c]=0;
    $perClient[$c]++;
}
arsort($perClient);
$topClient = array_key_first($perClient) ?? "N/A";
$topClientCount = $perClient[$topClient] ?? 0;
$topClientPct = $totalNCR>0 ? round(($topClientCount/$totalNCR)*100,1) : 0;

/* Lowest test types (por tabla ya controlada no tiene sentido buscar -3 de todo si hay pocos) */
$lowestTests = array_slice(array_keys($ncrPerType), -3);
$lowNames = [];
foreach ($lowestTests as $c){
    $lowNames[] = $testNames[$c] ?? $c;
}
$lowTestsStr = implode(", ", $lowNames);

$msg = "
The NCR analysis for the period {$year} highlights several important quality performance findings:
- The test type with the greatest number of non-conformities was {$topTestName}, accounting for approximately {$topPct}% of all NCRs.
- The client with the highest NCR concentration was {$topClient}, responsible for {$topClientPct}% of all recorded cases.
- Test types with the lowest NCR frequency included {$lowTestsStr}, indicating consistent performance in those categories.
- NCR behavior shows clustering in specific test types and materials, rather than uniform distribution across operations.

These trends highlight clear improvement priorities where targeted corrective actions would yield the highest reduction in NCR frequency.
";

$pdf->SetFont("Arial","",8);
$pdf->WriteFormatted($msg);
$pdf->Ln(5);

END_NCR_SECTION:
unset($allNCR);
gc_collect_cycles();


/* ============================================================
   SECTION 9 — SAMPLE FLOW DIAGNOSTICS (BOTTLENECK FINDER) — OPTIMIZED
============================================================ */

ini_set("memory_limit","512M");
gc_enable();

$pdf->AddPage();
$pdf->SectionTitle("9. Sample Flow Diagnostics (Bottleneck Finder)");

/* ============================================================
   CLIENT CACHE (AVOIDS THOUSANDS OF REPEATED QUERIES)
============================================================ */

$clientCacheRaw = find_by_sql("
    SELECT Sample_ID, Sample_Number, Client
    FROM lab_test_requisition_form
");
$clientIndex = [];
foreach ($clientCacheRaw as $cx){
    $clientIndex[$cx["Sample_ID"]."-".$cx["Sample_Number"]] = $cx["Client"];
}
unset($clientCacheRaw);
gc_collect_cycles();

function cacheClient($sid,$num,$cache){
    return $cache[$sid."-".$num] ?? "UNKNOWN";
}

/* ============================================================
   9.0 LOAD ALL FLOW DATA (SINGLE QUERY, NO DUPLICATE HITS)
============================================================ */

$flowData = find_by_sql("
    SELECT 
        r.Sample_ID,
        r.Sample_Number,
        r.Test_Type,
        r.Registed_Date AS RegDate,

        /* PREPARATION */
        p.Start_Date AS PrepStart,
        tpr.Process_Started AS PrepTS,

        /* REALIZATION */
        rl.Start_Date AS RealStart,
        tr.Process_Started AS RealTS,

        /* DELIVERY */
        d.Start_Date AS DelStart,
        td.Process_Started AS DelTS,
        d.Start_Date AS DelivDate

    FROM lab_test_requisition_form r

    LEFT JOIN test_preparation p
        ON r.Sample_ID = p.Sample_ID
       AND r.Sample_Number = p.Sample_Number
       AND FIND_IN_SET(p.Test_Type, r.Test_Type)

    LEFT JOIN test_workflow tpr
        ON tpr.Sample_ID = r.Sample_ID
       AND tpr.Sample_Number = r.Sample_Number
       AND tpr.Test_Type = p.Test_Type
       AND tpr.Status = 'Preparación'

    LEFT JOIN test_realization rl
        ON r.Sample_ID = rl.Sample_ID
       AND r.Sample_Number = rl.Sample_Number
       AND FIND_IN_SET(rl.Test_Type, r.Test_Type)

    LEFT JOIN test_workflow tr
        ON tr.Sample_ID = r.Sample_ID
       AND tr.Sample_Number = r.Sample_Number
       AND tr.Test_Type = rl.Test_Type
       AND tr.Status = 'Realización'

    LEFT JOIN test_delivery d
        ON r.Sample_ID = d.Sample_ID
       AND r.Sample_Number = d.Sample_Number
       AND FIND_IN_SET(d.Test_Type, r.Test_Type)

    LEFT JOIN test_workflow td
        ON td.Sample_ID = r.Sample_ID
       AND td.Sample_Number = r.Sample_Number
       AND td.Test_Type = d.Test_Type
       AND td.Status = 'Entrega'

    WHERE r.Registed_Date BETWEEN '{$start}' AND '{$end}'
");

/* Helper — avoids null and invalid dates */
function daysDiffSafe($a, $b){
    if (!$a || !$b) return null;
    $t1 = strtotime($a);
    $t2 = strtotime($b);
    if (!$t1 || !$t2) return null;
    return round(($t2 - $t1) / 86400, 2);
}

/* ============================================================
   METRIC ARRAYS — INITIALIZED LIGHT
============================================================ */

$reg2prep   = [];
$prep2real  = [];
$real2del   = [];
$totalCycle = [];

$perMonthStage = [
    "RegPrep"  => array_fill(1,12,[]),
    "PrepReal" => array_fill(1,12,[]),
    "RealDel"  => array_fill(1,12,[])
];

$testDelay = [];

/* ============================================================
   MAIN LOOP — OPTIMIZED
============================================================ */

foreach ($flowData as $f){

    $reg  = $f["RegDate"];
    $prep = $f["PrepStart"] ?? $f["PrepTS"];
    $real = $f["RealStart"] ?? $f["RealTS"];
    $del  = $f["DelivDate"] ?? $f["DelStart"] ?? $f["DelTS"];

    $tests = explode(",", $f["Test_Type"]);

    foreach ($tests as $t){

        $t = trim($t);
        if ($t === "") continue;

        $d1 = daysDiffSafe($reg,  $prep);
        $d2 = daysDiffSafe($prep, $real);
        $d3 = daysDiffSafe($real, $del);
        $dT = daysDiffSafe($reg,  $del);

        if ($d1 !== null) $reg2prep[]  = $d1;
        if ($d2 !== null) $prep2real[] = $d2;
        if ($d3 !== null) $real2del[]  = $d3;
        if ($dT !== null) $totalCycle[]= $dT;

        if (!isset($testDelay[$t])) $testDelay[$t] = [];
        if ($dT !== null) $testDelay[$t][] = $dT;

        if ($del){
            $m = intval(substr($del,5,2));
            if ($m>=1 && $m<=12){
                if ($d1 !== null) $perMonthStage["RegPrep"][$m][]  = $d1;
                if ($d2 !== null) $perMonthStage["PrepReal"][$m][] = $d2;
                if ($d3 !== null) $perMonthStage["RealDel"][$m][]  = $d3;
            }
        }
    }
}

unset($flowData);
gc_collect_cycles();

/* ============================================================
   AVERAGES — SAFE
============================================================ */

$avgD1 = count($reg2prep)  ? round(array_sum($reg2prep)/count($reg2prep),2) : 0;
$avgD2 = count($prep2real) ? round(array_sum($prep2real)/count($prep2real),2) : 0;
$avgD3 = count($real2del)  ? round(array_sum($real2del)/count($real2del),2) : 0;
$avgDT = count($totalCycle)? round(array_sum($totalCycle)/count($totalCycle),2) : 0;

$sumX = $avgD1 + $avgD2 + $avgD3;
if ($sumX == 0) $sumX = 1;

$p1 = round(($avgD1/$sumX)*100,1);
$p2 = round(($avgD2/$sumX)*100,1);
$p3 = round(($avgD3/$sumX)*100,1);

/* ============================================================
   9.1 — PROCESS FLOW TIMELINE
============================================================ */

$pdf->SubTitle("9.1 Process Flow Timeline");

$pdf->TableHeader([
    50=>"Transition",
    30=>"Avg Days",
    25=>"% of Cycle"
]);

$pdf->TableRow(["50"=>"Registration - Preparation", "30"=>$avgD1, "25"=>"$p1%"]);
$pdf->TableRow(["50"=>"Preparation - Realization",  "30"=>$avgD2, "25"=>"$p2%"]);
$pdf->TableRow(["50"=>"Realization - Delivery",     "30"=>$avgD3, "25"=>"$p3%"]);
$pdf->TableRow(["50"=>"TOTAL CYCLE TIME",           "30"=>$avgDT, "25"=>"100%"]);

$pdf->Ln(8);

/* ---- Chart ---- */
ensureSpace($pdf,60);

$x0 = 20;
$y0 = $pdf->GetY();
$h  = 6;

$maxBar = max([$avgD1,$avgD2,$avgD3,1]);

$items = [
    "Reg - Prep"=>$avgD1,
    "Prep - Real"=>$avgD2,
    "Real - Del"=>$avgD3
];

$colors = [
    [70,130,180],
    [220,100,80],
    [120,180,90]
];

$i=0;
foreach ($items as $label=>$v){

    $bar = ($v/$maxBar)*110;

    $pdf->SetXY($x0,$y0);
    $pdf->Cell(35,$h,$label,0,0);

    $pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
    $pdf->Rect($x0+40,$y0,$bar,$h,"F");

    $pdf->SetXY($x0+155,$y0);
    $pdf->Cell(10,$h,$v,0,0);

    $y0 += $h + 3;
    $i++;
}

$pdf->Ln(15);

/* ============================================================
   9.2 — MONTHLY BOTTLENECK HEATMAP
============================================================ */

$pdf->SubTitle("9.2 Monthly Bottleneck Heatmap");

$pdf->TableHeader([
    15=>"Month",
    25=>"Reg-Prep",
    35=>"Prep-Real",
    30=>"Real-Del",
    45=>"Bottleneck"
]);

$monthNames = [
 1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",
 7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec"
];

foreach ($monthNames as $m=>$mn){

    $L1 = $perMonthStage["RegPrep"][$m];
    $L2 = $perMonthStage["PrepReal"][$m];
    $L3 = $perMonthStage["RealDel"][$m];

    $c1 = max(count($L1),1);
    $c2 = max(count($L2),1);
    $c3 = max(count($L3),1);

    $d1 = count($L1)? round(array_sum($L1)/$c1,2) : 0;
    $d2 = count($L2)? round(array_sum($L2)/$c2,2) : 0;
    $d3 = count($L3)? round(array_sum($L3)/$c3,2) : 0;

    $maxV = max([$d1,$d2,$d3]);

    $bot = $maxV==0 ? "None" :
          ($maxV==$d1 ? "Reg-Prep" :
          ($maxV==$d2 ? "Prep-Real" : "Real-Del"));

    $pdf->TableRow([
        15=>$mn,
        25=>$d1,
        35=>$d2,
        30=>$d3,
        45=>$bot
    ]);
}

$pdf->Ln(10);

/* ============================================================
   9.3 — TEST TYPES WITH HIGHEST CYCLE TIME
============================================================ */

$pdf->SubTitle("9.3 Test Types with Highest Total Cycle Time");

$avgTestDelay = [];
foreach ($testDelay as $t=>$vals){
    $c = count($vals);
    $avgTestDelay[$t] = $c>0 ? round(array_sum($vals)/$c,2) : 0;
}

arsort($avgTestDelay);
$top5 = array_slice($avgTestDelay,0,5,true);
$sumTop = max(array_sum($top5),1);

$pdf->TableHeader([
    50=>"Test Type",
    30=>"Avg Days",
    35=>"% Delay Impact"
]);

foreach ($top5 as $t=>$v){
    $pct = round(($v/$sumTop)*100,1);
    $name = $testNames[$t] ?? $t;

    $pdf->TableRow([
        50=>utf8_decode($name),
        30=>$v,
        35=>$pct."%"
    ]);
}

$pdf->TableRow([50=>"TOTAL (Top 5)",30=>"",35=>"100%"]);
$pdf->Ln(10);

/* ---- Bar chart ---- */
ensureSpace($pdf,40);
$x0 = 20;
$y0 = $pdf->GetY();
$h  = 6;

$maxD = max($top5 ?: [1]);
foreach ($top5 as $t=>$v){

    $bar = ($v/$maxD)*110;

    $name = $testNames[$t] ?? $t;

    $pdf->SetXY($x0,$y0);
    $pdf->Cell(45,$h,utf8_decode($name),0,0);

    $pdf->SetFillColor(150,60,60);
    $pdf->Rect($x0+48,$y0,$bar,$h,"F");

    $pdf->SetXY($x0+160,$y0);
    $pdf->Cell(10,$h,$v,0,0);

    $y0 += $h + 3;
}

$pdf->Ln(15);

/* ============================================================
   9.4 — BOTTLENECK PROBABILITY MATRIX
============================================================ */

$pdf->SubTitle("9.4 Bottleneck Probability Matrix");

$prob = [
    "Reg-Prep"=>["P"=>2,"S"=>1],
    "Prep-Real"=>["P"=>3,"S"=>3],
    "Real-Del"=>["P"=>2,"S"=>2]
];

$pdf->TableHeader([
    35=>"Stage",
    25=>"Prob",
    20=>"Sev",
    30=>"Risk Score"
]);

$highestRiskScore = -1;
$highestRiskStage = "";

foreach ($prob as $st=>$r){
    $score = $r["P"]*$r["S"];
    if ($score > $highestRiskScore){
        $highestRiskScore = $score;
        $highestRiskStage = $st." (Risk: ".$score.")";
    }

    $pdf->TableRow([
        35=>$st,
        25=>$r["P"],
        20=>$r["S"],
        30=>$score
    ]);
}

$pdf->SetFont("Arial","",7);
$pdf->MultiCell(0,4,utf8_decode("LEGEND:
Prob = Probability (1=Low, 2=Med, 3=High)
Sev  = Severity
Risk Score = Prob × Sev"),0,'L');
$pdf->SetFont("Arial","",8);

$pdf->Ln(8);

/* ---- Risk bars ---- */
ensureSpace($pdf,40);

$x0 = 20;
$y0 = $pdf->GetY();
$h  = 5;

foreach ($prob as $st=>$r){

    $score = $r["P"]*$r["S"];
    $bar   = ($score/9)*100;

    $pdf->SetXY($x0,$y0);
    $pdf->Cell(40,$h,$st,0,0);

    $pdf->SetFillColor(255,120,60);
    $pdf->Rect($x0+42,$y0,$bar,$h,"F");

    $pdf->SetXY($x0+150,$y0);
    $pdf->Cell(10,$h,$score,0,0);

    $y0 += $h + 3;
}

$pdf->Ln(15);

/* ============================================================
   9.5 — INTERPRETATION
============================================================ */

$pdf->SubTitle("9.5 Interpretation");

/* Main bottleneck */
$stageAverages = [
    "Registration - Preparation"=>$avgD1,
    "Preparation - Realization"=>$avgD2,
    "Realization - Delivery"=>$avgD3
];
arsort($stageAverages);
$mainBottle = array_key_first($stageAverages);

/* Slowest tests */
$sortedDelays = $avgTestDelay;
arsort($sortedDelays);
$slowTop = array_slice($sortedDelays,0,3,true);

$slowStr = implode(", ", array_map(function($t)use($testNames,$sortedDelays){
    return ($testNames[$t] ?? $t)." (".$sortedDelays[$t]." days)";
}, array_keys($slowTop)));

/* Slow months */
$monthCycleAvg = [];
foreach ($perMonthStage["RegPrep"] as $m=>$dummy){

    $L1=$perMonthStage["RegPrep"][$m];
    $L2=$perMonthStage["PrepReal"][$m];
    $L3=$perMonthStage["RealDel"][$m];

    $c1=max(count($L1),1);
    $c2=max(count($L2),1);
    $c3=max(count($L3),1);

    $d1=count($L1)?array_sum($L1)/$c1:0;
    $d2=count($L2)?array_sum($L2)/$c2:0;
    $d3=count($L3)?array_sum($L3)/$c3:0;

    $monthCycleAvg[$m]=round($d1+$d2+$d3,2);
}

arsort($monthCycleAvg);
$slowM = array_slice(array_keys($monthCycleAvg),0,2);
$monthFull=[1=>"January",2=>"February",3=>"March",4=>"April",5=>"May",
            6=>"June",7=>"July",8=>"August",9=>"September",10=>"October",
            11=>"November",12=>"December"];
$slowMonthStr = implode(" & ", array_map(fn($m)=>$monthFull[$m],$slowM));

/* Insight */
$msg="
During the analysis for {$year}, the following flow patterns were identified:
- The main bottleneck in the process was **{$mainBottle}**, contributing the highest average delay.
- The test types with the slowest cycle times were: **{$slowStr}**.
- The months with the highest congestion were: **{$slowMonthStr}**.
- The highest-risk stage was **{$highestRiskStage}**, based on probability and severity scoring.
- Addressing delays in **{$mainBottle}** will significantly reduce the laboratory's total cycle time.
";


/* Cleanup */
unset($reg2prep,$prep2real,$real2del,$totalCycle,$perMonthStage,$testDelay);
gc_collect_cycles();


$pdf->SetFont("Arial","",8);
$pdf->WriteFormatted($msg);

$pdf->AddPage();
$pdf->SectionTitle("Closing Statement");

$closingText = "
The results achieved throughout the year reflect a laboratory that operates with discipline, technical rigor, and full commitment to quality. The consistency observed across workflows, documentation, and test execution confirms the strength of the processes that support project development.

Beyond the metrics, this performance is the result of the dedication of every technician, supervisor, and document control specialist. The laboratory's reliability is built daily through teamwork, responsibility, and adherence to established standards.

Looking forward, the laboratory is positioned to strengthen operational capacity, optimize bottleneck stages identified in this report, and further improve response times. These efforts will reinforce compliance, traceability, and readiness to support the evolving needs of the project.

The year closes with a stable operational base and a clear path for continued improvement. With this foundation, the upcoming cycle begins with renewed direction, technical focus, and confidence in the team's ability to deliver at the highest standard.
";

$pdf->SetFont("Arial","",8);
$pdf->MultiCell(0,5,utf8_decode($closingText),0,'L');
$pdf->Ln(5);



/* ============================================================
   OUTPUT
============================================================ */
ob_end_clean();
$pdf->Output("I","Annual_Report_$year.pdf");
exit;