<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===============================
   1. DEFINIR SEMANA ISO
================================*/
$year = isset($_GET['anio']) ? (int)$_GET['anio'] : date('o');
$week = isset($_GET['semana']) ? (int)$_GET['semana'] : date('W');

$dt = new DateTime();
$dt->setISODate($year, $week, 1);
$start_str = $dt->format("Y-m-d 00:00:00");
$dt->setISODate($year, $week, 7);
$end_str = $dt->format("Y-m-d 23:59:59");

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* ===============================
   2. COUNT HELPER
================================*/
function get_count($table, $field, $start, $end) {
    $q = "SELECT COUNT(*) AS total FROM {$table}
          WHERE {$field} BETWEEN '{$start}' AND '{$end}'";
    $r = find_by_sql($q);
    return (int)($r[0]['total'] ?? 0);
}

/* ===============================
   3. CONSULTAS BASE
================================*/
function resumen_diario_cliente($start, $end){
    return find_by_sql("
        SELECT 
            DATE(Registed_Date) AS dia,
            Client,
            COUNT(*) AS total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY DATE(Registed_Date), Client
        ORDER BY dia ASC, Client ASC
    ");
}

function resumen_tipo_entregado($start,$end){
    return find_by_sql("
        SELECT 
            t.Sample_ID,
            t.Sample_Number,
            r.Client,
            t.Test_Type
        FROM test_delivery t
        LEFT JOIN lab_test_requisition_form r
          ON t.Sample_ID = r.Sample_ID
         AND t.Sample_Number = r.Sample_Number
         AND t.Test_Type = r.Test_Type
        WHERE t.Register_Date BETWEEN '{$start}' AND '{$end}'
    ");
}

/* ===============================
   4. FUNCION PARA EVITAR CORTE
================================*/
function ensure_space($pdf, $neededHeight = 80){
    if ($pdf->GetY() + $neededHeight > 260) {
        $pdf->AddPage();
    }
}

/* ===============================
   5. PALETA DE COLORES
================================*/
function pickColor($i){
    $colors = [
        [66,133,244],   // azul
        [219,68,55],    // rojo
        [244,180,0],    // amarillo
        [15,157,88],    // verde
        [171,71,188],   // morado
        [0,172,193],    // cyan
        [255,112,67]    // naranja
    ];
    return $colors[$i % count($colors)];
}

/* ======================================================
   RESUMEN CLIENTE PARA COMPLETION GRAPH — WEEKLY REPORT
======================================================*/
function resumen_cliente($start, $end){
    return find_by_sql("
        SELECT 
            UPPER(TRIM(r.Client)) AS Client,
            COUNT(*) AS solicitados,
            SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) AS entregados
        FROM lab_test_requisition_form r
        LEFT JOIN test_delivery d
            ON r.Sample_ID = d.Sample_ID
            AND r.Sample_Number = d.Sample_Number
            AND r.Test_Type = d.Test_Type
        WHERE r.Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY r.Client
        ORDER BY solicitados DESC
    ");
}


/* ===============================
   6. MAPA DE ALIAS → NOMBRE
================================*/
function loadTechnicianAliasMap() {

    $rows = find_by_sql("
        SELECT name, alias
        FROM users
        WHERE TRIM(name) <> ''
    ");

    $aliasMap = [];
    $firstLetterCount = [];
    $firstLetterName  = [];

    foreach ($rows as $r){

        $name = trim((string)$r['name']);
        if ($name === "") continue;

        $aliasRaw = trim((string)$r['alias']);

        if ($aliasRaw !== "") {
            $tokens = preg_split('/[\/\-\s,\\\\]+/', strtolower($aliasRaw));
            foreach ($tokens as $t) {
                $t = trim($t);
                if ($t !== "") {
                    $aliasMap[$t] = $name;
                }
            }
        }

        $first = strtolower(substr($name,0,1));
        if (!isset($firstLetterCount[$first])) $firstLetterCount[$first] = 0;
        $firstLetterCount[$first]++;
    }

    foreach ($rows as $r){
        $name = trim((string)$r['name']);
        if ($name === "") continue;

        $first = strtolower(substr($name,0,1));

        if ($firstLetterCount[$first] === 1){
            $firstLetterName[$first] = $name;
        }
    }

    return [$aliasMap, $firstLetterName];
}

/* ===============================
   7. RESOLVER TECNICO
================================*/
function resolveTechnician($aliasMap, $firstLetterName, $rawTech){

    if ($rawTech === null) return null;

    $clean = strtolower(trim((string)$rawTech));
    if ($clean === "") return null;

    $parts = preg_split('/[\/\-\s,\\\\]+/', $clean);
    $names = [];

    foreach ($parts as $p){

        if ($p === "") continue;

        if (isset($aliasMap[$p])) {
            $names[] = $aliasMap[$p];
            continue;
        }

        $first = strtolower(substr($p,0,1));

        if (isset($firstLetterName[$first])) {
            $names[] = $firstLetterName[$first];
        }
    }

    $names = array_unique($names);
    if (empty($names)) return null;

    return implode(", ", $names);
}

/* ===============================
   8. PDF CLASS
================================*/
class PDF_WEEKLY extends FPDF {

    public $week; 
    public $year;
    public $current_table_header = null;

    function __construct($week,$year){
        parent::__construct();
        $this->week = $week;
        $this->year = $year;
    }

    function Header() {
        if ($this->PageNo() > 1) return;

        if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
            $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 55);
        }

        $this->SetFont('Arial','B',16);
        $this->SetXY(120,12);
        $this->Cell(80,8,'WEEKLY LABORATORY REPORT',0,1,'R');

        $s = new DateTime();
        $s->setISODate($this->year,$this->week,1);
        $e = new DateTime();
        $e->setISODate($this->year,$this->week,7);

        $this->SetFont('Arial','',11);
        $this->SetXY(120,22);
        $this->Cell(80,7,"ISO WEEK {$this->week} ( ".$s->format('d M Y')." - ".$e->format('d M Y')." )",0,1,'R');

        $this->Ln(12);
        $this->section_title("1. Weekly Personnel Assigned");

        $this->SetFont('Arial','',10);
        $this->MultiCell(0,6,utf8_decode("
Chief Laboratory: Wendin De Jesús
Document Control: Yamilexi Mejía, Arturo Santana, Frandy Espinal
Lab Supervisors: Diana Vázquez, Victor Mercedes
Lab Technicians: Wilson Martínez, Rafy Leocadio, Rony Vargas, Jonathan Vargas
Lab Technicians: Rafael Reyes, Darielvy Félix, Jordany Almonte, Melvin Castillo
"));
        $this->Ln(3);
    }

    function section_title($txt){
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(200,220,255);
        $this->Cell(0,8,utf8_decode($txt),0,1,'L',true);
        $this->Ln(3);
    }

    function SubTitle($txt){
        $this->SetFont('Arial','B',11);
        $this->Cell(0,7,utf8_decode($txt),0,1,'L');
        $this->Ln(2);
    }

    function table_header($cols,$w){
        $this->SetFont('Arial','B',10);
        foreach($cols as $i=>$c){
            $this->Cell($w[$i],7,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
        $this->current_table_header = [
            'cols' => $cols,
            'widths' => $w
        ];
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',10);
        foreach($data as $i=>$d){
            $this->Cell($w[$i],7,utf8_decode($d),1,0,'C');
        }
        $this->Ln();
    }
}
$pdf = new PDF_WEEKLY($week,$year);
$pdf->AddPage();

/* =============================================
   ROW MULTILÍNEA + PAGE BREAK CONTROL
=============================================*/
function table_row_multiline($pdf, $data, $w){
    $pdf->SetFont('Arial','',9);
    $maxHeight = 5;

    foreach($data as $i => $txt){
        $txt = (string)$txt;
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i] - 2, 1);
        $h  = max(ceil($nb) * 5, 7);
        if($h > $maxHeight) $maxHeight = $h;
    }

    if ($pdf->GetY() + $maxHeight > 260){
        $pdf->AddPage();
        if($pdf->current_table_header){
            $pdf->table_header(
                $pdf->current_table_header['cols'],
                $pdf->current_table_header['widths']
            );
        }
    }

    foreach($data as $i => $txt){
        $txt = (string)$txt;
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($w[$i],5,utf8_decode($txt),1,'L');
        $pdf->SetXY($x + $w[$i], $y);
    }

    $pdf->Ln($maxHeight);
}

/* ===============================
   SECCIÓN 2 — WEEKLY SUMMARY
================================*/
$pdf->section_title("2. Weekly Summary of Activities");

$req  = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$prep = get_count("test_preparation","Register_Date",$start_str,$end_str);
$real = get_count("test_realization","Register_Date",$start_str,$end_str);
$del  = get_count("test_delivery","Register_Date",$start_str,$end_str);

$pdf->table_header(["Activity","Total"],[90,30]);
$pdf->table_row(["Requisitioned",$req],[90,30]);
$pdf->table_row(["In Preparation",$prep],[90,30]);
$pdf->table_row(["In Realization",$real],[90,30]);
$pdf->table_row(["Completed",$del],[90,30]);
$pdf->Ln(6);

/* ---------- GRÁFICO KPI COMPACTO ---------- */
ensure_space($pdf, 60);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,"Graph: Weekly Activity Summary",0,1);

$labels  = ["Req","Prep","Real","Del"];
$values  = [$req, $prep, $real, $del];

$chartX = 20;
$chartY = $pdf->GetY() + 4;
$chartW = 120;
$chartH = 40;

// Ejes
$pdf->SetDrawColor(0,0,0);
$pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);
$pdf->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH);

// Max
$maxVal = max($values);
if ($maxVal <= 0) $maxVal = 1;

// Valores del eje Y
$pdf->SetFont('Arial','',7);
$steps = 3;
for($i = 0; $i <= $steps; $i++){
    $posY = $chartY + $chartH - ($chartH / $steps * $i);
    $val  = round($maxVal / $steps * $i);
    $pdf->SetXY($chartX - 8, $posY - 2);
    $pdf->Cell(7,3,$val,0,0,'R');
}

// Barras
$countValues = count($values);
$barWidth = $countValues > 0 ? floor($chartW / $countValues) - 12 : 10;
if ($barWidth < 4) $barWidth = 4;

for($i = 0; $i < $countValues; $i++){
    list($r,$g,$b) = pickColor($i);
    $pdf->SetFillColor($r,$g,$b);

    $barH = ($values[$i] / $maxVal) * ($chartH - 5);
    $barX = $chartX + 10 + $i * ($barWidth + 12);
    $barY = $chartY + $chartH - $barH;

    // barra
    $pdf->Rect($barX, $barY, $barWidth, $barH, "F");

    // valor
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY($barX, $barY - 4);
    $pdf->Cell($barWidth,4,$values[$i],0,0,'C');

    // label eje X
    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($barX, $chartY + $chartH + 1);
    $pdf->Cell($barWidth,4,$labels[$i],0,0,'C');
}

// Leyenda compacta
$legendX = $chartX + $chartW + 4;
$legendY = $chartY + 2;

$pdf->SetFont('Arial','B',7);
$pdf->SetXY($legendX, $legendY);
$pdf->Cell(20,4,"Legend",0,1);

foreach($labels as $i=>$lbl){
    list($r,$g,$b) = pickColor($i);
    $pdf->SetFillColor($r,$g,$b);

    $pdf->Rect($legendX, $legendY + 5 + ($i*5), 4, 4, "F");
    $pdf->SetXY($legendX + 6, $legendY + 4 + ($i*5));
    $pdf->SetFont('Arial','',6);
    $pdf->Cell(20,4,$lbl);
}

$pdf->Ln(50);
/* ===============================
   SECCIÓN 3 — DAILY BREAKDOWN BY CLIENT
================================*/
$pdf->section_title("3. Daily Breakdown by Client");

/* ---------- Construir matriz día × cliente ---------- */

// 1) Lista de clientes de la semana
$clientesRes = find_by_sql("
    SELECT DISTINCT Client
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND TRIM(IFNULL(Client,'')) <> ''
    ORDER BY Client ASC
");

$clientNames = array_column($clientesRes, 'Client');

// 2) Matriz inicial (días de la semana ISO)
$matriz = [];
$dayCursor = new DateTime($start_str);
$endCursor = new DateTime($end_str);

while ($dayCursor <= $endCursor) {
    $fecha = $dayCursor->format("Y-m-d");
    $matriz[$fecha] = [];
    foreach ($clientNames as $cl) {
        $matriz[$fecha][$cl] = 0;
    }
    $dayCursor->modify("+1 day");
}

// 3) Llenar con datos reales
$raw = resumen_diario_cliente($start_str,$end_str);

foreach ($raw as $r) {
    $dia = $r['dia'];
    $cl  = $r['Client'];
    if (!isset($matriz[$dia])) continue;
    if (!in_array($cl,$clientNames,true)) continue;
    $matriz[$dia][$cl] = (int)$r['total'];
}

/* ---------- TABLA (DIA × CLIENTE) ---------- */

$pdf->SubTitle("Daily Registered Samples by Client");

if (empty($clientNames)) {
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No registered samples for this week.",1,1,'C',true);
    $pdf->Ln(5);
} else {

    // ancho columnas: fecha + 18 mm por cliente
    $colWidths = [45];  // fecha
    foreach ($clientNames as $cl) $colWidths[] = 22;

    // encabezado
    $header = ["Date"];
    foreach ($clientNames as $cl) {
        $header[] = $cl;
    }

    $pdf->table_header($header, $colWidths);

    // filas
    foreach ($matriz as $dia => $row) {
        $cells = [ date("D d-M", strtotime($dia)) ];
        foreach ($clientNames as $cl) {
            $cells[] = $row[$cl];
        }
        $pdf->table_row($cells, $colWidths);
    }

    $pdf->Ln(8);

    /* ---------- GRÁFICO MULTI-SERIE (CLIENTE × DÍA) ---------- */

    // Verificamos si hay datos > 0
    $hasData = false;
    foreach ($matriz as $row) {
        foreach ($row as $v) {
            if ($v > 0) { $hasData = true; break 2; }
        }
    }

    if ($hasData) {

        $pdf->SubTitle("Graph: Daily Samples by Client");

        ensure_space($pdf, 95);

        $chartX = 20;
        $chartY = $pdf->GetY() + 5;
        $chartW = 150;
        $chartH = 55;

        // Dibujar ejes
        $pdf->SetDrawColor(0,0,0);
        $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);                 // Eje Y
        $pdf->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH);  // Eje X

        // Calcular máximo
        $maxVal = 0;
        foreach ($matriz as $row) {
            foreach ($row as $v) {
                if ($v > $maxVal) $maxVal = $v;
            }
        }
        if ($maxVal <= 0) $maxVal = 1;

        // Escala eje Y
        $pdf->SetFont("Arial", "", 7);
        $steps = 4;
        for ($i = 0; $i <= $steps; $i++) {
            $yPos = $chartY + $chartH - ($chartH / $steps * $i);
            $val  = round($maxVal / $steps * $i);
            $pdf->SetXY($chartX - 8, $yPos - 2);
            $pdf->Cell(7, 4, $val, 0, 0, 'R');
        }

        // Dibujar barras
        $days        = array_keys($matriz);
        $barsPerDay  = count($clientNames);
        $totalBars   = $barsPerDay * count($days);
        if ($totalBars <= 0) $totalBars = 1;

        $bw = ($chartW - 20) / $totalBars;
        if ($bw <= 0) $bw = 1;

        $x = $chartX + 10;

        foreach ($days as $d) {
            $row = $matriz[$d];
            foreach ($clientNames as $idx => $cl) {

                $v = $row[$cl];
                $h = ($v / $maxVal) * ($chartH - 4);
                $y = $chartY + ($chartH - $h);

                list($r,$g,$b) = pickColor($idx);
                $pdf->SetFillColor($r,$g,$b);

                $pdf->Rect($x, $y, $bw, $h, "F");

                if ($v > 0) {
                    $pdf->SetFont('Arial','B',7);
                    $pdf->SetXY($x, $y - 4);
                    $pdf->Cell($bw, 4, $v, 0, 0, 'C');
                }

                $x += $bw;
            }
        }

        // EJE X: días
        $pdf->SetFont("Arial", "", 7);
        $x = $chartX + 10;
        $groupW = $bw * $barsPerDay;

        foreach ($days as $d) {
            $pdf->SetXY($x, $chartY + $chartH + 2);
            $pdf->MultiCell($groupW, 4, date("D", strtotime($d)), 0, 'C');
            $x += $groupW;
        }

        // LEYENDA VERTICAL DERECHA (COLORES POR CLIENTE)
        $pdf->SetFont("Arial","B",8);
        $legendX = $chartX + $chartW + 6;
        $legendY = $chartY + 2;

        $pdf->SetXY($legendX, $legendY);
        $pdf->Cell(20,5,"Legend",0,1);

        foreach ($clientNames as $i => $cl) {
            list($r,$g,$b) = pickColor($i);
            $pdf->SetFillColor($r,$g,$b);

            $pdf->SetXY($legendX, $legendY + 6 + ($i * 6));
            $pdf->Rect($pdf->GetX(), $pdf->GetY(), 4, 4, "F");

            $pdf->SetXY($legendX + 6, $legendY + 5 + ($i * 6));
            $pdf->SetFont("Arial","",7);
            $pdf->Cell(22,5,$cl);
        }

        $pdf->SetY($chartY + $chartH + 14);
    } else {
        $pdf->SetFont("Arial","B",10);
        $pdf->SetFillColor(245,245,245);
        $pdf->Cell(0,10,"No daily client activity to display in graph.",1,1,'C',true);
        $pdf->Ln(5);
    }
}
// Datos desde tu función existente
$clientes_res = resumen_cliente($start_str,$end_str);

if (!empty($clientes_res)) {

    $pdf->SubTitle("Graph: Client Completion Percentage");

    ensure_space($pdf, 85);

    // Preparar puntos (% completado)
    $points = [];
    foreach ($clientes_res as $c){
        $sol = (int)$c['solicitados'];
        $ent = (int)$c['entregados'];
        $pct = ($sol > 0) ? round(($ent * 100) / $sol) : 0;

        $points[] = [
            "label" => $c['Client'],
            "pct"   => $pct
        ];
    }

    // Área del gráfico
    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartW = 150;
    $chartH = 55;

    // Ejes
    $pdf->SetDrawColor(0,0,0);
    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);              // Y
    $pdf->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH);  // X

    // Y-axis labels 0-100%
    $pdf->SetFont('Arial','',7);
    $steps = 4;
    for ($i=0; $i <= $steps; $i++){
        $yPos = $chartY + $chartH - ($chartH / $steps * $i);
        $val  = round(100 / $steps * $i);
        $pdf->SetXY($chartX - 10, $yPos - 2);
        $pdf->Cell(8,4,$val,0,0,'R');
    }

    // Barras
    $bars = count($points);
    $bw   = ($chartW - 20) / max($bars,1);
    if ($bw < 10) $bw = 10;  // ancho mínimo

    $x = $chartX + 10;

    foreach ($points as $i => $p){

        $pct = $p['pct'];
        $h   = ($pct / 100) * ($chartH - 4);
        $y   = $chartY + ($chartH - $h);

        // Color consistente
        list($r,$g,$b) = pickColor($i);
        $pdf->SetFillColor($r,$g,$b);

        // Barra
        $pdf->Rect($x, $y, $bw, $h, "F");

        // Valor porcentaje encima
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY($x, $y - 4);
        $pdf->Cell($bw,4,$pct."%",0,0,'C');

        // Label cliente en X
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($x, $chartY + $chartH + 2);
        $pdf->MultiCell($bw,4,$p["label"],0,'C');

        $x += $bw;
    }

    // Leyenda vertical derecha
    $legendX = $chartX + $chartW + 6;
    $legendY = $chartY;

    $pdf->SetFont('Arial','B',8);
    $pdf->SetXY($legendX, $legendY);
    $pdf->Cell(20,4,"Legend",0,1);

    foreach ($points as $i => $p){
        list($r,$g,$b) = pickColor($i);
        $pdf->SetFillColor($r,$g,$b);

        $pdf->SetXY($legendX, $legendY + 6 + ($i * 6));
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 4, 4, "F");

        $pdf->SetXY($legendX + 6, $legendY + 5 + ($i * 6));
        $pdf->SetFont("Arial","",7);
        $pdf->Cell(30,5, $p['label'],0,1,'L');
    }

    $pdf->SetY($chartY + $chartH + 16);
}

/* ===============================
   SECCIÓN 4 — TEST DISTRIBUTION BY TYPE AND CLIENT
================================*/
$pdf->section_title("4. Test Distribution by Type and Client");

/* ---------- Construir matriz Tipo × Cliente ---------- */

$rowsTipo = resumen_tipo_entregado($start_str,$end_str);
$matrix4  = [];
$clients4 = [];

foreach ($rowsTipo as $r){

    $client = trim((string)$r['Client']);
    if ($client === '') $client = 'N/A';

    $testsRaw = (string)$r['Test_Type'];
    $testsArr = array_filter(array_map('trim', explode(',', $testsRaw)));

    foreach ($testsArr as $t){
        if ($t === '') continue;

        if (!isset($matrix4[$t])) $matrix4[$t] = [];
        if (!isset($matrix4[$t][$client])) $matrix4[$t][$client] = 0;

        $matrix4[$t][$client]++;

        if (!in_array($client,$clients4,true)) {
            $clients4[] = $client;
        }
    }
}

if (empty($matrix4)) {

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No completed tests in this week.",1,1,'C',true);
    $pdf->Ln(5);

} else {

    sort($clients4);
    $testTypes = array_keys($matrix4);
    sort($testTypes);

    // ---------- TABLA TIPO × CLIENTE (B + D MIX) ----------
    $pdf->SubTitle("Completed Tests by Type and Client");

    $colWidths = [40];
    $numClients4 = count($clients4);
    $restWidth = 190 - 40; // ancho útil aprox
    $wClient = $numClients4 > 0 ? max(20, floor($restWidth / $numClients4)) : 20;

    foreach ($clients4 as $cl) {
        $colWidths[] = $wClient;
    }

    $header = ["Test Type"];
    foreach ($clients4 as $cl) {
        $header[] = $cl;
    }

    $pdf->table_header($header, $colWidths);

    foreach ($testTypes as $tp){
        $row = [$tp];
        foreach ($clients4 as $cl){
            $row[] = $matrix4[$tp][$cl] ?? 0;
        }
        $pdf->table_row($row, $colWidths);
    }

    $pdf->Ln(6);

    /* ---------- GRÁFICO: TYPE × CLIENT ---------- */
    $pdf->SubTitle("Graph: Tests by Type and Client");

    // Verificamos datos
    $hasData4 = false;
    $maxVal4  = 0;
    foreach ($testTypes as $tp){
        foreach ($clients4 as $cl){
            $v = $matrix4[$tp][$cl] ?? 0;
            if ($v > 0) $hasData4 = true;
            if ($v > $maxVal4) $maxVal4 = $v;
        }
    }

    if ($hasData4) {

        ensure_space($pdf, 95);

        $chartX = 20;
        $chartY = $pdf->GetY() + 5;
        $chartW = 150;
        $chartH = 45;

        // Ejes
        $pdf->SetDrawColor(0,0,0);
        $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);
        $pdf->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH);

        if ($maxVal4 <= 0) $maxVal4 = 1;

        // Escala eje Y
        $pdf->SetFont("Arial","",7);
        $steps = 4;
        for ($i=0; $i <= $steps; $i++){
            $yPos = $chartY + $chartH - ($chartH/$steps * $i);
            $val  = round($maxVal4/$steps * $i);
            $pdf->SetXY($chartX - 8, $yPos - 2);
            $pdf->Cell(7, 4, $val, 0, 0, 'R');
        }

        // Cálculo de barras
        $barsPerType = count($clients4);
        $totalBars   = $barsPerType * count($testTypes);
        if ($totalBars <= 0) $totalBars = 1;

        $bw = ($chartW - 20) / $totalBars;
        if ($bw <= 0) $bw = 1;

        $x = $chartX + 10;

        foreach ($testTypes as $tp){
            foreach ($clients4 as $cIndex => $cl){

                $v = $matrix4[$tp][$cl] ?? 0;
                $h = ($v / $maxVal4) * ($chartH - 4);
                $y = $chartY + ($chartH - $h);

                // color único por cliente
                list($r,$g,$b) = pickColor($cIndex);
                $pdf->SetFillColor($r,$g,$b);

                $pdf->Rect($x, $y, $bw, $h, "F");

                if ($v > 0){
                    $pdf->SetFont("Arial","B",7);
                    $pdf->SetXY($x, $y - 4);
                    $pdf->Cell($bw, 4, $v, 0, 0, 'C');
                }

                $x += $bw;
            }
        }

        // Labels eje X (test types)
        $pdf->SetFont("Arial","",7);
        $x = $chartX + 10;
        $groupW = $bw * $barsPerType;

        foreach ($testTypes as $tp){
            $pdf->SetXY($x, $chartY + $chartH + 2);
            $pdf->MultiCell($groupW, 4, $tp, 0, 'C');
            $x += $groupW;
        }

        // LEYENDA VERTICAL DERECHA (CLIENTES)
        $pdf->SetFont("Arial","B",8);
        $legendX = $chartX + $chartW + 6;
        $legendY = $chartY + 2;

        $pdf->SetXY($legendX, $legendY);
        $pdf->Cell(20,5,"Legend",0,1);

        foreach ($clients4 as $i => $cl) {
            list($r,$g,$b) = pickColor($i);
            $pdf->SetFillColor($r,$g,$b);

            $pdf->SetXY($legendX, $legendY + 6 + ($i * 6));
            $pdf->Rect($pdf->GetX(), $pdf->GetY(), 4, 4, "F");

            $pdf->SetXY($legendX + 6, $legendY + 5 + ($i * 6));
            $pdf->SetFont("Arial","",7);
            $pdf->Cell(22,5,$cl);
        }

        $pdf->SetY($chartY + $chartH + 14);

    } else {
        $pdf->SetFont("Arial","B",10);
        $pdf->SetFillColor(245,245,245);
        $pdf->Cell(0,10,"No data to display in type-client graph.",1,1,'C',true);
        $pdf->Ln(5);
    }
}

/* ===============================
   5. Newly Registered Samples (Weekly)
================================*/
$pdf->section_title("5. Newly Registered Samples (Weekly)");

$muestras = find_by_sql("
    SELECT 
        Sample_ID,
        Sample_Number,
        Client,
        Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

if (empty($muestras)) {

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No samples were registered during this week.",1,1,'C',true);
    $pdf->Ln(5);

} else {

    // ===========================
    // 1) PREPARE MATRICES
    // ===========================
    $totalSamplesWeek = count($muestras);

    $clientsMap     = []; // muestras por cliente
    $testTypeTotals = []; // total de ensayos por tipo
    $matrix5        = []; // matriz tipo × cliente

    foreach ($muestras as $row) {

        $client = trim((string)$row['Client']);
        if ($client === '') $client = 'N/A';

        // Contar muestras por cliente
        if (!isset($clientsMap[$client])) $clientsMap[$client] = 0;
        $clientsMap[$client]++;

        // Separar test types si vienen "GS, SP, LAA"
        $testsRaw = (string)$row['Test_Type'];
        $testsArr = array_filter(array_map('trim', explode(',', $testsRaw)));

        foreach ($testsArr as $t) {
            if ($t === '') continue;

            if (!isset($testTypeTotals[$t])) $testTypeTotals[$t] = 0;
            $testTypeTotals[$t]++;

            if (!isset($matrix5[$t])) $matrix5[$t] = [];
            if (!isset($matrix5[$t][$client])) $matrix5[$t][$client] = 0;

            $matrix5[$t][$client]++;
        }
    }

    // ===========================
    // 2) SUMMARY CARDS
    // ===========================
    // Top client
    $topClientName  = "-";
    $topClientCount = 0;
    foreach ($clientsMap as $c => $cnt) {
        if ($cnt > $topClientCount) {
            $topClientCount = $cnt;
            $topClientName  = $c;
        }
    }

    // Top test type
    $topTestName  = "-";
    $topTestCount = 0;
    foreach ($testTypeTotals as $t => $cnt) {
        if ($cnt > $topTestCount) {
            $topTestCount = $cnt;
            $topTestName  = $t;
        }
    }

    // ---- Tarjetas estilo dashboard ----
    $boxW = 95;

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(230,230,230);

    // Primera fila: total samples + top client
    $pdf->Cell($boxW,6,"Total Samples this Week",1,0,'L',true);
    $pdf->Cell($boxW,6,"Top Client this Week",1,1,'L',true);

    $pdf->SetFont('Arial','',11);
    $pdf->Cell($boxW,8,$totalSamplesWeek,1,0,'C');

    $txtTopClient = ($topClientName === "-")
        ? "-"
        : ($topClientName." - ".$topClientCount." samples");
    $pdf->Cell($boxW,8,utf8_decode($txtTopClient),1,1,'C');

    // Segunda fila — full width
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($boxW*2,6,"Top Test Type this Week",1,1,'L',true);

    $pdf->SetFont('Arial','',11);
    $txtTopTest = ($topTestName === "-")
        ? "-"
        : ($topTestName." - ".$topTestCount." tests");
    $pdf->Cell($boxW*2,8,utf8_decode($txtTopTest),1,1,'C');

    $pdf->Ln(10);

    // ===========================
    // 3) TABLA TIPO × CLIENTE (OPCIÓN B + D)
    // ===========================
    $pdf->SubTitle("Test Type vs Client (Registered This Week)");

    $clients5 = array_keys($clientsMap);
    sort($clients5);

    $types5 = array_keys($matrix5);
    sort($types5);

    if (empty($types5) || empty($clients5)) {

        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(245,245,245);
        $pdf->Cell(0,10,"No test types available for this week.",1,1,'C',true);
        $pdf->Ln(5);

    } else {

        // anchos de columnas
        $colWidths = [40]; // columna de Test Type
        $numClients = count($clients5);
        $restWidth = 190 - 40;
        $wClient = max(22, floor($restWidth / $numClients));

        foreach ($clients5 as $c) {
            $colWidths[] = $wClient;
        }

        // encabezado
        $header = ["Test Type"];
        foreach ($clients5 as $c) $header[] = $c;

        $pdf->table_header($header,$colWidths);

        // filas
        foreach ($types5 as $tp) {
            $row = [$tp];
            foreach ($clients5 as $cl) {
                $row[] = $matrix5[$tp][$cl] ?? 0;
            }
            $pdf->table_row($row,$colWidths);
        }

        $pdf->Ln(10);
    }
}

/* ===============================
   SECCIÓN 6 — SUMMARY OF TESTS BY TECHNICIAN
================================*/
$pdf->section_title("6. Summary of Tests by Technician");

/* Cargar mapa de alias → nombre */
list($aliasMap, $firstLetterName) = loadTechnicianAliasMap();

/* Traer datos crudos por técnico (alias) */
$tecRaw = find_by_sql("
    SELECT Technician, COUNT(*) total, 'In Preparation' etapa
    FROM test_preparation
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND TRIM(IFNULL(Technician,'')) <> ''
    GROUP BY Technician

    UNION ALL

    SELECT Technician, COUNT(*) total, 'In Realization' etapa
    FROM test_realization
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND TRIM(IFNULL(Technician,'')) <> ''
    GROUP BY Technician

    UNION ALL

    SELECT Technician, COUNT(*) total, 'Completed' etapa
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND TRIM(IFNULL(Technician,'')) <> ''
    GROUP BY Technician
");

/* Aggregar por nombre "real" */
$techSummary = []; // [name][etapa] = total

foreach ($tecRaw as $row){

    $rawTech = $row['Technician'];
    $count   = (int)$row['total'];
    $stage   = $row['etapa'];

    $resolved = resolveTechnician($aliasMap, $firstLetterName, $rawTech);

    if ($resolved === null) {
        continue; // técnico desconocido -> no se incluye
    }

    // resolveTechnician devuelve string "A, B, C"
    $names = array_filter(array_map('trim', explode(',', $resolved)));

    foreach ($names as $name){
        if ($name === '') continue;

        if (!isset($techSummary[$name])) {
            $techSummary[$name] = [
                'In Preparation' => 0,
                'In Realization' => 0,
                'Completed'      => 0
            ];
        }

        if (!isset($techSummary[$name][$stage])) {
            $techSummary[$name][$stage] = 0;
        }

        $techSummary[$name][$stage] += $count;
    }
}

if (empty($techSummary)) {

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No technician activity recorded for this week.",1,1,'C',true);
    $pdf->Ln(5);

} else {

    ksort($techSummary);

    $pdf->table_header(["Technician","In Preparation","In Realization","Completed","Total"],[60,30,30,30,30]);

    foreach ($techSummary as $name => $stages){

        $prep = (int)($stages['In Preparation'] ?? 0);
        $real = (int)($stages['In Realization'] ?? 0);
        $comp = (int)($stages['Completed']      ?? 0);
        $total = $prep + $real + $comp;

        $pdf->table_row([
            $name,
            $prep,
            $real,
            $comp,
            $total
        ], [60,30,30,30,30]);
    }

    $pdf->Ln(8);
}

/* ===============================
   SECCIÓN 7 — TEST TYPE × CLIENT × PROCESS
   TABLA DINÁMICA (MULTI-PÁGINAS)
================================*/

$pdf->section_title("7. Summary of Tests by Type and Client (Dynamic Width)");

/* ===============================
   1. Cargar datos crudos
================================*/
$raw7 = find_by_sql("
    SELECT r.Client, p.Test_Type, 'Prep' etapa, COUNT(*) total
    FROM test_preparation p
    LEFT JOIN lab_test_requisition_form r
      ON p.Sample_ID = r.Sample_ID
     AND p.Sample_Number = r.Sample_Number
     AND p.Test_Type = r.Test_Type
    WHERE p.Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY r.Client, p.Test_Type

    UNION ALL

    SELECT r.Client, z.Test_Type, 'Real' etapa, COUNT(*) total
    FROM test_realization z
    LEFT JOIN lab_test_requisition_form r
      ON z.Sample_ID = r.Sample_ID
     AND z.Sample_Number = r.Sample_Number
     AND z.Test_Type = r.Test_Type
    WHERE z.Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY r.Client, z.Test_Type

    UNION ALL

    SELECT r.Client, d.Test_Type, 'Del' etapa, COUNT(*) total
    FROM test_delivery d
    LEFT JOIN lab_test_requisition_form r
      ON d.Sample_ID = r.Sample_ID
     AND d.Sample_Number = r.Sample_Number
     AND d.Test_Type = r.Test_Type
    WHERE d.Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY r.Client, d.Test_Type
");

/* ===============================
   2. Construir matriz
================================*/
$matrix7 = [];
$clientes7 = [];
$tipos7 = [];

foreach ($raw7 as $r){
    $client = trim($r['Client']) ?: "N/A";
    $test   = trim($r['Test_Type']);
    if ($test === "") continue;

    $etapa  = $r['etapa'];
    $cnt    = (int)$r['total'];

    if (!isset($matrix7[$test])) $matrix7[$test] = [];
    if (!isset($matrix7[$test][$client]))
        $matrix7[$test][$client] = ['Prep'=>0,'Real'=>0,'Del'=>0];

    $matrix7[$test][$client][$etapa] += $cnt;

    if (!in_array($client,$clientes7,true)) $clientes7[] = $client;
    if (!in_array($test,$tipos7,true))      $tipos7[] = $test;
}

sort($clientes7);
sort($tipos7);

/* ===============================
   3. CONFIGURAR ANCHOS DINÁMICOS
================================*/
$wTest = 35;
$wSub  = 18; // Prep / Real / Del
$usableWidth = 190 - $wTest;

// cuántos clientes caben por página
$clientBlockCapacity = floor($usableWidth / ($wSub * 3));
if ($clientBlockCapacity < 1) $clientBlockCapacity = 1;

// dividir clientes en bloques
$clientBlocks = array_chunk($clientes7, $clientBlockCapacity);

/* ===============================
   4. PINTAR TABLAS POR BLOQUES
================================*/
foreach ($clientBlocks as $blockIndex => $blockClients){

    if ($blockIndex > 0){
        $pdf->AddPage();
        $pdf->section_title("7. Summary of Tests by Type and Client (cont.)");
    }

    // ENCABEZADO LÍNEA 1
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($wTest, 10, "Test Type", 1, 0, 'C');

    foreach ($blockClients as $c){
        $pdf->Cell($wSub*3, 10, utf8_decode($c), 1, 0, 'C');
    }
    $pdf->Ln();

    // ENCABEZADO LÍNEA 2
    $pdf->Cell($wTest, 7, "", 1, 0, 'C');
    foreach ($blockClients as $c){
        $pdf->SetFont('Arial','',9);
        $pdf->Cell($wSub,7,"Prep",1,0,'C');
        $pdf->Cell($wSub,7,"Real",1,0,'C');
        $pdf->Cell($wSub,7,"Comp",1,0,'C');
    }
    $pdf->Ln();

    // FILAS
    $pdf->SetFont('Arial','',10);
    foreach ($tipos7 as $tp){

        $pdf->Cell($wTest, 7, utf8_decode($tp), 1, 0, 'L');

        foreach ($blockClients as $cl){

            $prep = $matrix7[$tp][$cl]['Prep'] ?? 0;
            $real = $matrix7[$tp][$cl]['Real'] ?? 0;
            $del  = $matrix7[$tp][$cl]['Del']  ?? 0;

            $pdf->Cell($wSub,7,$prep,1,0,'C');
            $pdf->Cell($wSub,7,$real,1,0,'C');
            $pdf->Cell($wSub,7,$del,1,0,'C');
        }

        $pdf->Ln();
    }

    $pdf->Ln(8);
}

/* ===============================
   SECCIÓN 8 — PENDING TESTS
================================*/
$pdf->section_title("8. Pending Tests");

$pendRaw = find_by_sql("
    SELECT 
        r.Sample_ID,
        r.Sample_Number,
        r.Client,
        r.Test_Type
    FROM lab_test_requisition_form r
    WHERE r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'

      /* NO en Preparation */
      AND NOT EXISTS (
          SELECT 1 FROM test_preparation p
          WHERE p.Sample_ID     = r.Sample_ID
            AND p.Sample_Number = r.Sample_Number
            AND p.Test_Type     = r.Test_Type
      )

      /* NO en Realization */
      AND NOT EXISTS (
          SELECT 1 FROM test_realization z
          WHERE z.Sample_ID     = r.Sample_ID
            AND z.Sample_Number = r.Sample_Number
            AND z.Test_Type     = r.Test_Type
      )

      /* NO en Delivery */
      AND NOT EXISTS (
          SELECT 1 FROM test_delivery d
          WHERE d.Sample_ID     = r.Sample_ID
            AND d.Sample_Number = r.Sample_Number
            AND d.Test_Type     = r.Test_Type
      )
");


/* Si NO hay pendientes */
if (empty($pendRaw)) {
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No pending tests for this week.",1,1,'C',true);
    $pdf->Ln(5);
    goto skip_pending;
}

/* =====================================================
   2) Procesar Test Types individuales
===================================================== */
$pending = [];     // pending[testType][client] = qty
$clients = [];     // lista única de clientes
$typeTotals = [];  // total qty por test type
$totalPending = 0; // total general

foreach ($pendRaw as $r) {

    $client = trim((string)$r['Client']);
    if ($client === "") $client = "N/A";

    if (!in_array($client, $clients, true)) {
        $clients[] = $client;
    }

    /* Separar test types individuales */
    $tests = array_filter(array_map('trim', explode(',', (string)$r['Test_Type'])));

    foreach ($tests as $t) {
        if ($t === "") continue;

        if (!isset($pending[$t])) $pending[$t] = [];
        if (!isset($pending[$t][$client])) $pending[$t][$client] = 0;

        $pending[$t][$client]++;
        $totalPending++;

        if (!isset($typeTotals[$t])) $typeTotals[$t] = 0;
        $typeTotals[$t]++;
    }
}

sort($clients);
ksort($pending);

/* =====================================================
   3) Determinar cantidad de clientes por bloque
===================================================== */
$maxColsPerBlock = 4; // igual que sección 7
$totalClients    = count($clients);
$blocks = ceil($totalClients / $maxColsPerBlock);

for ($b = 0; $b < $blocks; $b++) {

    $start = $b * $maxColsPerBlock;
    $slice = array_slice($clients, $start, $maxColsPerBlock);
    $end   = $start + count($slice);

    /* Subtítulo por bloque */
    $pdf->SubTitle("Pending Tests (Clients ".($start+1)."to".$end.")");

    /* =====================================================
       4) Construir encabezado doble
    ====================================================== */

    /* Anchos dinámicos */
    $wType  = 35;
    $wQty   = 12;
    $wPct   = 14;
    $wTotal = 20;

    /* Primera fila del encabezado */
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($wType, 10, "Test Type", 1, 0, 'C');

    foreach ($slice as $c) {
        $pdf->Cell($wQty + $wPct, 10, utf8_decode($c), 1, 0, 'C');
    }

    /* TOTAL al final */
    $pdf->Cell($wTotal + $wPct, 10, "TOTAL", 1, 1, 'C');

    /* Segunda fila: subcolumnas */
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell($wType, 6, "", 1, 0, 'C');

    foreach ($slice as $c) {
        $pdf->Cell($wQty, 6, "Qty", 1, 0, 'C');
        $pdf->Cell($wPct, 6, "%Pend", 1, 0, 'C');
    }

    $pdf->Cell($wTotal, 6, "Qty", 1, 0, 'C');
    $pdf->Cell($wPct,   6, "%Pend", 1, 1, 'C');

    /* =====================================================
       5)   Filas por Test Type
    ====================================================== */
    $pdf->SetFont('Arial','',9);

    foreach ($pending as $type => $row) {

        /* Control de salto de página */
        if ($pdf->GetY() > 255) {
            $pdf->AddPage();
        }

        $pdf->Cell($wType, 6, $type, 1, 0, 'L');

        $sumType = $typeTotals[$type];

        foreach ($slice as $cl) {

            $qty = $row[$cl] ?? 0;
            $pct = ($sumType > 0) ? round(($qty * 100) / $sumType) : 0;

            $pdf->Cell($wQty, 6, $qty, 1, 0, 'C');
            $pdf->Cell($wPct, 6, $pct."%", 1, 0, 'C');
        }

        /* TOTAL de la fila (Qty + %) */
        $totalQty = $sumType;
        $totalPct = ($totalPending > 0) ? round(($totalQty * 100) / $totalPending) : 0;

        $pdf->Cell($wTotal, 6, $totalQty, 1, 0, 'C');
        $pdf->Cell($wPct,   6, $totalPct."%", 1, 1, 'C');
    }

    /* =====================================================
       6) Fila final TOTAL por cliente dentro del bloque
    ====================================================== */
    $pdf->SetFont('Arial','B',9);

    $pdf->Cell($wType, 7, "TOTAL", 1, 0, 'C');

    foreach ($slice as $cl) {

        /* total de ese cliente */
        $qtyCl = 0;
        foreach ($pending as $tp => $rw) {
            $qtyCl += ($rw[$cl] ?? 0);
        }

        $pctCl = ($totalPending > 0) ? round(($qtyCl * 100) / $totalPending) : 0;

        $pdf->Cell($wQty, 7, $qtyCl, 1, 0, 'C');
        $pdf->Cell($wPct, 7, $pctCl."%", 1, 0, 'C');
    }

    /* TOTAL global */
    $pdf->Cell($wTotal, 7, $totalPending, 1, 0, 'C');
    $pdf->Cell($wPct,   7, "100%", 1, 1, 'C');

    $pdf->Ln(6);
}

skip_pending:

/* ===============================
   SECCIÓN 9 — SUMMARY OF DAM CONSTRUCTION TESTS
================================*/

/*
 REGLA DE CLASIFICACIÓN (ESTRUCTURA):
 -------------------------------------
 - Stockpile:
      a) Structure contiene 'STOCK'
      b) ó Sample_ID comienza con 'PVDJ-AGG'
 - Borrow:
      a) Sample_ID comienza con 'LBOR'
 - De lo contrario se usa la columna Structure
*/

function classifyStructure($sampleId, $structure){
    $sid = strtoupper(trim($sampleId));
    $st  = strtoupper(trim($structure));

    if (str_starts_with($sid, "PVDJ-AGG")) return "STOCKPILE";
    if (str_contains($st, "STOCK")) return "STOCKPILE";

    if (str_starts_with($sid, "LBOR")) return "BORROW";

    return ($st === "") ? "UNKNOWN" : $st;
}

/* ============================================================
   1. CARGAR DATOS BRUTOS DE LA SEMANA
==============================================================*/
$rows = find_by_sql("
    SELECT *
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$pdf->section_title("9. Summary of Dam Construction Tests");

if (empty($rows)) {

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No dam construction tests reported this week.",1,1,'C',true);
    $pdf->Ln(5);

} else {

    /* ============================================================
       2. ARMAR MATRIZ ESTRUCTURA → MATERIAL → TEST → PASSED/FAILED
    ===============================================================*/
    $matrix = [];

    foreach ($rows as $r) {

        $structure = classifyStructure($r['Sample_ID'], $r['Structure']);
        $material   = strtoupper(trim((string)$r['Material_Type']));
        if ($material === "") $material = "OTHER";

        $testType   = strtoupper(trim((string)$r['Test_Type']));
        if ($testType === "") $testType = "UNKNOWN";

        $comment = strtoupper((string)$r['Comments']);
        $cond    = strtoupper((string)$r['Test_Condition']);
        $ncr     = strtoupper((string)$r['Noconformidad']);

        /* REGLA PARA FAIL */
        $isFail =
            str_contains($comment,"FAIL") ||
            str_contains($comment,"NO CUMPLE") ||
            str_contains($comment,"RECHAZ") ||
            str_contains($comment,"NCR") ||
            str_contains($ncr,"NCR") ||
            str_contains($cond,"NOT OK");

        if (!isset($matrix[$structure])) $matrix[$structure] = [];
        if (!isset($matrix[$structure][$material])) $matrix[$structure][$material] = [];
        if (!isset($matrix[$structure][$material][$testType])) {
            $matrix[$structure][$material][$testType] = [
                "passed" => 0,
                "failed" => 0
            ];
        }

        if ($isFail) $matrix[$structure][$material][$testType]["failed"]++;
        else         $matrix[$structure][$material][$testType]["passed"]++;
    }

    /* ============================================================
       3. TABLA EJECUTIVA (DOBLE FILA DE ENCABEZADO)
    ===============================================================*/

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"9.1 Summary Table",0,1);
    $pdf->Ln(2);

    // --- FILA 1 DEL ENCABEZADO ---
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(220,220,220);

    $pdf->Cell(40,7,"Structure",1,0,'C',true);
    $pdf->Cell(35,7,"Material",1,0,'C',true);
    $pdf->Cell(30,7,"Test Type",1,0,'C',true);

    $pdf->Cell(44,7,"RESULTS",1,1,'C',true);  // grupo de dos columnas

    // --- FILA 2 DEL ENCABEZADO ---
    $pdf->Cell(40,6,"",1,0);
    $pdf->Cell(35,6,"",1,0);
    $pdf->Cell(30,6,"",1,0);

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(22,6,"Passed",1,0,'C',true);
    $pdf->Cell(22,6,"Failed",1,1,'C',true);

    /* ---------- FILAS ---------- */
    $pdf->SetFont('Arial','',9);

    foreach ($matrix as $structure => $matData) {

        foreach ($matData as $material => $testData) {

            foreach ($testData as $testType => $info) {

                $passed = $info["passed"];
                $failed = $info["failed"];

                if ($passed==0 && $failed==0) continue;

                $pdf->Cell(40,6,$structure,1);
                $pdf->Cell(35,6,$material,1);
                $pdf->Cell(30,6,$testType,1);

                $pdf->Cell(22,6,$passed,1,0,'C');
                $pdf->Cell(22,6,$failed,1,1,'C');
            }
        }
    }

    $pdf->Ln(8);
}

skip_section_9:


/* ===============================
   SECCIÓN 10 — OBSERVATIONS & NCR
================================*/
$pdf->section_title("10. Observations & Non-Conformities");

$ncr = find_by_sql("
    SELECT *
    FROM ensayos_reporte
    WHERE Noconformidad IS NOT NULL
      AND TRIM(IFNULL(Noconformidad,'')) <> ''
      AND Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

if (empty($ncr)) {

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No non-conformities were reported this week.",1,1,'C',true);
    $pdf->Ln(5);

} else {

    $pdf->table_header(["Sample","Observations"],[40,150]);

    foreach($ncr as $n){
        $sampleIdText = $n['Sample_ID']."-".$n['Sample_Number'];
        if (!empty($n['Material_Type'])) {
            $sampleIdText .= "-".$n['Material_Type'];
        }

        table_row_multiline($pdf,[
            $sampleIdText,
            $n['Noconformidad']
        ], [40,150]);
    }

    $pdf->Ln(8);
}

/* ===============================
   SECCIÓN 11 — RESPONSIBLE
================================*/
$pdf->section_title("11. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(60,8,"Report prepared by",1);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Weekly_Lab_Report_Week{$week}_{$year}.pdf");
?>
