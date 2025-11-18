<?php
// file: /api/kanban_list.php
declare(strict_types=1);

require_once('../config/load.php');

@ini_set('display_errors', '0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

/* ========= Helpers ========= */
function json_out(array $payload): void {
  echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function norm_upper(string $v): string {
  return strtoupper(trim($v));
}

/* ========= Parámetros ========= */
$q    = isset($_GET['q'])    ? trim((string)$_GET['q'])    : '';
$test = isset($_GET['test']) ? trim((string)$_GET['test']) : '';

$ALLOWED_STATUS = ['Registrado','Preparación','Realización','Entrega'];

$SLA = [
  'Registrado'  => 24,
  'Preparación' => 48,
  'Realización' => 72,
  'Entrega'     => 24,
];

/* ========= WHERE dinámico ========= */
$where = [];
$where[] = "Status IN ('Registrado','Preparación','Realización','Entrega')";

if ($q !== '') {
  $like = '%' . $GLOBALS['db']->escape($q) . '%';
  $where[] = "(Sample_ID LIKE '{$like}'
           OR  Sample_Number LIKE '{$like}'
           OR  Test_Type LIKE '{$like}')";
}

if ($test !== '') {
  $tt = norm_upper($test);
  $where[] = "UPPER(TRIM(Test_Type)) = '" . $GLOBALS['db']->escape($tt) . "'";
}

/*
 * Regla especial:
 *  - Las muestras en ESTADO = 'Entrega'
 *    solo se muestran si Process_Started es de la última semana.
 */
$whereEntrega = "(
    Status <> 'Entrega'
    OR (Status = 'Entrega'
        AND Process_Started >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    )
)";
$where[] = $whereEntrega;

$sql = "
  SELECT
    id,
    Sample_ID,
    Sample_Number,
    Test_Type,
    Status,
    Process_Started,
    Updated_By,
    sub_stage,
    TIMESTAMPDIFF(HOUR, Process_Started, NOW()) AS Dwell_Hours
  FROM test_workflow
  WHERE " . implode(' AND ', $where) . "
  ORDER BY Process_Started DESC
  LIMIT 800
";

try {
  $rows = find_by_sql($sql);
} catch (Throwable $e) {
  json_out([
    'ok'    => false,
    'error' => 'DB_ERROR: ' . $e->getMessage(),
  ]);
}

/* ========= Armar estructura por columnas ========= */

$data = [];
foreach ($ALLOWED_STATUS as $st) {
  $data[$st] = [];
}

foreach ($rows as $r) {
  $status = (string)($r['Status'] ?? '');
  if (!in_array($status, $ALLOWED_STATUS, true)) continue;

  $slaH   = (int)($SLA[$status] ?? 48);
  $dwell  = (int)($r['Dwell_Hours'] ?? 0);

  $item = [
    'id'            => (string)($r['id'] ?? ''),
    'Sample_ID'     => (string)($r['Sample_ID'] ?? ''),
    'Sample_Number' => (string)($r['Sample_Number'] ?? ''),
    'Test_Type'     => (string)($r['Test_Type'] ?? ''),
    'Status'        => $status,
    'Since'         => (string)($r['Process_Started'] ?? ''),
    'Updated_By'    => (string)($r['Updated_By'] ?? ''),
    'sub_stage'     => (string)($r['sub_stage'] ?? ''),
    'Dwell_Hours'   => $dwell,
    'SLA_Hours'     => $slaH,
    'Alert'         => ($dwell >= $slaH),
  ];

  $data[$status][] = $item;
}

/* ========= Respuesta JSON limpia ========= */
json_out([
  'ok'   => true,
  'data' => $data,
]);
