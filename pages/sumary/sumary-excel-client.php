<?php
require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Verificar si se ha proporcionado un Material_Type y Client
$Material_Type = trim($_GET['Material_Type'] ?? '');
$Client = trim($_GET['Client'] ?? '');

if (!$Material_Type || !$Client) {
    die("Error: Material_Type y Client no proporcionados.");
}

// Crear un nuevo archivo Excel
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0); // Eliminar hoja inicial

// Definir títulos cortos para hojas
$titles = [
    'atterberg_limit' => 'AL',
    'standard_proctor' => 'SP',
    'grain_size_general' => 'GS',
    'grain_size_fine' => 'GS_Fine',
    'grain_size_coarse' => 'GS_Coarse',
    'los_angeles_abrasion_coarse_aggregate' => 'LAA_Coarse_Aggregate',
    'los_angeles_abrasion_coarse_filter' => 'LAA_Coarse_Filter',
    'moisture_oven' => 'MC',
    'moisture_microwave' => 'MC_Microwave',
    'moisture_constant_mass' => 'MC_Constant_Mass',
    'moisture_scale' => 'MC_Scale',
    'point_load' => 'PLT',
    'unixial_compressive' => 'UCS',
    'specific_gravity' => 'SG',
    'specific_gravity_coarse' => 'SG_Coarse',
    'specific_gravity_fine' => 'SG_Fine',
];

// Tablas a excluir
$excludeTables = [
    'calendar',
    'categories',
    'ensayos_reporte',
    'inalteratedsample',
    'lab_test_requisition_form',
    'media',
    'products',
    'test_delivery',
    'test_preparation',
    'test_realization',
    'test_repeat',
    'test_review',
    'test_reviewed',
    'users',
    'user_groups'
];

// 🔹 Columnas a incluir por tabla
$includeColumnsByTable = [
    'atterberg_limit' => [
        'Structure',
        'Sample_ID',
        'Sample_Number',
        'Sample_Date',
        'Area',
        'Source',
        'Depth_From',
        'Depth_To',
        'North',
        'East',
        'Elev',
        'Material_Type',
        'Technician',
        'Liquid_Limit_Porce',
        'Plastic_Limit_Porce',
        'Plasticity_Index',
        'Liquidity_Index_Porce',
        'Classification',
        'Standard',
        'Comments'
    ],
    'los_angeles_abrasion_large' => [
        'Structure',
        'Sample_ID',
        'Sample_Number',
        'Sample_Date',
        'Area',
        'Source',
        'Depth_From',
        'Depth_To',
        'North',
        'East',
        'Elev',
        'Material_Type',
        'Technician',
        'Grading',
        'Initial_Weight',
        'Final_Weight',
        'Weight_Loss',
        'Weight_Loss_Porce',
        'Standard',
        'Comments'
    ],
    'los_angeles_abrasion_small' => [
        'Structure',
        'Sample_ID',
        'Sample_Number',
        'Sample_Date',
        'Area',
        'Source',
        'Depth_From',
        'Depth_To',
        'North',
        'East',
        'Elev',
        'Material_Type',
        'Technician',
        'Grading',
        'Initial_Weight',
        'Final_Weight',
        'Weight_Loss',
        'Weight_Loss_Porce',
        'Standard',
        'Comments'
    ],
    'standard_proctor' => [
        'Structure',
        'Sample_ID',
        'Sample_Number',
        'Sample_Date',
        'Area',
        'Source',
        'Depth_From',
        'Depth_To',
        'North',
        'East',
        'Elev',
        'Material_Type',
        'Technician',
        'Spec_Gravity',
        'Max_Dry_Density_kgm3',
        'Optimun_MC_Porce',
        'Corrected_Dry_Unit_Weigt',
        'Corrected_Water_Content_Finer',
        'Standard',
        'Comments'
    ],
    'specific_gravity' => [
        'Structure',
        'Sample_ID',
        'Sample_Number',
        'Sample_Date',
        'Test_Type',
        'Area',
        'Source',
        'Depth_From',
        'Depth_To',
        'North',
        'East',
        'Elev',
        'Material_Type',
        'Technician',
        'Specific_Gravity_Soil_Solid',
        'Standard',
        'Comments'
    ],
    'specific_gravity_coarse' => [
        'Structure',
        'Sample_ID',
        'Sample_Number',
        'Sample_Date',
        'Test_Type',
        'Area',
        'Source',
        'Depth_From',
        'Depth_To',
        'North',
        'East',
        'Elev',
        'Material_Type',
        'Technician',
        'Specific_Gravity_OD',
        'Specific_Gravity_SSD',
        'Apparent_Specific_Gravity',
        'Percent_Absortion',
        'Standard',
        'Comments'
    ],
    'specific_gravity_fine' => [
        'Structure',
        'Sample_ID',
        'Sample_Number',
        'Sample_Date',
        'Test_Type',
        'Area',
        'Source',
        'Depth_From',
        'Depth_To',
        'North',
        'East',
        'Elev',
        'Material_Type',
        'Technician',
        'Specific_Gravity_OD',
        'Specific_Gravity_SSD',
        'Apparent_Specific_Gravity',
        'Percent_Absortion',
        'Standard',
        'Comments'
    ]
];

// 🔹 Grupos de tablas que deben fusionarse en una hoja
$mergedSheets = [
    'LAA' => [
        'los_angeles_abrasion_large',
        'los_angeles_abrasion_small'
    ],
    'SG' => [
        'specific_gravity',
        'specific_gravity_coarse',
        'specific_gravity_fine'
    ]
];

// Función para obtener columnas dinámicamente
function getColumns($db, $table, $includeColumnsByTable)
{
    if (isset($includeColumnsByTable[$table])) {
        return array_combine(
            $includeColumnsByTable[$table],
            $includeColumnsByTable[$table]
        );
    }

    $columns = [];
    $res = $db->query("SHOW COLUMNS FROM $table");
    while ($col = $db->fetch_assoc($res)) {
        $columns[$col['Field']] = $col['Field'];
    }
    return $columns;
}

// 🔹 Procesar tablas fusionadas (LAA, SG, etc.)
foreach ($mergedSheets as $sheetName => $tables) {
    $allData = [];
    $allColumns = [];

    foreach ($tables as $table) {
        $columns = getColumns($db, $table, $includeColumnsByTable);
        if (empty($columns)) continue;

        $query = "SELECT * FROM $table 
                  WHERE LOWER(TRIM(Material_Type)) = LOWER(TRIM('$Material_Type'))
                  AND LOWER(TRIM(Client)) = LOWER(TRIM('$Client'))";

        $result = $db->query($query);

        if ($db->num_rows($result) > 0) {
            foreach ($columns as $col) {
                if (!in_array($col, $allColumns)) {
                    $allColumns[] = $col;
                }
            }

            while ($row = $db->fetch_assoc($result)) {
                $rowData = [];
                foreach ($columns as $db_column) {
                    $rowData[$db_column] = $row[$db_column] ?? '';
                }
                $rowData['_source'] = $table; // opcional para saber de dónde vino
                $allData[] = $rowData;
            }
        }
    }

    if (!empty($allData)) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle(substr($sheetName, 0, 31));

        $headers = $allColumns;
        $sheet->fromArray($headers, null, 'A1');

        $rowIndex = 2;
        foreach ($allData as $dataRow) {
            $rowData = [];
            foreach ($headers as $col) {
                $rowData[] = $dataRow[$col] ?? '';
            }
            $sheet->fromArray($rowData, null, 'A' . $rowIndex);
            $rowIndex++;
        }
    }
}

// 🔹 Procesar tablas individuales
$resTables = $db->query("SHOW TABLES");
while ($tblRow = $db->fetch_array($resTables)) {
    $table = $tblRow[0];

    if (in_array($table, $excludeTables)) {
        continue;
    }

    // Saltar las tablas que ya se manejan en mergedSheets
    $skip = false;
    foreach ($mergedSheets as $tables) {
        if (in_array($table, $tables)) {
            $skip = true;
            break;
        }
    }
    if ($skip) continue;

    $columns = getColumns($db, $table, $includeColumnsByTable);
    if (empty($columns)) continue;

    $query = "SELECT * FROM $table 
              WHERE LOWER(TRIM(Material_Type)) = LOWER(TRIM('$Material_Type'))
              AND LOWER(TRIM(Client)) = LOWER(TRIM('$Client'))";

    $result = $db->query($query);

    if ($db->num_rows($result) > 0) {
        $sheet = $spreadsheet->createSheet();
        $sheetTitle = $titles[$table] ?? $table;
        $sheet->setTitle(substr($sheetTitle, 0, 31));

        $headers = array_values($columns);
        $sheet->fromArray($headers, null, 'A1');

        $rowIndex = 2;
        while ($row = $db->fetch_assoc($result)) {
            $rowData = [];
            foreach ($columns as $db_column => $header) {
                $rowData[] = $row[$db_column] ?? '';
            }
            $sheet->fromArray($rowData, null, 'A' . $rowIndex);
            $rowIndex++;
        }
    }
}

// Configurar headers para forzar descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Summary-' . $Client . '-' . $Material_Type . '.xlsx"');
header('Cache-Control: max-age=0');

// Crear el escritor y enviar la salida directo al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
