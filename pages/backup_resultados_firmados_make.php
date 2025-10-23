<?php
// pages/backup_resultados_firmados_make.php
require_once('../config/load.php');
page_require_level(2);

if (session_status() === PHP_SESSION_NONE) session_start();

// Recomendado para ZIPs grandes
@set_time_limit(0);
@ini_set('memory_limit', '1024M');

function ensure_dir($d){ if(!is_dir($d)) mkdir($d, 0775, true); }
function normalize_web_path($p){
  $p = trim($p); $p = str_replace('\\','/',$p); $p = preg_replace('#/+#','/',$p);
  if ($p !== '' && $p[0] !== '/') $p = '/'.$p; return $p;
}
function hash_file_algo($algo, $file){
  if ($algo==='sha256') return hash_file('sha256', $file);
  if ($algo==='sha1')   return hash_file('sha1',   $file);
  if ($algo==='md5')    return hash_file('md5',    $file);
  return null;
}

$ROOT = rtrim(str_replace('\\','/', realpath(__DIR__ . '/..')), '/');
if ($ROOT === '' || $ROOT === false) {
  http_response_code(500);
  echo "No ROOT";
  exit;
}

$year  = isset($_POST['year'])  ? (int)$_POST['year']  : (int)date('Y');
$month = isset($_POST['month']) ? (int)$_POST['month'] : (int)date('n');
$zipNameInput = isset($_POST['zip_name']) ? trim($_POST['zip_name']) : '';
$algo = $_POST['checksum_algo'] ?? 'sha256';

if ($year < 2000 || $year > 2100) $year = (int)date('Y');
if ($month < 1 || $month > 12)    $month = (int)date('n');

$y  = sprintf('%04d', $year);
$m2 = sprintf('%02d', $month);

// ====== FUENTES EXACTAS COMO LAS GUARDAS ======
// Resultados generados por mes
$SRC_RESULTS_MONTH = $ROOT . "/uploads/results/$y/$m2";

// Firmados: soporta dos esquemas a la vez
$SRC_SIGNED_MONTH  = $ROOT . "/storage/pdfs/signed/$y/$m2"; // si usas YYYY/MM
$SRC_SIGNED_ROOT   = $ROOT . "/storage/pdfs/signed";        // si guardas directo en /signed (sin YYYY/MM)

// ====== DESTINO ZIP + MANIFIESTO ======
$OUT_DIR = $ROOT . "/storage/backups/results/$y/$m2";
ensure_dir($OUT_DIR);

$zipBase = $zipNameInput !== '' ? basename($zipNameInput) : "Resultados_Firmados_{$y}-{$m2}.zip";
$zipBase = preg_replace('/[^\w.\-]+/u','_', $zipBase);
if (!preg_match('/\.zip$/i', $zipBase)) $zipBase .= '.zip';

$zipAbs      = $OUT_DIR . '/' . $zipBase;
$manifestAbs = $OUT_DIR . '/manifest.json';

// ====== RECOLECTAR PDFs ======
$files = [];
$failedScan = [];

$scanDirs = [];
if (is_dir($SRC_RESULTS_MONTH)) $scanDirs[] = $SRC_RESULTS_MONTH;
if (is_dir($SRC_SIGNED_MONTH))  $scanDirs[] = $SRC_SIGNED_MONTH;
if (is_dir($SRC_SIGNED_ROOT))   $scanDirs[] = $SRC_SIGNED_ROOT;

foreach ($scanDirs as $src) {
  $src = str_replace('\\','/',$src);
  try {
    $it = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
      RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($it as $f) {
      /** @var SplFileInfo $f */
      if ($f->isFile()) {
        $name = $f->getFilename();
        if (preg_match('/\.pdf$/i', $name)) {
          $abs = str_replace('\\','/',$f->getPathname());
          $files[] = $abs;
        }
      }
    }
  } catch (Throwable $e) {
    $failedScan[] = "No se pudo escanear: $src (" . $e->getMessage() . ")";
  }
}

if (empty($files)) {
  $_SESSION['flash'] = ['type'=>'warning', 'msg'=>"No se encontraron PDFs en $y/$m2."];
  if (!empty($failedScan)) {
    $_SESSION['last_backup_signed_results_failed'] = $failedScan;
  }
  header("Location: /pages/backup_resultados_firmados.php?period={$y}-{$m2}");
  exit;
}

// ====== CREAR ZIP (SIN REALPATH; FALLBACK addFromString) ======
$zip = new ZipArchive();
if ($zip->open($zipAbs, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
  $_SESSION['flash'] = ['type'=>'danger', 'msg'=>"No se pudo crear el ZIP."];
  header("Location: /pages/backup_resultados_firmados.php?period={$y}-{$m2}");
  exit;
}

$added = 0;
$failed = [];

// Ayuda para calcular la ruta RELATIVA dentro del ZIP tal cual estructura proyecto
$ROOT_PREFIX = $ROOT . '/';

foreach ($files as $abs) {
  $absNorm = str_replace('\\','/', $abs);

  if (!is_file($absNorm) || !is_readable($absNorm)) {
    $failed[] = $absNorm . ' (no legible)';
    continue;
  }

  // Ruta relativa a $ROOT (para mantener estructura dentro del ZIP)
  $rel = $absNorm;
  if (strpos($absNorm, $ROOT_PREFIX) === 0) {
    $rel = substr($absNorm, strlen($ROOT_PREFIX)); // p.ej. storage/pdfs/signed/2025/10/archivo.pdf
  } else {
    // Si por alguna razón está fuera, usa el nombre base para no romper
    $rel = basename($absNorm);
  }

  // Normalizar a separadores ZIP
  $rel = str_replace('\\','/', $rel);

  // Intento 1: agregar archivo directo
  $ok = $zip->addFile($absNorm, $rel);

  // Intento 2 (fallback): si falla, leer y agregar desde memoria
  if ($ok === false) {
    $contents = @file_get_contents($absNorm);
    if ($contents === false) {
      $failed[] = $absNorm . ' (addFile y lectura fallaron)';
      continue;
    }
    $ok = $zip->addFromString($rel, $contents);
    if ($ok === false) {
      $failed[] = $absNorm . ' (addFromString falló)';
      continue;
    }
  }

  $added++;
}

$zip->close();

// Si no se agregó nada, purgar ZIP y notificar
if ($added === 0) {
  @unlink($zipAbs);
  $msg = "El ZIP quedó vacío: no se pudo agregar ningún PDF. Verifica permisos/rutas.";
  if (!empty($failed) || !empty($failedScan)) {
    $_SESSION['last_backup_signed_results_failed'] = array_merge($failedScan, $failed);
    $msg .= " Se guardaron detalles de fallos en sesión.";
  }
  $_SESSION['flash'] = ['type'=>'danger', 'msg'=>$msg];
  header("Location: /pages/backup_resultados_firmados.php?period={$y}-{$m2}");
  exit;
}

// ====== MANIFIESTO (OPCIONAL) CON MISMAS RUTAS QUE VAN EN EL ZIP ======
$manifest = [
  'period'       => ['year'=>$y, 'month'=>$m2],
  'generated_at' => date('Y-m-d H:i:s'),
  'algorithm'    => ($algo==='none'?'none':$algo),
  'files'        => []
];

if ($algo !== 'none') {
  foreach ($files as $abs) {
    $absNorm = str_replace('\\','/', $abs);
    if (!is_file($absNorm) || !is_readable($absNorm)) continue;

    $rel = $absNorm;
    if (strpos($absNorm, $ROOT_PREFIX) === 0) {
      $rel = substr($absNorm, strlen($ROOT_PREFIX));
    } else {
      $rel = basename($absNorm);
    }
    $rel = str_replace('\\','/',$rel);

    $hash = hash_file_algo($algo, $absNorm);
    $manifest['files'][] = ['path'=>$rel, 'hash'=>$hash];
  }
  @file_put_contents($manifestAbs, json_encode($manifest, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
} else {
  if (is_file($manifestAbs)) @unlink($manifestAbs);
}

// ====== RUTAS WEB ======
$zipWeb      = normalize_web_path("storage/backups/results/$y/$m2/".basename($zipAbs));
$manifestWeb = is_file($manifestAbs) ? normalize_web_path("storage/backups/results/$y/$m2/manifest.json") : null;

// ====== GUARDAR EN SESIÓN (para mostrar/descargar/purgar) ======
$nonce = bin2hex(random_bytes(16));
$_SESSION['last_backup_signed_results'] = [
  'year'         => $y,
  'month'        => (string)$month,
  'month2'       => $m2,
  'zip_abs'      => $zipAbs,
  'zip_web'      => $zipWeb,
  'zip_size'     => @filesize($zipAbs),
  'manifest_abs' => $manifestAbs,
  'manifest_web' => $manifestWeb,
  'created_at'   => date('Y-m-d H:i:s'),
  'nonce'        => $nonce
];

if (!empty($failed) || !empty($failedScan)) {
  $_SESSION['last_backup_signed_results_failed'] = array_merge($failedScan, $failed);
}

// Mensaje OK y redirección a la página (o directo a descarga si prefieres)
$_SESSION['flash'] = ['type'=>'success', 'msg'=>"Backup generado correctamente para $y-$m2. ($added archivos en el ZIP)"];

// Si quieres que inicie descarga de una vez, descomenta la siguiente línea:
// header("Location: /pages/backup_resultados_firmados_download.php?type=zip"); exit;

header("Location: /pages/backup_resultados_firmados.php?period={$y}-{$m2}");
exit;
