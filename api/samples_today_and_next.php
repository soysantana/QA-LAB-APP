<?php
declare(strict_types=1);
require_once('../config/load.php');
date_default_timezone_set('America/Santo_Domingo');

// Si Registed_Date es DATETIME, cambia a true
$REGISTERED_IS_DATETIME = false;

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

page_require_level(2);
if (!function_exists('current_user') || !current_user()) json_error(401,'No autenticado');

ini_set('display_errors','0');
header('Cache-Control: no-store');

// ---------- Reglas de prefijos ----------
$NO_YEAR = [
  'LLD-258','SD3-258','SD2-258','SD1-258'
];
$WITH_YEAR_PEGADO = [
  'PVDJ-AGG','PVDJ-AGG-INV','PVDJ-AGG-DIO','LBOR','PVDJ-MISC'
];

function mysql_regex_quote(string $s): string {
  return preg_replace('/([\\\\.^$|()?*+\\[\\]{}-])/', '\\\\$1', $s);
}
function resolve_prefix(string $base, array $NO_YEAR, array $WITH_YEAR_PEGADO, string $yy): string {
  // Si ya termina con -dd (dos dígitos), respeta tal cual
  if (preg_match('/-\d{2}$/', $base)) return $base;

  if (in_array($base, $NO_YEAR, true)) {
    // NO se agrega año
    return $base;
  }
  if (in_array($base, $WITH_YEAR_PEGADO, true)) {
    // SE agrega año pegado (sin guion): PVDJ-AGG + 25 => PVDJ-AGG25
    return $base . $yy;
  }
  // Por defecto, no agregamos año (seguridad)
  return $base;
}

function scalar_row(string $sql): ?array {
  $rows = find_by_sql($sql);
  if (!is_array($rows) || !isset($rows[0])) return null;
  return $rows[0];
}

$action = isset($_GET['action']) ? trim((string)$_GET['action']) : 'next';
$yy = (new DateTimeImmutable('now'))->format('y');

// ---------- Siguiente por prefijo ----------
if ($action === 'next') {
  $base_prefix = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
  $base_prefix = preg_replace('/[^A-Za-z0-9\-_]/','',$base_prefix);
  if ($base_prefix==='') json_error(400,'Falta ?prefix=');

  $pad_len = isset($_GET['pad']) ? max(1,(int)$_GET['pad']) : 4;

  $resolved = resolve_prefix($base_prefix, $NO_YEAR, $WITH_YEAR_PEGADO, $yy);

  global $db;
  $like = $db->escape($resolved);
  $rx   = '^'.mysql_regex_quote($resolved).'-[0-9]+$';
  $rx_e = $db->escape($rx);

  // Buscar última exacta en Sample_ID
  $sql_last_id = "
    SELECT TRIM(Sample_ID) AS val,
           CAST(SUBSTRING_INDEX(TRIM(Sample_ID), '-', -1) AS UNSIGNED) AS n
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID) LIKE '{$like}-%'
      AND TRIM(Sample_ID) REGEXP '{$rx_e}'
    ORDER BY n DESC
    LIMIT 1
  ";
  // ...y en Sample_Number
  $sql_last_num = "
    SELECT TRIM(Sample_Number) AS val,
           CAST(SUBSTRING_INDEX(TRIM(Sample_Number), '-', -1) AS UNSIGNED) AS n
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_Number) LIKE '{$like}-%'
      AND TRIM(Sample_Number) REGEXP '{$rx_e}'
    ORDER BY n DESC
    LIMIT 1
  ";

  $row_id  = scalar_row($sql_last_id);
  $row_num = scalar_row($sql_last_num);

  $best_val = null; $best_n = 0; $from_col = null;
  if ($row_id && isset($row_id['n']) && (int)$row_id['n'] > $best_n) {
    $best_n = (int)$row_id['n']; $best_val = (string)$row_id['val']; $from_col = 'Sample_ID';
  }
  if ($row_num && isset($row_num['n']) && (int)$row_num['n'] > $best_n) {
    $best_n = (int)$row_num['n']; $best_val = (string)$row_num['val']; $from_col = 'Sample_Number';
  }

  $next_n   = ($best_n > 0) ? ($best_n + 1) : 1;
  $next_pad = str_pad((string)$next_n, $pad_len, '0', STR_PAD_LEFT);
  $use_this = $resolved . '-' . $next_pad;

  json_ok([
    'params' => [
      'base_prefix' => $base_prefix,
      'resolved'    => $resolved,
      'yy'          => $yy,
      'pad_len'     => $pad_len
    ],
    'last_found' => [
      'from_column' => $from_col,
      'value'       => $best_val,
      'suffix_n'    => $best_n,
    ],
    'suggestion' => [
      'next_n'      => $next_n,
      'next_padded' => $next_pad,
      'use_this'    => $use_this, // <- Úsalo como siguiente
    ],
  ]);
}

// ---------- Listado de hoy (opcionalmente filtrado por prefijo base) ----------
if ($action === 'today') {
  $base_prefix = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
  $base_prefix = preg_replace('/[^A-Za-z0-9\-_]/','',$base_prefix);
  $resolved = '';
  if ($base_prefix!=='') {
    $resolved = resolve_prefix($base_prefix, $NO_YEAR, $WITH_YEAR_PEGADO, $yy);
  }

  $dateFilter = $REGISTERED_IS_DATETIME
    ? "Registed_Date >= CURDATE() AND Registed_Date < CURDATE() + INTERVAL 1 DAY"
    : "Registed_Date = CURDATE()";

  if ($resolved!=='') {
    global $db;
    $like = $db->escape($resolved);
    $where = "$dateFilter AND (TRIM(Sample_ID) LIKE '{$like}-%' OR TRIM(Sample_Number) LIKE '{$like}-%')";
  } else {
    $where = $dateFilter;
  }

  $sql = "
    SELECT id, Sample_ID, Sample_Number, Test_Type, Material_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE $where
    ORDER BY id DESC
  ";
  $rows = find_by_sql($sql);

  json_ok([
    'params' => [
      'base_prefix' => $base_prefix,
      'resolved'    => $resolved,
      'yy'          => $yy
    ],
    'today_count' => is_array($rows) ? count($rows) : 0,
    'today'       => $rows
  ]);
}

json_error(400,'Acción no válida. Usa action=next o action=today');
