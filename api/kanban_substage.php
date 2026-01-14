<?php
// /api/kanban_substage.php
declare(strict_types=1);
require_once('../config/load.php');

@ini_set('display_errors','0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

function respond(bool $ok, array $p=[]){
  echo json_encode(['ok'=>$ok] + $p, JSON_UNESCAPED_UNICODE);
  exit;
}

$VALID = [
  'Preparación' => ['P1','P2','P3','P4'],
  'Realización' => ['R1','R2','R3','R4'],
  'Repetición'  => ['RE1','RE2','RE3'],
  'Entrega'     => ['E1']
];

function firstRow($db, string $sql): ?array {
  $q = $db->query($sql);
  if (!$q) return null;
  $r = $db->fetch_assoc($q);
  return $r ?: null;
}

function hasColumn($db, string $table, string $col): bool {
  $t = $db->escape($table);
  $c = $db->escape($col);
  $r = firstRow($db, "SHOW COLUMNS FROM `{$t}` LIKE '{$c}'");
  return (bool)$r;
}

try {
  if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
    http_response_code(405);
    respond(false, ['error'=>'Método no permitido']);
  }

  $payload = json_decode(file_get_contents('php://input'), true);
  if (!is_array($payload)) respond(false, ['error'=>'Payload inválido']);

  $sub = trim((string)($payload['sub_stage'] ?? ''));
  if ($sub === '') respond(false, ['error'=>'Sub-etapa requerida']);

  // Usuario
  $user = function_exists('current_user')
    ? (current_user()['name'] ?? current_user()['username'] ?? 'system')
    : 'system';
  $userEsc = $db->escape((string)$user);

  // 1) Buscar por llave real si viene
  $sid = trim((string)($payload['Sample_ID'] ?? ''));
  $sno = trim((string)($payload['Sample_Number'] ?? ''));
  $tt  = trim((string)($payload['Test_Type'] ?? ''));

  // Si no viene llave, intentar por id (compatibilidad)
  $incomingId = trim((string)($payload['id'] ?? ''));

  $wf = null;

  // ---- A) Si viene llave real, matching tolerante ----
  if ($sid !== '' && $sno !== '' && $tt !== '') {
    $sidEsc = $db->escape($sid);
    $snoEsc = $db->escape($sno);
    $ttEsc  = $db->escape($tt);

    // Nota: Sample_Number puede venir "0078" pero estar guardado "78"
    // Por eso comparamos TRIM y también comparación numérica.
    $wf = firstRow($db, "
      SELECT id, Status
      FROM test_workflow
      WHERE TRIM(Sample_ID) = TRIM('{$sidEsc}')
        AND (
              TRIM(Sample_Number) = TRIM('{$snoEsc}')
           OR CAST(TRIM(Sample_Number) AS UNSIGNED) = CAST(TRIM('{$snoEsc}') AS UNSIGNED)
        )
        AND (
              TRIM(Test_Type) = TRIM('{$ttEsc}')
           OR TRIM(Test_Type) = TRIM(UPPER('{$ttEsc}'))
           OR TRIM(Test_Type) = TRIM(LOWER('{$ttEsc}'))
        )
      LIMIT 1
    ");

    // Si no encuentra con TT estricto, intenta sin TT (por si workflow guarda TT vacío)
    if (!$wf) {
      $wf = firstRow($db, "
        SELECT id, Status
        FROM test_workflow
        WHERE TRIM(Sample_ID) = TRIM('{$sidEsc}')
          AND (
                TRIM(Sample_Number) = TRIM('{$snoEsc}')
             OR CAST(TRIM(Sample_Number) AS UNSIGNED) = CAST(TRIM('{$snoEsc}') AS UNSIGNED)
          )
        LIMIT 1
      ");
    }
  }

  // ---- B) Si no encontró por llave, intenta por id directo ----
  if (!$wf && $incomingId !== '') {
    $idEsc = $db->escape($incomingId);
    $wf = firstRow($db, "
      SELECT id, Status
      FROM test_workflow
      WHERE id='{$idEsc}'
      LIMIT 1
    ");
  }

  // 2) Si NO EXISTE en workflow, crearla automáticamente (UPSERT)
  if (!$wf) {
    if ($sid === '' || $sno === '' || $tt === '') {
      respond(false, ['error'=>'Tarjeta no encontrada (y no hay llave para crearla)']);
    }

    // Generar id tipo hex (32 chars) parecido a muchos sistemas
    $newId = bin2hex(random_bytes(16));
    $newIdEsc = $db->escape($newId);

    $sidEsc = $db->escape($sid);
    $snoEsc = $db->escape($sno);
    $ttEsc  = $db->escape($tt);

    // Status por defecto si no existe
    $defaultStatus = 'Registrado';

    // Insert dinámico según columnas reales
    $cols = ['id','Sample_ID','Sample_Number','Test_Type','Status'];
    $vals = ["'{$newIdEsc}'","'{$sidEsc}'","'{$snoEsc}'","'{$ttEsc}'","'".$db->escape($defaultStatus)."'"];

    if (hasColumn($db,'test_workflow','Sub_Stage')) {
      $cols[] = 'Sub_Stage';
      $vals[] = "'".$db->escape($sub)."'";
    }
    if (hasColumn($db,'test_workflow','Updated_By')) {
      $cols[] = 'Updated_By';
      $vals[] = "'{$userEsc}'";
    }
    if (hasColumn($db,'test_workflow','Updated_At')) {
      $cols[] = 'Updated_At';
      $vals[] = "NOW()";
    }
    if (hasColumn($db,'test_workflow','Created_At')) {
      $cols[] = 'Created_At';
      $vals[] = "NOW()";
    }

    $sqlIns = "INSERT INTO test_workflow (".implode(',', array_map(fn($c)=>"`$c`",$cols)).")
               VALUES (".implode(',',$vals).")";
    $db->query($sqlIns);

    // Releer
    $wf = firstRow($db, "SELECT id, Status FROM test_workflow WHERE id='{$newIdEsc}' LIMIT 1");
    if (!$wf) respond(false, ['error'=>'No se pudo crear la tarjeta en test_workflow']);
  }

  // 3) Validar sub-etapa contra el Status actual
  $status = (string)$wf['Status'];
  if (!isset($VALID[$status]) || !in_array($sub, $VALID[$status], true)) {
    respond(false, ['error'=>"Sub-etapa no válida para este estado ({$status})"]);
  }

  // 4) Actualizar sub-etapa
  $wfIdEsc = $db->escape((string)$wf['id']);
  $subEsc  = $db->escape($sub);

  $set = ["`Sub_Stage`='{$subEsc}'"];
  if (hasColumn($db,'test_workflow','Updated_By')) $set[] = "Updated_By='{$userEsc}'";
  if (hasColumn($db,'test_workflow','Updated_At')) $set[] = "Updated_At=NOW()";

  $db->query("
    UPDATE test_workflow
    SET ".implode(',', $set)."
    WHERE id='{$wfIdEsc}'
    LIMIT 1
  ");

  respond(true);

} catch(Throwable $e){
  http_response_code(400);
  respond(false, ['error'=>$e->getMessage()]);
}
