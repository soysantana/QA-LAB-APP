<?php
require_once('../config/load.php');
page_require_level(2);

try {
  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) throw new Exception('ID inválido');
  $signed_by = $db->escape($current_user['name'] ?? 'Sistema');
  $sql = "
    UPDATE doc_files
       SET status='signed',
           signed_by='{$signed_by}',
           signed_at=NOW()
     WHERE id={$id}
     LIMIT 1
  ";
  $db->query($sql);
  $session->msg('s','Documento marcado como firmado.');
} catch (Throwable $e) {
  $session->msg('d','Error: '.$e->getMessage());
}
header('Location: /pages/docs_list.php'); exit;
