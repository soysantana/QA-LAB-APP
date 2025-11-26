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
   3. CONSULTAS
================================*/
function resumen_diario($start, $end){
    return find_by_sql("
        SELECT DATE(Registed_Date) AS dia, COUNT(*) AS total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY DATE(Registed_Date)
        ORDER BY dia ASC
    ");
}

function resumen_tipo($start,$end){
    return find_by_sql("
        SELECT UPPER(TRIM(Test_Type)) AS Test_Type,
               COUNT(*) AS total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type
    ");
}

function resumen_cliente($start,$end){
    return find_by_sql("
        SELECT 
          UPPER(TRIM(r.Client)) AS Client,
          COUNT(*) AS solicitados,
          SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) AS entregados
        FROM lab_test_requisition_form r
        LEFT JOIN test_delivery d
        ON r.Sample_ID=d.Sample_ID
        AND r.Sample_Number=d.Sample_Number
        AND r.Test_Type=d.Test_Type
        WHERE r.Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY r.Client
        ORDER BY solicitados DESC
    ");
}

/* ======================================================
   NUEVO: RESUMEN DIARIO POR CLIENTE (SEMANA ISO)
======================================================*/
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

$data_dia_cliente = resumen_diario_cliente($start_str, $end_str);

/*******************************************************
   CREAR MATRIZ DIA × CLIENTE
*******************************************************/
$matriz = [];
$diasSemana = [];
$clientes = [];

foreach ($data_dia_cliente as $r){
    $d = $r['dia'];
    $c = trim($r['Client']);
    $v = (int)$r['total'];

    if (!in_array($d, $diasSemana)) $diasSemana[] = $d;
    if (!in_array($c, $clientes)) $clientes[] = $c;

    $matriz[$d][$c] = $v;
}

sort($diasSemana);
sort($clientes);

/* Completar ceros */
foreach ($diasSemana as $d){
    foreach ($clientes as $c){
        if (!isset($matriz[$d][$c])) $matriz[$d][$c] = 0;
    }
}

/*******************************************************
   FUNCIONES DE TABLA Y SALTOS DE PÁGINA
*******************************************************/
function table_row_multiline($pdf, $data, $w){
    $pdf->SetFont('Arial','',9);
    $maxHeight = 5;

    foreach($data as $i => $txt){
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i] - 2, 1);
        $h  = max(ceil($nb) * 5, 7);
        if($h > $maxHeight) $maxHeight = $h;
    }

    if ($pdf->GetY() + $maxHeight > $pdf->getPageBreakTrigger()){
        $pdf->AddPage();
        if($pdf->current_table_header){
            $pdf->table_header(
                $pdf->current_table_header['cols'],
                $pdf->current_table_header['widths']
            );
        }
    }

    foreach($data as $i=>$txt){
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($w[$i],5,utf8_decode($txt),1,'L');
        $pdf->SetXY($x + $w[$i], $y);
    }

    $pdf->Ln($maxHeight);
}

/*******************************************************
   EVITAR QUE EL GRÁFICO SE PARTA
*******************************************************/
function ensure_space($pdf, $neededHeight = 80){
    if ($pdf->GetY() + $neededHeight > 260) {
        $pdf->AddPage();
    }
}

/*******************************************************
   PDF CLASS
*******************************************************/
class PDF_WEEKLY extends FPDF {

    public $week; 
    public $year;
    public $current_table_header = null;

    function __construct($week,$year){
        parent::__construct();
        $this->week = $week;
        $this->year = $year;
    }

    function getPageBreakTrigger() {
        return $this->PageBreakTrigger;
    }

    function Header() {
        if ($this->PageNo() > 1) return;

        if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
            $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 55);
        }

        $this->SetFont('Arial','B',16);
        $this->SetXY(120,12);
        $this->Cell(80,8,'WEEKLY LABORATORY REPORT',0,1,'R');

        $start = new DateTime();
        $start->setISODate($this->year,$this->week,1);
        $end = new DateTime();
        $end->setISODate($this->year,$this->week,7);

        $this->SetFont('Arial','',11);
        $this->SetXY(120,22);
        $this->Cell(80,7,"ISO WEEK {$this->week} ( ".$start->format('d M Y')." - ".$end->format('d M Y')." )",0,1,'R');

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

    function table_header($cols,$w){
        $this->SetFont('Arial','B',10);
        foreach($cols as $i=>$c){
            $this->Cell($w[$i],7,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
        $this->current_table_header = [
            'cols'=>$cols,
            'widths'=>$w
        ];
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',10);
        foreach($data as $i=>$d){
            $this->Cell($w[$i],7,utf8_decode($d),1,0,'C');
        }
        $this->Ln();
    }

    function SubTitle($txt){
        $this->SetFont('Arial','B',11);
        $this->Cell(0,7,utf8_decode($txt),0,1,'L');
        $this->Ln(2);
    }
}

$pdf = new PDF_WEEKLY($week,$year);
$pdf->AddPage();
/* ===============================
   PALETA DE COLORES PARA GRÁFICOS
================================*/
function pickColor($i){
    $colors = [
        [66,133,244],   // Azul
        [219,68,55],    // Rojo
        [244,180,0],    // Amarillo
        [15,157,88],    // Verde
        [171,71,188],   // Morado
        [0,172,193],    // Cyan
        [255,112,67]    // Naranja
    ];
    return $colors[$i % count($colors)];
}

/* ===============================
   6. GRÁFICOS COMPLETOS
================================*/

/* ---------- GRAPH 1 ---------- */
function chart_samples($pdf,$data){
    if(empty($data)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 1: Samples Registered Per Day",0,1);

    ensure_space($pdf, 80);

    $chartX = 20;
    $chartY = $pdf->GetY()+5;
    $chartW = 170;
    $chartH = 50;

    $max = 1;
    foreach($data as $d){
        if($d['total'] > $max) $max = $d['total'];
    }

    // Ejes
    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $bars = count($data);
    if ($bars == 0) return;

    $bw = ($chartW-20)/$bars;
    $x = $chartX+10;

    foreach($data as $d){

        $h = ($d['total'] / $max) * ($chartH - 5);
        $y = $chartY + ($chartH - $h);

        $pdf->SetFillColor(100,149,237);
        $pdf->Rect($x, $y, $bw-4, $h, "F");

        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($x, $chartY + $chartH + 2);
        $pdf->MultiCell($bw-4, 3, date("D",strtotime($d['dia'])), 0, 'C');

        $x += $bw;
    }

    $pdf->Ln(10);
}


/* ---------- GRAPH 2 ---------- */
function chart_types($pdf,$data){
    if(empty($data)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 2: Tests Completed By Type",0,1);

    ensure_space($pdf, 80);

    $chartX = 20;
    $chartY = $pdf->GetY()+5;
    $chartW = 170;
    $chartH = 50;

    $max = 1;
    foreach($data as $t){
        if($t['total'] > $max) $max = $t['total'];
    }

    // ejes
    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $bars = count($data);
    if ($bars == 0) return;

    $bw = ($chartW-20)/$bars;
    $x = $chartX+10;

    foreach($data as $t){

        $h = ($t['total'] / $max) * ($chartH - 5);
        $y = $chartY + ($chartH - $h);

        $pdf->SetFillColor(50,180,120);
        $pdf->Rect($x, $y, $bw-4, $h, "F");

        $pdf->SetFont('Arial','',6);
        $pdf->SetXY($x, $chartY + $chartH + 2);
        $pdf->MultiCell($bw-4, 3, $t['Test_Type'], 0, 'C');

        $x += $bw;
    }

    $pdf->Ln(12);
}


/* ---------- GRAPH 3 ---------- */
function chart_client($pdf,$clientData){
    if(empty($clientData)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 3: Client Completion Percentage",0,1);

    ensure_space($pdf, 90);

    $points = [];
    foreach($clientData as $c){
        $sol = (int)$c['solicitados'];
        $ent = (int)$c['entregados'];
        $pct = $sol > 0 ? round(($ent*100)/$sol) : 0;

        $points[] = [
            "label" => $c['Client'],
            "pct"   => $pct
        ];
    }

    $chartX = 20;
    $chartY = $pdf->GetY()+5;
    $chartW = 170;
    $chartH = 50;

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $bars = count($points);
    if ($bars == 0) return;

    $bw = ($chartW-20)/$bars;
    $x  = $chartX + 10;

    foreach($points as $p){

        $h = ($p["pct"]/100) * ($chartH - 5);
        $y = $chartY + ($chartH - $h);

        $pdf->SetFillColor(255,165,0);
        $pdf->Rect($x,$y,$bw-4,$h,"F");

        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY($x,$y-4);
        $pdf->Cell($bw-4,4,$p["pct"]."%",0,0,'C');

        $pdf->SetFont('Arial','',6);
        $pdf->SetXY($x,$chartY+$chartH+2);
        $pdf->MultiCell($bw-4,3,$p["label"],0,'C');

        $x += $bw;
    }

    $pdf->Ln(12);
}


/* ======================================================
   GRAFICO MULTISERIE (CLIENTE × DIA) — CORREGIDO
======================================================*/
function chart_client_daily($pdf, $dias, $clientes, $matriz){

    if (empty($clientes) || empty($dias)) return;

    ensure_space($pdf, 95);

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartW = 150;
    $chartH = 55;

    // Ejes
    $pdf->SetDrawColor(0,0,0);
    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    // MAX
    $max = 1;
    foreach ($dias as $d){
        foreach ($clientes as $cl){
            if ($matriz[$d][$cl] > $max) $max = $matriz[$d][$cl];
        }
    }

    // Eje Y valores
    $pdf->SetFont("Arial", "", 7);
    $steps = 4;
    for ($i = 0; $i <= $steps; $i++) {
        $yPos = $chartY + $chartH - ($chartH / $steps * $i);
        $val  = round($max / $steps * $i);
        $pdf->SetXY($chartX - 8, $yPos - 2);
        $pdf->Cell(7, 4, $val, 0, 0, 'R');
    }

    // Barras
    $barsPerDay = count($clientes);
    $totalBars  = $barsPerDay * count($dias);
    if ($totalBars == 0) return;

    $bw = ($chartW - 20) / $totalBars;
    $x = $chartX + 10;

    $colorIndex = 0;

    foreach ($dias as $d){
        foreach ($clientes as $cl){

            $v = $matriz[$d][$cl];
            $h = ($v / $max) * ($chartH - 4);
            $y = $chartY + ($chartH - $h);

            list($r,$g,$b) = pickColor($colorIndex++);
            $pdf->SetFillColor($r,$g,$b);

            $pdf->Rect($x, $y, $bw, $h, "F");

            if ($v > 0){
                $pdf->SetFont("Arial","B",7);
                $pdf->SetXY($x, $y-4);
                $pdf->Cell($bw,4,$v,0,0,'C');
            }

            $x += $bw;
        }
    }

    // Eje X — días
    $pdf->SetFont("Arial","",7);
    $x = $chartX + 10;
    $groupW = $bw * $barsPerDay;

    foreach ($dias as $d){
        $pdf->SetXY($x, $chartY + $chartH + 2);
        $pdf->MultiCell($groupW, 4, date("D", strtotime($d)), 0, 'C');
        $x += $groupW;
    }

    // Leyenda a la derecha (vertical)
    $pdf->SetFont("Arial","B",8);
    $legendX = $chartX + $chartW + 6;
    $legendY = $chartY + 2;

    $pdf->SetXY($legendX, $legendY);
    $pdf->Cell(20,5,"Legend",0,1);

    foreach ($clientes as $i=>$cl){
        list($r,$g,$b) = pickColor($i);
        $pdf->SetFillColor($r,$g,$b);

        $pdf->SetXY($legendX, $legendY + 6 + ($i * 6));
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 4, 4, "F");

        $pdf->SetXY($legendX + 6, $legendY + 5 + ($i * 6));
        $pdf->SetFont("Arial","",7);
        $pdf->Cell(22,5,$cl);
    }

    $pdf->SetY($chartY + $chartH + 14);
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
$pdf->Ln(10);

/* ===============================
   GRAFICO KPI COMPACTO
================================*/
ensure_space($pdf, 60);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,"Graph: Weekly Activity Summary",0,1);

$labels  = ["Req","Prep","Real","Del"];
$values  = [$req, $prep, $real, $del];

// Asegurar que max no sea 0
$maxVal = max($values);
if ($maxVal == 0) $maxVal = 1;

$chartX = 20;
$chartY = $pdf->GetY() + 4;
$chartW = 120;
$chartH = 40;

// Ejes
$pdf->SetDrawColor(0,0,0);
$pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);               // Y
$pdf->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH); // X

/* ========== ESCALA Y ========== */
$pdf->SetFont('Arial','',7);
$steps = 4;
for($i = 0; $i <= $steps; $i++){
    $posY = $chartY + $chartH - ($chartH / $steps * $i);
    $val  = round($maxVal / $steps * $i);

    $pdf->SetXY($chartX - 8, $posY - 2);
    $pdf->Cell(7,3,$val,0,0,'R');
}

/* ========== BARRAS ========== */
$barWidth = floor($chartW / count($values)) - 12;

for($i = 0; $i < count($values); $i++){
    list($r,$g,$b) = pickColor($i);
    $pdf->SetFillColor($r,$g,$b);

    $barH = ($values[$i] / $maxVal) * ($chartH - 5);
    $barX = $chartX + 10 + $i * ($barWidth + 12);
    $barY = $chartY + $chartH - $barH;

    // barra
    $pdf->Rect($barX, $barY, $barWidth, $barH, "F");

    // valor arriba
    $pdf->SetFont('Arial','B',7);
    $pdf->SetXY($barX, $barY - 4);
    $pdf->Cell($barWidth,4,$values[$i],0,0,'C');

    // label eje X
    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($barX, $chartY + $chartH + 1);
    $pdf->Cell($barWidth,4,$labels[$i],0,0,'C');
}

/* ========== LEYENDA (VERTICAL DERECHA) ========== */
$legendX = $chartX + $chartW + 4;
$legendY = $chartY + 2;

$pdf->SetFont('Arial','B',7);
$pdf->SetXY($legendX, $legendY);
$pdf->Cell(20,4,"Legend",0,1);

foreach($labels as $i=>$lbl){
    list($r,$g,$b) = pickColor($i);
    $pdf->SetFillColor($r,$g,$b);

    $pdf->Rect($legendX, $legendY + 5 + ($i*6), 4, 4, "F");
    $pdf->SetXY($legendX + 6, $legendY + 4 + ($i*6));
    $pdf->SetFont('Arial','',7);
    $pdf->Cell(20,4,$lbl);
}

$pdf->Ln(45);
/* ===============================
   SECCIÓN 3 — DAILY BREAKDOWN BY CLIENT
================================*/
$pdf->section_title("3. Daily Breakdown by Client");

/* 1) Obtener CLIENTES de la semana */
$clientes = find_by_sql("
    SELECT DISTINCT Client
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    ORDER BY Client ASC
");

$clientNames = array_column($clientes, 'Client');

/* Si no hay clientes → mostrar mensaje */
if (empty($clientNames)) {
    $pdf->SetFont("Arial","B",10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No registered samples for this week.",1,1,'C',true);
    $pdf->Ln(5);
} else {

/* 2) Construir matriz Día × Cliente */
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

/* 3) Llenar matriz con datos reales */
$raw = find_by_sql("
    SELECT DATE(Registed_Date) AS dia, Client, COUNT(*) AS total
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY DATE(Registed_Date), Client
    ORDER BY dia ASC
");

foreach ($raw as $r) {
    $matriz[$r['dia']][$r['Client']] = (int)$r['total'];
}
}

/* ============================
   TABLA (DIA × CLIENTE)
============================= */

$pdf->SubTitle("Daily Registered Samples by Client");

// ancho de columnas
$colWidths = [28];  // fecha
foreach ($clientNames as $cl) $colWidths[] = 18;

// encabezado
$header = ["Date"];
foreach ($clientNames as $cl) $header[] = $cl;

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

/* ============================
   GRAFICO (MULTISERIE CLIENTE × DIA)
============================= */

$hasData = false;
foreach ($matriz as $row) {
    foreach ($row as $v) {
        if ($v > 0) { $hasData = true; break 2; }
    }
}

$pdf->SubTitle("Graph: Daily Samples by Client");

if (!$hasData) {
    $pdf->SetFont("Arial","B",10);
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,"No activity recorded for this week.",1,1,'C',true);
    $pdf->Ln(5);
} else {

    ensure_space($pdf, 95);

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartW = 150;
    $chartH = 55;

    // ejes
    $pdf->SetDrawColor(0,0,0);
    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);               
    $pdf->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH);  

    // máximo
    $maxVal = 0;
    foreach ($matriz as $row)
        foreach ($row as $v)
            if ($v > $maxVal) $maxVal = $v;

    if ($maxVal == 0) $maxVal = 1;

    // escala eje Y
    $pdf->SetFont("Arial","",7);
    $steps = 4;
    for ($i = 0; $i <= $steps; $i++) {
        $yPos = $chartY + $chartH - ($chartH / $steps * $i);
        $val  = round($maxVal / $steps * $i);

        $pdf->SetXY($chartX - 8, $yPos - 2);
        $pdf->Cell(7, 4, $val, 0, 0, 'R');
    }

    // valores y barras
    $days = array_keys($matriz);
    $barsPerDay = count($clientNames);
    $totalBars = $barsPerDay * count($days);

    $bw = ($chartW - 20) / $totalBars;
    $x = $chartX + 10;

    foreach ($matriz as $fecha => $row) {
        $colorIndex = 0;
        foreach ($clientNames as $cl) {

            $v = $row[$cl];
            $barH = ($v / $maxVal) * ($chartH - 4);
            $y = $chartY + ($chartH - $barH);

            list($r,$g,$b) = pickColor($colorIndex++);
            $pdf->SetFillColor($r,$g,$b);

            $pdf->Rect($x, $y, $bw, $barH, "F");

            if ($v > 0) {
                $pdf->SetFont('Arial','B',7);
                $pdf->SetXY($x, $y - 4);
                $pdf->Cell($bw, 4, $v, 0, 0, 'C');
            }

            $x += $bw;
        }
    }

    // eje X: días
    $pdf->SetFont("Arial","",7);
    $x = $chartX + 10;
    $groupW = $bw * $barsPerDay;

    foreach ($days as $d) {
        $pdf->SetXY($x, $chartY + $chartH + 2);
        $pdf->MultiCell($groupW, 4, date("D", strtotime($d)), 0, 'C');
        $x += $groupW;
    }

    /* ========= LEYENDA VERTICAL DERECHA ========= */

    $pdf->SetFont("Arial","B",8);
    $legendX = $chartX + $chartW + 6;
    $legendY = $chartY + 2;

    $pdf->SetXY($legendX, $legendY);
    $pdf->Cell(22,5,"Legend",0,1);

    $i = 0;
    foreach ($clientNames as $cl) {
        list($r,$g,$b) = pickColor($i);

        $pdf->SetFillColor($r,$g,$b);
        $pdf->Rect($legendX, $legendY + 6 + ($i * 6), 4, 4, "F");

        $pdf->SetXY($legendX + 6, $legendY + 5 + ($i * 6));
        $pdf->SetFont("Arial","",7);
        $pdf->Cell(22,5,$cl);

        $i++;
    }

    $pdf->SetY($chartY + $chartH + 14);
}

/* ===============================
   SECCIÓN 4 — TEST DISTRIBUTION BY TYPE
================================*/
$pdf->section_title("4. Test Distribution by Type");

$data_tipo = resumen_tipo($start_str,$end_str);

$pdf->table_header(["Test Type","Completed"],[80,40]);

foreach($data_tipo as $t){
    $pdf->table_row([$t['Test_Type'],$t['total']],[80,40]);
}

$pdf->Ln(10);


/* ===============================
   SECCIÓN 5 — SAMPLES GRAPH (DIA)
================================*/
ensure_space($pdf, 80);
chart_samples($pdf,$data_dia);


/* ===============================
   SECCIÓN 6 — TYPES GRAPH
================================*/
ensure_space($pdf, 80);
chart_types($pdf,$data_tipo);


/* ===============================
   SECCIÓN 7 — CLIENT COMPLETION GRAPH
================================*/
$clientes_res = resumen_cliente($start_str,$end_str);
ensure_space($pdf, 90);
chart_client($pdf,$clientes_res);

$pdf->Ln(10);


/* ===============================
   SECCIÓN 8 — NEWLY REGISTERED SAMPLES
================================*/
$pdf->section_title("5. Newly Registered Samples (Weekly)");

$muestras = find_by_sql("
    SELECT *
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    ORDER BY Registed_Date ASC
");

$pdf->table_header(["Sample","Structure","Client","Test Type"],[45,35,30,60]);

foreach($muestras as $m){
    $pdf->table_row([
        $m['Sample_ID']."-".$m['Sample_Number'],
        $m['Structure'],
        $m['Client'],
        $m['Test_Type']
    ],[45,35,30,60]);
}

$pdf->Ln(10);


/* ===============================
   SECCIÓN 9 — TESTS BY TECHNICIAN
================================*/
$pdf->section_title("6. Summary of Tests by Technician");

$tec = find_by_sql("
    SELECT Technician, COUNT(*) total, 'In Preparation' etapa
    FROM test_preparation
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician

    UNION ALL

    SELECT Technician, COUNT(*) total, 'In Realization'
    FROM test_realization
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician

    UNION ALL

    SELECT Technician, COUNT(*) total, 'Completed'
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician
");

$pdf->table_header(["Technician","Process","Qty"],[70,50,20]);

foreach($tec as $t){
    $pdf->table_row([$t['Technician'],$t['etapa'],$t['total']], [70,50,20]);
}

$pdf->Ln(10);


/* ===============================
   SECCIÓN 10 — TESTS BY TYPE
================================*/
$pdf->section_title("7. Summary of Tests by Type");

$tipos = find_by_sql("
    SELECT Test_Type, COUNT(*) total, 'In Preparation' etapa
    FROM test_preparation
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type

    UNION ALL

    SELECT Test_Type, COUNT(*) total, 'In Realization'
    FROM test_realization
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type

    UNION ALL

    SELECT Test_Type, COUNT(*) total, 'Completed'
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type
");

$pdf->table_header(["Test Type","Process","Qty"],[70,50,20]);

foreach($tipos as $t){
    $pdf->table_row([$t['Test_Type'],$t['etapa'],$t['total']], [70,50,20]);
}

$pdf->Ln(10);


/* ===============================
   SECCIÓN 11 — PENDING TESTS
================================*/
$pdf->section_title("8. Pending Tests");

$pendientes = find_by_sql("
    SELECT r.*
    FROM lab_test_requisition_form r
    WHERE r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND NOT EXISTS (
          SELECT 1 FROM test_delivery d
          WHERE d.Sample_ID=r.Sample_ID
            AND d.Sample_Number=r.Sample_Number
            AND d.Test_Type=r.Test_Type
      )
");

$pdf->table_header(["Sample","Number","Type","Date"],[40,35,50,25]);

foreach($pendientes as $p){
    $pdf->table_row([
        $p['Sample_ID'],
        $p['Sample_Number'],
        $p['Test_Type'],
        date("d-M",strtotime($p['Sample_Date']))
    ],[40,35,50,25]);
}

$pdf->Ln(10);


/* ===============================
   SECCIÓN 12 — DAM CONSTRUCTION TESTS
================================*/
$pdf->section_title("9. Summary of Dam Construction Tests");

$ensayos = find_by_sql("
    SELECT *
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$pdf->table_header(
    ["Sample","Structure","Test","Condition","Comments"],
    [40,25,50,25,50]
);

foreach($ensayos as $e){

    $sampleFull = $e['Sample_ID']."-".$e['Sample_Number'];
    if (!empty($e['Material_Type'])) {
        $sampleFull .= " / ".$e['Material_Type'];
    }

    table_row_multiline($pdf,[
        $sampleFull,
        $e['Structure'],
        $e['Test_Type'],
        $e['Test_Condition'],
        $e['Comments']
    ], [40,25,50,25,50]);
}

$pdf->Ln(10);


/* ===============================
   SECCIÓN 13 — OBSERVATIONS & NCR
================================*/
$pdf->section_title("10. Observations & Non-Conformities");

$ncr = find_by_sql("
    SELECT *
    FROM ensayos_reporte
    WHERE Noconformidad IS NOT NULL
      AND TRIM(Noconformidad) <> ''
      AND Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$pdf->table_header(["Sample","Observations"],[40,150]);

foreach($ncr as $n){
    table_row_multiline($pdf,[
        $n['Sample_ID']."-".$n['Sample_Number']."-".$n['Material_Type'],
        $n['Noconformidad']
    ], [40,150]);
}

$pdf->Ln(10);


/* ===============================
   SECCIÓN 14 — RESPONSIBLE
================================*/
$pdf->section_title("11. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(60,8,"Report prepared by",1);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);


/* ===============================
   OUTPUT PDF
================================*/
ob_end_clean();
$pdf->Output("I","Weekly_Lab_Report_Week{$week}_{$year}.pdf");

?>
