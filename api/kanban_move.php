<?php
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

/**
 * Columnas reales por tabla (AJUSTA si tu BD usa otros nombres)
 * La idea: NO usar un mapping genérico para todas.
 */
$OPS = [
  'test_preparation' => [
    'id'            => 'id',
    'sample_id'     => 'Sample_ID',
    'sample_number' => 'Sample_Number',
    'test_type'     => 'Test_Type',
    'technician'    => 'Technician',
    'register_by'   => 'Register_By',
    'register_date' => 'Register_Date',
    'status'        => 'Status',
  ],
  'test_realization' => [
    'id'            => 'id',
    'sample_id'     => 'Sample_ID',
    'sample_number' => 'Sample_Number',
    'test_type'     => 'Test_Type',
    'technician'    => 'Technician',
    'register_by'   => 'Register_By',
    'register_date' => 'Register_Date',
    'status'        => 'Status',
  ],
  'test_repeat' => [
    'id'            => 'id',
    'sample_id'     => 'Sample_ID',
    'sample_number' => 'Sample_Number',
    'test_type'     => 'Test_Type',
    'technician'    => 'Technician',
    'register_by'   => 'Register_By',
    'register_date' => 'Register_Date',
    'status'        => 'Status',
    // OJO: intencionalmente NO pongo Start_Date aquí
  ],
  'test_delivery' => [
    'id'            => 'id',
    'sample_id'     => 'Sample_ID',
    'sample_number' => 'Sample_Number',
    'test_type'     => 'Test_Type',
    'technician'    => 'Technician',
    'register_by'   => 'Register_By',
    'register_date' => 'Register_Date',
    'status'        => 'Status',
  ],
];

function uuid_hex(): string { return bin2hex(random_bytes(16)); }

function respond(bool $ok, array $payload=[]): void {
  echo json_encode(['ok'=>$ok] + $payload, JSON_UNESCAPED_UNICODE);
  exit;
}

function initial_sub_stage(string $status): ?string {
  if ($status === 'Preparación') return 'P1';
  if ($status === 'Realización') return 'R1';
  if ($status === 'Repetición')  return 'RE1';
  if ($status === 'Entrega')     return 'E1';
  return null;
}

function q($db, string $sql): void {
  $res = $db->query($sql);
  if ($res === false) {
    throw new RuntimeException($db->error ?: 'DB_ERROR');
  }
}

function insert_operational_rows(
  $db,
  string $to,
  array $testRow,
  string $who,
  array $techs,
  array $TABLES,
  array $OPS
): void {
  if ($to === 'Registrado') return;
  if (!isset($TABLES[$to])) return;

  $tbl = $TABLES[$to];
  if (!isset($OPS[$tbl])) return;

  $C = $OPS[$tbl];

  $techList = array_values(array_unique(array_filter(array_map('strval',$techs))));
  if (!$techList) $techList = [$who];

  foreach ($techList as $t) {
    $tid = uuid_hex();

    // INSERT solo con columnas seguras por tabla
    $sql = sprintf(
      "INSERT INTO %s (%s,%s,%s,%s,%s,%s,%s,%s)
       VALUES('%s','%s','%s','%s','%s','%s',NOW(),'In Progress')",
      $tbl,
      $C['id'], $C['sample_id'], $C['sample_number'], $C['test_type'],
      $C['technician'], $C['register_by'], $C['register_date'], $C['status'],
      $db->escape($tid),
      $db->escape($testRow['Sample_ID'] ?? ''),
      $db->escape($testRow['Sample_Number'] ?? ''),
      $db->escape($testRow['Test_Type'] ?? ''),
      $db->escape($t),
      $db->escape($who)
    );

    q($db, $sql);
  }
}

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, ['error'=>'Método no permitido']);
  }

  $raw = file_get_contents('php://input');
  $payload = json_decode($raw, true);

  if (!is_array($payload)) {
    respond(false, ['error'=>'Payload inválido', 'raw'=>$raw]);
  }

  $id    = trim((string)($payload['id'] ?? ''));
  $to    = trim((string)($payload['to'] ?? ''));
  $note  = trim((string)($payload['note'] ?? ''));
  $techs = is_array($payload['technicians'] ?? null) ? $payload['technicians'] : [];

  if ($id === '' || $to === '') respond(false, ['error'=>'Datos insuficientes']);
  if (!in_array($to, $ALLOWED, true)) respond(false, ['error'=>'Estado destino no permitido', 'to'=>$to]);

  $u = function_exists('current_user') ? current_user() : [];
  $user = $u['name'] ?? $u['username'] ?? 'system';

  $idEsc = $db->escape($id);

  $rs = $db->query("SELECT * FROM test_workflow WHERE id='{$idEsc}' LIMIT 1");
  if (!$rs) throw new RuntimeException($db->error ?: 'DB_ERROR');
  $test = $db->fetch_assoc($rs);

  if (!$test) respond(false, ['error'=>'Tarjeta no encontrada']);

  $from = $test['Status'] ?? '';

  q($db, "START TRANSACTION");

  // activity
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
  q($db, $sqlAct);

  foreach ($techs as $t) {
    $sqlTech = sprintf(
      "INSERT IGNORE INTO test_activity_technician (activity_id,Technician)
       VALUES('%s','%s')",
      $db->escape($actId),
      $db->escape((string)$t)
    );
    q($db, $sqlTech);
  }

  // workflow update
  $sub = initial_sub_stage($to);

  if ($sub === null) {
    $sqlUpd = sprintf(
      "UPDATE test_workflow
          SET Status='%s', Process_Started=NOW(), Updated_By='%s', Updated_At=NOW()
        WHERE id='%s' LIMIT 1",
      $db->escape($to),
      $db->escape($user),
      $idEsc
    );
  } else {
    $sqlUpd = sprintf(
      "UPDATE test_workflow
          SET Status='%s', sub_stage='%s', Process_Started=NOW(), Updated_By='%s', Updated_At=NOW()
        WHERE id='%s' LIMIT 1",
      $db->escape($to),
      $db->escape($sub),
      $db->escape($user),
      $idEsc
    );
  }
  q($db, $sqlUpd);

  // operational insert
  insert_operational_rows($db, $to, $test, $user, $techs, $TABLES, $OPS);

  q($db, "COMMIT");
  respond(true);

} catch (Throwable $e) {
  // si falló antes del START, rollback igual no rompe
  @ $db->query("ROLLBACK");
  http_response_code(400);
  respond(false, ['error'=>$e->getMessage()]);
}
