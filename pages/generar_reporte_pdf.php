<?php
require_once('../config/load.php');

function normalize($v) {
  return strtoupper(trim((string)($v ?? '')));
}

$requisitions = find_all("lab_test_requisition_form");
$tables_to_check = [
  'test_preparation',
  'test_delivery',
  'test_realization',
  'test_repeat',
  'test_review',
  'test_reviewed'
];

$indexed_status = [];

// Cargar claves existentes desde tablas de seguimiento
foreach ($tables_to_check as $table) {
  $data = find_all($table);
  foreach ($data as $row) {
    if (!isset($row['Sample_Name'], $row['Sample_Number'], $row['Test_Type'])) continue;
    $key = normalize($row['Sample_Name']) . "|" . normalize($row['Sample_Number']) . "|" . normalize($row['Test_Type']);
    $indexed_status[$key] = true;
  }
}

// Mostrar claves generadas por requisiciones
echo "<h2>CLAVES GENERADAS DESDE REQUISICIONES</h2><ul>";
foreach ($requisitions as $requisition) {
  for ($i = 1; $i <= 20; $i++) {
    $testKey = "Test_Type{$i}";
    if (empty($requisition[$testKey])) continue;

    $sample_id = normalize($requisition['Sample_ID']);
    $sample_number = normalize($requisition['Sample_Number']);
    $test_type = normalize($requisition[$testKey]);
    $key = $sample_id . "|" . $sample_number . "|" . $test_type;

    echo "<li style='color:blue;'>$key</li>";
  }
}
echo "</ul>";

// Mostrar claves encontradas ya procesadas
echo "<h2>CLAVES YA PROCESADAS EN OTRAS TABLAS</h2><ul>";
foreach ($indexed_status as $key => $_) {
  echo "<li style='color:green;'>$key</li>";
}
echo "</ul>";
?>