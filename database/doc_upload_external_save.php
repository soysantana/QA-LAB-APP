<?php
require_once('../config/load.php'); page_require_level(2);

function ensure_dir($d){ if(!is_dir($d)) mkdir($d, 0775, true); }

try {
  if (empty($_FILES['pdf']['name'])) throw new Exception('PDF requerido');

  // Sanitizar inputs
  $sample_id_raw     = $_POST['sample_id']     ?? '';
  $sample_number_raw = $_POST['sample_number'] ?? '';
  $test_type_raw     = $_POST['test_type']     ?? '';

  $sample_id     = $db->escape($sample_id_raw);
  $sample_number = $db->escape($sample_number_raw);
  $test_type     = $db->escape($test_type_raw);

  $root = realpath(__DIR__.'/..');
  $dir  = $root.'/uploads/results/'.date('Y/m');
  ensure_dir($dir);

  // calcular próxima versión
  $max = find_by_sql("
    SELECT MAX(version) AS v
      FROM doc_files
     WHERE sample_id='{$sample_id}'
       AND sample_number='{$sample_number}'
       AND test_type='{$test_type}'
  ");
  $nextVer = (int)($max[0]['v'] ?? 0) + 1;

  // nombre de archivo (sin 'external', sin 'template')
  $safe = sprintf(
    '%s_%s_v%d.pdf',
    preg_replace('/[^A-Za-z0-9\-_.]/','-', $sample_id_raw ?: 'NA'),
    preg_replace('/[^A-Za-z0-9\-_.]/','-', $sample_number_raw ?: 'NA'),
    $nextVer
  );

  $abs = $dir.'/'.$safe;
  if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $abs)) {
    throw new Exception('No se pudo guardar el archivo');
  }
  $rel = str_replace($root, '', $abs);

  // template = NULL (si la columna acepta NULL). Si no acepta, usa ''.
  $template_sql = "NULL"; // ó "' '" si prefieres vacío

  // INSERT ALINEADO CON LAS COLUMNAS
  $sql = sprintf(
    "INSERT INTO doc_files
      (sample_id, sample_number, test_type, template, version, source, file_path, file_name, status)
     VALUES
      ('%s',      '%s',          '%s',      %s,       %d,      'external','%s',     '%s',      'awaiting_signature')",
    $sample_id, $sample_number, $test_type, $template_sql, $nextVer, $db->escape($rel), $db->escape($safe)
  );
  $db->query($sql);

  $session->msg('s','PDF externo subido (v'.$nextVer.').');
  header('Location: /pages/docs_list.php'); exit;

} catch (Throwable $e) {
  $session->msg('d','Error: '.$e->getMessage());
  header('Location: /pages/doc_upload_external.php'); exit;
}
