<?php
// pages/serve_pdf.php — Universal viewer (por id o path) con soporte HEAD/Range
require_once('../config/load.php');
page_require_level(2);

@ini_set('display_errors', 0);
@ini_set('log_errors', 1);
@ini_set('zlib.output_compression', '0');
@ini_set('output_buffering', '0');
@ini_set('expose_php', '0');

$DEBUG = isset($_GET['debug']) && $_GET['debug'] == '1';
if (function_exists('ob_get_level')) { while (ob_get_level()) { @ob_end_clean(); } }

function out_error($code, $msg, $trace = []) {
  http_response_code($code);
  if (!headers_sent()) header('Content-Type: text/plain; charset=utf-8');
  echo $msg;
  if (!empty($trace)) {
    echo "\n\n-- Debug --\n";
    foreach ($trace as $k => $v) {
      if (is_array($v)) echo "$k: " . json_encode($v, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . "\n";
      else echo "$k: $v\n";
    }
  }
  exit;
}
function norm($p){ return str_replace('\\','/', $p); }

// 1) Resolver file_path (id o path)
$stored = ''; $from = '';
if (isset($_GET['id']) && (int)$_GET['id'] > 0) {
  $id  = (int)$_GET['id'];
  $sql = "SELECT file_path FROM doc_files WHERE id={$id} LIMIT 1";
  $res = $db->query($sql);
  $row = $res ? $db->fetch_assoc($res) : null;
  if (!$row || empty($row['file_path'])) out_error(404, "Archivo no encontrado para id={$id}", $DEBUG ? ['sql'=>$sql] : []);
  $stored = (string)$row['file_path']; $from = 'id';
} elseif (!empty($_GET['path'])) {
  $stored = (string)$_GET['path']; $from = 'path';
} else {
  out_error(400, "Falta 'id' o 'path'. Usa ?id=123 o ?path=/uploads/...pdf");
}

// 2) Normaliza
$DOCROOT       = realpath($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2));
$DOCROOT_PARENT= $DOCROOT ? dirname($DOCROOT) : null;
$PROJECT_ROOT  = realpath(dirname(__DIR__, 1));

$norm = norm(trim($stored));
$norm = preg_replace('#/+#','/', $norm);
$norm = rtrim($norm, " \t\n\r\0\x0B+");
if ($norm !== '' && $norm[0] === '/') $norm = ltrim($norm,'/');

// prefijo lógico
$prefix = '';
if (stripos($norm, 'uploads/results/') === 0)             $prefix = 'uploads/results';
elseif (stripos($norm, 'storage/pdfs/signed/') === 0)      $prefix = 'storage/pdfs/signed';
elseif (stripos($norm, 'storage/pdfs/') === 0)             $prefix = 'storage/pdfs';

// subpath sin prefijo
$subpath = $norm;
foreach (['uploads/results/', 'storage/pdfs/signed/', 'storage/pdfs/'] as $p) {
  if (stripos($norm, $p) === 0) { $subpath = substr($norm, strlen($p)); break; }
}

// join helper
$join = function() {
  $parts = func_get_args();
  $p = join(DIRECTORY_SEPARATOR, $parts);
  return preg_replace('#'.preg_quote(DIRECTORY_SEPARATOR,'#').'+#', DIRECTORY_SEPARATOR, $p);
};

// bases
$doc_uploads_results = $DOCROOT       ? $join($DOCROOT,       'uploads', 'results')        : null;
$doc_storage_pdfs    = $DOCROOT       ? $join($DOCROOT,       'storage', 'pdfs')           : null;
$doc_storage_signed  = $DOCROOT       ? $join($DOCROOT,       'storage', 'pdfs', 'signed') : null;

$docp_uploads_results= $DOCROOT_PARENT? $join($DOCROOT_PARENT,'uploads', 'results')        : null;
$docp_storage_pdfs   = $DOCROOT_PARENT? $join($DOCROOT_PARENT,'storage', 'pdfs')           : null;
$docp_storage_signed = $DOCROOT_PARENT? $join($DOCROOT_PARENT,'storage', 'pdfs', 'signed') : null;

$prj_uploads_results = $PROJECT_ROOT  ? $join($PROJECT_ROOT,  'uploads', 'results')        : null;
$prj_storage_pdfs    = $PROJECT_ROOT  ? $join($PROJECT_ROOT,  'storage', 'pdfs')           : null;
$prj_storage_signed  = $PROJECT_ROOT  ? $join($PROJECT_ROOT,  'storage', 'pdfs', 'signed') : null;

// candidatos
$candidates = []; $checked = [];

// ruta absoluta literal
if (preg_match('#^([A-Za-z]:/|/)#', $stored)) {
  $candidates[] = $stored;
  $candidates[] = norm($stored);
  $candidates[] = str_replace('/','\\',$stored);
}

// según prefijo
switch ($prefix) {
  case 'uploads/results':
    $trySets = [
      [$doc_uploads_results,$subpath],[$docp_uploads_results,$subpath],[$prj_uploads_results,$subpath]
    ]; break;
  case 'storage/pdfs/signed':
    $trySets = [
      [$doc_storage_signed,$subpath],[$docp_storage_signed,$subpath],[$prj_storage_signed,$subpath]
    ]; break;
  case 'storage/pdfs':
    $trySets = [
      [$doc_storage_pdfs,$subpath],[$docp_storage_pdfs,$subpath],[$prj_storage_pdfs,$subpath]
    ]; break;
  default:
    $trySets = [
      [$doc_uploads_results,$subpath],[$docp_uploads_results,$subpath],[$prj_uploads_results,$subpath],
      [$doc_storage_signed,$subpath], [$docp_storage_signed,$subpath], [$prj_storage_signed,$subpath],
      [$doc_storage_pdfs,$subpath],   [$docp_storage_pdfs,$subpath],   [$prj_storage_pdfs,$subpath],
    ];
}
foreach ($trySets as $pair) {
  list($base, $rel) = $pair; if (!$base) continue;
  $candidates[] = $join($base, str_replace('/', DIRECTORY_SEPARATOR, $rel));
}

// fallback directo
if ($PROJECT_ROOT) {
  $candidates[] = $PROJECT_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $norm);
}

$resolved = null;
foreach ($candidates as $cand) {
  $rp = @realpath($cand);
  $checked[] = ['candidate'=>$cand, 'realpath'=>$rp];
  if ($rp && is_file($rp) && is_readable($rp)) { $resolved = $rp; break; }
}
if (!$resolved) {
  if ($DEBUG) out_error(404, "Archivo no encontrado", [
    'from'=>$from,'param'=>$stored,'normalized'=>$norm,'prefix'=>$prefix,'subpath'=>$subpath,
    'DOCROOT'=>$DOCROOT,'DOCROOT_PARENT'=>$DOCROOT_PARENT,'PROJECT_ROOT'=>$PROJECT_ROOT,'checked'=>$checked
  ]);
  out_error(404, "Archivo no encontrado");
}

// seguridad
$allowedRoots = array_values(array_filter([
  $doc_uploads_results, $docp_uploads_results, $prj_uploads_results,
  $doc_storage_pdfs,    $docp_storage_pdfs,    $prj_storage_pdfs,
  $doc_storage_signed,  $docp_storage_signed,  $prj_storage_signed,
]));
$allowed=false;
foreach ($allowedRoots as $root) {
  if ($root) {
    $rootReal = realpath($root);
    if ($rootReal && strpos($resolved, $rootReal) === 0) { $allowed = true; break; }
  }
}
if (!$allowed) {
  if ($DEBUG) out_error(403, "Acceso denegado", ['resolved'=>$resolved,'allowedRoots'=>$allowedRoots]);
  out_error(403, "Acceso denegado");
}

// servir
if (function_exists('ob_get_level')) { while (ob_get_level()) { @ob_end_clean(); } }
@header_remove('X-Powered-By');

$filesize = @filesize($resolved);
if ($filesize === false) out_error(500, "Error interno (filesize)");
$filename = basename($resolved);

if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
  header('Content-Type: application/pdf');
  header('Content-Disposition: inline; filename="'.$filename.'"');
  header('Accept-Ranges: bytes');
  header('Content-Length: ' . $filesize);
  header('Cache-Control: private, max-age=0, must-revalidate');
  header('Pragma: public');
  header('X-Accel-Buffering: no');
  exit;
}

$range = null;
if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/i', $_SERVER['HTTP_RANGE'], $m)) {
  $start = $m[1] === '' ? null : (int)$m[1];
  $end   = $m[2] === '' ? null : (int)$m[2];
  if ($start === null && $end !== null) { $start = max(0, $filesize - $end); $end = $filesize - 1; }
  else { if ($start === null) $start = 0; if ($end === null || $end >= $filesize) $end = $filesize - 1; }
  if ($start <= $end && $start < $filesize) $range = [$start, $end];
}

$fp = @fopen($resolved, 'rb');
if (!$fp) out_error(500, "Error interno (fopen)");

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.$filename.'"');
header('Accept-Ranges: bytes');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
header('X-Accel-Buffering: no');

if ($range) {
  list($start, $end) = $range; $length = $end - $start + 1;
  http_response_code(206);
  header("Content-Range: bytes $start-$end/$filesize");
  header("Content-Length: $length");
  fseek($fp, $start);
  $remaining = $length; $chunk = 8192;
  while ($remaining > 0 && !feof($fp)) {
    $read = ($remaining > $chunk) ? $chunk : $remaining;
    $buf = fread($fp, $read);
    if ($buf === false) break;
    echo $buf; $remaining -= strlen($buf);
  }
} else {
  header('Content-Length: ' . $filesize);
  $chunk = 8192;
  while (!feof($fp)) {
    $buf = fread($fp, $chunk);
    if ($buf === false) break;
    echo $buf;
  }
}
@fclose($fp);
exit;
