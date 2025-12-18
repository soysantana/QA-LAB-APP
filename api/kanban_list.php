<?php
// /api/kanban_list.php
declare(strict_types=1);
require_once('../config/load.php');
header('Content-Type: application/json; charset=utf-8');

$SLA = [
  'Registrado'  => 24,
  'Preparación' => 48,
  'Realización' => 72,
  'Repetición'  => 72,
  'Entrega'     => 24,
  'Revisado'    => 24,
];

function slaHours(string $status, string $testType, array $SLA): int {
  if (isset($SLA[$testType]) && is_array($SLA[$testType]) && isset($SLA[$testType][$status])) {
    return (int)$SLA[$testType][$status];
  }
  return (int)($SLA[$status] ?? 48);
}

function norm_test(string $v): string {
  $v = str_replace("\xC2\xA0", " ", $v);
  $v = strtoupper(trim($v));
  $v = preg_replace('/\s+/', '', $v);
  $v = preg_replace('/[\-\_\.\/]+/', '', $v);
  return $v;
}

function split_tests(string $tt): array {
  $tt = str_replace("\xC2\xA0", " ", $tt);
  $tt = trim($tt);
  if ($tt === '') return [];
  $parts = array_filter(array_map('trim', explode(',', $tt)));
  $out = [];
  foreach ($parts as $p) {
    $p = norm_test($p);
    if ($p !== '') $out[] = $p;
  }
  return array_values(array_unique($out));
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
    // Soporta filas con "GS,MC,AL"
    $where[] = "FIND_IN_SET(UPPER('{$testEsc}'), REPLACE(UPPER(Test_Type),' ','')) > 0";
  }

  $whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';

  $sql = "
    SELECT
      id,
      Sample_ID,
      Sample_Number,
      TRIM(Test_Type) AS Test_Type,
      TRIM(Status) AS Status,
      sub_stage,
      Process_Started,
      Updated_At,
      Updated_By
    FROM test_workflow
    {$whereSql}
    ORDER BY FIELD(Status,'Registrado','Preparación','Realización','Repetición','Entrega','Revisado'),
             Updated_At DESC
  ";

  $rs = $db->query($sql);
  if (!$rs) throw new RuntimeException($db->error ?: 'DB_ERROR');

  $by = [
    'Registrado'  => [],
    'Preparación' => [],
    'Realización' => [],
    'Repetición'  => [],
    'Entrega'     => [],
    'Revisado'    => [],
  ];

  $now = new DateTime('now');

  while ($r = $db->fetch_assoc($rs)) {

    $status = trim((string)($r['Status'] ?? 'Registrado'));
    if (!isset($by[$status])) $status = 'Registrado';

    $started = !empty($r['Process_Started']) ? new DateTime((string)$r['Process_Started']) : clone $now;
    $diffH   = max(0, (int) floor(($now->getTimestamp() - $started->getTimestamp()) / 3600));

    $tests = split_tests((string)($r['Test_Type'] ?? ''));

    // si por alguna razón no hay coma y viene vacío raro
    if (!$tests) {
      $single = norm_test((string)($r['Test_Type'] ?? ''));
      if ($single !== '') $tests = [$single];
    }

    foreach ($tests as $tt) {
      $limitH = slaHours($status, $tt, $SLA);
      $alert  = $diffH >= $limitH;

      // ID por tarjeta = "rowId|TEST"
      $cardId = (string)$r['id'] . '|' . $tt;

      $by[$status][] = [
        'id'            => $cardId,
        'row_id'        => (string)$r['id'],
        'Sample_ID'     => (string)$r['Sample_ID'],
        'Sample_Number' => (string)$r['Sample_Number'],
        'Test_Type'     => $tt,
        'Status'        => $status,
        'Sub_Stage'     => $r['sub_stage'],
        'Since'         => $r['Process_Started'],
        'Updated_By'    => $r['Updated_By'],
        'Dwell_Hours'   => $diffH,
        'SLA_Hours'     => $limitH,
        'Alert'         => $alert,
      ];
    }
  }

  echo json_encode(['ok' => true, 'data' => $by], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
