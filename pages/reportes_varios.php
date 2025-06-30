<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

$user = current_user();
$nombre_responsable = $user['name']; // o 'full_name' o el campo correcto


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
  public $day_of_week;
  public $week_number;
  public $fecha_en;

  function __construct($fecha_en) {
    parent::__construct();
    $this->day_of_week = date('w');
    $this->week_number = date('W');
    $this->fecha_en = $fecha_en;
  }
  function Header() {
    $user = current_user();
$nombre_responsable = $user['name']; // o 'full_name' o el campo correcto
    if ($this->PageNo() > 1) return;
    if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
      $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 50);
    }
     $this->SetFont('Arial', 'B', 14);
    $this->SetXY(150, 10); // Posiciona el cursor en la parte superior derecha
    $this->Cell(50, 10, utf8_decode('Daily Laboratory Report'), 0, 1, 'R');

    $this->SetFont('Arial', '', 10);
    $this->SetXY(150, 18); // Un poco más abajo para la fecha
    $this->Cell(50, 8, "Date: {$this->fecha_en}", 0, 1, 'R');

    $this->Ln(10); // Espacio antes del contenido principal

    $this->SetFont('Arial', 'B', 11);
    $this->section_title("1. Personnel Assigned");
    $this->SetFont('Arial', '', 10);

    if (in_array($this->day_of_week, [1, 2, 3, 4])) { // Lunes a Jueves
  $this->MultiCell(0, 6, "Contractor Lab Technicians: Wilson Martinez, Rafy Leocadio, Rony Vargas, Jonathan Vargas", 0, 'L');
  $this->MultiCell(0, 6, "PV Laboratory Supervisors: Diana Vazquez", 0, 'L');
  $this->MultiCell(0, 6, "Lab Document Control: Yamilexi Mejia, Frandy Espinal", 0, 'L');
  $this->MultiCell(0, 6, "Field Supervisor: Adelqui Acosta", 0, 'L');
  $this->MultiCell(0, 6, "Field Technicians: Jordany Amparo", 0, 'L');
  $this->MultiCell(0, 6, utf8_decode("Chief laboratory: Wendin De Jesús Mendoza"), 0, 'L');

}

if (in_array($this->day_of_week, [3, 4, 5, 6])) { // Miércoles a Sábado
  $this->MultiCell(0, 6, "Contractor Lab Technicians: Rafael Reyes, Darielvy Felix, Jordany Almonte, Joel Ledesma", 0, 'L');
  $this->MultiCell(0, 6, "PV Laboratory Supervisors: Laura Sanchez", 0, 'L');
  $doc_control = $this->week_number % 2 === 0 ? "Yamilexi Mejia, Arturo Santana" : "Arturo Santana, Yamilexi Mejia";
  $this->MultiCell(0, 6, "Lab Document Control: {$doc_control}", 0, 'L');
  $this->MultiCell(0, 6, "Field Supervisor: Victor Mercedes", 0, 'L');
  $this->MultiCell(0, 6, "Field Technicians: Luis Monegro", 0, 'L');
}



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

$pdf = new PDF($fecha_en);

$pdf->AddPage();

$pdf->section_title("2. Summary of  Daily Activities");
$pdf->section_table(["Actividad", "Cantidad"], [
  ["Registradas", get_count("lab_test_requisition_form", "Registed_Date", $start, $end)],
  ["Preparadas", get_count("test_preparation", "Register_Date", $start, $end)],
  ["Realizadas", get_count("test_realization", "Register_Date", $start, $end)],
  ["Entregadas", get_count("test_delivery", "Register_Date", $start, $end)]
], [90, 40]);

$pdf->section_title("3. Resumen por Cliente del Día");
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
$pdf->section_table(["Technician", "Process", "Quantity"], $t_rows, [60, 50, 40]);

$pdf->section_title("6. Test By Type");
$tipos = resumen_tipo($start, $end);
$type_rows = [];
foreach ($tipos as $r) {
  $type_rows[] = [$r['Test_Type'], $r['etapa'], $r['total']];
}
$pdf->section_table(["Test Type", "Etapa", "Quantity"], $type_rows, [70, 50, 30]);

$pdf->section_title("7. Observations / Non-Conformities");
$pdf->SetFont('Arial', '', 10);
$obs = observaciones($start, $end);
if (count($obs) > 0) {
  foreach ($obs as $o) {
    $pdf->MultiCell(0, 6, "- {$o['Sample_ID']}: {$o['Comment']}");
  }
} else {
  $pdf->MultiCell(0, 6, "No Observations registered.");
}
$pdf->Ln(5);

$pdf->section_title("8. Responsible");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, "Report prepared by", 1);
$pdf->Cell(130, 8, utf8_decode($nombre_responsable), 1, 1);




ob_end_clean();
$pdf->Output('I', "Reporte_Diario_{$fecha}.pdf");
