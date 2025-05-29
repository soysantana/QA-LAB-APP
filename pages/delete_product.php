<?php
$page_title = 'Eliminar producto';
require_once('../config/load.php');
page_require_level(3);

// Verificar que se haya enviado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  $session->msg("d", "ID no válido.");
  redirect('../pages/product.php'); // Cambia al nombre real de tu lista de productos
}

$product_id = (int)$_GET['id'];

// Buscar el producto
$product = find_by_id('products', $product_id);
if (!$product) {
  $session->msg("d", "Producto no encontrado.");
  redirect('../pages/product.php')
}

// Eliminar
$delete_result = delete_by_id('products', $product_id);
if ($delete_result) {
  $session->msg("s", "Producto eliminado exitosamente.");
} else {
  $session->msg("d", "No se pudo eliminar el producto.");
}
redirect('../pages/product.php')
?>
