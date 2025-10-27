<?php
declare(strict_types=1);

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

// ---------- Helpers ----------
function json_error(int $code, string $msg): never {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false, 'error'=>$msg], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function json_ok(array $data): never {
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

// ---------- Entrada ----------
page_require_level(2);

$id   = $_GET['id']   ?? '';
$mode = $_GET['mode'] ?? 'json'; // 'json' | 'silent'
if ($id === '') json_error(400, 'Falta id');

$Search = find_by_id('moisture_microwave', $id);
if (!$Search) json_error(404, 'Ensayo no encontrado');

// ---------- PDF ----------
class PDF extends Fpdi { function Header(){} function Footer(){} }

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', [300, 250]);

$root = realpath(__DIR__ . '/..'); // raíz del proyecto
$template = $root . '/pdf/template/PV-F-83834_Laboratory Moisture Content by Microwave_Rev 1.pdf';
if (!file_exists($template)) json_error(500, 'Plantilla no encontrada');

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Cabecera laboratorio
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(67, 33); $pdf->Cell(30, 5, 'PVDJ SOIL LAB', 0, 1, 'C');
$pdf->SetXY(67, 37); $pdf->Cell(30, 5, (string)($Search['Technician'] ?? ''), 0, 1, 'C');
$pdf->SetXY(67, 42); $pdf->Cell(30, 5, (string)($Search['Sample_By'] ?? ''), 0, 1, 'C');

$pdf->SetXY(185, 30); $pdf->Cell(30, 6, (string)($Search['Method'] ?? ''), 0, 1, 'C');
$pdf->SetXY(185, 36); $pdf->Cell(30, 6, (string)($Search['Test_Start_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(185, 42); $pdf->Cell(30, 6, (string)($Search['Registed_Date'] ?? ''), 0, 1, 'C');

// Información de muestra
$pdf->SetXY(135,  58);  $pdf->Cell(81, 6, (string)($Search['Structure'] ?? ''),      0, 1, 'C');
$pdf->SetXY(135,  64);  $pdf->Cell(81, 6, (string)($Search['Area'] ?? ''),           0, 1, 'C');
$pdf->SetXY(135,  69);  $pdf->Cell(81, 6, (string)($Search['Source'] ?? ''),         0, 1, 'C');
$pdf->SetXY(135,  76);  $pdf->Cell(81, 6, (string)($Search['Sample_Date'] ?? ''),    0, 1, 'C');
$pdf->SetXY(135,  82.5);$pdf->Cell(81, 6, (string)($Search['Sample_ID'] ?? ''),      0, 1, 'C');
$pdf->SetXY(135,  89);  $pdf->Cell(81, 6, (string)($Search['Sample_Number'] ?? ''),  0, 1, 'C');
$pdf->SetXY(135,  94.5);$pdf->Cell(81, 6, (string)($Search['Material_Type'] ?? ''),  0, 1, 'C');
$pdf->SetXY(135, 100);  $pdf->Cell(81, 6, (string)($Search['Depth_From'] ?? ''),     0, 1, 'C');
$pdf->SetXY(135, 105);  $pdf->Cell(81, 6, (string)($Search['Depth_To'] ?? ''),       0, 1, 'C');
$pdf->SetXY(135, 111);  $pdf->Cell(81, 6, (string)($Search['North'] ?? ''),          0, 1, 'C');
$pdf->SetXY(135, 116);  $pdf->Cell(81, 6, (string)($Search['East'] ?? ''),           0, 1, 'C');
$pdf->SetXY(135, 122.5);$pdf->Cell(81, 6, (string)($Search['Elev'] ?? ''),           0, 1, 'C');

// Datos de ensayo (microondas)
$pdf->SetXY(135, 146);   $pdf->Cell(81, 6, (string)($Search['Tare_Name'] ?? ''),            0, 1, 'C');
$pdf->SetXY(135, 152);   $pdf->Cell(81, 6, (string)($Search['Microwave_Model'] ?? ''),      0, 1, 'C');
$pdf->SetXY(135, 158);   $pdf->Cell(81, 6, '',                                             0, 1, 'C'); // reservado
$pdf->SetXY(135, 164);   $pdf->Cell(81, 6, '',                                             0, 1, 'C'); // reservado
$pdf->SetXY(135, 169.5); $pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil'] ?? ''),   0, 1, 'C');
$pdf->SetXY(135, 175);   $pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 181);   $pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 187);   $pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_3'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 192);   $pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_4'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 198);   $pdf->Cell(81, 6, (string)($Search['Water_Ww'] ?? ''),            0, 1, 'C');
$pdf->SetXY(135, 203);   $pdf->Cell(81, 6, (string)($Search['Tare'] ?? ''),                0, 1, 'C');
$pdf->SetXY(135, 208.5); $pdf->Cell(81, 6, (string)($Search['Dry_Soil'] ?? ''),            0, 1, 'C');
$pdf->SetXY(135, 214);   $pdf->Cell(81, 6, (string)($Search['Moisture_Content_Porce'] ?? ''), 0, 1, 'C');

// Comentarios
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(22, 232);
$pdf->Cell(180, 4, (string)($Search['Comments'] ?? ''), 0, 1, 'L');

// ====== Generar en memoria ======
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes) < 1000) json_error(500, 'No se pudo generar PDF');

// ====== Versionado + guardado + doc_files ======
$sample_id     = (string)($Search['Sample_ID']     ?? '');
$sample_number = (string)($Search['Sample_Number'] ?? '');
$test_type     = (string)($Search['Test_Type']     ?? 'MC_Microwave'); // código por defecto
$templateName  = 'MC-Microwave'; // etiqueta para el nombre del archivo

// versión siguiente
$max = find_by_sql(sprintf(
  "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
  $db->escape($sample_id), $db->escape($sample_number), $db->escape($test_type)
));
$nextVer = (int)($max[0]['v'] ?? 0) + 1;

// ruta destino
$dir = $root . '/uploads/results/' . date('Y/m');
ensure_dir($dir);

$filename = sprintf('%s_%s_%s_v%d.pdf', s($sample_id), s($sample_number), s($templateName), $nextVer);
$abs = $dir . '/' . $filename;
if (file_put_contents($abs, $pdfBytes) === false) {
  json_error(500, 'No se pudo escribir el archivo en disco');
}
$rel = str_replace($root, '', $abs);

// insertar doc_files
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

// ====== Salida ======
if ($mode === 'silent') {
  http_response_code(204); // No Content (no muestra nada)
  exit;
}

json_ok([
  'filename'       => $filename,
  'path'           => $rel,
  'version'        => $nextVer,
  'sample_id'      => $sample_id,
  'sample_number'  => $sample_number,
  'test_type'      => $test_type,
]);
