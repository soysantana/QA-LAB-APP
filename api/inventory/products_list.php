<?php
@ob_clean();
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/load.php');
page_require_level(3);

function j($arr){ echo json_encode($arr); exit; }

$draw   = (int)($_GET['draw'] ?? 1);
$start  = (int)($_GET['start'] ?? 0);
$length = (int)($_GET['length'] ?? 10);
$search = trim($_GET['search']['value'] ?? '');

$status   = trim($_GET['status'] ?? '');
$category = trim($_GET['category'] ?? '');

$cols = [
  0 => 'p.id',
  1 => 'p.name',
  2 => 'p.Marca_Modelo',
  3 => 'p.Codigo',
  4 => 'p.Status',
  5 => 'c.name',
  6 => 'p.quantity',
  7 => 'p.buy_price',
  8 => 'p.date'
];

$orderCol = (int)($_GET['order'][0]['column'] ?? 8);
$orderDir = strtolower($_GET['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$orderBy  = $cols[$orderCol] ?? 'p.date';

$where = [];
$where[] = "1=1";

if ($search !== '') {
  $s = $db->escape($search);
  $where[] = "(
      p.name LIKE '%{$s}%'
   OR p.Marca_Modelo LIKE '%{$s}%'
   OR p.Codigo LIKE '%{$s}%'
   OR p.Status LIKE '%{$s}%'
   OR c.name LIKE '%{$s}%'
  )";
}

if ($status !== '') {
  $st = $db->escape($status);
  $where[] = "p.Status = '{$st}'";
}

if ($category !== '') {
  $cat = (int)$category;
  $where[] = "p.categorie_id = {$cat}";
}

$whereSql = implode(" AND ", $where);

// Total sin filtros
$totalRow = find_by_sql("SELECT COUNT(*) total FROM products");
$recordsTotal = (int)($totalRow[0]['total'] ?? 0);

// Total con filtros
$filteredRow = find_by_sql("
  SELECT COUNT(*) total
  FROM products p
  LEFT JOIN categories c ON c.id = p.categorie_id
  WHERE {$whereSql}
");
$recordsFiltered = (int)($filteredRow[0]['total'] ?? 0);

// Data paginada
$data = find_by_sql("
  SELECT
    p.id,
    p.name,
    p.Marca_Modelo,
    p.Codigo,
    p.Status,
    p.quantity,
    p.buy_price,
    p.date,
    p.media_id,
    m.file_name AS image,
    c.name AS categorie
  FROM products p
  LEFT JOIN categories c ON c.id = p.categorie_id
  LEFT JOIN media m ON m.id = p.media_id
  WHERE {$whereSql}
  ORDER BY {$orderBy} {$orderDir}
  LIMIT {$start}, {$length}
");

j([
  "draw" => $draw,
  "recordsTotal" => $recordsTotal,
  "recordsFiltered" => $recordsFiltered,
  "data" => $data
]);
