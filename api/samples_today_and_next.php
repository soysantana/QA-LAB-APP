<?php
declare(strict_types=1);
require_once('../config/load.php');
date_default_timezone_set('America/Santo_Domingo');

/**
 * CONFIG: Cambia a true si Registed_Date es DATETIME (no DATE)
 */
$REGISTERED_IS_DATETIME = false;

/* ---------- Helpers JSON ---------- */
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

/* ---------- Seguridad (si tu app usa login/roles) ---------- */
if (function_exists('page_require_level')) page_require_level(2);
if (function_exists('current_user') && !current_user()) json_error(401, 'No autenticado');

ini_set('display_errors','0');
header('Cache-Control: no-store');

/* ---------- Reglas de prefijo ----------

  NO_YEAR:   no se agrega año.
  WITH_YEAR_PEGADO: se agrega el año "pegado" (yy), ej: PVDJ-AGG + 25 => PVDJ-AGG25
--------------------------------------------------------------- */
$NO_YEAR          = ['LLD-258','SD3-258','SD2-258','SD1-258'];
$WITH_YEAR_PEGADO = ['PVDJ-AGG','PVDJ-AGG-INV','PVDJ-AGG-DIO','LBOR','PVDJ-MISC'];

/* ---------- Utilidades SQL / Prefijos ---------- */
function mysql_regex_quote(string $s): string {
  return preg_replace('/([\\\\.^$|()?*+\\[\\]{}-])/', '\\\\$1', $s);
}
function resolve_prefix_by_rules(string $base, array $NO_YEAR, array $WITH_YEAR_PEGADO, string $yy): string {
  // Si ya termina en -yy, respetar (ej: SD3-258-25)
  if (preg_match('/-\d{2}$/', $base)) return $base;
  if (in_array($base, $NO_YEAR, true)) return $base;
  if (in_array($base, $WITH_YEAR_PEGADO, true)) return $base.$yy;
  return $base;
}
function first_row(string $sql): ?array {
  $rows = find_by_sql($sql);
  return (is_array($rows) && isset($rows[0])) ? $rows[0] : null;
}

/**
 * Núcleo: obtiene "último" y "siguiente" para un prefijo YA RESUELTO (con/ sin año).
 * Soporta:
 *   A) Hífen en la misma columna:   {resolved}-{####}   en Sample_ID o Sample_Number
 *   B) Modo separado:               Sample_ID = {resolved}  y Sample_Number = {####} (numérico)
 * Respeta el padding detectado (p.ej. 0480 -> 0481).
 */
function get_last_and_next_for_resolved(string $resolved, int $pad_len): array {
  global $db;

  // --- Formato con guion en la MISMA columna: {resolved}-{####}
  $like = $db->escape($resolved);
  $rx   = '^' . mysql_regex_quote($resolved) . '-[0-9]+$';
  $rx_e = $db->escape($rx);

  $sql_last_id_hyphen = "
    SELECT TRIM(Sample_ID) AS val,
           CAST(SUBSTRING_INDEX(TRIM(Sample_ID), '-', -1) AS UNSIGNED) AS n,
           CHAR_LENGTH(SUBSTRING_INDEX(TRIM(Sample_ID), '-', -1)) AS nlen,
           'Sample_ID' AS src
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID) LIKE '{$like}-%'
      AND TRIM(Sample_ID) REGEXP '{$rx_e}'
    ORDER BY n DESC
    LIMIT 1
  ";
  $sql_last_num_hyphen = "
    SELECT TRIM(Sample_Number) AS val,
           CAST(SUBSTRING_INDEX(TRIM(Sample_Number), '-', -1) AS UNSIGNED) AS n,
           CHAR_LENGTH(SUBSTRING_INDEX(TRIM(Sample_Number), '-', -1)) AS nlen,
           'Sample_Number' AS src
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_Number) LIKE '{$like}-%'
      AND TRIM(Sample_Number) REGEXP '{$rx_e}'
    ORDER BY n DESC
    LIMIT 1
  ";

  $row_id_h  = first_row($sql_last_id_hyphen);
  $row_num_h = first_row($sql_last_num_hyphen);

  // --- Formato SEPARADO: Sample_ID = {resolved}  y  Sample_Number = {####}
  $esc_resolved = $db->escape($resolved);
  $sql_split = "
    SELECT TRIM(Sample_ID) AS id,
           TRIM(Sample_Number) AS num,
           CAST(TRIM(Sample_Number) AS UNSIGNED) AS n,
           CHAR_LENGTH(TRIM(Sample_Number)) AS nlen,
           'SPLIT' AS src
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID) = '{$esc_resolved}'
      AND TRIM(Sample_Number) REGEXP '^[0-9]+$'
    ORDER BY n DESC
    LIMIT 1
  ";
  $row_split = first_row($sql_split);

  // Escoge el mejor (máximo n) entre los tres modos
  $candidatos = [];
  if ($row_id_h)   $candidatos[] = ['val'=>$row_id_h['val'],  'n'=>(int)$row_id_h['n'],  'nlen'=>(int)$row_id_h['nlen'],  'src'=>$row_id_h['src']];
  if ($row_num_h)  $candidatos[] = ['val'=>$row_num_h['val'], 'n'=>(int)$row_num_h['n'], 'nlen'=>(int)$row_num_h['nlen'], 'src'=>$row_num_h['src']];
  if ($row_split)  $candidatos[] = ['val'=>$row_split['num'], 'n'=>(int)$row_split['n'], 'nlen'=>(int)$row_split['nlen'],'src'=>$row_split['src']];

  $best = null;
  foreach ($candidatos as $c) {
    if (!$best || $c['n'] > $best['n']) $best = $c;
  }

  $from_col = null;
  $best_val = null;
  $best_n   = 0;
  $pad_detected = $pad_len;

  if ($best) {
    $best_val = (string)$best['val'];
    $best_n   = (int)$best['n'];
    $pad_detected = max($pad_len, (int)$best['nlen']);
    // Mapear origen
    if ($best['src'] === 'SPLIT') {
      // modo separado: el último n provino de Sample_Number con Sample_ID = resolved
      $from_col = 'Sample_Number (split with Sample_ID=' . $resolved . ')';
    } else {
      $from_col = $best['src']; // 'Sample_ID' o 'Sample_Number'
    }
  }

  // siguiente
  $next_n   = ($best_n > 0) ? $best_n + 1 : 1;
  $next_pad = str_pad((string)$next_n, $pad_detected, '0', STR_PAD_LEFT);

  // Para consistencia en UI, el "usar" se entrega en formato con guion
  $use_this = $resolved . '-' . $next_pad;

  return [
    'last_found' => [
      'column'      => $from_col,     // "Sample_ID" | "Sample_Number" | "Sample_Number (split ...)" | null
      'from_column' => $from_col,     // alias compatibilidad
      'value'       => $best_val,     // ej. "PVDJ-AGG25-0480" o "0480"
      'suffix'      => $best_n,       // ej. 480
      'suffix_n'    => $best_n,       // alias
      'pad_len'     => $pad_detected  // padding detectado
    ],
    'next' => [
      'next_suffix' => $next_n,       // ej. 481
      'next_padded' => $next_pad,     // ej. "0481"
      'use_code'    => $use_this      // ej. "PVDJ-AGG25-0481"
    ],
    'suggestion' => [                 // bloque adicional para UIs existentes
      'next_n'      => $next_n,
      'next_padded' => $next_pad,
      'use_this'    => $use_this
    ]
  ];
}

/* ---------- Router de acciones ---------- */
$action = isset($_GET['action']) ? trim((string)$_GET['action']) : 'next';
$yy = (new DateTimeImmutable('now'))->format('y');

/* === A) Siguiente por un prefijo ===
   GET params:
     - prefix (string requerido)
     - pad (int opcional, default 4) */
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

/* === B) Registradas HOY ===
   GET params:
     - prefix (string opcional: base, se resuelve con reglas) */
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
    $where = "$dateFilter AND (TRIM(Sample_ID) LIKE '{$like}-%' OR TRIM(Sample_Number) LIKE '{$like}-%' OR TRIM(Sample_ID) = '{$like}')";
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

/* === C) Registradas SEMANA (últimos 7 días) ===
   GET params:
     - prefix (string opcional: base, se resuelve con reglas) */
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
    $where = "$dateFilter AND (TRIM(Sample_ID) LIKE '{$like}-%' OR TRIM(Sample_Number) LIKE '{$like}-%' OR TRIM(Sample_ID) = '{$like}')";
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

/* === D) NUEVO: Último y Siguiente por CONJUNTO de prefijos ===
   Soporta:
     - GET:  ?action=latest_by_prefixes&prefix[]=PVDJ-AGG&prefix[]=LLD-258&pad=4
     - POST: JSON {"prefix":["PVDJ-AGG","LLD-258"], "pad":4}
*/
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

/* --- Si ninguna acción coincidió --- */
json_error(400, 'Acción no válida. Usa action=next, action=today, action=week o action=latest_by_prefixes');
