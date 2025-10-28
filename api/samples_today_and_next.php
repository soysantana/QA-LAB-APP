<?php
declare(strict_types=1);
require_once('../config/load.php');
date_default_timezone_set('America/Santo_Domingo');

/* ============== Ajustes ============== */
$REGISTERED_IS_DATETIME = false; // true si Registed_Date es DATETIME
/* ==================================== */

function json_ok(array $data){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>true]+$data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function json_error(int $code, string $msg, array $extra=[]){
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'error'=>$msg]+$extra, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

/* --- Auth --- */
page_require_level(2);
if (!function_exists('current_user') || !current_user()) {
  json_error(401,'No autenticado');
}

/* --- Hardening salida --- */
ini_set('display_errors','0');
header('Cache-Control: no-store');

/* --- Helpers DB --- */
function scalar_query(string $sql){
  $rows = find_by_sql($sql);
  if (!is_array($rows) || !isset($rows[0])) return null;
  $first = $rows[0];
  $val = reset($first);
  return $val===null ? null : $val;
}
function exists_col(string $col, string $value): bool {
  $col_safe = preg_replace('/[^A-Za-z0-9_]/','',$col);
  $val = $GLOBALS['db']->escape($value);
  $sql = "SELECT COUNT(*) c FROM lab_test_requisition_form WHERE TRIM({$col_safe}) = '{$val}'";
  $rows = find_by_sql($sql);
  return isset($rows[0]['c']) && (int)$rows[0]['c']>0;
}
function pair_exists(string $id, string $num): bool {
  $id  = $GLOBALS['db']->escape($id);
  $num = $GLOBALS['db']->escape($num);
  $sql = "SELECT COUNT(*) c
          FROM lab_test_requisition_form
          WHERE TRIM(Sample_ID)='{$id}' AND TRIM(Sample_Number)='{$num}'";
  $rows = find_by_sql($sql);
  return isset($rows[0]['c']) && (int)$rows[0]['c']>0;
}
function build_sample_name(string $id, string $num): string { return $id.' | '.$num; }

/* --- Máximos por prefijo --- */
function max_suffix_in_id(string $id_prefix): int {
  $p = $GLOBALS['db']->escape($id_prefix);
  $sql = "
    SELECT MAX(CAST(SUBSTRING_INDEX(TRIM(Sample_ID), '-', -1) AS UNSIGNED)) AS max_n
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID) LIKE '{$p}-%'
  ";
  $val = scalar_query($sql);
  return $val!==null ? (int)$val : 0;
}
/* number_mode:
   - 'number_only' => Sample_Number guarda SOLO el número; se filtra por filas cuyo Sample_ID empiece por id_prefix
   - 'with_prefix' => Sample_Number tiene también prefijo; se filtra por number_prefix
*/
function max_suffix_in_number(string $id_prefix, string $number_mode, ?string $number_prefix): int {
  if ($number_mode === 'with_prefix') {
    $np = $GLOBALS['db']->escape($number_prefix ?? $id_prefix);
    $sql = "
      SELECT MAX(CAST(SUBSTRING_INDEX(TRIM(Sample_Number), '-', -1) AS UNSIGNED)) AS max_n
      FROM lab_test_requisition_form
      WHERE TRIM(Sample_Number) LIKE '{$np}-%'
    ";
  } else { // number_only
    $ip = $GLOBALS['db']->escape($id_prefix);
    $sql = "
      SELECT MAX(CAST(TRIM(Sample_Number) AS UNSIGNED)) AS max_n
      FROM lab_test_requisition_form
      WHERE TRIM(Sample_ID) LIKE '{$ip}-%'
        AND TRIM(Sample_Number) REGEXP '^[0-9]{1,12}$'
    ";
  }
  $val = scalar_query($sql);
  return $val!==null ? (int)$val : 0;
}

/* --------- Ruteo por action --------- */
$action = isset($_GET['action']) ? trim((string)$_GET['action']) : 'next';

if ($action === 'today') {
  if ($REGISTERED_IS_DATETIME) {
    $sqlToday = "
      SELECT id, Sample_ID, Sample_Number, Test_Type, Material_Type, Registed_Date
      FROM lab_test_requisition_form
      WHERE Registed_Date >= CURDATE() AND Registed_Date < CURDATE() + INTERVAL 1 DAY
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
  $rows = find_by_sql($sqlToday);
  $todayCount = is_array($rows) ? count($rows) : 0;
  json_ok(['today_count'=>$todayCount, 'today'=>$rows]);
}

/* --------- action=next (principal) --------- */
$id_prefix_raw    = isset($_GET['id_prefix']) ? trim((string)$_GET['id_prefix']) : '';
$id_prefix_raw    = preg_replace('/[^A-Za-z0-9\-_]/','',$id_prefix_raw);
if ($id_prefix_raw==='') json_error(400,'Falta ?id_prefix=');

$number_mode      = isset($_GET['number_mode']) ? trim((string)$_GET['number_mode']) : 'number_only';
if ($number_mode!=='number_only' && $number_mode!=='with_prefix') $number_mode='number_only';

$number_prefix_raw= isset($_GET['number_prefix']) ? trim((string)$_GET['number_prefix']) : '';
$number_prefix_raw= preg_replace('/[^A-Za-z0-9\-_]/','',$number_prefix_raw);
if ($number_mode==='with_prefix' && $number_prefix_raw==='') {
  // Si no mandas number_prefix, asumimos el mismo que id_prefix
  $number_prefix_raw = $id_prefix_raw;
}

$pad_len          = isset($_GET['pad']) ? max(1,(int)$_GET['pad']) : 4;

$id_prefix        = $id_prefix_raw;
$number_prefix    = ($number_mode==='with_prefix') ? $number_prefix_raw : null;

/* --- Cálculo de máximos considerando ambos --- */
$max_id  = max_suffix_in_id($id_prefix);
$max_num = max_suffix_in_number($id_prefix, $number_mode, $number_prefix);

/* siguientes “independientes” */
$next_id_num  = $max_id  + 1;
$next_num_num = $max_num + 1;

/* recomendado = máximo común para alinear ambos */
$recommended  = max($next_id_num, $next_num_num);
$pad          = str_pad((string)$recommended, $pad_len, '0', STR_PAD_LEFT);

/* construir candidatos */
$cand_id  = $id_prefix . '-' . $pad;
$cand_num = ($number_mode==='with_prefix')
  ? (($number_prefix ?? $id_prefix) . '-' . $pad)
  : $pad;

/* evitar colisiones (ID / Number / parejita) */
while ( exists_col('Sample_ID', $cand_id)
     || exists_col('Sample_Number', $cand_num)
     || pair_exists($cand_id, $cand_num) ) {
  $recommended++;
  $pad     = str_pad((string)$recommended, $pad_len, '0', STR_PAD_LEFT);
  $cand_id = $id_prefix . '-' . $pad;
  $cand_num= ($number_mode==='with_prefix')
              ? (($number_prefix ?? $id_prefix) . '-' . $pad)
              : $pad;
}

/* respuesta */
json_ok([
  'params' => [
    'id_prefix'     => $id_prefix,
    'number_mode'   => $number_mode,           // number_only | with_prefix
    'number_prefix' => $number_prefix,         // si aplica
    'pad_len'       => $pad_len
  ],
  'status' => [
    'sample_id' => [
      'max_found'   => $max_id,
      'next_number' => $next_id_num,
      'next_id'     => $id_prefix . '-' . str_pad((string)$next_id_num, $pad_len, '0', STR_PAD_LEFT),
    ],
    'sample_number' => [
      'scope'       => ($number_mode==='with_prefix' ? "LIKE {$number_prefix}-%" : "ID LIKE {$id_prefix}-% (numérico)"),
      'max_found'   => $max_num,
      'next_number' => $next_num_num,
      'next_value'  => ($number_mode==='with_prefix')
                        ? (($number_prefix ?? $id_prefix) . '-' . str_pad((string)$next_num_num, $pad_len, '0', STR_PAD_LEFT))
                        : str_pad((string)$next_num_num, $pad_len, '0', STR_PAD_LEFT),
    ],
  ],
  'recommended' => [
    'recommended_next' => $recommended,
    'next_padded'      => $pad,
    'use_for_id'       => $cand_id,
    'use_for_number'   => $cand_num,
    'sample_name'      => build_sample_name($cand_id, $cand_num),
  ],
]);
