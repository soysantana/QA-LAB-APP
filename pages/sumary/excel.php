<?php
require_once('../../config/load.php');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Definir las tablas a consultar y sus encabezados
$tables = [
    'atterberg_limit' => ['Sample Name', 'Sample Number', 'Material Type', 'Moisture Natural', 'Liquid Limit (%)', 'Plastic Limit (%)', 'Plasticity Index (%)', 'Liquidity Index (%)', 'ASTM-UCS Soil Classification', 'Comments'],
    'brazilian' => ['Sample Name', 'Sample Number', 'Strength (MPa)', 'Comments'],
    // Agrega las demás tablas con sus encabezados específicos...
];

// Definir el material a buscar
$material_type = 'LPF'; // Ajusta según el valor que necesitas

// Crear un nuevo archivo Excel
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0); // Eliminar la hoja inicial que crea por defecto

// Crear una hoja para cada tabla
foreach ($tables as $table => $headers) {
    // Crear una nueva hoja
    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle($table);

    // Escribir encabezados específicos de la tabla
    $sheet->fromArray(array_merge(['Table Name'], $headers), null, 'A1');

    // Obtener los datos de la tabla
    $query = "SELECT * FROM $table WHERE Material_Type = '$material_type'";
    $result = $db->query($query);

    // Escribir los datos en la hoja
    $rowIndex = 2; // Iniciar después de los encabezados
    if ($db->num_rows($result) > 0) {
        while ($row = $db->fetch_assoc($result)) {
            $rowData = [$table]; // Inicia con el nombre de la tabla
            foreach ($headers as $header) {
                $rowData[] = $row[$header] ?? ''; // Agrega cada valor basado en los encabezados
            }
            $sheet->fromArray($rowData, null, 'A' . $rowIndex);
            $rowIndex++;
        }
    }
}

// Crear el archivo Excel y enviarlo al navegador
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="materials_report.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>
