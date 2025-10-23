<?php
// pages/backup_resultados_firmados_purge.php
require_once('../config/load.php');
page_require_level(2);

if (session_status() === PHP_SESSION_NONE) session_start();

$last = $_SESSION['last_backup_signed_results'] ?? null;
$nonce = $_POST['nonce'] ?? '';

if (!$last || !$nonce || !hash_equals($last['nonce'], $nonce)) {
  $_SESSION['flash'] = ['type'=>'warning', 'msg'=>'No hay backup para purgar o el token no es válido.'];
  header('Location: /pages/backup_resultados_firmados.php');
  exit;
}

// Por seguridad: solo borrar dentro de /storage/backups/…
$ROOT = realpath(__DIR__ . '/..');
$BASE = $ROOT . '/storage/backups';
$okZip = isset($last['zip_abs']) && strpos(realpath($last['zip_abs']), realpath($BASE)) === 0;
$okMan = isset($last['manifest_abs']) && is_file($last['manifest_abs']) && strpos(realpath($last['manifest_abs']), realpath($BASE)) === 0;

$errors = [];

if ($okZip && is_file($last['zip_abs'])) {
  if (!@unlink($last['zip_abs'])) $errors[] = 'No se pudo eliminar el ZIP.';
}
if ($okMan) {
  @unlink($last['manifest_abs']); // si falla, no es crítico
}

// Limpiar sesión del último backup
unset($_SESSION['last_backup_signed_results']);

if ($errors) {
  $_SESSION['flash'] = ['type'=>'danger', 'msg'=>implode(' ', $errors)];
} else {
  $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Backup purgado correctamente.'];
}

header('Location: /pages/backup_resultados_firmados.php');
exit;
