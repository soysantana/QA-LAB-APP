<?php
require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * === Configuración ===
 * Nombre del campo "Cliente" en la tabla lab_test_requisition_form
 * Cambia 'Client' si en tu DB se llama distinto (p. ej. 'Customer' o 'Client_Name').
 */
$CLIENT_FIELD = 'Client';

/** Rango temporal: últimos 12 meses **/
$FECHA_LIMITE = date('Y-m-d', strtotime('-12 months'));

/** Encabezados comunes para todas las hojas **/
$COMMON_HEADERS = [
  'Sample ID', 'Sample Number', 'Sample Type',
  'Depth From (m)', 'Depth To (m)', 'Sample Date',
  'Test Type', 'Client',
  'Length (m)', 'Weight (kg)', 'Store In', 'Comment'
];

/** Sanea nombres de hoja para Excel (caracteres inválidos y máx. 31 chars) */
function sanitizeSheetName($name) {
  $name = preg_replace('/[:\\\\\\/\\?\\*\\[\\]]/', '-', (string)$name);
  $name = trim($name);
  if ($name === '') $name = 'Sin_Cliente';
  return mb_substr($name, 0, 31, 'UTF-8');
}

/** Pinta encabezados en una hoja y ajusta ancho */
function setHeaders(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $headers) {
  $col = 'A';
  foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->getFont()->setBold(true);
    $sheet->getStyle($col . '1')->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
  }
}

/** Agrega filas a la hoja desde un result set */
function fillRows(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, $db, $sql) {
  $rs = $db->query($sql);
  $rowNum = 2;
  while ($row = $db->fetch_assoc($rs)) {
    $sheet->setCellValue("A{$rowNum}", $row['Sample_ID']);
    $sheet->setCellValue("B{$rowNum}", $row['Sample_Number']);
    $sheet->setCellValue("C{$rowNum}", $row['Sample_Type']);
    $sheet->setCellValue("D{$rowNum}", $row['Depth_From']);
    $sheet->setCellValue("E{$rowNum}", $row['Depth_To']);
    $sheet->setCellValue("F{$rowNum}", $row['Sample_Date']);
    $sheet->setCellValue("G{$rowNum}", $row['Test_Type']);
    $sheet->setCellValue("H{$rowNum}", $row['Client']);
    $sheet->setCellValue("I{$rowNum}", $row['sample_length']);
    $sheet->setCellValue("J{$rowNum}", $row['sample_weight']);
    $sheet->setCellValue("K{$rowNum}", $row['store_in']);
    $sheet->setCellValue("L{$rowNum}", $row['comment']);
    $rowNum++;
  }
}

/** Agrega una hoja por estado store_in (general) */
function addStoreSheet(Spreadsheet $spreadsheet, $db, $title, $storeIn, $clientField, $fechaLimite, array $headers) {
  $sheet = $spreadsheet->createSheet();
  $sheet->setTitle(sanitizeSheetName($title));
  setHeaders($sheet, $headers);

  $sql = "
    SELECT 
      r.Sample_ID, r.Sample_Number, r.Sample_Type,
      r.Depth_From, r.Depth_To, r.Sample_Date,
      r.Test_Type, r.`{$clientField}` AS Client,
      i.sample_length, i.sample_weight, i.store_in, i.comment
    FROM lab_test_requisition_form r
    LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
    WHERE i.store_in = '{$db->escape($storeIn)}'
      AND r.Sample_Date >= '{$fechaLimite}'
    ORDER BY r.Sample_Date DESC, r.Sample_ID ASC
  ";
  fillRows($sheet, $db, $sql);
}

/** Obtiene lista de clientes distintos en los últimos 12 meses */
function getClients($db, $clientField, $fechaLimite) {
  $sql = "
    SELECT DISTINCT r.`{$clientField}` AS Client
    FROM lab_test_requisition_form r
    WHERE r.Sample_Date >= '{$fechaLimite}'
    ORDER BY r.`{$clientField}` ASC
  ";
  $rs = $db->query($sql);
  $clientes = [];
  while ($c = $db->fetch_assoc($rs)) {
    $clientes[] = $c['Client'] ?: 'Sin_Cliente';
  }
  return $clientes;
}

/** Agrega dos hojas por cliente (Enviadas / Almacenadas) */
function addClientSheets(Spreadsheet $spreadsheet, $db, $clientField, $fechaLimite, array $headers) {
  $clientes = getClients($db, $clientField, $fechaLimite);

  foreach ($clientes as $cliente) {
    // Condición por cliente (NULL/empty → Sin_Cliente)
    if ($cliente === 'Sin_Cliente') {
      $condCliente = " (r.`{$clientField}` IS NULL OR r.`{$clientField}` = '') ";
    } else {
      $clienteEsc = $db->escape($cliente);
      $condCliente = " r.`{$clientField}` = '{$clienteEsc}' ";
    }

    // 1) Hoja Enviadas
    $sheet1 = $spreadsheet->createSheet();
    $sheet1->setTitle(sanitizeSheetName("{$cliente} – Enviadas"));
    setHeaders($sheet1, $headers);

    $sql1 = "
      SELECT 
        r.Sample_ID, r.Sample_Number, r.Sample_Type,
        r.Depth_From, r.Depth_To, r.Sample_Date,
        r.Test_Type, r.`{$clientField}` AS Client,
        i.sample_length, i.sample_weight, i.store_in, i.comment
      FROM lab_test_requisition_form r
      LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
      WHERE {$condCliente}
        AND r.Sample_Date >= '{$fechaLimite}'
        AND i.store_in = 'Sended_To'
      ORDER BY r.Sample_Date DESC, r.Sample_ID ASC
    ";
    fillRows($sheet1, $db, $sql1);

    // 2) Hoja Almacenadas
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle(sanitizeSheetName("{$cliente} – Almacenadas"));
    setHeaders($sheet2, $headers);

    $sql2 = "
      SELECT 
        r.Sample_ID, r.Sample_Number, r.Sample_Type,
        r.Depth_From, r.Depth_To, r.Sample_Date,
        r.Test_Type, r.`{$clientField}` AS Client,
        i.sample_length, i.sample_weight, i.store_in, i.comment
      FROM lab_test_requisition_form r
      LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
      WHERE {$condCliente}
        AND r.Sample_Date >= '{$fechaLimite}'
        AND i.store_in = 'Stored_PVLab'
      ORDER BY r.Sample_Date DESC, r.Sample_ID ASC
    ";
    fillRows($sheet2, $db, $sql2);
  }
}

/* ============================
   Construcción del Excel
   ============================ */
$spreadsheet = new Spreadsheet();
// quitar la hoja inicial vacía
$spreadsheet->removeSheetByIndex(0);

// Hojas generales por store_in
addStoreSheet($spreadsheet, $db, 'Muestras enviadas',   'Sended_To',    $CLIENT_FIELD, $FECHA_LIMITE, $COMMON_HEADERS);
addStoreSheet($spreadsheet, $db, 'Muestras almacenadas','Stored_PVLab', $CLIENT_FIELD, $FECHA_LIMITE, $COMMON_HEADERS);

// Hojas por cliente (Enviadas / Almacenadas)
addClientSheets($spreadsheet, $db, $CLIENT_FIELD, $FECHA_LIMITE, $COMMON_HEADERS);

// Activar la primera hoja
$spreadsheet->setActiveSheetIndex(0);

/* ============================
   Descarga del archivo
   ============================ */
$filename  = 'Inventario_Muestras_' . date('Ymd_His') . '.xlsx';
$temp_file = tempnam(sys_get_temp_dir(), 'excel_');
$writer    = new Xlsx($spreadsheet);
$writer->save($temp_file);

// Forzar descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
readfile($temp_file);
unlink($temp_file);
exit;
