<?php
// /api/kanban_substage.php
declare(strict_types=1);
require_once('../config/load.php');

@ini_set('display_errors','0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

function respond(bool $ok,array $p=[]){
  echo json_encode(['ok'=>$ok]+$p,JSON_UNESCAPED_UNICODE);
  exit;
}

$VALID = [
  'Preparación' => ['P1','P2','P3','P4'],
  'Realización' => ['R1','R2','R3','R4'],
  'Repetición'  => ['RE1','RE2','RE3'],
  'Entrega'     => ['E1']
];

try {
  if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
    http_response_code(405);
    respond(false,['error'=>'Método no permitido']);
  }

  $payload = json_decode(file_get_contents('php://input'), true);
  if (!is_array($payload)) respond(false,['error'=>'Payload inválido']);

  $sub = trim((string)($payload['sub_stage'] ?? ''));
  if ($sub==='') respond(false,['error'=>'Sub-etapa requerida']);

  // 1) SI VIENE LA LLAVE REAL → USARLA (solución definitiva)
  $sid = trim((string)($payload['Sample_ID'] ?? ''));
  $sno = trim((string)($payload['Sample_Number'] ?? ''));
  $tt  = trim((string)($payload['Test_Type'] ?? ''));

  $wf = null;

  if ($sid !== '' && $sno !== '' && $tt !== '') {
    $sidEsc = $db->escape($sid);
    $snoEsc = $db->escape($sno);
    $ttEsc  = $db->escape($tt);

    $wf = $db->fetch_assoc($db->query("
      SELECT id, Status
      FROM test_workflow
      WHERE Sample_ID='{$sidEsc}'
        AND Sample_Number='{$snoEsc}'
        AND Test_Type='{$ttEsc}'
      LIMIT 1
    "));
  }

  // 2) BACKUP: si no vino llave, intentar por id (por si hay tarjetas reales)
  if (!$wf) {
    $id = trim((string)($payload['id'] ?? ''));
    if ($id !== '') {
      $idEsc = $db->escape($id);
      $wf = $db->fetch_assoc($db->query("
        SELECT id, Status
        FROM test_workflow
        WHERE id='{$idEsc}'
        LIMIT 1
      "));
    }
  }

  if (!$wf) respond(false,['error'=>'Tarjeta no encontrada']);

  $status = (string)$wf['Status'];
  if (!isset($VALID[$status]) || !in_array($sub, $VALID[$status], true)) {
    respond(false,['error'=>'Sub-etapa no válida para este estado']);
  }

  $user = function_exists('current_user')
    ? (current_user()['name'] ?? current_user()['username'] ?? 'system')
    : 'system';

  $wfIdEsc = $db->escape((string)$wf['id']);
  $subEsc  = $db->escape($sub);
  $usrEsc  = $db->escape((string)$user);

  $db->query("
    UPDATE test_workflow
    SET `Sub_Stage`='{$subEsc}',
        Updated_By='{$usrEsc}',
        Updated_At=NOW()
    WHERE id='{$wfIdEsc}'
    LIMIT 1
  ");

  respond(true);

} catch(Throwable $e){
  http_response_code(400);
  respond(false,['error'=>$e->getMessage()]);
}
