<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors',1);
error_reporting(E_ALL);

/* ======================================================
   1. DEFINIR MES Y AÑO
====================================================== */
$year  = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');
$month = isset($_GET['mes'])  ? (int)$_GET['mes']  : date('n');

$start_str = "{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01 00:00:00";
$end_str   = date("Y-m-t 23:59:59", strtotime($start_str));

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* ======================================================
   2. FUNCIONES SQL
====================================================== */

function get_count($table,$field,$start,$end){
    $r = find_by_sql("
        SELECT COUNT(*) total
        FROM {$table}
        WHERE {$field} BETWEEN '{$start}' AND '{$end}'
    ");
    return (int)$r[0]['total'];
}

function monthly_top_client($start,$end){
    $rows = find_by_sql("
        SELECT Client, COUNT(*) total
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Client
        ORDER BY total DESC
        LIMIT 1
    ");
    return $rows ? $rows[0] : ['Client'=>'N/A','total'=>0];
}

function last_6_months(){
    return find_by_sql("
        SELECT DATE_FORMAT(Registed_Date,'%Y-%m') AS mes,
               COUNT(*) AS total
        FROM lab_test_requisition_form
        WHERE Registed_Date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes
        ORDER BY mes ASC
    ");
}

function pending_tests_expanded($start,$end){

    $reqs = find_by_sql("
        SELECT *
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $pend = [];

    foreach ($reqs as $r){
        $tests = explode(',', strtoupper(trim($r['Test_Type'])));

        foreach ($tests as $test){
            $test = trim($test);
            if ($test == '') continue;

            $exists = find_by_sql("
                SELECT 1
                FROM (
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_preparation
                    UNION ALL
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_realization
                    UNION ALL
                    SELECT Sample_ID, Sample_Number, Test_Type FROM test_delivery
                ) x
                WHERE x.Sample_ID     = '{$r['Sample_ID']}'
                  AND x.Sample_Number = '{$r['Sample_Number']}'
                  AND x.Test_Type     = '{$test}'
                LIMIT 1
            ");

            if (empty($exists)){
                $pend[] = [
                    'Sample_ID'     => $r['Sample_ID'],
                    'Sample_Number' => $r['Sample_Number'],
                    'Structure'     => $r['Structure'],
                    'Client'        => $r['Client'],
                    'Test_Type'     => $test,
                    'Registed_Date' => $r['Registed_Date'],
                ];
            }
        }
    }
    return $pend;
}

function tests_registered_expanded($start, $end){

    $reqs = find_by_sql("
        SELECT Sample_ID, Sample_Number, Test_Type
        FROM lab_test_requisition_form
        WHERE Registed_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $result = [];

    foreach($reqs as $r){
        $types = explode(',', strtoupper(trim($r['Test_Type'])));
        foreach($types as $t){
            $t = trim($t);
            if ($t == '') continue;
            if (!isset($result[$t])) $result[$t] = 0;
            $result[$t]++;
        }
    }
    return $result;
}

function tests_delivered_expanded($start, $end){
    $rows = find_by_sql("
        SELECT Test_Type
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
    ");

    $result = [];
    foreach($rows as $r){
        $t = strtoupper(trim($r['Test_Type']));
        if ($t == '') continue;
        if (!isset($result[$t])) $result[$t] = 0;
        $result[$t]++;
    }
    return $result;
}

function ncr_month($start,$end){
    return find_by_sql("
        SELECT *
        FROM ensayos_reporte
        WHERE Noconformidad IS NOT NULL
          AND TRIM(Noconformidad) <> ''
          AND Report_Date BETWEEN '{$start}' AND '{$end}'
    ");
}
/* ======================================================
   CLIENTES DEL MES (FUNCION NECESARIA)
====================================================== */
function monthly_clients($start,$end){
    return find_by_sql("
        SELECT 
            r.Client,
            COUNT(*) AS requested,
            SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) AS delivered
        FROM lab_test_requisition_form r
        LEFT JOIN test_delivery d
            ON r.Sample_ID = d.Sample_ID
           AND r.Sample_Number = d.Sample_Number
           AND r.Test_Type = d.Test_Type
        WHERE r.Registed_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY r.Client
        ORDER BY requested DESC
    ");
}

/* ======================================================
   3. FUNCIONES AUX
====================================================== */

function table_row_multiline($pdf,$data,$w){

    $pdf->SetFont('Arial','',9);
    $maxH = 5;

    foreach($data as $i=>$txt){
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i]-2,1);
        $h  = max(ceil($nb)*5, 7);
        if($h > $maxH) $maxH = $h;
    }

    if($pdf->GetY() + $maxH > $pdf->getPageBreakTrigger()){
        $pdf->AddPage();
        if($pdf->current_table_header){
            $pdf->table_header(
                $pdf->current_table_header['cols'],
                $pdf->current_table_header['widths']
            );
        }
    }

    foreach($data as $i=>$txt){
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($w[$i],5,utf8_decode($txt),1,'L');
        $pdf->SetXY($x+$w[$i],$y);
    }

    $pdf->Ln($maxH);
}

function ensure_space_for_graph($pdf, $height = 80){
    if ($pdf->GetY() + $height > 250){
        $pdf->AddPage();
    }
}

/* ======================================================
   4. PDF CLASS
====================================================== */

class PDF_MONTHLY extends FPDF {

    public $current_table_header = null;

    function getPageBreakTrigger() {
        return $this->PageBreakTrigger;
    }

    function Header(){}

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }

    function section_title($txt){
        $this->SetFont('Arial','B',13);
        $this->SetFillColor(200,220,255);
        $this->Cell(0,9,utf8_decode($txt),0,1,'L',true);
        $this->Ln(2);
    }

    function table_header($cols,$w){
        $this->SetFont('Arial','B',10);
        foreach($cols as $i=>$c){
            $this->Cell($w[$i],7,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
        $this->current_table_header=[ 'cols'=>$cols,'widths'=>$w ];
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',10);
        foreach($data as $i=>$txt){
            $this->Cell($w[$i],7,utf8_decode($txt),1,0,'C');
        }
        $this->Ln();
    }
}

/* ======================================================
   PORTADA
====================================================== */

$pdf = new PDF_MONTHLY();
$pdf->AddPage();

if (file_exists('../assets/img/Pueblo-Viejo.jpg')){
    $pdf->Image('../assets/img/Pueblo-Viejo.jpg', 12, 12, 55);
}

$pdf->SetY(48);
$pdf->SetFont('Arial','B',24);
$pdf->Cell(0,20,'MONTHLY LABORATORY REPORT',0,1,'C');

$pdf->SetFont('Arial','',16);
$pdf->Cell(0,10,date("F Y", strtotime($start_str)),0,1,'C');

$pdf->Ln(15);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Pueblo Viejo Mine  TSF Laboratory',0,1,'C');
$pdf->Cell(0,8,'Prepared by: '.utf8_decode($responsable),0,1,'C');

$pdf->Ln(12);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,8,'(Monthly report generated by CQA Laboratory)',0,1,'C');

$pdf->AddPage();

/* ======================================================
   1. EXECUTIVE SUMMARY
====================================================== */

$pdf->section_title("1. Executive Summary");

$total_tests = get_count("test_delivery","Register_Date",$start_str,$end_str);
$total_req   = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$total_ncr   = count(ncr_month($start_str,$end_str));
$top_client  = monthly_top_client($start_str,$end_str);

/* CALCULOS PARA TEXTO DINÁMICO */
$prep = get_count("test_preparation","Register_Date",$start_str,$end_str);
$real = get_count("test_realization","Register_Date",$start_str,$end_str);

$workflow_msg = "balanced";
if ($prep > $real * 1.3) $workflow_msg = "preparation-heavy";
if ($real > $prep * 1.3) $workflow_msg = "realization-intensive";

$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,6,utf8_decode("
During this month, laboratory operations remained {$workflow_msg}, showing consistent flow between sample registration, preparation and delivery.  
A total of {$total_req} samples were received and {$total_tests} tests were completed.  
The highest demand came from {$top_client['Client']} ({$top_client['total']} samples).  
A total of {$total_ncr} non-conformities were recorded and all were managed within the period.
"));
$pdf->Ln(6);

/* ======================================================
   2. MONTHLY KPIs
====================================================== */

$pdf->section_title("2. Monthly KPIs");

$pending = $total_req - $total_tests;

$pdf->table_header(["Metric","Value"],[100,40]);
$pdf->table_row(["Samples Registered",$total_req],[100,40]);
$pdf->table_row(["Tests Prepared",$prep],[100,40]);
$pdf->table_row(["Tests Performed",$real],[100,40]);
$pdf->table_row(["Tests Delivered",$total_tests],[100,40]);
$pdf->table_row(["Pending Tests",$pending],[100,40]);

$pdf->Ln(12);

/* ======================================================
   3. WORKLOAD & PERFORMANCE
====================================================== */

$pdf->section_title("3. Workload & Performance");

$registered = tests_registered_expanded($start_str,$end_str);
$delivered  = tests_delivered_expanded($start_str,$end_str);

$total_reg = array_sum($registered);
$total_del = array_sum($delivered);
$total_pen = $total_reg - $total_del;

$pdf->table_header(["Indicator","Value"],[100,40]);
$pdf->table_row(["Total Tests Registered",$total_reg],[100,40]);
$pdf->table_row(["Total Tests Delivered",$total_del],[100,40]);
$pdf->table_row(["Pending (Expanded)",$total_pen],[100,40]);

$pdf->Ln(14);
/* ======================================================
   4. TEST TYPE PERFORMANCE — COMPARACIÓN REG VS DELIV
====================================================== */

$pdf->section_title("4. Test Type Performance (Registered vs Delivered)");

ensure_space_for_graph($pdf, 120);

/* Unir tipos */
$allTests = array_unique(array_merge(array_keys($registered), array_keys($delivered)));
sort($allTests);

/* Determinar máximo */
$maxVal = 1;
foreach($allTests as $t){
    $maxVal = max($maxVal, $registered[$t] ?? 0, $delivered[$t] ?? 0);
}

/* Configuración del gráfico */
$chartX = 20;
$chartY = $pdf->GetY() + 10;
$chartW = 170;
$chartH = 70;

$barGroupW = 14;   // ancho total del grupo (reg + del)
$barW       = 6;   // ancho por barra
$space      = 10;  // espacio entre grupos

/* Ejes */
$pdf->Line($chartX, $chartY,     $chartX,           $chartY + $chartH);
$pdf->Line($chartX, $chartY+$chartH, $chartX+$chartW, $chartY+$chartH);

$x = $chartX + 8;

/* === DIBUJAR GRUPOS DE BARRAS === */
foreach($allTests as $t){

    $reg = $registered[$t] ?? 0;
    $del = $delivered[$t] ?? 0;

    /* --- REGISTERED (VERDE) --- */
    $hReg = ($reg / $maxVal) * ($chartH - 5);
    $yReg = $chartY + ($chartH - $hReg);

    $pdf->SetFillColor(60,160,60);
    $pdf->Rect($x, $yReg, $barW, $hReg, "F");

    /* --- DELIVERED (AZUL) --- */
    $hDel = ($del / $maxVal) * ($chartH - 5);
    $yDel = $chartY + ($chartH - $hDel);

    $pdf->SetFillColor(50,100,200);
    $pdf->Rect($x + $barW + 2, $yDel, $barW, $hDel, "F");

    /* Etiqueta */
    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($x - 3, $chartY + $chartH + 2);
    $pdf->MultiCell($barGroupW + 6, 3, utf8_decode($t), 0, 'C');

    $x += $barGroupW + $space;
}

$pdf->Ln($chartH + 20);

/* === TABLA === */
$pdf->table_header(
    ["Test Type","Registered","Delivered","Pending"],
    [70,30,30,30]
);

foreach($allTests as $t){
    $r = $registered[$t] ?? 0;
    $d = $delivered[$t] ?? 0;
    $p = $r - $d;
    $pdf->table_row([$t,$r,$d,$p],[70,30,30,30]);
}

$pdf->Ln(14);

/* ======================================================
   5. CLIENT SUMMARY
====================================================== */

$pdf->section_title("5. Monthly Summary by Client");

$clients = monthly_clients($start_str,$end_str);

$pdf->table_header(["Client","Requested","Delivered","Pending"], [70,30,30,30]);

foreach($clients as $c){
    $p = $c['requested'] - $c['delivered'];
    $pdf->table_row(
        [$c['Client'], $c['requested'], $c['delivered'], $p],
        [70,30,30,30]
    );
}

$pdf->Ln(14);
/* ======================================================
   6. PENDING TESTS (EXPANDED)
====================================================== */

$pdf->section_title("6. Pending Tests (Expanded)");

$pend = pending_tests_expanded($start_str,$end_str);

$pdf->table_header(
    ["Sample","Test","Client","Date"],
    [60,50,40,30]
);

foreach($pend as $p){
    $pdf->table_row([
        $p['Sample_ID']."-".$p['Sample_Number'],
        $p['Test_Type'],
        $p['Client'],
        date("d-M", strtotime($p['Registed_Date']))
    ], [60,50,40,30]);
}

$pdf->Ln(16);

/* ======================================================
   7. NCR / OBSERVACIONES
====================================================== */

$pdf->section_title("7. Non-Conformities & Observations");

$ncrs = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observation"], [50,140]);

foreach($ncrs as $n){
    table_row_multiline($pdf,[
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ], [50,140]);
}

$pdf->Ln(16);

/* ======================================================
   8. RECOMMENDATIONS OF THE MONTH
====================================================== */

$pdf->section_title("8. Recommendations of the Month");

$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,6,utf8_decode("
• Continue monitoring turnaround time and balance workloads across test categories.
• Improve documentation completeness at the moment of sample registration.
• Reinforce communication between preparation and realization teams for high–demand tests.
• Maintain NCR closure discipline and preventive actions to reduce recurrence.
• Evaluate opportunities for automating repetitive result–entry tasks.
"));

$pdf->Ln(16);

/* ======================================================
   9. RESPONSIBLE
====================================================== */

$pdf->section_title("9. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,8,"Report prepared by",1);
$pdf->Cell(130,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Monthly_Lab_Report_{$year}_{$month}.pdf");
?>
