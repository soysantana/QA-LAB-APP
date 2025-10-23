<?php
// database/docs_download_day.php
require_once('../config/load.php');
page_require_level(2);
if (session_status() === PHP_SESSION_NONE) session_start();

function norm($p){ $p=str_replace('\\','/',$p); return preg_replace('#/+#','/',$p); }
function is_within($child,$parent){
  $c=rtrim(norm($child),'/').'/'; $p=rtrim(norm($parent),'/').'/';
  return strpos($c,$p)===0;
}

try {
  $date = $_GET['date'] ?? date('Y-m-d');
  if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date)) throw new Exception('Fecha inválida');

  $scope = $_GET['scope'] ?? 'created'; // 'created' | 'signed'
  if (!in_array($scope, ['created','signed'], true)) $scope = 'created';

  $from = $date.' 00:00:00';
  $to   = $date.' 23:59:59';

  $fromEsc = $db->escape($from);
  $toEsc   = $db->escape($to);

  if ($scope === 'signed') {
    $sql = "
      SELECT id, file_path FROM doc_files
       WHERE signed_at BETWEEN '{$fromEsc}' AND '{$toEsc}'
         AND COALESCE(file_path,'') <> ''
       ORDER BY signed_at DESC";
  } else {
    $sql = "
      SELECT id, file_path FROM doc_files
       WHERE created_at BETWEEN '{$fromEsc}' AND '{$toEsc}'
         AND COALESCE(file_path,'') <> ''
       ORDER BY created_at DESC";
  }

  $rows = find_by_sql($sql) ?: [];
  if (empty($rows)) {
    $session->msg('w', 'No hay PDFs para '.$date.' (scope: '.$scope.').');
    header('Location: /pages/docs_list.php'); exit;
  }

  $PROJECT_ROOT = realpath(dirname(__DIR__,1));
  if (!$PROJECT_ROOT) throw new Exception('No se pudo resolver PROJECT_ROOT');
  $PROJECT_ROOT = norm($PROJECT_ROOT);

  $files = [];
  foreach ($rows as $r) {
    $fp = trim($r['file_path'] ?? '');
    if ($fp === '') continue;
    $fp = norm($fp);
    if ($fp[0] === '/') $fp = substr($fp,1);
    $abs = norm($PROJECT_ROOT.'/'.$fp);
    if (!is_within($abs, $PROJECT_ROOT)) continue;
    if (is_file($abs) && is_readable($abs)) $files[] = $abs;
  }

  if (empty($files)) {
    $session->msg('w', 'No se encontraron archivos físicos para '.$date.'.');
    header('Location: /pages/docs_list.php'); exit;
  }

  $label = ($scope === 'signed') ? 'PDFs_firmados' : 'PDFs_creados';
  $zipName = "{$label}_{$date}.zip";
  $tmpZip = tempnam(sys_get_temp_dir(), 'zip_');
  if (!$tmpZip) throw new Exception('No se pudo crear archivo temporal');

  $zip = new ZipArchive();
  if ($zip->open($tmpZip, ZipArchive::OVERWRITE) !== true) throw new Exception('No se pudo abrir ZIP temporal');

  $rootPrefix = $PROJECT_ROOT.'/';
  $added = 0;

  foreach ($files as $abs) {
    $abs = norm($abs);
    $rel = (strpos($abs, $rootPrefix)===0) ? substr($abs, strlen($rootPrefix)) : basename($abs);
    $relInZip = "pdfs_{$date}/".$rel;
    if (!$zip->addFile($abs, $relInZip)) {
      $contents = @file_get_contents($abs);
      if ($contents !== false) $zip->addFromString($relInZip, $contents);
    } else {
      $added++;
    }
  }
  $zip->close();

  if ($added === 0) { @unlink($tmpZip); throw new Exception('No se pudieron agregar PDFs al ZIP.'); }

  header('Content-Type: application/zip');
  header('Content-Disposition: attachment; filename="'.basename($zipName).'"');
  header('Content-Length: '.filesize($tmpZip));
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  readfile($tmpZip);
  @unlink($tmpZip);
  exit;

} catch (Throwable $e) {
  $session->msg('d','Error: '.$e->getMessage());
  header('Location: /pages/docs_list.php'); exit;
}
