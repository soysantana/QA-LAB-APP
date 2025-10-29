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

// ---------- Entrada ----------
page_require_level(2);

$id   = $_GET['id']   ?? '';
$mode = $_GET['mode'] ?? 'json'; // 'json' | 'silent'
if ($id === '') json_error(400, 'Falta id');

$Search = find_by_id('atterberg_limit', $id);
if (!$Search) json_error(404, 'Ensayo no encontrado');

// Leer JSON recibido (opcional: gráficas en base64)
$raw = file_get_contents('php://input');
$input = $raw ? json_decode($raw, true) : null;
$liquidlimit = $input['liquidlimit'] ?? null;
$plasticity  = $input['plasticity']  ?? null;

// ---------- PDF ----------
class PDF extends Fpdi { function Header(){} function Footer(){} }

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('L', [370, 290]);

$root = realpath(__DIR__ . '/..'); // raíz del proyecto
$template = $root . '/pdf/template/PV-F-80769 Laboratory Atteberg Limits.pdf'; // usa el nombre exacto de tu archivo
if (!file_exists($template)) json_error(500, 'Plantilla no encontrada');

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Fuentes
$pdf->SetFont('Arial', 'B', 10);

// Campos de cabecera/datos generales
$fields = [
  [58,  36, "PVDJ Soil Lab"],
  [58,  43.5, (string)($Search['Technician'] ?? '')],
  [58,  50,   (string)($Search['Sample_By'] ?? '')],
  [165, 36,   (string)($Search['Standard'] ?? '')],
  [165, 43.5, (string)($Search['Test_Start_Date'] ?? '')],
  [165, 50,   (string)($Search['Registed_Date'] ?? '')],
  [267, 36,   ""],
  [267, 42,   (string)($Search['Split_Method'] ?? '')],
  [267, 48,   (string)($Search['Preparation_Method'] ?? '')],

  [58,  67,   (string)($Search['Structure'] ?? '')],
  [58,  72,   (string)($Search['Area'] ?? '')],
  [58,  77,   (string)($Search['Source'] ?? '')],
  [58,  82,   (string)($Search['Material_Type'] ?? '')],

  [165, 67,   (string)($Search['Sample_ID'] ?? '')],
  [165, 72,   (string)($Search['Sample_Number'] ?? '')],
  [165, 77,   (string)($Search['Sample_Date'] ?? '')],
  [165, 82,   (string)($Search['Elev'] ?? '')],
  [165, 88,   (string)($Search['Nat_Mc'] ?? '')],

  [267, 67,   (string)($Search['Depth_From'] ?? '')],
  [267, 72,   (string)($Search['Depth_To'] ?? '')],
  [267, 77,   (string)($Search['North'] ?? '')],
  [267, 82,   (string)($Search['East'] ?? '')],
];

foreach ($fields as [$x, $y, $val]) {
  $pdf->SetXY($x, $y);
  $pdf->Cell(30, 1, $val, 0, 1, 'C');
}

// Test Information - Liquid Limit
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(66, 112);  $pdf->Cell(35, 4, (string)($Search['LL_Blows_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,112);  $pdf->Cell(28, 4, (string)($Search['LL_Blows_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,112);  $pdf->Cell(24, 4, (string)($Search['LL_Blows_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 120);  $pdf->Cell(35, 4, (string)($Search['LL_Container_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,120);  $pdf->Cell(28, 4, (string)($Search['LL_Container_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,120);  $pdf->Cell(24, 4, (string)($Search['LL_Container_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 128);  $pdf->Cell(35, 4, (string)($Search['LL_Wet_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,128);  $pdf->Cell(28, 4, (string)($Search['LL_Wet_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,128);  $pdf->Cell(24, 4, (string)($Search['LL_Wet_Soil_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 136);  $pdf->Cell(35, 4, (string)($Search['LL_Dry_Soil_Tare_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,136);  $pdf->Cell(28, 4, (string)($Search['LL_Dry_Soil_Tare_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,136);  $pdf->Cell(24, 4, (string)($Search['LL_Dry_Soil_Tare_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 144);  $pdf->Cell(35, 4, (string)($Search['LL_Water_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,144);  $pdf->Cell(28, 4, (string)($Search['LL_Water_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,144);  $pdf->Cell(24, 4, (string)($Search['LL_Water_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 153);  $pdf->Cell(35, 4, (string)($Search['LL_Tare_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,153);  $pdf->Cell(28, 4, (string)($Search['LL_Tare_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,153);  $pdf->Cell(24, 4, (string)($Search['LL_Tare_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 161);  $pdf->Cell(35, 4, (string)($Search['LL_Wt_Dry_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,161);  $pdf->Cell(28, 4, (string)($Search['LL_Wt_Dry_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,161);  $pdf->Cell(24, 4, (string)($Search['LL_Wt_Dry_Soil_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 169);  $pdf->Cell(35, 4, (string)($Search['LL_MC_Porce_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,169);  $pdf->Cell(28, 4, (string)($Search['LL_MC_Porce_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,169);  $pdf->Cell(24, 4, (string)($Search['LL_MC_Porce_3'] ?? ''), 0, 1, 'C');

// Test Information - Plastic Limit
$pdf->SetXY(66, 194);   $pdf->Cell(35, 4, (string)($Search['PL_Container_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,194);   $pdf->Cell(28, 4, (string)($Search['PL_Container_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,194);   $pdf->Cell(24, 4, (string)($Search['PL_Container_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 201);   $pdf->Cell(35, 4, (string)($Search['PL_Wet_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,201);   $pdf->Cell(28, 4, (string)($Search['PL_Wet_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,201);   $pdf->Cell(24, 4, (string)($Search['PL_Wet_Soil_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 208);   $pdf->Cell(35, 4, (string)($Search['PL_Dry_Soil_Tare_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,208);   $pdf->Cell(28, 4, (string)($Search['PL_Dry_Soil_Tare_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,208);   $pdf->Cell(24, 4, (string)($Search['PL_Dry_Soil_Tare_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 214);   $pdf->Cell(35, 4, (string)($Search['PL_Water_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,214);   $pdf->Cell(28, 4, (string)($Search['PL_Water_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,214);   $pdf->Cell(24, 4, (string)($Search['PL_Water_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 221);   $pdf->Cell(35, 4, (string)($Search['PL_Tare_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,221);   $pdf->Cell(28, 4, (string)($Search['PL_Tare_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,221);   $pdf->Cell(24, 4, (string)($Search['PL_Tare_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 228);   $pdf->Cell(35, 4, (string)($Search['PL_Wt_Dry_Soil_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(100,228);   $pdf->Cell(28, 4, (string)($Search['PL_Wt_Dry_Soil_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(136,228);   $pdf->Cell(24, 4, (string)($Search['PL_Wt_Dry_Soil_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(66, 235);   $pdf->Cell(35, 4, (string)($Search['PL_MC_Porce_1'] ?? ''), 0, 1, 'C');
$pdf->SetXY(97, 235);   $pdf->Cell(35, 4, (string)($Search['PL_MC_Porce_2'] ?? ''), 0, 1, 'C');
$pdf->SetXY(131,235);   $pdf->Cell(35, 4, (string)($Search['PL_MC_Porce_3'] ?? ''), 0, 1, 'C');

$pdf->SetXY(97, 242);   $pdf->Cell(35, 4, (string)($Search['PL_Avg_Mc_Porce'] ?? ''), 0, 1, 'C');

// Sumario parámetros
$pdf->SetXY(328, 104);  $pdf->Cell(24, 4, (string)($Search['Liquid_Limit_Porce'] ?? ''),       0, 1, 'C');
$pdf->SetXY(328, 112);  $pdf->Cell(24, 4, (string)($Search['Plastic_Limit_Porce'] ?? ''),      0, 1, 'C');
$pdf->SetXY(328, 120);  $pdf->Cell(24, 4, (string)($Search['Plasticity_Index_Porce'] ?? ''),   0, 1, 'C');
$pdf->SetXY(328, 128);  $pdf->Cell(24, 4, (string)($Search['Liquidity_Index_Porce'] ?? ''),    0, 1, 'C');
$pdf->SetXY(328, 151);  $pdf->Cell(24, 4, (string)($Search['Classification'] ?? ''),           0, 1, 'C');

// Comentarios
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(293, 182);
$pdf->MultiCell(59, 4, (string)($Search['Comments'] ?? ''), 0, 'L');

// Gráficas (si llegaron en el body)
insertarImagenBase64($pdf, $liquidlimit, 182,  91, 88, 0); // Límite líquido
insertarImagenBase64($pdf, $plasticity,  182, 170, 88, 0); // Índice de plasticidad

// ====== Generar en memoria ======
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes) < 1000) json_error(500, 'No se pudo generar PDF');

// ====== Versionado + guardado + doc_files ======
$sample_id     = (string)($Search['Sample_ID']     ?? '');
$sample_number = (string)($Search['Sample_Number'] ?? '');
$test_type     = (string)($Search['Test_Type']     ?? 'AL');
$templateName  = 'AL';

// versión siguiente
$max = find_by_sql(sprintf(
  "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
  $db->escape($sample_id),
  $db->escape($sample_number),
  $db->escape($test_type)
));
$nextVer = (int)($max[0]['v'] ?? 0) + 1;

// ruta destino
$dir = $root . '/uploads/results/' . date('Y/m');
ensure_dir($dir);

$filename = sprintf('%s-%s-%s-v%d.pdf', s($sample_id), s($sample_number), s($templateName), $nextVer);
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
]);
