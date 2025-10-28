<?php
declare(strict_types=1);
require_once('../config/load.php');

date_default_timezone_set('America/Santo_Domingo');

function json_ok(array $data){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>true]+$data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function json_error(int $code, string $msg){
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

// =================== CONFIGURABLE ===================
// Si tu columna Registed_Date es DATETIME, pon true:
$REGISTERED_IS_DATETIME = false; // true si guardas fecha+hora (misma zona)

// Cómo quieres construir el "nombre de la muestra":
// Ajusta el formato aquí (por ejemplo, "ID - NUM", "ID | NUM", etc.)
function build_sample_name(string $sample_id, string $sample_number): string {
  return $sample_id . ' | ' . $sample_number;
}
// ====================================================

// --------- Parámetros ---------
$prefix_raw = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
$prefix_raw = preg_replace('/[^A-Za-z0-9\-_]/', '', $prefix_raw);

$now   = new DateTimeImmutable('now');
$yy    = $now->format('y');        // "25"
$month = (int)$now->format('n');   // 1..12

// Detecta si el prefijo ya trae año al final (2 dígitos)
$hasYearSuffix = (bool)preg_match('/\d{2}$/', $prefix_raw);

// Si NO trae año, se asume el año actual (auto-anexo)
$prefix_current_year = $hasYearSuffix ? $prefix_raw : ($prefix_raw !== '' ? $prefix_raw.$yy : '');

// También preparamos el año anterior para tolerancia en enero (opcional)
$yy_prev = $now->modify('-1 year')->format('y');
$prefix_prev_year = ($prefix_raw !== '')
  ? ( $hasYearSuffix ? preg_replace('/\d{2}$/', $yy_prev, $prefix_raw) : $prefix_raw.$yy_prev )
  : '';

// --------- Query “muestras de hoy” ---------
if ($REGISTERED_IS_DATETIME) {
  $sqlToday = "
    SELECT id, Sample_ID, Sample_Number, Test_Type, Material_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date >= CURDATE()
      AND Registed_Date <  CURDATE() + INTERVAL 1 DAY
    ORDER BY Registed_Date DESC, id DESC
  ";
} else {
  $sqlToday = "
    SELECT id, Sample_ID, Sample_Number, Test_Type, Material_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date = CURDATE()
    ORDER BY id DESC
  ";
}
$todayRows  = find_by_sql($sqlToday);
$todayCount = is_array($todayRows) ? count($todayRows) : 0;

// --------- Utilidades para consecutivos ---------
global $db;

/**
 * Devuelve el mayor sufijo numérico al final de la cadena (tras el último guion)
 * para filas donde la columna $col comienza con "$prefix-".
 * Ej.: PVDJ-AGG25-0012 => 12
 */
function max_suffix_for_prefix_col(string $col, string $prefix_like): int {
  if ($prefix_like === '') return 0;
  $col_safe = preg_replace('/[^A-Za-z0-9_]/', '', $col);
  $like     = $GLOBALS['db']->escape($prefix_like);

  $sql = sprintf(
    "SELECT MAX(CAST(SUBSTRING_INDEX(%s,'-',-1) AS UNSIGNED)) AS max_n
     FROM lab_test_requisition_form
     WHERE %s LIKE '%s-%%'",
     $col_safe, $col_safe, $like
  );
  $row = find_by_sql($sql);
  return isset($row[0]['max_n']) ? (int)$row[0]['max_n'] : 0;
}

function value_exists_in_col(string $col, string $value): bool {
  $col_safe = preg_replace('/[^A-Za-z0-9_]/', '', $col);
  $val      = $GLOBALS['db']->escape($value);
  $sql = sprintf(
    "SELECT COUNT(*) AS c FROM lab_test_requisition_form WHERE %s = '%s'",
    $col_safe, $val
  );
  $row = find_by_sql($sql);
  return isset($row[0]['c']) && (int)$row[0]['c'] > 0;
}

/**
 * Verifica si ya existe una fila con esa combinación exacta de Sample_ID + Sample_Number.
 */
function pair_exists(string $sample_id, string $sample_number): bool {
  $id  = $GLOBALS['db']->escape($sample_id);
  $num = $GLOBALS['db']->escape($sample_number);
  $sql = "
    SELECT COUNT(*) AS c
    FROM lab_test_requisition_form
    WHERE Sample_ID = '{$id}' AND Sample_Number = '{$num}'
  ";
  $row = find_by_sql($sql);
  return isset($row[0]['c']) && (int)$row[0]['c'] > 0;
}

// --------- Siguiente número por prefijo dinámico (ID + Number + PAR) ---------
$nextInfo = null;

if ($prefix_raw !== '') {

  // Paso 1: máximos por columna usando el prefijo del año actual
  $maxN_id_current  = max_suffix_for_prefix_col('Sample_ID',     $prefix_current_year);
  $maxN_num_current = max_suffix_for_prefix_col('Sample_Number', $prefix_current_year);

  // Paso 2: (enero) contexto año anterior por si no hay nada aún este año
  $maxN_id_prev  = 0;
  $maxN_num_prev = 0;
  if ($month === 1 && ($maxN_id_current === 0 && $maxN_num_current === 0)) {
    $maxN_id_prev  = max_suffix_for_prefix_col('Sample_ID',     $prefix_prev_year);
    $maxN_num_prev = max_suffix_for_prefix_col('Sample_Number', $prefix_prev_year);
  }

  // Cálculo base de siguiente por cada columna
  $nextN_id  = $maxN_id_current  + 1;
  $nextN_num = $maxN_num_current + 1;

  // Sugerencia unificada (para que ambos coincidan)
  $recommendedNext = max($nextN_id, $nextN_num);

  // Construye candidatos (mismo sufijo para ID y Number)
  $pad = str_pad((string)$recommendedNext, 4, '0', STR_PAD_LEFT);
  $cid = $prefix_current_year . '-' . $pad;
  $cnu = $prefix_current_year . '-' . $pad;

  // Verifica colisiones: en ID, en Number y en la PAREJA (ID+Number)
  $collision_id   = value_exists_in_col('Sample_ID',     $cid);
  $collision_num  = value_exists_in_col('Sample_Number', $cnu);
  $collision_pair = pair_exists($cid, $cnu);

  // Si choca, incrementa hasta encontrar un par libre en los TRES chequeos
  if ($collision_id || $collision_num || $collision_pair) {
    $bump = $recommendedNext;
    do {
      $bump++;
      $pad = str_pad((string)$bump, 4, '0', STR_PAD_LEFT);
      $cid = $prefix_current_year . '-' . $pad;
      $cnu = $prefix_current_year . '-' . $pad;
      $collision_id   = value_exists_in_col('Sample_ID',     $cid);
      $collision_num  = value_exists_in_col('Sample_Number', $cnu);
      $collision_pair = pair_exists($cid, $cnu);
    } while ($collision_id || $collision_num || $collision_pair);
    $recommendedNext = $bump;
  }

  // Nombre de la muestra (combinación de ambos)
  $sample_name = build_sample_name($cid, $cnu);

  $nextInfo = [
    'base_prefix'     => $prefix_raw,           // lo que enviaste (con o sin año)
    'resolved_prefix' => $prefix_current_year,  // prefijo realmente usado para este año
    'year'            => $yy,

    // Estado por columna (qué hay registrado y cuál sería el próximo si fuesen independientes)
    'sample_id' => [
      'max_found'   => $maxN_id_current,
      'next_number' => $maxN_id_current + 1,
      'next_id'     => $prefix_current_year . '-' . str_pad((string)($maxN_id_current + 1), 4, '0', STR_PAD_LEFT),
    ],
    'sample_number' => [
      'max_found'   => $maxN_num_current,
      'next_number' => $maxN_num_current + 1,
      'next_value'  => $prefix_current_year . '-' . str_pad((string)($maxN_num_current + 1), 4, '0', STR_PAD_LEFT),
    ],

    // Propuesta final unificada (sin colisión en ningún lado)
    'recommended' => [
      'recommended_next' => $recommendedNext,
      'next_padded'      => str_pad((string)$recommendedNext, 4, '0', STR_PAD_LEFT),
      'use_for_id'       => $cid,
      'use_for_number'   => $cnu,
      'sample_name'      => $sample_name, // <<<<<< NOMBRE DE LA MUESTRA
    ],
  ];

  if ($month === 1 && ($maxN_id_prev > 0 || $maxN_num_prev > 0)) {
    $nextInfo['prev_year_context'] = [
      'prev_year'       => $yy_prev,
      'prev_prefix'     => $prefix_prev_year,
      'prev_max_found'  => [
        'Sample_ID'     => $maxN_id_prev,
        'Sample_Number' => $maxN_num_prev,
      ],
      'note'            => 'Transición de año: se inicia conteo nuevo para el año actual.',
    ];
  }
}

// --------- Salida ---------
json_ok([
  'today_count' => $todayCount,
  'today'       => $todayRows,
  'next'        => $nextInfo,
]);
