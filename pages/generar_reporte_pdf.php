<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ============================================================
   1. SEMANA ISO
============================================================ */
$anio  = isset($_GET['anio']) ? (int)$_GET['anio'] : date('o');
$semana = isset($_GET['semana']) ? (int)$_GET['semana'] : date('W');

// Lunes
$start = new DateTime();
$start->setISODate($anio, $semana, 1);
$start_str = $start->format('Y-m-d 00:00:00');

// Domingo
$end = new DateTime();
$end->setISODate($anio, $semana, 7);
$end_str = $end->format('Y-m-d 23:59:59');

// Usuario
$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* ============================================================
   2. HELPER CONTAR
============================================================ */
function get_count($table, $field, $start, $end) {
    $res = find_by_sql("
        SELECT COUNT(*) AS t
        FROM {$table}
        WHERE {$field} BETWEEN '{$start}' AND '{$end}'
    ");
    return (int)($res[0]['t'] ?? 0);
}

/* ============================================================
   3. CONSULTAS DE LA SEMANA ISO
============================================================ */

// 3.1 Requisiciones por día
function resumen_diario_semana($start, $end) {
    return find_by_sql("
        SELECT DATE(Registed_Date) AS d, COUNT(*) AS t
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY DATE(Registed_Date)
        ORDER BY d
    ");
}

// 3.2 Ensayos por tipo
function resumen_tipo_semana($start, $end) {
    return find_by_sql("
        SELECT UPPER(TRIM(Test_Type)) AS Test_Type, COUNT(*) AS total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type
        ORDER BY total DESC
    ");
}

// 3.3 Por cliente
function resumen_cliente_semana($start, $end) {
    return find_by_sql("
        SELECT 
            r.Client,
            COUNT(*) AS solicitados,
            SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) AS entregados
        FROM lab_test_requisition_form r
        LEFT JOIN test_delivery d
             ON r.Sample_ID = d.Sample_ID
            AND r.Sample_Number = d.Sample_Number
            AND r.Test_Type = d.Test_Type
        WHERE r.Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY r.Client
        ORDER BY solicitados DESC
    ");
}

/* ============================================================
   4. CLASE PDF
============================================================ */
class PDF_WEEK extends FPDF {

    public $week, $year;

    function __construct($week, $year) {
        parent::__construct();
        $this->week = $week;
        $this->year = $year;
    }

    function Header() {
        if ($this->PageNo() > 1) return;

        if (file_exists("../assets/img/Pueblo-Viejo.jpg")) {
            $this->Image("../assets/img/Pueblo-Viejo.jpg", 10, 10, 50);
        }

        $this->SetFont("Arial","B",16);
        $this->SetXY(120, 12);
        $this->Cell(80, 8, "WEEKLY LABORATORY REPORT", 0, 1, "R");

        $s = new DateTime();
        $s->setISODate($this->year, $this->week, 1);
        $e = new DateTime();
        $e->setISODate($this->year, $this->week, 7);

        $range = $s->format("d M Y") . " - " . $e->format("d M Y");

        $this->SetFont("Arial","",11);
        $this->SetXY(120, 20);
        $this->Cell(80, 7, "ISO Week {$this->week}  ({$range})", 0, 1, "R");

        $this->Ln(15);

        /* ===============================================
           PERSONAL COMPLETO DE LA SEMANA (TODOS)
        =============================================== */
        $this->SetFont('Arial','B',12);
        $this->Cell(0,8,"1. Weekly Personnel Assigned",0,1,'L');

        $this->SetFont('Arial','',10);
        $this->MultiCell(0,6,
            "PV Laboratory Supervisors: Diana Vazquez, Victor Mercedes\n".
            "Chief Laboratory: Wendin De Jesús\n".
            "Contractor Lab Technicians: Wilson Martinez, Rafy Leocadio, ".
            "Rony Vargas, Jonathan Vargas, Rafael Reyes, Darielvy Felix, ".
            "Jordany Almonte, Melvin Castillo\n".
            "Lab Document Control: Frandy Espinal, Arturo Santana, Yamilexi Mejia"
        );

        $this->Ln(5);
    }

    function section_title($t) {
        $this->SetFont("Arial","B",12);
        $this->SetFillColor(200,220,255);
        $this->Cell(0,8,utf8_decode($t),0,1,'L',true);
        $this->Ln(2);
    }

    function table_header($cols,$w) {
        $this->SetFont("Arial","B",10);
        foreach ($cols as $i=>$c) {
            $this->Cell($w[$i],7,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
    }

    function table_row($data,$w) {
        $this->SetFont("Arial","",10);
        foreach ($data as $i=>$d) {
            $this->Cell($w[$i],7,utf8_decode($d),1,0,'C');
        }
        $this->Ln();
    }
}

$pdf = new PDF_WEEK($semana,$anio);
$pdf->AddPage();

/* ============================================================
   5. SECCIÓN 2 — WEEKLY SUMMARY
============================================================ */
$pdf->section_title("2. Weekly Summary of Activities");

$pdf->table_header(["Activity","Qty"], [90,40]);

$pdf->table_row(["Requisitioned",  get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str)], [90,40]);
$pdf->table_row(["In Preparation", get_count("test_preparation","Register_Date",$start_str,$end_str)], [90,40]);
$pdf->table_row(["In Realization", get_count("test_realization","Register_Date",$start_str,$end_str)], [90,40]);
$pdf->table_row(["Completed",      get_count("test_delivery","Register_Date",$start_str,$end_str)], [90,40]);

$pdf->Ln(10);

/* ============================================================
   6. SECCIÓN 3 — DAILY BREAKDOWN
============================================================ */
$pdf->section_title("3. Daily Breakdown");

$data_dia = resumen_diario_semana($start_str,$end_str);

$pdf->table_header(["Date","Samples"], [70,40]);

foreach ($data_dia as $d) {
    $pdf->table_row([
        date("D d-M",strtotime($d['d'])),
        $d['t']
    ], [70,40]);
}

$pdf->Ln(10);

/* ============================================================
   7. SECCIÓN 4 — TEST DISTRIBUTION
============================================================ */
$pdf->section_title("4. Test Distribution by Type");

$data_tipo = resumen_tipo_semana($start_str,$end_str);

$pdf->table_header(["Test Type","Completed"], [90,40]);

foreach ($data_tipo as $t) {
    $pdf->table_row([
        $t['Test_Type'],
        $t['total']
    ],
    [90,40]);
}

$pdf->Ln(10);

/* ============================================================
   GRÁFICO 1 — SAMPLES PER DAY
============================================================ */
function chart_samples_week($pdf,$data) {
    if (empty($data)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 1: Samples Registered Per Day",0,1);

    $x0=20; $y0=$pdf->GetY()+5;
    $w=150; $h=40;

    $max=1;
    foreach($data as $d){ if($d['t']>$max) $max=$d['t']; }

    $pdf->Line($x0,$y0,$x0,$y0+$h);
    $pdf->Line($x0,$y0+$h,$x0+$w,$y0+$h);

    $n=count($data);
    $bw=($w-20)/$n;
    $x=$x0+10;

    foreach($data as $d){
        $bh=($d['t']/$max)*$h;
        $y=$y0+$h-$bh;

        $pdf->SetFillColor(100,149,237);
        $pdf->Rect($x,$y,$bw-4,$bh,"F");

        $pdf->SetFont("Arial","",7);
        $pdf->SetXY($x,$y0+$h+2);
        $pdf->MultiCell($bw-4,3,date("D",strtotime($d['d'])),0,'C');

        $x+=$bw;
    }

    $pdf->Ln(10);
}

chart_samples_week($pdf,$data_dia);

/* ============================================================
   GRÁFICO 2 — TEST DISTRIBUTION
============================================================ */
function chart_tests_week($pdf,$data) {
    if (empty($data)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 2: Test Distribution by Type",0,1);

    $x0=20; $y0=$pdf->GetY()+5;
    $w=170; $h=50;

    $max=1;
    foreach($data as $d){ if($d['total']>$max) $max=$d['total']; }

    $pdf->Line($x0,$y0,$x0,$y0+$h);
    $pdf->Line($x0,$y0+$h,$x0+$w,$y0+$h);

    $n=count($data);
    $bw=($w-20)/$n;
    $x=$x0+10;

    foreach($data as $d){
        $bh=($d['total']/$max)*$h;
        $y=$y0+$h-$bh;

        $pdf->SetFillColor(50,180,120);
        $pdf->Rect($x,$y,$bw-4,$bh,"F");

        $pdf->SetFont("Arial","",6);
        $pdf->SetXY($x,$y0+$h+2);
        $pdf->MultiCell($bw-4,3,$d['Test_Type'],0,'C');

        $x+=$bw;
    }

    $pdf->Ln(10);
}

chart_tests_week($pdf,$data_tipo);

$pdf->Ln(8);
/* ============================================================
   8. GRÁFICO 3 — CLIENT COMPLETION
============================================================ */
function chart_client_week($pdf,$clientes){
    if(empty($clientes)) return;

    $data=[];
    foreach($clientes as $c){
        $sol=(int)$c['solicitados'];
        $ent=(int)$c['entregados'];
        $pct=$sol>0 ? round(($ent*100)/$sol) : 0;

        $label=strtoupper(trim($c['Client']));
        if(strlen($label)>14){
            $label=substr($label,0,10)."...";
        }

        $data[]=[
            "label"=>$label,
            "pct"=>$pct
        ];
    }

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 3: Client Completion Percentage",0,1);

    $x0=20; $y0=$pdf->GetY()+5;
    $w=170; $h=50;

    $max=0;
    foreach($data as $d){ if($d['pct']>$max) $max=$d['pct']; }
    if($max<=0) return;

    if($pdf->GetY()+80>250){
        $pdf->AddPage();
    }

    $pdf->Line($x0,$y0,$x0,$y0+$h);
    $pdf->Line($x0,$y0+$h,$x0+$w,$y0+$h);

    $n=count($data);
    $bw=($w-20)/$n;
    $x=$x0+10;

    foreach($data as $d){
        $pct=$d['pct'];
        $bh=($pct/$max)*$h;
        $y=$y0+$h-$bh;

        $pdf->SetFillColor(255,165,0);
        $pdf->Rect($x,$y,$bw-4,$bh,"F");

        $pdf->SetFont("Arial","B",7);
        $pdf->SetXY($x,$y-4);
        $pdf->Cell($bw-4,4,$pct."%",0,0,'C');

        $pdf->SetFont("Arial","",6);
        $pdf->SetXY($x,$y0+$h+2);
        $pdf->MultiCell($bw-4,3,$d['label'],0,'C');

        $x+=$bw;
    }
    $pdf->Ln(12);
}

$clientes_resumen=resumen_cliente_semana($start_str,$end_str);
chart_client_week($pdf,$clientes_resumen);

/* ============================================================
   9. SECCIÓN 5 — NEWLY REGISTERED SAMPLES
============================================================ */
$pdf->section_title("5. Newly Registered Samples (Weekly)");

$muestras=find_by_sql("
    SELECT Sample_ID,Sample_Number,Structure,Client,Test_Type
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    ORDER BY Registed_Date
");

$pdf->table_header(
    ["Sample ID","Structure","Client","Test Type"],
    [45,35,35,75]
);

foreach($muestras as $m){
    $pdf->table_row([
        $m['Sample_ID']."-".$m['Sample_Number'],
        $m['Structure'],
        $m['Client'],
        $m['Test_Type']
    ],
    [45,35,35,75]);
}

$pdf->Ln(10);

/* ============================================================
   10. SECCIÓN 6 — SUMMARY BY TECHNICIAN
============================================================ */
$pdf->section_title("6. Summary of Tests by Technician (Weekly)");

$tec=find_by_sql("
    SELECT Technician,COUNT(*) total,'In Preparation' etapa
    FROM test_preparation
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician
    UNION ALL
    SELECT Technician,COUNT(*) total,'In Realization'
    FROM test_realization
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician
    UNION ALL
    SELECT Technician,COUNT(*) total,'Completed'
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician
");

$pdf->table_header(["Technician","Process","Qty"],[60,50,20]);

foreach($tec as $t){
    $pdf->table_row([$t['Technician'],$t['etapa'],$t['total']], [60,50,20]);
}

$pdf->Ln(10);

/* ============================================================
   11. SECCIÓN 7 — SUMMARY BY TEST TYPE
============================================================ */
$pdf->section_title("7. Summary of Tests by Type (Weekly)");

$tipos=find_by_sql("
    SELECT Test_Type,COUNT(*) total,'In Preparation' etapa
    FROM test_preparation
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type
    UNION ALL
    SELECT Test_Type,COUNT(*) total,'In Realization'
    FROM test_realization
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP_BY Test_Type
    UNION ALL
    SELECT Test_Type,COUNT(*) total,'Completed'
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type
");

$pdf->table_header(["Test Type","Process","Qty"],[70,50,20]);

foreach($tipos as $t){
    $pdf->table_row([$t['Test_Type'],$t['etapa'],$t['total']], [70,50,20]);
}

$pdf->Ln(10);

/* ============================================================
   12. SECCIÓN 8 — PENDING TESTS (WEEKLY)
============================================================ */
$pdf->section_title("8. Pending Tests (Weekly)");

$pendientes=find_by_sql("
    SELECT 
        r.Client,
        r.Sample_ID,
        r.Sample_Number,
        r.Test_Type,
        r.Sample_Date
    FROM lab_test_requisition_form r
    WHERE r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND NOT EXISTS(
        SELECT 1 FROM test_delivery d
        WHERE d.Sample_ID=r.Sample_ID
          AND d.Sample_Number=r.Sample_Number
          AND d.Test_Type=r.Test_Type
      )
");

$pdf->table_header(
    ["Client","Sample ID","Sample No.","Test Type","Date"],
    [40,35,35,40,25]
);

foreach($pendientes as $p){
    $pdf->table_row([
        $p['Client'],
        $p['Sample_ID'],
        $p['Sample_Number'],
        $p['Test_Type'],
        date("d-M",strtotime($p['Sample_Date']))
    ],
    [40,35,35,40,25]);
}

$pdf->Ln(10);

/* ============================================================
   13. SECCIÓN 9 — SUMMARY DAM CONSTRUCTION TEST
============================================================ */
$pdf->section_title("9. Summary of Dam Constructions Test");

$ensayos=find_by_sql("
    SELECT *
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$pdf->table_header(
    ["Sample","Structure","Mat.","Test Type","Condition","Comments"],
    [35,25,20,30,20,60]
);

foreach($ensayos as $e){
    $pdf->table_row([
        $e['Sample_ID']."-".$e['Sample_Number'],
        $e['Structure'],
        $e['Material_Type'],
        $e['Test_Type'],
        $e['Test_Condition'],
        substr($e['Comments'],0,60)
    ],
    [35,25,20,30,20,60]);
}

$pdf->Ln(10);

/* ============================================================
   14. SECCIÓN 10 — OBSERVATIONS / NON CONFORMITIES
============================================================ */
$pdf->section_title("10. Summary of Observations / Non-Conformities");

$ncr=find_by_sql("
    SELECT 
        Sample_ID,
        Sample_Number,
        Structure,
        Material_Type,
        Noconformidad
    FROM ensayos_reporte
    WHERE Noconformidad IS NOT NULL
      AND TRIM(Noconformidad) <> ''
      AND Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

$pdf->table_header(["Sample","Observations"],[45,145]);

foreach($ncr as $n){
    $pdf->table_row([
        $n['Sample_ID']."-".$n['Sample_Number']."-".$n['Material_Type'],
        substr($n['Noconformidad'],0,250)
    ],
    [45,145]);
}

$pdf->Ln(10);

/* ============================================================
   15. RESPONSIBLE
============================================================ */
$pdf->section_title("11. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(60,8,"Report prepared by:",1,0);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Weekly_Laboratory_Report_ISOWeek{$semana}_{$anio}.pdf");
<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('o');
$lastWeek = (int) date("W", strtotime("$anio-12-28"));
?>

<main id="main" class="main"> 
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-calendar3"></i> Weekly Reports — ISO <?= $anio ?></h1>
  </div>

  <form method="GET" class="mb-3 d-flex gap-2">
    <select name="anio" class="form-select" style="max-width:150px;">
      <?php for($y=date('Y');$y>=2020;$y--): ?>
        <option value="<?=$y?>" <?=$y==$anio?'selected':''?>><?=$y?></option>
      <?php endfor; ?>
    </select>
    <button class="btn btn-dark">Filter Year</button>
  </form>

  <section class="section">
    <div class="card shadow-sm">
      <div class="card-body pt-4">

        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>ISO Week</th>
              <th>Date Range</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
          <?php for($w=1;$w<=$lastWeek;$w++): 
              $d1=new DateTime(); $d1->setISODate($anio,$w,1);
              $d2=new DateTime(); $d2->setISODate($anio,$w,7);
          ?>
            <tr>
              <td><?=$w?></td>
              <td><?=$anio?> — Week <?=$w?></td>
              <td><?=$d1->format('d-m-Y')?> → <?=$d2->format('d-m-Y')?></td>

              <td>
                <a  class="btn btn-danger btn-sm"
                    target="_blank"
                    href="../pages/reporte_semanal_pdf.php?anio=<?=$anio?>&semana=<?=$w?>">
                    PDF
                </a>
              </td>
            </tr>
          <?php endfor; ?>
          </tbody>

        </table>

      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
