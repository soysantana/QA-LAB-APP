<?php
declare(strict_types=1);
require_once('../config/load.php');

header('Content-Type: application/json; charset=utf-8');

function respond(bool $ok, array $p=[]){
  echo json_encode(['ok'=>$ok] + $p, JSON_UNESCAPED_UNICODE);
  exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$id      = trim((string)($payload['id'] ?? ''));
$stage   = trim((string)($payload['sub_stage'] ?? ''));

if ($id==='' || $stage==='') respond(false, ['error'=>'Datos insuficientes', 'payload'=>$payload]);

$idEsc = $db->escape($id);

// buscar en workflow
$wf = $db->fetch_assoc($db->query("SELECT id, Sample_ID, Sample_Number, Test_Type, Status FROM test_workflow WHERE id='{$idEsc}' LIMIT 1"));

// buscar en repeat
$tr = $db->fetch_assoc($db->query("SELECT id, Sample_ID, Sample_Number, Test_Type, Status FROM test_repeat WHERE id='{$idEsc}' LIMIT 1"));

respond(false, [
  'error' => 'DEBUG: ID recibido no actualiza todavía',
  'received_id' => $id,
  'received_stage' => $stage,
  'found_in_test_workflow' => $wf ?: null,
  'found_in_test_repeat'   => $tr ?: null
]);
