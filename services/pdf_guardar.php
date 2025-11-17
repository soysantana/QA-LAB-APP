<?php
// services/pdf_guardar.php
declare(strict_types=1);

require_once(__DIR__ . '/../config/load.php');

function ensure_dir(string $dir): void {
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }
}

function s($v): string {
  return preg_replace(
    '/[^A-Za-z0-9\-_.]/',
    '-',
    trim((string)$v) === '' ? 'NA' : (string)$v
  );
}

/**
 * Guarda un PDF de ensayo, lo versiona y lo registra en doc_files.
 *
 * @param string $pdfBytes      Contenido del PDF (Output('S'))
 * @param array  $row           Fila del ensayo (con Sample_ID, Sample_Number, Test_Type, etc.)
 * @param string $templateName  Nombre lógico de la plantilla (ej. 'AL-Rev2', 'SP-Rev1')
 *
 * @return array ['filename', 'path', 'version', 'sample_id', 'sample_number', 'test_type', 'template']
 */
function guardar_pdf_ensayo(string $pdfBytes, array $row, string $templateName): array {
  global $db;

  if (!$pdfBytes || strlen($pdfBytes) < 1000) {
    // Por si quieres lanzar error aquí también
    http_response_code(500);
    throw new RuntimeException('PDF vacío o muy pequeño');
  }

  $root = realpath(__DIR__ . '/..');

  $sample_id     = (string)($row['Sample_ID']     ?? '');
  $sample_number = (string)($row['Sample_Number'] ?? '');
  $test_type     = (string)($row['Test_Type']     ?? '');

  if ($test_type === '') {
    // Si tu tabla no tiene Test_Type, puedes poner fijo el código del ensayo
    $test_type = $templateName;
  }

  // Buscar versión previa
  $sql = sprintf(
    "SELECT MAX(version) AS v
       FROM doc_files
      WHERE sample_id='%s'
        AND sample_number='%s'
        AND test_type='%s'",
    $db->escape($sample_id),
    $db->escape($sample_number),
    $db->escape($test_type)
  );
  $max = find_by_sql($sql);
  $nextVer = (int)($max[0]['v'] ?? 0) + 1;

  // Carpeta año/mes
  $dir = $root . '/uploads/results/' . date('Y/m');
  ensure_dir($dir);

  // Nombre del archivo
  $filename = sprintf(
    '%s-%s-%s-v%d.pdf',
    s($sample_id),
    s($sample_number),
    s($templateName),
    $nextVer
  );
  $abs = $dir . '/' . $filename;

  if (file_put_contents($abs, $pdfBytes) === false) {
    http_response_code(500);
    throw new RuntimeException('No se pudo escribir el archivo en disco');
  }

  $rel = str_replace($root, '', $abs);

  // Registrar en doc_files
  $sqlIns = sprintf(
    "INSERT INTO doc_files
       (sample_id, sample_number, test_type, template, version, source, file_path, file_name, status)
     VALUES ('%s','%s','%s','%s',%d,'system','%s','%s','awaiting_signature')",
    $db->escape($sample_id),
    $db->escape($sample_number),
    $db->escape($test_type),
    $db->escape($templateName),
    $nextVer,
    $db->escape($rel),
    $db->escape($filename)
  );
  $db->query($sqlIns);

  return [
    'filename'       => $filename,
    'path'           => $rel,
    'version'        => $nextVer,
    'sample_id'      => $sample_id,
    'sample_number'  => $sample_number,
    'test_type'      => $test_type,
    'template'       => $templateName,
  ];
}
