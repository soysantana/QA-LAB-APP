<?php
// /api/kanban_move.php
declare(strict_types=1);
require_once('../config/load.php');

@ini_set('display_errors', '0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

$ALLOWED = ['Registrado','Preparación','Realización','Entrega'];

$TABLES = [
  'Preparación' => 'test_preparation',
  'Realización' => 'test_realization',
  'Entrega'     => 'test_delivery',
];

$OP_COLUMNS = [
  'id'            => 'id',
  'sample_id'     => 'Sample_ID',
  'sample_number' => 'Sample_Number',
  'test_type'     => 'Test_Type',
  'technician'    => 'Technician',
  'start_date'    => 'Start_Date',
  'register_by'   => 'Register_By',
  'register_date' => 'Register_Date',
  'status'        => 'Status',
];

function uuid_hex(): string { return bin2hex(random_bytes(16)); }

function respond($ok, $payload = []) {
  echo json_encode(['ok' => $ok] + $payload, JSON_UNESCAPED_UNICODE);
  exit;
}

function insert_operational_rows(
  $db,
  string $to,
  array $testRow,
  string $who,
  array $technicians,
  array $TABLES,
  array $OP_COLUMNS
): void {
  if ($to === 'Registrado') return;
  if (!isset($TABLES[$to]) || !$TABLES[$to]) return;
  $tbl = $TABLES[$to];

  $techs = array_values(array_unique(array_filter(array_map('strval', $technicians))));
  if (!$techs) { $techs = [$who]; }

  foreach ($techs as $t) {
    $tid = uuid_hex();
    $sql = sprintf(
      "INSERT INTO %s (%s,%s,%s,%s,%s,%s,%s,%s,%s)
       VALUES ('%s','%s','%s','%s','%s',CURDATE(),'%s',NOW(),'InProgress')",
      $tbl,
      $OP_COLUMNS['id'], $OP_COLUMNS['sample_id'], $OP_COLUMNS['sample_number'], $OP_COLUMNS['test_type'],
      $OP_COLUMNS['technician'], $OP_COLUMNS['start_date'], $OP_COLUMNS['register_by'], $OP_COLUMNS['register_date'], $OP_COLUMNS['status'],
      $db->escape($tid),
      $db->escape($testRow['Sample_ID']),
      $db->escape($testRow['Sample_Number']),
      $db->escape($testRow['Test_Type']),
      $db->escape($t),
      $db->escape($who)
    );
    $db->query($sql);
  }
}

function initial_sub_stage(string $status): ?string {
  if ($status === 'Preparación') return 'P1';
  if ($status === 'Realización') return 'R1';
  if ($status === 'Entrega')     return 'E1';
  return null;
}

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, ['error' => 'Método no permitido']);
  }

  $raw = file_get_contents('php://input');
  $payload = json_decode($raw, true);
  if (!is_array($payload)) {
    respond(false, ['error' => 'Payload inválido']);
  }

  $id    = isset($payload['id']) ? trim((string)$payload['id']) : '';
  $to    = isset($payload['to']) ? trim((string)$payload['to']) : '';
  $note  = isset($payload['note']) ? trim((string)$payload['note']) : '';
  $techs = (isset($payload['technicians']) && is_array($payload['technicians'])) ? $payload['technicians'] : [];

  if ($id === '' || $to === '') respond(false, ['error' => 'Datos insuficientes']);
  if (!in_array($to, $ALLOWED, true)) respond(false, ['error' => 'Estado destino no permitido']);

  $user = function_exists('current_user')
    ? (current_user()['name'] ?? current_user()['username'] ?? 'system')
    : 'system';
  $who  = (string)$user;

  $idEsc   = $db->escape($id);
  $sqlLoad = "SELECT * FROM test_workflow WHERE id='{$idEsc}' LIMIT 1";
  $test    = $db->fetch_assoc($db->query($sqlLoad));
  if (!$test) respond(false, ['error' => 'Tarjeta no encontrada']);

  $from = $test['Status'];

  $db->query('START TRANSACTION');

  $actId = uuid_hex();
  $sqlAct = sprintf(
    "INSERT INTO test_activity (id, test_id, From_Status, To_Status, Changed_At, Changed_By, Note)
     VALUES ('%s','%s','%s','%s',NOW(),'%s','%s')",
    $db->escape($actId),
    $idEsc,
    $db->escape($from),
    $db->escape($to),
    $db->escape($who),
    $db->escape($note)
  );
  $db->query($sqlAct);

  $techs = array_values(array_unique(array_filter(array_map('strval', $techs))));
  foreach ($techs as $t) {
    $sqlTech = sprintf(
      "INSERT IGNORE INTO test_activity_technician (activity_id, Technician)
       VALUES ('%s','%s')",
      $db->escape($actId),
      $db->escape($t)
    );
    $db->query($sqlTech);
  }

  $sub = initial_sub_stage($to);
  if ($sub === null) {
    $sqlUpd = sprintf(
      "UPDATE test_workflow
       SET Status='%s', Process_Started=NOW(), Updated_By='%s', Updated_At=NOW()
       WHERE id='%s' LIMIT 1",
      $db->escape($to),
      $db->escape($who),
      $idEsc
    );
  } else {
    $sqlUpd = sprintf(
      "UPDATE test_workflow
       SET Status='%s', sub_stage='%s', Process_Started=NOW(), Updated_By='%s', Updated_At=NOW()
       WHERE id='%s' LIMIT 1",
      $db->escape($to),
      $db->escape($sub),
      $db->escape($who),
      $idEsc
    );
  }
  $db->query($sqlUpd);

  insert_operational_rows($db, $to, $test, $who, $techs, $TABLES, $OP_COLUMNS);

  $db->query('COMMIT');

  respond(true);

} catch (Throwable $e) {
  try { $db->query('ROLLBACK'); } catch (\Throwable $ignored) {}
  http_response_code(400);
  respond(false, ['error' => $e->getMessage()]);
}
