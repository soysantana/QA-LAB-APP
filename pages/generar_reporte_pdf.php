<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user = current_user();
$nombre_responsable = $user['name']; // o 'full_name' o el campo correcto

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('Y/m/d') : 'Fecha inválida';

// Rango "diario" (16:00 del día anterior a 15:59:59 del día seleccionado)
$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 15:59:59"));
$end   = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

/* =============================
 * Helpers de consulta
 * ============================= */
function get_count($table, $field, $start, $end) {
  $r = find_by_sql("SELECT COUNT(*) as total FROM {$table} WHERE {$field} BETWEEN '{$start}' AND '{$end}'");
  return (int)$r[0]['total'];
}

// Helper: detecta "envío/envio/envíos/envios" como token
if (!function_exists('es_envio_tt')) {
  function es_envio_tt(string $s): bool {
    $t = mb_strtolower((string)$s, 'UTF-8');
    // Palabra "envio/envío" (sing/plural), tolera separadores comunes alrededor
    return (bool)preg_match('/(^|[,\s\/\-\|;])env[ií]os?($|[,\s\/\-\|;])/u', $t);
  }
}

/* =============================
 * 3. Resumen entregas por cliente
 * ============================= */
function resumen_entregas_por_cliente($end) {
  $stats  = [];
  $inicio = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($end)));

  // Solicitudes del último mes
  $solicitudes = find_by_sql("
    SELECT Client, Sample_ID, Sample_Number, Test_Type
    FROM lab_test_requisition_form
    WHERE Sample_Date BETWEEN '{$inicio}' AND '{$end}'
  ");

  // Entregas dentro del mismo rango
  $entregas = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$inicio}' AND '{$end}'
  ");

  // Mapa de entregados por (ID|NUM|TEST_TYPE) excluyendo "envío"
  $entregado_map = [];
  foreach ($entregas as $e) {
    $sid = strtoupper(trim($e['Sample_ID'] ?? ''));
    $sno = strtoupper(trim($e['Sample_Number'] ?? ''));
    $tts = (string)($e['Test_Type'] ?? '');

    foreach (preg_split('/[;,]+/', $tts) as $tt) {
      $tt = trim($tt);
      if ($tt === '' || es_envio_tt($tt)) continue; // excluir "envío/envio"
      $key = $sid . '|' . $sno . '|' . strtoupper($tt);
      $entregado_map[$key] = true;
    }
  }

  // Recorre solicitudes y cuenta por cliente excluyendo "envío"
  foreach ($solicitudes as $s) {
    $cliente   = strtoupper(trim($s['Client'] ?? '')) ?: 'PENDING INFO';
    $sample_id = strtoupper(trim($s['Sample_ID'] ?? ''));
    $sample_no = strtoupper(trim($s['Sample_Number'] ?? ''));
    $tts       = (string)($s['Test_Type'] ?? '');

    if (!isset($stats[$cliente])) {
      $stats[$cliente] = ['solicitados' => 0, 'entregados' => 0, 'porcentaje' => 0];
    }

    foreach (preg_split('/[;,]+/', $tts) as $tt) {
      $tt = trim($tt);
      if ($tt === '' || es_envio_tt($tt)) continue; // excluir "envío/envio"

      $stats[$cliente]['solicitados']++;

      $key = $sample_id . '|' . $sample_no . '|' . strtoupper($tt);
      if (isset($entregado_map[$key])) {
        $stats[$cliente]['entregados']++;
      }
    }
  }

  // % por cliente
  foreach ($stats as $cliente => &$val) {
    $s = (int)$val['solicitados'];
    $e = (int)$val['entregados'];
    $val['porcentaje'] = $s > 0 ? round(($e / $s) * 100, 2) : 0;
  }
  unset($val);

  return $stats;
}

function count_by_sample($table, $sample, $field = 'Sample_ID') {
  return count(find_by_sql("SELECT id FROM {$table} WHERE {$field} = '{$sample}'"));
}

/* =============================
 * 4. Muestras nuevas (excluyendo "envío")
 * ============================= */
function muestras_nuevas($start, $end) {
  // 1) Filtro en SQL: evita traer filas cuyo Test_Type sea solo "envío/envio"
  $sql = "
    SELECT 
      Sample_ID,
      Sample_Number,
      Structure,
      Client,
      Test_Type,
      Registed_Date
    FROM 
      lab_test_requisition_form
    WHERE 
      Registed_Date BETWEEN '{$start}' AND '{$end}'
      AND NOT (LOWER(CONVERT(Test_Type USING utf8)) LIKE '%envio%')
    ORDER BY 
      Registed_Date ASC
  ";
  $rows = find_by_sql($sql);

  // 2) Filtro en PHP: elimina el/los tokens 'envío/envio' si vienen mezclados
  $out = [];
  foreach ($rows as $r) {
    $raw = (string)($r['Test_Type'] ?? '');
    // separadores coma o punto y coma
    $tokens = preg_split('/[;,]+/', $raw);
    $clean  = [];

    foreach ($tokens as $tt) {
      $tt = trim($tt);
      if ($tt === '') continue;

      // match 'envio' o 'envío' (sing/plural), insensible a mayúsculas
      $tlow = mb_strtolower($tt, 'UTF-8');
      if (preg_match('/\benv[ií]os?\b/u', $tlow)) {
        continue; // excluir "envío/envio"
      }
      $clean[] = $tt;
    }

    // Si luego de limpiar no queda nada, no incluir la fila
    if (empty($clean)) continue;

    // Re-escribe Test_Type sin los "envío"
    $r['Test_Type'] = implode(', ', $clean);
    $out[] = $r;
  }

  return $out;
}

/* =============================
 * 7. Ensayos pendientes
 * ============================= */

// Función auxiliar para detectar columnas existentes
function get_columns_for_table($tabla) {
  global $db;
  $cols = [];
  $res = $db->query("SHOW COLUMNS FROM {$tabla}");
  while ($row = $res->fetch_assoc()) {
    $cols[] = $row['Field'];
  }
  return $cols;
}

// Función principal de pendientes
function ensayos_pendientes($start, $end) {
  // Obtener requisiciones dentro del rango
  $requisitions = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Sample_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
  ");

  // Tablas donde puede aparecer un ensayo ya ejecutado
  $tablas = [
    'test_preparation',
    'test_realization',
    'test_delivery',
    'test_review',
    'test_reviewed',
    'test_repeat',
    'doc_files'
  ];

  $indexados = [];

  foreach ($tablas as $tabla) {
    $columnas = get_columns_for_table($tabla);
    $campo_id = in_array('Sample_ID', $columnas) ? 'Sample_ID' : 'Sample_ID';

    $datos = find_by_sql("
      SELECT 
        {$campo_id} AS Sample_ID,
        Sample_Number, Test_Type
      FROM {$tabla}
    ");

    foreach ($datos as $d) {
      $key = strtoupper(trim($d['Sample_ID'])) . '|' .
             strtoupper(trim($d['Sample_Number'])) . '|' .
             strtoupper(trim($d['Test_Type']));
      $indexados[$key] = true;
    }
  }

  // Analizar ensayos pendientes
  $pendientes = [];

  foreach ($requisitions as $r) {
    $sample_id   = strtoupper(trim($r['Sample_ID']));
    $sample_num  = strtoupper(trim($r['Sample_Number']));
    $tipos_raw   = str_replace(';', ',', $r['Test_Type']); // Unificar separadores
    $tipos       = explode(',', $tipos_raw);
    $fecha       = $r['Sample_Date'];

    foreach ($tipos as $tipo_raw) {
      $tipo = strtoupper(trim($tipo_raw));

      // Excluir los que contienen "ENVIO"
      if ($tipo === '' || strpos($tipo, 'ENVIO') !== false) {
        continue;
      }

      $key = $sample_id . '|' . $sample_num . '|' . $tipo;

      if (!isset($indexados[$key])) {
        $pendientes[] = [
          'Sample_ID'     => $r['Sample_ID'],
          'Sample_Number' => $r['Sample_Number'],
          'Test_Type'     => $tipo_raw,
          'Sample_Date'   => $fecha
        ];
      }
    }
  }

  return $pendientes;
}

/* =============================
 * 5 y 6. Resumen por técnico y por tipo
 * ============================= */
function resumen_tecnico($start, $end) {
  return find_by_sql("
    SELECT Technician, COUNT(*) as total, 'In Preparation' as etapa 
    FROM test_preparation 
    WHERE Register_Date BETWEEN '{$start}' AND '{$end}' 
    GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) as total, 'In Realization' 
    FROM test_realization 
    WHERE Register_Date BETWEEN '{$start}' AND '{$end}' 
    GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) as total, 'Completed' 
    FROM test_delivery 
    WHERE Register_Date BETWEEN '{$start}' AND '{$end}' 
    GROUP BY Technician
  ");
}

function resumen_tipo($start, $end) {
  return find_by_sql("
    SELECT Test_Type, COUNT(*) as total, 'In Preparation' as etapa 
    FROM test_preparation 
    WHERE Register_Date BETWEEN '{$start}' AND '{$end}' 
    GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type, COUNT(*) as total, 'In Realization' 
    FROM test_realization 
    WHERE Register_Date BETWEEN '{$start}' AND '{$end}' 
    GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type, COUNT(*) as total, 'Completed' 
    FROM test_delivery 
    WHERE Register_Date BETWEEN '{$start}' AND '{$end}' 
    GROUP BY Test_Type
  ");
}

/* =============================
 * 3.1 Gráfico de barras por cliente
 * ============================= */
function draw_client_bar_chart($pdf, array $clientes) {
  if (empty($clientes)) return;

  // Construir data: cliente + porcentaje
  $data = [];
  foreach ($clientes as $cli => $d) {
    $sol = (int)($d['solicitados'] ?? 0);
    $ent = (int)($d['entregados'] ?? 0);
    $pct = $sol > 0 ? round(($ent * 100) / $sol) : 0;

    // Abreviar nombre del cliente para que quepa
    $label = strtoupper(trim($cli));
    if (mb_strlen($label, 'UTF-8') > 20) {
      // MUY IMPORTANTE: usar SOLO "..." ASCII (no el caracter "…" unicode)
      $label = mb_substr($label, 0, 10, 'UTF-8') . '...';
    }

    $data[] = [
      'label' => $label,
      'pct'   => $pct,
    ];
  }

  // Si todos los % son 0, no dibujar
  $maxPct = 0;
  foreach ($data as $d) {
    if ($d['pct'] > $maxPct) $maxPct = $d['pct'];
  }
  if ($maxPct <= 0) return;

  // Verificar espacio en la página, si no cabe agregamos nueva página
  $chartHeight = 45;        // alto del área de barras
  $chartBottomMargin = 18;  // espacio para etiquetas
  $needed = $chartHeight + $chartBottomMargin + 10;
  if ($pdf->GetY() + $needed > 260) {
    $pdf->AddPage();
  }

  // Posición y dimensiones básicas
  $x0 = $pdf->GetX();
  $y0 = $pdf->GetY() + 4; // un poquito debajo del título
  $chartWidth  = 180;     // ancho total disponible para el gráfico
  $numBars     = count($data);
  $gap         = 4;       // separación entre barras

  // Calcular ancho de cada barra
  $barWidth = ($chartWidth - ($numBars + 1) * $gap) / max($numBars, 1);
  if ($barWidth < 8) {
    $barWidth = 8; // mínimo ancho para que sea legible
  }

  // Ejes
  $pdf->SetDrawColor(0, 0, 0);
  $pdf->Line($x0, $y0, $x0, $y0 + $chartHeight); // eje Y
  $pdf->Line($x0, $y0 + $chartHeight, $x0 + $chartWidth, $y0 + $chartHeight); // eje X

  // Escala de % (0, 25, 50, 75, 100 o hasta maxPct)
  $pdf->SetFont('Arial', '', 7);
  $steps = [0, 25, 50, 75, 100];
  foreach ($steps as $pctRef) {
    if ($pctRef > $maxPct) continue;
    $yLine = $y0 + $chartHeight - ($pctRef * $chartHeight / $maxPct);
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->Line($x0, $yLine, $x0 + $chartWidth, $yLine);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetXY($x0 - 8, $yLine - 2);
    $pdf->Cell(8, 4, $pctRef . '%', 0, 0, 'R');
  }

  // Dibujar barras
  $pdf->SetFont('Arial', '', 8);
  $i = 0;
  foreach ($data as $d) {
    $pct  = $d['pct'];
    $lbl  = $d['label'];

    $barHeight = ($pct * $chartHeight) / $maxPct;
    $x = $x0 + $gap + $i * ($barWidth + $gap);
    $y = $y0 + $chartHeight - $barHeight;

    // Color de la barra (azul suave)
    $pdf->SetFillColor(100, 149, 237);
    $pdf->Rect($x, $y, $barWidth, $barHeight, 'F');

    // Etiqueta de % sobre la barra
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY($x, $y - 4);
    $pdf->Cell($barWidth, 4, $pct . '%', 0, 0, 'C');

    // Etiqueta del cliente debajo
    $pdf->SetXY($x, $y0 + $chartHeight + 2);
    $pdf->MultiCell($barWidth, 3, $lbl, 0, 'C');

    $i++;
  }

  // Mover el cursor por debajo del gráfico
  $pdf->SetY($y0 + $chartHeight + $chartBottomMargin);
}

/* =============================
 * 8. Ensayos reporte + 9. Observaciones
 * ============================= */
function render_ensayos_reporte($pdf, $start, $end) {

    // Filtrar SOLO las estructuras permitidas
    $allowed_structures = "'LLD','SD1','SD2','SD3','PVDJ-AGG',,'LBOR','PVDJ-AGG-INV'";

    // Obtener datos filtrados
    $ensayos_reporte = find_by_sql("
        SELECT * 
        FROM ensayos_reporte
        WHERE DATE(Report_Date) = DATE('{$end}')
        AND UPPER(Structure) IN ($allowed_structures)
    ");

    // Título
    $pdf->section_title("8. Summary of Dam Constructions Test");

    // Encabezados
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(40, 8, 'Sample', 1);
    $pdf->Cell(25, 8, 'Structure', 1);
    $pdf->Cell(20, 8, 'Mat. Type', 1);
    $pdf->Cell(30, 8, 'Test Type', 1);
    $pdf->Cell(20, 8, 'Condition', 1);
    $pdf->Cell(55, 8, 'Comments', 1);
    $pdf->Ln();

    // Contenido
    $pdf->SetFont('Arial', '', 9);

    foreach ($ensayos_reporte as $row) {

        $sample = $row['Sample_ID'] . '-' . $row['Sample_Number'];
        $structure = $row['Structure'];
        $mat_type = $row['Material_Type'];
        $test_type = $row['Test_Type'];
        $condition = $row['Test_Condition'];
        $comments = substr($row['Comments'], 0, 45);

        $pdf->Cell(40, 8, $sample, 1);
        $pdf->Cell(25, 8, $structure, 1);
        $pdf->Cell(20, 8, $mat_type, 1);
        $pdf->Cell(30, 8, $test_type, 1);
        $pdf->Cell(20, 8, $condition, 1);
        $pdf->Cell(55, 8, $comments, 1);
        $pdf->Ln();
    }
}

function observaciones_ensayos_reporte($end) {
  return find_by_sql("
    SELECT 
      Sample_ID,
      Sample_Number,
      Structure,
      Material_Type,
      Noconformidad
    FROM ensayos_reporte
    WHERE 
      Noconformidad <> ''
      AND Noconformidad IS NOT NULL
      AND DATE(Report_Date) = DATE('{$end}')
  ");
}


/* =============================
 * Clase PDF
 * ============================= */
class PDF extends FPDF {
  public $day_of_week;
  public $week_number;
  public $fecha_en;

  function __construct($fecha_en) {
    parent::__construct();
    $this->day_of_week = date('w');
    $this->week_number = date('W');
    $this->fecha_en = $fecha_en;
  }

  function Header() {
    $user = current_user();
    $nombre_responsable = $user['name']; // o 'full_name' o el campo correcto

    if ($this->PageNo() > 1) return;

    if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
      $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 50);
    }

    $this->SetFont('Arial', 'B', 14);
    $this->SetXY(150, 10); // Posiciona el cursor en la parte superior derecha
    $this->Cell(50, 10, utf8_decode('Daily Laboratory Report'), 0, 1, 'R');

    $this->SetFont('Arial', '', 10);
    $this->SetXY(150, 18); // Un poco más abajo para la fecha
    $this->Cell(50, 8, "Date: {$this->fecha_en}", 0, 1, 'R');

    $this->Ln(10); // Espacio antes del contenido principal

    $this->SetFont('Arial', 'B', 11);
    $this->section_title("1. Personnel Assigned");
    $this->SetFont('Arial', '', 10);

    $semana = $this->week_number;
    $dia = $this->day_of_week;

    // GRUPO DIANA — Domingo a miércoles (TODAS LAS SEMANAS)
    if (in_array($dia, [0, 1, 2, 3])) {
      $this->MultiCell(0, 6, "Contractor Lab Technicians: Wilson Martinez, Rafy Leocadio, Rony Vargas, Jonathan Vargas", 0, 'L');
      $this->MultiCell(0, 6, "PV Laboratory Supervisors: Diana Vazquez", 0, 'L');
      $this->MultiCell(0, 6, "Lab Document Control: Frandy Espinal", 0, 'L');
    }

    // GRUPO LAURA — Miércoles a sábado (TODAS LAS SEMANAS)
    if (in_array($dia, [3, 4, 5, 6])) {
      $this->MultiCell(0, 6, "Contractor Lab Technicians: Rafael Reyes, Darielvy Felix, Jordany Almonte, Melvin Castillo", 0, 'L');
      $this->MultiCell(0, 6, "PV Laboratory Supervisors: Victor Mercedes", 0, 'L');
      $this->MultiCell(0, 6, "Lab Document Control: Arturo Santana", 0, 'L');
    }

    // YAMILEXI + WENDIN — Rotación semanal
    if (
      ($semana % 2 === 0 && in_array($dia, [1, 2, 3, 4, 5])) ||  // Semana par: lunes a viernes
      ($semana % 2 !== 0 && in_array($dia, [1, 2, 3, 4]))        // Semana impar: lunes a jueves
    ) {
      $this->MultiCell(0, 6, "Lab Document Control: Yamilexi Mejia", 0, 'L');
      $this->MultiCell(0, 6, utf8_decode("Chief laboratory: Wendin De Jesús"), 0, 'L');
    }

    $this->Ln(5);
  }

  function section_title($title) {
    $this->SetFont('Arial', 'B', 12);
    $this->SetFillColor(200, 220, 255);
    $this->Cell(0, 8, $title, 0, 1, 'L', true);
    $this->Ln(2);
  }

  function section_table($headers, $rows, $widths) {
    // Encabezados
    $this->SetFont('Arial', 'B', 10);
    foreach ($headers as $i => $h) {
      $this->Cell($widths[$i], 7, $h, 1, 0, 'C');
    }
    $this->Ln();

    // Filas
    $this->SetFont('Arial', '', 10);
    $fill = false;
    foreach ($rows as $row) {
      // Zebra simple
      if ($fill) {
        $this->SetFillColor(245, 245, 245);
      } else {
        $this->SetFillColor(255, 255, 255);
      }
      foreach ($row as $i => $col) {
        $this->Cell($widths[$i], 6, $col, 1, 0, 'C', true);
      }
      $this->Ln();
      $fill = !$fill;
    }
    $this->Ln(3);
  }
}

/* =============================
 * Generación del PDF
 * ============================= */
$pdf = new PDF($fecha_en);
$pdf->AddPage();

// 2. Summary of Daily Activities
$pdf->section_title("2. Summary of  Daily Activities");
$pdf->section_table(
  ["Activities", "Quantity"],
  [
    ["Requisitioned",  get_count("lab_test_requisition_form", "Registed_Date",  $start, $end)],
    ["In Preparation", get_count("test_preparation",          "Register_Date",  $start, $end)],
    ["In Realizacion", get_count("test_realization",          "Register_Date",  $start, $end)],
    ["Completed",      get_count("test_delivery",             "Register_Date",  $start, $end)]
  ],
  [90, 40]
);

// 3. Client Summary of Completed Tests
$pdf->section_title("3. Client Summary of Completed Tests");

$clientes = resumen_entregas_por_cliente($end);

// Tabla
$rows = [];
foreach ($clientes as $cli => $d) {
  $pct = ($d['solicitados'] > 0)
        ? round($d['entregados'] * 100 / $d['solicitados'])
        : 0;

  $rows[] = [
    $cli,
    $d['solicitados'],
    $d['entregados'],
    "{$pct}%"
  ];
}

$pdf->section_table(
  ["Client", "Requested", "Completed", "%"],
  $rows,
  [50, 35, 35, 25]
);

// Título gráfico
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'Client Completion %', 0, 1, 'L');

// Gráfico de barras (ya con labels arreglados)
draw_client_bar_chart($pdf, $clientes);

$pdf->Ln(4);

// 4. Newly Registered Samples
$pdf->section_title("4. Newly Registered Samples");
$muestras = muestras_nuevas($start, $end);
$rows = [];
foreach ($muestras as $m) {
  $rows[] = [
    $m['Sample_ID'] . ' - ' . $m['Sample_Number'],
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

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Test Legend: AR= Acid Reativity, GS= Grain Size, SG= Specific Gravity, SP= Standard Proctor, MP= Modified Proctor, AL= Atterberg Limit,   ', 0, 1);
$pdf->Cell(0, 5, 'HY= Hidrometer, DHY= Double Hydromter, SCT= Sand Castle, SND= Soundness, LAA= Los Angeles Abrasion, MC= Moisture Content, ', 0, 1);
$pdf->Cell(0, 5, 'PLT= Point Load, UCS= Simple Compression, BTS, Brazilian, Shape= Particle Shape,  ', 0, 1);
$pdf->Ln(4);

// 5. Summary of Tests by Technician
$pdf->section_title("5. Summary of Tests by Technician ");
$tec = resumen_tecnico($start, $end);
$t_rows = [];
foreach ($tec as $r) {
  $t_rows[] = [$r['Technician'], $r['etapa'], $r['total']];
}
$pdf->section_table(
  ["Technician", "Process", "Quantity"],
  $t_rows,
  [60, 50, 40]
);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, 'Tech. Legend: WM= Wilson Martinez, JV= Jonathan Vargas, RV= Roni Vargas, RL =Rafy Leocadio,', 0, 1);
$pdf->Cell(0, 4, 'RR= Rafael Reyes, MC= Melvin Castillo, DF= Darielvy Felix, JA= Jordany Almonte , ', 0, 1);
$pdf->Ln(5);

// 6. Distribution of Tests by Type
$pdf->section_title("6. Distribution of Tests by Type");
$tipos = resumen_tipo($start, $end);
$type_rows = [];
foreach ($tipos as $r) {
  $type_rows[] = [$r['Test_Type'], $r['etapa'], $r['total']];
}
$pdf->section_table(
  ["Test Type", "Process", "Quantity"],
  $type_rows,
  [70, 50, 30]
);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Test Legend: AR= Acid Reativity, GS= Grain Size, SG= Specific Gravity, SP= Standard Proctor, MP= Modified Proctor, AL= Atterberg Limit,   ', 0, 1);
$pdf->Cell(0, 5, 'HY= Hidrometer, DHY= Double Hydromter, SCT= Sand Castle, SND= Soundness, LAA= Los Angeles Abrasion, MC= Moisture Content, ', 0, 1);
$pdf->Cell(0, 5, 'PLT= Point Load, UCS= Simple Compression, BTS, Brazilian, Shape= Particle Shape,  ', 0, 1);
$pdf->Ln(4);

// 7. Pending Tests
$pdf->section_title("7. Pending Tests");

// Definir fecha de inicio exclusivo para ensayos pendientes (1 mes atrás desde $end)
$start_pendientes = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($end)));

// Obtener los ensayos pendientes en ese rango
$pendientes = ensayos_pendientes($start_pendientes, $end);

$rows = [];
foreach ($pendientes as $p) {
  if (!empty($p['Test_Type'])) { // Excluir los que tengan Test_Type vacío o null
    $rows[] = [
      $p['Sample_ID'],
      $p['Sample_Number'],
      $p['Test_Type'],
      $p['Sample_Date']
    ];
  }
}
$pdf->section_table(
  ["Sample ID", "Sample Number", "Test Type", "Date"],
  $rows,
  [40, 40, 60, 40]
);

// 8. Summary of Dam Constructions Test
render_ensayos_reporte($pdf, $start, $end);
$pdf->Ln(5);

// 9. Summary of Observations/Non-Conformities
$pdf->section_title("9. Summary of Observations/Non-Conformities");
$observaciones = observaciones_ensayos_reporte($end);


// Encabezado
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Sample', 1);
$pdf->Cell(145, 8, 'Observations', 1);
$pdf->Ln();

// Cuerpo
$pdf->SetFont('Arial', '', 9);
foreach ($observaciones as $obs) {
  $sample = $obs['Sample_ID'] . '-' . $obs['Sample_Number'] . '-' . $obs['Material_Type'];
  $pdf->Cell(45, 8, $sample, 1); 
  $pdf->Cell(145, 8, substr($obs['Noconformidad'], 0, 100), 1);
  $pdf->Ln();
}

$pdf->Ln(5);

// 10. Responsible
$pdf->section_title("10. Responsible");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, "Report prepared by", 1);
$pdf->Cell(120, 8, utf8_decode($nombre_responsable), 1, 1);

ob_end_clean();
$pdf->Output("I", "Daily_Laboratory_Report_{$fecha}.pdf");
