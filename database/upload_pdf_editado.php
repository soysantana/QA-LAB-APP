<?php
// pages/upload_pdf_editado.php
require_once('../config/load.php');
page_require_level(2);

// Carpeta base para PDFs firmados: /storage/pdfs/signed
$BASE_DIR = realpath(__DIR__ . '/../storage/pdfs');
if ($BASE_DIR === false) {
  @mkdir(__DIR__ . '/../storage/pdfs/signed', 0775, true);
  $BASE_DIR = realpath(__DIR__ . '/../storage/pdfs');
}
if ($BASE_DIR === false) {
  http_response_code(500); header('Content-Type: text/plain; charset=utf-8');
  echo "No se pudo crear/abrir storage/pdfs"; exit;
}
$SIGNED_DIR = $BASE_DIR . DIRECTORY_SEPARATOR . 'signed';
if (!is_dir($SIGNED_DIR)) { @mkdir($SIGNED_DIR, 0775, true); }
$SIGNED_DIR = realpath($SIGNED_DIR);

if (!$SIGNED_DIR) {
  http_response_code(500); header('Content-Type: text/plain; charset=utf-8');
  echo "No se pudo preparar carpeta signed/"; exit;
}

if (empty($_FILES['file']['tmp_name'])) {
  http_response_code(400); header('Content-Type: text/plain; charset=utf-8');
  echo "Falta archivo 'file'"; exit;
}

$doc_id = isset($_POST['doc_id']) ? (int)$_POST['doc_id'] : 0;
$origName = $_FILES['file']['name'] ?? ('document_'.time().'.pdf');
$origName = preg_replace('/[^\w\-.]+/','_', $origName);

$target = $SIGNED_DIR . DIRECTORY_SEPARATOR . $origName;
$pi = pathinfo($target);
$i = 0;
while (file_exists($target)) {
  $i++;
  $target = $pi['dirname'] . DIRECTORY_SEPARATOR . $pi['filename'] . "_$i." . ($pi['extension'] ?? 'pdf');
}

if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
  http_response_code(500); header('Content-Type: text/plain; charset=utf-8');
  echo "Error al guardar el archivo"; exit;
}

// Ruta relativa desde storage/pdfs
$relFromStorage = str_replace($BASE_DIR . DIRECTORY_SEPARATOR, '', $target); // ej: signed/mi.pdf
$relFromStorage = str_replace('\\','/',$relFromStorage);

// Si quieres que el registro apunte al firmado y marque firmado:
if ($doc_id > 0) {
  $safe = $db->escape($relFromStorage);
  $signed_by = $db->escape($current_user['name'] ?? 'Sistema');
  $sql = "
    UPDATE doc_files
       SET status     = 'signed',
           signed_by  = '{$signed_by}',
           signed_at  = NOW(),
           file_path  = CONCAT('storage/pdfs/', '{$safe}')
     WHERE id = {$doc_id}
     LIMIT 1
  ";
  $db->query($sql);
}

header('Content-Type: text/plain; charset=utf-8');
echo "OK: " . basename($target);
