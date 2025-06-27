<?php
require_once('../config/load.php');

if (!isset($_GET['periodo'])) {
  echo json_encode(['error' => 'Periodo no especificado']);
  exit;
}

$periodo = $_GET['periodo'];
$fecha_base = date('Y-m-d');

switch ($periodo) {
  case 'semanal':
    $inicio = date('Y-m-d', strtotime('monday this week', strtotime($fecha_base)));
    $fin = date('Y-m-d', strtotime('sunday this week', strtotime($fecha_base)));
    break;
  case 'mensual':
    $inicio = date('Y-m-01', strtotime($fecha_base));
    $fin = date('Y-m-t', strtotime($fecha_base));
    break;
  case 'trimestral':
    $mes = date('n', strtotime($fecha_base));
    $trimestre_inicio = $mes - (($mes - 1) % 3);
    $inicio = date('Y-' . str_pad($trimestre_inicio, 2, '0', STR_PAD_LEFT) . '-01');
    $fin = date('Y-m-t', strtotime("$inicio +2 months"));
    break;
  default:
    echo json_encode(['error' => 'Periodo inválido']);
    exit;
}

$start = "$inicio 00:00:00";
$end = "$fin 23:59:59";

$totales = [
  'muestras' => (int) find_by_sql("SELECT COUNT(*) as total FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'],
  'ensayos'  => (int) find_by_sql("SELECT COUNT(*) as total FROM ensayos_reporte WHERE Report_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'],
  'clientes' => (int) find_by_sql("SELECT COUNT(DISTINCT Client) as total FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'")[0]['total'],
];

// Ensayos por tipo
$ensayos_tipo = find_by_sql("SELECT Test_Type as tipo, COUNT(*) as cantidad FROM ensayos_reporte WHERE Report_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type");

// Observaciones
$observaciones = find_by_sql("SELECT Sample_Name, Sample_Number, Comments FROM ensayos_reporte WHERE Report_Date BETWEEN '{$start}' AND '{$end}' AND Comments IS NOT NULL AND TRIM(Comments) != ''");
$obs_list = [];
foreach ($observaciones as $obs) {
  $obs_list[] = [
    'sample' => $obs['Sample_Name'] . '-' . $obs['Sample_Number'],
    'comentario' => $obs['Comments']
  ];
}

// Clientes
$clientes = find_by_sql("SELECT Client, COUNT(*) as muestras FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Client");
$ensayos_cliente = find_by_sql("SELECT Client, COUNT(*) as ensayos FROM lab_test_requisition_form f JOIN ensayos_reporte e ON f.Sample_ID = e.Sample_Name WHERE e.Report_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Client");

$clientes_info = [];
foreach ($clientes as $c) {
  $clientes_info[$c['Client']] = [
    'muestras' => (int) $c['muestras'],
    'ensayos' => 0
  ];
}
foreach ($ensayos_cliente as $e) {
  if (isset($clientes_info[$e['Client']])) {
    $clientes_info[$e['Client']]['ensayos'] = (int) $e['ensayos'];
  }
}

$cliente_list = [];
foreach ($clientes_info as $cliente => $data) {
  $cliente_list[] = [
    'cliente' => $cliente,
    'muestras' => $data['muestras'],
    'ensayos' => $data['ensayos']
  ];
}

// Salida
header('Content-Type: application/json');
echo json_encode([
  'totales' => $totales,
  'por_ensayo' => $ensayos_tipo,
  'observaciones' => $obs_list,
  'clientes' => $cliente_list
]);
exit;
