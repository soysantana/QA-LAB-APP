<?php
require_once('../../config/load.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$table = $db->escape($data['table'] ?? '');
$criteria = $data['criteria'] ?? [];

if (!$table || empty($criteria)) {
    echo json_encode([]);
    exit;
}

// Construir la cláusula WHERE
$where = [];
foreach ($criteria as $key => $value) {
    $where[] = "{$key} = '" . $db->escape($value) . "'";
}
$whereSQL = implode(' AND ', $where);

$sql = "SELECT * FROM {$table} WHERE {$whereSQL} LIMIT 1";
$result = $db->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode($row ?? []);
} else {
    echo json_encode([]);
}
