<?php
require_once('../../config/load.php');
page_require_level(3);

global $db;

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

// CSRF
$csrf = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
  http_response_code(403);
  exit("CSRF token inválido.");
}

$requisition_id = isset($_POST['requisition_id']) ? (int)$_POST['requisition_id'] : 0;
$return_url     = isset($_POST['return_url']) ? (string)$_POST['return_url'] : '../../pages/inventario_inalterada.php';

// Sanitize return_url (evita open redirect)
if (strpos($return_url, 'http') === 0) {
  $return_url = '../../pages/inventario_inalterada.php';
}

$length  = isset($_POST['Sample_Length']) ? trim((string)$_POST['Sample_Length']) : '';
$weight  = isset($_POST['Sample_Weight']) ? trim((string)$_POST['Sample_Weight']) : '';
$store   = isset($_POST['Store_In'])      ? trim((string)$_POST['Store_In'])      : '';
$comment = isset($_POST['Comment'])       ? trim((string)$_POST['Comment'])       : '';

// Normaliza números (acepta coma)
$length = ($length !== '') ? (float)str_replace(',', '.', $length) : null;
$weight = ($weight !== '') ? (float)str_replace(',', '.', $weight) : null;

// Validar store_in
$allowed_store = ['', 'Stored_PVLab', 'Sended_To'];
if (!in_array($store, $allowed_store, true)) $store = '';

// comment limit
if (mb_strlen($comment) > 255) $comment = mb_substr($comment, 0, 255);

if ($requisition_id <= 0) {
  http_response_code(400);
  exit("ID inválido.");
}

// Verificar requisición existe
$chk = $db->prepare("SELECT id FROM lab_test_requisition_form WHERE id = ? LIMIT 1");
$chk->bind_param("i", $requisition_id);
$chk->execute();
$chkres = $chk->get_result();
if ($chkres->num_rows === 0) {
  $chk->close();
  http_response_code(404);
  exit("Requisición no encontrada.");
}
$chk->close();

// UPSERT (requiere UNIQUE en inalteratedsample.requisition_id)
$sql = "
INSERT INTO inalteratedsample (requisition_id, sample_length, sample_weight, store_in, comment)
VALUES (?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
  sample_length = VALUES(sample_length),
  sample_weight = VALUES(sample_weight),
  store_in      = VALUES(store_in),
  comment       = VALUES(comment)
";

$stmt = $db->prepare($sql);

// bind_param con null: funciona si usas "d" y pasas variable null (mysqli lo acepta).
$stmt->bind_param("iddss", $requisition_id, $length, $weight, $store, $comment);

if (!$stmt->execute()) {
  $stmt->close();
  http_response_code(500);
  exit("DB error: " . $stmt->error);
}
$stmt->close();

// Redirect preservando filtros y mostrando toast
$sep = (strpos($return_url, '?') !== false) ? '&' : '?';
header("Location: {$return_url}{$sep}saved=1");
exit;
