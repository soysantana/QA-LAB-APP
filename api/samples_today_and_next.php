<?php
declare(strict_types=1);
require_once('../config/load.php');

date_default_timezone_set('America/Santo_Domingo');

function json_ok(array $data){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok'=>true]+$data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function json_error(int $code, string $msg){ http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }

// --------- Parámetros ---------
$prefix_raw = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
$prefix_raw = preg_replace('/[^A-Za-z0-9\-_]/', '', $prefix_raw);

// Si tu columna Registed_Date es DATETIME, pon true:
$REGISTERED_IS_DATETIME = false; // true si guardas fecha+hora (misma zona)

$now = new DateTimeImmutable('now');
$yy  = $now->format('y');          // "25"
$yyyy= $now->format('Y');          // "2025"
$month = (int)$now->format('n');   // 1..12

// Detecta si el prefijo ya trae año al final (2 dígitos)
$matches = [];
$hasYearSuffix = (bool)preg_match('/\d{2}$/', $prefix_raw, $matches);

// Si NO trae año, se asume el año actual (auto-anexo)
$prefix_current_year = $hasYearSuffix ? $prefix_raw : ($prefix_raw !== '' ? $prefix_raw.$yy : '');

// También preparamos el año anterior para tolerancia en enero (opcional)
$yy_prev = $now->modify('-1 year')->format('y');
$prefix_prev_year = ($prefix_raw !== '') ? ( $hasYearSuffix ? preg_replace('/\d{2}$/', $yy_prev, $prefix_raw) : $prefix_raw.$yy_prev ) : '';

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
$todayRows = find_by_sql($sqlToday);
$todayCount = is_array($todayRows) ? count($todayRows) : 0;

// --------- Siguiente número por prefijo dinámico ---------
global $db;

function max_suffix_for_prefix(string $prefix_like): int {
  // Busca el mayor número al final del Sample_ID (último segmento tras "-")
  $sql = sprintf(
    "SELECT MAX(CAST(SUBSTRING_INDEX(Sample_ID,'-',-1) AS UNSIGNED)) AS max_n
     FROM lab_test_requisition_form
     WHERE Sample_ID LIKE '%s-%%'",
     $GLOBALS['db']->escape($prefix_like)
  );
  $row = find_by_sql($sql);
  return isset($row[0]['max_n']) ? (int)$row[0]['max_n'] : 0;
}

$nextInfo = null;
if ($prefix_raw !== '') {
  // Paso 1: intenta con año actual (prefijo con yy al final)
  $maxN_current = max_suffix_for_prefix($prefix_current_year);

  // Paso 2: (opcional) si estamos en ENERO y no hay historial con año actual,
  //         mira el año anterior para informar contexto
  $maxN_prev = 0;
  if ($month === 1 && $maxN_current === 0) {
    $maxN_prev = max_suffix_for_prefix($prefix_prev_year);
  }

  // Siguiente número = el mayor encontrado del año actual + 1
  $nextN = $maxN_current + 1;
  $nextPadded = str_pad((string)$nextN, 4, '0', STR_PAD_LEFT);

  $nextInfo = [
    'base_prefix'     => $prefix_raw,             // lo que envió el cliente (con o sin año)
    'resolved_prefix' => $prefix_current_year,    // prefijo que realmente se usa para este año
    'year'            => $yy,
    'max_found'       => $maxN_current,
    'next_number'     => $nextN,
    'next_id'         => $prefix_current_year . '-' . $nextPadded,
  ];

  if ($month === 1 && $maxN_prev > 0) {
    // información adicional útil al cambiar de año
    $nextInfo['prev_year_context'] = [
      'prev_year'      => $yy_prev,
      'prev_prefix'    => $prefix_prev_year,
      'prev_max_found' => $maxN_prev,
      'note'           => 'Transición de año: se inicia conteo nuevo para el año actual.'
    ];
  }
}

// --------- Salida ---------
json_ok([
  'today_count' => $todayCount,
  'today'       => $todayRows,
  'next'        => $nextInfo,
]);
