<?php
// /api/kanban_list.php
declare(strict_types=1);
require_once('../config/load.php');
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

try {
  $q    = trim($_GET['q'] ?? '');
  $test = trim($_GET['test'] ?? '');

  $where = [];
  if ($q !== '') {
    $qEsc = $db->escape($q);
    $where[] = "(Sample_ID LIKE '%{$qEsc}%' OR Sample_Number LIKE '%{$qEsc}%' OR Test_Type LIKE '%{$qEsc}%')";
  }
  if ($test !== '') {
    $testEsc = $db->escape($test);
    $where[] = "UPPER(TRIM(Test_Type)) = UPPER('{$testEsc}')";
  }
  $whereSql = $where ? ('WHERE '.implode(' AND ', $where)) : '';

  $sql = "
    SELECT
      id,
      Sample_ID,
      Sample_Number,
      UPPER(TRIM(Test_Type)) AS Test_Type,
      TRIM(Status) AS Status,
      sub_stage,
      Process_Started,
      Updated_At,
      Updated_By
    FROM test_workflow
    {$whereSql}
    ORDER BY FIELD(Status,'Registrado','Preparación','Realización','Entrega','Revisado'),
             Updated_At DESC
  ";

  $rs = $db->query($sql);

  $by = [
    'Registrado'  => [],
    'Preparación' => [],
    'Realización' => [],
    'Entrega'     => [],
    'Revisado'    => [],
  ];

  $now = new DateTime('now');

  while ($r = $db->fetch_assoc($rs)) {
    $status = $r['Status'];
    if (!isset($by[$status])) {
      $status = 'Registrado';
    }

    $started = $r['Process_Started'] ? new DateTime($r['Process_Started']) : clone $now;
    $diffH   = max(0, (int)floor(($now->getTimestamp() - $started->getTimestamp()) / 3600));
    $limitH  = slaHours($status, $r['Test_Type'], $SLA);
    $alert   = $diffH >= $limitH;

    $by[$status][] = [
      'id'            => $r['id'],
      'Sample_ID'     => $r['Sample_ID'],
      'Sample_Number' => $r['Sample_Number'],
      'Test_Type'     => $r['Test_Type'],
      'Status'        => $status,
      'Sub_Stage'     => $r['sub_stage'],
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
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
