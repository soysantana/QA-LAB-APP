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
$Search = find_by_id('moisture_oven', $id);
if (!$Search) {
  http_response_code(404);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Ensayo no encontrado']);
  exit;
}

// === Crear PDF (misma geometría y plantilla que tu código original) ===
class PDF extends Fpdi {}
$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', [300, 250]); // Portrait, 300x250

$root = realpath(__DIR__.'/..'); // raíz del proyecto (carpeta padre de /pages)
$template = $root . '/pdf/template/PV-F-81248 Laboratory Moisture Content by Oven.pdf';
if (!file_exists($template)) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'Plantilla no encontrada']);
  exit;
}

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// ----- Dibujo de campos (idéntico a tu script original) -----
$pdf->SetFont('Arial', '', 10);

// Encabezado/LAB info
$pdf->SetXY(52, 32);
$pdf->Cell(30, 5, (string)('PVDJ SOIL LAB'), 0, 1, 'L');

$pdf->SetXY(52, 37);
$pdf->Cell(30, 5, (string)($Search['Technician'] ?? ''), 0, 1, 'L');

$pdf->SetXY(52, 42);
$pdf->Cell(30, 5, (string)($Search['Sample_By'] ?? ''), 0, 1, 'L');

$pdf->SetXY(170, 30);
$pdf->Cell(30, 6, (string)($Search['Method'] ?? ''), 0, 1, 'C');

$pdf->SetXY(170, 36);
$pdf->Cell(30, 6, (string)($Search['Test_Start_Date'] ?? ''), 0, 1, 'C');

$pdf->SetXY(170, 42);
$regDate = '';
if (!empty($Search['Registed_Date'])) {
  $ts = strtotime((string)$Search['Registed_Date']);
  $regDate = $ts ? date('Y-m-d', $ts) : (string)$Search['Registed_Date'];
}
$pdf->Cell(30, 6, $regDate, 0, 1, 'C');

// Sample info
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(120, 58);
$pdf->Cell(81, 6, (string)($Search['Structure'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 64);
$pdf->Cell(81, 6, (string)($Search['Area'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 69);
$pdf->Cell(81, 6, (string)($Search['Source'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 75);
$pdf->Cell(81, 6, (string)($Search['Sample_Date'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 81);
$pdf->Cell(81, 6, (string)($Search['Sample_ID'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 87);
$pdf->Cell(81, 6, (string)($Search['Sample_Number'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 93);
$pdf->Cell(81, 6, (string)($Search['Material_Type'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 99);
$pdf->Cell(81, 6, (string)($Search['Depth_From'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 104);
$pdf->Cell(81, 6, (string)($Search['Depth_To'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 109);
$pdf->Cell(81, 6, (string)($Search['North'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 115);
$pdf->Cell(81, 6, (string)($Search['East'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 120);
$pdf->Cell(81, 6, (string)($Search['Elev'] ?? ''), 0, 1, 'C');

// Datos de ensayo
$pdf->SetXY(120, 141);
$pdf->Cell(81, 1, '1', 0, 1, 'C');

$pdf->SetXY(120, 147);
$pdf->Cell(81, 1, (string)($Search['Tare_Name'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 153);
// Si Temperature viene con UTF-8 y pone símbolos, puedes usar utf8_decode, pero cast a string igual:
$pdf->Cell(81, 1, (string)($Search['Temperature'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 158);
$pdf->Cell(81, 1, (string)($Search['Tare_Plus_Wet_Soil'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 163);
$pdf->Cell(81, 1, (string)($Search['Tare_Plus_Dry_Soil'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 169);
$pdf->Cell(81, 1, (string)($Search['Water_Ww'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 175);
$pdf->Cell(81, 1, (string)($Search['Tare_g'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 181);
$pdf->Cell(81, 1, (string)($Search['Dry_Soil_Ws'] ?? ''), 0, 1, 'C');

$pdf->SetXY(120, 186);
$pdf->Cell(81, 1, (string)($Search['Moisture_Content_Porce'] ?? ''), 0, 1, 'C');

// Comentarios
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(21, 210);
$pdf->Cell(170, 0, (string)($Search['Comments'] ?? ''), 0, 1, 'L');

// === PDF en memoria (NO descargar aquí) ===
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes) < 1000) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>'No se pudo generar PDF']);
  exit;
}

// === Datos base para versionado/registro ===
$sample_id     = $Search['Sample_ID'] ?? '';
$sample_number = $Search['Sample_Number'] ?? '';
$test_type     = $Search['Test_Type'] ?? 'MC'; // respeta tu esquema
$templateName  = 'MC-Oven';

// === Siguiente versión ===
$max = find_by_sql(sprintf(
  "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
  $db->escape($sample_id), $db->escape($sample_number), $db->escape($test_type)
));
$nextVer = (int)($max[0]['v'] ?? 0) + 1;

// === Guardar PDF en disco ===
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
$rel = str_replace($root, '', $abs); // p.ej: /uploads/results/2025/10/XXX.pdf

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

// === Responder JSON ===
header('Content-Type: application/json');
echo json_encode([
  'ok'       => true,
  'filename' => $filename,
  'path'     => $rel,
  'version'  => $nextVer
]);
exit;
