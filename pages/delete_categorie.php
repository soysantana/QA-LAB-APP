<?php
$page_title = 'Eliminar Categoría';
require_once('../config/load.php');
page_require_level(3);

// Validar el ID recibido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  $session->msg("d", "ID inválido.");
  redirect('../pages/categories.php');
}

$id = (int)$_GET['id'];

// Verificar que la categoría exista
$categorie = find_by_id('categories', $id);
if (!$categorie) {
  $session->msg("d", "La categoría no existe.");
   redirect('../pages/categories.php');
}

// Eliminar la categoría
if (delete_by_id('categories', $id)) {
  $session->msg("s", "✅ Categoría eliminada exitosamente.");
} else {
  $session->msg("d", "❌ No se pudo eliminar la categoría.");
}

 redirect('../pages/categories.php');
