<?php
require_once('../config/load.php');
page_require_level(2);

function ensure_dir($d){ if(!is_dir($d)) mkdir($d, 0775, true); }
function flatten_files_array(array $files): array {
  $out = [];
  if (!isset($files['name']) || !is_array($files['name'])) return $out;
  $count = count($files['name']);
  for ($i=0; $i<$count; $i++) {
    $out[] = [
      'name'     => $files['name'][$i],
      'type'     => $files['type'][$i],
      'tmp_name' => $files['tmp_name'][$i],
      'error'    => $files['error'][$i],
      'size'     => $files['size'][$i],
    ];
  }
  return $out;
}
function normalize_web_path($p){
  $p = trim($p);
  $p = str_replace('\\','/',$p);
  $p = preg_replace('#/+#','/',$p);
  $p = rtrim($p, " \t\n\r\0\x0B+");
  if ($p !== '' && $p[0] !== '/') $p = '/'.$p;
  return $p;
}

try {
  if (empty($_FILES['pdfs'])) throw new Exception('No se recibieron PDFs.');
  $files = flatten_files_array($_FILES['pdfs']);

  $sample_ids     = $_POST['sample_id']     ?? [];
  $sample_numbers = $_POST['sample_number'] ?? [];
  $test_types     = $_POST['test_type']     ?? [];

  if (count($files) !== count($sample_ids)
   || count($files) !== count($sample_numbers)
   || count($files) !== count($test_types)) {
    throw new Exception('Desfase entre archivos y filas del formulario.');
  }

  $root = realpath(__DIR__.'/..');
  $dir  = $root.'/uploads/results/'.date('Y/m');
  ensure_dir($dir);

  $ok = 0; $fail = 0; $msgs = [];

  foreach ($files as $idx => $f) {
    try {
      if ($f['error'] !== UPLOAD_ERR_OK) throw new Exception('Error al subir (código '.$f['error'].')');

      $origName = $f['name'];
      $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
      if ($ext !== 'pdf') throw new Exception('Archivo no es PDF: '.$origName);
      if ($f['size'] > 50*1024*1024) throw new Exception('Excede 50 MB: '.$origName);

      $sid_raw  = trim((string)$sample_ids[$idx]);
      $snum_raw = trim((string)$sample_numbers[$idx]);
      $tt_raw   = trim((string)$test_types[$idx]);
      if ($sid_raw === '' || $snum_raw === '' || $tt_raw === '') {
        throw new Exception('Faltan campos (Sample ID, Sample Number, Test Type) para '.$origName);
      }

      $sid  = $db->escape($sid_raw);
      $snum = $db->escape($snum_raw);
      $tt   = $db->escape($tt_raw);

      $max = find_by_sql("
        SELECT MAX(version) AS v
          FROM doc_files
         WHERE sample_id='{$sid}'
           AND sample_number='{$snum}'
           AND test_type='{$tt}'
      ");
      $nextVer = (int)($max[0]['v'] ?? 0) + 1;

      $safe = sprintf(
        '%s_%s_%s_v%d.pdf',
        preg_replace('/[^A-Za-z0-9\-_.]/','-', $sid_raw ?: 'NA'),
        preg_replace('/[^A-Za-z0-9\-_.]/','-', $snum_raw ?: 'NA'),
        preg_replace('/[^A-Za-z0-9\-_.]/','-', $tt_raw ?: 'NA'),
        $nextVer
      );

      $abs = $dir.'/'.$safe;
      if (!is_uploaded_file($f['tmp_name'])) throw new Exception('Subida inválida');
      if (!move_uploaded_file($f['tmp_name'], $abs)) throw new Exception('No se pudo guardar archivo');

      $rel = normalize_web_path('/uploads/results/'.date('Y/m').'/'.$safe);

      $template_sql = "NULL";
      $sql = sprintf(
        "INSERT INTO doc_files
          (sample_id, sample_number, test_type, template, version, source, file_path, file_name, status, created_at)
         VALUES
          ('%s','%s','%s',%s,%d,'external','%s','%s','awaiting_signature', NOW())",
        $sid, $snum, $tt, $template_sql, $nextVer, $db->escape($rel), $db->escape($safe)
      );
      $db->query($sql);

      $ok++;
    } catch (Throwable $ie) {
      $fail++; $msgs[] = $ie->getMessage(); continue;
    }
  }

  if ($fail === 0)        $session->msg('s', "Se subieron {$ok} PDF(s) correctamente.");
  else if ($ok > 0)       $session->msg('w', "Subidos: {$ok}. Fallidos: {$fail}. Detalles: ".implode(' | ', $msgs));
  else                    $session->msg('d', "Todos fallaron: ".implode(' | ', $msgs));

  header('Location: /pages/docs_list.php'); exit;

} catch (Throwable $e) {
  $session->msg('d','Error: '.$e->getMessage());
  header('Location: /pages/doc_upload_external.php'); exit;
}
