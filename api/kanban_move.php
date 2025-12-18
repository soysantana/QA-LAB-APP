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

function split_tests(string $tt): array {
  $tt = strtoupper(trim($tt));
  $tt = str_replace("\xC2\xA0", " ", $tt);
  $tt = preg_replace('/\s+/', '', $tt); // quita espacios
  if ($tt === '') return [];
  $parts = array_filter(array_map('trim', explode(',', $tt)));
  $out = [];
  foreach ($parts as $p){
    $p = strtoupper(trim($p));
    $p = preg_replace('/[\s\-\_\.\/]+/', '', $p);
    if ($p !== '') $out[] = $p;
  }
  return array_values(array_unique($out));
}

function join_tests(array $tests): string {
  $tests = array_values(array_unique(array_filter($tests)));
  return implode(',', $tests);
}

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
  if ($res === false) throw new RuntimeException($db->error ?: 'DB_ERROR');
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
  if (!is_array($payload)) respond(false, ['error'=>'Payload inválido', 'raw'=>$raw]);

  // ============================
  // ID puede venir: "rowId" o "rowId|TEST"
  // ============================
  $idRaw = trim((string)($payload['id'] ?? ''));
  $to    = trim((string)($payload['to'] ?? ''));
  $note  = trim((string)($payload['note'] ?? ''));
  $techs = is_array($payload['technicians'] ?? null) ? $payload['technicians'] : [];

  if ($idRaw === '' || $to === '') respond(false, ['error'=>'Datos insuficientes']);
  if (!in_array($to, $ALLOWED, true)) respond(false, ['error'=>'Estado destino no permitido', 'to'=>$to]);

  $rowId = $idRaw;
  $moveTest = ''; // si viene row|TEST, aquí cae TEST

  if (strpos($idRaw, '|') !== false) {
    [$rowId, $moveTest] = explode('|', $idRaw, 2);
    $rowId = trim($rowId);
    $moveTest = strtoupper(trim($moveTest));
    $moveTest = preg_replace('/[\s\-\_\.\/]+/', '', $moveTest);
  }

  $id = $rowId;

  $u = function_exists('current_user') ? current_user() : [];
  $user = $u['name'] ?? $u['username'] ?? 'system';

  $idEsc = $db->escape($id);

  $rs = $db->query("SELECT * FROM test_workflow WHERE id='{$idEsc}' LIMIT 1");
  if (!$rs) throw new RuntimeException($db->error ?: 'DB_ERROR');
  $test = $db->fetch_assoc($rs);
  if (!$test) respond(false, ['error'=>'Tarjeta no encontrada']);

  $from = $test['Status'] ?? '';

  // Tests actuales de la tarjeta
  $originalTests = split_tests((string)($test['Test_Type'] ?? ''));

  if ($moveTest !== '') {
    if (!in_array($moveTest, $originalTests, true)) {
      respond(false, [
        'error' => 'Ese Test_Type no existe en esa tarjeta',
        'moveTest' => $moveTest,
        'cardTests' => $originalTests
      ]);
    }
  }

  q($db, "START TRANSACTION");

  // ===================================
  // ACTIVITY (siempre registramos acción)
  // ===================================
  $actId = uuid_hex();
  $sqlAct = sprintf(
    "INSERT INTO test_activity (id,test_id,From_Status,To_Status,Changed_At,Changed_By,Note)
     VALUES('%s','%s','%s','%s',NOW(),'%s','%s')",
    $db->escape($actId),
    $db->escape($id),
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

  // ==============================
  // UPDATE / SPLIT
  // ==============================
  $sub = initial_sub_stage($to);

  if ($moveTest === '') {

    // ===== CASO NORMAL: mueve la fila completa =====
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

    // Inserción operacional (fila completa tal cual está)
    insert_operational_rows($db, $to, $test, $user, $techs, $TABLES, $OPS);

  } else {

    // ===== CASO SPLIT: mover solo 1 test =====

    // 1) quitar moveTest de la tarjeta original
    $remaining = array_values(array_diff($originalTests, [$moveTest]));
    $remainingTT = join_tests($remaining);

    if ($remainingTT === '') {
      // si no quedan tests, eliminar tarjeta original
      q($db, "DELETE FROM test_workflow WHERE id='{$idEsc}' LIMIT 1");
    } else {
      // si quedan tests, actualizar Test_Type en tarjeta original
      $sqlKeep = sprintf(
        "UPDATE test_workflow
            SET Test_Type='%s', Updated_By='%s', Updated_At=NOW()
          WHERE id='%s' LIMIT 1",
        $db->escape($remainingTT),
        $db->escape($user),
        $idEsc
      );
      q($db, $sqlKeep);
    }

    // 2) crear nueva tarjeta solo con moveTest y moverla a $to
    $newId = uuid_hex();
    $newIdEsc = $db->escape($newId);

    $subSql = ($sub !== null) ? ("'".$db->escape($sub)."'") : "NULL";

    $sqlNew = sprintf(
      "INSERT INTO test_workflow
        (id, Sample_ID, Sample_Number, Test_Type, Status, sub_stage, Process_Started, Updated_At, Updated_By)
       VALUES
        ('%s','%s','%s','%s','%s',%s,NOW(),NOW(),'%s')",
      $newIdEsc,
      $db->escape((string)($test['Sample_ID'] ?? '')),
      $db->escape((string)($test['Sample_Number'] ?? '')),
      $db->escape($moveTest),
      $db->escape($to),
      $subSql,
      $db->escape($user)
    );
    q($db, $sqlNew);

    // 3) activity extra para la nueva tarjeta (opcional pero recomendado)
    $act2 = uuid_hex();
    $sqlAct2 = sprintf(
      "INSERT INTO test_activity (id,test_id,From_Status,To_Status,Changed_At,Changed_By,Note)
       VALUES('%s','%s','%s','%s',NOW(),'%s','%s')",
      $db->escape($act2),
      $newIdEsc,
      $db->escape($from),
      $db->escape($to),
      $db->escape($user),
      $db->escape("Split from {$id}. ".$note)
    );
    q($db, $sqlAct2);

    foreach ($techs as $t) {
      $sqlTech2 = sprintf(
        "INSERT IGNORE INTO test_activity_technician (activity_id,Technician)
         VALUES('%s','%s')",
        $db->escape($act2),
        $db->escape((string)$t)
      );
      q($db, $sqlTech2);
    }

    // 4) Inserción operacional SOLO para el test movido
    $testSingle = $test;
    $testSingle['Test_Type'] = $moveTest;
    insert_operational_rows($db, $to, $testSingle, $user, $techs, $TABLES, $OPS);

    // puedes devolver el newId si el front quiere refrescar
    // respond(true, ['new_id'=>$newId]);
  }

  q($db, "COMMIT");
  respond(true);

} catch (Throwable $e) {
  @ $db->query("ROLLBACK");
  http_response_code(400);
  respond(false, ['error'=>$e->getMessage()]);
}
