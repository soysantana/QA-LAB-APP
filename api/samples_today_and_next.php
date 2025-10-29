<?php
declare(strict_types=1);
require_once('../config/load.php');
date_default_timezone_set('America/Santo_Domingo');

// CAMBIA a true si Registed_Date es DATETIME:
$REGISTERED_IS_DATETIME = false;

function json_ok(array $data){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>true] + $data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function json_error(int $code, string $msg, array $extra = []){
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false, 'error'=>$msg] + $extra, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

// Seguridad (si aplica en tu app)
if (function_exists('page_require_level')) page_require_level(2);
if (function_exists('current_user') && !current_user()) json_error(401, 'No autenticado');

ini_set('display_errors','0');
header('Cache-Control: no-store');

// ---------- Reglas de prefijo ----------
$NO_YEAR          = ['LLD-258','SD3-258','SD2-258','SD1-258'];
$WITH_YEAR_PEGADO = ['PVDJ-AGG','PVDJ-AGG-INV','PVDJ-AGG-DIO','LBOR','PVDJ-MISC'];

function mysql_regex_quote(string $s): string {
  return preg_replace('/([\\\\.^$|()?*+\\[\\]{}-])/', '\\\\$1', $s);
}
function resolve_prefix_by_rules(string $base, array $NO_YEAR, array $WITH_YEAR_PEGADO, string $yy): string {
  if (preg_match('/-\d{2}$/', $base)) return $base;                 // ya trae -yy
  if (in_array($base, $NO_YEAR, true)) return $base;                // sin año
  if (in_array($base, $WITH_YEAR_PEGADO, true)) return $base.$yy;   // año pegado
  return $base;
}
function first_row(string $sql): ?array {
  $rows = find_by_sql($sql);
  return (is_array($rows) && isset($rows[0])) ? $rows[0] : null;
}

function get_last_and_next_for_resolved(string $resolved, int $pad_len): array {
  global $db;

  $like = $db->escape($resolved);
  $rx   = '^' . mysql_regex_quote($resolved) . '-[0-9]+$';
  $rx_e = $db->escape($rx);

  $sql_last_id = "
    SELECT TRIM(Sample_ID) AS val,
           CAST(SUBSTRING_INDEX(TRIM(Sample_ID), '-', -1) AS UNSIGNED) AS n
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID) LIKE '{$like}-%'
      AND TRIM(Sample_ID) REGEXP '{$rx_e}'
    ORDER BY n DESC
    LIMIT 1
  ";
  $sql_last_num = "
    SELECT TRIM(Sample_Number) AS val,
           CAST(SUBSTRING_INDEX(TRIM(Sample_Number), '-', -1) AS UNSIGNED) AS n
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_Number) LIKE '{$like}-%'
      AND TRIM(Sample_Number) REGEXP '{$rx_e}'
    ORDER BY n DESC
    LIMIT 1
  ";

  $row_id  = first_row($sql_last_id);
  $row_num = first_row($sql_last_num);

  $best_val = null; $best_n = 0; $from_col = null;
  if ($row_id && (int)$row_id['n'] > $best_n) { $best_n=(int)$row_id['n']; $best_val=(string)$row_id['val']; $from_col='Sample_ID'; }
  if ($row_num && (int)$row_num['n'] > $best_n){ $best_n=(int)$row_num['n']; $best_val=(string)$row_num['val']; $from_col='Sample_Number'; }

  $next_n   = ($best_n > 0) ? $best_n + 1 : 1;
  $next_pad = str_pad((string)$next_n, $pad_len, '0', STR_PAD_LEFT);
  $use_this = $resolved . '-' . $next_pad;

  return [
    'last_found' => [
      'column'      => $from_col,     // "Sample_ID" | "Sample_Number" | null
      'from_column' => $from_col,     // alias compatibilidad
      'value'       => $best_val,     // ej. "SD3-258-0003"
      'suffix'      => $best_n,       // ej. 3
      'suffix_n'    => $best_n        // alias
    ],
    'next' => [
      'next_suffix' => $next_n,       // ej. 4
      'next_padded' => $next_pad,     // ej. "0004"
      'use_code'    => $use_this      // ej. "SD3-258-0004"
    ],
    'suggestion' => [                 // bloque adicional para UIs existentes
      'next_n'      => $next_n,
      'next_padded' => $next_pad,
      'use_this'    => $use_this
    ]
  ];
}

$action = isset($_GET['action']) ? trim((string)$_GET['action']) : 'next';
$yy = (new DateTimeImmutable('now'))->format('y');

// === A) Siguiente por un prefijo ===
if ($action === 'next') {
  $base = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
  $base = preg_replace('/[^A-Za-z0-9\-_]/', '', $base);
  if ($base === '') json_error(400, 'Falta ?prefix=');

  $pad_len = isset($_GET['pad']) ? max(1,(int)$_GET['pad']) : 4;
  $resolved = resolve_prefix_by_rules($base, $NO_YEAR, $WITH_YEAR_PEGADO, $yy);

  $out = get_last_and_next_for_resolved($resolved, $pad_len);

  json_ok([
    'params' => [
      'prefix_base'     => $base,
      'prefix_resolved' => $resolved,
      'resolved'        => $resolved,
      'yy'              => $yy,
      'pad_len'         => $pad_len
    ]
  ] + $out);
}

// === B) Hoy ===
if ($action === 'today') {
  $base = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
  $base = preg_replace('/[^A-Za-z0-9\-_]/', '', $base);

  $resolved = '';
  if ($base !== '') $resolved = resolve_prefix_by_rules($base, $NO_YEAR, $WITH_YEAR_PEGADO, $yy);

  $dateFilter = $REGISTERED_IS_DATETIME
    ? "Registed_Date >= CURDATE() AND Registed_Date < CURDATE() + INTERVAL 1 DAY"
    : "Registed_Date = CURDATE()";

  if ($resolved !== '') {
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
    ORDER BY ".($REGISTERED_IS_DATETIME ? "Registed_Date DESC, id DESC" : "id DESC")."
  ";
  $rows = find_by_sql($sql);

  json_ok([
    'params' => [
      'prefix_base'     => $base,
      'prefix_resolved' => $resolved,
      'resolved'        => $resolved,
      'yy'              => $yy
    ],
    'today_count' => is_array($rows) ? count($rows) : 0,
    'today' => $rows
  ]);
}

// === C) Semana (últimos 7 días) ===
if ($action === 'week') {
  $base = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
  $base = preg_replace('/[^A-Za-z0-9\-_]/', '', $base);

  $resolved = '';
  if ($base !== '') $resolved = resolve_prefix_by_rules($base, $NO_YEAR, $WITH_YEAR_PEGADO, $yy);

  if ($REGISTERED_IS_DATETIME) {
    $dateFilter = "Registed_Date >= (CURDATE() - INTERVAL 6 DAY) AND Registed_Date < (CURDATE() + INTERVAL 1 DAY)";
  } else {
    $dateFilter = "Registed_Date BETWEEN (CURDATE() - INTERVAL 6 DAY) AND CURDATE()";
  }

  if ($resolved !== '') {
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
    ORDER BY ".($REGISTERED_IS_DATETIME ? "Registed_Date DESC, id DESC" : "Registed_Date DESC, id DESC")."
  ";
  $rows = find_by_sql($sql);

  json_ok([
    'params' => [
      'prefix_base'     => $base,
      'prefix_resolved' => $resolved,
      'resolved'        => $resolved,
      'yy'              => $yy
    ],
    'week_count' => is_array($rows) ? count($rows) : 0,
    'week' => $rows
  ]);
}

// === D) NUEVO: Último y Siguiente por CONJUNTO de prefijos ===
// Soporta GET (prefix[]=) y POST JSON { prefix:[], pad?:N }
if ($action === 'latest_by_prefixes') {
  $pad_len_global = 4;

  // Lee prefijos por GET
  $prefixes = [];
  if (isset($_GET['prefix'])) {
    $pfx = $_GET['prefix'];
    if (is_array($pfx)) $prefixes = $pfx;
    else $prefixes = [$pfx];
  }

  // Lee cuerpo JSON opcional
  $raw = file_get_contents('php://input');
  if ($raw) {
    $json = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
      if (isset($json['prefix'])) {
        if (is_array($json['prefix'])) $prefixes = $json['prefix'];
        elseif (is_string($json['prefix'])) $prefixes[] = $json['prefix'];
      }
      if (isset($json['pad']) && is_numeric($json['pad'])) {
        $pad_len_global = max(1, (int)$json['pad']);
      }
    }
  }

  // También permite ?pad= por GET
  if (isset($_GET['pad'])) $pad_len_global = max(1,(int)$_GET['pad']);

  // Normaliza y valida
  $prefixes = array_values(array_filter(array_map(function($s){
    $s = trim((string)$s);
    return preg_replace('/[^A-Za-z0-9\-_]/', '', $s);
  }, $prefixes), fn($v)=>$v!==''));

  if (empty($prefixes)) {
    json_error(400, 'Faltan prefijos. Usa prefix[]=... en GET o { "prefix":[...]} por JSON.');
  }

  $yy = (new DateTimeImmutable('now'))->format('y');
  $results = [];

  foreach ($prefixes as $base) {
    $resolved = resolve_prefix_by_rules($base, $NO_YEAR, $WITH_YEAR_PEGADO, $yy);
    $calc = get_last_and_next_for_resolved($resolved, $pad_len_global);

    $results[] = [
      'prefix_base'     => $base,
      'prefix_resolved' => $resolved,
      'resolved'        => $resolved,
      'yy'              => $yy,
      'pad_len'         => $pad_len_global,
      'last_found'      => $calc['last_found'],
      'next'            => $calc['next'],
      'suggestion'      => $calc['suggestion']
    ];
  }

  json_ok([
    'count'   => count($results),
    'results' => $results
  ]);
}

json_error(400, 'Acción no válida. Usa action=next, action=today, action=week o action=latest_by_prefixes');
