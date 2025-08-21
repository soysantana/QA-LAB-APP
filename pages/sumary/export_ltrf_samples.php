<?php
require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/** ==== Configuración general ==== */
$FECHA_LIMITE = date('Y-m-d', strtotime('-12 months')); // últimos 12 meses
$COMMON_HEADERS = [
  'Sample ID', 'Sample Number', 'Sample Type',
  'Depth From (m)', 'Depth To (m)', 'Sample Date',
  'Test Type', 'Client',
  'Length (m)', 'Weight (kg)', 'Store In', 'Comment'
];

/** ==== Utilidades ==== */
function sanitizeSheetName($name) {
  $name = preg_replace('/[:\\\\\\/\\?\\*\\[\\]]/', '-', (string)$name);
  $name = trim($name);
  if ($name === '') $name = 'Sin_Cliente';
  return mb_substr($name, 0, 31, 'UTF-8');
}

function setHeaders(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $headers) {
  $col = 'A';
  foreach ($headers as $header) {
    $sheet->setCellValue($col.'1', $header);
    $sheet->getStyle($col.'1')->getFont()->setBold(true);
    $sheet->getStyle($col.'1')->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
  }
}

function fillRows(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, $db, $sql, $clientFieldOrNull) {
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
    // Si existe campo cliente en el SELECT, úsalo; si no, deja vacío
    $sheet->setCellValue("H{$rowNum}", isset($row['Client']) ? $row['Client'] : '');
    $sheet->setCellValue("I{$rowNum}", $row['sample_length']);
    $sheet->setCellValue("J{$rowNum}", $row['sample_weight']);
    $sheet->setCellValue("K{$rowNum}", $row['store_in']);
    $sheet->setCellValue("L{$rowNum}", $row['comment']);
    $rowNum++;
  }
}

/**
 * Detecta el campo "cliente" en lab_test_requisition_form usando DESCRIBE
 * Retorna el nombre del campo si existe; si no, null.
 */
function detectClientField($db) {
  $candidates = ['Client','Client_Name','Customer','Company','Area','Department','Owner','Requester','Requesting_Department'];
  $cols = [];
  $desc = $db->query("DESCRIBE lab_test_requisition_form");
  if ($desc) {
    while ($row = $db->fetch_assoc($desc)) {
      $cols[] = $row['Field'];
    }
  }
  foreach ($candidates as $c) {
    if (in_array($c, $cols, true)) return $c;
  }
  return null;
}

/** Hojas generales por store_in */
function addStoreSheet(Spreadsheet $spreadsheet, $db, $title, $storeIn, $clientFieldOrNull, $fechaLimite, array $headers) {
  $sheet = $spreadsheet->createSheet();
  $sheet->setTitle(sanitizeSheetName($title));
  setHeaders($sheet, $headers);

  $selectClient = $clientFieldOrNull ? " , r.`{$clientFieldOrNull}` AS Client " : " , '' AS Client ";
  $sql = "
    SELECT 
      r.Sample_ID, r.Sample_Number, r.Sample_Type,
      r.Depth_From, r.Depth_To, r.Sample_Date,
      r.Test_Type
      {$selectClient},
      i.sample_length, i.sample_weight, i.store_in, i.comment
    FROM lab_test_requisition_form r
    LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
    WHERE i.store_in = '".$db->escape($storeIn)."'
      AND r.Sample_Date >= '{$fechaLimite}'
    ORDER BY r.Sample_Date DESC, r.Sample_ID ASC
  ";
  fillRows($sheet, $db, $sql, $clientFieldOrNull);
}

/** Obtiene lista de clientes distintos (respeta fecha) */
function getClients($db, $clientField, $fechaLimite) {
  $sql = "
    SELECT DISTINCT r.`{$clientField}` AS Client
    FROM lab_test_requisition_form r
    WHERE r.Sample_Date >= '{$fechaLimite}'
    ORDER BY r.`{$clientField}` ASC
  ";
  $rs = $db->query($sql);
  $clientes = [];
  if ($rs) {
    while ($c = $db->fetch_assoc($rs)) {
      $clientes[] = ($c['Client'] === null || $c['Client'] === '') ? 'Sin_Cliente' : $c['Client'];
    }
  }
  return $clientes;
}

/** Agrega dos hojas por cliente (Enviadas / Almacenadas) */
function addClientSheets(Spreadsheet $spreadsheet, $db, $clientField, $fechaLimite, array $headers) {
  $clientes = getClients($db, $clientField, $fechaLimite);

  // Hoja "Clientes" con listado
  $listSheet = $spreadsheet->createSheet();
  $listSheet->setTitle(sanitizeSheetName('Client'));
  $listSheet->setCellValue('A1', 'Client');
  $listSheet->getStyle('A1')->getFont()->setBold(true);
  $listSheet->getColumnDimension('A')->setAutoSize(true);
  $r = 2;
  foreach ($clientes as $c) {
    $listSheet->setCellValue("A{$r}", $c);
    $r++;
  }

  foreach ($clientes as $cliente) {
    // Condición por cliente (NULL/empty → Sin_Cliente)
    if ($cliente === 'Sin_Cliente') {
      $condCliente = " (r.`{$clientField}` IS NULL OR r.`{$clientField}` = '') ";
    } else {
      $clienteEsc = $db->escape($cliente);
      $condCliente = " r.`{$clientField}` = '{$clienteEsc}' ";
    }

    // Enviadas
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
    fillRows($sheet1, $db, $sql1, $clientField);

    // Almacenadas
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
    fillRows($sheet2, $db, $sql2, $clientField);
  }
}

/** ==== Construcción del Excel ==== */
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0); // eliminar hoja inicial

// 1) Detectar campo de cliente automáticamente
$detectedClientField = detectClientField($db);

// 2) Hojas generales (store_in)
addStoreSheet($spreadsheet, $db, 'Muestras enviadas',    'Sended_To',    $detectedClientField, $FECHA_LIMITE, $COMMON_HEADERS);
addStoreSheet($spreadsheet, $db, 'Muestras almacenadas', 'Stored_PVLab', $detectedClientField, $FECHA_LIMITE, $COMMON_HEADERS);

// 3) Resumen
$summary = $spreadsheet->createSheet();
$summary->setTitle('Resumen');
$summary->setCellValue('A1', 'Campo de cliente detectado');
$summary->setCellValue('B1', $detectedClientField ?: 'N/D');
$summary->getStyle('A1:B1')->getFont()->setBold(true);
$summary->getColumnDimension('A')->setAutoSize(true);
$summary->getColumnDimension('B')->setAutoSize(true);

// 4) Hojas por cliente (si existe campo)
if ($detectedClientField) {
  addClientSheets($spreadsheet, $db, $detectedClientField, $FECHA_LIMITE, $COMMON_HEADERS);
  $summary->setCellValue('A3', 'Estado');
  $summary->setCellValue('B3', 'Hojas por cliente generadas');
} else {
  $summary->setCellValue('A3', 'Estado');
  $summary->setCellValue('B3', 'No se encontró campo de cliente. Solo hojas generales.');
}
$summary->setCellValue('A5', 'Rango de fechas');
$summary->setCellValue('B5', $FECHA_LIMITE . ' → ' . date('Y-m-d'));

// Activar la primera hoja
$spreadsheet->setActiveSheetIndex(0);

/** ==== Descarga ==== */
$filename  = 'Inventario_Muestras_' . date('Ymd_His') . '.xlsx';
$temp_file = tempnam(sys_get_temp_dir(), 'excel_');
$writer    = new Xlsx($spreadsheet);
$writer->save($temp_file);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: max-age=0');
readfile($temp_file);
unlink($temp_file);
exit;
