<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/*=======================================================
  1. HELPER: Contar registros entre fechas
========================================================*/
function get_count($table, $field, $start, $end) {
    $sql = "
        SELECT COUNT(*) AS total 
        FROM {$table}
        WHERE {$field} BETWEEN '{$start}' AND '{$end}'
    ";
    $res = find_by_sql($sql);
    return isset($res[0]['total']) ? (int)$res[0]['total'] : 0;
}

/*=======================================================
  2. FECHA BASE & SEMANA ISO
========================================================*/
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha_obj = new DateTime($fecha);

$week_iso = (int)$fecha_obj->format('W');
$year_iso = (int)$fecha_obj->format('o');

// Lunes de semana ISO
$start = new DateTime();
$start->setISODate($year_iso, $week_iso, 1);
$start_str = $start->format('Y-m-d 00:00:00');

// Domingo de semana ISO
$end = new DateTime();
$end->setISODate($year_iso, $week_iso, 7);
$end_str = $end->format('Y-m-d 23:59:59');

// Responsable
$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/*=======================================================
  3. CLASE PDF
========================================================*/
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

        // Logo
        if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
            $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 50);
        }

        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->SetXY(120, 12);
        $this->Cell(80, 8, 'WEEKLY LABORATORY REPORT', 0, 1, 'R');

        // Rango de fechas
        $week_start = new DateTime();
        $week_start->setISODate($this->year_iso, $this->week_iso, 1);

        $week_end = new DateTime();
        $week_end->setISODate($this->year_iso, $this->week_iso, 7);

        $range = $week_start->format('d M Y') . " - " . $week_end->format('d M Y');

        $this->SetFont('Arial', '', 11);
        $this->SetXY(120, 20);
        $this->Cell(80, 7, utf8_decode("ISO Week {$this->week_iso}  ($range)"), 0, 1, 'R');

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

// Crear PDF
$pdf = new PDF_Weekly($week_iso, $year_iso);
$pdf->AddPage();

/*=======================================================
  4. FUNCIONES SQL DEL REPORTE SEMANAL
========================================================*/

// 4.1 Muestras registradas por día
function resumen_diario_semana($start_str, $end_str) {
    return find_by_sql("
        SELECT 
            DATE(Registed_Date) AS dia,
            COUNT(*) AS total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
        GROUP BY DATE(Registed_Date)
        ORDER BY DATE(Registed_Date)
    ");
}

// 4.2 Ensayos por tipo entregados en la semana
function resumen_tipo_semana($start_str, $end_str) {
    return find_by_sql("
        SELECT 
            UPPER(TRIM(Test_Type)) AS Test_Type,
            COUNT(*) AS total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
        GROUP BY Test_Type
        ORDER BY total DESC
    ");
}

// 4.3 Cumplimiento por cliente
function cumplimiento_cliente_semana($start_str, $end_str) {
    return find_by_sql("
        SELECT 
            UPPER(TRIM(r.Client)) AS Client,
            COUNT(*) AS solicitados,
            SUM(
                CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END
            ) AS entregados
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

/*=======================================================
  5. SECCIÓN 1 — RESUMEN SEMANAL
========================================================*/
$pdf->section_title("1. Weekly Summary of Activities");

$requisiciones = get_count("lab_test_requisition_form", "Registed_Date", $start_str, $end_str);
$prep           = get_count("test_preparation", "Register_Date", $start_str, $end_str);
$realiz         = get_count("test_realization", "Register_Date", $start_str, $end_str);
$entregas       = get_count("test_delivery", "Register_Date", $start_str, $end_str);

$pdf->table_header(["Activity", "Total"], [80, 30]);
$pdf->table_row(["Requisitioned", $requisiciones], [80, 30]);
$pdf->table_row(["In Preparation", $prep], [80, 30]);
$pdf->table_row(["In Realization", $realiz], [80, 30]);
$pdf->table_row(["Completed", $entregas], [80, 30]);

$pdf->Ln(8);

/*=======================================================
  6. SECCIÓN 2 — Muestras Registradas Por Día
========================================================*/
$pdf->section_title("2. Daily Breakdown (ISO Week)");

$data_dia = resumen_diario_semana($start_str, $end_str);

$pdf->table_header(["Date", "Samples Registered"], [60, 50]);

foreach ($data_dia as $row) {
    $pdf->table_row([
        date("D d-M", strtotime($row['dia'])),
        $row['total']
    ], [60, 50]);
}

$pdf->Ln(8);

/*=======================================================
  7. SECCIÓN 3 — Distribución de Ensayos por Tipo
========================================================*/
$pdf->section_title("3. Test Distribution by Type");

$data_tipo = resumen_tipo_semana($start_str, $end_str);

$pdf->table_header(["Test Type", "Completed"], [80, 30]);

foreach ($data_tipo as $row) {
    $pdf->table_row([$row['Test_Type'], $row['total']], [80, 30]);
}

$pdf->Ln(8);

/*=======================================================
  8. SECCIÓN 4 — KPI por Cliente
========================================================*/
$pdf->section_title("4. Client Completion Summary");

$clientes = cumplimiento_cliente_semana($start_str, $end_str);

$pdf->table_header(["Client", "Requested", "Delivered", "%"], [60, 30, 30, 20]);

foreach ($clientes as $c) {
    $sol = (int)$c['solicitados'];
    $ent = (int)$c['entregados'];
    $pct = ($sol > 0) ? round(($ent * 100) / $sol, 1) : 0;

    $pdf->table_row([
        $c['Client'],
        $sol,
        $ent,
        $pct . "%"
    ], [60, 30, 30, 20]);
}

$pdf->Ln(10);

/*=======================================================
  9. GRÁFICOS DEL REPORTE SEMANAL (FPDF PURO)
========================================================*/

/*-------------- 9.1 Samples per Day --------------*/
function chart_samples_per_day($pdf, $data_dia) {
    if (empty($data_dia)) return;

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, "Graph 1: Samples Registered Per Day", 0, 1);

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartWidth = 170;
    $chartHeight = 50;

    $maxValue = 0;
    foreach ($data_dia as $row) {
        if ($row['total'] > $maxValue) $maxValue = $row['total'];
    }
    if ($maxValue == 0) $maxValue = 1;

    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);
    $pdf->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

    $numBars = count($data_dia);
    $barWidth = ($chartWidth - 20) / $numBars;
    $x = $chartX + 10;

    foreach ($data_dia as $row) {

        $value = (int)$row['total'];
        $barHeight = ($value / $maxValue) * $chartHeight;

        $y = $chartY + $chartHeight - $barHeight;

        $pdf->SetFillColor(100, 149, 237);
        $pdf->Rect($x, $y, $barWidth - 4, $barHeight, "F");

        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($x, $chartY + $chartHeight + 2);
        $pdf->MultiCell($barWidth - 4, 3, date("D", strtotime($row['dia'])), 0, 'C');

        $x += $barWidth;
    }

    $pdf->Ln(40);
}

/*-------------- 9.2 Test Distribution --------------*/
function chart_tests_by_type($pdf, $data_tipo) {
    if (empty($data_tipo)) return;

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, "Graph 2: Test Distribution by Type", 0, 1);

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartWidth = 170;
    $chartHeight = 50;

    $maxValue = 1;
    foreach ($data_tipo as $row) {
        if ($row['total'] > $maxValue) $maxValue = $row['total'];
    }

    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);
    $pdf->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

    $numBars = count($data_tipo);
    $barWidth = ($chartWidth - 20) / $numBars;
    $x = $chartX + 10;

    foreach ($data_tipo as $row) {

        $value = (int)$row['total'];
        $barHeight = ($value / $maxValue) * $chartHeight;

        $y = $chartY + $chartHeight - $barHeight;

        $pdf->SetFillColor(50, 180, 120);
        $pdf->Rect($x, $y, $barWidth - 4, $barHeight, "F");

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY($x, $chartY + $chartHeight + 2);
        $pdf->MultiCell($barWidth - 4, 3, strtoupper($row['Test_Type']), 0, 'C');

        $x += $barWidth;
    }

    $pdf->Ln(40);
}

/*-------------- 9.3 Client Completion Graph --------------*/
function chart_client_completion($pdf, $clientes) {
    if (empty($clientes)) return;

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, "Graph 3: Client Completion Percentage", 0, 1);

    $data = [];
    foreach ($clientes as $c) {
        $sol = (int)$c['solicitados'];
        $ent = (int)$c['entregados'];
        $pct = ($sol > 0) ? round(($ent * 100) / $sol) : 0;

        $data[] = [
            "label" => strtoupper($c['Client']),
            "pct"   => $pct
        ];
    }

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartWidth = 170;
    $chartHeight = 50;

    $maxValue = 100;

    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);
    $pdf->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

    $numBars = count($data);
    $barWidth = ($chartWidth - 20) / $numBars;
    $x = $chartX + 10;

    foreach ($data as $row) {

        $pct = $row['pct'];
        $barHeight = ($pct / 100) * $chartHeight;

        $y = $chartY + $chartHeight - $barHeight;

        $pdf->SetFillColor(255, 165, 0);
        $pdf->Rect($x, $y, $barWidth - 4, $barHeight, "F");

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetXY($x, $y - 4);
        $pdf->Cell($barWidth - 4, 4, $pct . "%", 0, 0, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY($x, $chartY + $chartHeight + 2);
        $pdf->MultiCell($barWidth - 4, 3, $row['label'], 0, 'C');

        $x += $barWidth;
    }

    $pdf->Ln(40);
}

/*------------- EJECUCIÓN DE GRÁFICOS -------------*/
chart_samples_per_day($pdf, $data_dia);
chart_tests_by_type($pdf, $data_tipo);
chart_client_completion($pdf, $clientes);

// =====================================================
// 5. WEEKLY NCR & GENERAL OBSERVATIONS (Unified Section)
// =====================================================
$pdf->section_title("5. Weekly NCR & General Observations");

// Traer NCR + comentarios unidos con cliente
$ncr_obs = find_by_sql("
    SELECT 
        r.Client,
        e.Sample_ID,
        e.Sample_Number,
        e.Structure,
        e.Material_Type,
        e.Noconformidad,
        e.Comments,
        e.Report_Date
    FROM ensayos_reporte e
    LEFT JOIN lab_test_requisition_form r
        ON r.Sample_ID = e.Sample_ID
       AND r.Sample_Number = e.Sample_Number
    WHERE 
        (
            (e.Noconformidad IS NOT NULL AND TRIM(e.Noconformidad) <> '')
            OR 
            (e.Comments IS NOT NULL AND TRIM(e.Comments) <> '')
        )
        AND e.Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
    ORDER BY e.Report_Date DESC
");

if (empty($ncr_obs)) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 8, "No NCR or observations reported this week.", 0, 1);
} else {

    // Cabeceras
    $pdf->table_header(
        ["Client", "Sample", "Material", "Observation / NCR", "Date"],
        [25, 25, 25, 105, 20]
    );

    // Definir anchos
    $wClient = 25;
    $wSample = 25;
    $wMat    = 25;
    $wObs    = 105;
    $wDate   = 20;

    foreach ($ncr_obs as $n) {

        // Combinar texto NCR + OBS
        $texto = "";
        if (!empty($n['Noconformidad'])) {
            $texto .= "NCR: " . utf8_decode($n['Noconformidad']) . "\n";
        }
        if (!empty($n['Comments'])) {
            $texto .= "OBS: " . utf8_decode($n['Comments']);
        }

        // Guardar posición inicial de la fila
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Dibujar columnas fijas
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell($wClient, 6, utf8_decode($n['Client'] ?: 'N/A'), 1);
        $pdf->Cell($wSample, 6, $n['Sample_ID']."-".$n['Sample_Number'], 1);
        $pdf->Cell($wMat, 6, utf8_decode($n['Material_Type']), 1);

        // MultiCell para Observación / NCR
        $pdf->MultiCell($wObs, 6, $texto, 1);

        // Volver a la derecha donde va la fecha
        $pdf->SetXY($x + $wClient + $wSample + $wMat + $wObs, $y);
        $pdf->Cell($wDate, 6, date("d-M", strtotime($n['Report_Date'])), 1);

        // Mover cursor debajo de la fila completa
        $pdf->Ln();
    }
}

$pdf->Ln(6);

/*=======================================================
  11. PENDING TESTS (WEEKLY)
========================================================*/
$pdf->section_title("6. Pending Tests (Weekly)");

$pendientes_semana = find_by_sql("
    SELECT 
        r.Sample_ID,
        r.Sample_Number,
        r.Test_Type,
        r.Sample_Date,
        r.Client
    FROM lab_test_requisition_form r
    WHERE 
        r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
        AND LOWER(r.Test_Type) NOT LIKE '%envio%'
        AND NOT EXISTS (
            SELECT 1 
            FROM test_delivery d
            WHERE d.Sample_ID = r.Sample_ID
              AND d.Sample_Number = r.Sample_Number
              AND d.Test_Type = r.Test_Type
        )
    ORDER BY r.Sample_Date DESC
");

if (empty($pendientes_semana)) {

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 8, "No pending tests for this week.", 0, 1);

} else {

    $pdf->table_header(
        ["Client", "Sample ID", "Sample No.", "Test Type", "Date"],
        [40, 35, 35, 50, 20]
    );

    foreach ($pendientes_semana as $p) {
        $pdf->table_row(
            [
                $p['Client'],
                $p['Sample_ID'],
                $p['Sample_Number'],
                strtoupper($p['Test_Type']),
                date("d-M", strtotime($p['Sample_Date']))
            ],
            [40, 35, 35, 50, 20]
        );
    }
}

$pdf->Ln(10);

/*=======================================================
  12. SIGNATURES & APPROVALS
========================================================*/
$pdf->section_title("7. Signatures & Approvals");

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, "Prepared by:", 1);
$pdf->Cell(120, 8, utf8_decode($user['name']), 1, 1);

/*=======================================================
  13. OUTPUT FINAL PDF
========================================================*/
$pdf->Output("I", "Weekly_Laboratory_Report_Week{$week_iso}_{$year_iso}.pdf");
