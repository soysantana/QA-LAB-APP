<?php
require_once('../config/load.php');
page_require_level(3);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  die('Método no permitido');
}

// Leer y normalizar
$sample_id     = isset($_POST['sample_id']) ? trim($_POST['sample_id']) : '';
$sample_number = isset($_POST['sample_number']) ? trim($_POST['sample_number']) : '';
$dias          = isset($_POST['dias']) ? (int)$_POST['dias'] : 0;
$estado_raw    = isset($_POST['estado']) ? trim($_POST['estado']) : '';

// Validación básica
if ($sample_id === '' || $sample_number === '' || !in_array($dias, [3,7,14,28], true)) {
  http_response_code(400);
  die('Parámetros inválidos');
}

// Normalizar estado a valores esperados
$estado = strtolower($estado_raw);
if ($estado === 'realizado') {
  $estado = 'realizado';
} elseif ($estado === 'no solicitado' || $estado === 'no_solicitado') {
  $estado = 'no solicitado';
} else {
  // fallback corto
  $estado = substr($estado, 0, 50);
}

// Escapar para MySqli_DB
$sample_id_esc     = $db->escape($sample_id);
$sample_number_esc = $db->escape($sample_number);
$estado_esc        = $db->escape($estado);

// Insert/Update con clave compuesta (requiere índice único)
$sql = "
  INSERT INTO estado_ensayo_concreto (sample_id, sample_number, dias, estado, updated_at)
  VALUES ('{$sample_id_esc}', '{$sample_number_esc}', {$dias}, '{$estado_esc}', NOW())
  ON DUPLICATE KEY UPDATE 
    estado = VALUES(estado),
    updated_at = NOW()
";

$res = $db->query($sql);
if (!$res) {
  http_response_code(500);
  die('Error al guardar estado: ' . $db->error);
}

// Volver a la página anterior o a una por defecto
$back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../pages/control_concreto.php';
header('Location: ' . $back);
exit;
