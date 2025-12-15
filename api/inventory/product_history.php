<?php
@ob_clean();
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/load.php');
page_require_level(3);

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { echo json_encode(["ok"=>false,"data"=>[]]); exit; }

$rows = find_by_sql("
  SELECT movement_type, qty, reason, ref, created_by, created_at
  FROM inventory_movements
  WHERE product_id = {$id}
  ORDER BY created_at DESC
  LIMIT 200
");

echo json_encode(["ok"=>true,"data"=>$rows]);
