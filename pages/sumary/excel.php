<?php
require_once('../../config/load.php');
require_once ('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear una nueva instancia de Spreadsheet
$spreadsheet = new Spreadsheet();

// Obtener la hoja activa
$sheet = $spreadsheet->getActiveSheet();

// Escribir algunos datos en la hoja
$sheet->setCellValue('A1', 'Hello World !');
$sheet->setCellValue('A2', 'This is a simple PhpSpreadsheet example.');

// Crear un escritor Xlsx
$writer = new Xlsx($spreadsheet);

// Guardar el archivo Excel en el sistema de archivos
$writer->save('example.xlsx');

echo "Archivo Excel creado con éxito.";
?>
