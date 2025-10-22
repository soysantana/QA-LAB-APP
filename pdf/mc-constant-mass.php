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
$Search = find_by_id('moisture_constant_mass', $id);
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
$pdf->AddPage('P', [320, 260]);  // Portrait 320x260

$root = realpath(__DIR__.'/..'); // raíz proyecto (carpeta padre de /pages)
$template = $root . '/pdf/template/PV-F-83815 Laboratory Moisture Content Constant Mass.pdf';
if (!file_exists($template)) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Plantilla no encontrada']);
  exit;
}

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// ----- Campos (idénticos a tu versión) -----
$pdf->SetFont('Arial', '', 11);

// Encabezado/LAB
$pdf->SetXY(65, 45);
$pdf->Cell(30, 6, (string)($Search['Technician'] ?? ''), 0, 1, 'C');
$pdf->SetXY(65, 51);
$pdf->Cell(30, 6, (string)($Search['Sample_By'] ?? ''), 0, 1, 'C');

$pdf->SetXY(200, 39);
$pdf->Cell(30, 6, (string)($Search['Method'] ?? ''), 0, 1, 'C');
$pdf->SetXY(200, 45);
$pdf->Cell(30, 6, (string)($Search['Test_Start_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(200, 51);
$regDate = '';
if (!empty($Search['Registed_Date'])) {
  $ts = strtotime((string)$Search['Registed_Date']);
  $regDate = $ts ? date('Y-m-d', $ts) : (string)$Search['Registed_Date'];
}
$pdf->Cell(30, 6, $regDate, 0, 1, 'C');

// Sample info
$pdf->SetXY(163, 65);
$pdf->Cell(81, 6, (string)($Search['Structure'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 70);
$pdf->Cell(81, 6, (string)($Search['Area'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 76);
$pdf->Cell(81, 6, (string)($Search['Source'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 83);
$pdf->Cell(81, 6, (string)($Search['Sample_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 89);
$pdf->Cell(81, 6, (string)($Search['Sample_ID'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 95);
$pdf->Cell(81, 6, (string)($Search['Sample_Number'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 100);
$pdf->Cell(81, 6, (string)($Search['Material_Type'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 106);
$pdf->Cell(81, 6, (string)($Search['Depth_From'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 111);
$pdf->Cell(81, 6, (string)($Search['Depth_To'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 116);
$pdf->Cell(81, 6, (string)($Search['North'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 121);
$pdf->Cell(81, 6, (string)($Search['East'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 126);
$pdf->Cell(81, 6, (string)($Search['Elev'] ?? ''), 0, 1, 'C');

// Datos de ensayo
$pdf->SetXY(163, 143);
$pdf->Cell(81, 6, '1', 0, 1, 'C');
$pdf->SetXY(163, 149);
$pdf->Cell(81, 6, (string)($Search['Tare_Name'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 155);
$pdf->Cell(81, 6, (string)($Search['Temperature'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 161);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 168);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 174);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 179);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_3'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 185);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_4'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 191);
$pdf->Cell(81, 6, (string)($Search['Water_Ww'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 196);
$pdf->Cell(81, 6, (string)($Search['Tare'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 202);
$pdf->Cell(81, 6, (string)($Search['Dry_Soil'] ?? ''), 0, 1, 'C');
$pdf->SetXY(163, 208);
$pdf->Cell(81, 6, (string)($Search['Moisture_Content_Porce'] ?? ''), 0, 1, 'C');

// Comentarios
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(15, 210);
$pdf->Cell(229, 45, (string)($Search['Comments'] ?? ''), 0, 1, 'L');

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
$test_type     = $Search['Test_Type'] ?? 'MC';     // seguimos tu convención
$templateName  = 'MC-ConstantMass';               // etiqueta de plantilla/ensayo

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
