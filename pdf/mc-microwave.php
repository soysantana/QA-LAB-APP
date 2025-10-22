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
$Search = find_by_id('moisture_microwave', $id);
if (!$Search) {
  http_response_code(404);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Ensayo no encontrado']);
  exit;
}

// === Crear PDF (misma geometría/plantilla que tu script) ===
class PDF extends Fpdi { public function Header(){} public function Footer(){} }
$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', [300, 250]);

$root = realpath(__DIR__.'/..'); // raíz del proyecto (carpeta padre de /pages)
$template = $root . '/pdf/template/PV-F-83834_Laboratory Moisture Content by Microwave_Rev 1.pdf';
if (!file_exists($template)) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Plantilla no encontrada']);
  exit;
}

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// ----- Dibujo de campos (idéntico a tu versión) -----
$pdf->SetFont('Arial', '', 11);

// LAB info
$pdf->SetXY(67, 33);
$pdf->Cell(30, 5, 'PVDJ SOIL LAB', 0, 1, 'C');
$pdf->SetXY(67, 37);
$pdf->Cell(30, 5, (string)($Search['Technician'] ?? ''), 0, 1, 'C');
$pdf->SetXY(67, 42);
$pdf->Cell(30, 5, (string)($Search['Sample_By'] ?? ''), 0, 1, 'C');

// Método y fechas
$pdf->SetXY(185, 30);
$pdf->Cell(30, 6, (string)($Search['Method'] ?? ''), 0, 1, 'C');
$pdf->SetXY(185, 36);
$pdf->Cell(30, 6, (string)($Search['Test_Start_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(185, 42);
$pdf->Cell(30, 6, (string)($Search['Registed_Date'] ?? ''), 0, 1, 'C');

// Sample info
$pdf->SetXY(135, 58);
$pdf->Cell(81, 6, (string)($Search['Structure'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 64);
$pdf->Cell(81, 6, (string)($Search['Area'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 69);
$pdf->Cell(81, 6, (string)($Search['Source'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 76);
$pdf->Cell(81, 6, (string)($Search['Sample_Date'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 82.5);
$pdf->Cell(81, 6, (string)($Search['Sample_ID'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 89);
$pdf->Cell(81, 6, (string)($Search['Sample_Number'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 94.5);
$pdf->Cell(81, 6, (string)($Search['Material_Type'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 100);
$pdf->Cell(81, 6, (string)($Search['Depth_From'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 105);
$pdf->Cell(81, 6, (string)($Search['Depth_To'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 111);
$pdf->Cell(81, 6, (string)($Search['North'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 116);
$pdf->Cell(81, 6, (string)($Search['East'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 122.5);
$pdf->Cell(81, 6, (string)($Search['Elev'] ?? ''), 0, 1, 'C');

// Datos de ensayo específicos Microwave
$pdf->SetXY(135, 146);
$pdf->Cell(81, 6, (string)($Search['Tare_Name'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 152);
$pdf->Cell(81, 6, (string)($Search['Microwave_Model'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 158);
$pdf->Cell(81, 6, '', 0, 1, 'C');
$pdf->SetXY(135, 164);
$pdf->Cell(81, 6, '', 0, 1, 'C');
$pdf->SetXY(135, 169.5);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 175);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 181);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 187);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_3'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 192);
$pdf->Cell(81, 6, (string)($Search['Tare_Plus_Wet_Soil_4'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 198);
$pdf->Cell(81, 6, (string)($Search['Water_Ww'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 203);
$pdf->Cell(81, 6, (string)($Search['Tare'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 208.5);
$pdf->Cell(81, 6, (string)($Search['Dry_Soil'] ?? ''), 0, 1, 'C');
$pdf->SetXY(135, 214);
$pdf->Cell(81, 6, (string)($Search['Moisture_Content_Porce'] ?? ''), 0, 1, 'C');

// Comentarios
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(22, 232);
$pdf->Cell(180, 4, (string)($Search['Comments'] ?? ''), 0, 1, 'L');

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
$test_type     = $Search['Test_Type'] ?? 'MC';    // misma convención
$templateName  = 'MC-Microwave';                  // etiqueta de plantilla/ensayo

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
