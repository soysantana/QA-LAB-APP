<?php
// /api/kanban_substage.php
declare(strict_types=1);
require_once('../config/load.php');

@ini_set('display_errors','0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

function respond(bool $ok, array $p = []) {
  echo json_encode(['ok'=>$ok] + $p, JSON_UNESCAPED_UNICODE);
  exit;
}

$VALID = [
  'Preparación' => ['P1','P2','P3','P4'],
  'Realización' => ['R1','R2','R3','R4'],
  'Repetición'  => ['RE1','RE2','RE3'],
  'Entrega'     => ['E1']
];

try {
  if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    respond(false, ['error' => 'Método no permitido']);
  }

  $payload = json_decode(file_get_contents('php://input'), true);
  if (!is_array($payload)) respond(false, ['error'=>'Payload inválido']);

  // Tu JS envía: { id, sub_stage }
  $id    = trim((string)($payload['id'] ?? ''));
  $stage = trim((string)($payload['sub_stage'] ?? ''));

  if ($id === '' || $stage === '') respond(false, ['error'=>'Datos insuficientes']);

  $idEsc    = $db->escape($id);
  $stageEsc = $db->escape($stage);

  // Usuario
  $user = function_exists('current_user')
    ? (current_user()['name'] ?? current_user()['username'] ?? 'system')
    : 'system';
  $userEsc = $db->escape((string)$user);

  /**
   * 1) INTENTAR DIRECTO EN test_workflow (caso normal)
   */
  $wf = $db->fetch_assoc($db->query("
    SELECT id, Status
    FROM test_workflow
    WHERE id='{$idEsc}'
    LIMIT 1
  "));

  /**
   * 2) SI NO EXISTE, PROBAR EN test_repeat (id viene de repetición)
   *    y mapear a la tarjeta real del workflow por (Sample_ID, Sample_Number, Test_Type)
   */
  if (!$wf) {
    $tr = $db->fetch_assoc($db->query("
      SELECT Sample_ID, Sample_Number, Test_Type
      FROM test_repeat
      WHERE id='{$idEsc}'
      LIMIT 1
    "));

    if ($tr) {
      $sid = $db->escape(trim((string)$tr['Sample_ID']));
      $sno = $db->escape(trim((string)$tr['Sample_Number']));
      $tt  = $db->escape(trim((string)$tr['Test_Type']));

      $wf = $db->fetch_assoc($db->query("
        SELECT id, Status
        FROM test_workflow
        WHERE Sample_ID='{$sid}'
          AND Sample_Number='{$sno}'
          AND Test_Type='{$tt}'
        LIMIT 1
      "));
    }
  }

  if (!$wf) {
    respond(false, ['error' => 'Tarjeta no encontrada']);
  }

  $status = (string)$wf['Status'];

  // Validación de sub-etapa según el Status real en workflow
  if (!isset($VALID[$status]) || !in_array($stage, $VALID[$status], true)) {
    respond(false, ['error' => 'Sub-etapa no válida para este estado']);
  }

  $wfIdEsc = $db->escape((string)$wf['id']);

  // Actualizar Sub_Stage (nota: uso backticks por seguridad)
  $sql = "
    UPDATE test_workflow
    SET `Sub_Stage`='{$stageEsc}',
        Updated_By='{$userEsc}',
        Updated_At=NOW()
    WHERE id='{$wfIdEsc}'
    LIMIT 1
  ";
  $db->query($sql);

  respond(true);

} catch (Throwable $e) {
  http_response_code(400);
  respond(false, ['error' => $e->getMessage()]);
}
