<?php
declare(strict_types=1);
require_once('../config/load.php');
date_default_timezone_set('America/Santo_Domingo');

function json_ok(array $data){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok'=>true]+$data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function json_error(int $code, string $msg, array $extra=[]){ http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok'=>false,'error'=>$msg]+$extra, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }

// ====== AJUSTES RÁPIDOS ======
$REGISTERED_IS_DATETIME   = false; // si Registed_Date es DATETIME
$SAMPLE_NUMBER_HAS_PREFIX = true;  // true: "LLD-258-0294"; false: "0294"
// =============================

page_require_level(2);
if (!function_exists('current_user') || !current_user()) json_error(401,'No autenticado');

ini_set('display_errors','0');
header('Cache-Control: no-store');

$debug       = isset($_GET['debug']) ? (int)$_GET['debug'] : 0;
$prefix_raw  = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
$prefix_raw  = preg_replace('/[^A-Za-z0-9\-_]/', '', $prefix_raw);
if ($prefix_raw==='') json_error(400,'Falta ?prefix=');

$prefix = $prefix_raw; // << NO anexa año
global $db;

// --- Helpers ---
function mysql_regex_quote(string $s): string {
  // Escapa todo lo especial para REGEXP en MySQL
  return preg_replace('/([\\\\.^$|()?*+\\[\\]{}-])/', '\\\\$1', $s);
}
function scalar_query(string $sql){ $r=find_by_sql($sql); if(!is_array($r)||!isset($r[0])) return null; $f=$r[0]; $v=reset($f); return $v===null?null:$v; }

function max_suffix_exact(string $col, string $prefix, array &$dbg=null): int {
  // Coincidencias EXACTAS tipo: "<prefix>-1234" sin nada más; usa TRIM para tolerar espacios almacenados
  $col_safe   = preg_replace('/[^A-Za-z0-9_]/','',$col);
  $prefix_sql = $GLOBALS['db']->escape($prefix);         // para usar en SQL como literal
  $rx_prefix  = mysql_regex_quote($prefix);
  $rx_sql     = $GLOBALS['db']->escape("^{$rx_prefix}-[0-9]+$"); // anclado

  $sql = "
    SELECT
      MAX(CAST(SUBSTRING(TRIM({$col_safe}), CHAR_LENGTH('{$prefix_sql}') + 2) AS UNSIGNED)) AS max_n
    FROM lab_test_requisition_form
    WHERE TRIM({$col_safe}) LIKE '{$prefix_sql}-%'
      AND TRIM({$col_safe}) REGEXP '{$rx_sql}'
  ";
  if (is_array($dbg)) $dbg[]=['col'=>$col,'sql'=>$sql];
  $val = scalar_query($sql);
  return $val!==null ? (int)$val : 0;
}

function max_suffix_unprefixed_number(string $id_prefix, array &$dbg=null): int {
  // Para cuando Sample_Number almacena SOLO el número (0001, 12, 0099) y queremos el máx SOLO entre filas cuyo Sample_ID empieza por <prefix>-
  $id_prefix_sql = $GLOBALS['db']->escape($id_prefix);
  $sql = "
    SELECT MAX(CAST(TRIM(Sample_Number) AS UNSIGNED)) AS max_n
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID) LIKE '{$id_prefix_sql}-%'
      AND TRIM(Sample_Number) REGEXP '^[0-9]{1,12}$'
  ";
  if (is_array($dbg)) $dbg[]=['col'=>'Sample_Number(unpref)','sql'=>$sql];
  $val = scalar_query($sql);
  return $val!==null ? (int)$val : 0;
}

function exists_col(string $col, string $value, array &$dbg=null): bool {
  $col_safe = preg_replace('/[^A-Za-z0-9_]/','',$col);
  $val_sql  = $GLOBALS['db']->escape($value);
  $sql = "SELECT COUNT(*) c FROM lab_test_requisition_form WHERE TRIM({$col_safe}) = '{$val_sql}'";
  if (is_array($dbg)) $dbg[]=['check'=>$col,'sql'=>$sql];
  $r = find_by_sql($sql);
  return isset($r[0]['c']) && (int)$r[0]['c']>0;
}
function pair_exists(string $id, string $num, array &$dbg=null): bool {
  $id_sql  = $GLOBALS['db']->escape($id);
  $num_sql = $GLOBALS['db']->escape($num);
  $sql = "SELECT COUNT(*) c FROM lab_test_requisition_form WHERE TRIM(Sample_ID)='{$id_sql}' AND TRIM(Sample_Number)='{$num_sql}'";
  if (is_array($dbg)) $dbg[]=['check'=>'pair','sql'=>$sql];
  $r = find_by_sql($sql);
  return isset($r[0]['c']) && (int)$r[0]['c']>0;
}
function build_sample_name(string $id, string $num): string { return $id.' | '.$num; }

// --- (Opcional) muestras de hoy para la vista ---
if ($REGISTERED_IS_DATETIME) {
  $sqlToday="SELECT id,Sample_ID,Sample_Number,Test_Type,Material_Type,Registed_Date
             FROM lab_test_requisition_form
             WHERE Registed_Date>=CURDATE() AND Registed_Date<CURDATE()+INTERVAL 1 DAY
             ORDER BY Registed_Date DESC,id DESC";
} else {
  $sqlToday="SELECT id,Sample_ID,Sample_Number,Test_Type,Material_Type,Registed_Date
             FROM lab_test_requisition_form
             WHERE Registed_Date = CURDATE()
             ORDER BY id DESC";
}
$todayRows  = find_by_sql($sqlToday);
$todayCount = is_array($todayRows)?count($todayRows):0;

// --- Cálculo robusto ---
$dbg = [];

$max_id  = max_suffix_exact('Sample_ID', $prefix, $dbg);
$max_num = $SAMPLE_NUMBER_HAS_PREFIX
  ? max_suffix_exact('Sample_Number', $prefix, $dbg)
  : max_suffix_unprefixed_number($prefix, $dbg);

// Siguientes independientes
$next_id_num  = $max_id  + 1;
$next_num_num = $max_num + 1;

// Recomendado (alineamos ambos)
$recommended  = max($next_id_num, $next_num_num);
$pad          = str_pad((string)$recommended, 4, '0', STR_PAD_LEFT);
$cand_id      = $prefix.'-'.$pad;
$cand_num     = $SAMPLE_NUMBER_HAS_PREFIX ? ($prefix.'-'.$pad) : $pad;

// Evitar colisiones (ID/Number/pareja)
while ( exists_col('Sample_ID',$cand_id,$dbg)
     || exists_col('Sample_Number',$cand_num,$dbg)
     || pair_exists($cand_id,$cand_num,$dbg) ) {
  $recommended++;
  $pad      = str_pad((string)$recommended, 4, '0', STR_PAD_LEFT);
  $cand_id  = $prefix.'-'.$pad;
  $cand_num = $SAMPLE_NUMBER_HAS_PREFIX ? ($prefix.'-'.$pad) : $pad;
}

// --- Debug útil: ejemplos que sí matchean tu prefijo ---
if ($debug) {
  $prefix_sql = $db->escape($prefix);
  $rx_prefix  = mysql_regex_quote($prefix);
  $rx_sql     = $db->escape("^{$rx_prefix}-[0-9]+$");
  $examples_id = find_by_sql("
    SELECT TRIM(Sample_ID) v FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID) LIKE '{$prefix_sql}-%'
    ORDER BY id DESC LIMIT 10
  ");
  $examples_num = find_by_sql("
    SELECT TRIM(Sample_Number) v FROM lab_test_requisition_form
    WHERE ".($SAMPLE_NUMBER_HAS_PREFIX
      ? "TRIM(Sample_Number) LIKE '{$prefix_sql}-%' AND TRIM(Sample_Number) REGEXP '{$rx_sql}'"
      : "TRIM(Sample_ID) LIKE '{$prefix_sql}-%' AND TRIM(Sample_Number) REGEXP '^[0-9]{1,12}$'")
    ." ORDER BY id DESC LIMIT 10
  ");
}

$payload = [
  'params'=>[
    'base_prefix'=>$prefix_raw,
    'resolved_prefix'=>$prefix,
    'number_has_prefix'=>$SAMPLE_NUMBER_HAS_PREFIX?'yes':'no'
  ],
  'status'=>[
    'sample_id'=>[
      'max_found'=>$max_id,
      'next_number'=>$next_id_num,
      'next_id'=>$prefix.'-'.str_pad((string)$next_id_num,4,'0',STR_PAD_LEFT),
    ],
    'sample_number'=>[
      'max_found'=>$max_num,
      'next_number'=>$next_num_num,
      'next_value'=>$SAMPLE_NUMBER_HAS_PREFIX ? ($prefix.'-'.str_pad((string)$next_num_num,4,'0',STR_PAD_LEFT))
                                              : str_pad((string)$next_num_num,4,'0',STR_PAD_LEFT),
    ],
  ],
  'recommended'=>[
    'recommended_next'=>$recommended,
    'next_padded'=>$pad,
    'use_for_id'=>$cand_id,
    'use_for_number'=>$cand_num,
    'sample_name'=>build_sample_name($cand_id,$cand_num),
  ],
  'today_count'=>$todayCount,
  'today'=>$todayRows,
];

if ($debug) {
  $payload['debug']=[
    'queries'=>$dbg,
    'examples'=>[
      'Sample_ID_like_prefix'=>$examples_id ?? [],
      'Sample_Number_scope'=>$examples_num ?? [],
    ],
  ];
}

json_ok($payload);
