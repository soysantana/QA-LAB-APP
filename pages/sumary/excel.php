<?php
require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Verificar si se ha proporcionado un Material_Type
$Material_Type = $_GET['Material_Type'] ?? null;

if (!$Material_Type) {
    die("Error: Material_Type no proporcionado.");
}

// Definir las tablas a consultar y sus encabezados personalizados
$tables = [
    'atterberg_limit' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Nat_Mc' => 'Moisture Natural',
        'Liquid_Limit_Porce' => 'Liquid Limit (%)',
        'Plastic_Limit_Porce' => 'Plastic Limit (%)',
        'Plasticity_Index_Porce' => 'Plasticity Index (%)',
        'Liquidity_Index_Porce' => 'Liquidity Index (%)',
        'Classification' => 'ASTM-UCS Soil Classification',
        'Comments' => 'Comments'
    ],
    'standard_proctor' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Methods' => 'Proctor Type (A-B-C)',
        'Spec_Gravity' => 'Specific Gravity (Gs-Ss) gs/cm3',
        'DryDensity1' => 'Dry Density pt1 (kg/m³)',
        'MoisturePorce1' => 'Moisture Content pt1 (%)',
        'DryDensity2' => 'Dry Density pt2 (kg/m³)',
        'MoisturePorce2' => 'Moisture Content pt2 (%)',
        'DryDensity3' => 'Dry Density pt3 (kg/m³)',
        'MoisturePorce3' => 'Moisture Content pt3 (%)',
        'DryDensity4' => 'Dry Density pt4 (kg/m³)',
        'MoisturePorce4' => 'Moisture Content pt4 (%)',
        'DryDensity5' => 'Dry Density pt5 (kg/m³)',
        'MoisturePorce5' => 'Moisture Content pt5 (%)',
        'DryDensity6' => 'Dry Density pt6 (kg/m³)',
        'MoisturePorce6' => 'Moisture Content pt6 (%)',
        'Max_Dry_Density_kgm3' => 'Max Dry Density (Kg/m3) Proctor',
        'Optimun_MC_Porce' => 'Opt moisture content (%) Proctor',
        'Comments' => 'Comments',
    ],
    'grain_size_general' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Pass1' => '8"',
        'Pass2' => '6"',
        'Pass3' => '5"',
        'Pass4' => '4"',
        'Pass5' => '3.5"',
        'Pass6' => '3"',
        'Pass7' => '2.5"',
        'Pass8' => '2"',
        'Pass9' => '1.5"',
        'Pass10' => '1"',
        'Pass11' => '3/4"',
        'Pass12' => '1/2"',
        'Pass13' => '3/8"',
        'Pass14' => 'No. 4',
        'Pass15' => 'No. 10',
        'Pass16' => 'No. 16',
        'Pass17' => 'No. 20',
        'Pass18' => 'No. 50',
        'Pass19' => 'No. 60',
        'Pass20' => 'No. 100',
        'Pass21' => 'No. 140',
        'Pass22' => 'No. 200',
        'Comments' => 'Comments',
    ],
    'grain_size_fine' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Pass1' => '5"',
        'Pass2' => '4"',
        'Pass3' => '3.5"',
        'Pass4' => '3"',
        'Pass5' => '2.5"',
        'Pass6' => '2"',
        'Pass7' => '1.5"',
        'Pass8' => '1"',
        'Pass9' => '3/4"',
        'Pass10' => '1/2"',
        'Pass11' => '3/8"',
        'Pass12' => 'No. 4',
        'Pass13' => 'No. 10',
        'Pass14' => 'No. 16',
        'Pass15' => 'No. 20',
        'Pass16' => 'No. 50',
        'Pass17' => 'No. 60',
        'Pass18' => 'No. 200',
        'Comments' => 'Comments',
    ],
    'grain_size_coarse' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Pass1' => '5"',
        'Pass2' => '4"',
        'Pass3' => '3.5"',
        'Pass4' => '3"',
        'Pass5' => '2.5"',
        'Pass6' => '2"',
        'Pass7' => '1.5"',
        'Pass8' => '1"',
        'Pass9' => '3/4"',
        'Pass10' => '3/8"',
        'Pass11' => 'No. 4',
        'Pass12' => 'No. 10',
        'Pass13' => 'No. 16',
        'Pass14' => 'No. 20',
        'Pass15' => 'No. 50',
        'Pass16' => 'No. 60',
        'Pass17' => 'No. 200',
        'Comments' => 'Comments',
    ],
    'grain_size_coarsethan' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Pass1' => '5"',
        'Pass2' => '4"',
        'Pass3' => '3.5"',
        'Pass4' => '3"',
        'Pass5' => '2.5"',
        'Pass6' => '2"',
        'Pass7' => '1.5"',
        'Pass8' => '1"',
        'Pass9' => '3/4"',
        'Pass10' => '3/8"',
        'Pass11' => 'No. 4',
        'Pass12' => 'No. 10',
        'Pass13' => 'No. 200',
        'Comments' => 'Comments',
    ],
    'moisture_oven' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Moisture_Content_Porce' => 'Oven Moisture Content %',
        'Comments' => 'Comments',
    ],
    'moisture_microwave' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Moisture_Content_Porce' => 'Oven Moisture Content %',
        'Comments' => 'Comments',
    ],
    'moisture_constant_mass' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Moisture_Content_Porce' => 'Oven Moisture Content %',
        'Comments' => 'Comments',
    ],
    'point_load' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'Depth_From' => 'Depth From',
        'Depth_To' => 'Depth To',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Methods' => 'Test Type',
        'DimensionL' => 'Sample Length (mm)',
        'DimensionD' => 'Sample Width (mm)',
        'PlattensSeparation' => 'Distance Between Platens (mm)',
        'LoadDirection' => 'Load Direction',
        'GaugeReading' => 'Gauge Reading',
        'FailureLoad' => 'Failure Load',
        'Comments' => 'Comments',
    ],
    'unixial_compressive' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'Depth_From' => 'Depth From',
        'Depth_To' => 'Depth To',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'DimensionD' => 'Diameter (cm)',
        'DimensionH' => 'Specimen Length (cm)',
        'AreaM2' => 'Area (m^2)',
        'VolM3' => 'Volume (m^3)',
        'WeightKg' => 'Weight of the Core (kg)',
        'UnitWeigKgm3' => 'Unit Weight of the core (Kg/m³)',
        'FailLoadKn' => 'Failure Load (kN)',
        'TestTimingS' => 'Test Timing (s)',
        'LoadpMpas' => 'Load Proportion (Mpa/s)',
        'UCSMpa' => 'Uniaxial Compressive Strength (MPa)',
        'FailureType' => 'Failure Mode',
        'Comments' => 'Comments',
    ],
    'specific_gravity' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'Depth_From' => 'Depth From',
        'Depth_To' => 'Depth To',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Specific_Gravity_Soil_Solid' => 'Specific Gravity',
        'Comments' => 'Comments',
    ],
    'specific_gravity_coarse' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'Depth_From' => 'Depth From',
        'Depth_To' => 'Depth To',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Specific_Gravity_OD' => 'Specific Gravity OD',
        'Specific_Gravity_SSD' => 'Specific Gravity SSD',
        'Apparent_Specific_Gravity' => 'Apparent Specific Gravity',
        'Percent_Absortion' => 'Percent Absortion',
        'Comments' => 'Comments',
    ],
    'specific_gravity_fine' => [
        'Structure' => 'Structure',
        'Area' => 'Work Area',
        'Sample_Date' => 'Sample Date',
        'Sample_ID' => 'Sample Name',
        'Sample_Number' => 'Sample Number',
        'Material_Type' => 'Material Type',
        'Technician' => 'Technicians',
        'Depth_From' => 'Depth From',
        'Depth_To' => 'Depth To',
        'North' => 'North',
        'East' => 'East',
        'Elev' => 'Elev',
        'Specific_Gravity_OD' => 'Specific Gravity OD',
        'Specific_Gravity_SSD' => 'Specific Gravity SSD',
        'Apparent_Specific_Gravity' => 'Apparent Specific Gravity',
        'Percent_Absortion' => 'Percent Absortion',
        'Comments' => 'Comments',
    ],
];

// Crear un nuevo archivo Excel
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0); // Eliminar la hoja inicial que crea por defecto

// Crear una hoja para cada tabla
foreach ($tables as $table => $columns) {
    // Crear una nueva hoja
    $sheet = $spreadsheet->createSheet();
    
    // Personalizar el título de la hoja
    switch ($table) {
        case 'atterberg_limit':
            $sheet->setTitle('AL');
            break;
        case 'standard_proctor':
            $sheet->setTitle('SP');
            break;
        case 'grain_size_general':
            $sheet->setTitle('GS');
            break;
        case 'grain_size_fine':
            $sheet->setTitle('GS_Fine');
            break;
        case 'grain_size_coarse':
            $sheet->setTitle('GS_Coarse');
            break;
        case 'grain_size_coarsethan':
            $sheet->setTitle('GS_Coarsethan');
            break;
        case 'moisture_oven':
            $sheet->setTitle('MC');
            break;
        case 'moisture_microwave':
            $sheet->setTitle('MC_Microwave');
            break;
        case 'moisture_constant_mass':
            $sheet->setTitle('MC_Constant_Mass');
            break;
        case 'point_load':
            $sheet->setTitle('PLT');
            break;
        case 'unixial_compressive':
            $sheet->setTitle('UCS');
            break;
        case 'specific_gravity':
            $sheet->setTitle('SG');
            break;
        case 'specific_gravity_coarse':
            $sheet->setTitle('SG_Coarse');
            break;
        case 'specific_gravity_fine':
            $sheet->setTitle('SG_Fine');
            break;
        default:
        $sheet->setTitle($table);
        break;
    }

    // Escribir encabezados personalizados
    $headers = array_values($columns); // Obtener los valores (encabezados personalizados)
    $sheet->fromArray($headers, null, 'A1');

    // Obtener los datos de la tabla
    $query = "SELECT * FROM $table WHERE Material_Type = '$Material_Type'";
    $result = $db->query($query);

    if ($db->num_rows($result) > 0) {
        // Escribir los datos en la hoja
        $rowIndex = 2; // Iniciar después de los encabezados
        while ($row = $db->fetch_assoc($result)) {
            $rowData = [];
            foreach ($columns as $db_column => $header) {
                $rowData[] = $row[$db_column] ?? ''; // Agrega cada valor basado en los nombres de columnas de la BD
            }
            $sheet->fromArray($rowData, null, 'A' . $rowIndex);
            $rowIndex++;
        }
    } else {
        // Manejo cuando no se encuentran datos
        $sheet->setCellValue('A2', 'No se encontraron datos para el Material_Type: ' . $Material_Type);
    }
}

// Crear un escritor Xlsx y guardar el archivo
$writer = new Xlsx($spreadsheet);
$excelFilePath = 'resultados_' . $Material_Type . '.xlsx';
$writer->save($excelFilePath);

echo "Archivo Excel creado con éxito: <a href='$excelFilePath'>$excelFilePath</a>";

?>