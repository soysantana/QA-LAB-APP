<?php
declare(strict_types=1);

require_once('../../config/load.php');

// Limpia cualquier salida previa para evitar warnings con headers
if (function_exists('ob_get_length')) {
  while (ob_get_length()) { ob_end_clean(); }
}

require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// ---- Verificación dura: si la clase no existe, es que Composer no cargó una versión compatible
if (!class_exists(Spreadsheet::class)) {
  http_response_code(500);
  die("Error: PhpSpreadsheet no está disponible. Reinstala con una versión compatible con tu PHP (ej. ^1.29 para PHP 7.4).");
}

// Cargar productos desde la base de datos
$products = join_product_table(); // asegura que devuelve un array indexado

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Inventario Equipos');

// Encabezados
$headers = ['N°','Artículo','Marca/Modelo','Código','Status','Categoría','Stock','Precio de Compra','Fecha Agregado'];
$sheet->fromArray($headers, null, 'A1');

// Datos
$row = 2;
foreach ($products as $idx => $p) {
  $sheet->setCellValue("A{$row}", $idx + 1);
  $sheet->setCellValue("B{$row}", $p['name']           ?? '');
  $sheet->setCellValue("C{$row}", $p['Marca_Modelo']   ?? '');
  $sheet->setCellValue("D{$row}", $p['Codigo']         ?? '');
  $sheet->setCellValue("E{$row}", $p['Status']         ?? '');
  $sheet->setCellValue("F{$row}", $p['categorie']      ?? '');
  $sheet->setCellValue("G{$row}", $p['quantity']       ?? '');
  $sheet->setCellValue("H{$row}", $p['buy_price']      ?? '');
  $sheet->setCellValue("I{$row}", $p['date']           ?? '');
  $row++;
}

// Estilos
$sheet->getStyle('A1:I1')->applyFromArray([
  'font' => ['bold' => true],
  'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
  'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
  'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEEEEEE']]
]);

$lastRow = max(2, $row - 1);
$sheet->setAutoFilter("A1:I{$lastRow}");
$sheet->freezePane('A2');

foreach (range('A','I') as $col) {
  $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Guardar y redirigir
$filename = 'Inventario_Equipos_' . date('Ymd_His') . '.xlsx';
$dir = '../../uploads/reports';
if (!is_dir($dir)) { mkdir($dir, 0777, true); }
$filepath = $dir . '/' . $filename;

$writer = new Xlsx($spreadsheet);
$writer->save($filepath);

// Descarga directa (más robusta que Location si hay auth)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Length: '.filesize($filepath));
readfile($filepath);
exit;
