<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===============================
   1. DEFINIR SEMANA ISO
================================*/
$year = isset($_GET['anio']) ? (int)$_GET['anio'] : date('o');
$week = isset($_GET['semana']) ? (int)$_GET['semana'] : date('W');

// Obtener lunes y domingo de la semana ISO
$dt = new DateTime();
$dt->setISODate($year, $week, 1);
$start_str = $dt->format("Y-m-d 00:00:00");

$dt->setISODate($year, $week, 7);
$end_str = $dt->format("Y-m-d 23:59:59");

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* ===============================
   2. COUNT HELPER
================================*/
function get_count($table, $field, $start, $end) {
    $q = "SELECT COUNT(*) AS total FROM {$table}
          WHERE {$field} BETWEEN '{$start}' AND '{$end}'";
    $r = find_by_sql($q);
    return (int)($r[0]['total'] ?? 0);
}

/* ===============================
   3. CONSULTAS SEMANALES
================================*/
function resumen_diario($start, $end){
    return find_by_sql("
        SELECT DATE(Registed_Date) AS dia, COUNT(*) AS total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY DATE(Registed_Date)
        ORDER BY dia ASC
    ");
}

function resumen_tipo($start,$end){
    return find_by_sql("
        SELECT UPPER(TRIM(Test_Type)) AS Test_Type,
               COUNT(*) AS total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type
    ");
}

function resumen_cliente($start,$end){
    return find_by_sql("
        SELECT 
          UPPER(TRIM(r.Client)) AS Client,
          COUNT(*) AS solicitados,
          SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) AS entregados
        FROM lab_test_requisition_form r
        LEFT JOIN test_delivery d
        ON r.Sample_ID=d.Sample_ID
        AND r.Sample_Number=d.Sample_Number
        AND r.Test_Type=d.Test_Type
        WHERE r.Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY r.Client
        ORDER BY solicitados DESC
    ");
}

/* ===============================
   4. PDF CLASS
================================*/
class PDF_WEEKLY extends FPDF {

    public $week; 
    public $year;

    function __construct($week,$year){
        parent::__construct();
        $this->week = $week;
        $this->year = $year;
    }

    function Header() {

        if ($this->PageNo() > 1) return;

        if (file_exists('../assets/img/Pueblo-Viejo.jpg')) {
            $this->Image('../assets/img/Pueblo-Viejo.jpg', 10, 10, 55);
        }

        $this->SetFont('Arial','B',16);
        $this->SetXY(120,12);
        $this->Cell(80,8,'WEEKLY LABORATORY REPORT',0,1,'R');

        $start = new DateTime();
        $start->setISODate($this->year,$this->week,1);
        $end = new DateTime();
        $end->setISODate($this->year,$this->week,7);

        $this->SetFont('Arial','',11);
        $this->SetXY(120,22);
        $this->Cell(80,7,"ISO WEEK {$this->week}  ( ".$start->format('d M Y')." - ".$end->format('d M Y')." )",0,1,'R');

        $this->Ln(10);
        $this->section_title("1.  Personnel Assigned");

        // SEMANA = TODOS
        $this->SetFont('Arial','',10);
$this->MultiCell(0,6,utf8_decode("
Chief Laboratory : Wendin De Jesús.
Document Control : Yamilexi Mejía, Arturo Santana, Frandy Esppinal.
Lab Supervisor : Diana Vázquez, Victor Mercedes.
Contractor Technician : Wilson Martinez, Rafy Leocadio, Rony Vargas, Jonathan Vargas.
Contractor Technician : Rafael Reyes,Darielvy Félix, Jordany Almonte, Melvin Castillo.
"));

        $this->Ln(3);
    }

    function section_title($txt){
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(200,220,255);
        $this->Cell(0,8,utf8_decode($txt),0,1,'L',true);
        $this->Ln(3);
    }

    function table_header($cols,$w){
        $this->SetFont('Arial','B',10);
        foreach($cols as $i=>$c){
            $this->Cell($w[$i],7,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',10);
        foreach($data as $i=>$d){
            $this->Cell($w[$i],7,utf8_decode($d),1,0,'C');
        }
        $this->Ln();
    }
}

$pdf = new PDF_WEEKLY($week,$year);
$pdf->AddPage();

/* ===============================
   5. SECTION 2 — WEEKLY SUMMARY
================================*/
$pdf->section_title("2. Weekly Summary of Activities");

$req = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$prep = get_count("test_preparation","Register_Date",$start_str,$end_str);
$real = get_count("test_realization","Register_Date",$start_str,$end_str);
$del = get_count("test_delivery","Register_Date",$start_str,$end_str);

$pdf->table_header(["Activity","Total"],[90,30]);
$pdf->table_row(["Requisitioned",$req],[90,30]);
$pdf->table_row(["In Preparation",$prep],[90,30]);
$pdf->table_row(["In Realization",$real],[90,30]);
$pdf->table_row(["Completed",$del],[90,30]);
$pdf->Ln(10);

/* ===============================
   6. SECTION 3 — DAILY BREAKDOWN
================================*/
$pdf->section_title("3. Daily Breakdown (ISO Week)");

$data_dia = resumen_diario($start_str,$end_str);

$pdf->table_header(["Date","Registered Samples"],[60,60]);

foreach($data_dia as $d){
    $pdf->table_row([
        date("D d-M",strtotime($d['dia'])),
        $d['total']
    ],[60,60]);
}

$pdf->Ln(10);
/* ===============================
   7. SECTION 4 — TEST DISTRIBUTION BY TYPE
================================*/
$pdf->section_title("4. Test Distribution by Type");

$data_tipo = resumen_tipo($start_str,$end_str);

$pdf->table_header(["Test Type","Completed"],[80,40]);

foreach($data_tipo as $t){
    $pdf->table_row([$t['Test_Type'],$t['total']],[80,40]);
}

$pdf->Ln(10);

/* ===============================
   8. GRÁFICOS
================================*/

// 8.1 — Samples Per Day
function chart_samples($pdf,$data){
    if(empty($data)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 1: Samples Registered Per Day",0,1);

    $chartX = 20;
    $chartY = $pdf->GetY()+5;
    $chartW = 170;
    $chartH = 50;

    $max = 1;
    foreach($data as $d){
        if($d['total']>$max) $max=$d['total'];
    }

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $bars = count($data);
    $bw = ($chartW-20)/$bars;
    $x = $chartX+10;

    foreach($data as $d){
        $h = ($d['total']/$max)*$chartH;
        $y = $chartY + $chartH - $h;

        $pdf->SetFillColor(100,149,237);
        $pdf->Rect($x,$y,$bw-4,$h,"F");

        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($x,$chartY+$chartH+2);
        $pdf->MultiCell($bw-4,3,date("D",strtotime($d['dia'])),0,'C');

        $x += $bw;
    }

    $pdf->Ln(12);
}

// 8.2 — Tests By Type
function chart_types($pdf,$data){
    if(empty($data)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 2: Tests Completed By Type",0,1);

    $chartX = 20;
    $chartY = $pdf->GetY()+5;
    $chartW = 170;
    $chartH = 50;

    $max = 1;
    foreach($data as $t){
        if($t['total'] > $max) $max = $t['total'];
    }

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $bars = count($data);
    $bw = ($chartW-20)/$bars;
    $x = $chartX+10;

    foreach($data as $t){
        $h = ($t['total']/$max)*$chartH;
        $y = $chartY + $chartH - $h;

        $pdf->SetFillColor(50,180,120);
        $pdf->Rect($x,$y,$bw-4,$h,"F");

        $pdf->SetFont('Arial','',6);
        $pdf->SetXY($x,$chartY+$chartH+2);
        $pdf->MultiCell($bw-4,3,$t['Test_Type'],0,'C');

        $x += $bw;
    }

    $pdf->Ln(12);
}

// 8.3 — Client Completion
function chart_client($pdf,$clientData){
    if(empty($clientData)) return;

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,"Graph 3: Client Completion Percentage",0,1);

    $data=[];
    foreach($clientData as $c){
        $sol=(int)$c['solicitados'];
        $ent=(int)$c['entregados'];
        $pct=$sol>0?round(($ent*100)/$sol):0;

        $data[]=[
            "label"=>$c['Client'],
            "pct"=>$pct
        ];
    }

    $chartX=20;
    $chartY=$pdf->GetY()+5;
    $chartW=170;
    $chartH=50;

    $pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
    $pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

    $bars=count($data);
    $bw=($chartW-20)/$bars;
    $x=$chartX+10;

    foreach($data as $d){
        $h=($d["pct"]/100)*$chartH;
        $y=$chartY+$chartH-$h;

        $pdf->SetFillColor(255,165,0);
        $pdf->Rect($x,$y,$bw-4,$h,"F");

        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY($x,$y-4);
        $pdf->Cell($bw-4,4,$d["pct"]."%",0,0,'C');

        $pdf->SetFont('Arial','',6);
        $pdf->SetXY($x,$chartY+$chartH+2);
        $pdf->MultiCell($bw-4,3,$d["label"],0,'C');

        $x+=$bw;
    }

    $pdf->Ln(12);
}

// Ejecutar gráficos
chart_samples($pdf,$data_dia);
chart_types($pdf,$data_tipo);

$clientes_res = resumen_cliente($start_str,$end_str);

// Asegurar que no se rompa página
if ($pdf->GetY() > 210) $pdf->AddPage();

chart_client($pdf,$clientes_res);

$pdf->Ln(10);

/* ===============================
   9. SECTION 5 — NEWLY REGISTERED SAMPLES
================================*/
$pdf->section_title("5. Newly Registered Samples (Weekly)");

$muestras = find_by_sql("
    SELECT *
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
    ORDER BY Registed_Date ASC
");

$pdf->table_header(["Sample","Structure","Client","Test Type"],[45,30,35,60]);

foreach($muestras as $m){
    $pdf->table_row([
        $m['Sample_ID']."-".$m['Sample_Number'],
        $m['Structure'],
        $m['Client'],
        $m['Test_Type']
    ],[45,30,35,60]);
}

$pdf->Ln(10);

/* ===============================
   10. SECTION 6 — TESTS BY TECHNICIAN
================================*/
$pdf->section_title("6. Summary of Tests by Technician");

$tec = find_by_sql("
    SELECT Technician, COUNT(*) total, 'In Preparation' etapa
    FROM test_preparation
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) total, 'In Realization'
    FROM test_realization
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician
    UNION ALL
    SELECT Technician, COUNT(*) total, 'Completed'
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Technician
");

$pdf->table_header(["Technician","Process","Qty"],[70,50,20]);

foreach($tec as $t){
    $pdf->table_row([$t['Technician'],$t['etapa'],$t['total']], [70,50,20]);
}

$pdf->Ln(10);
/* ===============================
   11. SECTION 7 — Summary of Test Types
================================*/
$pdf->section_title("7. Summary of Tests by Type");

$tipos = find_by_sql("
    SELECT Test_Type, COUNT(*) total, 'In Preparation' etapa
    FROM test_preparation
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type

    UNION ALL

    SELECT Test_Type, COUNT(*) total, 'In Realization'
    FROM test_realization
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type

    UNION ALL

    SELECT Test_Type, COUNT(*) total, 'Completed'
    FROM test_delivery
    WHERE Register_Date BETWEEN '{$start_str}' AND '{$end_str}'
    GROUP BY Test_Type
");

$pdf->table_header(["Test Type","Process","Qty"],[70,50,20]);

foreach($tipos as $t){
    $pdf->table_row([$t['Test_Type'],$t['etapa'],$t['total']], [70,50,20]);
}

$pdf->Ln(10);

/* ===============================
   12. SECTION 8 — Pending Tests
================================*/
$pdf->section_title("8. Pending Tests");

$pendientes = find_by_sql("
    SELECT r.*
    FROM lab_test_requisition_form r
    WHERE r.Registed_Date BETWEEN '{$start_str}' AND '{$end_str}'
      AND NOT EXISTS (
          SELECT 1 FROM test_delivery d
          WHERE d.Sample_ID=r.Sample_ID
            AND d.Sample_Number=r.Sample_Number
            AND d.Test_Type=r.Test_Type
      )
");

$pdf->table_header(["Sample","Number","Type","Date"],[40,35,50,25]);

foreach($pendientes as $p){
    $pdf->table_row([
        $p['Sample_ID'],
        $p['Sample_Number'],
        $p['Test_Type'],
        date("d-M",strtotime($p['Sample_Date']))
    ],
    [40,35,50,25]);
}

$pdf->Ln(10);

/* ===============================
   13. DAM CONSTRUCTION TESTS
================================*/
$pdf->section_title("9. Summary of Dam Construction Tests");

$ensayos = find_by_sql("
    SELECT *
    FROM ensayos_reporte
    WHERE Report_Date BETWEEN '{$start_str}' AND '{$end_str}'
");

/*
   Columnas: Sample | Structure | Test | Condition | Comments
   Anchos:    60      25          30      25         50  = 190
*/

$pdf->table_header(
    ["Sample","Structure","Test","Condition","Comments"],
    [40,25,50,25,50]
);

foreach($ensayos as $e){

    // Construir Sample Full
    $sampleFull = $e['Sample_ID']."-".$e['Sample_Number'];

    if (!empty($e['Material_Type'])) {
        $sampleFull .= " / ".$e['Material_Type'];
    }

    table_row_multiline($pdf,[
        $sampleFull,
        $e['Structure'],
        $e['Test_Type'],
        $e['Test_Condition'],
        $e['Comments']
    ], [60,25,30,25,50]);
}

$pdf->Ln(10);

/* ===============================
   14. SECTION 10 — Observations / NCR
================================*/
$pdf->section_title("10. Observations & Non-Conformities");

$ncr = find_by_sql("
    SELECT *
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

/* ===============================
   15. RESPONSIBLE
================================*/
$pdf->section_title("11. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(60,8,"Report prepared by",1);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Weekly_Lab_Report_Week{$week}_{$year}.pdf");
