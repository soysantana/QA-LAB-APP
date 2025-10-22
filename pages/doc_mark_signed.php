<?php
require_once('../config/load.php'); page_require_level(2);
$id = (int)($_POST['id'] ?? 0);
if ($id>0) {
  $user = $db->escape(current_user()['name'] ?? 'user');
  $db->query("UPDATE doc_files SET status='signed', signed_by='{$user}', signed_at=NOW() WHERE id={$id} LIMIT 1");
  $session->msg('s','Documento marcado como firmado.');
}
header('Location: /pages/docs_list.php'); exit;
