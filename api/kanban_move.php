<?php
// /api/kanban_move.php
declare(strict_types=1);
require_once('../config/load.php');

// ====== Cabeceras seguras (evitan contaminar el JSON con advertencias) ======
@ini_set('display_errors', '0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

// ====== CONFIG ======
$ALLOWED = ['Registrado','Preparación','Realización','Entrega'];

// Nombre exacto de tus tablas operativas:
$TABLES = [
  'Preparación' => 'test_preparation',
  'Realización' => 'test_realization',
  'Entrega'     => 'test_delivery',
];

// Columnas esperadas en cada tabla operativa (ajusta si tus nombres difieren)
$OP_COLUMNS = [
  'id'            => 'id',             // VARCHAR(36) PK
  'sample_id'     => 'Sample_ID',      // VARCHAR
  'sample_number' => 'Sample_Number',  // VARCHAR
  'test_type'     => 'Test_Type',      // VARCHAR
  'technician'    => 'Technician',     // VARCHAR (permite NULL)
  'start_date'    => 'Start_Date',     // DATE
  'register_by'   => 'Register_By',    // VARCHAR
  'register_date' => 'Register_Date',  // DATETIME
  'status'        => 'Status',         // VARCHAR (ej. 'InProgress')
];

// ====== HELPERS ======
/** Genera UUID(32 hex) portable */
function uuid_hex(): string { return bin2hex(random_bytes(16)); }

/** Respuesta JSON estándar */
function respond($ok, $payload = []) {
  echo json_encode(['ok' => $ok] + $payload, JSON_UNESCAPED_UNICODE);
  exit;
}

/** Inserta filas en la tabla operativa del estado destino (una por técnico) */
function insert_operational_rows($db, string $to, array $testRow, string $who, array $technicians, array $TABLES, array $OP_COLUMNS): void {
  if ($to === 'Registrado') return; // no hay tabla operativa para este estado

  if (!isset($TABLES[$to]) || !$TABLES[$to]) return;
  $tbl = $TABLES[$to];

  // dedup y saneo
  $techs = array_values(array_unique(array_filter(array_map('strval', $technicians))));
  // Si no mandan técnicos, por defecto registramos al ejecutor (mejor trazabilidad que NULL)
  if (count($techs) === 0) { $techs = [$who]; }

  foreach ($techs as $t) {
    $tid = uuid_hex();
    $tec = $db->escape($t);

    $sql = sprintf(
      "INSERT INTO %s
      (%s, %s, %s, %s, %s, %s, %s, %s, %s)
      VALUES
      ('%s', '%s', '%s', '%s', '%s', CURDATE(), '%s', NOW(), 'InProgress')",
      $tbl,
      $OP_COLUMNS['id'], $OP_COLUMNS['sample_id'], $OP_COLUMNS['sample_number'], $OP_COLUMNS['test_type'],
      $OP_COLUMNS['technician'], $OP_COLUMNS['start_date'], $OP_COLUMNS['register_by'], $OP_COLUMNS['register_date'], $OP_COLUMNS['status'],
      $db->escape($tid),
      $db->escape($testRow['Sample_ID']),
      $db->escape($testRow['Sample_Number']),
      $db->escape($testRow['Test_Type']),
      $tec,
      $db->escape($who)
    );
    $db->query($sql);
  }
}

try {
  // ====== Validación método ======
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, ['error' => 'Método no permitido']);
  }

  // ====== Carga payload ======
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

  // ====== Usuario ejecutor ======
  $user = function_exists('current_user') ? (current_user()['name'] ?? current_user()['username'] ?? 'system') : 'system';
  $who  = (string)$user;

  // ====== Cargar la tarjeta ======
  $idEsc = $db->escape($id);
  $sqlLoad = "SELECT * FROM test_workflow WHERE id='{$idEsc}' LIMIT 1";
  $test = $db->fetch_assoc($db->query($sqlLoad));
  if (!$test) respond(false, ['error' => 'Tarjeta no encontrada']);

  $from = $test['Status'];

  // ====== Transacción ======
  $db->query('START TRANSACTION');

  // ====== Registrar actividad ======
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

  // ====== Técnicos de la actividad (deduplicados) ======
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

  // ====== Actualizar estado actual (reinicia Process_Started) ======
  $sqlUpd = sprintf(
    "UPDATE test_workflow
     SET Status='%s', Process_Started=NOW(), Updated_By='%s'
     WHERE id='%s' LIMIT 1",
    $db->escape($to),
    $db->escape($who),
    $idEsc
  );
  $db->query($sqlUpd);

  // ====== Insertar filas operativas según estado destino ======
  insert_operational_rows($db, $to, $test, $who, $techs, $TABLES, $OP_COLUMNS);

  // ====== Commit ======
  $db->query('COMMIT');

  respond(true);

} catch (Throwable $e) {
  // rollback seguro
  try { $db->query('ROLLBACK'); } catch (\Throwable $ignored) {}
  http_response_code(400);
  respond(false, ['error' => $e->getMessage()]);
}
