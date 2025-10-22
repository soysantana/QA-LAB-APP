<?php
// pages/serve_pdf.php — Universal (id o path). Soporta:
// - uploads/results/<...>.pdf
// - storage/pdfs/<...>.pdf
// - storage/pdfs/signed/<...>.pdf
// - Variantes con backslashes de Windows
require_once('../config/load.php');
page_require_level(2);

$DOCROOT       = realpath($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2)); // ej: /var/www/html
$PROJECT_ROOT  = realpath(dirname(__DIR__, 1));                               // carpeta que contiene /pages
$DEBUG         = isset($_GET['debug']) && $_GET['debug'] == '1';

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

// 1) Obtener ruta guardada: por id o por path
$stored = '';
$from   = '';
if (isset($_GET['id']) && (int)$_GET['id'] > 0) {
  $id  = (int)$_GET['id'];
  $row = $db->fetch_assoc($db->query("SELECT file_path FROM doc_files WHERE id={$id} LIMIT 1"));
  if (!$row || empty($row['file_path'])) out_error(404, "Archivo no encontrado para id={$id}");
  $stored = (string)$row['file_path'];
  $from   = 'id';
} elseif (isset($_GET['path'])) {
  $stored = (string)$_GET['path'];
  $from   = 'path';
} else {
  out_error(400, "Falta parámetro 'id' o 'path'. Usa serve_pdf.php?id=123 o serve_pdf.php?path=...pdf");
}

// 2) Normalizar separadores y limpiar
$norm = str_replace('\\','/',$stored);
$norm = preg_replace('#/+#','/',$norm);
$norm = ltrim($norm,'/'); // quitar / inicial

// 3) Detectar prefijo para elegir base de búsqueda
$prefix = '';
if (stripos($norm, 'uploads/results/') === 0) $prefix = 'uploads/results';
elseif (stripos($norm, 'results/') === 0)      $prefix = 'uploads/results';
elseif (stripos($norm, 'storage/pdfs/signed/') === 0) $prefix = 'storage/pdfs/signed';
elseif (stripos($norm, 'storage/pdfs/') === 0)        $prefix = 'storage/pdfs';
elseif (stripos($norm, 'signed/') === 0)              $prefix = 'storage/pdfs/signed';

// Subruta sin el prefijo conocido
$subpath = $norm;
foreach (['uploads/results/', 'results/', 'storage/pdfs/signed/', 'storage/pdfs/', 'signed/'] as $p) {
  if (stripos($norm, $p) === 0) { $subpath = substr($norm, strlen($p)); break; }
}

// 4) Construir candidatos
$candidates = [];
$checked    = [];

if (preg_match('#^([A-Za-z]:/|/)#', $stored)) {
  $candidates[] = $stored;
  $candidates[] = $norm;
  $candidates[] = str_replace('/','\\',$stored);
}

// Helper join
$join = function(...$parts){
  $p = join(DIRECTORY_SEPARATOR, $parts);
  return preg_replace('#'.preg_quote(DIRECTORY_SEPARATOR).'+#', DIRECTORY_SEPARATOR, $p);
};

// Bases conocidas
$doc_uploads_results = $DOCROOT      ? $join($DOCROOT, 'uploads', 'results')            : null;
$doc_storage_pdfs    = $DOCROOT      ? $join($DOCROOT, 'storage', 'pdfs')               : null;
$doc_storage_signed  = $DOCROOT      ? $join($DOCROOT, 'storage', 'pdfs', 'signed')     : null;

$prj_uploads_results = $PROJECT_ROOT ? $join($PROJECT_ROOT, 'uploads', 'results')       : null;
$prj_storage_pdfs    = $PROJECT_ROOT ? $join($PROJECT_ROOT, 'storage', 'pdfs')          : null;
$prj_storage_signed  = $PROJECT_ROOT ? $join($PROJECT_ROOT, 'storage', 'pdfs', 'signed'): null;

$trySets = [];
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
  [$base, $rel] = $pair;
  if (!$base) continue;
  $candidates[] = $join($base, str_replace('/', DIRECTORY_SEPARATOR, $rel));
}

// 5) Resolver en disco
$resolved = null;
foreach ($candidates as $cand) {
  $rp = @realpath($cand);
  $checked[] = ['candidate'=>$cand, 'realpath'=>$rp];
  if ($rp && is_file($rp) && is_readable($rp)) { $resolved = $rp; break; }
}

if (!$resolved) {
  $hint = "No se halló el PDF. Revisa que el archivo exista en /uploads/results, /storage/pdfs o /storage/pdfs/signed. Param: {$stored}";
  if ($DEBUG) out_error(404, "Archivo no encontrado", [
    'from'=>$from,'param'=>$stored,'normalized'=>$norm,'prefix'=>$prefix,'subpath'=>$subpath,
    'DOCROOT'=>$DOCROOT,'PROJECT_ROOT'=>$PROJECT_ROOT,'checked'=>$checked,'hint'=>$hint
  ]);
  out_error(404, "Archivo no encontrado");
}

// 6) Seguridad: limitar a zonas permitidas
$allowedRoots = array_filter([
  $doc_uploads_results, $prj_uploads_results,
  $doc_storage_pdfs,    $prj_storage_pdfs,
  $doc_storage_signed,  $prj_storage_signed,
]);
$allowed=false;
foreach ($allowedRoots as $root) {
  if ($root && str_starts_with($resolved, $root)) { $allowed=true; break; }
}
if (!$allowed) {
  if ($DEBUG) out_error(403, "Acceso denegado (fuera de rutas permitidas)", ['resolved'=>$resolved,'allowedRoots'=>$allowedRoots]);
  out_error(403, "Acceso denegado");
}

// 7) Entregar inline
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.basename($resolved).'"');
header('Content-Length: ' . filesize($resolved));
if (isset($_SERVER['HTTP_ORIGIN'])) {
  header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
  header('Vary: Origin');
}
$fp = fopen($resolved, 'rb'); fpassthru($fp); exit;
