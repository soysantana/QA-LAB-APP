<?php
// /api/kanban_list.php
declare(strict_types=1);
require_once('../config/load.php');

@ini_set('display_errors','0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

/* =========================
   Helpers JSON
   ========================= */
function json_error(int $code, string $msg): never {
  http_response_code($code);
  echo json_encode(
    ['ok' => false, 'error' => $msg],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
  );
  exit;
}

function json_ok(array $data): never {
  echo json_encode(
    ['ok' => true] + $data,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
  );
  exit;
}

function N(string $v): string {
  return strtoupper(trim($v));
}

/* =========================
   Parámetros de filtro
   ========================= */
$q    = isset($_GET['q'])    ? trim((string)$_GET['q'])    : '';
$test = isset($_GET['test']) ? trim((string)$_GET['test']) : '';

global $db;

$where = [];

// Solo estados que manejas en el tablero
$where[] = "w.Status IN ('Registrado','Preparación','Realización','Entrega')";

// Filtro de búsqueda (Sample_ID, Sample_Number, Test_Type)
if ($q !== '') {
  $qEsc = '%' . $db->escape($q) . '%';
  $where[] = sprintf(
    "(w.Sample_ID LIKE '%s' OR w.Sample_Number LIKE '%s' OR w.Test_Type LIKE '%s')",
    $qEsc, $qEsc, $qEsc
  );
}

// Filtro por tipo de ensayo
if ($test !== '') {
  $tEsc = N($db->escape($test));
  $where[] = "UPPER(TRIM(w.Test_Type)) = '{$tEsc}'";
}

/*
 * Regla ESPECIAL para 'Entrega':
 * - Si Status = 'Entrega' => solo mostrar si Process_Started >= hoy - 7 días
 * - Los demás estados NO tienen restricción de fecha aquí
 *
 * Si prefieres usar Updated_At, cambia Process_Started por Updated_At.
 */
$where[] = "(
  w.Status <> 'Entrega'
  OR (w.Status = 'Entrega' AND w.Process_Started >= DATE_SUB(CURDATE(), INTERVAL 7 DAY))
)";

$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* =========================
   SLA por estado
   ========================= */
$SLA = [
  'Registrado'  => 24,
  'Preparación' => 48,
  'Realización' => 72,
  'Entrega'     => 24,
];

function sla_for(string $status): int {
  global $SLA;
  return (int)($SLA[$status] ?? 48);
}

/* =========================
   Consulta principal
   ========================= */
/*
 * Asegúrate de que test_workflow tenga estas columnas:
 *   - id
 *   - Sample_ID
 *   - Sample_Number
 *   - Test_Type
 *   - Status
 *   - Sub_Stage   (si no existe, quita del SELECT)
 *   - Process_Started
 *   - Updated_By
 *   - Updated_At
 */
$sql = "
  SELECT
    w.id,
    w.Sample_ID,
    w.Sample_Number,
    w.Test_Type,
    w.Status,
    w.Sub_Stage,
    w.Process_Started,
    w.Updated_By,
    w.Updated_At,
    TIMESTAMPDIFF(HOUR, w.Process_Started, NOW()) AS Dwell_Hours
  FROM test_workflow AS w
  {$sqlWhere}
  ORDER BY w.Updated_At DESC
  LIMIT 600
";

$rows = find_by_sql($sql);
if ($rows === null) {
  json_error(500, 'Error al consultar test_workflow');
}

/* =========================
   Armar respuesta por columna
   ========================= */
$data = [
  'Registrado'  => [],
  'Preparación' => [],
  'Realización' => [],
  'Entrega'     => [],
];

foreach ($rows as $r) {
  $status = (string)($r['Status'] ?? '');
  if (!isset($data[$status])) {
    // Si aparece otro estado raro, lo ignoramos para el tablero
    continue;
  }

  $sla    = sla_for($status);
  $dhrs   = (int)($r['Dwell_Hours'] ?? 0);
  $alert  = $dhrs >= $sla;

  $item = [
    'id'            => (string)$r['id'],
    'Sample_ID'     => (string)($r['Sample_ID'] ?? ''),
    'Sample_Number' => (string)($r['Sample_Number'] ?? ''),
    'Test_Type'     => (string)($r['Test_Type'] ?? ''),
    'Status'        => $status,
    'Sub_Stage'     => (string)($r['Sub_Stage'] ?? ''),
    'Since'         => (string)($r['Process_Started'] ?? ''),
    'Updated_By'    => (string)($r['Updated_By'] ?? ''),
    'Dwell_Hours'   => $dhrs,
    'SLA_Hours'     => $sla,
    'Alert'         => $alert,
  ];

  $data[$status][] = $item;
}

json_ok(['data' => $data]);
