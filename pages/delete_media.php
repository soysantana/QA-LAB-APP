<?php
require_once('../config/load.php');
page_require_level(3);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  $session->msg("d", "ID inválido.");
  redirect('media.php');
}

$id = (int)$_GET['id'];
$media = find_by_id('media', $id);

if (!$media) {
  $session->msg("d", "Imagen no encontrada.");
  redirect('media.php');
}

// Ruta del archivo a eliminar
$file_path = dirname(__DIR__) . "/uploads/products/" . $media['file_name'];

// Eliminar archivo del sistema
if (file_exists($file_path)) {
  unlink($file_path);
}

// Eliminar registro de la base de datos
$sql = "DELETE FROM media WHERE id = '{$db->escape($id)}'";
$result = $db->query($sql);

if ($result) {
  $session->msg("s", "Imagen eliminada exitosamente.");
} else {
  $session->msg("d", "No se pudo eliminar la imagen.");
}

redirect('../pages/media.php');
