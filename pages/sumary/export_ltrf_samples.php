<?php
require_once('../../config/load.php');
page_require_level(3);

// Parámetros (mismos que en la vista)
$fecha_limite_def = date('Y-m-d', strtotime('-12 month'));
$tipo  = isset($_GET['tipo'])  && $_GET['tipo']  !== '' ? $db->escape($_GET['tipo'])  : null;
$desde = isset($_GET['desde']) && $_GET['desde'] !== '' ? $db->escape($_GET['desde']) : $fecha_limite_def;
$hasta = isset($_GET['hasta']) && $_GET['hasta'] !== '' ? $db->escape($_GET['hasta']) : date('Y-m-d');

// WHERE base (igual que en la página)
$where = [];
$where[] = "(r.Sample_Type IN ('Shelby','Mazier','Lexan','Ring','Rock') OR FIND_IN_SET('Envio', r.Test_Type))";
$where[] = "r.Sample_Date BETWEEN '{$desde}' AND '{$hasta}'";
if ($tipo) { $where[] = "r.Sample_Type = '{$tipo}'"; }

$sql = "
  SELECT
    r.Sample_ID,
    r.Sample_Number,
    r.Sample_Type,
    r.Sample_Date,
    r.Test_Type,
    r.Depth_From,
    r.Depth_To,
    COALESCE(i.store_in,'') AS Store_In
  FROM lab_test_requisition_form r
  LEFT JOIN inalteratedsample i
    ON r.id = i.requisition_id
  WHERE " . implode(' AND ', $where) . "
  ORDER BY r.Sample_Date DESC, r.Sample_ID ASC
";

$res = $db->query($sql);

// Salida como CSV (Excel lo abre sin problema)
$filename = 'ltrf_samples_'.date('Ymd_His').'.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
// BOM para que Excel reconozca UTF-8
echo "\xEF\xBB\xBF";

$fp = fopen('php://output', 'w');

// Encabezados
$headers = [
  'Sample ID',
  'Sample Number',
  'Sample Type',
  'Sample Date',
  'Test Type',
  'Depth From (m)',
  'Depth To (m)',
  'Store In'
];
fputcsv($fp, $headers);

// Filas
while ($row = $db->fetch_assoc($res)) {
  fputcsv($fp, [
    $row['Sample_ID'],
    $row['Sample_Number'],
    $row['Sample_Type'],
    $row['Sample_Date'],
    $row['Test_Type'],
    $row['Depth_From'],
    $row['Depth_To'],
    $row['Store_In']
  ]);
}

fclose($fp);
exit;
