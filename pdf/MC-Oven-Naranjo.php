<?php
declare(strict_types=1);

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

page_require_level(2);

// ---------- Helpers ----------
function json_error(int $code, string $msg): never {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false, 'error'=>$msg], JSON_UNESCAPED_UNICODE);
  exit;
}
function json_ok(array $data): never {
  header('Content-Type: application/json');
  echo json_encode(['ok'=>true] + $data, JSON_UNESCAPED_UNICODE);
  exit;
}
function ensure_dir(string $dir): void {
  if (!is_dir($dir)) mkdir($dir, 0775, true);
}
function s($v): string {
  return preg_replace('/[^A-Za-z0-9\-_.]/', '-', trim((string)$v) === '' ? 'NA' : (string)$v);
}

// ---------- Entrada ----------
$id   = $_GET['id']   ?? '';
if ($id === '') json_error(400, 'Falta id');

$Search = find_by_id('moisture_oven', $id);
if (!$Search) json_error(404, 'Ensayo no encontrado');

// ---------- PDF ----------
class PDF extends Fpdi {}
$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', [300, 250]);

$root = realpath(__DIR__.'/..'); // raíz del proyecto
$template = $root . '/pdf/template/PV-F-81248 Laboratory Moisture Content by Oven.pdf';
if (!file_exists($template)) json_error(500, 'Plantilla no encontrada');

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Campos
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(52, 32); $pdf->Cell(30, 5, 'PVDJ SOIL LAB', 0, 1, 'L');
$pdf->SetXY(52, 37); $pdf->Cell(30, 5, (string)($Search['Technician'] ?? ''), 0, 1, 'L');
$pdf->SetXY(52, 42); $pdf->Cell(30, 5, (string)($Search['Sample_By'] ?? ''), 0, 1, 'L');

$pdf->SetXY(170, 30); $pdf->Cell(30, 6, (string)($Search['Method'] ?? ''), 0, 1, 'C');
$pdf->SetXY(170, 36); $pdf->Cell(30, 6, (string)($Search['Test_Start_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(170, 42); $pdf->Cell(30, 6, (string)(isset($Search['Registed_Date']) ? date('Y-m-d', strtotime($Search['Registed_Date'])) : ''), 0, 1, 'C');

// Contenido adicional
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(120, 58);  $pdf->Cell(81, 6, (string)($Search['Structure'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 64);  $pdf->Cell(81, 6, (string)($Search['Area'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 69);  $pdf->Cell(81, 6, (string)($Search['Source'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 75);  $pdf->Cell(81, 6, (string)($Search['Sample_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 81);  $pdf->Cell(81, 6, (string)($Search['Sample_ID'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 87);  $pdf->Cell(81, 6, (string)($Search['Sample_Number'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 93);  $pdf->Cell(81, 6, (string)($Search['Material_Type'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 99);  $pdf->Cell(81, 6, (string)($Search['Depth_From'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 104); $pdf->Cell(81, 6, (string)($Search['Depth_To'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 109); $pdf->Cell(81, 6, (string)($Search['North'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 115); $pdf->Cell(81, 6, (string)($Search['East'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 120); $pdf->Cell(81, 6, (string)($Search['Elev'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 141); $pdf->Cell(81, 1, '1', 0, 1, 'C');
$pdf->SetXY(120, 147); $pdf->Cell(81, 1, (string)($Search['Tare_Name'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 153); $pdf->Cell(81, 1, (string)($Search['Temperature'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 158); $pdf->Cell(81, 1, (string)($Search['Tare_Plus_Wet_Soil'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 163); $pdf->Cell(81, 1, (string)($Search['Tare_Plus_Dry_Soil'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 169); $pdf->Cell(81, 1, (string)($Search['Water_Ww'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 175); $pdf->Cell(81, 1, (string)($Search['Tare_g'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 181); $pdf->Cell(81, 1, (string)($Search['Dry_Soil_Ws'] ?? ''), 0, 1, 'C');
$pdf->SetXY(120, 186); $pdf->Cell(81, 1, (string)($Search['Moisture_Content_Porce'] ?? ''), 0, 1, 'C');

// Comentarios
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(21, 210);
$pdf->Cell(170, 0, (string)($Search['Comments'] ?? ''), 0, 1, 'L');

// ----- Generar en memoria -----
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes) < 1000) json_error(500, 'No se pudo generar PDF');

// ---------- Versionado + guardado + doc_files ----------
$sample_id     = (string)($Search['Sample_ID']     ?? '');
$sample_number = (string)($Search['Sample_Number'] ?? '');
$test_type     = (string)($Search['Test_Type']     ?? 'MC_Oven'); // igual que tus otros JSON
$templateName  = 'MO-Oven';

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
$db->query(sprintf(
  "INSERT INTO doc_files (sample_id,sample_number,test_type,template,version,source,file_path,file_name,status)
   VALUES ('%s','%s','%s','%s',%d,'system','%s','%s','awaiting_signature')",
  $db->escape($sample_id),
  $db->escape($sample_number),
  $db->escape($test_type),
  $db->escape($templateName),
  $nextVer,
  $db->escape($rel),
  $db->escape($filename)
));



json_ok([
  'filename' => $filename,
  'path'     => $rel,
  'version'  => $nextVer,
  'sample_id' => $sample_id,
  'sample_number' => $sample_number,
  'test_type' => $test_type,
]);
