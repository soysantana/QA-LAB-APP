<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* =====================================================
   1. HELPER GENERAL PARA CONTAR ENTRE FECHAS
===================================================== */
function get_count($table, $field, $start, $end) {
    $sql = "SELECT COUNT(*) AS total 
            FROM {$table}
            WHERE {$field} BETWEEN '{$start}' AND '{$end}'";
    $res = find_by_sql($sql);
    return isset($res[0]['total']) ? (int)$res[0]['total'] : 0;
}

/* =====================================================
   2. FECHAS → DEFINIR SEMANA ISO
===================================================== */
$fecha = $_GET['fecha'] ?? date('Y-m-d');
$fecha_obj = new DateTime($fecha);

$week_iso = (int)$fecha_obj->format('W');
$year_iso = (int)$fecha_obj->format('o');

// Lunes de la semana ISO
$start = new DateTime();
$start->setISODate($year_iso, $week_iso, 1);
$start_str = $start->format('Y-m-d 00:00:00');

// Domingo de la semana ISO
$end = new DateTime();
$end->setISODate($year_iso, $week_iso, 7);
$end_str = $end->format('Y-m-d 23:59:59');

// Usuario
$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* =====================================================
   3. FUNCIONES SQL DEL REPORTE SEMANAL
===================================================== */

// 3.1 Muestras registradas por día de la semana (ISO)
function resumen_diario_semana($start_str, $end_str) {
    return find_by_sql("
        SELECT DATE(Registed_Date) AS dia,
               COUNT(*) AS total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
        GROUP BY DATE(Registed_Date)
        ORDER BY DATE(Registed_Date)
    ");
}

// 3.2 Ensayos entregados por tipo
function resumen_tipo_semana($start_str, $end_str) {
    return find_by_sql("
        SELECT UPPER(TRIM(Test_Type)) AS Test_Type,
               COUNT(*) AS total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
        GROUP BY Test_Type
        ORDER BY total DESC
    ");
}

// 3.3 Cumplimiento semanal por cliente
function cumplimiento_cliente_semana($start_str, $end_str) {
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
        WHERE r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
        GROUP BY r.Client
        ORDER BY solicitados DESC
    ");
}

/* =====================================================
   4. CLASE PDF (Encabezado + estilos)
===================================================== */
class PDF_Weekly extends FPDF {

    public $week_iso;
    public $year_iso;

    function __construct($week, $year) {
        parent::__construct();
        $this->week_iso = $week;
        $this->year_iso = $year;
    }

    function Header() {

        if ($this->PageNo() > 1) return;

        if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
            $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 50);
        }

        $this->SetFont('Arial', 'B', 16);
        $this->SetXY(120, 12);
        $this->Cell(80, 8, 'WEEKLY LABORATORY REPORT', 0, 1, 'R');

        $week_start = new DateTime();
        $week_start->setISODate($this->year_iso, $this->week_iso, 1);

        $week_end = new DateTime();
        $week_end->setISODate($this->year_iso, $this->week_iso, 7);

        $range = $week_start->format('d M Y') . " - " . $week_end->format('d M Y');

        $this->SetFont('Arial', '', 11);
        $this->SetXY(120, 20);
        $this->Cell(80, 7, "ISO Week {$this->week_iso}  ({$range})", 0, 1, 'R');

        $this->Ln(15);
    }

    function section_title($title) {
        $this->SetFont('Arial', 'B', 13);
        $this->SetFillColor(220, 230, 255);
        $this->Cell(0, 9, utf8_decode($title), 0, 1, 'L', true);
        $this->Ln(3);
    }

    function table_header($cols, $widths) {
        $this->SetFont('Arial', 'B', 10);
        foreach ($cols as $i => $c) {
            $this->Cell($widths[$i], 7, utf8_decode($c), 1, 0, 'C');
        }
        $this->Ln();
    }

    function table_row($data, $widths) {
        $this->SetFont('Arial', '', 10);
        foreach ($data as $i => $d) {
            $this->Cell($widths[$i], 7, utf8_decode($d), 1, 0, 'C');
        }
        $this->Ln();
    }
}

$pdf = new PDF_Weekly($week_iso, $year_iso);
$pdf->AddPage();

/* =====================================================
   5. SECCIÓN 1 — WEEKLY SUMMARY
===================================================== */
$pdf->section_title("1. Weekly Summary of Activities");

$requisiciones = get_count("lab_test_requisition_form", "Registed_Date", $start_str, $end_str);
$prep           = get_count("test_preparation", "Register_Date", $start_str, $end_str);
$realiz         = get_count("test_realization", "Register_Date", $start_str, $end_str);
$entregas       = get_count("test_delivery", "Register_Date", $start_str, $end_str);

$pdf->table_header(["Activity", "Total"], [80, 30]);
$pdf->table_row(["Requisitioned",  $requisiciones], [80, 30]);
$pdf->table_row(["In Preparation", $prep], [80, 30]);
$pdf->table_row(["In Realization", $realiz], [80, 30]);
$pdf->table_row(["Completed",      $entregas], [80, 30]);

$pdf->Ln(10);

/* =====================================================
   6. SECCIÓN 2 — DAILY BREAKDOWN
===================================================== */
$pdf->section_title("2. Daily Breakdown (ISO Week)");

$data_dia = resumen_diario_semana($start_str, $end_str);

$pdf->table_header(["Date", "Samples Registered"], [60, 50]);

foreach ($data_dia as $row) {
    $pdf->table_row([
        date("D d-M", strtotime($row['dia'])),
        $row['total']
    ], [60, 50]);
}

$pdf->Ln(10);

/* =====================================================
   7. SECCIÓN 3 — TEST DISTRIBUTION BY TYPE
===================================================== */
$pdf->section_title("3. Test Distribution by Type");

$data_tipo = resumen_tipo_semana($start_str, $end_str);

$pdf->table_header(["Test Type", "Completed"], [80, 30]);

foreach ($data_tipo as $row) {
    $pdf->table_row([$row['Test_Type'], $row['total']], [80, 30]);
}

$pdf->Ln(10);
/* =====================================================
   8. GRÁFICOS — BARRAS FPDF
===================================================== */

// ---------- 8.1 Samples Per Day ----------
function chart_samples_per_day($pdf, $data_dia) {
    if (empty($data_dia)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 1: Samples Registered Per Day",0,1);

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartWidth = 170;
    $chartHeight = 50;

    $maxValue = 1;
    foreach ($data_dia as $r){
        if ($r['total'] > $maxValue) $maxValue = $r['total'];
    }

    // Ejes
    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartHeight);
    $pdf->Line($chartX,$chartY+$chartHeight,$chartX+$chartWidth,$chartY+$chartHeight);

    $numBars = count($data_dia);
    $barWidth = ($chartWidth - 20)/$numBars;
    $x = $chartX + 10;

    foreach ($data_dia as $r){
        $value = (int)$r['total'];
        $barHeight = ($value/$maxValue) * $chartHeight;
        $y = $chartY + $chartHeight - $barHeight;

        $pdf->SetFillColor(100,149,237);
        $pdf->Rect($x,$y,$barWidth-4,$barHeight,"F");

        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($x,$chartY+$chartHeight+2);
        $pdf->MultiCell($barWidth-4,3,date("D",strtotime($r['dia'])),0,'C');

        $x += $barWidth;
    }

    $pdf->Ln(10);
}

// ---------- 8.2 Test Distribution ----------
function chart_tests_by_type($pdf, $data_tipo) {
    if (empty($data_tipo)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 2: Test Distribution by Type",0,1);

    $chartX = 20;
    $chartY = $pdf->GetY()+5;
    $chartWidth = 170;
    $chartHeight = 50;

    $maxValue = 1;
    foreach ($data_tipo as $r){
        if ($r['total']>$maxValue) $maxValue = $r['total'];
    }

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartHeight);
    $pdf->Line($chartX,$chartY+$chartHeight,$chartX+$chartWidth,$chartY+$chartHeight);

    $numBars = count($data_tipo);
    $barWidth = ($chartWidth - 20) / $numBars;
    $x = $chartX + 10;

    foreach ($data_tipo as $r){

        $value = (int)$r['total'];
        $barHeight = ($value/$maxValue) * $chartHeight;
        $y = $chartY + $chartHeight - $barHeight;

        $pdf->SetFillColor(50,180,120);
        $pdf->Rect($x,$y,$barWidth-4,$barHeight,"F");

        $pdf->SetFont('Arial','',6);
        $pdf->SetXY($x,$chartY+$chartHeight+2);
        $pdf->MultiCell($barWidth-4,3,strtoupper($r['Test_Type']),0,'C');

        $x += $barWidth;
    }

    $pdf->Ln(10);
}

// ---------- 8.3 Client Completion ----------
function chart_client_completion($pdf, $clientes) {
    if (empty($clientes)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 3: Client Completion Percentage",0,1);

    $data = [];
    foreach ($clientes as $c) {
        $sol = (int)$c['solicitados'];
        $ent = (int)$c['entregados'];
        $pct = ($sol > 0) ? round(($ent*100)/$sol) : 0;

        $data[] = [
            "label" => strtoupper($c['Client']),
            "pct" => $pct
        ];
    }

    $chartX = 20;
    $chartY = $pdf->GetY()+5;
    $chartWidth = 170;
    $chartHeight = 50;

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartHeight);
    $pdf->Line($chartX,$chartY+$chartHeight,$chartX+$chartWidth,$chartY+$chartHeight);

    $numBars = count($data);
    $barWidth = ($chartWidth-20)/$numBars;
    $x = $chartX + 10;

    foreach ($data as $d){

        $pct = $d['pct'];
        $barHeight = ($pct/100) * $chartHeight;
        $y = $chartY + $chartHeight - $barHeight;

        $pdf->SetFillColor(255,165,0);
        $pdf->Rect($x,$y,$barWidth-4,$barHeight,"F");

        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY($x,$y-4);
        $pdf->Cell($barWidth-4,4,$pct."%",0,0,'C');

        $pdf->SetFont('Arial','',6);
        $pdf->SetXY($x,$chartY+$chartHeight+2);
        $pdf->MultiCell($barWidth-4,3,$d['label'],0,'C');

        $x += $barWidth;
    }

    $pdf->Ln(10);
}

/* =====================================================
   Ejecutar los 3 gráficos
===================================================== */
chart_samples_per_day($pdf,$data_dia);
chart_tests_by_type($pdf,$data_tipo);

$clientes_resumen = cumplimiento_cliente_semana($start_str,$end_str);
// ===== FIX PARA EVITAR QUE EL GRÁFICO SE DIVIDA EN PÁGINAS =====
$neededHeight = 95;  // Alto estimado del gráfico completo (barras + etiquetas)

if ($pdf->GetY() + $neededHeight > 250) {   // ← Límite seguro antes del footer
    $pdf->AddPage(); 
}

// Ahora sí dibujar el gráfico
chart_client_completion($pdf, $clientes_resumen);


$pdf->Ln(10);

/* =====================================================
   9. Sección 4 — Weekly Registered Samples
===================================================== */
$pdf->section_title("4. Newly Registered Samples (Weekly)");

$muestras = find_by_sql("
    SELECT Sample_ID, Sample_Number, Structure, Client, Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    ORDER BY Registed_Date ASC
");

$pdf->table_header(
    ["Sample ID","Structure","Client","Test Type"],
    [45,35,35,75]
);

foreach ($muestras as $m) {
    $pdf->table_row([
        $m["Sample_ID"]."-".$m["Sample_Number"],
        $m["Structure"],
        $m["Client"],
        $m["Test_Type"]
    ],
    [45,35,35,75]);
}

$pdf->Ln(8);

/* =====================================================
   10. Sección 5 — Summary of Tests by Technician
===================================================== */
$pdf->section_title("5. Summary of Tests by Technician (Weekly)");

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

$pdf->table_header(["Technician","Process","Qty"],[60,50,20]);

foreach ($tec as $t){
    $pdf->table_row([$t['Technician'],$t['etapa'],$t['total']],[60,50,20]);
}

$pdf->Ln(8);

/* =====================================================
   11. Sección 6 — Summary of Test Types
===================================================== */
$pdf->section_title("6. Summary of Tests by Type (Weekly)");

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

foreach ($tipos as $t){
    $pdf->table_row([$t['Test_Type'],$t['etapa'],$t['total']],[70,50,20]);
}

$pdf->Ln(8);

/* =====================================================
   12. Sección 7 — Pending Tests (Weekly)
===================================================== */
$pdf->section_title("7. Pending Tests (Weekly)");

$pendientes = find_by_sql("
    SELECT 
        r.Client,
        r.Sample_ID,
        r.Sample_Number,
        r.Test_Type,
        r.Sample_Date
    FROM lab_test_requisition_form r
    WHERE r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND NOT EXISTS (
          SELECT 1 FROM test_delivery d
          WHERE d.Sample_ID = r.Sample_ID
            AND d.Sample_Number = r.Sample_Number
            AND d.Test_Type = r.Test_Type
      )
");

$pdf->table_header(["Client","Sample ID","Sample No.","Test Type","Date"],[40,35,35,40,25]);

foreach ($pendientes as $p){
    $pdf->table_row([
        $p['Client'],
        $p['Sample_ID'],
        $p['Sample_Number'],
        $p['Test_Type'],
        date("d-M",strtotime($p['Sample_Date']))
    ],
    [40,35,35,40,25]);
}

$pdf->Ln(10);
/* =====================================================
   13. Sección 8 — Summary of Dam Construction Test
===================================================== */
$pdf->section_title("8. Summary of Dam Constructions Test");

$ensayos = find_by_sql("
    SELECT *
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$pdf->table_header(
    ["Sample","Structure","Mat.","Test Type","Condition","Comments"],
    [35,25,20,30,20,60]
);

foreach ($ensayos as $e){
    $pdf->table_row([
        $e['Sample_ID']."-".$e['Sample_Number'],
        $e['Structure'],
        $e['Material_Type'],
        $e['Test_Type'],
        $e['Test_Condition'],
        substr($e['Comments'],0,60)
    ],
    [35,25,20,30,20,60]);
}

$pdf->Ln(10);

/* =====================================================
   14. Sección 9 — NCR & Observations (Exacto diario)
===================================================== */
$pdf->section_title("9. Summary of Observations / Non-Conformities");

$ncr = find_by_sql("
    SELECT 
        Sample_ID,
        Sample_Number,
        Structure,
        Material_Type,
        Noconformidad
    FROM ensayos_reporte
    WHERE Noconformidad IS NOT NULL
      AND TRIM(Noconformidad) <> ''
      AND Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$pdf->table_header(["Sample","Observations"],[45,145]);

foreach ($ncr as $n){
    $sample = $n['Sample_ID']."-".$n['Sample_Number']."-".$n['Material_Type'];
    $pdf->table_row([$sample, substr($n['Noconformidad'],0,250)], [45,145]);
}

$pdf->Ln(10);

/* =====================================================
   15. Sección 10 — Responsible
===================================================== */
$pdf->section_title("10. Responsible");

$pdf->SetFont('Arial','',10);
$pdf->Cell(60,8,"Report prepared by",1);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Weekly_Laboratory_Report_Week{$week_iso}_{$year_iso}.pdf");
