<?php
// pages/serve_pdf.php — Universal viewer (por id o path)
require_once('../config/load.php');
page_require_level(2);

// --- Ajustes de salida / hosting ---
@ini_set('display_errors', 0); // en prod: oculto (usa ?debug=1 para ver detalles)
@ini_set('log_errors', 1);
@ini_set('zlib.output_compression', '0'); // evita gzip que daña binarios
@ini_set('output_buffering', '0');
@ini_set('expose_php', '0');

$DEBUG = isset($_GET['debug']) && $_GET['debug'] == '1';

// Limpia cualquier salida previa (incl. BOM o echo de includes)
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

$DOCROOT      = realpath(isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 2));
$PROJECT_ROOT = realpath(dirname(__DIR__, 1));

// 1) Obtener ruta guardada: por id o por path
$stored = '';
$from   = '';
if (isset($_GET['id']) && (int)$_GET['id'] > 0) {
  $id  = (int)$_GET['id'];
  $sql = "SELECT file_path FROM doc_files WHERE id={$id} LIMIT 1";
  $res = $db->query($sql);
  $row = $res ? $db->fetch_assoc($res) : null;
  if (!$row || empty($row['file_path'])) out_error(404, "Archivo no encontrado para id={$id}", $DEBUG ? ['sql'=>$sql] : []);
  $stored = (string)$row['file_path'];
  $from   = 'id';
} elseif (!empty($_GET['path'])) {
  $stored = (string)$_GET['path'];
  $from   = 'path';
} else {
  out_error(400, "Falta parámetro 'id' o 'path'. Usa serve_pdf.php?id=123 o serve_pdf.php?path=...pdf");
}

// 2) Normalizar
$norm = str_replace('\\','/',$stored);
$norm = preg_replace('#/+#','/',$norm);
$norm = ltrim($norm,'/'); // quitar / inicial

// 3) Detectar prefijo
$prefix = '';
if (stripos($norm, 'uploads/results/') === 0)             $prefix = 'uploads/results';
elseif (stripos($norm, 'results/') === 0)                  $prefix = 'uploads/results';
elseif (stripos($norm, 'storage/pdfs/signed/') === 0)      $prefix = 'storage/pdfs/signed';
elseif (stripos($norm, 'storage/pdfs/') === 0)             $prefix = 'storage/pdfs';
elseif (stripos($norm, 'signed/') === 0)                   $prefix = 'storage/pdfs/signed';

// Subruta sin prefijo
$subpath = $norm;
foreach (['uploads/results/', 'results/', 'storage/pdfs/signed/', 'storage/pdfs/', 'signed/'] as $p) {
  if (stripos($norm, $p) === 0) { $subpath = substr($norm, strlen($p)); break; }
}

// Helper join
$join = function() {
  $parts = func_get_args();
  $p = join(DIRECTORY_SEPARATOR, $parts);
  return preg_replace('#'.preg_quote(DIRECTORY_SEPARATOR,'#').'+#', DIRECTORY_SEPARATOR, $p);
};

// Bases conocidas
$doc_uploads_results = $DOCROOT      ? $join($DOCROOT, 'uploads', 'results')            : null;
$doc_storage_pdfs    = $DOCROOT      ? $join($DOCROOT, 'storage', 'pdfs')               : null;
$doc_storage_signed  = $DOCROOT      ? $join($DOCROOT, 'storage', 'pdfs', 'signed')     : null;

$prj_uploads_results = $PROJECT_ROOT ? $join($PROJECT_ROOT, 'uploads', 'results')       : null;
$prj_storage_pdfs    = $PROJECT_ROOT ? $join($PROJECT_ROOT, 'storage', 'pdfs')          : null;
$prj_storage_signed  = $PROJECT_ROOT ? $join($PROJECT_ROOT, 'storage', 'pdfs', 'signed'): null;

$candidates = [];
$checked    = [];

// Si vino ruta absoluta, probar tal cual
if (preg_match('#^([A-Za-z]:/|/)#', $stored)) {
  $candidates[] = $stored;
  $candidates[] = $norm;
  $candidates[] = str_replace('/','\\',$stored);
}

// Listas de búsqueda según prefijo
switch ($prefix) {
  case 'uploads/results':
    $trySets = [[$doc_uploads_results,$subpath],[$prj_uploads_results,$subpath]];
    break;
  case 'storage/pdfs/signed':
    $trySets = [[$doc_storage_signed,$subpath],[$prj_storage_signed,$subpath]];
    break;
  case 'storage/pdfs':
    $trySets = [[$doc_storage_pdfs,$subpath],[$prj_storage_pdfs,$subpath]];
    break;
  default:
    $trySets = [
      [$doc_uploads_results,$subpath],[$prj_uploads_results,$subpath],
      [$doc_storage_signed,$subpath], [$prj_storage_signed,$subpath],
      [$doc_storage_pdfs,$subpath],   [$prj_storage_pdfs,$subpath],
    ];
}

foreach ($trySets as $pair) {
  list($base, $rel) = $pair;
  if (!$base) continue;
  $candidates[] = $join($base, str_replace('/', DIRECTORY_SEPARATOR, $rel));
}

// 4) Resolver en disco
$resolved = null;
foreach ($candidates as $cand) {
  $rp = @realpath($cand);
  $checked[] = ['candidate'=>$cand, 'realpath'=>$rp];
  if ($rp && is_file($rp) && is_readable($rp)) { $resolved = $rp; break; }
}

if (!$resolved) {
  $hint = "No se halló el PDF. Revisa que exista en /uploads/results, /storage/pdfs o /storage/pdfs/signed. Param: {$stored}";
  if ($DEBUG) out_error(404, "Archivo no encontrado", [
    'from'=>$from,'param'=>$stored,'normalized'=>$norm,'prefix'=>$prefix,'subpath'=>$subpath,
    'DOCROOT'=>$DOCROOT,'PROJECT_ROOT'=>$PROJECT_ROOT,'checked'=>$checked,'hint'=>$hint
  ]);
  out_error(404, "Archivo no encontrado");
}

// 5) Seguridad: limitar a zonas permitidas (compatible PHP 7.x)
$allowedRoots = array_values(array_filter([
  $doc_uploads_results, $prj_uploads_results,
  $doc_storage_pdfs,    $prj_storage_pdfs,
  $doc_storage_signed,  $prj_storage_signed,
]));
$allowed = false;
foreach ($allowedRoots as $root) {
  if ($root) {
    $rootReal = realpath($root);
    if ($rootReal && strpos($resolved, $rootReal) === 0) { $allowed = true; break; }
  }
}
if (!$allowed) {
  if ($DEBUG) out_error(403, "Acceso denegado (fuera de rutas permitidas)", ['resolved'=>$resolved,'allowedRoots'=>$allowedRoots]);
  out_error(403, "Acceso denegado");
}

// --- Servir PDF inline de forma segura ---
if (function_exists('ob_get_level')) { while (ob_get_level()) { @ob_end_clean(); } }
@header_remove('X-Powered-By');

$filesize = @filesize($resolved);
if ($filesize === false) {
  if ($DEBUG) out_error(500, "No se pudo leer tamaño del archivo", ['resolved'=>$resolved]);
  out_error(500, "Error interno");
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.basename($resolved).'"');
header('Content-Length: ' . $filesize);
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
// Para nginx/proxies
header('X-Accel-Buffering: no');

$fp = @fopen($resolved, 'rb');
if (!$fp) {
  if ($DEBUG) out_error(500, "No se pudo abrir el archivo", ['resolved'=>$resolved]);
  out_error(500, "Error interno");
}
fpassthru($fp);
@fclose($fp);
exit;
