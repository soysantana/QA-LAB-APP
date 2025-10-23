<?php
// pages/backup_resultados_firmados_download.php
require_once('../config/load.php');
page_require_level(2);

if (session_status() === PHP_SESSION_NONE) session_start();

// Debe existir un backup reciente en sesión
$last = $_SESSION['last_backup_signed_results'] ?? null;
if (!$last) {
  http_response_code(404);
  header('Content-Type: text/plain; charset=utf-8');
  echo "No hay backup reciente para descargar.";
  exit;
}

// Parámetros
$type = $_GET['type'] ?? 'zip';            // 'zip' | 'manifest'
$filename = trim($_GET['filename'] ?? ''); // nombre sugerido (opcional)

// Sanitizar nombre sugerido
if ($filename !== '') {
  $filename = preg_replace('/[^\w.\-]+/u','_', $filename);
  $filename = trim($filename, '._ ');
}

// Resolver ruta física del archivo a descargar
$ROOT = realpath(__DIR__ . '/..');
$BASE = $ROOT . '/storage/backups';

if ($type === 'manifest') {
  $abs = $last['manifest_abs'] ?? null;
  $defaultName = 'manifest.json';
  $ctype = 'application/json';
} else {
  $type = 'zip';
  $abs = $last['zip_abs'] ?? null;
  $defaultName = basename($last['zip_abs'] ?? 'backup.zip');
  $ctype = 'application/zip';
}

if (!$abs || !is_file($abs)) {
  http_response_code(404);
  header('Content-Type: text/plain; charset=utf-8');
  echo "Archivo no encontrado.";
  exit;
}

// Seguridad: el archivo debe estar dentro de /storage/backups
$realAbs = realpath($abs);
if ($realAbs === false || strpos($realAbs, realpath($BASE)) !== 0) {
  http_response_code(403);
  header('Content-Type: text/plain; charset=utf-8');
  echo "Acceso denegado.";
  exit;
}

// Preparar headers para forzar descarga
$suggest = $filename !== '' ? $filename : $defaultName;
$size = filesize($realAbs);

// Evitar problemas de salida previa
if (function_exists('ob_get_level')) {
  while (ob_get_level() > 0) { @ob_end_clean(); }
}

header('Content-Description: File Transfer');
header('Content-Type: ' . $ctype);
header('Content-Disposition: attachment; filename="' . rawurlencode($suggest) . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $size);
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Enviar el archivo en chunks (memoria segura)
$chunk = 8 * 1024 * 1024; // 8 MB
$fp = fopen($realAbs, 'rb');
if ($fp === false) {
  http_response_code(500);
  header('Content-Type: text/plain; charset=utf-8');
  echo "No se pudo abrir el archivo.";
  exit;
}
while (!feof($fp)) {
  echo fread($fp, $chunk);
  @flush();
}
fclose($fp);
exit;
