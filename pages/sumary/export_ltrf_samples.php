<?php
require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/** ========= Configuración ========= */
$CLIENT_FIELD = 'Client'; // <-- cambia si tu campo de cliente se llama distinto

// Tipos permitidos
$ALLOWED_TYPES = ['Shelby','Mazier','Lexan','Ring','Rock'];

/** ========= Utilidades ========= */
function sanitizeSheetName($name) {
  $name = preg_replace('/[:\\\\\\/\\?\\*\\[\\]]/', '-', (string)$name); // caracteres no válidos
  $name = trim($name);
  if ($name === '') $name = 'Sin_Cliente';
  return mb_substr($name, 0, 31, 'UTF-8'); // máx. 31 chars
}

function setHeaders(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
  $headers = [
    'Sample ID', 'Sample Number', 'Sample Type',
    'Sample Date', 'Test Type', 'Depth From (m)', 'Depth To (m)', 'Store In'
  ];
  $col = 'A';
  foreach ($headers as $h) {
    $sheet->setCellValue($col.'1', $h);
    $sheet->getStyle($col.'1')->getFont()->setBold(true);
    $sheet->getStyle($col.'1')->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
  }
}

function fillRows(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, $db, $sql) {
  $rs = $db->query($sql);
  $r = 2;
  while ($row = $db->fetch_assoc($rs)) {
    $sheet->setCellValue("A{$r}", $row['Sample_ID']);
    $sheet->setCellValue("B{$r}", $row['Sample_Number']);
    $sheet->setCellValue("C{$r}", $row['Sample_Type']);
    $sheet->setCellValue("D{$r}", $row['Sample_Date']);
    $sheet->setCellValue("E{$r}", $row['Test_Type']);
    $sheet->setCellValue("F{$r}", $row['Depth_From']);
    $sheet->setCellValue("G{$r}", $row['Depth_To']);
    $sheet->setCellValue("H{$r}", $row['Store_In']);
    $r++;
  }
}

/** ========= Preparación de SQL ========= */
// Construir IN ('Shelby','Mazier',...)
$typesIn = "('" . implode("','", array_map(function($t){ return $t; }, $ALLOWED_TYPES)) . "')";

// Nota: si quieres filtrar por fechas, agrega por ejemplo:
// AND r.Sample_Date BETWEEN '2025-01-01' AND '2025-12-31'

/** ========= Obtener lista de clientes (solo con tipos permitidos) ========= */
$sqlClients = "
  SELECT DISTINCT COALESCE(NULLIF(TRIM(r.`{$CLIENT_FIELD}`), ''), 'Sin_Cliente') AS Client
  FROM lab_test_requisition_form r
  WHERE r.Sample_Type IN {$typesIn}
  ORDER BY Client ASC
";
$rsClients = $db->query($sqlClients);

$clientes = [];
while ($c = $db->fetch_assoc($rsClients)) {
  $clientes[] = $c['Client'];
}

/** ========= Construir Excel ========= */
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0); // quitar hoja vacía inicial

if (empty($clientes)) {
  // Si no hay clientes, crear una hoja informativa
  $sheet = $spreadsheet->createSheet();
  $sheet->setTitle('Sin datos');
  $sheet->setCellValue('A1', 'No se encontraron clientes con muestras de tipo permitido.');
  $spreadsheet->setActiveSheetIndex(0);
} else {
  foreach ($clientes as $cliente) {
    // Nombre de hoja por cliente
    $title = sanitizeSheetName($cliente);
    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle($title);
    setHeaders($sheet);

    // Condición por cliente (maneja 'Sin_Cliente')
    if ($cliente === 'Sin_Cliente') {
      $condCliente = " (r.`{$CLIENT_FIELD}` IS NULL OR r.`{$CLIENT_FIELD}` = '' ) ";
    } else {
      $clienteEsc = $db->escape($cliente);
      $condCliente = " r.`{$CLIENT_FIELD}` = '{$clienteEsc}' ";
    }

    // Traer filas por cliente, solo tipos permitidos
    $sql = "
      SELECT
        r.Sample_ID,
        r.Sample_Number,
        r.Sample_Type,
        r.Sample_Date,
        r.Test_Type,
        r.Depth_From,
        r.Depth_To,
        COALESCE(i.store_in, '') AS Store_In
      FROM lab_test_requisition_form r
      LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
      WHERE {$condCliente}
        AND r.Sample_Type IN {$typesIn}
      ORDER BY r.Sample_Date DESC, r.Sample_ID ASC
    ";

    fillRows($sheet, $db, $sql);
  }

  // Activar primera hoja real
  $spreadsheet->setActiveSheetIndex(0);
}

/** ========= Descargar ========= */ 
$filename  = 'Samples_Inventory_' . date('Ymd_His') . '.xlsx';
$temp_file = tempnam(sys_get_temp_dir(), 'excel_');
$writer    = new Xlsx($spreadsheet);
$writer->save($temp_file);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: max-age=0');
readfile($temp_file);
unlink($temp_file);
exit;
