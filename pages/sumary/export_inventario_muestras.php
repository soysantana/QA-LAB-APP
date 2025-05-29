<?php
require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear instancia del archivo Excel
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0); // Eliminar hoja inicial vacía

function agregarHoja($spreadsheet, $db, $tituloHoja, $storeIn) {
    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle($tituloHoja);

    $fechaLimite = date('Y-m-d', strtotime('-12 months'));
    $query = "SELECT r.Sample_ID, r.Sample_Number, r.Sample_Type, r.Depth_From, r.Depth_To, r.Sample_Date,
                     i.sample_length, i.sample_weight, i.store_in, i.comment
              FROM lab_test_requisition_form r
              LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
              WHERE i.store_in = '{$storeIn}' AND r.Sample_Date >= '{$fechaLimite}'
              ORDER BY r.Sample_Date DESC";

    $data = $db->query($query);
    $result = [];

    while ($row = $db->fetch_assoc($data)) {
        $result[] = $row;
    }

    // Encabezados
    $headers = [
        'Nombre de Muestra', 'Número', 'Tipo', 'Profundidad Desde', 'Hasta', 
        'Fecha', 'Longitud', 'Peso (kg)', 'Ubicación', 'Comentario'
    ];

    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $sheet->getStyle($col . '1')->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->getStyle($col . '1')->getAlignment()->setHorizontal('center');
        $col++;
    }

    // Contenido
    $rowNum = 2;
    foreach ($result as $row) {
        $sheet->setCellValue("A{$rowNum}", $row['Sample_ID']);
        $sheet->setCellValue("B{$rowNum}", $row['Sample_Number']);
        $sheet->setCellValue("C{$rowNum}", $row['Sample_Type']);
        $sheet->setCellValue("D{$rowNum}", $row['Depth_From']);
        $sheet->setCellValue("E{$rowNum}", $row['Depth_To']);
        $sheet->setCellValue("F{$rowNum}", $row['Sample_Date']);
        $sheet->setCellValue("G{$rowNum}", $row['sample_length']);
        $sheet->setCellValue("H{$rowNum}", $row['sample_weight']);
        $sheet->setCellValue("I{$rowNum}", $row['store_in']);
        $sheet->setCellValue("J{$rowNum}", $row['comment']);
        $rowNum++;
    }
}

// Agregar hoja de muestras enviadas
agregarHoja($spreadsheet, $db, 'Muestras enviadas', 'Sended_To');

// Agregar hoja de muestras almacenadas
agregarHoja($spreadsheet, $db, 'Muestras almacenadas', 'Stored_PVLab');

// Activar primera hoja
$spreadsheet->setActiveSheetIndex(0);

// Guardar archivo temporal y descargar
$filename = 'Inventario_Muestras_' . date('Ymd_His') . '.xlsx';
$temp_file = tempnam(sys_get_temp_dir(), 'excel_');
$writer = new Xlsx($spreadsheet);
$writer->save($temp_file);

// Forzar descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
readfile($temp_file);
unlink($temp_file);
exit;
