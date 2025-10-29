<?php
declare(strict_types=1);

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

/* ===================== Helpers ===================== */
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
  // Sanitiza para nombres de archivo y rutas
  return preg_replace('/[^A-Za-z0-9\-_.]/', '-', trim((string)$v) === '' ? 'NA' : (string)$v);
}

function insertarImagenBase64(Fpdi $pdf, ?string $base64Str, float $x, float $y, float $w, float $h = 0): void {
  if (!$base64Str) return;
  $base64Str = preg_replace('#^data:image/\w+;base64,#i', '', $base64Str);
  $imageData = base64_decode($base64Str, true);
  if ($imageData === false) return;
  $tmpFile = tempnam(sys_get_temp_dir(), 'img') . '.png';
  if (file_put_contents($tmpFile, $imageData) !== false) {
    $pdf->Image($tmpFile, $x, $y, $w, $h);
    @unlink($tmpFile);
  }
}

/* ===================== Entrada ===================== */
page_require_level(2);

$id   = $_GET['id']   ?? '';
$mode = $_GET['mode'] ?? 'json'; // 'json' | 'silent'
if ($id === '') json_error(400, 'Falta id');

$Search = find_by_id('atterberg_limit', $id);
if (!$Search) json_error(404, 'Ensayo no encontrado');

// Leer JSON (gráficos base64 opcionales)
$raw   = file_get_contents('php://input');
$input = $raw ? json_decode($raw, true) : null;
$liquidlimit = $input['liquidlimit'] ?? null;
$plasticity  = $input['plasticity']  ?? null;

/* ===================== PDF ===================== */
class PDF extends Fpdi { function Header(){} function Footer(){} }

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('L', [420, 330]); // Tamaño que usas en tu script

$root = realpath(__DIR__ . '/..'); // raíz del proyecto
$template = $root . '/pdf/template/PV-F-01718 Laboratory Atterberg Limit Test_Rev2.pdf';
// Si tu plantilla está en /template directamente, cambia la ruta a:
// $template = $root . '/template/PV-F-01718 Laboratory Atterberg Limit Test_Rev2.pdf';

if (!file_exists($template)) json_error(500, 'Plantilla no encontrada: ' . basename($template));

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Fuente
$pdf->SetFont('Arial', '', 12);

/* ===================== Campos de cabecera ===================== */
$fields = [
  [58,  36,  (string)($Search['Project_Name'] ?? '')],
  [58,  54,  "PVDJ Soil Lab"],
  [58,  60,  (string)($Search['Technician'] ?? '')],
  [58,  67,  (string)($Search['Sample_By'] ?? '')],

  [58,  83,  (string)($Search['Structure'] ?? '')],
  [58,  88,  (string)($Search['Area'] ?? '')],
  [58,  93,  (string)($Search['Source'] ?? '')],
  [58,  98,  (string)($Search['Material_Type'] ?? '')],

  [175, 36,  (string)($Search['Project_Number'] ?? '')],
  [175, 54,  (string)($Search['Standard'] ?? '')],
  [175, 60,  (string)($Search['Test_Start_Date'] ?? '')],
  [175, 67,  (string)($Search['Registed_Date'] ?? '')],

  [175, 83,  (string)($Search['Sample_ID'] ?? '')],
  [175, 88,  (string)($Search['Sample_Number'] ?? '')],
  [175, 93,  (string)($Search['Sample_Date'] ?? '')],
  [175, 98,  (string)($Search['Elev'] ?? '')],
  [175, 104, (string)($Search['Nat_Mc'] ?? '')],

  [285, 36,  (string)($Search['Client'] ?? '')],
  [285, 54,  ""],
  [285, 60,  (string)($Search['Preparation_Method'] ?? '')],
  [285, 67,  (string)($Search['Split_Method'] ?? '')],

  [285, 83,  (string)($Search['Depth_From'] ?? '')],
  [285, 89,  (string)($Search['Depth_To'] ?? '')],
  [285, 95,  (string)($Search['North'] ?? '')],
  [285, 100, (string)($Search['East'] ?? '')],
];

foreach ($fields as [$x, $y, $value]) {
  $pdf->SetXY($x, $y);
  $pdf->Cell(30, 1, $value, 0, 1, 'C');
}

/* ===================== Liquid Limit ===================== */
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(68, 128);  $pdf->Cell(35, 4, (string)($Search['LL_Blows_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,128);  $pdf->Cell(28, 4, (string)($Search['LL_Blows_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,128);  $pdf->Cell(24, 4, (string)($Search['LL_Blows_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 136);  $pdf->Cell(35, 4, (string)($Search['LL_Container_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,136);  $pdf->Cell(28, 4, (string)($Search['LL_Container_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,136);  $pdf->Cell(24, 4, (string)($Search['LL_Container_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 145);  $pdf->Cell(35, 4, (string)($Search['LL_Wet_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,145);  $pdf->Cell(28, 4, (string)($Search['LL_Wet_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,145);  $pdf->Cell(24, 4, (string)($Search['LL_Wet_Soil_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 153);  $pdf->Cell(35, 4, (string)($Search['LL_Dry_Soil_Tare_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,153);  $pdf->Cell(28, 4, (string)($Search['LL_Dry_Soil_Tare_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,153);  $pdf->Cell(24, 4, (string)($Search['LL_Dry_Soil_Tare_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 161);  $pdf->Cell(35, 4, (string)($Search['LL_Water_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,161);  $pdf->Cell(28, 4, (string)($Search['LL_Water_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,161);  $pdf->Cell(24, 4, (string)($Search['LL_Water_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 170);  $pdf->Cell(35, 4, (string)($Search['LL_Tare_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,170);  $pdf->Cell(28, 4, (string)($Search['LL_Tare_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,170);  $pdf->Cell(24, 4, (string)($Search['LL_Tare_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 178);  $pdf->Cell(35, 4, (string)($Search['LL_Wt_Dry_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,178);  $pdf->Cell(28, 4, (string)($Search['LL_Wt_Dry_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,178);  $pdf->Cell(24, 4, (string)($Search['LL_Wt_Dry_Soil_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 187);  $pdf->Cell(35, 4, (string)($Search['LL_MC_Porce_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,187);  $pdf->Cell(28, 4, (string)($Search['LL_MC_Porce_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,187);  $pdf->Cell(24, 4, (string)($Search['LL_MC_Porce_3'] ?? ''), 0, 1, 'C');

/* ===================== Plastic Limit ===================== */
$pdf->SetXY(68, 215);  $pdf->Cell(35, 4, (string)($Search['PL_Container_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,215);  $pdf->Cell(28, 4, (string)($Search['PL_Container_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,215);  $pdf->Cell(24, 4, (string)($Search['PL_Container_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 222);  $pdf->Cell(35, 4, (string)($Search['PL_Wet_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,222);  $pdf->Cell(28, 4, (string)($Search['PL_Wet_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,222);  $pdf->Cell(24, 4, (string)($Search['PL_Wet_Soil_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 229);  $pdf->Cell(35, 4, (string)($Search['PL_Dry_Soil_Tare_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,229);  $pdf->Cell(28, 4, (string)($Search['PL_Dry_Soil_Tare_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,229);  $pdf->Cell(24, 4, (string)($Search['PL_Dry_Soil_Tare_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 236);  $pdf->Cell(35, 4, (string)($Search['PL_Water_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,236);  $pdf->Cell(28, 4, (string)($Search['PL_Water_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,236);  $pdf->Cell(24, 4, (string)($Search['PL_Water_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 243);  $pdf->Cell(35, 4, (string)($Search['PL_Tare_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,243);  $pdf->Cell(28, 4, (string)($Search['PL_Tare_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,243);  $pdf->Cell(24, 4, (string)($Search['PL_Tare_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 250);  $pdf->Cell(35, 4, (string)($Search['PL_Wt_Dry_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(104,250);  $pdf->Cell(28, 4, (string)($Search['PL_Wt_Dry_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(139,250);  $pdf->Cell(24, 4, (string)($Search['PL_Wt_Dry_Soil_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(68, 257);  $pdf->Cell(35, 4, (string)($Search['PL_MC_Porce_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,257);  $pdf->Cell(35, 4, (string)($Search['PL_MC_Porce_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(134,257);  $pdf->Cell(35, 4, (string)($Search['PL_MC_Porce_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(100, 264); $pdf->Cell(35, 4, (string)($Search['PL_Avg_Mc_Porce'] ?? ''), 0, 1, 'C');

/* ===================== Resumen/Status ===================== */
$Material = (string)($Search['Material_Type'] ?? '');
$IP       = isset($Search['Plasticity_Index_Porce']) ? (float)$Search['Plasticity_Index_Porce'] : 0.0;

$status = '';
if ($Material === 'LPF') {
  $status = ($IP >= 14.5) ? 'Passed' : 'Failed';
} elseif ($Material === 'IRF') {
  $status = ($IP <= 7) ? 'Passed' : 'Failed';
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(349, 120);  $pdf->Cell(24, 4, (string)($Search['Liquid_Limit_Porce'] ?? ''),     0, 1, 'C');
$pdf->SetXY(349, 128);  $pdf->Cell(24, 4, (string)($Search['Plastic_Limit_Porce'] ?? ''),    0, 1, 'C');
$pdf->SetXY(349, 136);  $pdf->Cell(24, 4, (string)($Search['Plasticity_Index_Porce'] ?? ''), 0, 1, 'C');
$pdf->SetXY(349, 145);  $pdf->Cell(24, 4, (string)($Search['Liquidity_Index_Porce'] ?? ''),  0, 1, 'C');
$pdf->SetXY(349, 153);  $pdf->Cell(24, 4, $status, 0, 1, 'C');

$pdf->SetXY(139, 278.5);
$pdf->Cell(24, 4, (string)($Search['Classification'] ?? ''), 0, 1, 'C');

/* ====== Comparison information (según tu layout) ====== */
$pdf->SetXY(349, 179);    $pdf->Cell(24, 4, (string)($Search['Liquid_Limit_Porce'] ?? ''),     0, 1, 'C');
$pdf->SetXY(349, 187);    $pdf->Cell(24, 4, (string)($Search['Plastic_Limit_Porce'] ?? ''),    0, 1, 'C');
$pdf->SetXY(349, 194.5);  $pdf->Cell(24, 4, (string)($Search['Plasticity_Index_Porce'] ?? ''), 0, 1, 'C');
$pdf->SetXY(349, 201);    $pdf->Cell(24, 4, (string)($Search['Liquidity_Index_Porce'] ?? ''),  0, 1, 'C');

/* ===================== Comentarios ===================== */
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(313, 220);
$pdf->MultiCell(86, 4, (string)($Search['Comments'] ?? ''), 0, 'L');
$pdf->SetXY(313, 263);
$pdf->MultiCell(86, 4, (string)($Search['FieldComment'] ?? ''), 0, 'L');

/* ===================== Gráficas (base64) ===================== */
insertarImagenBase64($pdf, $liquidlimit, 190, 110, 95, 0); // Límite líquido
insertarImagenBase64($pdf, $plasticity,  190, 200, 95, 0); // Índice de plasticidad

/* ===================== Generar en memoria ===================== */
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes) < 1000) json_error(500, 'No se pudo generar el PDF');

/* ===================== Guardado versionado + doc_files ===================== */
global $db; // proviene de load.php

$sample_id     = (string)($Search['Sample_ID']     ?? '');
$sample_number = (string)($Search['Sample_Number'] ?? '');
$test_type     = (string)($Search['Test_Type']     ?? 'AL');
$templateName  = 'AL-Rev2';

// calcular próxima versión
$max = find_by_sql(sprintf(
  "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
  $db->escape($sample_id),
  $db->escape($sample_number),
  $db->escape($test_type)
));
$nextVer = (int)($max[0]['v'] ?? 0) + 1;

// ruta
$dir = $root . '/uploads/results/' . date('Y/m');
ensure_dir($dir);

$filename = sprintf('%s-%s-%s-v%d.pdf', s($sample_id), s($sample_number), s($templateName), $nextVer);
$abs = $dir . '/' . $filename;
if (file_put_contents($abs, $pdfBytes) === false) {
  json_error(500, 'No se pudo escribir el archivo en disco');
}
$rel = str_replace($root, '', $abs);

// insertar registro en doc_files
$db->query(sprintf(
  "INSERT INTO doc_files (sample_id, sample_number, test_type, template, version, source, file_path, file_name, status)
   VALUES ('%s','%s','%s','%s',%d,'system','%s','%s','awaiting_signature')",
  $db->escape($sample_id),
  $db->escape($sample_number),
  $db->escape($test_type),
  $db->escape($templateName),
  $nextVer,
  $db->escape($rel),
  $db->escape($filename)
));

/* ===================== Salida ===================== */
if ($mode === 'silent') {
  http_response_code(204); // No Content
  exit;
}

json_ok([
  'filename'       => $filename,
  'path'           => $rel,
  'version'        => $nextVer,
  'sample_id'      => $sample_id,
  'sample_number'  => $sample_number,
  'test_type'      => $test_type,
  'template'       => $templateName,
]);
