<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');

$user = current_user();
$nombre_responsable = $user['name']; // o 'full_name' o el campo correcto


$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_en = $fecha_obj ? $fecha_obj->format('Y/m/d') : 'Fecha inválida';
$start = date('Y-m-d H:i:s', strtotime("$fecha -1 day 16:00:00"));
$end   = date('Y-m-d H:i:s', strtotime("$fecha 15:59:59"));

function get_count($table, $field, $start, $end) {
  $r = find_by_sql("SELECT COUNT(*) as total FROM {$table} WHERE {$field} BETWEEN '{$start}' AND '{$end}'");
  return (int)$r[0]['total'];
}

function resumen_entregas_por_cliente( $end) {
  $stats = [];
  $inicio = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($end)));

  // Obtener solicitudes de ensayos en el rango de fechas
  $solicitudes = find_by_sql("
    SELECT Client, Sample_ID, Sample_Number, Test_Type
    FROM lab_test_requisition_form
    WHERE Sample_Date BETWEEN '{$inicio}' AND '{$end}'
  ");

  // Obtener todas las entregas registradas
  $entregas = find_by_sql("SELECT Sample_Name, Sample_Number, Test_Type FROM test_delivery");

  // Mapear entregas
  $entregado_map = [];
  foreach ($entregas as $e) {
    $key = strtoupper(trim($e['Sample_Name'])) . '|' . strtoupper(trim($e['Sample_Number'])) . '|' . strtoupper(trim($e['Test_Type']));
    $entregado_map[$key] = true;
  }

  // Procesar progreso por cliente
  foreach ($solicitudes as $s) {
    $cliente = $s['Client'] ?: 'SIN CLIENTE';
    $sample_id = strtoupper(trim($s['Sample_ID']));
    $sample_num = strtoupper(trim($s['Sample_Number']));
    $test_type = strtoupper(trim($s['Test_Type']));
    $key = $sample_id . '|' . $sample_num . '|' . $test_type;

    if (!isset($stats[$cliente])) {
      $stats[$cliente] = ['solicitados' => 0, 'entregados' => 0];
    }

    $stats[$cliente]['solicitados']++;

    if (isset($entregado_map[$key])) {
      $stats[$cliente]['entregados']++;
    }
  }

  return $stats;
}


function count_by_sample($table, $sample, $field = 'Sample_Name') {
  return count(find_by_sql("SELECT id FROM {$table} WHERE {$field} = '{$sample}'"));
}

function muestras_nuevas($start, $end) {
  return find_by_sql("SELECT Sample_ID, Sample_Number, Structure, Client, Test_Type FROM lab_test_requisition_form WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'");
}

function ensayos_pendientes( $start, $end) {
  $requisitions = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Sample_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
  ");

  $tables_to_check = [
    'test_preparation',
    'test_realization',
    'test_delivery'
  ];

  $indexed_status = [];

  foreach ($tables_to_check as $table) {
    $column_fecha = ($table == 'test_reviewed') ? 'Start_Date' : 'Register_Date';
    $data = find_by_sql("SELECT Sample_Name, Sample_Number, Test_Type FROM {$table} WHERE {$column_fecha} BETWEEN '{$start}' AND '{$end}'");
    foreach ($data as $row) {
      $key = strtoupper(trim($row['Sample_Name'])) . "|" . strtoupper(trim($row['Sample_Number'])) . "|" . strtoupper(trim($row['Test_Type']));
      $indexed_status[$key] = true;
    }
  }

  $pending_tests = [];

  foreach ($requisitions as $r) {
    $sample_id = strtoupper(trim($r['Sample_ID']));
    $sample_num = strtoupper(trim($r['Sample_Number']));
    $test_types = json_decode($r['Test_Type'], true); // asegurarse que es JSON

    if (!is_array($test_types)) continue;

    foreach ($test_types as $raw_test) {
      $test = strtoupper(trim($raw_test));
      $key = $sample_id . "|" . $sample_num . "|" . $test;

      if (!isset($indexed_status[$key])) {
        $pending_tests[] = [
          'Sample_ID' => $r['Sample_ID'],
          'Sample_Number' => $r['Sample_Number'],
          'Test_Type' => $raw_test,
          'Sample_Date' => $r['Sample_Date']
        ];
      }
    }
  }

  return $pending_tests;
}


function resumen_tecnico($start, $end) {
  return find_by_sql("SELECT Technician, COUNT(*) as total, 'In Preparation' as etapa FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) as total, 'In Realization' FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) as total, 'Completed' FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Technician");
}

function resumen_tipo($start, $end) {
  return find_by_sql("SELECT Test_Type, COUNT(*) as total, 'In Preparation' as etapa FROM test_preparation WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type, COUNT(*) as total, 'In Realization' FROM test_realization WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type, COUNT(*) as total, 'Completed' FROM test_delivery WHERE Register_Date BETWEEN '{$start}' AND '{$end}' GROUP BY Test_Type");
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
$pdf->section_table(["Activities", "Quantity"], [
  ["Requisitioned", get_count("lab_test_requisition_form", "Registed_Date", $start, $end)],
  ["In Preparation", get_count("test_preparation", "Register_Date", $start, $end)],
  ["In Realizacion", get_count("test_realization", "Register_Date", $start, $end)],
  ["Completed", get_count("test_delivery", "Register_Date", $start, $end)]
], [90, 40]);

$pdf->section_title("3. Client Summary of Delivered Tests");


$clientes = resumen_entregas_por_cliente($start, $end);
$rows = [];
foreach ($clientes as $cli => $d) {
  $pct = $d['solicitados'] > 0 ? round($d['entregados'] * 100 / $d['solicitados']) : 0;
  $rows[] = [$cli, $d['solicitados'], $d['entregados'], "$pct%"];
}

$pdf->section_table(["Client", "Requested", "Delivered", "%"], $rows, [50, 35, 35, 25]);
$pdf->Ln(4);

$pdf->section_title("4. Newly Registered Samples");
$muestras = muestras_nuevas($start, $end);
$rows = [];
foreach ($muestras as $m) {
 $rows = [];
foreach ($muestras as $m) {
  $rows[] = [$m['Sample_ID'] . ' -' . $m['Sample_Number'], $m['Structure'], $m['Client'], $m['Test_Type']];
}


}
$pdf->section_table(["Sample ID", "Structure", "Client", "Test Type"], $rows, [45, 35, 35, 75]);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Test Legend: AR= Acid Reativity, GS= Grain Size, SG= Specific Gravity, SP= Standard Proctor, MP= Modified Proctor, AL= Atterberg Limit,   ', 0, 1);
$pdf->Cell(0, 5, 'HY= Hidrometer, DHY= Double Hydromter, SCT= Sand Castle, SND= Soundness, LAA= Los Angeles Abrasion, MC= Moisture Content, ', 0, 1);
$pdf->Cell(0, 5, 'PLT= Point Load, UCS= Simple Compression, BTS, Brazilian, Shape= Particle Shape,  ', 0, 1);
$pdf->Ln(4);
$pdf->section_title("5. Summary of Tests by Technician ");
$tec = resumen_tecnico($start, $end);
$t_rows = [];
foreach ($tec as $r) {
  $t_rows[] = [$r['Technician'], $r['etapa'], $r['total']];
}
$pdf->section_table(["Technician", "Process", "Quantity"], $t_rows, [60, 50, 40]);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, 'Tech. Legend: WM= Wilson Martinez, JV= Jonathan Vargas, RV= Roni Vargas, RL =Rafy Leocadio,', 0, 1);
$pdf->Cell(0, 4, 'RR= Rafael Reyes, JL= Joel Ledesma, DF= Darielvy Felix, JA= Jordany Almonte , ', 0, 1);
$pdf->Ln(5);


$pdf->section_title("6. Distribution of Tests by Type");
$tipos = resumen_tipo($start, $end);
$type_rows = [];
foreach ($tipos as $r) {
  $type_rows[] = [$r['Test_Type'], $r['etapa'], $r['total']];
}
$pdf->section_table(["Test Type", "Process", "Quantity"], $type_rows, [70, 50, 30]);



$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Test Legend: AR= Acid Reativity, GS= Grain Size, SG= Specific Gravity, SP= Standard Proctor, MP= Modified Proctor, AL= Atterberg Limit,   ', 0, 1);
$pdf->Cell(0, 5, 'HY= Hidrometer, DHY= Double Hydromter, SCT= Sand Castle, SND= Soundness, LAA= Los Angeles Abrasion, MC= Moisture Content, ', 0, 1);
$pdf->Cell(0, 5, 'PLT= Point Load, UCS= Simple Compression, BTS, Brazilian, Shape= Particle Shape,  ', 0, 1);
$pdf->Ln(4);

$pdf->section_title("7. Pending Tests");

// Definir fecha de inicio exclusivo para ensayos pendientes (1 mes atrás desde $end)
$start_pendientes = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($end)));

// Obtener los ensayos pendientes en ese rango
$pendientes = ensayos_pendientes($start_pendientes, $end);

$rows = [];
foreach ($pendientes as $p) {
  $rows[] = [$p['Sample_ID'], $p['Sample_Number'], $p['Test_Type'], $p['Sample_Date']];
}





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
