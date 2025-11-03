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

// Ventana de 4:00 pm a 4:00 pm (día -1 15:59:59 → día 15:59:59)
$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 15:59:59"));
$end   = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

function get_count($table, $field, $start, $end) {
  $r = find_by_sql("SELECT COUNT(*) as total FROM {$table} WHERE {$field} BETWEEN '{$start}' AND '{$end}'");
  return (int)$r[0]['total'];
}

// --- Helpers para detectar "envío/envio/envíos/envios" ---
if (!function_exists('es_envio_tt')) {
  function es_envio_tt(string $s): bool {
    $t = mb_strtolower((string)$s, 'UTF-8');
    // token como palabra, tolera separadores comunes
    return (bool)preg_match('/(^|[,\s\/\-\|;])env[ií]os?($|[,\s\/\-\|;])/u', $t);
  }
}
if (!function_exists('es_envio')) {
  function es_envio(string $s): bool {
    $t = $s;
    if (function_exists('iconv')) {
      $tmp = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
      if ($tmp !== false) $t = $tmp;
    }
    $t = mb_strtolower($t, 'UTF-8');
    return (bool)preg_match('/\benvios?\b/u', $t);
  }
}

/**
 * Resumen de entregas por cliente (usa rango [$start,$end] como llamas la función)
 * EXCLUYENDO test types que sean "envío/envio".
 */
function resumen_entregas_por_cliente($start, $end) {
  $stats  = [];

  // Solicitudes del rango (no filtramos aquí por SQL por si Test_Type viene como lista)
  $solicitudes = find_by_sql("
    SELECT Client, Sample_ID, Sample_Number, Test_Type
    FROM lab_test_requisition_form
    WHERE Sample_Date BETWEEN '{$start}' AND '{$end}'
  ");

  // Entregas (construimos mapa por cada token de Test_Type, excluyendo 'envío')
  $entregas = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
  ");

  $entregado_map = [];
  foreach ($entregas as $e) {
    $sid = strtoupper(trim($e['Sample_ID'] ?? ''));
    $sno = strtoupper(trim($e['Sample_Number'] ?? ''));
    $tts = (string)($e['Test_Type'] ?? '');
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

    if (!isset($stats[$cliente])) {
      $stats[$cliente] = ['solicitados' => 0, 'entregados' => 0, 'porcentaje' => 0];
    }

    foreach (explode(',', $tts) as $tt) {
      $tt = trim($tt);
      if ($tt === '' || es_envio_tt($tt)) continue; // EXCLUYE ENVÍO

      $stats[$cliente]['solicitados']++;

      $key = $sample_id.'|'.$sample_no.'|'.strtoupper($tt);
      if (isset($entregado_map[$key])) {
        $stats[$cliente]['entregados']++;
      }
    }
  }

  // % por cliente
  foreach ($stats as $cliente => $val) {
    $s = $val['solicitados'];
    $e = $val['entregados'];
    $stats[$cliente]['porcentaje'] = $s > 0 ? round(($e / $s) * 100, 2) : 0;
  }

  return $stats;
}
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

// Ventana de 4:00 pm a 4:00 pm (día -1 15:59:59 → día 15:59:59)
$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 15:59:59"));
$end   = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

function get_count($table, $field, $start, $end) {
  $r = find_by_sql("SELECT COUNT(*) as total FROM {$table} WHERE {$field} BETWEEN '{$start}' AND '{$end}'");
  return (int)$r[0]['total'];
}

// --- Helpers para detectar "envío/envio/envíos/envios" ---
if (!function_exists('es_envio_tt')) {
  function es_envio_tt(string $s): bool {
    $t = mb_strtolower((string)$s, 'UTF-8');
    // token como palabra, tolera separadores comunes
    return (bool)preg_match('/(^|[,\s\/\-\|;])env[ií]os?($|[,\s\/\-\|;])/u', $t);
  }
}
if (!function_exists('es_envio')) {
  function es_envio(string $s): bool {
    $t = $s;
    if (function_exists('iconv')) {
      $tmp = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
      if ($tmp !== false) $t = $tmp;
    }
    $t = mb_strtolower($t, 'UTF-8');
    return (bool)preg_match('/\benvios?\b/u', $t);
  }
}

/**
 * Resumen de entregas por cliente (usa rango [$start,$end] como llamas la función)
 * EXCLUYENDO test types que sean "envío/envio".
 */
function resumen_entregas_por_cliente($start, $end) {
  $stats  = [];

  // Solicitudes del rango (no filtramos aquí por SQL por si Test_Type viene como lista)
  $solicitudes = find_by_sql("
    SELECT Client, Sample_ID, Sample_Number, Test_Type
    FROM lab_test_requisition_form
    WHERE Sample_Date BETWEEN '{$start}' AND '{$end}'
  ");

  // Entregas (construimos mapa por cada token de Test_Type, excluyendo 'envío')
  $entregas = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
  ");

  $entregado_map = [];
  foreach ($entregas as $e) {
    $sid = strtoupper(trim($e['Sample_ID'] ?? ''));
    $sno = strtoupper(trim($e['Sample_Number'] ?? ''));
    $tts = (string)($e['Test_Type'] ?? '');
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

    if (!isset($stats[$cliente])) {
      $stats[$cliente] = ['solicitados' => 0, 'entregados' => 0, 'porcentaje' => 0];
    }

    foreach (explode(',', $tts) as $tt) {
      $tt = trim($tt);
      if ($tt === '' || es_envio_tt($tt)) continue; // EXCLUYE ENVÍO

      $stats[$cliente]['solicitados']++;

      $key = $sample_id.'|'.$sample_no.'|'.strtoupper($tt);
      if (isset($entregado_map[$key])) {
        $stats[$cliente]['entregados']++;
      }
    }
  }

  // % por cliente
  foreach ($stats as $cliente => $val) {
    $s = $val['solicitados'];
    $e = $val['entregados'];
    $stats[$cliente]['porcentaje'] = $s > 0 ? round(($e / $s) * 100, 2) : 0;
  }

  return $stats;
}
function count_by_sample($table, $sample, $field = 'Sample_ID') {
  return count(find_by_sql("SELECT id FROM {$table} WHERE {$field} = '{$sample}'"));
}

function muestras_nuevas($start, $end) {
  // Se mantiene tu ventana 4pm-4pm con BETWEEN $start y $end
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

// Función principal: ensayos pendientes en rango
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
$pdf->section_table(["Client", "Requested", "Completed", "%"], $rows, [50, 35, 35, 25]);
$pdf->Ln(4);

$pdf->section_title("4. Newly Registered Samples");
$muestras = muestras_nuevas($start, $end);
$rows = [];
foreach ($muestras as $m) {
  // Excluir registros donde el Test_Type contenga "envío/envio"
  if (isset($m['Test_Type']) && es_envio($m['Test_Type'])) continue;

  $rows[] = [
    $m['Sample_ID'] . ' - ' . $m['Sample_Number'],
    $m['Structure'],
    $m['Client'],
    $m['Test_Type']
  ];
}
$pdf->section_table(["Sample ID", "Structure", "Client", "Test Type"], $rows, [45, 35, 35, 75]);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Test Legend: AR= Acid Reativity, GS= Grain Size, SG= Specific Gravity, SP= Standard Proctor, MP= Modified Proctor, AL= Atterberg Limit,   ', 0, 1);
$pdf->Cell(0, 5, 'HY= Hidrometer, DHY= Double Hydromter, SCT= Sand Castle, SND= Soundness, LAA= Los Angeles Abrasion, MC= Moisture Content, ', 0, 1);
$pdf->Cell(0, 5, 'PLT= Point Load, UCS= Simple Compression, BTS, Brazilian, Shape= Particle Shape,  ', 0, 1);
$pdf->Ln(4);

$pdf->section_title("5. Summary of Tests by Technician ");
$tec = resumen_tecnico($start, $end);
$t_rows = [];
foreach ($tec as $r) {
  $t_rows[] = [$r['Technician'], $r['etapa'], $r['total']];
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

// Fecha de inicio para pendientes (1 mes atrás desde $end)
$start_pendientes = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($end)));
$pendientes = ensayos_pendientes($start_pendientes, $end);

$rows = [];
foreach ($pendientes as $p) {
  if (!empty($p['Test_Type'])) {
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
  $pdf->Cell(145, 8, substr($obs['Noconformidad'], 0, 100), 1);
  $pdf->Ln();
}

$pdf->Ln(5);

$pdf->section_title("10. Responsible");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, "Report prepared by", 1);
$pdf->Cell(120, 8, utf8_decode($nombre_responsable), 1, 1);

ob_end_clean();
$pdf->Output("I", "Daily_Laboratory_Report_{$fecha}.pdf");
