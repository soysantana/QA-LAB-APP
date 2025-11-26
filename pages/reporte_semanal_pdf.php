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
/* ======================================================
   CREAR MATRIZ DIA × CLIENTE
======================================================*/
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

/* =============================================
   ROW MULTILÍNEA + PAGE BREAK CONTROL
=============================================*/
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

    foreach($data as $i => $txt){
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($w[$i],5,utf8_decode($txt),1,'L');
        $pdf->SetXY($x + $w[$i], $y);
    }

    $pdf->Ln($maxHeight);
}

/* ===============================
   4. FUNCION PARA EVITAR QUE EL GRÁFICO SE PARTA
================================*/
function ensure_space($pdf, $neededHeight = 80){
    if ($pdf->GetY() + $neededHeight > 260) {
        $pdf->AddPage();
    }
}

/* ===============================
   5. PDF CLASS
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
   (NECESARIA PARA pickColor)
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

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $bars = count($data);
    $bw = ($chartW-20)/$bars;
    $x = $chartX+10;

    foreach($data as $d){

        $h = ($d['total'] / $max) * $chartH;
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

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $bars = count($data);
    $bw = ($chartW-20)/$bars;
    $x = $chartX+10;

    foreach($data as $t){

        $h = ($t['total'] / $max) * $chartH;
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
    $bw = ($chartW-20)/$bars;
    $x  = $chartX + 10;

    foreach($points as $p){

        $h = ($p["pct"]/100) * $chartH;
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
   GRAFICO MULTISERIE (CLIENTE × DIA)
   Una barra por día dentro de cada cliente
======================================================*/
function chart_client_daily($pdf, $dias, $clientes, $matriz){

    if (empty($clientes) || empty($dias)) return;

    ensure_space($pdf, 90);

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartW = 170;
    $chartH = 55;

    // Ejes
    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartH);
    $pdf->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH);

    /* -------- Máximo -------- */
    $max = 1;
    foreach ($dias as $d){
        foreach ($clientes as $c){
            if ($matriz[$d][$c] > $max) $max = $matriz[$d][$c];
        }
    }

    /* -------- Config -------- */
    $groups = count($clientes);
    $series = count($dias);

    $groupW = floor($chartW / $groups);
    $barW   = floor(($groupW - 5) / $series);

    /* -------- Dibujar -------- */
    foreach ($clientes as $g => $cliente){

        $baseX = $chartX + ($g * $groupW);

        foreach ($dias as $i => $dia){

            $v = $matriz[$dia][$cliente];
            $barH = ($v / $max) * ($chartH - 5);

            list($r,$gC,$b) = pickColor($i);
            $pdf->SetFillColor($r,$gC,$b);

            $x = $baseX + ($i * $barW) + 3;
            $y = $chartY + ($chartH - $barH);

            $pdf->Rect($x, $y, $barW, $barH, "F");
        }

        // Label cliente
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($baseX, $chartY + $chartH + 1);
        $pdf->Cell($groupW, 4, $cliente, 0, 0, 'C');
    }

    /* -------- Leyenda -------- */
    $pdf->SetFont('Arial','B',8);
    $pdf->SetXY($chartX, $chartY - 4);
    $pdf->Cell(40,4,"Legend");

    $i = 0;
    foreach ($dias as $d){
        list($r,$gC,$b) = pickColor($i);
        $pdf->SetFillColor($r,$gC,$b);

        $pdf->Rect($chartX + 45 + ($i*20), $chartY - 3, 4, 4, "F");
        $pdf->SetXY($chartX + 50 + ($i*20), $chartY - 4);
        $pdf->SetFont("Arial","",7);
        $pdf->Cell(20,5,date("D",strtotime($d)));

        $i++;
    }

    $pdf->SetY($chartY + $chartH + 12);
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
//----------------------------------------------
// GRAFICO KPI COMPACTO (SECCIÓN 2)
//----------------------------------------------

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
if ($maxVal == 0) $maxVal = 1;

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
$barWidth = floor($chartW / count($values)) - 12;

for($i = 0; $i < count($values); $i++){
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


$pdf->section_title("3. Daily Breakdown by Client (ISO Week)");

/* ------- Encabezado dinámico -------- */
$header = ["Day"];
$widths = [25];

foreach($clientes as $c){
    $header[] = $c;
    $widths[] = 25;
}

$pdf->table_header($header, $widths);

/* ------- Filas ------- */
foreach($diasSemana as $d){
    $row = [date("D d-M", strtotime($d))];

    foreach($clientes as $c){
        $row[] = $matriz[$d][$c];
    }

    $pdf->table_row($row, $widths);
}

$pdf->Ln(8);
$pdf->SubTitle("Graph: Samples Per Day by Client");

chart_client_daily($pdf, $diasSemana, $clientes, $matriz);


/* ===============================
   SECCIÓN 4 — TEST DISTRIBUTION
================================*/
$pdf->section_title("4. Test Distribution by Type");

$data_tipo = resumen_tipo($start_str,$end_str);

$pdf->table_header(["Test Type","Completed"],[80,40]);

foreach($data_tipo as $t){
    $pdf->table_row([$t['Test_Type'],$t['total']],[80,40]);
}

$pdf->Ln(10);

/* ===============================
   GRAFICOS
================================*/
ensure_space($pdf, 80);
chart_samples($pdf,$data_dia);

ensure_space($pdf, 80);
chart_types($pdf,$data_tipo);

$clientes_res = resumen_cliente($start_str,$end_str);
ensure_space($pdf, 90);
chart_client($pdf,$clientes_res);

$pdf->Ln(10);
/* ===============================
   5. NEWLY REGISTERED SAMPLES
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
   6. TESTS BY TECHNICIAN
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
   7. TEST SUMMARY BY TYPE
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
   8. PENDING TESTS
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
   9. DAM CONSTRUCTION TESTS
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
   10. OBSERVATIONS & NCR
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
   11. RESPONSIBLE
================================*/
$pdf->section_title("11. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(60,8,"Report prepared by",1);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Weekly_Lab_Report_Week{$week}_{$year}.pdf");
?>
