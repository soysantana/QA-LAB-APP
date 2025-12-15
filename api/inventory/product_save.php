<?php
@ob_clean();
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/load.php');
page_require_level(3);

function j($ok, $msg = '', $extra = []) {
  echo json_encode(array_merge(["ok"=>$ok, "msg"=>$msg], $extra));
  exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) j(false, "ID inválido");

$name  = $db->escape(trim($_POST['name'] ?? ''));
$mm    = $db->escape(trim($_POST['Marca_Modelo'] ?? ''));
$code  = $db->escape(trim($_POST['Codigo'] ?? ''));
$stat  = $db->escape(trim($_POST['Status'] ?? 'Disponible'));
$qty   = (float)($_POST['quantity'] ?? 0);
$price = (float)($_POST['buy_price'] ?? 0);
$catId = (int)($_POST['categorie_id'] ?? 0);

if ($name === '') j(false, "Nombre requerido");

$sql = "
  UPDATE products SET
    name='{$name}',
    Marca_Modelo='{$mm}',
    Codigo='{$code}',
    Status='{$stat}',
    quantity='{$qty}',
    buy_price='{$price}',
    categorie_id='{$catId}'
  WHERE id='{$id}'
  LIMIT 1
";

if ($db->query($sql)) j(true, "Actualizado");
j(false, "No se pudo actualizar");
