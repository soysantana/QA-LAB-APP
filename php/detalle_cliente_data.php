<?php
require_once('../config/load.php');

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$anio = $_POST['anio'] ?? '';
$trimestre = $_POST['trimestre'] ?? '';

$where = [];

if ($anio !== '') {
  $where[] = "YEAR(Sample_Date) = '{$db->escape($anio)}'";
}

if ($trimestre !== '') {
  switch ($trimestre) {
    case 'Q1': $where[] = "MONTH(Sample_Date) BETWEEN 1 AND 3"; break;
    case 'Q2': $where[] = "MONTH(Sample_Date) BETWEEN 4 AND 6"; break;
    case 'Q3': $where[] = "MONTH(Sample_Date) BETWEEN 7 AND 9"; break;
    case 'Q4': $where[] = "MONTH(Sample_Date) BETWEEN 10 AND 12"; break;
  }
}

$where_sql = (!empty($where)) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql_muestras = "SELECT * FROM lab_test_requisition_form $where_sql";
$muestras = find_by_sql($sql_muestras);

$clientes_data = [];
$muestras_semana = [];
$inicio_semana = date('Y-m-d', strtotime('monday this week'));
$fin_semana = date('Y-m-d', strtotime('sunday this week'));

foreach ($muestras as $m) {
  $cliente = $m['Client'];
  $id = $m['Sample_ID'];
  $num = $m['Sample_Number'];
  $fecha = $m['Sample_Date'];
  $test = $m['Test_Type'];

  if (!isset($clientes_data[$cliente])) {
    $clientes_data[$cliente] = [
      'ensayos' => 0,
      'muestras' => [],
      'solicitados' => 0,
      'entregados' => 0
    ];
  }

  $clientes_data[$cliente]['ensayos']++;
  $clientes_data[$cliente]['muestras'][$id . '-' . $num] = true;
  $clientes_data[$cliente]['solicitados']++;

  $match = find_by_sql("SELECT COUNT(*) as c FROM test_delivery WHERE Sample_Name = '{$db->escape($id)}' AND Sample_Number = '{$db->escape($num)}' AND Test_Type = '{$db->escape($test)}'");
  if ((int)$match[0]['c'] > 0) {
    $clientes_data[$cliente]['entregados']++;
  }

  if ($fecha >= $inicio_semana && $fecha <= $fin_semana) {
    $muestras_semana[] = [
      'Client' => $cliente,
      'Sample_ID' => $id,
      'Sample_Number' => $num,
      'Sample_Date' => $fecha
    ];
  }
}

$grafico = [];
$progreso = [];

foreach ($clientes_data as $cliente => $info) {
  $grafico[] = [
    'cliente' => $cliente,
    'ensayos' => $info['ensayos'],
    'muestras' => count($info['muestras'])
  ];

  $p = ($info['solicitados'] > 0) ? round(($info['entregados'] / $info['solicitados']) * 100) : 0;

  $progreso[] = [
    'Client' => $cliente,
    'Solicitados' => $info['solicitados'],
    'Entregados' => $info['entregados'],
    'Porcentaje' => $p
  ];
}

$response = [
  'grafico' => $grafico,
  'muestras_semana' => $muestras_semana,
  'progreso' => $progreso
];

echo json_encode($response);
exit;
