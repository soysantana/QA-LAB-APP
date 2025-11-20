<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ============================================
// HELPER: Contar registros entre dos fechas
// ============================================
function get_count($table, $field, $start, $end) {
    $sql = "
        SELECT COUNT(*) AS total 
        FROM {$table}
        WHERE {$field} BETWEEN '{$start}' AND '{$end}'
    ";
    $res = find_by_sql($sql);
    return isset($res[0]['total']) ? (int)$res[0]['total'] : 0;
}

// ==============================
// 1. FECHA BASE (desde GET o hoy)
// ==============================
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Convertir a DateTime
$fecha_obj = new DateTime($fecha);

// ==============================
// 2. CÁLCULO DE SEMANA ISO
// ==============================
$week_iso = (int)$fecha_obj->format('W');
$year_iso = (int)$fecha_obj->format('o');

// Lunes de la semana ISO
$start = new DateTime();
$start->setISODate($year_iso, $week_iso, 1);  // 1 = lunes
$start_str = $start->format('Y-m-d 00:00:00');

// Domingo de la semana ISO
$end = new DateTime();
$end->setISODate($year_iso, $week_iso, 7);    // 7 = domingo
$end_str = $end->format('Y-m-d 23:59:59');

// Responsable
$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';


// =====================================================
// 3. CLASE PDF — Cabecera profesional estilo PVJ
// =====================================================
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

        // Subtítulo: semana ISO
        $this->SetFont('Arial', '', 11);
        $this->SetXY(120, 20);
        $this->Cell(80, 7, "ISO Week: {$this->week_iso} - {$this->year_iso}", 0, 1, 'R');

        $this->Ln(15);
    }

    function section_title($title) {
        $this->SetFont('Arial', 'B', 13);
        $this->SetFillColor(220, 230, 255);
        $this->Cell(0, 9, $title, 0, 1, 'L', true);
        $this->Ln(3);
    }

    function table_header($cols, $widths) {
        $this->SetFont('Arial', 'B', 10);
        foreach ($cols as $i => $c) {
            $this->Cell($widths[$i], 7, $c, 1, 0, 'C');
        }
        $this->Ln();
    }

    function table_row($data, $widths) {
        $this->SetFont('Arial', '', 10);
        foreach ($data as $i => $d) {
            $this->Cell($widths[$i], 7, $d, 1, 0, 'C');
        }
        $this->Ln();
    }
}

// Crear PDF
$pdf = new PDF_Weekly($week_iso, $year_iso);
$pdf->AddPage();
// =====================================================
// 4. FUNCIONES DE CONSULTA PARA EL REPORTE SEMANAL
// =====================================================

// ----------- 4.1 Total de ensayos/muestras por día -----------
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

// ----------- 4.2 Ensayos por tipo en la semana -----------
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

// ----------- 4.3 % de cumplimiento por cliente -----------
function cumplimiento_cliente_semana($start_str, $end_str) {
    return find_by_sql("
        SELECT 
            UPPER(TRIM(r.Client)) AS Client,
            COUNT(*) AS solicitados,
            SUM(CASE 
                    WHEN d.id IS NOT NULL 
                    THEN 1 
                    ELSE 0 
                END) AS entregados
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


// =====================================================
// 5. SECCIÓN: RESUMEN DE ACTIVIDADES SEMANALES
// =====================================================
$pdf->section_title("1. Weekly Summary of Activities");

$requisiciones = get_count("lab_test_requisition_form", "Registed_Date", $start_str, $end_str);
$prep           = get_count("test_preparation",          "Register_Date", $start_str, $end_str);
$realiz         = get_count("test_realization",          "Register_Date", $start_str, $end_str);
$entregas       = get_count("test_delivery",             "Register_Date", $start_str, $end_str);

$pdf->table_header(["Activity", "Total"], [70, 30]);
$pdf->table_row(["Requisitioned", $requisiciones], [70, 30]);
$pdf->table_row(["In Preparation", $prep], [70, 30]);
$pdf->table_row(["In Realization", $realiz], [70, 30]);
$pdf->table_row(["Completed", $entregas], [70, 30]);
$pdf->Ln(6);


// =====================================================
// 6. SECCIÓN: MUESTRAS POR DÍA DE LA SEMANA (Lun → Dom)
// =====================================================
$pdf->section_title("2. Daily Breakdown (ISO Week)");

$data_dia = resumen_diario_semana($start_str, $end_str);

$pdf->table_header(["Date", "Samples Registered"], [50, 50]);

foreach ($data_dia as $row) {
    $pdf->table_row([
        date("D d-M", strtotime($row['dia'])),
        $row['total']
    ], [50, 50]);
}

$pdf->Ln(8);


// =====================================================
// 7. SECCIÓN: DISTRIBUCIÓN DE ENSAYOS POR TIPO
// =====================================================
$pdf->section_title("3. Test Distribution by Type");

$data_tipo = resumen_tipo_semana($start_str, $end_str);

$pdf->table_header(["Test Type", "Completed"], [70, 30]);

foreach ($data_tipo as $row) {
    $pdf->table_row([$row['Test_Type'], $row['total']], [70, 30]);
}

$pdf->Ln(8);


// =====================================================
// 8. SECCIÓN: CUMPLIMIENTO POR CLIENTE (Weekly KPI)
// =====================================================
$pdf->section_title("4. Client Completion Summary");

$clientes = cumplimiento_cliente_semana($start_str, $end_str);

// Encabezado
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
// =====================================================
// 9. GRAFICOS DEL REPORTE SEMANAL (FPDF PURO)
// =====================================================

// ======= 9.1 Grafico: Samples per Day (Bar Chart) =======
function chart_samples_per_day($pdf, $data_dia) {
    if (empty($data_dia)) return;

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, "Graph 1: Samples Registered Per Day", 0, 1);

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartWidth = 170;
    $chartHeight = 50;

    // Encontrar máximo
    $maxValue = 0;
    foreach ($data_dia as $row) {
        if ($row['total'] > $maxValue) $maxValue = $row['total'];
    }
    if ($maxValue == 0) $maxValue = 1;

    // Ejes
    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);
    $pdf->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

    // Barras
    $numBars = count($data_dia);
    $barWidth = ($chartWidth - 20) / $numBars;
    $x = $chartX + 10;

    foreach ($data_dia as $row) {
        $value = (int)$row['total'];
        $barHeight = ($value / $maxValue) * $chartHeight;

        $y = $chartY + $chartHeight - $barHeight;

        // Color
        $pdf->SetFillColor(100, 149, 237);

        $pdf->Rect($x, $y, $barWidth - 4, $barHeight, "F");

        // Etiqueta
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($x, $chartY + $chartHeight + 2);
        $pdf->MultiCell($barWidth - 4, 3, date("D", strtotime($row['dia'])), 0, 'C');

        $x += $barWidth;
    }

    $pdf->Ln(60);
}


// ======= 9.2 Graph: Test Distribution by Type =======
function chart_tests_by_type($pdf, $data_tipo) {
    if (empty($data_tipo)) return;

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, "Graph 2: Test Distribution by Type", 0, 1);

    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartWidth = 170;
    $chartHeight = 50;

    // Maximo
    $maxValue = 1;
    foreach ($data_tipo as $row) {
        if ($row['total'] > $maxValue) $maxValue = $row['total'];
    }

    // Ejes
    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);
    $pdf->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

    $numBars = count($data_tipo);
    $barWidth = ($chartWidth - 20) / $numBars;
    $x = $chartX + 10;

    foreach ($data_tipo as $row) {
        $value = (int)$row['total'];
        $barHeight = ($value / $maxValue) * $chartHeight;

        $y = $chartY + $chartHeight - $barHeight;

        // Color
        $pdf->SetFillColor(50, 180, 120);

        $pdf->Rect($x, $y, $barWidth - 4, $barHeight, "F");

        // Etiqueta
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY($x, $chartY + $chartHeight + 2);
        $pdf->MultiCell($barWidth - 4, 3, strtoupper($row['Test_Type']), 0, 'C');

        $x += $barWidth;
    }

    $pdf->Ln(60);
}


// ======= 9.3 Graph: Client Completion (%) =======
function chart_client_completion($pdf, $clientes) {
    if (empty($clientes)) return;

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, "Graph 3: Client Completion Percentage", 0, 1);

    // Transformar datos
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

    // Área del gráfico
    $chartX = 20;
    $chartY = $pdf->GetY() + 5;
    $chartWidth = 170;
    $chartHeight = 50;

    // Maximo
    $maxValue = 100;

    // Ejes
    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);
    $pdf->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

    // Barras
    $numBars = count($data);
    $barWidth = ($chartWidth - 20) / $numBars;
    $x = $chartX + 10;

    foreach ($data as $row) {
        $pct = $row['pct'];
        $barHeight = ($pct / $maxValue) * $chartHeight;

        $y = $chartY + $chartHeight - $barHeight;

        // Color
        $pdf->SetFillColor(255, 165, 0); // naranja

        $pdf->Rect($x, $y, $barWidth - 4, $barHeight, "F");

        // Etiquetas
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetXY($x, $y - 4);
        $pdf->Cell($barWidth - 4, 4, $pct . "%", 0, 0, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY($x, $chartY + $chartHeight + 2);
        $pdf->MultiCell($barWidth - 4, 3, $row['label'], 0, 'C');

        $x += $barWidth;
    }

    $pdf->Ln(60);
}


// =====================================================
// LLAMADO A LOS GRAFICOS (después de las tablas)
// =====================================================

chart_samples_per_day($pdf, $data_dia);
chart_tests_by_type($pdf, $data_tipo);
chart_client_completion($pdf, $clientes);
// =====================================================
// 10. NCR – NON-CONFORMITIES (WEEKLY)
// =====================================================
$pdf->section_title("5. Weekly Non-Conformities (NCR)");

$ncr_semana = find_by_sql("
    SELECT 
        Sample_ID,
        Sample_Number,
        Structure,
        Material_Type,
        Noconformidad,
        Report_Date
    FROM ensayos_reporte
    WHERE 
        Noconformidad IS NOT NULL 
        AND TRIM(Noconformidad) != ''
        AND Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
    ORDER BY Report_Date DESC
");

if (empty($ncr_semana)) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 8, "No NCR reported during this week.", 0, 1);
} else {
    $pdf->table_header(
        ["Sample", "Material", "NCR", "Date"],
        [40, 30, 80, 20]
    );

    foreach ($ncr_semana as $n) {
        $pdf->table_row(
            [
                $n['Sample_ID'] . "-" . $n['Sample_Number'],
                $n['Material_Type'],
                substr($n['Noconformidad'], 0, 120),
                date("d-M", strtotime($n['Report_Date']))
            ],
            [40, 30, 80, 20]
        );
    }
}

$pdf->Ln(8);


// =====================================================
// 11. PENDING TESTS (WEEKLY)
// =====================================================
$pdf->section_title("6. Pending Tests (Weekly)");

$pendientes_semana = find_by_sql("
    SELECT 
        r.Sample_ID,
        r.Sample_Number,
        r.Test_Type,
        r.Sample_Date
    FROM lab_test_requisition_form r
    WHERE 
        r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
        AND NOT EXISTS (
            SELECT 1 
            FROM test_delivery d
            WHERE d.Sample_ID = r.Sample_ID
              AND d.Sample_Number = r.Sample_Number
              AND d.Test_Type = r.Test_Type
        )
        AND LOWER(r.Test_Type) NOT LIKE '%envio%'
    ORDER BY r.Sample_Date DESC
");

if (empty($pendientes_semana)) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 8, "No pending tests for this week.", 0, 1);
} else {
    $pdf->table_header(
        ["Sample ID", "Sample No.", "Test Type", "Date"],
        [40, 40, 60, 20]
    );

    foreach ($pendientes_semana as $p) {
        $pdf->table_row(
            [
                $p['Sample_ID'],
                $p['Sample_Number'],
                strtoupper($p['Test_Type']),
                date("d-M", strtotime($p['Sample_Date']))
            ],
            [40, 40, 60, 20]
        );
    }
}

$pdf->Ln(8);


// =====================================================
// 12. GENERAL OBSERVATIONS
// =====================================================
$pdf->section_title("7. General Observations");

$observaciones_semana = find_by_sql("
    SELECT Comments, Report_Date
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
    ORDER BY Report_Date DESC
");

if (empty($observaciones_semana)) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, "No general observations were recorded this week.", 0, 1);
} else {
    foreach ($observaciones_semana as $obs) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 5, date("d-M", strtotime($obs['Report_Date'])) . ":", 0, 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 5, "- " . $obs['Comments'], 0, 'L');
        $pdf->Ln(1);
    }
}

$pdf->Ln(10);


// =====================================================
// 13. SIGNATURES & APPROVALS
// =====================================================
$pdf->section_title("8. Signatures & Approvals");

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, "Prepared by:", 1);
$pdf->Cell(120, 8, $user['name'], 1, 1);

$pdf->Cell(60, 8, "Reviewed by:", 1);
$pdf->Cell(120, 8, "__________________________", 1, 1);

$pdf->Cell(60, 8, "Approved by:", 1);
$pdf->Cell(120, 8, "__________________________", 1, 1);

$pdf->Ln(15);


// =====================================================
// 14. OUTPUT FINAL DEL PDF
// =====================================================
$pdf->Output("I", "Weekly_Laboratory_Report_Week{$week_iso}_{$year_iso}.pdf");
