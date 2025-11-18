<?php
// /api/kanban_list.php
declare(strict_types=1);
require_once('../config/load.php');

// Evitar que errores se mezclen con el JSON
@ini_set('display_errors', '0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

/**
 * SLA por estado (en horas).
 */
$SLA = [
  'Registrado'  => 24,
  'Preparación' => 48,
  'Realización' => 72,
  'Entrega'     => 24,
  'Revisado'    => 24,
];

function slaHours(string $status, string $testType, array $SLA): int {
  if (isset($SLA[$testType]) && is_array($SLA[$testType]) && isset($SLA[$testType][$status])) {
    return (int)$SLA[$testType][$status];
  }
  return (int)($SLA[$status] ?? 48);
}

/**
 * Verifica si una columna existe en una tabla
 */
function col_exists(mysqli $db, string $table, string $col): bool {
  $t = $db->escape_string($table);
  $c = $db->escape_string($col);
  $sql = "SHOW COLUMNS FROM `{$t}` LIKE '{$c}'";
  $res = $db->query($sql);
  return $res && $res->num_rows > 0;
}

try {
  global $db;

  $q    = trim($_GET['q'] ?? '');
  $test = trim($_GET['test'] ?? '');

  $where = [];

  // Solo los estados que usas en el tablero
  $where[] = "Status IN ('Registrado','Preparación','Realización','Entrega')";

  // Regla especial para Entrega -> solo últimos 7 días
  $where[] = "(
      Status <> 'Entrega'
      OR (Status = 'Entrega'
          AND Process_Started >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
      )
    )";

  if ($q !== '') {
    $qEsc = $db->escape_string($q);
    $where[] = "(Sample_ID LIKE '%{$qEsc}%'
                 OR Sample_Number LIKE '%{$qEsc}%'
                 OR Test_Type LIKE '%{$qEsc}%')";
  }

  if ($test !== '') {
    $testEsc = $db->escape_string($test);
    $where[] = "UPPER(TRIM(Test_Type)) = UPPER('{$testEsc}')";
  }

  $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

  // Soportar que la columna sub_stage no exista
  $hasSubStage = col_exists($db, 'test_workflow', 'sub_stage');
  $subSelect   = $hasSubStage ? 'sub_stage' : "NULL AS sub_stage";

  $sql = "
    SELECT
      id,
      Sample_ID,
      Sample_Number,
      UPPER(TRIM(Test_Type)) AS Test_Type,
      TRIM(Status) AS Status,
      {$subSelect},
      Process_Started,
      Updated_At,
      Updated_By
    FROM test_workflow
    {$whereSql}
    ORDER BY FIELD(Status,'Registrado','Preparación','Realización','Entrega'),
             Updated_At DESC
  ";

  $rs = $db->query($sql);
  if (!$rs) {
    throw new Exception('DB error: ' . $db->error);
  }

  // Estructura que espera tu JS
  $by = [
    'Registrado'  => [],
    'Preparación' => [],
    'Realización' => [],
    'Entrega'     => [],
  ];

  $now = new DateTime('now');

  while ($r = $db->fetch_assoc($rs)) {
    $status = $r['Status'];

    // por seguridad, si viene otro estado, lo mando a Registrado
    if (!isset($by[$status])) {
      $status = 'Registrado';
    }

    $started = $r['Process_Started']
      ? new DateTime($r['Process_Started'])
      : clone $now;

    $diffH  = max(0, (int)floor(($now->getTimestamp() - $started->getTimestamp()) / 3600));
    $limitH = slaHours($status, $r['Test_Type'], $SLA);
    $alert  = $diffH >= $limitH;

    $by[$status][] = [
      'id'            => $r['id'],
      'Sample_ID'     => $r['Sample_ID'],
      'Sample_Number' => $r['Sample_Number'],
      'Test_Type'     => $r['Test_Type'],
      'Status'        => $status,
      'Sub_Stage'     => $r['sub_stage'],      // alias o NULL
      'Since'         => $r['Process_Started'],
      'Updated_By'    => $r['Updated_By'],
      'Dwell_Hours'   => $diffH,
      'SLA_Hours'     => $limitH,
      'Alert'         => $alert,
    ];
  }

  echo json_encode(['ok' => true, 'data' => $by], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok'    => false,
    'error' => $e->getMessage(),
  ], JSON_UNESCAPED_UNICODE);
}
