<?php
require_once('../config/load.php');
page_require_level(3);

function ensure_dir($d){ if(!is_dir($d)) mkdir($d, 0775, true); }

try {
  $period = $_POST['period'] ?? '';
  if (!preg_match('/^\d{4}\-\d{2}$/', $period)) throw new Exception('Periodo inválido. Use YYYY-MM');

  list($year, $month) = explode('-', $period);

  $root        = realpath(__DIR__.'/..');
  $storageRoot = realpath($root.'/storage');
  if ($storageRoot === false) throw new Exception('No existe carpeta /storage');

  // Origen exacto
  $sourceDir = $storageRoot.'/uploads/results/'.$year.'/'.$month;
  if (!is_dir($sourceDir)) throw new Exception("No existe carpeta: $sourceDir");

  // Destino
  $destDir = $storageRoot."/backups/signed/$year/$month";
  ensure_dir($destDir);

  $zipPath      = "$destDir/backup-$year-$month.zip";
  $manifestPath = "$destDir/manifest-$year-$month.json";

  $pdfs = glob($sourceDir.'/*.pdf');
  if (empty($pdfs)) throw new Exception("No se encontraron PDFs en $sourceDir");

  $files = [];
  $totalBytes = 0;

  $zip = new ZipArchive();
  if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    throw new Exception('No se pudo crear el ZIP');
  }

  foreach ($pdfs as $path) {
    $name = basename($path);
    $sha = hash_file('sha256', $path);
    $bytes = filesize($path);
    $zip->addFile($path, "uploads/results/$year/$month/$name"); // conserva estructura parcial
    $files[] = ['rel'=>"/uploads/results/$year/$month/$name", 'bytes'=>$bytes, 'sha256'=>$sha];
    $totalBytes += $bytes;
  }
  $zip->close();

  $manifest = [
    'period'       => "$year-$month",
    'created_at'   => gmdate('c'),
    'source_dir'   => "/uploads/results/$year/$month",
    'zip_rel'      => "/backups/signed/$year/$month/backup-$year-$month.zip",
    'total_files'  => count($files),
    'total_bytes'  => $totalBytes,
    'files'        => $files
  ];
  file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

  $session->msg('s', "Backup generado: $year-$month (".count($files)." archivos, ".number_format($totalBytes/1024/1024,2)." MB).");
  header('Location: ../pages/backup_resultados_firmados.php'); exit;

} catch (Throwable $e) {
  $session->msg('d','Error: '.$e->getMessage());
  header('Location: ../pages/backup_resultados_firmados.php'); exit;
}
