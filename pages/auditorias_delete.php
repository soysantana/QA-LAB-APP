<?php
require_once "../config/load.php";
page_require_level(2);

global $db;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
  header("Location: ../pages/auditorias_list.php");
  exit;
}

// Opcional: podrías primero verificar que existe
$rows = find_by_sql("SELECT id FROM auditorias_lab WHERE id = {$id} LIMIT 1");
if (!$rows) {
  header("Location: ../pages/auditorias_list.php");
  exit;
}

$sql = "DELETE FROM auditorias_lab WHERE id = {$id} LIMIT 1";
$db->query($sql);

header("Location: ../pages/auditorias_list.php");
exit;
