<?php
require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Cargar productos desde la base de datos
$products = join_product_table(); 

// Crear nuevo libro
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet(); 
$sheet->setTitle('Inventario Equipos');

// Establecer encabezados
$headers = [
    'N°', 'Artículo', 'Marca/Modelo', 'Código', 'Status', 'Categoría',
    'Stock', 'Precio de Compra', 'Fecha Agregado'
];
$sheet->fromArray($headers, NULL, 'A1');

// Agregar datos
$rowIndex = 2;
foreach ($products as $index => $product) {
    $sheet->setCellValue("A{$rowIndex}", $index + 1);
    $sheet->setCellValue("B{$rowIndex}", $product['name']);
    $sheet->setCellValue("C{$rowIndex}", $product['Marca_Modelo']);
    $sheet->setCellValue("D{$rowIndex}", $product['Codigo']);
    $sheet->setCellValue("E{$rowIndex}", $product['Status']);
    $sheet->setCellValue("F{$rowIndex}", $product['categorie']);
    $sheet->setCellValue("G{$rowIndex}", $product['quantity']);
    $sheet->setCellValue("H{$rowIndex}", $product['buy_price']);
    $sheet->setCellValue("I{$rowIndex}", $product['date']);
    $rowIndex++;
}

// Ajustar ancho de columnas
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Estilo encabezado
$headerStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEEEEEE']]
];
$sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

// Guardar archivo
$filename = 'Inventario_Equipos_' . date('Ymd_His') . '.xlsx';
$filepath = '../../uploads/reports/' . $filename;

if (!file_exists('../../uploads/reports')) {
    mkdir('../../uploads/reports', 0777, true);
}

$writer = new Xlsx($spreadsheet);
$writer->save($filepath);

// Redirigir al archivo
header("Location: ../../uploads/reports/{$filename}");
exit;
