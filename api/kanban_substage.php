<?php
// /api/kanban_substage.php
declare(strict_types=1);
require_once('../config/load.php');

// Evitar que notices/errores rompan el JSON
@ini_set('display_errors', '0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

function respond(bool $ok, array $payload = []): void {
  echo json_encode(['ok' => $ok] + $payload, JSON_UNESCAPED_UNICODE);
  exit;
}

// Sub-etapas válidas por estado
$VALID = [
  'Preparación' => ['P1','P2','P3','P4'],
  'Realización' => ['R1','R2','R3','R4'],
  'Entrega'     => ['E1'],
];

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

  $id    = isset($payload['id'])        ? trim((string)$payload['id'])        : '';
  $stage = isset($payload['sub_stage']) ? trim((string)$payload['sub_stage']) : '';

  if ($id === '' || $stage === '') {
    respond(false, ['error' => 'Datos insuficientes']);
  }

  $idEsc    = $db->escape($id);
  $stageEsc = $db->escape($stage);

  // Leer estado actual de la tarjeta
  $row = $db->fetch_assoc(
    $db->query("SELECT Status FROM test_workflow WHERE id='{$idEsc}' LIMIT 1")
  );
  if (!$row) {
    respond(false, ['error' => 'Tarjeta no encontrada']);
  }

  $status = $row['Status'];

  // Validar que la sub-etapa aplique a ese estado
  if (!isset($VALID[$status]) || !in_array($stage, $VALID[$status], true)) {
    respond(false, ['error' => 'Sub-etapa no válida para este estado']);
  }

  // Usuario que cambia la sub-etapa
  $user = function_exists('current_user')
    ? (current_user()['name'] ?? current_user()['username'] ?? 'system')
    : 'system';

  // Actualizar sub_stage y Updated_By
  $sql = sprintf(
    "UPDATE test_workflow
     SET sub_stage='%s', Updated_By='%s', Updated_At=NOW()
     WHERE id='%s' LIMIT 1",
    $stageEsc,
    $db->escape($user),
    $idEsc
  );
  $db->query($sql);

  respond(true);

} catch (Throwable $e) {
  http_response_code(400);
  respond(false, ['error' => $e->getMessage()]);
}
