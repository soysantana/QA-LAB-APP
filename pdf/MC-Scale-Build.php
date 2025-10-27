<?php
declare(strict_types=1);

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

// ==== Protección JSON: no imprimir HTML de errores ====
ini_set('display_errors', '0');      // No imprimir warnings/notices en la salida
ini_set('log_errors', '1');          // Manda errores al log de PHP
ob_start();                          // Captura cualquier salida accidental

set_error_handler(function($severity, $message, $file, $line) {
  // Convierte warnings/notices en excepciones para poder responder JSON
  throw new ErrorException($message, 0, $severity, $file, $line);
});

function json_error(int $code, string $msg): never {
  if (ob_get_length()) { ob_clean(); }
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false, 'error'=>$msg], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function json_ok(array $data): never {
  if (ob_get_length()) { ob_clean(); }
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>true] + $data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function ensure_dir(string $dir): void {
  if (!is_dir($dir)) mkdir($dir, 0775, true);
}
function s($v): string {
  return preg_replace('/[^A-Za-z0-9\-_.]/', '-', trim((string)$v) === '' ? 'NA' : (string)$v);
}

page_require_level(2);

// ---------- Entrada ----------
$id = $_GET['id'] ?? '';
if ($id === '') json_error(400, 'Falta id');

try {
  $Search = find_by_id('moisture_scale', $id);
  if (!$Search) json_error(404, 'Ensayo no encontrado');

  // ---------- PDF ----------
  class PDF extends Fpdi {}
  $pdf = new PDF();
  $pdf->SetMargins(0, 0, 0);
  $pdf->AddPage('P', [300, 220]);

  $root = realpath(__DIR__.'/..'); // raíz del proyecto
  $template = $root . '/pdf/template/PV-F-01714_Laboratory Moisture Content with Scale_Rev 1.pdf';
  if (!file_exists($template)) json_error(500, 'Plantilla no encontrada');

  $pdf->setSourceFile($template);
  $tplIdx = $pdf->importPage(1);
  $pdf->useTemplate($tplIdx, 0, 0);

  // Campos
  $pdf->SetFont('Arial', '', 10);
  $data_fields = [
    [52, 38, 'PVDJ SOIL LAB', 'L', 30, 1],
    [52, 43, (string)($Search['Technician']       ?? ''), 'L', 30, 1],
    [52, 49, (string)($Search['Sample_By']        ?? ''), 'L', 30, 1],
    [150,38, (string)($Search['Methods']          ?? ''), 'C', 30, 1],
    [150,43, (string)($Search['Test_Start_Date']  ?? ''), 'C', 30, 1],
    [150,49, (string)($Search['Registed_Date']    ?? ''), 'C', 30, 1],
  ];
  foreach ($data_fields as [$x,$y,$txt,$al,$w,$h]) {
    $pdf->SetXY($x,$y);
    $pdf->Cell($w,$h,$txt,0,1,$al);
  }

  $pdf->SetFont('Arial', '', 11);
  $additional_fields = [
    [107,  64,   (string)($Search['Structure']               ?? '')],
    [107,  70,   (string)($Search['Area']                    ?? '')],
    [107,  74.5, (string)($Search['Source']                  ?? '')],
    [107,  80,   (string)($Search['Sample_Date']             ?? '')],
    [107,  85,   (string)($Search['Sample_ID']               ?? '')],
    [107,  89.5, (string)($Search['Sample_Number']           ?? '')],
    [107,  95,   (string)($Search['Material_Type']           ?? '')],
    [107,  99.5, (string)($Search['Depth_From']              ?? '')],
    [107,  104.5,(string)($Search['Depth_To']                ?? '')],
    [107,  109.5,(string)($Search['North']                   ?? '')],
    [107,  115,  (string)($Search['East']                    ?? '')],
    [107,  120,  (string)($Search['Elev']                    ?? '')],
    [107,  133.6,'1'],
    [107,  138.5,(string)($Search['Tare_Name']               ?? '')],
    [107,  144,  (string)($Search['Moisture_Content_Porce']  ?? '')],
  ];
  foreach ($additional_fields as [$x,$y,$txt]) {
    $pdf->SetXY($x,$y);
    $pdf->Cell(81, 1, $txt, 0, 1, 'C');
  }

  // Comentarios
  $pdf->SetFont('Arial', '', 12);
  $pdf->SetXY(19, 165);
  $pdf->Cell(150, 4, (string)($Search['Comments'] ?? ''), 0, 1, 'L');

  // ----- Generar en memoria -----
  $pdfBytes = $pdf->Output('S');
  if (!$pdfBytes || strlen($pdfBytes) < 1000) json_error(500, 'No se pudo generar PDF');

  // ---------- Versionado + guardado + doc_files ----------
  $sample_id     = (string)($Search['Sample_ID']     ?? '');
  $sample_number = (string)($Search['Sample_Number'] ?? '');
  $test_type     = (string)($Search['Test_Type']     ?? 'MC');
  $templateName  = 'MC-Scale';

  // next version
  $max = find_by_sql(sprintf(
    "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
    $db->escape($sample_id), $db->escape($sample_number), $db->escape($test_type)
  ));
  $nextVer = (int)($max[0]['v'] ?? 0) + 1;

  // path
  $dir = $root . '/uploads/results/' . date('Y/m');
  ensure_dir($dir);

  $filename = sprintf('%s_%s_%s_v%d.pdf', s($sample_id), s($sample_number), s($templateName), $nextVer);
  $abs = $dir . '/' . $filename;
  if (file_put_contents($abs, $pdfBytes) === false) {
    json_error(500, 'No se pudo escribir el archivo en disco');
  }
  $rel = str_replace($root, '', $abs);

  // insert doc_files
  $sql = sprintf(
    "INSERT INTO doc_files (sample_id,sample_number,test_type,template,version,source,file_path,file_name,status)
     VALUES ('%s','%s','%s','%s',%d,'system','%s','%s','awaiting_signature')",
    $db->escape($sample_id),
    $db->escape($sample_number),
    $db->escape($test_type),
    $db->escape($templateName),
    $nextVer,
    $db->escape($rel),
    $db->escape($filename)
  );
  if (!$db->query($sql)) {
    json_error(500, 'DB error: '.$db->error);
  }

  // ---------- Respuesta ----------
  json_ok([
    'filename'       => $filename,
    'path'           => $rel,
    'version'        => $nextVer,
    'sample_id'      => $sample_id,
    'sample_number'  => $sample_number,
    'test_type'      => $test_type,
  ]);

} catch (Throwable $e) {
  // Cualquier warning/notice/exception termina aquí como JSON
  json_error(500, $e->getMessage());
}
