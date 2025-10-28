<?php
declare(strict_types=1);
require_once('../config/load.php');
date_default_timezone_set('America/Santo_Domingo');

function json_ok(array $data){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok'=>true]+$data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function json_error(int $code, string $msg, array $extra=[]){ http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok'=>false,'error'=>$msg]+$extra, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }

page_require_level(2);
if (!function_exists('current_user') || !current_user()) json_error(401,'No autenticado');

ini_set('display_errors','0');
header('Cache-Control: no-store');

// ------------ Parámetros ------------
$id_prefix_raw = isset($_GET['id_prefix']) ? trim((string)$_GET['id_prefix']) : '';
$id_prefix_raw = preg_replace('/[^A-Za-z0-9\-_]/','',$id_prefix_raw);
if ($id_prefix_raw==='') json_error(400,'Falta ?id_prefix=');

// Cómo guardas Sample_Number: number_only (recomendado) o with_prefix
$number_mode = isset($_GET['number_mode']) ? trim((string)$_GET['number_mode']) : 'number_only';
// Padding (4 => 0001)
$pad_len = isset($_GET['pad']) ? max(1,(int)$_GET['pad']) : 4;

// Si quieres autodetectar mode, cambia a 'auto'. Mantengo por defecto number_only como pediste.
if ($number_mode !== 'number_only' && $number_mode !== 'with_prefix') {
  $number_mode = 'number_only';
}

global $db;

function scalar_query(string $sql){
  $r = find_by_sql($sql);
  if (!is_array($r) || !isset($r[0])) return null;
  $first = $r[0];
  $val = reset($first);
  return $val===null ? null : $val;
}

function exists_pair(string $id, string $num): bool {
  $id  = $GLOBALS['db']->escape($id);
  $num = $GLOBALS['db']->escape($num);
  $sql = "SELECT COUNT(*) c
          FROM lab_test_requisition_form
          WHERE TRIM(Sample_ID)='{$id}' AND TRIM(Sample_Number)='{$num}'";
  $r = find_by_sql($sql);
  return isset($r[0]['c']) && (int)$r[0]['c']>0;
}

// ¿Existe el SUFIJO N ya usado en Sample_Number en cualquiera de los dos formatos?
function exists_number_suffix(int $n): bool {
  $n_str = (string)$n;

  // Coincidencias tipo: "0001", "12", etc.
  $sql_plain = "
    SELECT COUNT(*) c
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_Number) REGEXP '^[0-9]{1,12}$'
      AND CAST(TRIM(Sample_Number) AS UNSIGNED) = {$n}
    LIMIT 1
  ";
  $c1 = (int)(scalar_query($sql_plain) ?? 0);
  if ($c1 > 0) return true;

  // Coincidencias tipo: "ALGO-0001", "PREF-12", etc. (cualquier prefijo)
  $sql_pref = "
    SELECT COUNT(*) c
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_Number) REGEXP '-[0-9]{1,12}$'
      AND CAST(SUBSTRING_INDEX(TRIM(Sample_Number), '-', -1) AS UNSIGNED) = {$n}
    LIMIT 1
  ";
  $c2 = (int)(scalar_query($sql_pref) ?? 0);
  return $c2 > 0;
}

// Máximo global de SUFIJO (independiente del Sample_ID)
function max_global_suffix(): int {
  // Máximo entre:
  //  A) números "puros" en Sample_Number
  //  B) sufijos numéricos al final cuando Sample_Number tiene prefijo
  $sql = "
    SELECT GREATEST(
      IFNULL((
        SELECT MAX(CAST(TRIM(SN) AS UNSIGNED))
        FROM (
          SELECT Sample_Number AS SN FROM lab_test_requisition_form
        ) t1
        WHERE TRIM(SN) REGEXP '^[0-9]{1,12}$'
      ), 0),
      IFNULL((
        SELECT MAX(CAST(SUBSTRING_INDEX(TRIM(SN2), '-', -1) AS UNSIGNED))
        FROM (
          SELECT Sample_Number AS SN2 FROM lab_test_requisition_form
        ) t2
        WHERE TRIM(SN2) REGEXP '-[0-9]{1,12}$'
      ), 0)
    ) AS max_n
  ";
  $val = scalar_query($sql);
  return $val!==null ? (int)$val : 0;
}

// --------- Cálculo principal ---------
$max_suffix = max_global_suffix();
$next_num   = $max_suffix + 1;
$pad        = str_pad((string)$next_num, $pad_len, '0', STR_PAD_LEFT);

// Construcción de candidatos
$id_prefix  = $id_prefix_raw;                   // ej. "LLD-258", "S15", "265"
$cand_id    = $id_prefix . '-' . $pad;         // Sample_ID final

if ($number_mode === 'with_prefix') {
  $cand_num = $id_prefix . '-' . $pad;         // Sample_Number con prefijo
} else {
  $cand_num = $pad;                             // Sample_Number solo número (recomendado)
}

// Evitar colisiones: si el sufijo ya existe en Sample_Number (en cualquier estilo)
// o la pareja (ID, Number) ya existe → incrementa hasta encontrar libre
while ( exists_number_suffix((int)$next_num) || exists_pair($cand_id, $cand_num) ) {
  $next_num++;
  $pad     = str_pad((string)$next_num, $pad_len, '0', STR_PAD_LEFT);
  $cand_id = $id_prefix . '-' . $pad;
  $cand_num= ($number_mode==='with_prefix') ? ($id_prefix.'-'.$pad) : $pad;
}

json_ok([
  'params' => [
    'id_prefix'   => $id_prefix_raw,
    'number_mode' => $number_mode,   // number_only | with_prefix
    'pad_len'     => $pad_len
  ],
  'status' => [
    'max_suffix_found_global' => $max_suffix,
    'next_number_candidate'   => $max_suffix + 1
  ],
  'recommended' => [
    'recommended_next' => (int)$next_num,
    'next_padded'      => $pad,
    'use_for_id'       => $cand_id,
    'use_for_number'   => $cand_num,
    'sample_name'      => $cand_id . ' | ' . $cand_num
  ]
]);
