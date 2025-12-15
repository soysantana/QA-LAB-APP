<?php
@ob_clean();
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/load.php');
page_require_level(3);

function j($ok, $msg = '', $extra = []) {
  echo json_encode(array_merge(["ok"=>$ok, "msg"=>$msg], $extra));
  exit;
}

$user = current_user();
$by = $db->escape($user['name'] ?? 'system');

$product_id = (int)($_POST['product_id'] ?? 0);
$type = strtoupper(trim($_POST['movement_type'] ?? ''));
$qty  = (float)($_POST['qty'] ?? 0);
$reason = $db->escape(trim($_POST['reason'] ?? ''));
$ref    = $db->escape(trim($_POST['ref'] ?? ''));

if ($product_id <= 0) j(false, "Producto inválido");
if (!in_array($type, ['IN','OUT','ADJUST'], true)) j(false, "Tipo inválido");
if ($qty <= 0) j(false, "Cantidad inválida");

$prod = find_by_sql("SELECT id, quantity FROM products WHERE id={$product_id} LIMIT 1");
if (empty($prod)) j(false, "Producto no existe");

$current = (float)$prod[0]['quantity'];
$newQty = $current;

if ($type === 'IN') $newQty = $current + $qty;
if ($type === 'OUT') $newQty = $current - $qty;
if ($type === 'ADJUST') $newQty = $qty; // ajuste fija el stock

if ($newQty < 0) j(false, "No puede quedar stock negativo (actual: {$current})");

$moveId = md5(uniqid((string)$product_id, true));

$db->query("START TRANSACTION");

$ok1 = $db->query("
  INSERT INTO inventory_movements (id, product_id, movement_type, qty, reason, ref, created_by)
  VALUES ('{$moveId}', {$product_id}, '{$type}', '{$qty}', '{$reason}', '{$ref}', '{$by}')
");

$ok2 = $db->query("UPDATE products SET quantity='{$newQty}' WHERE id={$product_id} LIMIT 1");

if ($ok1 && $ok2) {
  $db->query("COMMIT");
  j(true, "Movimiento registrado", ["new_quantity"=>$newQty]);
}

$db->query("ROLLBACK");
j(false, "No se pudo registrar el movimiento");
