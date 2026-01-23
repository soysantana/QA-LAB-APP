<?php
// /api/kanban_list.php
declare(strict_types=1);
require_once('../config/load.php');
header('Content-Type: application/json; charset=utf-8');

$db = $GLOBALS['db'];

/* ============================================================
   SLA
============================================================ */
$SLA = [
  'Registrado'  => 24,
  'Preparación' => 48,
  'Realización' => 72,
  'Repetición'  => 72,
  'Entrega'     => 24,
  'Revisado'    => 24,
];

function slaHours(string $status, string $testTypeNorm, array $SLA): int {
  // (Si algún día tienes SLA por tipo, puedes mapearlo por norm)
  return (int)($SLA[$status] ?? 48);
}

/* ============================================================
   NORMALIZACIÓN (solo para llaves / comparar)
============================================================ */
function norm_test(string $v): string {
  $v = str_replace("\xC2\xA0", " ", $v);
  $v = strtoupper(trim($v));
  $v = preg_replace('/\s+/', '', $v);
  $v = preg_replace('/[\-\_\.\/]+/', '', $v);
  return $v;
}

/* ============================================================
   LABEL BONITO (solo para mostrar)
============================================================ */
function label_test(string $v): string {
  $v = str_replace("\xC2\xA0", " ", $v);
  $v = trim($v);
  $v = preg_replace('/\s+/', ' ', $v);
  return $v;
}

/* ============================================================
   Split que devuelve PARES: [ ['norm'=>..., 'label'=>...], ... ]
============================================================ */
function split_tests_pairs(string $tt): array {
  $tt = str_replace("\xC2\xA0", " ", $tt);
  $tt = trim($tt);
  if ($tt === '') return [];

  $parts = preg_split('/[,\n;\r\/]+/', $tt);

  $seen = [];
  $out  = [];

  foreach ($parts as $p) {
    $lab = label_test((string)$p);
    if ($lab === '') continue;

    $norm = norm_test($lab);
    if ($norm === '') continue;

    if (isset($seen[$norm])) continue;
    $seen[$norm] = true;

    $out[] = ['norm'=>$norm, 'label'=>$lab];
  }

  return $out;
}

function key_row(string $sid, string $num): string {
  return strtoupper(trim($sid)).'|'.strtoupper(trim($num));
}
function key_test(string $sid, string $num, string $ttNorm): string {
  return key_row($sid, $num).'|'.$ttNorm;
}

/* ============================================================
   ID manual cuando tu tabla NO tiene AUTO_INCREMENT
============================================================ */
function next_workflow_id($db) {
  $col = $db->query("SHOW COLUMNS FROM test_workflow LIKE 'id'");
  if (!$col) return uniqid('WF');

  $c = $db->fetch_assoc($col);
  $type = strtolower((string)($c['Type'] ?? ''));

  $isNumeric = (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false);

  if (!$isNumeric) {
    return uniqid('WF'); // varchar / char
  }

  $rs = $db->query("SELECT MAX(id) AS m FROM test_workflow");
  $m = 0;
  if ($rs) {
    $row = $db->fetch_assoc($rs);
    $m = (int)($row['m'] ?? 0);
  }
  return $m + 1;
}

/**
 * Inserta fila real en workflow si no existe y devuelve row_id.
 * Inserta el Test_Type usando el LABEL (bonito) para que quede así guardado.
 */
function ensure_workflow_row($db, string $sid, string $num, string $ttLabel): ?string {
  $sidE = $db->escape($sid);
  $numE = $db->escape($num);
  $labE = $db->escape($ttLabel);
  $norm = norm_test($ttLabel);
  $normE = $db->escape($norm);

  // búsqueda por normalizado para no depender de mayúsculas/espacios
  $ex = find_by_sql("
    SELECT id FROM test_workflow
    WHERE Sample_ID='{$sidE}'
      AND Sample_Number='{$numE}'
      AND REPLACE(UPPER(Test_Type),' ','') = REPLACE(UPPER('{$normE}'),' ','')
    LIMIT 1
  ");

  if ($ex && isset($ex[0]['id'])) {
    return (string)$ex[0]['id'];
  }

  $newId = next_workflow_id($db);
  $newIdE = $db->escape((string)$newId);

  $ok = $db->query("
    INSERT INTO test_workflow
      (id, Sample_ID, Sample_Number, Test_Type, Status, sub_stage, Process_Started, Updated_At, Updated_By)
    VALUES
      ('{$newIdE}','{$sidE}','{$numE}','{$labE}','Registrado','',NOW(),NOW(),'AUTO')
  ");

  if (!$ok) return null;

  return (string)$newId;
}

try {
  $q    = trim($_GET['q'] ?? '');
  $test = trim($_GET['test'] ?? '');
  $testNorm = $test !== '' ? norm_test($test) : '';

  /* ============================================================
     1) REQUISICIÓN = FUENTE DE VERDAD
     - Mapa: SID|NUM|TTNORM => LABEL
  ============================================================ */
  $reqWhere = [];
  if ($q !== '') {
    $qE = $db->escape($q);
    $reqWhere[] = "(Sample_ID LIKE '%{$qE}%' OR Sample_Number LIKE '%{$qE}%' OR Test_Type LIKE '%{$qE}%')";
  }
  $reqWhereSql = $reqWhere ? "WHERE ".implode(" AND ", $reqWhere) : "";

  $reqRows = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type
    FROM lab_test_requisition_form
    {$reqWhereSql}
  ");

  $current = []; // key => label

  foreach ($reqRows as $r) {
    $sid = trim((string)($r['Sample_ID'] ?? ''));
    $num = trim((string)($r['Sample_Number'] ?? ''));
    $raw = (string)($r['Test_Type'] ?? '');

    if ($sid === '') continue;

    $pairs = split_tests_pairs($raw);
    foreach ($pairs as $p) {
      $ttN = $p['norm'];
      $ttL = $p['label'];

      if ($testNorm !== '' && $ttN !== $testNorm) continue;

      $k = key_test($sid, $num, $ttN);

      // guarda el label del requisition como “preferido”
      if (!isset($current[$k])) $current[$k] = $ttL;
    }
  }

  /* ============================================================
     2) WORKFLOW ACTUAL
  ============================================================ */
  $wfWhere = [];
  if ($q !== '') {
    $qE = $db->escape($q);
    $wfWhere[] = "(Sample_ID LIKE '%{$qE}%' OR Sample_Number LIKE '%{$qE}%' OR Test_Type LIKE '%{$qE}%')";
  }
  $wfWhereSql = $wfWhere ? "WHERE ".implode(" AND ", $wfWhere) : "";

  $wfSql = "
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
    {$wfWhereSql}
    ORDER BY FIELD(Status,'Registrado','Preparación','Realización','Repetición','Entrega','Revisado'),
             Updated_At DESC
  ";

  $rs = $db->query($wfSql);
  if (!$rs) throw new RuntimeException($db->error ?: 'DB_ERROR');

  $wfRows = [];
  while ($r = $db->fetch_assoc($rs)) $wfRows[] = $r;

  // Mapa: SID|NUM|TTNORM => row_id
  $existingWF = [];
  foreach ($wfRows as $r) {
    $sid = (string)($r['Sample_ID'] ?? '');
    $num = (string)($r['Sample_Number'] ?? '');
    $pairs = split_tests_pairs((string)($r['Test_Type'] ?? ''));

    foreach ($pairs as $p) {
      $ttN = $p['norm'];
      $k = key_test($sid, $num, $ttN);
      $existingWF[$k] = (string)$r['id'];
    }
  }

  /* ============================================================
     3) INSERTAR ENSAYOS NUEVOS (REQUISICIÓN -> WORKFLOW)
============================================================ */
  foreach ($current as $k => $ttLabel) {
    if (isset($existingWF[$k])) continue;

    $parts = explode('|', $k, 3);
    $sid = $parts[0] ?? '';
    $num = $parts[1] ?? '';
    if ($sid === '') continue;

    $newRowId = ensure_workflow_row($db, $sid, $num, $ttLabel);
    if ($newRowId) {
      $existingWF[$k] = $newRowId;

      // añadir para render inmediato
      $wfRows[] = [
        'id' => $newRowId,
        'Sample_ID' => $sid,
        'Sample_Number' => $num,
        'Test_Type' => $ttLabel, // guardamos label
        'Status' => 'Registrado',
        'sub_stage' => '',
        'Process_Started' => date('Y-m-d H:i:s'),
        'Updated_At' => date('Y-m-d H:i:s'),
        'Updated_By' => 'AUTO',
      ];
    }
  }

  /* ============================================================
     4) RENDER: SOLO LO QUE ESTÁ HOY EN REQUISICIÓN
============================================================ */
  $by = [
    'Registrado'  => [],
    'Preparación' => [],
    'Realización' => [],
    'Repetición'  => [],
    'Entrega'     => [],
    'Revisado'    => [],
  ];

  $now = new DateTime('now');

  foreach ($wfRows as $r) {
    $status = trim((string)($r['Status'] ?? 'Registrado'));
    if (!isset($by[$status])) $status = 'Registrado';

    $sid = (string)($r['Sample_ID'] ?? '');
    $num = (string)($r['Sample_Number'] ?? '');

    $started = !empty($r['Process_Started']) ? new DateTime((string)$r['Process_Started']) : clone $now;
    $diffH   = max(0, (int) floor(($now->getTimestamp() - $started->getTimestamp()) / 3600));

    $pairs = split_tests_pairs((string)($r['Test_Type'] ?? ''));
    if (!$pairs) continue;

    foreach ($pairs as $p) {
      $ttN = $p['norm'];

      // ✅ filtro principal: si ya NO está en requisición, NO se muestra
      $k = key_test($sid, $num, $ttN);
      if (!isset($current[$k])) continue;

      $ttLabel = $current[$k] ?? $p['label'];

      $limitH = slaHours($status, $ttN, $SLA);
      $alert  = $diffH >= $limitH;

      // ID para mover/substage: row_id|TTNORM (estable)
      $cardId = (string)$r['id'] . '|' . $ttN;

      $by[$status][] = [
        'id'            => $cardId,
        'row_id'        => (string)$r['id'],
        'Sample_ID'     => $sid,
        'Sample_Number' => $num,

        // IMPORTANTE:
        // Test_Type = NORMALIZADO para que tus APIs no se rompan
        // Test_Label = para UI bonita
        'Test_Type'     => $ttN,
        'Test_Label'    => $ttLabel,

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
