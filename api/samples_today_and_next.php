<?php
declare(strict_types=1);
require_once('../config/load.php');
date_default_timezone_set('America/Santo_Domingo');

function json_ok(array $data){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok'=>true]+$data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function json_error(int $code, string $msg){ http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }

// ---------- AJUSTES RÁPIDOS ----------
$REGISTERED_IS_DATETIME     = false;   // true si Registed_Date es DATETIME
$SAMPLE_NUMBER_HAS_PREFIX   = true;    // true: guarda "LLD-258-0294"; false: guarda "0294"
$AUTO_APPEND_YEAR_DEFAULT   = false;   // <<< AHORA POR DEFECTO NO SE ANEXA AÑO
// ------------------------------------

page_require_level(2);
if (!function_exists('current_user') || !current_user()) {
  json_error(401, 'No autenticado. Inicia sesión.');
}

ini_set('display_errors', '0');
header('Cache-Control: no-store');

$prefix_raw = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
$prefix_raw = preg_replace('/[^A-Za-z0-9\-_]/', '', $prefix_raw);
if ($prefix_raw === '') json_error(400, 'Falta ?prefix=');

// Permite forzar comportamiento por URL: &append_year=1/0
$append_year = isset($_GET['append_year']) ? ((int)$_GET['append_year'] === 1) : $AUTO_APPEND_YEAR_DEFAULT;

$now   = new DateTimeImmutable('now');
$yy    = $now->format('y');
$month = (int)$now->format('n');

// Si decides anexar año: solo lo hacemos si NO termina ya en "-dd" (2 dígitos)
function resolve_prefix(string $raw, string $yy, bool $appendYear): string {
  if (!$appendYear) return $raw;
  if (preg_match('/-\d{2}$/', $raw)) return $raw; // ya trae -yy
  return $raw . '-' . $yy; // anexa como un segmento nuevo: LLD-258-25
}

// Prefijo resuelto según tu configuración
$prefix_current = resolve_prefix($prefix_raw, $yy, $append_year);

// ----------- MUESTRAS DE HOY (solo para mostrar en la vista) -----------
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

// ---------------- UTILIDADES ----------------
global $db;
function scalar_query(string $sql): ?string {
  $rows = find_by_sql($sql);
  if (!is_array($rows) || !isset($rows[0])) return null;
  $first = $rows[0];
  $val = reset($first);
  return $val === null ? null : (string)$val;
}
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
  $val = scalar_query($sql);
  return $val !== null ? (int)$val : 0;
}
function max_suffix_for_unprefixed_number(string $id_prefix_like): int {
  $like = $GLOBALS['db']->escape($id_prefix_like);
  $sql = "
    SELECT MAX(CAST(Sample_Number AS UNSIGNED)) AS max_n
    FROM lab_test_requisition_form
    WHERE Sample_ID LIKE '{$like}-%'
      AND Sample_Number REGEXP '^[0-9]{1,8}$'
  ";
  $val = scalar_query($sql);
  return $val !== null ? (int)$val : 0;
}
function value_exists_in_col(string $col, string $value): bool {
  $col_safe = preg_replace('/[^A-Za-z0-9_]/', '', $col);
  $val      = $GLOBALS['db']->escape($value);
  $sql = sprintf(
    "SELECT COUNT(*) AS c FROM lab_test_requisition_form WHERE %s = '%s'",
    $col_safe, $val
  );
  $rows = find_by_sql($sql);
  return isset($rows[0]['c']) && (int)$rows[0]['c'] > 0;
}
function pair_exists(string $id, string $num): bool {
  $id  = $GLOBALS['db']->escape($id);
  $num = $GLOBALS['db']->escape($num);
  $sql = "SELECT COUNT(*) AS c
          FROM lab_test_requisition_form
          WHERE Sample_ID = '{$id}' AND Sample_Number = '{$num}'";
  $rows = find_by_sql($sql);
  return isset($rows[0]['c']) && (int)$rows[0]['c'] > 0;
}
function build_sample_name(string $id, string $num): string {
  return $id . ' | ' . $num;
}

// ---------------- LÓGICA DE CONSECUTIVO ----------------
// Importante: AHORA buscamos con el prefijo EXACTO que envías (p.ej. "LLD-258")
$maxN_id  = max_suffix_for_prefix_col('Sample_ID', $prefix_current);
$maxN_num = $SAMPLE_NUMBER_HAS_PREFIX
  ? max_suffix_for_prefix_col('Sample_Number', $prefix_current)
  : max_suffix_for_unprefixed_number($prefix_current);

// Próximos individuales
$next_id_num  = $maxN_id  + 1;
$next_num_num = $maxN_num + 1;

// Recomendado (alinear ambos)
$recommendedNext = max($next_id_num, $next_num_num);
$pad = str_pad((string)$recommendedNext, 4, '0', STR_PAD_LEFT);

$candidate_id  = $prefix_current . '-' . $pad;
$candidate_num = $SAMPLE_NUMBER_HAS_PREFIX ? ($prefix_current . '-' . $pad) : $pad;

// Asegurar que no exista ni ID, ni Number, ni la combinación
while ( value_exists_in_col('Sample_ID', $candidate_id)
     || value_exists_in_col('Sample_Number', $candidate_num)
     || pair_exists($candidate_id, $candidate_num) ) {
  $recommendedNext++;
  $pad = str_pad((string)$recommendedNext, 4, '0', STR_PAD_LEFT);
  $candidate_id  = $prefix_current . '-' . $pad;
  $candidate_num = $SAMPLE_NUMBER_HAS_PREFIX ? ($prefix_current . '-' . $pad) : $pad;
}

$payload = [
  'params' => [
    'base_prefix'       => $prefix_raw,
    'resolved_prefix'   => $prefix_current,   // ahora suele ser igual al base (no añade año)
    'append_year_used'  => $append_year ? 'yes' : 'no',
    'yy'                => $yy,
    'number_has_prefix' => $SAMPLE_NUMBER_HAS_PREFIX ? 'yes' : 'no',
  ],
  'today_count' => $todayCount,
  'today'       => $todayRows,
  'next' => [
    'sample_id' => [
      'max_found'   => $maxN_id,
      'next_number' => $next_id_num,
      'next_id'     => $prefix_current . '-' . str_pad((string)$next_id_num, 4, '0', STR_PAD_LEFT),
    ],
    'sample_number' => [
      'max_found'   => $maxN_num,
      'next_number' => $next_num_num,
      'next_value'  => $SAMPLE_NUMBER_HAS_PREFIX
        ? $prefix_current . '-' . str_pad((string)$next_num_num, 4, '0', STR_PAD_LEFT)
        : str_pad((string)$next_num_num, 4, '0', STR_PAD_LEFT),
    ],
    'recommended' => [
      'recommended_next' => $recommendedNext,
      'next_padded'      => $pad,
      'use_for_id'       => $candidate_id,
      'use_for_number'   => $candidate_num,
      'sample_name'      => build_sample_name($candidate_id, $candidate_num),
    ],
  ],
];

json_ok($payload);
