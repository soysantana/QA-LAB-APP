<?php
require_once('../config/load.php');
page_require_level(3);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  die('Método no permitido');
}

$sample_id     = isset($_POST['sample_id']) ? trim($_POST['sample_id']) : '';
$sample_number = isset($_POST['sample_number']) ? trim($_POST['sample_number']) : '';
$dias          = isset($_POST['dias']) ? (int)$_POST['dias'] : 0;
$estado_raw    = isset($_POST['estado']) ? trim($_POST['estado']) : '';

$estado = strtolower($estado_raw); // normalizamos en minúsculas
if ($estado === 'realizado') $estado = 'realizado';
elseif ($estado === 'no solicitado') $estado = 'no solicitado';
else {
  // por si viene otro valor no esperado
  $estado = substr($estado, 0, 50);
}

if ($sample_id === '' || $sample_number === '' || !in_array($dias, [3,7,14,28], true)) {
  http_response_code(400);
  die('Parámetros inválidos');
}

// RECOMENDADO: índice único en (sample_id, sample_number, dias)
// Ver SQL más abajo

// Insert/Update con clave compuesta
$stmt = $db->prepare("
  INSERT INTO estado_ensayo_concreto (sample_id, sample_number, dias, estado, updated_at)
  VALUES (?, ?, ?, ?, NOW())
  ON DUPLICATE KEY UPDATE 
    estado = VALUES(estado),
    updated_at = NOW()
");

if (!$stmt) {
  http_response_code(500);
  die('Error de preparación: ' . $db->error);
}

$stmt->bind_param('ssis', $sample_id, $sample_number, $dias, $estado);
$ok = $stmt->execute();

if (!$ok) {
  http_response_code(500);
  die('Error al guardar: ' . $stmt->error);
}

// Redirige de vuelta a la página anterior
$back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../pages/control_concreto.php';
header('Location: ' . $back);
exit;
