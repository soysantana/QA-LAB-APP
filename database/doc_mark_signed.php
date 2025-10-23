<?php
require_once('../config/load.php');
page_require_level(2);

try {
  if (session_status() === PHP_SESSION_NONE) session_start();

  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) throw new Exception('ID inválido');

  // === Resolver firmante ===
  $signed_by_val = null;

  // 1) $current_user
  if (isset($current_user) && is_array($current_user)) {
    $signed_by_val = $current_user['name'] ?? $current_user['username'] ?? null;
  }

  // 2) $user (por si tu framework lo expone)
  if (!$signed_by_val && isset($user) && is_array($user)) {
    $signed_by_val = $user['name'] ?? $user['username'] ?? null;
  }

  // 3) $_SESSION['user'] común
  if (!$signed_by_val && isset($_SESSION['user'])) {
    if (is_array($_SESSION['user'])) {
      $signed_by_val = $_SESSION['user']['name'] ?? $_SESSION['user']['username'] ?? null;
    } elseif (is_string($_SESSION['user'])) {
      $signed_by_val = $_SESSION['user']; // ej. usuario en texto
    }
  }

  // 4) Lookup por user_id en tabla users
  if (!$signed_by_val && !empty($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    if ($uid > 0) {
      $urow = find_by_sql("SELECT name, username FROM users WHERE id={$uid} LIMIT 1");
      if ($urow && (isset($urow[0]['name']) || isset($urow[0]['username']))) {
        $signed_by_val = $urow[0]['name'] ?: $urow[0]['username'];
      }
    }
  }

  // 5) Último recurso
  if (!$signed_by_val) $signed_by_val = 'Sistema';

  // Sanitiza y acota a longitud razonable (ajusta a tu columna)
  $signed_by_val = trim((string)$signed_by_val);
  if ($signed_by_val === '') $signed_by_val = 'Sistema';
  $signed_by_val = mb_substr($signed_by_val, 0, 100, 'UTF-8');

  $signed_by = $db->escape($signed_by_val);

  // === Actualiza como firmado ===
  $sql = "
    UPDATE doc_files
       SET status    = 'signed',
           signed_by = '{$signed_by}',
           signed_at = NOW()
     WHERE id = {$id}
     LIMIT 1
  ";
  $db->query($sql);

  $session->msg('s','Documento marcado como firmado por: '.$signed_by_val);

} catch (Throwable $e) {
  $session->msg('d','Error: '.$e->getMessage());
}

header('Location: /pages/docs_list.php');
exit;
