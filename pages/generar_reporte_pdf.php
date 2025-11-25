<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$user = current_user();
$nombre_responsable = $user['name'];

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('Y/m/d') : 'Invalid date';

// Rango de trabajo (16:00 día anterior → 15:59 del día actual)
$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 15:59:59"));
$end   = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

/* ----------------------------------------
   SAFE TEXT FOR FPDF
---------------------------------------- */
function pdf_text_safe($txt)
{
    $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $txt);
    if ($converted === false) return utf8_decode($txt);
    return $converted;
}

/* ----------------------------------------
   HELPERS
---------------------------------------- */
function get_count($table, $field, $start, $end)
{
    $r = find_by_sql("SELECT COUNT(*) total FROM {$table} WHERE {$field} BETWEEN '{$start}' AND '{$end}'");
    return (int)$r[0]['total'];
}

function es_envio_tt($s)
{
    $t = mb_strtolower($s, 'UTF-8');
    return preg_match('/(^|[,\s\/\-\|;])env[ií]os?($|[,\s\/\-\|;])/u', $t);
}

/* ----------------------------------------
   RESUMEN POR CLIENTE
---------------------------------------- */
function resumen_entregas_por_cliente($end)
{
    $stats = [];
    $inicio = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($end)));

    $sol = find_by_sql("
        SELECT Client, Sample_ID, Sample_Number, Test_Type
        FROM lab_test_requisition_form
        WHERE Sample_Date BETWEEN '{$inicio}' AND '{$end}'
    ");

    $ent = find_by_sql("
        SELECT Sample_ID, Sample_Number, Test_Type
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$inicio}' AND '{$end}'
    ");

    $map = [];
    foreach ($ent as $e) {
        $sid = strtoupper(trim($e['Sample_ID']));
        $sno = strtoupper(trim($e['Sample_Number']));
        $types = preg_split('/[;,]+/', $e['Test_Type']);

        foreach ($types as $t) {
            $t = strtoupper(trim($t));
            if ($t === '' || es_envio_tt($t)) continue;
            $map["$sid|$sno|$t"] = true;
        }
    }

    foreach ($sol as $s) {
        $cli = strtoupper(trim($s['Client'])) ?: 'UNKNOWN';
        if (!isset($stats[$cli])) {
            $stats[$cli] = ['solicitados' => 0, 'entregados' => 0, 'pct' => 0];
        }

        $sid = strtoupper(trim($s['Sample_ID']));
        $sno = strtoupper(trim($s['Sample_Number']));
        $types = preg_split('/[;,]+/', $s['Test_Type']);

        foreach ($types as $t) {
            $t = strtoupper(trim($t));
            if ($t === '' || es_envio_tt($t)) continue;

            $stats[$cli]['solicitados']++;

            $key = "$sid|$sno|$t";
            if (isset($map[$key])) $stats[$cli]['entregados']++;
        }
    }

    foreach ($stats as &$s) {
        $s['pct'] = $s['solicitados'] > 0
            ? round($s['entregados'] * 100 / $s['solicitados'])
            : 0;
    }

    return $stats;
}

/* ----------------------------------------
   MUESTRAS NUEVAS
---------------------------------------- */
function muestras_nuevas($start, $end)
{
    $rows = find_by_sql("
        SELECT Sample_ID, Sample_Number, Structure, Client, Test_Type, Registed_Date
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
          AND NOT (LOWER(CONVERT(Test_Type USING utf8)) LIKE '%envio%')
        ORDER BY Registed_Date ASC
    ");

    $out = [];
    foreach ($rows as $r) {
        $types = preg_split('/[;,]+/', $r['Test_Type']);
        $clean = [];
        foreach ($types as $t) {
            $t = trim($t);
            if ($t === '') continue;
            if (es_envio_tt($t)) continue;
            $clean[] = $t;
        }
        if (empty($clean)) continue;

        $r['Test_Type'] = implode(', ', $clean);
        $out[] = $r;
    }
    return $out;
}

/* ----------------------------------------
   PENDIENTES
---------------------------------------- */
function get_columns_for_table($tabla)
{
    global $db;
    $cols = [];
    $res = $db->query("SHOW COLUMNS FROM {$tabla}");
    while ($row = $res->fetch_assoc()) $cols[] = $row['Field'];
    return $cols;
}

function ensayos_pendientes($start, $end)
{
    $req = find_by_sql("
        SELECT Sample_ID, Sample_Number, Test_Type, Sample_Date
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $tablas = [
        'test_preparation',
        'test_realization',
        'test_delivery',
        'test_review',
        'test_reviewed',
        'test_repeat',
        'doc_files'
    ];

    $index = [];

    foreach ($tablas as $t) {
        $cols = get_columns_for_table($t);
        $datos = find_by_sql("
            SELECT Sample_ID, Sample_Number, Test_Type
            FROM {$t}
        ");
        foreach ($datos as $d) {
            $key = strtoupper(trim($d['Sample_ID'])) . '|' .
                strtoupper(trim($d['Sample_Number'])) . '|' .
                strtoupper(trim($d['Test_Type']));
            $index[$key] = true;
        }
    }

    $pend = [];
    foreach ($req as $r) {
        $sid = strtoupper(trim($r['Sample_ID']));
        $sno = strtoupper(trim($r['Sample_Number']));
        $types = preg_split('/[;,]+/', $r['Test_Type']);

        foreach ($types as $t) {
            $t = strtoupper(trim($t));
            if ($t === '' || strpos($t, 'ENVIO') !== false) continue;

            $key = "$sid|$sno|$t";
            if (!isset($index[$key])) {
                $pend[] = [
                    'Sample_ID'     => $r['Sample_ID'],
                    'Sample_Number' => $r['Sample_Number'],
                    'Test_Type'     => $t,
                    'Sample_Date'   => $r['Sample_Date']
                ];
            }
        }
    }

    return $pend;
}

/* ----------------------------------------
   RESUMEN POR TÉCNICO / TIPO
---------------------------------------- */
function resumen_tecnico($start, $end)
{
    return find_by_sql("
        SELECT Technician, COUNT(*) total, 'Preparation' etapa
        FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Technician

        UNION ALL
        SELECT Technician, COUNT(*) total, 'Realization'
        FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Technician

        UNION ALL
        SELECT Technician, COUNT(*) total, 'Completed'
        FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Technician
    ");
}

function resumen_tipo($start, $end)
{
    return find_by_sql("
        SELECT Test_Type, COUNT(*) total, 'Preparation' etapa
        FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type

        UNION ALL
        SELECT Test_Type, COUNT(*) total, 'Realization'
        FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type

        UNION ALL
        SELECT Test_Type, COUNT(*) total, 'Completed'
        FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type
    ");
}

/* ----------------------------------------
   GRÁFICO DE BARRAS CLIENTES
---------------------------------------- */
function draw_client_bar_chart($pdf, array $clientes)
{
    if (empty($clientes)) return;

    $data = [];
    foreach ($clientes as $cli => $d) {
        $lbl = strtoupper(trim($cli));
        if (mb_strlen($lbl, 'UTF-8') > 10) {
            $lbl = mb_substr($lbl, 0, 10, 'UTF-8') . '…';
        }

        $pct = $d['solicitados'] > 0
            ? round($d['entregados'] * 100 / $d['solicitados'])
            : 0;

        $data[] = ['label' => pdf_text_safe($lbl), 'pct' => $pct];
    }

    $maxPct = max(array_column($data, 'pct'));
    if ($maxPct <= 0) return;

    if ($pdf->GetY() + 80 > 260) $pdf->AddPage();

    $x0 = $pdf->GetX();
    $y0 = $pdf->GetY() + 5;
    $chartW = 180;
    $chartH = 45;
    $gap = 5;
    $barW = (180 - (count($data) + 1) * $gap) / max(1, count($data));
    if ($barW < 10) $barW = 10;

    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Line($x0, $y0, $x0, $y0 + $chartH);
    $pdf->Line($x0, $y0 + $chartH, $x0 + $chartW, $y0 + $chartH);

    $steps = [0, 25, 50, 75, 100];
    foreach ($steps as $s) {
        if ($s > $maxPct) continue;
        $y = $y0 + $chartH - ($s * $chartH / $maxPct);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line($x0, $y, $x0 + $chartW, $y);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($x0 - 10, $y - 2);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(10, 4, "$s%", 0, 0, 'R');
    }

    $pdf->SetFont('Arial', '', 8);
    $i = 0;
    foreach ($data as $d) {
        $pct = $d['pct'];
        $lbl = $d['label'];

        $h = $pct * $chartH / $maxPct;
        $x = $x0 + $gap + $i * ($barW + $gap);
        $y = $y0 + $chartH - $h;

        $pdf->SetFillColor(100, 149, 237);
        $pdf->Rect($x, $y, $barW, $h, 'F');

        $pdf->SetXY($x, $y - 4);
        $pdf->Cell($barW, 4, "$pct%", 0, 0, 'C');

        $pdf->SetXY($x, $y0 + $chartH + 2);
        $pdf->MultiCell($barW, 4, $lbl, 0, 'C');

        $i++;
    }

    $pdf->SetY($y0 + $chartH + 20);
}

/* ----------------------------------------
   ENSAYOS REPORTE
---------------------------------------- */
function render_ensayos_reporte($pdf, $start, $end)
{
    $rows = find_by_sql("SELECT * FROM ensayos_reporte WHERE Report_Date BETWEEN '{$start}' AND '{$end}'");

    $pdf->section_title("8. Summary of Dam Constructions Test");

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(40, 8, 'Sample', 1);
    $pdf->Cell(25, 8, 'Structure', 1);
    $pdf->Cell(20, 8, 'Mat Type', 1);
    $pdf->Cell(30, 8, 'Test Type', 1);
    $pdf->Cell(20, 8, 'Condition', 1);
    $pdf->Cell(55, 8, 'Comments', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 9);
    foreach ($rows as $r) {
        $pdf->Cell(40, 8, pdf_text_safe($r['Sample_ID'] . '-' . $r['Sample_Number']), 1);
        $pdf->Cell(25, 8, pdf_text_safe($r['Structure']), 1);
        $pdf->Cell(20, 8, pdf_text_safe($r['Material_Type']), 1);
        $pdf->Cell(30, 8, pdf_text_safe($r['Test_Type']), 1);
        $pdf->Cell(20, 8, pdf_text_safe($r['Test_Condition']), 1);
        $pdf->Cell(55, 8, pdf_text_safe(substr($r['Comments'], 0, 50)), 1);
        $pdf->Ln();
    }
}

/* ----------------------------------------
   OBSERVACIONES
---------------------------------------- */
function observaciones_ensayos_reporte($start, $end)
{
    return find_by_sql("
        SELECT Sample_ID, Sample_Number, Structure, Material_Type, Noconformidad
        FROM ensayos_reporte
        WHERE Noconformidad IS NOT NULL
          AND TRIM(Noconformidad) != ''
          AND Report_Date BETWEEN '{$start}' AND '{$end}'
    ");
}

/* ----------------------------------------
   CLASE PDF
---------------------------------------- */
class PDF extends FPDF
{
    public $fecha_en;

    function __construct($f)
    {
        parent::__construct();
        $this->fecha_en = $f;
    }

    function Header()
    {
        if ($this->PageNo() > 1) return;

        if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
            $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 50);
        }

        $this->SetFont('Arial', 'B', 14);
        $this->SetXY(150, 10);
        $this->Cell(50, 8, 'Daily Laboratory Report', 0, 1, 'R');

        $this->SetFont('Arial', '', 10);
        $this->SetXY(150, 18);
        $this->Cell(50, 8, "Date: {$this->fecha_en}", 0, 1, 'R');

        $this->Ln(12);

        $this->section_title("1. Personnel Assigned");
        $this->SetFont('Arial', '', 10);

        $dia = date('w');
        $sem = date('W');

        if (in_array($dia, [0, 1, 2, 3])) {
            $this->MultiCell(0, 6, pdf_text_safe("Contractor Techs: Wilson, Rafy, Rony, Jonathan"), 0);
            $this->MultiCell(0, 6, pdf_text_safe("PV Supervisors: Diana Vazquez"), 0);
            $this->MultiCell(0, 6, pdf_text_safe("Document Control: Frandy Espinal"), 0);
        }

        if (in_array($dia, [3, 4, 5, 6])) {
            $this->MultiCell(0, 6, pdf_text_safe("Contractor Techs: Rafael Reyes, Darielvy, Jordany, Melvin"), 0);
            $this->MultiCell(0, 6, pdf_text_safe("PV Supervisors: Victor Mercedes"), 0);
            $this->MultiCell(0, 6, pdf_text_safe("Document Control: Arturo Santana"), 0);
        }

        if (
            ($sem % 2 === 0 && in_array($dia, [1, 2, 3, 4, 5])) ||
            ($sem % 2 !== 0 && in_array($dia, [1, 2, 3, 4]))
        ) {
            $this->MultiCell(0, 6, pdf_text_safe("Document Control: Yamilexi Mejia"), 0);
            $this->MultiCell(0, 6, pdf_text_safe("Chief Laboratory: Wendin De Jesus"), 0);
        }

        $this->Ln(5);
    }

    function section_title($t)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(0, 8, pdf_text_safe($t), 0, 1, 'L', true);
        $this->Ln(2);
    }

    function section_table($headers, $rows, $w)
    {
        $this->SetFont('Arial', 'B', 10);
        foreach ($headers as $i => $h) {
            $this->Cell($w[$i], 7, pdf_text_safe($h), 1, 0, 'C');
        }
        $this->Ln();

        $this->SetFont('Arial', '', 10);
        foreach ($rows as $row) {
            foreach ($row as $i => $col) {
                $this->Cell($w[$i], 6, pdf_text_safe($col), 1, 0, 'C');
            }
            $this->Ln();
        }
        $this->Ln(4);
    }
}

/* ----------------------------------------
   GENERACIÓN DEL PDF
---------------------------------------- */
$pdf = new PDF($fecha_en);
$pdf->AddPage();

/* ---------- 2. RESUMEN DIARIO ---------- */
$pdf->section_title("2. Summary of Daily Activities");
$pdf->section_table(
    ["Activities", "Quantity"],
    [
        ["Requisitioned",  get_count("lab_test_requisition_form", "Registed_Date",  $start, $end)],
        ["In Preparation", get_count("test_preparation",          "Register_Date",  $start, $end)],
        ["In Realization", get_count("test_realization",          "Register_Date",  $start, $end)],
        ["Completed",      get_count("test_delivery",             "Register_Date",  $start, $end)]
    ],
    [90, 40]
);

/* ---------- 3. RESUMEN CLIENTES ---------- */
$pdf->section_title("3. Client Summary of Completed Tests");

$clientes = resumen_entregas_por_cliente($end);

$rows = [];
$label = strtoupper(trim($cli));
$label = pdf_text_safe($label); // <-- conversión limpia

// Si es demasiado largo
if (strlen($label) > 12) {
    // cortar sin partir caracteres
    $label = substr($label, 0, 12) . "...";   // <-- 3 puntos, 100% compatible
}


$pdf->section_table(
    ["Client", "Requested", "Completed", "%"],
    $rows,
    [50, 35, 35, 25]
);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, pdf_text_safe("Client Completion %"), 0, 1, 'L');

draw_client_bar_chart($pdf, $clientes);

$pdf->Ln(5);

/* ---------- 4. MUESTRAS NUEVAS ---------- */
$pdf->section_title("4. Newly Registered Samples");

$muestras = muestras_nuevas($start, $end);
$rows = [];

foreach ($muestras as $m) {
    $rows[] = [
        $m['Sample_ID'] . " - " . $m['Sample_Number'],
        $m['Structure'],
        $m['Client'],
        $m['Test_Type']
    ];
}

$pdf->section_table(
    ["Sample ID", "Structure", "Client", "Test Type"],
    $rows,
    [45, 35, 35, 75]
);

/* ---------- 5. POR TÉCNICO ---------- */
$pdf->section_title("5. Summary of Tests by Technician");

$tec = resumen_tecnico($start, $end);
$rows = [];

foreach ($tec as $t) {
    $rows[] = [$t['Technician'], $t['etapa'], $t['total']];
}

$pdf->section_table(
    ["Technician", "Process", "Quantity"],
    $rows,
    [60, 50, 40]
);

/* ---------- 6. POR TIPO ---------- */
$pdf->section_title("6. Distribution of Tests by Type");

$tipos = resumen_tipo($start, $end);
$rows = [];

foreach ($tipos as $t) {
    $rows[] = [$t['Test_Type'], $t['etapa'], $t['total']];
}

$pdf->section_table(
    ["Test Type", "Process", "Quantity"],
    $rows,
    [70, 50, 30]
);

/* ---------- 7. PENDIENTES ---------- */
$pdf->section_title("7. Pending Tests");

$rowsPend = [];
$pend = ensayos_pendientes(date('Y-m-d H:i:s', strtotime('-1 month', strtotime($end))), $end);

foreach ($pend as $p) {
    $rowsPend[] = [
        $p['Sample_ID'],
        $p['Sample_Number'],
        $p['Test_Type'],
        $p['Sample_Date']
    ];
}

$pdf->section_table(
    ["Sample ID", "Number", "Test Type", "Date"],
    $rowsPend,
    [40, 40, 60, 40]
);

/* ---------- 8. ENSAYOS REPORTE ---------- */
render_ensayos_reporte($pdf, $start, $end);

/* ---------- 9. OBSERVACIONES ---------- */
$pdf->section_title("9. Summary of Observations / Non-Conformities");

$obs = observaciones_ensayos_reporte($start, $end);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Sample', 1);
$pdf->Cell(145, 8, 'Observations', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
foreach ($obs as $o) {
    $pdf->Cell(45, 8, pdf_text_safe($o['Sample_ID'] . "-" . $o['Sample_Number']), 1);
    $pdf->Cell(145, 8, pdf_text_safe(substr($o['Noconformidad'], 0, 100)), 1);
    $pdf->Ln();
}

/* ---------- 10. RESPONSIBLE ---------- */
$pdf->section_title("10. Responsible");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, "Report prepared by", 1);
$pdf->Cell(120, 8, pdf_text_safe($nombre_responsable), 1, 1);

ob_end_clean();
$pdf->Output("I", "Daily_Laboratory_Report_{$fecha}.pdf");
