<?php
declare(strict_types=1);

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

page_require_level(2);

function ensure_dir(string $dir){ if(!is_dir($dir)) mkdir($dir, 0775, true); }

// === Validación de ID ===
$id = $_GET['id'] ?? '';
if ($id === '') {
  http_response_code(400);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Falta id']);
  exit;
}

// === Datos del ensayo ===
$Search = find_by_id('moisture_oven', $id);
if (!$Search) {
  http_response_code(404);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Ensayo no encontrado']);
  exit;
}

// === Crear PDF con tu misma plantilla y coordenadas ===
class PDF extends Fpdi {}
$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', [300, 260]); // igual a tu script

$root = realpath(__DIR__.'/..');
$template = $root . '/pdf/template/PV-F-01713 Laboratory Moisture Content With Oven.pdf';
if (!file_exists($template)) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Plantilla no encontrada']);
  exit;
}

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 11);

// -------- Project Information --------
$pdf->SetXY(58.5, 37);
$pdf->Cell(25, 1, (string)($Search['Project_Name'] ?? ''), 0, 1, 'L');
$pdf->SetXY(137, 37);
$pdf->Cell(25, 1, (string)($Search['Project_Number'] ?? ''), 0, 1, 'L');
$pdf->SetXY(220, 34);
$pdf->Cell(25, 5, (string)($Search['Client'] ?? ''), 0, 1, 'L');

// -------- Laboratory Information --------
$pdf->SetXY(58, 52);
$pdf->Cell(27, 1, 'PVDJ SOIL LAB', 0, 1, 'L');
$pdf->SetXY(58, 58);
$pdf->Cell(27, 1, (string)($Search['Technician'] ?? ''), 0, 1, 'L');
$pdf->SetXY(58, 64);
$pdf->Cell(27, 1, (string)($Search['Sample_By'] ?? ''), 0, 1, 'L');
$pdf->SetXY(137, 52);
$pdf->Cell(26, 1, (string)($Search['Standard'] ?? ''), 0, 1, 'L');
$pdf->SetXY(137, 55);
$pdf->Cell(27, 5, (string)($Search['Test_Start_Date'] ?? ''), 0, 1, 'L');
$pdf->SetXY(137, 63);
$regDate = '';
if (!empty($Search['Registed_Date'])) {
  $ts = strtotime((string)$Search['Registed_Date']);
  $regDate = $ts ? date('Y-m-d', $ts) : (string)$Search['Registed_Date'];
}
$pdf->Cell(26.5, 1, $regDate, 0, 1, 'L');
$pdf->SetXY(220, 52);
$pdf->Cell(26, 1, (string)($Search['Method'] ?? ''), 0, 1, 'L');

// -------- Sample Information --------
$pdf->SetXY(58.5, 80);
$pdf->Cell(25, 1, (string)($Search['Structure'] ?? ''), 0, 1, 'L');
$pdf->SetXY(58.5, 86);
$pdf->Cell(25, 1, (string)($Search['Area'] ?? ''), 0, 1, 'L');
$pdf->SetXY(58.5, 92);
$pdf->Cell(25, 1, (string)($Search['Source'] ?? ''), 0, 1, 'L');
$pdf->SetXY(58.5, 98);
$pdf->Cell(25, 1, (string)($Search['Material_Type'] ?? ''), 0, 1, 'L');

$pdf->SetXY(137, 78);
$pdf->Cell(25, 1, (string)($Search['Sample_ID'] ?? ''), 0, 1, 'C');
$pdf->SetXY(137, 85);
$pdf->Cell(25, 1, (string)($Search['Sample_Number'] ?? ''), 0, 1, 'C');
$pdf->SetXY(137, 93);
$pdf->Cell(25, 1, (string)($Search['Sample_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(137, 100);
$pdf->Cell(25, 1, (string)($Search['Elev'] ?? ''), 0, 1, 'C');

$pdf->SetXY(220, 79);
$pdf->Cell(25, 1, (string)($Search['Depth_From'] ?? ''), 0, 1, 'C');
$pdf->SetXY(220, 86);
$pdf->Cell(25, 1, (string)($Search['Depth_To'] ?? ''), 0, 1, 'C');
$pdf->SetXY(220, 94);
$pdf->Cell(25, 1, (string)($Search['North'] ?? ''), 0, 1, 'C');
$pdf->SetXY(220, 100);
$pdf->Cell(25, 1, (string)($Search['East'] ?? ''), 0, 1, 'C');

// -------- Test Information --------
$pdf->SetXY(147, 117);
$pdf->Cell(25, 1, '1', 0, 1, 'C');
$pdf->SetXY(147, 123);
$pdf->Cell(25, 1, (string)($Search['Tare_Name'] ?? ''), 0, 1, 'C');
$pdf->SetXY(147, 129);
$pdf->Cell(25, 1, (string)($Search['Temperature'] ?? ''), 0, 1, 'C');
$pdf->SetXY(147, 134);
$pdf->Cell(25, 1, (string)($Search['Tare_Plus_Wet_Soil'] ?? ''), 0, 1, 'C');
$pdf->SetXY(147, 140);
$pdf->Cell(25, 1, (string)($Search['Tare_Plus_Dry_Soil'] ?? ''), 0, 1, 'C');
$pdf->SetXY(147, 146);
$pdf->Cell(25, 1, (string)($Search['Water_Ww'] ?? ''), 0, 1, 'C');
$pdf->SetXY(147, 152);
$pdf->Cell(25, 1, (string)($Search['Tare_g'] ?? ''), 0, 1, 'C');
$pdf->SetXY(147, 157.5);
$pdf->Cell(25, 1, (string)($Search['Dry_Soil_Ws'] ?? ''), 0, 1, 'C');
$pdf->SetXY(147, 162.5);
$pdf->Cell(25, 1, (string)($Search['Moisture_Content_Porce'] ?? ''), 0, 1, 'C');

// -------- Test Results --------
$pdf->SetXY(198, 172.5);
$passed = (
  ($Search['Material_Type'] ?? '') !== "LPF" ||
  (
    isset($Search['Moisture_Content_Porce']) &&
    is_numeric($Search['Moisture_Content_Porce']) &&
    (float)$Search['Moisture_Content_Porce'] >= 14.5 &&
    (float)$Search['Moisture_Content_Porce'] <= 27.4
  )
) ? "Passed" : "Failed";
$pdf->Cell(25, 1, $passed, 0, 1, 'C');

// -------- Comparación --------
$pdf->SetXY(72, 189.5);
$pdf->Cell(25, 1, (string)($Search['Moisture_Content_Porce'] ?? ''), 0, 1, 'C');

// -------- Comentarios --------
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(24, 208);
$pdf->MultiCell(166, 4, (string)($Search['Comments'] ?? ''), 0, 'L');

// === PDF en memoria (NO enviar inline) ===
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes) < 1000) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'No se pudo generar PDF']);
  exit;
}

// === Versionado / registro (MISMO ESQUEMA) ===
$sample_id     = $Search['Sample_ID'] ?? '';
$sample_number = $Search['Sample_Number'] ?? '';
$test_type     = $Search['Test_Type'] ?? 'MC';   // mantenemos "MC" como en los otros
$templateName  = 'MC-Oven-01713';                // etiqueta de plantilla para el filename

$max = find_by_sql(sprintf(
  "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
  $db->escape($sample_id), $db->escape($sample_number), $db->escape($test_type)
));
$nextVer = (int)($max[0]['v'] ?? 0) + 1;

// === Guardar en disco ===
$dir = $root . '/uploads/results/' . date('Y/m');
ensure_dir($dir);

$filename = sprintf('%s_%s_%s_v%d.pdf',
  preg_replace('/[^A-Za-z0-9\-_.]/','-', $sample_id ?: 'NA'),
  preg_replace('/[^A-Za-z0-9\-_.]/','-', $sample_number ?: 'NA'),
  $templateName,
  $nextVer
);
$abs = $dir . '/' . $filename;
file_put_contents($abs, $pdfBytes);
$rel = str_replace($root, '', $abs); // ej: /uploads/results/2025/10/xxx.pdf

// === Registrar en BD ===
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

// === Respuesta JSON estándar ===
header('Content-Type: application/json');
echo json_encode([
  'ok'       => true,
  'filename' => $filename,
  'path'     => $rel,
  'version'  => $nextVer
]);
exit;
