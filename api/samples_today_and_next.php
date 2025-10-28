<?php
declare(strict_types=1);
require_once('../config/load.php');
date_default_timezone_set('America/Santo_Domingo');

// Configura según tu DB:
$REGISTERED_IS_DATETIME = false; // true si Registed_Date es DATETIME

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
if (!function_exists('current_user') || !current_user()) {
  json_error(401,'No autenticado');
}

ini_set('display_errors','0');
header('Cache-Control: no-store');

// ---------- Helpers ----------
function scalar_row(string $sql): ?array {
  $rows = find_by_sql($sql);
  if (!is_array($rows) || !isset($rows[0])) return null;
  return $rows[0];
}
function scalar_val(string $sql){
  $row = scalar_row($sql);
  if (!$row) return null;
  $v = reset($row);
  return $v===null ? null : $v;
}
function mysql_regex_quote(string $s): string {
  return preg_replace('/([\\\\.^$|()?*+\\[\\]{}-])/', '\\\\$1', $s);
}
function resolve_year_prefix(string $base, bool $autoYear, string $yy): string {
  if (!$autoYear) return $base;
  // Si ya termina con "-dd" (2 dígitos), no anexes
  if (preg_match('/-\d{2}$/', $base)) return $base;
  return $base . '-' . $yy; // ej. SD3-258 -> SD3-258-25
}

$action = isset($_GET['action']) ? trim((string)$_GET['action']) : 'next';
$yy = (new DateTimeImmutable('now'))->format('y');

// ---------- Acción: siguiente por prefijo ----------
if ($action === 'next') {
  $base_prefix = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
  $base_prefix = preg_replace('/[^A-Za-z0-9\-_]/','',$base_prefix);
  if ($base_prefix==='') json_error(400,'Falta ?prefix=');

  $auto_year = isset($_GET['auto_year']) ? (int)$_GET['auto_year']===1 : true; // por defecto SÍ agrega -yy
  $pad_len   = isset($_GET['pad']) ? max(1,(int)$_GET['pad']) : 4;

  $resolved  = resolve_year_prefix($base_prefix, $auto_year, $yy);

  global $db;
  $p_like = $db->escape($resolved);
  $rx = '^'.mysql_regex_quote($resolved).'-[0-9]+$';
  $rx_sql = $db->escape($rx);

  // Busca la última coincidencia EXACTA en Sample_ID
  $sql_last_id = "
    SELECT TRIM(Sample_ID) AS val,
           CAST(SUBSTRING_INDEX(TRIM(Sample_ID), '-', -1) AS UNSIGNED) AS n
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID) LIKE '{$p_like}-%'
      AND TRIM(Sample_ID) REGEXP '{$rx_sql}'
    ORDER BY n DESC
    LIMIT 1
  ";
  // ... y en Sample_Number (por si alguna vez se guardó así)
  $sql_last_num = "
    SELECT TRIM(Sample_Number) AS val,
           CAST(SUBSTRING_INDEX(TRIM(Sample_Number), '-', -1) AS UNSIGNED) AS n
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_Number) LIKE '{$p_like}-%'
      AND TRIM(Sample_Number) REGEXP '{$rx_sql}'
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
      'auto_year'   => $auto_year ? 1 : 0,
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
      'use_this'    => $use_this,
    ],
  ]);
}

// ---------- Acción: listado de hoy (con filtro opcional por prefijo resuelto) ----------
if ($action === 'today') {
  $base_prefix = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
  $base_prefix = preg_replace('/[^A-Za-z0-9\-_]/','',$base_prefix);
  $auto_year = isset($_GET['auto_year']) ? (int)$_GET['auto_year']===1 : false; // por defecto NO fuerza año aquí
  $resolved  = $base_prefix ? resolve_year_prefix($base_prefix, $auto_year, $yy) : '';

  $dateFilter = $REGISTERED_IS_DATETIME
    ? "Registed_Date >= CURDATE() AND Registed_Date < CURDATE() + INTERVAL 1 DAY"
    : "Registed_Date = CURDATE()";

  if ($resolved !== '') {
    global $db;
    $p_like = $db->escape($resolved);
    $where = "$dateFilter AND (TRIM(Sample_ID) LIKE '{$p_like}-%' OR TRIM(Sample_Number) LIKE '{$p_like}-%')";
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
      'auto_year'   => $auto_year ? 1 : 0,
      'resolved'    => $resolved,
      'yy'          => $yy
    ],
    'today_count' => is_array($rows) ? count($rows) : 0,
    'today'       => $rows
  ]);
}

// Si llega aquí, acción no válida
json_error(400, 'Acción no válida. Usa action=next o action=today');
