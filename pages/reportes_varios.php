<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('d/m/Y') : 'Fecha inválida';
$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 16:00:00"));
$end   = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

function get_count($table, $field, $start, $end) {
  $r = find_by_sql("SELECT COUNT(*) as total FROM {$table} WHERE {$field} BETWEEN '{$start}' AND '{$end}'");
  return (int)$r[0]['total'];
}

function resumen_cliente($start, $end) {
  $clientes = [];
  $muestras = find_by_sql("SELECT Client, Sample_ID FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'");
  foreach ($muestras as $m) {
    $c = $m['Client']; $s = $m['Sample_ID'];
    if (!isset($clientes[$c])) $clientes[$c] = ['total' => 0, 'prep' => 0, 'real' => 0, 'ent' => 0];
    $clientes[$c]['total']++;
    if (count_by_sample('test_preparation', $s) > 0) $clientes[$c]['prep']++;
    if (count_by_sample('test_realization', $s) > 0) $clientes[$c]['real']++;
    if (count_by_sample('test_delivery', $s) > 0) $clientes[$c]['ent']++;
  }
  return $clientes;
}

function count_by_sample($table, $sample) {
  return count(find_by_sql("SELECT id FROM {$table} WHERE Sample_Name = '{$sample}'"));
}

function muestras_nuevas($start, $end) {
  return find_by_sql("SELECT Sample_ID, Structure, Client, Test_Type FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'");
}

function pendientes($start, $end) {
  return find_by_sql("SELECT Sample_ID, Test_Type FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'");
}

function resumen_tecnico($start, $end) {
  return find_by_sql("SELECT Technician, COUNT(*) as total, 'Preparación' as etapa FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) as total, 'Realización' FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) as total, 'Entrega' FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician");
}

function resumen_tipo($start, $end) {
  return find_by_sql("SELECT Test_Type, COUNT(*) as total, 'Preparación' as etapa FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type, COUNT(*) as total, 'Realización' FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type, COUNT(*) as total, 'Entrega' FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type");
}

function observaciones($start, $end) {
  return find_by_sql("SELECT Sample_ID, Comment FROM lab_test_requisition_form WHERE Comment IS NOT NULL AND Registed_Date BETWEEN '{$start}' AND '{$end}'");
}

class PDF extends FPDF {
  function header() {
    global $fecha_en;
    $this->SetFont('Arial', 'B', 14);
    $this->Cell(0, 10, 'REPORTE DIARIO DE ACTIVIDAD DEL LABORATORIO', 0, 1, 'C');
    $this->SetFont('Arial', '', 10);
    $this->Cell(0, 10, "Fecha del reporte: {$fecha_en}", 0, 1, 'C');
    $this->Ln(5);
  }
  function section_title($title) {
    $this->SetFont('Arial', 'B', 12);
    $this->SetFillColor(200, 220, 255);
    $this->Cell(0, 8, $title, 0, 1, 'L', true);
    $this->Ln(2);
  }
  function section_table($headers, $rows, $widths) {
    $this->SetFont('Arial', 'B', 10);
    foreach ($headers as $i => $h) {
      $this->Cell($widths[$i], 7, $h, 1, 0, 'C');
    }
    $this->Ln();
    $this->SetFont('Arial', '', 10);
    foreach ($rows as $row) {
      foreach ($row as $i => $col) {
        $this->Cell($widths[$i], 6, $col, 1, 0, 'C');
      }
      $this->Ln();
    }
    $this->Ln(3);
  }
}

$pdf = new PDF();
$pdf->AddPage();

$pdf->section_title("1. Resumen General del Día");
$pdf->section_table(["Actividad", "Cantidad"], [
  ["Registradas", get_count("lab_test_requisition_form", "Registed_Date", $start, $end)],
  ["Preparadas", get_count("test_preparation", "Register_Date", $start, $end)],
  ["Realizadas", get_count("test_realization", "Register_Date", $start, $end)],
  ["Entregadas", get_count("test_delivery", "Register_Date", $start, $end)]
], [90, 40]);

$pdf->section_title("2. Resumen por Cliente del Día");
$clientes = resumen_cliente($start, $end);
$rows = [];
foreach ($clientes as $cli => $d) {
  $pct = $d['total'] ? round($d['ent'] * 100 / $d['total']) : 0;
  $rows[] = [$cli, $d['total'], $d['prep'], $d['real'], $d['ent'], "$pct%"];
}
$pdf->section_table(["Cliente", "Registradas", "Preparadas", "Realizadas", "Entregadas", "%"], $rows, [35, 25, 25, 25, 25, 25]);

$pdf->section_title("3. Muestras Nuevas Registradas");
$muestras = muestras_nuevas($start, $end);
$rows = [];
foreach ($muestras as $m) {
  $rows[] = [$m['Sample_ID'], $m['Structure'], $m['Client'], $m['Test_Type']];
}
$pdf->section_table(["Sample ID", "Estructura", "Cliente", "Ensayos"], $rows, [45, 35, 35, 75]);

$pdf->section_title("4. Ensayos Pendientes");
$pendientes = pendientes($start, $end);
$p_rows = [];
foreach ($pendientes as $p) {
  $tests = json_decode($p['Test_Type'], true);
  foreach ($tests as $t) {
    $delivered = count(find_by_sql("SELECT id FROM test_delivery WHERE Sample_Name = '{$p['Sample_ID']}' AND Test_Type = '{$t}'"));
    if ($delivered == 0) {
      $p_rows[] = [$p['Sample_ID'], $t, "Pendiente"];
    }
  }
}
$pdf->section_table(["Sample ID", "Ensayo", "Estado"], $p_rows, [50, 60, 40]);

$pdf->section_title("5. Ensayos por Técnico");
$tec = resumen_tecnico($start, $end);
$t_rows = [];
foreach ($tec as $r) {
  $t_rows[] = [$r['Technician'], $r['etapa'], $r['total']];
}
$pdf->section_table(["Técnico", "Etapa", "Cantidad"], $t_rows, [60, 50, 40]);

$pdf->section_title("6. Ensayos por Tipo");
$tipos = resumen_tipo($start, $end);
$type_rows = [];
foreach ($tipos as $r) {
  $type_rows[] = [$r['Test_Type'], $r['etapa'], $r['total']];
}
$pdf->section_table(["Tipo de Ensayo", "Etapa", "Cantidad"], $type_rows, [70, 50, 30]);

$pdf->section_title("7. Observaciones / No Conformidades");
$pdf->SetFont('Arial', '', 10);
$obs = observaciones($start, $end);
if (count($obs) > 0) {
  foreach ($obs as $o) {
    $pdf->MultiCell(0, 6, "- {$o['Sample_ID']}: {$o['Comment']}");
  }
} else {
  $pdf->MultiCell(0, 6, "Sin observaciones registradas.");
}
$pdf->Ln(5);

$pdf->section_title("8. Responsable");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, "Nombre Responsable", 1);
$pdf->Cell(130, 8, "Wendin De Jesús Mendoza", 1, 1);
$pdf->Cell(60, 20, "Firma", 1);
$pdf->Cell(130, 20, "[Espacio para firma]", 1, 1);

ob_end_clean();
$pdf->Output('I', "Reporte_Diario_{$fecha}.pdf");
