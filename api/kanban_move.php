<?php
// /api/kanban_move.php
declare(strict_types=1);
require_once('../config/load.php');

@ini_set('display_errors','0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

$ALLOWED = ['Registrado','Preparación','Realización','Repetición','Entrega'];

$TABLES = [
  'Preparación' => 'test_preparation',
  'Realización' => 'test_realization',
  'Repetición'  => 'test_repeat',
  'Entrega'     => 'test_delivery'
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
  'status'        => 'Status'
];

function uuid_hex(): string { return bin2hex(random_bytes(16)); }

function respond($ok, $payload=[]){
  echo json_encode(['ok'=>$ok]+$payload, JSON_UNESCAPED_UNICODE);
  exit;
}

function initial_sub_stage(string $status): ?string {
  if ($status === 'Preparación') return 'P1';
  if ($status === 'Realización') return 'R1';
  if ($status === 'Repetición')  return 'RE1';
  if ($status === 'Entrega')     return 'E1';
  return null;
}

function insert_operational_rows($db, string $to, array $testRow, string $who, array $techs, array $TABLES, array $COLUMNS): void {
  if ($to === 'Registrado') return;
  if (!isset($TABLES[$to])) return;

  $tbl = $TABLES[$to];

  $techList = array_values(array_unique(array_filter(array_map('strval',$techs))));
  if (!$techList) $techList = [$who];

  foreach ($techList as $t){
    $tid = uuid_hex();

    $sql = sprintf(
      "INSERT INTO %s (%s,%s,%s,%s,%s,%s,%s,%s,%s)
       VALUES('%s','%s','%s','%s','%s',CURDATE(),'%s',NOW(),'InProgress')",
      $tbl,
      $COLUMNS['id'], $COLUMNS['sample_id'], $COLUMNS['sample_number'], $COLUMNS['test_type'],
      $COLUMNS['technician'], $COLUMNS['start_date'], $COLUMNS['register_by'], $COLUMNS['register_date'], $COLUMNS['status'],
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

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    respond(false,['error'=>'Método no permitido']);
  }

  $raw = file_get_contents('php://input');
  $payload = json_decode($raw,true);

  if (!is_array($payload)){
    respond(false,['error'=>'Payload inválido']);
  }

  $id    = trim((string)($payload['id'] ?? ''));
  $to    = trim((string)($payload['to'] ?? ''));
  $note  = trim((string)($payload['note'] ?? ''));
  $techs = is_array($payload['technicians'] ?? null) ? $payload['technicians'] : [];

  if ($id==='' || $to==='') respond(false,['error'=>'Datos insuficientes']);
  if (!in_array($to,$ALLOWED,true)) respond(false,['error'=>'Estado destino no permitido']);

  $user = function_exists('current_user')
    ? (current_user()['name'] ?? current_user()['username'] ?? 'system')
    : 'system';

  $idEsc = $db->escape($id);
  $test = $db->fetch_assoc($db->query("SELECT * FROM test_workflow WHERE id='{$idEsc}' LIMIT 1"));
  if (!$test) respond(false,['error'=>'Tarjeta no encontrada']);

  $from = $test['Status'];

  $db->query("START TRANSACTION");

  $actId = uuid_hex();
  $sqlAct = sprintf(
    "INSERT INTO test_activity (id,test_id,From_Status,To_Status,Changed_At,Changed_By,Note)
     VALUES('%s','%s','%s','%s',NOW(),'%s','%s')",
    $db->escape($actId),
    $idEsc,
    $db->escape($from),
    $db->escape($to),
    $db->escape($user),
    $db->escape($note)
  );
  $db->query($sqlAct);

  foreach ($techs as $t){
    $sqlTech = sprintf(
      "INSERT IGNORE INTO test_activity_technician (activity_id,Technician)
       VALUES('%s','%s')",
      $db->escape($actId),
      $db->escape($t)
    );
    $db->query($sqlTech);
  }

  $sub = initial_sub_stage($to);

  if ($sub === null){
    $sqlUpd = sprintf(
      "UPDATE test_workflow SET Status='%s',Process_Started=NOW(),Updated_By='%s',Updated_At=NOW()
       WHERE id='%s' LIMIT 1",
      $db->escape($to),
      $db->escape($user),
      $idEsc
    );
  } else {
    $sqlUpd = sprintf(
      "UPDATE test_workflow SET Status='%s',sub_stage='%s',Process_Started=NOW(),Updated_By='%s',Updated_At=NOW()
       WHERE id='%s' LIMIT 1",
      $db->escape($to),
      $db->escape($sub),
      $db->escape($user),
      $idEsc
    );
  }
  $db->query($sqlUpd);

  insert_operational_rows($db,$to,$test,$user,$techs,$TABLES,$OP_COLUMNS);

  $db->query("COMMIT");

  respond(true);

} catch (Throwable $e){
  $db->query("ROLLBACK");
  http_response_code(400);
  respond(false,['error'=>$e->getMessage()]);
}
