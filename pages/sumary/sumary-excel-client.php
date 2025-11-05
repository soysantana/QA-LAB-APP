<?php
declare(strict_types=1);

require_once('../../config/load.php');
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/* --------------------------
   Parámetros
--------------------------- */
$Material_Type = trim($_GET['Material_Type'] ?? '');
$Client        = trim($_GET['Client'] ?? '');

if ($Material_Type === '' || $Client === '') {
  http_response_code(400);
  die("Error: Material_Type y Client no proporcionados.");
}

$Material_TypeE = $db->escape($Material_Type);
$ClientE        = $db->escape($Client);

/* --------------------------
   Configuración
--------------------------- */

// Títulos cortos (si no está mapeado, se usará el nombre de la tabla)
$titles = [
  'atterberg_limit'                 => 'AL',
  'standard_proctor'                => 'SP',
  'grain_size_general'              => 'GS',
  'grain_size_fine'                 => 'GS_Fine',
  'grain_size_coarse'               => 'GS_Coarse',
  'grain_size_full'                 => 'GS_Full',
  'grain_size_lpf'                  => 'GS_LPF',
  'grain_size_upstream_transition_fill' => 'GS_UTF',
  'hydrometer'                      => 'HYD',
  'los_angeles_abrasion_large'      => 'LAA_Large',
  'los_angeles_abrasion_small'      => 'LAA_Small',
  'moisture_constant_mass'          => 'MC_Const',
  'moisture_microwave'              => 'MC_Micro',
  'moisture_oven'                   => 'MC_Oven',
  'moisture_scale'                  => 'MC_Scale',
  'pinhole_test'                    => 'Pinhole',
  'point_load'                      => 'PLT',
  'unixial_compressive'             => 'UCS',
  'reactivity'                      => 'Reactivity',
  'soundness'                       => 'Soundness',
  'specific_gravity'                => 'SG',
  'specific_gravity_coarse'         => 'SG_Coarse',
  'specific_gravity_fine'           => 'SG_Fine',
];

// Tablas a excluir del barrido general
$excludeTables = [
  'calendar','categories','ensayos_reporte','inalteratedsample',
  'lab_test_requisition_form','media','products',
  'test_delivery','test_preparation','test_realization','test_repeat','test_review','test_reviewed',
  'users','user_groups'
];

// Columnas recomendadas por tabla (si existen; si no, se auto-descubren)
$includeColumnsByTable = [
  'atterberg_limit' => [
    'Structure','Sample_ID','Sample_Number','Sample_Date','Area','Source',
    'Depth_From','Depth_To','North','East','Elev','Material_Type','Technician',
    'Liquid_Limit_Porce','Plastic_Limit_Porce','Plasticity_Index','Liquidity_Index_Porce',
    'Classification','Standard','Comments'
  ],
  'los_angeles_abrasion_large' => [
    'Structure','Sample_ID','Sample_Number','Sample_Date','Area','Source',
    'Depth_From','Depth_To','North','East','Elev','Material_Type','Technician',
    'Grading','Initial_Weight','Final_Weight','Weight_Loss','Weight_Loss_Porce',
    'Standard','Comments'
  ],
  'los_angeles_abrasion_small' => [
    'Structure','Sample_ID','Sample_Number','Sample_Date','Area','Source',
    'Depth_From','Depth_To','North','East','Elev','Material_Type','Technician',
    'Grading','Initial_Weight','Final_Weight','Weight_Loss','Weight_Loss_Porce',
    'Standard','Comments'
  ],
  'standard_proctor' => [
    'Structure','Sample_ID','Sample_Number','Sample_Date','Area','Source',
    'Depth_From','Depth_To','North','East','Elev','Material_Type','Technician',
    'Spec_Gravity','Max_Dry_Density_kgm3','Optimun_MC_Porce',
    'Corrected_Dry_Unit_Weigt','Corrected_Water_Content_Finer',
    'Standard','Comments'
  ],
  'specific_gravity' => [
    'Structure','Sample_ID','Sample_Number','Sample_Date','Test_Type','Area','Source',
    'Depth_From','Depth_To','North','East','Elev','Material_Type','Technician',
    'Specific_Gravity_Soil_Solid','Standard','Comments'
  ],
  'specific_gravity_coarse' => [
    'Structure','Sample_ID','Sample_Number','Sample_Date','Test_Type','Area','Source',
    'Depth_From','Depth_To','North','East','Elev','Material_Type','Technician',
    'Specific_Gravity_OD','Specific_Gravity_SSD','Apparent_Specific_Gravity',
    'Percent_Absortion','Standard','Comments'
  ],
  'specific_gravity_fine' => [
    'Structure','Sample_ID','Sample_Number','Sample_Date','Test_Type','Area','Source',
    'Depth_From','Depth_To','North','East','Elev','Material_Type','Technician',
    'Specific_Gravity_OD','Specific_Gravity_SSD','Apparent_Specific_Gravity',
    'Percent_Absortion','Standard','Comments'
  ],
];

// Grupos de hojas fusionadas (un solo sheet para varias tablas)
$mergedSheets = [
  'LAA' => ['los_angeles_abrasion_large','los_angeles_abrasion_small'],
  'SG'  => ['specific_gravity','specific_gravity_coarse','specific_gravity_fine'],
];

/* --------------------------
   Helpers
--------------------------- */
function hasColumns($db, string $table, array $cols): bool {
  $need = array_flip(array_map('strtolower', $cols));
  $res = $db->query("SHOW COLUMNS FROM `{$table}`");
  if (!$res) return false;
  while ($c = $db->fetch_assoc($res)) {
    unset($need[strtolower($c['Field'])]);
  }
  return empty($need);
}

/** Devuelve lista de columnas a usar: prefijadas si existen; si no, auto-descubiertas */
function getColumns($db, string $table, array $includeColumnsByTable): array {
  if (isset($includeColumnsByTable[$table])) {
    // Filtra solo las que realmente existan en la tabla
    $existing = [];
    $res = $db->query("SHOW COLUMNS FROM `{$table}`");
    $have = [];
    while ($res && ($c = $db->fetch_assoc($res))) {
      $have[strtolower($c['Field'])] = $c['Field'];
    }
    foreach ($includeColumnsByTable[$table] as $col) {
      $key = strtolower($col);
      if (isset($have[$key])) $existing[] = $have[$key];
    }
    if (!empty($existing)) return array_combine($existing, $existing);
  }

  // Auto-descubrir todas
  $columns = [];
  $res = $db->query("SHOW COLUMNS FROM `{$table}`");
  while ($res && ($col = $db->fetch_assoc($res))) {
    $columns[$col['Field']] = $col['Field'];
  }
  return $columns;
}

/* --------------------------
   Crear spreadsheet
--------------------------- */
$spreadsheet = new Spreadsheet();
// borra la hoja inicial para crear solo las necesarias
$spreadsheet->removeSheetByIndex(0);

$totalSheetsCreated = 0;

/* --------------------------
   Procesar hojas fusionadas
--------------------------- */
foreach ($mergedSheets as $sheetName => $tables) {
  $allData   = [];
  $allCols   = []; // columnas unificadas (en orden de aparición)

  foreach ($tables as $table) {
    // Solo procesar si la tabla existe y tiene columnas de filtro
    if (!hasColumns($db, $table, ['Material_Type','Client'])) continue;

    $columns = getColumns($db, $table, $includeColumnsByTable);
    if (empty($columns)) continue;

    $query = "
      SELECT * FROM `{$table}`
      WHERE LOWER(TRIM(`Material_Type`)) COLLATE utf8mb4_0900_ai_ci = LOWER(TRIM('{$Material_TypeE}')) COLLATE utf8mb4_0900_ai_ci
        AND LOWER(TRIM(`Client`))        COLLATE utf8mb4_0900_ai_ci = LOWER(TRIM('{$ClientE}'))        COLLATE utf8mb4_0900_ai_ci
    ";
    $result = $db->query($query);

    if ($result && $db->num_rows($result) > 0) {
      // Agregar columnas nuevas al set global respetando orden
      foreach (array_values($columns) as $col) {
        if (!in_array($col, $allCols, true)) $allCols[] = $col;
      }

      while ($row = $db->fetch_assoc($result)) {
        $rowData = [];
        foreach ($columns as $dbCol) {
          $rowData[$dbCol] = $row[$dbCol] ?? '';
        }
        $rowData['_source'] = $table; // opcional
        $allData[] = $rowData;
      }
    }
  }

  if (!empty($allData)) {
    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle(substr($sheetName, 0, 31));

    // Encabezados (agrega _source al final si existe en alguna fila)
    if (!in_array('_source', $allCols, true)) {
      // Solo agrega si al menos una fila tiene _source (todas lo tienen en este bloque)
      $allCols[] = '_source';
    }

    $sheet->fromArray($allCols, null, 'A1');
    $rowIndex = 2;

    foreach ($allData as $dataRow) {
      $rowOut = [];
      foreach ($allCols as $c) $rowOut[] = $dataRow[$c] ?? '';
      $sheet->fromArray($rowOut, null, 'A'.$rowIndex);
      $rowIndex++;
    }

    $totalSheetsCreated++;
  }
}

/* --------------------------
   Procesar tablas individuales
--------------------------- */
$resTables = $db->query("SHOW TABLES");
if ($resTables) {
  while ($tbl = $db->fetch_array($resTables)) {
    $table = $tbl[0];

    // Saltar excluidas
    if (in_array($table, $excludeTables, true)) continue;

    // Saltar las que ya están en grupos fusionados
    $isMerged = false;
    foreach ($mergedSheets as $grp) {
      if (in_array($table, $grp, true)) { $isMerged = true; break; }
    }
    if ($isMerged) continue;

    // Debe tener Material_Type y Client
    if (!hasColumns($db, $table, ['Material_Type','Client'])) continue;

    $columns = getColumns($db, $table, $includeColumnsByTable);
    if (empty($columns)) continue;

    $query = "
      SELECT * FROM `{$table}`
      WHERE LOWER(TRIM(`Material_Type`)) COLLATE utf8mb4_0900_ai_ci = LOWER(TRIM('{$Material_TypeE}')) COLLATE utf8mb4_0900_ai_ci
        AND LOWER(TRIM(`Client`))        COLLATE utf8mb4_0900_ai_ci = LOWER(TRIM('{$ClientE}'))        COLLATE utf8mb4_0900_ai_ci
    ";
    $result = $db->query($query);

    if ($result && $db->num_rows($result) > 0) {
      $sheet = $spreadsheet->createSheet();
      $sheetTitle = $titles[$table] ?? $table;
      $sheet->setTitle(substr($sheetTitle, 0, 31));

      $headers = array_values($columns);
      $sheet->fromArray($headers, null, 'A1');

      $rowIndex = 2;
      while ($row = $db->fetch_assoc($result)) {
        $rowData = [];
        foreach ($columns as $dbCol => $hdr) {
          // $hdr no se usa; usamos $dbCol para traer el valor
          $rowData[] = $row[$dbCol] ?? '';
        }
        $sheet->fromArray($rowData, null, 'A'.$rowIndex);
        $rowIndex++;
      }

      $totalSheetsCreated++;
    }
  }
}

/* --------------------------
   Si no hubo datos, crea hoja informativa
--------------------------- */
if ($totalSheetsCreated === 0) {
  $sheet = $spreadsheet->createSheet();
  $sheet->setTitle('Resumen');
  $sheet->setCellValue('A1', 'Sin resultados para los criterios:');
  $sheet->setCellValue('A2', 'Client:');
  $sheet->setCellValue('B2', $Client);
  $sheet->setCellValue('A3', 'Material_Type:');
  $sheet->setCellValue('B3', $Material_Type);
}

/* --------------------------
   Salida XLSX
--------------------------- */
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'Summary-'.preg_replace('/[^A-Za-z0-9_\-]+/','_', $Client)
       .'-'.preg_replace('/[^A-Za-z0-9_\-]+/','_', $Material_Type).'.xlsx';
header('Content-Disposition: attachment; filename="'.$fname.'"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
