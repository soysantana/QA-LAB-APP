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

/* ==========================================================
   SECCIÓN 2 — WEEKLY CLIENT COMPLETION SUMMARY (LAST 30 DAYS)
   (Reemplaza completamente la sección 2 actual)
========================================================== */

$pdf->section_title("2. Weekly Client Completion Summary (Last 30 Days)");

/* ==========================================================
   1. Fechas — Último mes desde el fin de la semana
========================================================== */
$endMonth   = $end_str;
$startMonth = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($endMonth)));

/* ==========================================================
   2. Función para detectar "envío / envio / envíos"
========================================================== */
function es_envio_tt($tt){
    $t = mb_strtolower(trim($tt), 'UTF-8');
    return preg_match('/\benv[ií]os?\b/u', $t);
}

/* ==========================================================
   3. Solicitudes del último mes
========================================================== */
$solicitudes = find_by_sql("
    SELECT 
        UPPER(TRIM(Client)) AS Client,
        Sample_ID,
        Sample_Number,
        Test_Type
    FROM lab_test_requisition_form
    WHERE Sample_Date BETWEEN '{$startMonth}' AND '{$endMonth}'
");

/* ==========================================================
   4. Cargar completados (Delivery + Review + Reviewed + DOC_FILES)
========================================================== */

$rawCompleted = [];

$tablas = [
    ["test_delivery", "Register_Date"],
    ["test_review",   "Start_Date"],
    ["test_reviewed", "Start_Date"],
    ["doc_files",     "created_at"]
];

foreach ($tablas as $t){

    list($table, $fecha) = $t;

    $rows = find_by_sql("
        SELECT Sample_ID, Sample_Number, Test_Type
        FROM {$table}
        WHERE {$fecha} BETWEEN '{$startMonth}' AND '{$endMonth}'
    ");

    foreach ($rows as $r){
        $rawCompleted[] = $r;
    }
}

/* ==========================================================
   5. Mapa de COMPLETADOS (SampleID|Number|Type)
========================================================== */

$entregado_map = [];

foreach ($rawCompleted as $e){

    $sid = strtoupper(trim($e['Sample_ID']   ?? ''));
    $sno = strtoupper(trim($e['Sample_Number'] ?? ''));
    $tts = (string)($e['Test_Type'] ?? '');

    foreach (preg_split('/[;,]+/', $tts) as $tt){

        $tt = trim($tt);
        if ($tt === '' || es_envio_tt($tt)) continue;

        $key = "{$sid}|{$sno}|" . strtoupper($tt);
        $entregado_map[$key] = true;
    }
}

/* ==========================================================
   6. Procesar solicitudes por cliente
========================================================== */

$stats = [];

foreach ($solicitudes as $s){

    $client   = $s['Client'] ?: "PENDING INFO";
    $sid      = strtoupper(trim($s['Sample_ID'] ?? ''));
    $sno      = strtoupper(trim($s['Sample_Number'] ?? ''));
    $tts      = (string)($s['Test_Type'] ?? '');

    foreach (preg_split('/[;,]+/', $tts) as $tt){

        $tt = trim($tt);
        if ($tt === '' || es_envio_tt($tt)) continue;

        // crear si no existe
        if (!isset($stats[$client])) {
            $stats[$client] = [
                'cliente'   => $client,
                'requested' => 0,
                'completed' => 0,
                'percent'   => 0
            ];
        }

        $stats[$client]['requested']++;

        $key = "{$sid}|{$sno}|" . strtoupper($tt);
        if (isset($entregado_map[$key])) {
            $stats[$client]['completed']++;
        }
    }
}

/* ==========================================================
   7. Calcular porcentaje
========================================================== */
foreach ($stats as &$row){
    $r = (int)$row['requested'];
    $c = (int)$row['completed'];
    $row['percent'] = $r > 0 ? round(($c/$r)*100, 1) : 0;
}
unset($row);

/* ==========================================================
   8. Convertir a array numérico para usar usort()
========================================================== */
$stats = array_values($stats);

/* ==========================================================
   9. Ordenar por Backlog DESC
========================================================== */
usort($stats, function($a,$b){
    return ($b['requested']-$b['completed']) <=> ($a['requested']-$a['completed']);
});

/* ==========================================================
   10. TABLA PDF
========================================================== */
$pdf->SetFont('Arial','B',10);
$pdf->table_header(
    ["Client","Requested","Completed","Backlog","%"],
    [60,30,30,30,20]
);

$pdf->SetFont('Arial','',10);
foreach ($stats as $row){

    $req = $row['requested'];
    $com = $row['completed'];
    $back= $req - $com;
    if ($back < 0) $back = 0;

    $pdf->table_row([
        $row['cliente'],
        $req,
        $com,
        $back,
        $row['percent']."%"
    ], [60,30,30,30,20]);
}

$pdf->Ln(8);

/* ==========================================================
   11. Transformar $stats → $clientesChart (para el gráfico)
========================================================== */

$clientesChart = [];

foreach ($stats as $row){

    $cli = $row['cliente'];
    $clientesChart[$cli] = [
        'solicitados' => (int)$row['requested'],
        'entregados'  => (int)$row['completed']
    ];
}

/* ==========================================================
   12. FUNCIÓN — GRÁFICO DE BARRAS VERTICAL
========================================================== */

function draw_client_bar_chart($pdf, array $clientes) {
  if (empty($clientes)) return;

  // Construir data: cliente + porcentaje
  $data = [];
  foreach ($clientes as $cli => $d) {
    $sol = (int)($d['solicitados'] ?? 0);
    $ent = (int)($d['entregados'] ?? 0);
    $pct = $sol > 0 ? round(($ent * 100) / $sol) : 0;

    $label = strtoupper(trim($cli));
    if (mb_strlen($label, 'UTF-8') > 20) {
      $label = mb_substr($label, 0, 10, 'UTF-8') . '...';
    }

    $data[] = [
      'label' => $label,
      'pct'   => $pct,
    ];
  }

  $maxPct = 0;
  foreach ($data as $d) {
    if ($d['pct'] > $maxPct) $maxPct = $d['pct'];
  }
  if ($maxPct <= 0) return;

  // espacio
  $chartHeight = 45;
  $chartBottomMargin = 18;
  $needed = $chartHeight + $chartBottomMargin + 10;
  if ($pdf->GetY() + $needed > 260) {
    $pdf->AddPage();
  }

  // ejes y dimensiones
  $x0 = $pdf->GetX();
  $y0 = $pdf->GetY() + 4;
  $chartWidth  = 180;
  $numBars     = count($data);
  $gap = 4;

  $barWidth = ($chartWidth - ($numBars + 1) * $gap) / max($numBars, 1);
  if ($barWidth < 8) $barWidth = 8;

  $pdf->SetDrawColor(0);
  $pdf->Line($x0, $y0, $x0, $y0 + $chartHeight);
  $pdf->Line($x0, $y0 + $chartHeight, $x0 + $chartWidth, $y0 + $chartHeight);

  // escala del 0% al 100%
  $pdf->SetFont('Arial','',7);
  $steps = [0,25,50,75,100];
  foreach ($steps as $pctRef){
      if ($pctRef > $maxPct) continue;
      $yLine = $y0 + $chartHeight - ($pctRef * $chartHeight / $maxPct);
      $pdf->SetDrawColor(220);
      $pdf->Line($x0, $yLine, $x0 + $chartWidth, $yLine);
      $pdf->SetDrawColor(0);
      $pdf->SetXY($x0 - 8, $yLine - 2);
      $pdf->Cell(8,4,$pctRef.'%',0,0,'R');
  }

  // barras
  $pdf->SetFont('Arial','',8);
  $i = 0;
  foreach ($data as $d){
      $pct  = $d['pct'];
      $lbl  = $d['label'];

      $barHeight = ($pct * $chartHeight) / $maxPct;
      $x = $x0 + $gap + $i * ($barWidth + $gap);
      $y = $y0 + $chartHeight - $barHeight;

      $pdf->SetFillColor(100,149,237);
      $pdf->Rect($x, $y, $barWidth, $barHeight, 'F');

      $pdf->SetXY($x, $y - 4);
      $pdf->Cell($barWidth,4,$pct.'%',0,0,'C');

      $pdf->SetXY($x, $y0 + $chartHeight + 2);
      $pdf->MultiCell($barWidth,3,$lbl,0,'C');

      $i++;
  }

  $pdf->SetY($y0 + $chartHeight + $chartBottomMargin);
}

/* ==========================================================
   13. Dibujar el gráfico
========================================================== */

$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,8,"2.1 Client Completion Chart",0,1);

draw_client_bar_chart($pdf, $clientesChart);

$pdf->Ln(8);

/* ===============================
   SECCIÓN 4 — WEEKLY SUMMARY
================================*/
$pdf->section_title("3. Weekly Summary of Activities");

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

$pdf->Ln(30);

/* ===============================
   SECCIÓN 4 — DAILY BREAKDOWN BY CLIENT
================================*/
$pdf->section_title("4. Daily Breakdown by Client");

/* ---------- Construir matriz día × cliente ---------- */

// 1) Lista de clientes de la semana
$clientesRes = find_by_sql("
    SELECT DISTINCT TRIM(Client) AS Client
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND TRIM(IFNULL(Client,'')) <> ''
    ORDER BY Client ASC
");

$clientNames = array_column($clientesRes, 'Client');

/* -------------------------------------------
   CALCULAR ANCHO DINÁMICO POR CLIENTE
------------------------------------------- */

if (!empty($clientNames)) {

    $totalWidth = 190;      // ancho útil página
    $dateCol    = 40;       // ancho columna DATE
    $usable     = $totalWidth - $dateCol;

    // medir ancho teórico de cada nombre
    $tempPDF = new FPDF();
    $tempPDF->AddPage();
    $tempPDF->SetFont('Arial','',9);

    $nameWidths = [];
    foreach ($clientNames as $cl) {
        $txtW = $tempPDF->GetStringWidth($cl) + 10; // margen
        if ($txtW < 20)  $txtW = 20;  // ancho mínimo
        if ($txtW > 45)  $txtW = 45;  // ancho máximo por cliente
        $nameWidths[$cl] = $txtW;
    }

    // normalizar para que todos juntos quepan
    $sum = array_sum($nameWidths);
    if ($sum > $usable) {
        // factor de reducción
        $factor = $usable / $sum;
        foreach ($nameWidths as $cl => $w) {
            $nameWidths[$cl] = max(18, $w * $factor); // ancho mínimo 18
        }
    }

    // construir array final de anchos
    $colWidths = [$dateCol];
    foreach ($clientNames as $cl) {
        $colWidths[] = $nameWidths[$cl];
    }
}

/* ---------- Matriz día × cliente ---------- */

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
    if (!array_key_exists($cl, $matriz[$dia])) continue;

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

    /* ---------- GRAFICO  DAILY SAMPLES ---------- */

    // Solo si hay valores > 0
    $hasData = false;
    foreach ($matriz as $row) {
        foreach ($row as $v) {
            if ($v > 0) { $hasData = true; break 2; }
        }
    }

    if (!$hasData) {
        $pdf->SetFont("Arial","B",10);
        $pdf->SetFillColor(245,245,245);
        $pdf->Cell(0,10,"No daily client activity to display in graph.",1,1,'C',true);
        $pdf->Ln(5);

    } else {

        $pdf->SubTitle("Graph: Daily Samples by Client");
        ensure_space($pdf, 95);

        $chartX = 20;
        $chartY = $pdf->GetY() + 5;
        $chartW = 150;
        $chartH = 55;

        // Dibujar ejes
        $pdf->SetDrawColor(0,0,0);
        $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);
        $pdf->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH);

        // max
        $maxVal = 1;
        foreach ($matriz as $row) {
            foreach ($row as $v) {
                if ($v > $maxVal) $maxVal = $v;
            }
        }

        // escala
        $pdf->SetFont("Arial", "", 7);
        $steps = 4;
        for ($i = 0; $i <= $steps; $i++) {
            $yPos = $chartY + $chartH - ($chartH / $steps * $i);
            $val  = round($maxVal / $steps * $i);
            $pdf->SetXY($chartX - 8, $yPos - 2);
            $pdf->Cell(7, 4, $val, 0, 0, 'R');
        }

        // barras
        $days        = array_keys($matriz);
        $barsPerDay  = count($clientNames);
        $totalBars   = max(1, $barsPerDay * count($days));
        $bw          = max(1, ($chartW - 20) / $totalBars);

        $x = $chartX + 10;

        foreach ($days as $d) {
            $row = $matriz[$d];
            foreach ($clientNames as $idx => $cl) {
                $v = $row[$cl];
                $barH = ($v / $maxVal) * ($chartH - 4);
                $y = $chartY + ($chartH - $barH);

                list($r,$g,$b) = pickColor($idx);
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

        // etiquetas días
        $pdf->SetFont("Arial", "", 7);
        $x = $chartX + 10;
        $groupW = $bw * $barsPerDay;

        foreach ($days as $d) {
            $pdf->SetXY($x, $chartY + $chartH + 2);
            $pdf->MultiCell($groupW, 4, date("D", strtotime($d)), 0, 'C');
            $x += $groupW;
        }

        // leyenda
        $legendX = $chartX + $chartW + 6;
        $legendY = $chartY + 2;

        $pdf->SetFont("Arial","B",8);
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

        $pdf->SetY($chartY + $chartH + 16);
    }
}

/* ===============================
   SECCIÓN 5 — TEST DISTRIBUTION BY TYPE AND CLIENT
================================*/
$pdf->section_title("5. Test Distribution by Type and Client");

/* ======================================================
   DICCIONARIO DE NOMBRES COMPLETOS
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
    "SND" => "Soundness",
    "LAA" => "Los Angeles Abrasion",
    "SHAPE"  => "Particle Shape",
    "PERM" => "Permeability",
    "ENVIO" => "For Shipment",
];

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

    /* Convertir abreviaturas → nombres completos */
    $testTypesPretty = [];
    foreach ($testTypes as $tp){
        $testTypesPretty[$tp] = $testNames[$tp] ?? $tp;
    }

    // ---------- TABLA TIPO × CLIENTE ----------

    $pdf->SubTitle("Completed Tests by Type and Client");

    $colWidths = [40];
    $numClients4 = count($clients4);
    $restWidth = 190 - 40;
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
        $row = [$testTypesPretty[$tp]];
        foreach ($clients4 as $cl){
            $row[] = $matrix4[$tp][$cl] ?? 0;
        }
        $pdf->table_row($row, $colWidths);
    }

    $pdf->Ln(6);

    /* ---------- GRÁFICO: TYPE × CLIENT ---------- */
    $pdf->SubTitle("Graph: Tests by Type and Client");

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

        // Labels eje X (test types con nombres completos)
        $pdf->SetFont("Arial","",7);
        $x = $chartX + 10;
        $groupW = $bw * $barsPerType;

        foreach ($testTypes as $tp){
            $label = $testNames[$tp] ?? $tp;
            $pdf->SetXY($x, $chartY + $chartH + 2);
            $pdf->MultiCell($groupW, 4, $label, 0, 'C');
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
   6. Newly Registered Samples (Weekly)
================================*/
$pdf->section_title("6. Newly Registered Samples (Weekly)");

/* ======================================================
   DICCIONARIO DE NOMBRES COMPLETOS
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
    "SND" => "Soundness",
    "LAA" => "Los Angeles Abrasion",
    "PS"  => "Particle Shape",
    "DEN" => "Density (Field/Lab)",
    "CBR" => "CBR Test",
];

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

    $clientsMap     = [];
    $testTypeTotals = [];
    $matrix5        = [];

    foreach ($muestras as $row) {

        $client = trim((string)$row['Client']);
        if ($client === '') $client = 'N/A';

        if (!isset($clientsMap[$client])) $clientsMap[$client] = 0;
        $clientsMap[$client]++;

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
    $topClientName  = "-";
    $topClientCount = 0;
    foreach ($clientsMap as $c => $cnt) {
        if ($cnt > $topClientCount) {
            $topClientCount = $cnt;
            $topClientName  = $c;
        }
    }

    // === Convertir top test a nombre completo ===
    $topTestName  = "-";
    $topTestCount = 0;
    foreach ($testTypeTotals as $t => $cnt) {
        if ($cnt > $topTestCount) {
            $topTestCount = $cnt;
            $topTestName  = $testNames[$t] ?? $t;
        }
    }

    // ---- Tarjetas estilo dashboard ----
    $boxW = 95;

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(230,230,230);

    $pdf->Cell($boxW,6,"Total Samples this Week",1,0,'L',true);
    $pdf->Cell($boxW,6,"Top Client this Week",1,1,'L',true);

    $pdf->SetFont('Arial','',11);
    $pdf->Cell($boxW,8,$totalSamplesWeek,1,0,'C');

    $txtTopClient = ($topClientName === "-")
        ? "-"
        : ($topClientName." - ".$topClientCount." samples");

    $pdf->Cell($boxW,8,utf8_decode($txtTopClient),1,1,'C');

    // === Top Test Type ===
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($boxW*2,6,"Top Test Type this Week",1,1,'L',true);

    $pdf->SetFont('Arial','',11);
    $txtTopTest = ($topTestName === "-")
        ? "-"
        : ($topTestName." - ".$topTestCount." tests");

    $pdf->Cell($boxW*2,8,utf8_decode($txtTopTest),1,1,'C');

    $pdf->Ln(10);

    // ===========================
    // 3) TABLA TIPO × CLIENTE (CON NOMBRES COMPLETOS)
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

        $colWidths = [40];
        $numClients = count($clients5);
        $restWidth = 190 - 40;
        $wClient = max(22, floor($restWidth / $numClients));

        foreach ($clients5 as $c) {
            $colWidths[] = $wClient;
        }

        $header = ["Test Type"];
        foreach ($clients5 as $c) $header[] = $c;

        $pdf->table_header($header,$colWidths);

        // filas con nombre completo
        foreach ($types5 as $tp) {

            $prettyName = $testNames[$tp] ?? $tp;

            $row = [$prettyName];

            foreach ($clients5 as $cl) {
                $row[] = $matrix5[$tp][$cl] ?? 0;
            }

            $pdf->table_row($row,$colWidths);
        }

        $pdf->Ln(10);
    }
}

/* ===============================
   SECCIÓN 7 — SUMMARY OF TESTS BY TECHNICIAN
================================*/
$pdf->section_title("7. Summary of Tests by Technician");

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
 CLASIFICACIÓN DE ESTRUCTURAS
*/
function classifyStructureNormalized($sampleId, $structure) {

    $sid = strtoupper(trim((string)$sampleId));
    $st  = strtoupper(trim((string)$structure));

    $startsWith = function($text, $prefix){
        return substr($text, 0, strlen($prefix)) === $prefix;
    };

    $contains = function($text, $needle){
        return strpos($text, $needle) !== false;
    };

    // STOCKPILE
    if ($startsWith($sid, "PVDJ-AGG")) return "Stockpile";
    if ($contains($st, "STOCK")) return "Stockpile";

    // BORROW
    if ($startsWith($sid, "LBOR")) return "Borrow";

    // LLD
    if ($startsWith($st, "LLD")) return "LLD";

    // SD1 / SD2 / SD3
    if ($startsWith($st, "SD1")) return "SD1";
    if ($startsWith($st, "SD2")) return "SD2";
    if ($startsWith($st, "SD3")) return "SD3";

    // CORE
    if ($startsWith($st, "CORE")) return "Core";

    // Otro
    if ($st !== "") return ucwords(strtolower($st));

    return "Unknown";
}

/* ORDEN LÓGICO DE ESTRUCTURAS */
function structureOrderValue($structure){
    $order = [
        "Stockpile" => 1,
        "Borrow"    => 2,
        "LLD"       => 3,
        "SD1"       => 4,
        "SD2"       => 5,
        "SD3"       => 6,
        "Core"      => 7,
        "Other"     => 8,
        "Unknown"   => 9
    ];
    return $order[$structure] ?? 999;
}

/* CARGAR DATOS */
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

    /* ===============================
       1. MATRIZ AGRUPADA
    ===============================*/
    $matrix = [];

    foreach ($rows as $r) {

        // Normalizar estructura según PV
        $structure = classifyStructureNormalized($r['Sample_ID'], $r['Structure']);

        // Material NO se toca (como pediste)
        $material = trim((string)$r['Material_Type']);
        if ($material === "") $material = "Other";

        // Test Type: limpiar espacios + capitalizar
        $rawType = trim((string)$r['Test_Type']);
        if ($rawType === "") $rawType = "Unknown";

        // eliminar dobles espacios
        $rawType = preg_replace('/\s+/', ' ', $rawType);

        // capitalizar solo primera letra
        $testType = ucfirst(strtolower($rawType));

        // Comentarios para FAIL
        $comment = strtoupper((string)$r['Comments']);
        $cond    = strtoupper((string)$r['Test_Condition']);
        $ncr     = strtoupper((string)$r['Noconformidad']);

        // Función contains
        $contains = function($text, $needle){
            return strpos($text, $needle) !== false;
        };

        // Palabras clave para fallos
        $failKeywords = [
            "FAIL","FAILED","FAILURE",
            "NO CUMPLE","RECHAZ",
            "REJECT","REJECTED",
            "NCR","NOT OK"
        ];

        // Detectar fallo
        $isFail = false;
        foreach ($failKeywords as $k){
            if ($contains($comment, $k) || $contains($cond, $k) || $contains($ncr, $k)) {
                $isFail = true;
                break;
            }
        }

        // Construcción de matriz agrupada
        if (!isset($matrix[$structure])) $matrix[$structure] = [];
        if (!isset($matrix[$structure][$material])) $matrix[$structure][$material] = [];
        if (!isset($matrix[$structure][$material][$testType])) {
            $matrix[$structure][$material][$testType] = [
                "passed" => 0,
                "failed" => 0
            ];
        }

        if ($isFail)
            $matrix[$structure][$material][$testType]["failed"]++;
        else
            $matrix[$structure][$material][$testType]["passed"]++;
    }

    /* ===============================
       2. ORDENAR MATRIZ POR ESTRUCTURA
    ===============================*/
    uksort($matrix, function($a,$b){
        return structureOrderValue($a) <=> structureOrderValue($b);
    });

    /* ===============================
       3. TABLA FORMATEADA
    ===============================*/

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"9.1 Summary Table",0,1);
    $pdf->Ln(2);

    // Fila 1 encabezado
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(220,220,220);

    $pdf->Cell(40,7,"Structure",1,0,'C',true);
    $pdf->Cell(40,7,"Material",1,0,'C',true);
    $pdf->Cell(40,7,"Test Type",1,0,'C',true);
    $pdf->Cell(44,7,"Results",1,1,'C',true);

    // Fila 2 encabezado
    $pdf->Cell(40,6,"",1,0);
    $pdf->Cell(40,6,"",1,0);
    $pdf->Cell(40,6,"",1,0);
    $pdf->Cell(22,6,"Passed",1,0,'C',true);
    $pdf->Cell(22,6,"Failed",1,1,'C',true);

    // Datos
    $pdf->SetFont('Arial','',9);

    foreach ($matrix as $structure => $materials) {
        foreach ($materials as $material => $tests) {
            foreach ($tests as $testType => $info) {

                $passed = $info["passed"];
                $failed = $info["failed"];

                if ($passed==0 && $failed==0) continue;

                $pdf->Cell(40,6,$structure,1);
                $pdf->Cell(40,6,$material,1);
                $pdf->Cell(40,6,$testType,1);

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
