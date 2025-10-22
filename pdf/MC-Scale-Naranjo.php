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
$pdf->AddPage('P', [300,220]);

$root = realpath(__DIR__.'/..'); // /pdf -> raíz proyecto
$template = $root.'/pdf/template/PV-F-01714_Laboratory Moisture Content with Scale_Rev 1.pdf';
if (!file_exists($template)) { http_response_code(500); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Plantilla no encontrada']); exit; }

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// ----- Dibuja tus campos (igual que ya tenías) -----
$pdf->SetFont('Arial','',10);
$fields = [
  [52,38,'PVDJ SOIL LAB'],
  [52,43,$Search['Technician'] ?? ''],
  [52,49,$Search['Sample_By'] ?? ''],
  [150,38,$Search['Methods'] ?? '','C'],
  [150,43,$Search['Test_Start_Date'] ?? '','C'],
  [150,49,$Search['Registed_Date'] ?? '','C'],
];
foreach ($fields as $f){ $pdf->SetXY($f[0],$f[1]); $pdf->Cell(30,1,(string)$f[2],0,1,$f[3]??'L'); }

$pdf->SetFont('Arial','',11);
$more = [
  [107, 64,   $Search['Structure'] ?? ''],
  [107, 70,   $Search['Area'] ?? ''],
  [107, 74.5, $Search['Source'] ?? ''],
  [107, 80,   $Search['Sample_Date'] ?? ''],
  [107, 85,   $Search['Sample_ID'] ?? ''],
  [107, 89.5, $Search['Sample_Number'] ?? ''],
  [107, 95,   $Search['Material_Type'] ?? ''],
  [107, 99.5, $Search['Depth_From'] ?? ''],
  [107, 104.5,$Search['Depth_To'] ?? ''],
  [107, 109.5,$Search['North'] ?? ''],
  [107, 115,  $Search['East'] ?? ''],
  [107, 120,  $Search['Elev'] ?? ''],
  [107, 133.6,'1'],
  [107, 138.5,$Search['Tare_Name'] ?? ''],
  [107, 144,  $Search['Moisture_Content_Porce'] ?? ''],
];
foreach ($more as $m){ $pdf->SetXY($m[0],$m[1]); $pdf->Cell(81,1,(string)$m[2],0,1,'C'); }

$pdf->SetFont('Arial','',12);
$pdf->SetXY(19,165);
$pdf->Cell(150,4,(string)($Search['Comments'] ?? ''),0,1,'L');

// === en memoria, NO descargar ===
$pdfBytes = $pdf->Output('S');
if (!$pdfBytes || strlen($pdfBytes)<1000){ http_response_code(500); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'No se pudo generar PDF']); exit; }

// === versionado: calcula próxima versión ===
$sample_id     = $Search['Sample_ID']   ?? '';
$sample_number = $Search['Sample_Number'] ?? '';
$test_type     = $Search['Test_Type']   ?? 'MC';
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
