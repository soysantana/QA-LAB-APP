<?php
/**
 * export_inventario_muestras.php
 * Genera un Excel con dos hojas:
 *  - "Muestras enviadas"  => i.store_in = 'Sended_To'
 *  - "Muestras almacenadas" => i.store_in = 'Stored_PVLab'
 *
 * Requiere PhpSpreadsheet instalado por Composer.
 */

declare(strict_types=1);

// ====== CARGA BOOTSTRAP APP Y COMPOSER ======
require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date as XLSDate;

// ====== VALIDACIÓN RÁPIDA DE ENTORNO ======
if (!class_exists(Spreadsheet::class)) {
    http_response_code(500);
    die('Error: PhpSpreadsheet no está disponible. Verifica "composer install".');
}

// Puedes ajustar el rango de meses con ?months=12
$monthsBack = isset($_GET['months']) ? max(1, (int)$_GET['months']) : 12;

// ====== CREAR LIBRO ======
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0); // eliminar hoja inicial

/**
 * Crea una hoja con datos de inventario de muestras inalteradas.
 *
 * @param Spreadsheet $spreadsheet
 * @param mixed       $db           Wrapper de DB de tu app (con ->query, ->fetch_assoc, ->escape)
 * @param string      $tituloHoja   Título de la hoja
 * @param string      $storeIn      Valor de i.store_in a consultar (ej. 'Sended_To' o 'Stored_PVLab')
 * @param int         $monthsBack   Meses hacia atrás (para Sample_Date)
 */
function agregarHoja(Spreadsheet $spreadsheet, $db, string $tituloHoja, string $storeIn, int $monthsBack = 12): void
{
    // Crear hoja
    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle($tituloHoja);

    // Rango de fechas (últimos N meses)
    $fechaLimite = date('Y-m-d', strtotime(sprintf('-%d months', $monthsBack)));

    // IMPORTANTE: valida/escapa el storeIn recibido
    $storeInEsc = $db->escape($storeIn);
    $fechaEsc   = $db->escape($fechaLimite);

    // NOTA: Asegúrate que el nombre de la tabla sea el correcto.
    // En tu app se usa "inalteratedsample" (con "alterated").
    // Si tu tabla se llama "inalteredsample", CAMBIA el nombre aquí.
    $query = "
        SELECT
            r.Sample_ID,
            r.Sample_Number,
            r.Sample_Type,
            r.Depth_From,
            r.Depth_To,
            r.Sample_Date,
            i.sample_length,
            i.sample_weight,
            i.store_in,
            i.comment
        FROM lab_test_requisition_form r
        LEFT JOIN inalteratedsample i
               ON r.id = i.requisition_id
        WHERE i.store_in = '{$storeInEsc}'
          AND r.Sample_Date >= '{$fechaEsc}'
        ORDER BY r.Sample_Date DESC, r.Sample_ID
    ";

    $data   = $db->query($query);
    $result = [];
    while ($row = $db->fetch_assoc($data)) {
        $result[] = $row;
    }

    // ====== ENCABEZADOS ======
    $headers = [
        'Nombre de Muestra', // A
        'Número',            // B
        'Tipo',              // C
        'Profundidad Desde', // D
        'Hasta',             // E
        'Fecha',             // F
        'Longitud',          // G
        'Peso (kg)',         // H
        'Ubicación',         // I
        'Comentario'         // J
    ];

    $headerRow = 1;
    $colIdx    = 1; // 1 = A
    foreach ($headers as $header) {
        $sheet->setCellValueByColumnAndRow($colIdx, $headerRow, $header);
        $colIdx++;
    }

    // Estilo de encabezados
    $lastCol = 'J';
    $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFont()->setBold(true);
    $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFill()->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setARGB('FFEFEFEF');
    $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

    // ====== CONTENIDO ======
    $rowNum = 2;
    foreach ($result as $r) {
        // A - Sample_ID
        $sheet->setCellValue("A{$rowNum}", (string)($r['Sample_ID'] ?? ''));
        // B - Sample_Number
        $sheet->setCellValue("B{$rowNum}", (string)($r['Sample_Number'] ?? ''));
        // C - Sample_Type
        $sheet->setCellValue("C{$rowNum}", (string)($r['Sample_Type'] ?? ''));
        // D - Depth_From
        $sheet->setCellValue("D{$rowNum}", $r['Depth_From'] !== null ? (string)$r['Depth_From'] : '');
        // E - Depth_To
        $sheet->setCellValue("E{$rowNum}", $r['Depth_To'] !== null ? (string)$r['Depth_To'] : '');

        // F - Sample_Date (formato fecha Excel si viene como YYYY-mm-dd)
        $fecha = $r['Sample_Date'] ?? null;
        if ($fecha && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $excelDate = XLSDate::PHPToExcel(strtotime($fecha));
            $sheet->setCellValue("F{$rowNum}", $excelDate);
        } else {
            // Si no es una fecha válida, lo coloca como texto
            $sheet->setCellValue("F{$rowNum}", (string)$fecha);
        }

        // G - sample_length
        $sheet->setCellValue("G{$rowNum}", $r['sample_length'] !== null ? (string)$r['sample_length'] : '');
        // H - sample_weight
        $sheet->setCellValue("H{$rowNum}", $r['sample_weight'] !== null ? (string)$r['sample_weight'] : '');
        // I - store_in
        $sheet->setCellValue("I{$rowNum}", (string)($r['store_in'] ?? ''));
        // J - comment
        $sheet->setCellValue("J{$rowNum}", (string)($r['comment'] ?? ''));

        $rowNum++;
    }

    // AutoSize columnas
    foreach (range('A', $lastCol) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Formato de fecha para toda la columna F (desde la fila 2 a la última)
    if ($rowNum > 2) {
        $sheet->getStyle("F2:F" . ($rowNum - 1))
              ->getNumberFormat()->setFormatCode('yyyy-mm-dd');
    }

    // Autofiltro
    $sheet->setAutoFilter("A{$headerRow}:{$lastCol}" . max($headerRow, $rowNum - 1));

    // Congelar fila de encabezados
    $sheet->freezePane('A2');

    // Ajuste de ancho mínimo para Comentario si no quieres autosize
    // $sheet->getColumnDimension('J')->setAutoSize(false);
    // $sheet->getColumnDimension('J')->setWidth(50);
}

// ====== AGREGAR LAS DOS HOJAS ======
agregarHoja($spreadsheet, $db, 'Muestras enviadas',   'Sended_To',   $monthsBack);
agregarHoja($spreadsheet, $db, 'Muestras almacenadas','Stored_PVLab', $monthsBack);

// Activar la primera hoja
$spreadsheet->setActiveSheetIndex(0);

// ====== SALIDA COMO DESCARGA ======

// Limpia buffers (evita problemas con BOM/espacios/errores previos)
if (function_exists('ob_get_length')) {
    while (ob_get_length()) { ob_end_clean(); }
}

// Opcional: subir límites si esperas muchos registros
@ini_set('memory_limit', '512M');
@set_time_limit(120);

$filename  = 'Inventario_Muestras_' . date('Ymd_His') . '.xlsx';

// Crear archivo temporal
$temp_file = tempnam(sys_get_temp_dir(), 'excel_');
$writer    = new Xlsx($spreadsheet);
$writer->save($temp_file);

// Forzar descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Content-Length: ' . filesize($temp_file));
readfile($temp_file);

// Limpieza
unlink($temp_file);
exit;
