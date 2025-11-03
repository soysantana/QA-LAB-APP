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
$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 15:59:59"));
$end   = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

function get_count($table, $field, $start, $end) {
  $r = find_by_sql("SELECT COUNT(*) as total FROM {$table} WHERE {$field} BETWEEN '{$start}' AND '{$end}'");
  return (int)$r[0]['total'];
}
// --- Helper: detecta "envío/envio/envíos/envios" como token ---
if (!function_exists('es_envio_tt')) {
  function es_envio_tt(string $s): bool {
    // Baja a minúsculas (unicode)
    $t = mb_strtolower($s, 'UTF-8');
    // Coincide 'envio' o 'envío', singular o plural, como palabra (rodea por separadores)
    // Acepta espacios, comas, guiones, slashes, etc. alrededor del token.
    return (bool)preg_match('/(^|[,\s\/\-\|;])env[ií]os?($|[,\s\/\-\|;])/u', $t);
  }
}

/**
 * Resumen de entregas por cliente (último mes hasta $end)
 * EXCLUYENDO test types que sean "envío/envio".
 */
function resumen_entregas_por_cliente($end) {
  $stats  = [];
  $inicio = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($end)));

  // Solicitudes (no filtramos aquí por SQL porque Test_Type puede venir como lista; filtramos por token más abajo)
  $solicitudes = find_by_sql("
    SELECT Client, Sample_ID, Sample_Number, Test_Type
    FROM lab_test_requisition_form
    WHERE Sample_Date BETWEEN '{$inicio}' AND '{$end}'
  ");

  // Entregas (armamos mapa pero excluyendo 'envío' para que no sumen entregados)
  $entregas = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type
    FROM test_delivery
  ");

  $entregado_map = [];
  foreach ($entregas as $e) {
    $sid = strtoupper(trim($e['Sample_ID'] ?? ''));
    $sno = strtoupper(trim($e['Sample_Number'] ?? ''));
    $tts = (string)($e['Test_Type'] ?? '');

    // Puede venir lista separada por comas
    foreach (explode(',', $tts) as $tt) {
      $tt = trim($tt);
      if ($tt === '' || es_envio_tt($tt)) continue; // EXCLUYE ENVÍO

      $key = $sid.'|'.$sno.'|'.strtoupper($tt);
      $entregado_map[$key] = true;
    }
  }

  foreach ($solicitudes as $s) {
    $cliente   = strtoupper(trim($s['Client'] ?? '')) ?: 'PENDING INFO';
    $sample_id = strtoupper(trim($s['Sample_ID'] ?? ''));
    $sample_no = strtoupper(trim($s['Sample_Number'] ?? ''));
    $tts       = (string)($s['Test_Type'] ?? '');

    // Puede venir lista; filtra cada token
    foreach (explode(',', $tts) as $tt) {
      $tt = trim($tt);
      if ($tt === '' || es_envio_tt($tt)) continue; // EXCLUYE ENVÍO

      if (!isset($stats[$cliente])) {
        $stats[$cliente] = ['solicitados' => 0, 'entregados' => 0, 'porcentaje' => 0];
      }

      $stats[$cliente]['solicitados']++;

      $key = $sample_id.'|'.$sample_no.'|'.strtoupper($tt);
      if (isset($entregado_map[$key])) {
        $stats[$cliente]['entregados']++;
      }
    }
  }

  // Calcula % por cliente
  foreach ($stats as $cli => &$d) {
    $d['porcentaje'] = $d['solicitados'] > 0 ? round($d['entregados'] * 100 / $d['solicitados']) : 0;
  }
  unset($d);

  return $stats;
}





function count_by_sample($table, $sample, $field = 'Sample_ID') {
  return count(find_by_sql("SELECT id FROM {$table} WHERE {$field} = '{$sample}'"));
}

function muestras_nuevas($start, $end) {
    // Accede a la variable global de conexión si es necesario
    global $db;

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
        ORDER BY 
            Registed_Date ASC
    ";

    return find_by_sql($sql);
}


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

// Función principal
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

function es_envio(string $s): bool {
  // Normaliza a ASCII sin tildes y baja a minúsculas
  $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
  if ($t === false) $t = $s;
  $t = mb_strtolower($t, 'UTF-8');
  // Coincide "envio" o "envios" como palabra o token en cadena
  return (bool)preg_match('/\benvios?\b/u', $t);
}



function resumen_tecnico($start, $end) {
  return find_by_sql("SELECT Technician, COUNT(*) as total, 'In Preparation' as etapa FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) as total, 'In Realization' FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) as total, 'Completed' FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician");
}

function resumen_tipo($start, $end) {
  return find_by_sql("SELECT Test_Type, COUNT(*) as total, 'In Preparation' as etapa FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type, COUNT(*) as total, 'In Realization' FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type, COUNT(*) as total, 'Completed' FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type");
}


function render_ensayos_reporte($pdf, $start, $end) {
  // Obtener datos desde la tabla `ensayos_reporte`
  $ensayos_reporte = find_by_sql("SELECT * FROM ensayos_reporte WHERE Report_Date BETWEEN '{$start}' AND '{$end}'");

  // Título de la sección
  $pdf->section_title("8. Summary of Dam Constructions Test");

  // Encabezados de la tabla
  $pdf->SetFont('Arial', 'B', 9);
  $pdf->Cell(40, 8, 'Sample', 1);
  $pdf->Cell(25, 8, 'Structure', 1);
  $pdf->Cell(20, 8, 'Mat. Type', 1);
  $pdf->Cell(30, 8, 'Test Type', 1);
  $pdf->Cell(20, 8, 'Condition', 1);
  $pdf->Cell(55, 8, 'Comments', 1);
  $pdf->Ln();

  // Contenido de la tabla
  $pdf->SetFont('Arial', '', 9);
  foreach ($ensayos_reporte as $row) {
    $sample = $row['Sample_ID'] . '-' . $row['Sample_Number'];
    $structure = $row['Structure'];
    $mat_type = $row['Material_Type'];
    $test_type = $row['Test_Type'];
    $condition = $row['Test_Condition'];
    $comments = substr($row['Comments'], 0, 45); // Limita comentarios largos

    $pdf->Cell(40, 8, $sample, 1);
    $pdf->Cell(25, 8, $structure, 1);
    $pdf->Cell(20, 8, $mat_type, 1);
    $pdf->Cell(30, 8, $test_type, 1);
    $pdf->Cell(20, 8, $condition, 1);
    $pdf->Cell(55, 8, $comments, 1);
    $pdf->Ln();
  }
}

function observaciones_ensayos_reporte($start, $end) {
  return find_by_sql("
    SELECT 
      Sample_ID, 
      Sample_Number, 
      Structure, 
      Material_Type, 
      Noconformidad 
    FROM ensayos_reporte 
    WHERE 
      Noconformidad IS NOT NULL 
      AND TRIM(Noconformidad) != '' 
      AND Report_Date BETWEEN '{$start}' AND '{$end}'
  ");
}


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

$semana = $this->week_number;
$dia = $this->day_of_week;

// =============================
// GRUPO DIANA — Domingo a miércoles (TODAS LAS SEMANAS)
// =============================
if (in_array($dia, [0, 1, 2, 3])) {
  $this->MultiCell(0, 6, "Contractor Lab Technicians: Wilson Martinez, Rafy Leocadio, Rony Vargas, Jonathan Vargas", 0, 'L');
  $this->MultiCell(0, 6, "PV Laboratory Supervisors: Diana Vazquez", 0, 'L');
  $this->MultiCell(0, 6, "Lab Document Control: Frandy Espinal", 0, 'L');
}

// =============================
// GRUPO LAURA — Miércoles a sábado (TODAS LAS SEMANAS)
// =============================
if (in_array($dia, [3, 4, 5, 6])) {
  $this->MultiCell(0, 6, "Contractor Lab Technicians: Rafael Reyes, Darielvy Felix, Jordany Almonte, Melvin Castillo", 0, 'L');
  $this->MultiCell(0, 6, "PV Laboratory Supervisors: Victor", 0, 'L');
  $this->MultiCell(0, 6, "Lab Document Control: Arturo Santana", 0, 'L');
}

// =============================
// YAMILEXI + WENDIN — Rotación semanal
// =============================
if (
  ($semana % 2 === 0 && in_array($dia, [1, 2, 3, 4, 5])) ||  // Semana par: lunes a viernes
  ($semana % 2 !== 0 && in_array($dia, [1, 2, 3, 4]))        // Semana impar: lunes a jueves
) {
  $this->MultiCell(0, 6, "Lab Document Control: Yamilexi Mejia", 0, 'L');   
  $this->MultiCell(0, 6, utf8_decode("Chief laboratory: Wendin De Jesús Mendoza"), 0, 'L');
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
    $this->SetFont('Arial', 'B', 10);
    foreach ($headers as $i => $h) {
      $this->Cell($widths[$i], 7, $h, 1, 0, 'C');
    }
    $this->Ln();
    $this->SetFont('Arial', '', 10);
    foreach ($rows as $row) {
      foreach ($row as $i => $col) {
        $this->Cell($widths[$i], 6, $col, 1, 0, 'C');
      }
      $this->Ln();
    }
    $this->Ln(3);
  }
}

$pdf = new PDF($fecha_en);

$pdf->AddPage();

$pdf->section_title("2. Summary of  Daily Activities");
$pdf->section_table(["Activities", "Quantity"], [
  ["Requisitioned", get_count("lab_test_requisition_form", "Registed_Date", $start, $end)],
  ["In Preparation", get_count("test_preparation", "Register_Date", $start, $end)],
  ["In Realizacion", get_count("test_realization", "Register_Date", $start, $end)],
  ["Completed", get_count("test_delivery", "Register_Date", $start, $end)]
], [90, 40]);

$pdf->section_title("3. Client Summary of Completed Tests");


$clientes = resumen_entregas_por_cliente($start, $end);
$rows = [];
foreach ($clientes as $cli => $d) {
  $pct = $d['solicitados'] > 0 ? round($d['entregados'] * 100 / $d['solicitados']) : 0;
  $rows[] = [$cli, $d['solicitados'], $d['entregados'], "$pct%"];
}

$pdf->section_title("3. Client Summary of Completed Tests");

$clientes = resumen_entregas_por_cliente($start, $end);
$rows = [];
foreach ($clientes as $cli => $d) {
  // Si tu función ya filtra en SQL, no necesitas esto.
  // Si NO, y tienes el tipo por cliente, aplica tu lógica aquí.
  // (Si no tienes Test_Type por cliente, deja solo el filtro en SQL.)

  $pct = $d['solicitados'] > 0 ? round($d['entregados'] * 100 / $d['solicitados']) : 0;
  $rows[] = [$cli, $d['solicitados'], $d['entregados'], "$pct%"];
}
$pdf->section_table(["Client", "Requested", "Completed", "%"], $rows, [50, 35, 35, 25]);
$pdf->Ln(4);

$pdf->section_title("4. Newly Registered Samples");
$muestras = muestras_nuevas($start, $end);

// IMPORTANTE: no reinicies $rows antes del foreach
$rows = [];
foreach ($muestras as $m) {
  // Excluir registros donde el Test_Type contenga "envío/envio"
  if (isset($m['Test_Type']) && es_envio($m['Test_Type'])) {
    continue;
  }
  $rows[] = [
    $m['Sample_ID'] . ' - ' . $m['Sample_Number'],
    $m['Structure'],
    $m['Client'],
    $m['Test_Type']
  ];
}
$pdf->section_table(["Technician", "Process", "Quantity"], $t_rows, [60, 50, 40]);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, 'Tech. Legend: WM= Wilson Martinez, JV= Jonathan Vargas, RV= Roni Vargas, RL =Rafy Leocadio,', 0, 1);
$pdf->Cell(0, 4, 'RR= Rafael Reyes, MC= Melvin Castillo, DF= Darielvy Felix, JA= Jordany Almonte , ', 0, 1);
$pdf->Ln(5);


$pdf->section_title("6. Distribution of Tests by Type");
$tipos = resumen_tipo($start, $end);
$type_rows = [];
foreach ($tipos as $r) {
  $type_rows[] = [$r['Test_Type'], $r['etapa'], $r['total']];
}
$pdf->section_table(["Test Type", "Process", "Quantity"], $type_rows, [70, 50, 30]);



$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Test Legend: AR= Acid Reativity, GS= Grain Size, SG= Specific Gravity, SP= Standard Proctor, MP= Modified Proctor, AL= Atterberg Limit,   ', 0, 1);
$pdf->Cell(0, 5, 'HY= Hidrometer, DHY= Double Hydromter, SCT= Sand Castle, SND= Soundness, LAA= Los Angeles Abrasion, MC= Moisture Content, ', 0, 1);
$pdf->Cell(0, 5, 'PLT= Point Load, UCS= Simple Compression, BTS, Brazilian, Shape= Particle Shape,  ', 0, 1);
$pdf->Ln(4);

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

$pdf->section_table(["Sample ID", "Sample Number", "Test Type", "Date"], $rows, [40, 40, 60, 40]);




render_ensayos_reporte($pdf, $start, $end);
$pdf->Ln(5);

$pdf->section_title("9. Summary of Observations/Non-Conformities");

$observaciones = observaciones_ensayos_reporte($start, $end);

// Encabezado
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Sample', 1);

$pdf->Cell(145, 8, 'Observations', 1);
$pdf->Ln();

// Cuerpo
$pdf->SetFont('Arial', '', 9);
foreach ($observaciones as $obs) {
  $sample = $obs['Sample_ID'] . '-' . $obs['Sample_Number'] .'-' . $obs['Material_Type'];
  $pdf->Cell(45, 8, $sample, 1); 
 
  $pdf->Cell(145, 8, substr($obs['Noconformidad'], 0, 100), 1); // puedes ajustar longitud si quieres
  $pdf->Ln();
}

$pdf->Ln(5);

$pdf->section_title("10. Responsible");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, "Report prepared by", 1);
$pdf->Cell(120, 8, utf8_decode($nombre_responsable), 1, 1);




ob_end_clean();
$pdf->Output("I", "Daily_Laboratory_Report_{$fecha}.pdf");
