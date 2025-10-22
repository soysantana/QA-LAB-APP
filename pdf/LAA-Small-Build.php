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

// === Buscar datos del ensayo ===
$Search = find_by_id('los_angeles_abrasion_small', $id);
if (!$Search) {
  http_response_code(404);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Ensayo no encontrado']);
  exit;
}

// === Crear PDF (misma geometría/plantilla y coordenadas que tu script) ===
class PDF extends Fpdi { function Header(){} function Footer(){} }
$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

// Tamaño de página (Portrait, 420 x 450) como en tu versión
$pdf->AddPage('P', [420, 450]);

$root = realpath(__DIR__.'/..'); // raíz del proyecto (carpeta padre de /pages)
$template = $root . '/pdf/template/PV-F-01716 Laboratory Los Angeles Abrasion for large agregate.pdf';
if (!file_exists($template)) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Plantilla no encontrada']);
  exit;
}

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// ---------- Information for the test ----------
$pdf->SetXY(75, 93);
$pdf->Cell(40, 5, (string)($Search['Project_Name'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 114);
$pdf->Cell(40, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(75, 121);
$pdf->Cell(40, 5, (string)($Search['Technician'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 128);
$pdf->Cell(40, 5, (string)($Search['Sample_By'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 148);
$pdf->Cell(40, 6, (string)($Search['Structure'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 155);
$pdf->Cell(40, 6, (string)($Search['Area'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 162);
$pdf->Cell(40, 6, (string)($Search['Source'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 169);
$pdf->Cell(40, 6, (string)($Search['Material_Type'] ?? ''), 0, 1, 'C');

$pdf->SetXY(175, 93);
$pdf->Cell(40, 5, (string)($Search['Project_Number'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 114);
$pdf->Cell(40, 6, (string)($Search['Standard'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 121);
$pdf->Cell(40, 6, (string)($Search['Test_Start_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 128);
$pdf->Cell(40, 6, (string)($Search['Registed_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 148);
$pdf->Cell(40, 6, (string)($Search['Sample_ID'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 155);
$pdf->Cell(40, 6, (string)($Search['Sample_Number'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 162);
$pdf->Cell(40, 6, (string)($Search['Sample_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 169);
$pdf->Cell(40, 6, (string)($Search['Elev'] ?? ''), 0, 1, 'C');

$pdf->SetXY(285, 93);
$pdf->Cell(40, 5, (string)($Search['Client'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 114);
$pdf->Cell(40, 6, (string)($Search['Methods'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 121);
$pdf->Cell(40, 6, (string)($Search['Preparation_Method'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 128);
$pdf->Cell(40, 6, (string)($Search['Split_Method'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 148);
$pdf->Cell(40, 6, (string)($Search['Depth_From'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 155);
$pdf->Cell(40, 6, (string)($Search['Depth_To'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 162);
$pdf->Cell(40, 6, (string)($Search['North'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 169);
$pdf->Cell(40, 6, (string)($Search['East'] ?? ''), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// ---------- Test Information and values ----------
$pdf->SetXY(198, 209);
$pdf->Cell(33, 8, (string)($Search['NominalMaxSize'] ?? ''), 0, 1, 'C');
$pdf->SetXY(198, 217);
$pdf->Cell(33, 8, (string)($Search['Grading'] ?? ''), 0, 1, 'C');
$pdf->SetXY(198, 225);
$pdf->Cell(33, 8, (string)($Search['Weight_Spheres'] ?? ''), 0, 1, 'C');
$pdf->SetXY(198, 233);
$pdf->Cell(33, 8, (string)($Search['Revolutions'] ?? ''), 0, 1, 'C');
$pdf->SetXY(198, 241);
$pdf->Cell(33, 8, (string)($Search['Initial_Weight'] ?? ''), 0, 1, 'C');
$pdf->SetXY(198, 249);
$pdf->Cell(33, 8, (string)($Search['Final_Weight'] ?? ''), 0, 1, 'C');
$pdf->SetXY(198, 258);
$pdf->Cell(33, 8, (string)($Search['Weight_Loss'] ?? ''), 0, 1, 'C');
$pdf->SetXY(198, 266);
$pdf->Cell(33, 8, (string)($Search['Weight_Loss_Porce'] ?? ''), 0, 1, 'C');

// ---------- Test Results ----------
$pdf->SetXY(198, 287);
$wl = isset($Search['Weight_Loss_Porce']) ? (float)$Search['Weight_Loss_Porce'] : null;
$texto = ($wl !== null && $wl < 45.0) ? 'Passed' : 'Failed';
$pdf->Cell(33, 5, $texto, 0, 1, 'C');

// ---------- Comparison Information ----------
$pdf->SetXY(132, 314);
$pdf->Cell(33, 5, (string)($Search['Weight_Loss_Porce'] ?? ''), 0, 1, 'C');

// ---------- Comments and observations ----------
$pdf->SetXY(63, 354);
$pdf->MultiCell(130, 4, (string)($Search['Comments'] ?? ''), 0, 'L');

$pdf->SetXY(200, 354);
$pdf->MultiCell(130, 4, (string)($Search['FieldComment'] ?? ''), 0, 'L');

// === PDF en memoria (NO enviar inline aquí) ===
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
$test_type     = $Search['Test_Type'] ?? 'LAA'; // mantenemos LAA
$templateName  = 'LAA';                         // etiqueta para el filename

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
