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
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'error'=>'Falta id']);
  exit;
}

// === Buscar datos ===
$Search = find_by_id('los_angeles_abrasion_large', $id);
if (!$Search) {
  http_response_code(404);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'error'=>'Ensayo no encontrado']);
  exit;
}

// === Crear PDF (misma geometría/plantilla que tu script) ===
class PDF extends Fpdi { function Header(){} function Footer(){} }
$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', [420, 450]);

$root = realpath(__DIR__.'/..'); // raíz proyecto (carpeta padre de /pages)
$template = $root . '/pdf/template/PV-F-01715 Laboratory Los Angeles Abrasion for Coarse Filtes-CF.pdf';
if (!file_exists($template)) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'error'=>'Plantilla no encontrada']);
  exit;
}

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// ---------- Information for the test ----------
$pdf->SetXY(75, 90);
$pdf->Cell(40, 5, (string)($Search['Project_Name'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 111);
$pdf->Cell(40, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(75, 118);
$pdf->Cell(40, 5, (string)($Search['Technician'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 125);
$pdf->Cell(40, 5, (string)($Search['Sample_By'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 145);
$pdf->Cell(40, 6, (string)($Search['Structure'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 151);
$pdf->Cell(40, 6, (string)($Search['Area'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 159);
$pdf->Cell(40, 6, (string)($Search['Source'] ?? ''), 0, 1, 'C');
$pdf->SetXY(75, 166);
$pdf->Cell(40, 6, (string)($Search['Material_Type'] ?? ''), 0, 1, 'C');

$pdf->SetXY(175, 90);
$pdf->Cell(40, 5, (string)($Search['Project_Number'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 111);
$pdf->Cell(40, 6, (string)($Search['Standard'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 118);
$pdf->Cell(40, 6, (string)($Search['Test_Start_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 125);
$pdf->Cell(40, 6, (string)($Search['Registed_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 145);
$pdf->Cell(40, 6, (string)($Search['Sample_ID'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 151);
$pdf->Cell(40, 6, (string)($Search['Sample_Number'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 159);
$pdf->Cell(40, 6, (string)($Search['Sample_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(175, 166);
$pdf->Cell(40, 6, (string)($Search['Elev'] ?? ''), 0, 1, 'C');

$pdf->SetXY(285, 90);
$pdf->Cell(40, 5, (string)($Search['Client'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 111);
$pdf->Cell(40, 6, (string)($Search['Methods'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 118);
$pdf->Cell(40, 6, (string)($Search['Preparation_Method'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 125);
$pdf->Cell(40, 6, (string)($Search['Split_Method'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 145);
$pdf->Cell(40, 6, (string)($Search['Depth_From'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 151);
$pdf->Cell(40, 6, (string)($Search['Depth_To'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 159);
$pdf->Cell(40, 6, (string)($Search['North'] ?? ''), 0, 1, 'C');
$pdf->SetXY(285, 166);
$pdf->Cell(40, 6, (string)($Search['East'] ?? ''), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// ---------- Test Information and values ----------
$pdf->SetXY(199, 205);
$pdf->Cell(33, 8, (string)($Search['NominalMaxSize'] ?? ''), 0, 1, 'C');
$pdf->SetXY(199, 213);
$pdf->Cell(33, 8, (string)($Search['Grading'] ?? ''), 0, 1, 'C');
$pdf->SetXY(199, 221);
$pdf->Cell(33, 8, (string)($Search['NoSpheres'] ?? ''), 0, 1, 'C');
$pdf->SetXY(199, 229);
$pdf->Cell(33, 8, (string)($Search['Weight_Spheres'] ?? ''), 0, 1, 'C');
$pdf->SetXY(199, 237);
$pdf->Cell(33, 8, (string)($Search['Revolutions'] ?? ''), 0, 1, 'C');
$pdf->SetXY(199, 245);
$pdf->Cell(33, 8, (string)($Search['Initial_Weight'] ?? ''), 0, 1, 'C');
$pdf->SetXY(199, 254);
$pdf->Cell(33, 8, (string)($Search['Final_Weight'] ?? ''), 0, 1, 'C');
$pdf->SetXY(199, 262);
$pdf->Cell(33, 8, (string)($Search['Weight_Loss'] ?? ''), 0, 1, 'C');
$pdf->SetXY(199, 270);
$pdf->Cell(33, 8, (string)($Search['Weight_Loss_Porce'] ?? ''), 0, 1, 'C');

// ---------- Test Results ----------
$pdf->SetXY(199, 291);
$wl = isset($Search['Weight_Loss_Porce']) ? (float)$Search['Weight_Loss_Porce'] : null;
$texto = ($wl !== null && $wl < 45.0) ? 'Passed' : 'Failed';
$pdf->Cell(33, 5, $texto, 0, 1, 'C');

// ---------- Comparison Information ----------
$pdf->SetXY(130, 318);
$pdf->Cell(35, 5, (string)($Search['Weight_Loss_Porce'] ?? ''), 0, 1, 'C');

// ---------- Comments and observations ----------
$pdf->SetXY(61, 358);
$pdf->MultiCell(130, 4, (string)($Search['Comments'] ?? ''), 0, 'L');

$pdf->SetXY(201, 358);
$pdf->MultiCell(130, 4, (string)($Search['FieldComment'] ?? ''), 0, 'L');

// === PDF en memoria (NO enviar inline) ===
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes) < 1000) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'error'=>'No se pudo generar PDF']);
  exit;
}

// === Versionado / registro (MISMO ESQUEMA) ===
$sample_id     = $Search['Sample_ID'] ?? '';
$sample_number = $Search['Sample_Number'] ?? '';
$test_type     = $Search['Test_Type'] ?? 'LAA';    // mantenemos LAA
$templateName  = 'LAA-Large';                      // etiqueta de plantilla/ensayo

$max = find_by_sql(sprintf(
  "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
  $db->escape($sample_id),
  $db->escape($sample_number),
  $db->escape($test_type)
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
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'ok'       => true,
  'filename' => $filename,
  'path'     => $rel,
  'version'  => $nextVer
]);
exit;
