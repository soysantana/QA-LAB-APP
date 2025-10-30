<?php
declare(strict_types=1);

date_default_timezone_set('America/Santo_Domingo');

/* =========================
   RESPUESTAS JSON
 ========================= */
function json_ok(array $data, int $code = 200): void {
  if (!headers_sent()) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  }
  echo json_encode(['ok'=>true] + $data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function json_error(int $code, string $msg, array $extra = []): void {
  if (!headers_sent()) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  }
  echo json_encode(['ok'=>false, 'error'=>$msg] + $extra, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

/* =========================
   BOOT SILENCIOSO
 ========================= */
ob_start();
require_once __DIR__ . '/../config/load.php';
ob_end_clean();

/* =========================
   CONFIG
 ========================= */
const REGISTERED_IS_DATETIME = false;         // TRUE si tu campo es DATETIME
const REGISTERED_COL = 'Registed_Date';       // Ajusta si tu columna se llama distinto

const NO_YEAR = ['LLD-258','SD3-258','SD2-258','SD1-258'];
const WITH_YEAR_PEGADO = ['PVDJ-AGG','PVDJ-AGG-INV','PVDJ-AGG-DIO','LBOR','PVDJ-MISC'];

/* =========================
   AUTH
 ========================= */


/* =========================
   UTILS
 ========================= */
function mysql_regex_quote(string $s): string {
  return preg_replace('/([\\\\.^$|()?*+\\[\\]{}-])/', '\\\\$1', $s);
}
function resolve_prefix_by_rules(string $base, array $noYear, array $withYearPegado, string $yy): string {
  if (preg_match('/-\d{2}$/', $base)) return $base;             // ya termina -yy
  if (in_array($base, $noYear, true)) return $base;             // sin año
  if (in_array($base, $withYearPegado, true)) return $base.$yy; // año pegado
  return $base;
}
function first_row(string $sql): ?array {
  $rows = find_by_sql($sql);
  return (is_array($rows) && isset($rows[0])) ? $rows[0] : null;
}
function where_for_resolved(string $resolved, $db): string {
  $esc = $db->escape($resolved);
  return "(TRIM(Sample_ID) LIKE '{$esc}-%' OR (TRIM(Sample_ID)='{$esc}' AND TRIM(Sample_Number) REGEXP '^[0-9]+$'))";
}

/** último y siguiente para un prefijo YA RESUELTO (con/sin año pegado) */
function get_last_and_next_for_resolved(string $resolved, int $pad_len): array {
  global $db;
  $like = $db->escape($resolved);
  $rx_e = $db->escape('^' . mysql_regex_quote($resolved) . '-[0-9]+$');

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

  $esc_resolved = $db->escape($resolved);
  $sql_split = "
    SELECT TRIM(Sample_ID) AS id,
           TRIM(Sample_Number) AS num,
           CAST(TRIM(Sample_Number) AS UNSIGNED) AS n,
           CHAR_LENGTH(TRIM(Sample_Number)) AS nlen,
           'SPLIT' AS src
    FROM lab_test_requisition_form
    WHERE TRIM(Sample_ID)='{$esc_resolved}'
      AND TRIM(Sample_Number) REGEXP '^[0-9]+$'
    ORDER BY n DESC
    LIMIT 1
  ";

  $row_id_h  = first_row($sql_last_id_hyphen);
  $row_num_h = first_row($sql_last_num_hyphen);
  $row_split = first_row($sql_split);

  $best = null;
  foreach ([$row_id_h, $row_num_h, $row_split] as $r) {
    if ($r && ($best === null || (int)$r['n'] > (int)$best['n'])) $best = $r;
  }

  $best_val = $best['val'] ?? $best['num'] ?? '';
  $best_n = (int)($best['n'] ?? 0);
  $pad_detected = isset($best['nlen']) ? max($pad_len, (int)$best['nlen']) : $pad_len;
  if ($best_val && preg_match('/(\d+)$/', $best_val, $m)) $pad_detected = max($pad_len, strlen($m[1]));

  $next_n = ($best_n > 0 ? $best_n + 1 : 1);
  $next_pad = str_pad((string)$next_n, $pad_detected, '0', STR_PAD_LEFT);
  return [
    'last_found' => ['value'=>$best_val, 'suffix'=>$best_n, 'pad_len'=>$pad_detected],
    'next' => ['use_code'=>$resolved.'-'.$next_pad, 'next_suffix'=>$next_n, 'next_padded'=>$next_pad]
  ];
}

/* =========================
   REQUEST PARSING (GET/POST)
 ========================= */
$action = $_GET['action'] ?? 'next';
$rawBody = file_get_contents('php://input');
$body = $rawBody ? json_decode($rawBody, true) : [];

/** Lee un parámetro ya sea de GET o del body JSON */
function in_req(string $key, array $body) {
  if (isset($_GET[$key])) return $_GET[$key];
  if (array_key_exists($key, $body)) return $body[$key];
  return null;
}

/** Alias para base: acepta base | prefix | prefijo */
function read_base(array $body): string {
  $v = in_req('base', $body);
  if ($v === null || $v === '') $v = in_req('prefix', $body);
  if ($v === null || $v === '') $v = in_req('prefijo', $body);
  return trim((string)($v ?? ''));
}

/* =========================
   CONTEXTO
 ========================= */
$yy = date('y');
$colReg = REGISTERED_COL;
$dateExpr = REGISTERED_IS_DATETIME ? "DATE($colReg)" : $colReg;
global $db;
$pad_default = (int)(in_req('pad', $body) ?? 4);

/* =========================
   DISPATCHER
 ========================= */
try {
  switch ($action) {
    case 'next': {
      $base = read_base($body);
      if ($base === '') json_error(400, "Falta parámetro 'base' (también acepto 'prefix' o 'prefijo').");
      $resolved = resolve_prefix_by_rules($base, NO_YEAR, WITH_YEAR_PEGADO, $yy);
      $info = get_last_and_next_for_resolved($resolved, max(1,$pad_default));
      json_ok(['base'=>$base,'resolved'=>$resolved,'result'=>$info]);
      break;
    }

    case 'today': {
      $base = read_base($body);
      $resolved = $base ? resolve_prefix_by_rules($base, NO_YEAR, WITH_YEAR_PEGADO, $yy) : '';
      $wherePrefix = $base ? where_for_resolved($resolved, $db) : '1=1';
      $sql = "
        SELECT TRIM(Sample_ID) AS Sample_ID, TRIM(Sample_Number) AS Sample_Number,
               TRIM(Test_Type) AS Test_Type, TRIM(Material_Type) AS Material_Type,
               $colReg AS registered_at
        FROM lab_test_requisition_form
        WHERE $dateExpr = CURDATE()
          AND $wherePrefix
        ORDER BY $colReg DESC
        LIMIT 500
      ";
      $rows = find_by_sql($sql);
      json_ok(['base'=>$base,'resolved'=>$resolved,'count'=>count($rows ?? []),'items'=>$rows ?? []]);
      break;
    }

    case 'week': {
      $base = read_base($body);
      $resolved = $base ? resolve_prefix_by_rules($base, NO_YEAR, WITH_YEAR_PEGADO, $yy) : '';
      $wherePrefix = $base ? where_for_resolved($resolved, $db) : '1=1';
      $sql = "
        SELECT TRIM(Sample_ID) AS Sample_ID, TRIM(Sample_Number) AS Sample_Number,
               TRIM(Test_Type) AS Test_Type, TRIM(Material_Type) AS Material_Type,
               $colReg AS registered_at
        FROM lab_test_requisition_form
        WHERE $dateExpr BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
          AND $wherePrefix
        ORDER BY $colReg DESC
        LIMIT 1000
      ";
      $rows = find_by_sql($sql);
      json_ok(['base'=>$base,'resolved'=>$resolved,'count'=>count($rows ?? []),'items'=>$rows ?? []]);
      break;
    }

    case 'latest_by_prefixes': {
      // GET ?prefixes[]=... o POST {"prefixes":[...]} — también acepta "bases"
      $prefixes = [];
      $g = $_GET['prefixes'] ?? $_GET['bases'] ?? null;
      $b = $body['prefixes'] ?? $body['bases'] ?? null;
      if (is_array($g)) $prefixes = $g;
      elseif (is_array($b)) $prefixes = $b;

      // tolerar un solo prefix/base plano
      if (empty($prefixes)) {
        $one = read_base($body);
        if ($one !== '') $prefixes = [$one];
      }
      if (empty($prefixes)) json_error(400, "Falta 'prefixes'/'bases' (array) o 'base/prefix/prefijo'.");

      $out = [];
      foreach ($prefixes as $p) {
        $p = trim((string)$p);
        if ($p === '') continue;
        $res = resolve_prefix_by_rules($p, NO_YEAR, WITH_YEAR_PEGADO, $yy);
        $info = get_last_and_next_for_resolved($res, max(1,$pad_default));
        $out[] = ['base'=>$p,'resolved'=>$res,'result'=>$info];
      }
      json_ok(['count'=>count($out),'items'=>$out]);
      break;
    }

    default:
      json_error(400, "Acción no soportada.", ['action'=>$action]);
  }
} catch (Throwable $e) {
  json_error(500, 'Error interno', ['msg'=>$e->getMessage()]);
}
