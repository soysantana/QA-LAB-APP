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

try {
  if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    respond(false, ['error' => 'Método no permitido']);
  }

  $payload = json_decode(file_get_contents('php://input'), true);
  if (!is_array($payload)) respond(false, ['error' => 'Payload inválido']);

  // Tu JS envía { id, sub_stage }
  $incomingId = trim((string)($payload['id'] ?? ''));
  $subStage   = trim((string)($payload['sub_stage'] ?? ''));

  if ($incomingId === '' || $subStage === '') {
    respond(false, ['error' => 'Datos insuficientes']);
  }

  $idEsc = $db->escape($incomingId);

  // 1) Intento directo: ID real en workflow
  $wf = firstRow($db, "
    SELECT id, Sample_ID, Sample_Number, Test_Type, Status
    FROM test_workflow
    WHERE id='{$idEsc}'
    LIMIT 1
  ");

  // 2) Intento directo: ID real en repeat
  $tr = null;
  if (!$wf) {
    $tr = firstRow($db, "
      SELECT id, Sample_ID, Sample_Number, Test_Type, Status
      FROM test_repeat
      WHERE id='{$idEsc}'
      LIMIT 1
    ");
  }

  // 3) Si NO existe, resolver si el id es un “virtual id”
  //    (concat / md5 / sha1 de Sample_ID + Sample_Number + Test_Type)
  if (!$wf && !$tr) {

    // 3A) si viene como "SampleID|SampleNumber|TestType" o "SampleID-SampleNumber-TestType"
    $parts = null;
    if (strpos($incomingId, '|') !== false) {
      $tmp = explode('|', $incomingId);
      if (count($tmp) >= 3) $parts = $tmp;
    } elseif (substr_count($incomingId, '-') >= 2) {
      // ojo: Sample_ID también tiene guiones, por eso tomamos los últimos 2 segmentos como num/test
      $tmp = explode('-', $incomingId);
      if (count($tmp) >= 3) {
        $test = array_pop($tmp);
        $num  = array_pop($tmp);
        $sid  = implode('-', $tmp);
        $parts = [$sid, $num, $test];
      }
    }

    if ($parts) {
      $sidEsc = $db->escape(trim((string)$parts[0]));
      $numEsc = $db->escape(trim((string)$parts[1]));
      $ttEsc  = $db->escape(trim((string)$parts[2]));

      $wf = firstRow($db, "
        SELECT id, Sample_ID, Sample_Number, Test_Type, Status
        FROM test_workflow
        WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$numEsc}' AND Test_Type='{$ttEsc}'
        LIMIT 1
      ");
    }

    // 3B) si es un hash (MD5/SHA1) del key
    if (!$wf) {
      $wf = firstRow($db, "
        SELECT id, Sample_ID, Sample_Number, Test_Type, Status
        FROM test_workflow
        WHERE MD5(CONCAT(Sample_ID,'|',Sample_Number,'|',Test_Type)) = '{$idEsc}'
           OR MD5(CONCAT(Sample_ID,Sample_Number,Test_Type))         = '{$idEsc}'
           OR SHA1(CONCAT(Sample_ID,'|',Sample_Number,'|',Test_Type))= '{$idEsc}'
           OR SHA1(CONCAT(Sample_ID,Sample_Number,Test_Type))        = '{$idEsc}'
        LIMIT 1
      ");
    }

    // 3C) si el hash corresponde a una fila en test_repeat, mapear al workflow real
    if (!$wf) {
      $tr = firstRow($db, "
        SELECT id, Sample_ID, Sample_Number, Test_Type, Status
        FROM test_repeat
        WHERE MD5(CONCAT(Sample_ID,'|',Sample_Number,'|',Test_Type)) = '{$idEsc}'
           OR MD5(CONCAT(Sample_ID,Sample_Number,Test_Type))         = '{$idEsc}'
           OR SHA1(CONCAT(Sample_ID,'|',Sample_Number,'|',Test_Type))= '{$idEsc}'
           OR SHA1(CONCAT(Sample_ID,Sample_Number,Test_Type))        = '{$idEsc}'
        LIMIT 1
      ");

      if ($tr) {
        $sidEsc = $db->escape(trim((string)$tr['Sample_ID']));
        $numEsc = $db->escape(trim((string)$tr['Sample_Number']));
        $ttEsc  = $db->escape(trim((string)$tr['Test_Type']));

        $wf = firstRow($db, "
          SELECT id, Sample_ID, Sample_Number, Test_Type, Status
          FROM test_workflow
          WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$numEsc}' AND Test_Type='{$ttEsc}'
          LIMIT 1
        ");
      }
    }
  }

  if (!$wf) {
    respond(false, [
      'error' => 'Tarjeta no encontrada',
      'received_id' => $incomingId
    ]);
  }

  $status = (string)$wf['Status'];

  if (!isset($VALID[$status]) || !in_array($subStage, $VALID[$status], true)) {
    respond(false, ['error' => 'Sub-etapa no válida para este estado']);
  }

  $user = function_exists('current_user')
    ? (current_user()['name'] ?? current_user()['username'] ?? 'system')
    : 'system';

  $wfIdEsc   = $db->escape((string)$wf['id']);
  $stageEsc  = $db->escape($subStage);
  $userEsc   = $db->escape((string)$user);

  $db->query("
    UPDATE test_workflow
    SET `Sub_Stage`='{$stageEsc}',
        Updated_By='{$userEsc}',
        Updated_At=NOW()
    WHERE id='{$wfIdEsc}'
    LIMIT 1
  ");

  respond(true);

} catch (Throwable $e) {
  http_response_code(400);
  respond(false, ['error' => $e->getMessage()]);
}
