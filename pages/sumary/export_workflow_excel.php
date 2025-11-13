<?php
declare(strict_types=1);

$ROOT = realpath(__DIR__ . '/../../');
if ($ROOT === false) { http_response_code(500); die('No se pudo resolver la ruta base.'); }

require_once($ROOT . '/config/load.php');
require_once($ROOT . '/vendor/autoload.php');

date_default_timezone_set('America/Santo_Domingo');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/* ===== Helpers mínimos ===== */
function s($v): string { return is_null($v) ? '' : (string)$v; }
function cs(?string $t): string {
  if ($t === null) return '';
  $t = str_replace(["\xC2\xA0","\xA0","\t","\r","\n"], ' ', $t);
  return preg_replace('/\s{2,}/',' ', trim($t));
}

/* ===== Filtros GET ===== */
$anio     = isset($_GET['anio'])     ? trim((string)$_GET['anio'])     : '';
$mes      = isset($_GET['mes'])      ? trim((string)$_GET['mes'])      : '';
$cliente  = isset($_GET['cliente'])  ? trim((string)$_GET['cliente'])  : '';
$proyecto = isset($_GET['proyecto']) ? trim((string)$_GET['proyecto']) : '';
$q        = isset($_GET['q'])        ? trim((string)$_GET['q'])        : '';
$test     = isset($_GET['test'])     ? trim((string)$_GET['test'])     : '';

$where = [];
if ($anio   !== '') $where[] = "YEAR(r.Sample_Date) = '".$db->escape($anio)."'";
if ($mes    !== '') $where[] = "MONTH(r.Sample_Date) = '".(int)$mes."'";
if ($cliente!== '') $where[] = "r.Client = '".$db->escape($cliente)."'";
if ($proyecto!== '') $where[] = "r.Project_Number = '".$db->escape($proyecto)."'";
if ($q !== '') {
  $like = '%'.$db->escape($q).'%';
  $where[] = "(r.Sample_ID LIKE '{$like}' OR r.Sample_Number LIKE '{$like}' OR r.Sample_Name LIKE '{$like}' OR r.Test_Type LIKE '{$like}')";
}
if ($test !== '') {
  $where[] = "UPPER(TRIM(r.Test_Type)) = '".strtoupper($db->escape($test))."'";
}
$whereSQL = $where ? 'WHERE '.implode(' AND ', $where) : '';

/* ===== SQL: Consolidado (solo 3 etapas) =====
   Usamos solo Start_Date como fecha por etapa para evitar columnas ausentes.
*/
$sql = "
SELECT
  r.Sample_ID, r.Sample_Number, UPPER(TRIM(r.Test_Type)) AS Test_Type,
 r.Client, r.Project_Number, r.Material_Type, r.Sample_Date,

  p.Technician AS Prep_Tech, p.Status AS Prep_Status, p.stage_date AS Prep_Date,
  z.Technician AS Real_Tech, z.Status AS Real_Status, z.stage_date AS Real_Date,
  d.Technician AS Deli_Tech, d.Status AS Deli_Status, d.stage_date AS Deli_Date

FROM lab_test_requisition_form r

/* PREPARACIÓN */
LEFT JOIN (
  SELECT t1.*
  FROM test_preparation t1
  JOIN (
    SELECT Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type, MAX(Start_Date) AS max_dt
    FROM test_preparation
    GROUP BY Sample_ID, Sample_Number, UPPER(TRIM(Test_Type))
  ) m ON m.Sample_ID=t1.Sample_ID AND m.Sample_Number=t1.Sample_Number
      AND UPPER(TRIM(t1.Test_Type))=m.Test_Type AND t1.Start_Date=m.max_dt
) p0 ON p0.Sample_ID=r.Sample_ID AND p0.Sample_Number=r.Sample_Number AND UPPER(TRIM(p0.Test_Type))=UPPER(TRIM(r.Test_Type))
LEFT JOIN (
  SELECT id, Sample_ID, Sample_Number, Test_Type, Technician, Status, Start_Date AS stage_date
  FROM test_preparation
) p ON p.id=p0.id

/* REALIZACIÓN */
LEFT JOIN (
  SELECT t1.*
  FROM test_realization t1
  JOIN (
    SELECT Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type, MAX(Start_Date) AS max_dt
    FROM test_realization
    GROUP BY Sample_ID, Sample_Number, UPPER(TRIM(Test_Type))
  ) m ON m.Sample_ID=t1.Sample_ID AND m.Sample_Number=t1.Sample_Number
      AND UPPER(TRIM(t1.Test_Type))=m.Test_Type AND t1.Start_Date=m.max_dt
) z0 ON z0.Sample_ID=r.Sample_ID AND z0.Sample_Number=r.Sample_Number AND UPPER(TRIM(z0.Test_Type))=UPPER(TRIM(r.Test_Type))
LEFT JOIN (
  SELECT id, Sample_ID, Sample_Number, Test_Type, Technician, Status, Start_Date AS stage_date
  FROM test_realization
) z ON z.id=z0.id

/* ENTREGA */
LEFT JOIN (
  SELECT t1.*
  FROM test_delivery t1
  JOIN (
    SELECT Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type, MAX(Start_Date) AS max_dt
    FROM test_delivery
    GROUP BY Sample_ID, Sample_Number, UPPER(TRIM(Test_Type))
  ) m ON m.Sample_ID=t1.Sample_ID AND m.Sample_Number=t1.Sample_Number
      AND UPPER(TRIM(t1.Test_Type))=m.Test_Type AND t1.Start_Date=m.max_dt
) d0 ON d0.Sample_ID=r.Sample_ID AND d0.Sample_Number=r.Sample_Number AND UPPER(TRIM(d0.Test_Type))=UPPER(TRIM(r.Test_Type))
LEFT JOIN (
  SELECT id, Sample_ID, Sample_Number, Test_Type, Technician, Status, Start_Date AS stage_date
  FROM test_delivery
) d ON d.id=d0.id

{$whereSQL}
ORDER BY r.Sample_Date DESC, r.Client, r.Project_Number, r.Sample_ID, r.Sample_Number, UPPER(TRIM(r.Test_Type));
";

$rows = find_by_sql($sql);

/* ===== Spreadsheet (sencillo) ===== */
$ss = new Spreadsheet();

/* --- Hoja Consolidado --- */
$ws = $ss->getActiveSheet();
$ws->setTitle('Consolidado');

$headers = [
  'Sample_ID','Sample_Number','Test_Type','Client','Project_Number','Material_Type','Sample_Date',
  'Prep_Tech','Prep_Status','Prep_Date',
  'Real_Tech','Real_Status','Real_Date',
  'Deli_Tech','Deli_Status','Deli_Date'
];
foreach ($headers as $i => $h) $ws->setCellValueByColumnAndRow($i+1, 1, $h);

$r = 2;
foreach ($rows as $row) {
  $data = [
    cs(s($row['Sample_ID'] ?? '')),
    cs(s($row['Sample_Number'] ?? '')),
    cs(strtoupper(s($row['Test_Type'] ?? ''))),
    
    cs(s($row['Client'] ?? '')),
    cs(s($row['Project_Number'] ?? '')),
    cs(s($row['Material_Type'] ?? '')),
    s($row['Sample_Date'] ?? ''),
    cs(s($row['Prep_Tech'] ?? '')),
    cs(s($row['Prep_Status'] ?? '')),
    s($row['Prep_Date'] ?? ''),
    cs(s($row['Real_Tech'] ?? '')),
    cs(s($row['Real_Status'] ?? '')),
    s($row['Real_Date'] ?? ''),
    cs(s($row['Deli_Tech'] ?? '')),
    cs(s($row['Deli_Status'] ?? '')),
    s($row['Deli_Date'] ?? ''),
  ];
  foreach ($data as $i => $v) {
    // Conservar como texto columnas 1,2 y 6 (IDs y Project)
    if (in_array($i+1, [1,2,6], true)) {
      $ws->setCellValueExplicitByColumnAndRow($i+1, $r, $v, DataType::TYPE_STRING);
    } else {
      $ws->setCellValueByColumnAndRow($i+1, $r, $v);
    }
  }
  // Fechas (Sample_Date = col 8; Prep=11; Real=14; Deli=17)
  foreach ([8,11,14,17] as $c) {
    $v = $ws->getCellByColumnAndRow($c, $r)->getValue();
    if ($v) {
      $ts = strtotime((string)$v);
      if ($ts !== false) {
        $excel = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ts);
        $ws->setCellValueByColumnAndRow($c, $r, $excel);
        $ws->getStyleByColumnAndRow($c, $r)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');
      }
    }
  }
  $r++;
}
$ws->setAutoFilter("A1:Q".($r-1));
for ($c=1;$c<=17;$c++) $ws->getColumnDimensionByColumn($c)->setAutoSize(true);

/* --- Función para hojas por etapa (compacto) --- */
$makeStage = function(string $title, string $techKey, string $statusKey, string $dateKey) use ($ss, $rows) {
  $w = $ss->createSheet();
  $w->setTitle($title);
  $hdr = ['Sample_ID','Sample_Number','Test_Type','Client','Project_Number','Sample_Date','Technician','Status','Stage_Date'];
  foreach ($hdr as $i => $h) $w->setCellValueByColumnAndRow($i+1,1,$h);
  $r = 2;
  foreach ($rows as $row) {
    if (empty($row[$dateKey])) continue;
    $data = [
      cs(s($row['Sample_ID'] ?? '')),
      cs(s($row['Sample_Number'] ?? '')),
      cs(strtoupper(s($row['Test_Type'] ?? ''))),
      cs(s($row['Client'] ?? '')),
      cs(s($row['Project_Number'] ?? '')),
      cs(s($row['Sample_Name'] ?? '')),
      s($row['Sample_Date'] ?? ''),
      cs(s($row[$techKey] ?? '')),
      cs(s($row[$statusKey] ?? '')),
      s($row[$dateKey] ?? ''),
    ];
    foreach ($data as $i => $v) {
      if (in_array($i+1,[1,2,5],true)) {
        $w->setCellValueExplicitByColumnAndRow($i+1, $r, $v, DataType::TYPE_STRING);
      } else {
        $w->setCellValueByColumnAndRow($i+1, $r, $v);
      }
    }
    // Fechas: Sample_Date(7) y Stage_Date(10)
    foreach ([7,10] as $c) {
      $v = $w->getCellByColumnAndRow($c,$r)->getValue();
      if ($v) {
        $ts = strtotime((string)$v);
        if ($ts !== false) {
          $excel = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ts);
          $w->setCellValueByColumnAndRow($c,$r,$excel);
          $w->getStyleByColumnAndRow($c,$r)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');
        }
      }
    }
    $r++;
  }
  $w->setAutoFilter("A1:J".($r-1));
  for ($c=1;$c<=10;$c++) $w->getColumnDimensionByColumn($c)->setAutoSize(true);
};

$makeStage('Preparación','Prep_Tech','Prep_Status','Prep_Date');
$makeStage('Realización','Real_Tech','Real_Status','Real_Date');
$makeStage('Entrega','Deli_Tech','Deli_Status','Deli_Date');

/* ===== Descarga ===== */
$filenameParts = ['Flujo_Muestras_SIMPLE'];
if ($anio)     $filenameParts[] = "Y{$anio}";
if ($mes)      $filenameParts[] = "M{$mes}";
if ($cliente)  $filenameParts[] = preg_replace('/[^A-Za-z0-9_-]+/','_', $cliente);
if ($proyecto) $filenameParts[] = preg_replace('/[^A-Za-z0-9_-]+/','_', $proyecto);
$filename = implode('_', $filenameParts) . '_'.date('Ymd_His').'.xlsx';

if (ob_get_length()) { ob_end_clean(); }
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($ss);
$writer->save('php://output');
exit;
