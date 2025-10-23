<?php
// pages/upload_pdf_editado.php
require_once('../config/load.php');
page_require_level(2);

function ensure_dir($d){ if(!is_dir($d)) mkdir($d, 0775, true); }

function normalize_web_path($p){
  $p = trim($p);
  $p = str_replace('\\','/',$p);
  $p = preg_replace('#/+#','/',$p);
  $p = rtrim($p, " \t\n\r\0\x0B+");
  if ($p !== '' && $p[0] !== '/') $p = '/'.$p;
  return $p;
}

/**
 * Genera un nombre "firmado" estable:
 * - Conserva hasta ..._vN si existe (e.g. _v1, _v2).
 * - Remueve sufijos ruidosos como _text_123456789, -edited, _edited, _tmp, _final, timestamps, etc.
 * - Devuelve: <base>_Signed.<ext>
 */
function make_signed_filename(string $original): string {
  $pi  = pathinfo($original);
  $ext = isset($pi['extension']) && $pi['extension'] !== '' ? $pi['extension'] : 'pdf';
  $ext = strtolower($ext);
  $name = $pi['filename'] ?? 'document';

  // Normalizar guiones/underscores repetidos
  $name = preg_replace('/[ _\-]+/','_', $name);

  // Si tiene patrón con versión: …_vN (N = 1..3 dígitos), corta ahí
  if (preg_match('/^(.*?_v\d{1,3})(?:[_\-].*)?$/i', $name, $m)) {
    $base = $m[1];
  } else {
    // Si no se detecta versión, eliminar sufijos comunes de ediciones/timestamps
    $base = preg_replace([
      '/(_text_\d{6,})$/i',
      '/(_ts_\d{6,})$/i',
      '/([_\-](edited|edit|signed|firmado|tmp|final|rev\d{1,2}))$/i',
      '/([_\-]\d{6,})$/',              // colas numéricas largas
      '/([_\-](\d{8}|\d{14}|\d{17}))$/', // fechas/timestamps típicos
    ], '', $name);

    // Quitar repeticiones residuales de guiones bajos
    $base = preg_replace('/[_\-]+$/','', $base);
    if ($base === '' || $base === null) $base = $name;
  }

  return $base . '_Signed.' . $ext;
}

/**
 * Asegura que el nombre de archivo sea seguro (sin rutas), solo [A-Za-z0-9._-]
 */
function sanitize_filename(string $filename): string {
  $filename = basename($filename);
  $filename = preg_replace('/[^\w.\-]+/u','_', $filename);
  // Evitar nombres peligrosos
  $filename = trim($filename, '._ ');
  if ($filename === '' ) $filename = 'document.pdf';
  return $filename;
}

/**
 * Genera una ruta única si ya existe el archivo (agrega " (1)", " (2)", … antes de la extensión)
 */
function unique_target_path(string $dir, string $filename): string {
  $dir = rtrim($dir, DIRECTORY_SEPARATOR);
  $pi = pathinfo($filename);
  $base = $pi['filename'] ?? 'document';
  $ext  = isset($pi['extension']) && $pi['extension'] !== '' ? '.'.$pi['extension'] : '';
  $candidate = $dir . DIRECTORY_SEPARATOR . $base . $ext;
  $n = 1;
  while (file_exists($candidate)) {
    $candidate = $dir . DIRECTORY_SEPARATOR . $base . " ($n)" . $ext;
    $n++;
    if ($n > 999) { // guardrail
      $candidate = $dir . DIRECTORY_SEPARATOR . $base . '_' . time() . $ext;
      break;
    }
  }
  return $candidate;
}

// ---- Preparar storage base ----
$BASE_DIR = realpath(__DIR__ . '/../storage/pdfs');
if ($BASE_DIR === false) {
  @mkdir(__DIR__ . '/../storage/pdfs/signed', 0775, true);
  $BASE_DIR = realpath(__DIR__ . '/../storage/pdfs');
}
if ($BASE_DIR === false) {
  http_response_code(500);
  header('Content-Type: text/plain; charset=utf-8');
  echo "No se pudo preparar storage/pdfs";
  exit;
}

$SIGNED_DIR = $BASE_DIR . DIRECTORY_SEPARATOR . 'signed';
if (!is_dir($SIGNED_DIR)) { @mkdir($SIGNED_DIR, 0775, true); }
$SIGNED_DIR = realpath($SIGNED_DIR);
if (!$SIGNED_DIR) {
  http_response_code(500);
  header('Content-Type: text/plain; charset=utf-8');
  echo "No se pudo preparar storage/pdfs/signed/";
  exit;
}

// ---- Validar archivo entrante ----
if (empty($_FILES['file']['tmp_name'])) {
  http_response_code(400);
  header('Content-Type: text/plain; charset=utf-8');
  echo "Falta archivo 'file'";
  exit;
}

// ---- Datos entrada ----
$doc_id    = isset($_POST['doc_id']) ? (int)$_POST['doc_id'] : 0;
$origName  = $_FILES['file']['name'] ?? ('document_'.time().'.pdf');
$origName  = sanitize_filename($origName);

// Forzar extensión pdf si vino otra (o sin extensión)
if (!preg_match('/\.pdf$/i', $origName)) $origName .= '.pdf';

// Crear nombre "estable" para firmado
$signedName = make_signed_filename($origName);
$signedName = sanitize_filename($signedName);

// Asegurar unicidad en carpeta /signed
$target = unique_target_path($SIGNED_DIR, $signedName);

// ---- Mover archivo ----
if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
  http_response_code(500);
  header('Content-Type: text/plain; charset=utf-8');
  echo "Error al guardar el archivo";
  exit;
}

// ---- Ruta web relativa (con slash inicial) para doc_files.file_path ----
$relFromStorage = str_replace($BASE_DIR . DIRECTORY_SEPARATOR, '', $target); // ej: signed/xxxx.pdf
$relFromStorage = normalize_web_path('storage/pdfs/'.$relFromStorage);       // => /storage/pdfs/signed/xxxx.pdf

// ---- Actualizar base de datos si corresponde ----
if ($doc_id > 0) {
  $safe_rel  = $db->escape($relFromStorage);
  $signed_by = $db->escape($current_user['name'] ?? 'Sistema');
  $sql = "
    UPDATE doc_files
       SET status     = 'signed',
           signed_by  = '{$signed_by}',
           signed_at  = NOW(),
           file_path  = '{$safe_rel}'
     WHERE id = {$doc_id}
     LIMIT 1
  ";
  $db->query($sql);
}

// ---- Respuesta ----
header('Content-Type: text/plain; charset=utf-8');
echo "OK: " . basename($target);
