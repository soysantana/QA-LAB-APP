<?php
declare(strict_types=1);

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

page_require_level(2);

function ensure_dir(string $dir){ if(!is_dir($dir)) mkdir($dir, 0775, true); }

$id = $_GET['id'] ?? '';
if ($id==='') { http_response_code(400); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Falta id']); exit; }

$Search = find_by_id('moisture_scale', $id);
if (!$Search) { http_response_code(404); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Ensayo no encontrado']); exit; }

class PDF extends Fpdi {}
$pdf = new PDF();
$pdf->SetMargins(0,0,0);
$pdf->AddPage('L', [340,250]);

$root = realpath(__DIR__.'/..');
$template = $root.'/pdf/template/PV-F-01714 Laboratory Moisture Content with Scale.pdf';
if (!file_exists($template)) { http_response_code(500); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Plantilla no encontrada']); exit; }

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// ----- Dibuja tus campos -----
$pdf->SetFont('Arial','',10);
$fields = [
  [80, 41.5, $Search['Project_Name'] ?? ''],
  [80, 57, 'PVDJ SOIL LAB'],
  [80, 62.5, $Search['Technician'] ?? ''],
  [80, 68, $Search['Sample_By'] ?? ''],
  [158, 41.5, $Search['Project_Number'] ?? ''],
  [158, 62.5, $Search['Test_Start_Date'] ?? ''],
  [158, 68, $Search['Registed_Date'] ?? ''],
  [250, 41.5, $Search['Client'] ?? ''],
  [250, 57, $Search['Methods'] ?? '']
];
foreach ($fields as $f){ $pdf->SetXY($f[0],$f[1]); $pdf->Cell(50,1,(string)$f[2],0,1,'L'); }

$pdf->SetFont('Arial','',11);
$more = [
  [80, 84, $Search['Structure'] ?? ''],
  [80, 90, $Search['Area'] ?? ''],
  [80, 97, $Search['Source'] ?? ''],
  [80, 103, $Search['Material_Type'] ?? ''],
  [158, 84, $Search['Sample_ID'] ?? ''],
  [158, 90, $Search['Sample_Number'] ?? ''],
  [158, 97, $Search['Sample_Date'] ?? ''],
  [158, 103, $Search['Elev'] ?? ''],
  [250, 84, $Search['Depth_From'] ?? ''],
  [250, 90, $Search['Depth_To'] ?? ''],
  [250, 97, $Search['North'] ?? ''],
  [250, 103, $Search['East'] ?? ''],
  [120, 130, '1'],
  [120, 134.5, $Search['Moisture_Content_Porce'] ?? '']
];
foreach ($more as $m){ $pdf->SetXY($m[0],$m[1]); $pdf->Cell(81,1,(string)$m[2],0,1,'C'); }

$pdf->SetFont('Arial','',12);
$pdf->SetXY(50,165);
$pdf->Cell(145,4,(string)($Search['Comments'] ?? ''),0,1,'L');

// === en memoria ===
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes)<1000){ http_response_code(500); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'No se pudo generar PDF']); exit; }

// === versionado ===
$sample_id     = $Search['Sample_ID'] ?? '';
$sample_number = $Search['Sample_Number'] ?? '';
$test_type     = $Search['Test_Type'] ?? 'MC';
$templateName  = 'MC-Scale';

$max = find_by_sql(sprintf(
  "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
  $db->escape($sample_id), $db->escape($sample_number), $db->escape($test_type)
));
$nextVer = (int)($max[0]['v'] ?? 0) + 1;

// === guardar en disco ===
$dir = $root.'/uploads/results/'.date('Y/m');
ensure_dir($dir);
$filename = sprintf('%s_%s_%s_v%d.pdf',
  preg_replace('/[^A-Za-z0-9\-_.]/','-',$sample_id ?: 'NA'),
  preg_replace('/[^A-Za-z0-9\-_.]/','-',$sample_number ?: 'NA'),
  $templateName,
  $nextVer
);
$abs = $dir.'/'.$filename;
file_put_contents($abs, $pdfBytes);
$rel = str_replace($root, '', $abs);

// === registrar en BD ===
$db->query(sprintf(
  "INSERT INTO doc_files (sample_id,sample_number,test_type,template,version,source,file_path,file_name,status)
   VALUES ('%s','%s','%s','%s',%d,'system','%s','%s','awaiting_signature')",
  $db->escape($sample_id), $db->escape($sample_number), $db->escape($test_type),
  $db->escape($templateName), $nextVer, $db->escape($rel), $db->escape($filename)
));

// === responder JSON ===
header('Content-Type: application/json');
echo json_encode(['ok'=>true,'filename'=>$filename,'path'=>$rel,'version'=>$nextVer]);
exit;
