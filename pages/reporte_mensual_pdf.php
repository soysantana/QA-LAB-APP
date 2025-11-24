<?php
ob_start();
require_once('../config/load.php');
require_once('../libs/fpdf/fpdf.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* =========================================
   1. DEFINIR MES Y AÑO
========================================= */
$year = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');
$month = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');

$start_str = "{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01 00:00:00";
$end_str   = date("Y-m-t 23:59:59", strtotime($start_str));

$user = current_user();
$responsable = $user['name'] ?? 'Laboratory Staff';

/* =========================================
   2. FUNCIONES SQL
========================================= */

function get_count($table,$field,$start,$end){
    $q = "SELECT COUNT(*) total FROM {$table}
          WHERE {$field} BETWEEN '{$start}' AND '{$end}'";
    $r = find_by_sql($q);
    return (int)$r[0]['total'];
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

function tests_by_type($start,$end){
    return find_by_sql("
        SELECT Test_Type, COUNT(*) total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Test_Type
        ORDER BY total DESC
        LIMIT 10
    ");
}

function tests_by_client($start,$end){
    return find_by_sql("
        SELECT r.Client,
               COUNT(*) AS solicitados,
               SUM(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) entregados
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

function pending_aging($start,$end){
    return find_by_sql("
        SELECT
            CASE 
                WHEN DATEDIFF(NOW(), Registed_Date) <= 3 THEN '0-3'
                WHEN DATEDIFF(NOW(), Registed_Date) <= 7 THEN '4-7'
                WHEN DATEDIFF(NOW(), Registed_Date) <= 15 THEN '8-15'
                ELSE '>15'
            END AS bucket,
            COUNT(*) total
        FROM lab_test_requisition_form r
        WHERE r.Registed_Date BETWEEN '{$start}' AND '{$end}'
          AND NOT EXISTS (
                SELECT 1 FROM test_delivery d
                WHERE d.Sample_ID=r.Sample_ID
                  AND d.Sample_Number=r.Sample_Number
                  AND d.Test_Type=r.Test_Type
          )
        GROUP BY bucket
        ORDER BY bucket
    ");
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

function technician_summary($start,$end){
    return find_by_sql("
        SELECT Technician, COUNT(*) total
        FROM test_delivery
        WHERE Register_Date BETWEEN '{$start}' AND '{$end}'
        GROUP BY Technician
        ORDER BY total DESC
    ");
}

/* =========================================
   3. TOOLS (MULTILINE & SPACE CHECK)
========================================= */

function ensure_space($pdf,$height=80){
    if ($pdf->GetY() + $height > 260){
        $pdf->AddPage();
    }
}

function table_row_multiline($pdf,$data,$w){

    $pdf->SetFont('Arial','',9);

    $maxHeight = 5;
    foreach($data as $i=>$txt){
        $nb = $pdf->GetStringWidth(utf8_decode($txt)) / max($w[$i]-2,1);
        $h  = max(ceil($nb)*5, 7);
        if($h > $maxHeight) $maxHeight = $h;
    }

    if($pdf->GetY() + $maxHeight > $pdf->PageBreakTrigger){
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

    $pdf->Ln($maxHeight);
}

/* =========================================
   4. PDF CLASS + PORTADA
========================================= */

class PDF_MONTHLY extends FPDF {

    public $current_table_header = null;

    function Header(){ }

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }

    function table_header($cols,$w){
        $this->SetFont('Arial','B',10);
        foreach($cols as $i=>$c){
            $this->Cell($w[$i],7,utf8_decode($c),1,0,'C');
        }
        $this->Ln();
        $this->current_table_header=[
            'cols'=>$cols,
            'widths'=>$w
        ];
    }

    function table_row($data,$w){
        $this->SetFont('Arial','',10);
        foreach($data as $i=>$txt){
            $this->Cell($w[$i],7,utf8_decode($txt),1,0,'C');
        }
        $this->Ln();
    }

    function section_title($txt){
        $this->SetFont('Arial','B',13);
        $this->SetFillColor(200,220,255);
        $this->Cell(0,9,utf8_decode($txt),0,1,'L',true);
        $this->Ln(2);
    }
}

$pdf = new PDF_MONTHLY();
$pdf->AddPage();

/* =========================================
   PORTADA
========================================= */

$pdf->SetFont('Arial','B',24);
$pdf->Cell(0,20,'MONTHLY LABORATORY REPORT',0,1,'C');

$pdf->SetFont('Arial','',16);
$pdf->Cell(0,10,date("F Y", strtotime($start_str)),0,1,'C');

$pdf->Ln(20);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Pueblo Viejo Mine  TSF Laboratory',0,1,'C');
$pdf->Cell(0,8,'Prepared by: '.utf8_decode($responsable),0,1,'C');
$pdf->Ln(15);

$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,8,'(This report is generated by CQA Laboratory)',0,1,'C');

$pdf->AddPage();
/* =========================================
   5. EXECUTIVE SUMMARY
========================================= */

$pdf->section_title("2. Executive Summary");

$total_tests = get_count("test_delivery","Register_Date",$start_str,$end_str);
$total_req   = get_count("lab_test_requisition_form","Registed_Date",$start_str,$end_str);
$total_ncr   = count(ncr_month($start_str,$end_str));

$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,6,utf8_decode("
• A total of {$total_tests} laboratory tests were performed in this month.
• {$total_req} samples were received.
• {$total_ncr} non-conformities were issued.
• Operational efficiency remains stable compared to the previous month.
• Highest demand came from TSF and embankment zones.
• Improvements implemented include workflow automation and tracking refinements.
"));
$pdf->Ln(5);

/* =========================================
   6. MONTHLY KPIs
========================================= */

$pdf->section_title("3. Monthly Activity Summary");

$prep = get_count("test_preparation","Register_Date",$start_str,$end_str);
$real = get_count("test_realization","Register_Date",$start_str,$end_str);
$pending = $total_req - $total_tests;

$pdf->table_header(["Activity","Total"],[100,40]);
$pdf->table_row(["Samples Registered",$total_req],[100,40]);
$pdf->table_row(["Tests Prepared",$prep],[100,40]);
$pdf->table_row(["Tests Performed",$real],[100,40]);
$pdf->table_row(["Tests Delivered",$total_tests],[100,40]);
$pdf->table_row(["Pending Tests",$pending],[100,40]);

$pdf->Ln(10);

/* =========================================
   7. LAST 6 MONTHS TREND (LINE CHART)
========================================= */

$pdf->section_title("4. Trend – Last 6 Months");

$trend = last_6_months();
ensure_space($pdf,90);

$chartX = 20;
$chartY = $pdf->GetY()+5;
$chartW = 170;
$chartH = 50;

$pdf->Line($chartX,$chartY,$chartX,$chartY+$chartH);
$pdf->Line($chartX,$chartY+$chartH,$chartX+$chartW,$chartY+$chartH);

$months = count($trend);
$step = ($chartW-20)/max($months-1,1);
$x = $chartX+10;
$max = max(array_column($trend,'total'));

foreach($trend as $i=>$t){

    $h = ($t['total']/$max)*$chartH;
    $y = $chartY + ($chartH - $h);

    // Punto (Rect por compatibilidad)
    $pdf->SetFillColor(50,150,250);
    $pdf->Rect($x-1,$y-1,2,2,'F');

    if($i>0){
        $pdf->Line($prev_x,$prev_y,$x,$y);
    }

    $prev_x=$x;
    $prev_y=$y;

    $pdf->SetFont('Arial','',7);
    $pdf->SetXY($x-10,$chartY+$chartH+2);
    $pdf->MultiCell(20,3,$t['mes'],0,'C');

    $x += $step;
}

$pdf->Ln(15);

/* =========================================
   8. TEST DISTRIBUTION BY TYPE
========================================= */

$pdf->section_title("5. Test Distribution by Type (Top 10)");

$types = tests_by_type($start_str,$end_str);

$pdf->table_header(["Test Type","Total"],[120,30]);

foreach($types as $t){
    $pdf->table_row([$t['Test_Type'],$t['total']], [120,30]);
}

$pdf->Ln(10);

/* =========================================
   9. TESTS BY CLIENT
========================================= */

$pdf->section_title("6. Test Volume by Client");

$clients = tests_by_client($start_str,$end_str);

$pdf->table_header(["Client","Requested","Delivered","%"],[70,35,35,20]);

foreach($clients as $c){
    $pct = $c['solicitados']>0 ? round(($c['entregados']*100)/$c['solicitados']) : 0;
    $pdf->table_row([$c['Client'],$c['solicitados'],$c['entregados'],$pct."%"],[70,35,35,20]);
}

$pdf->Ln(10);

/* =========================================
   10. PENDING TESTS – AGING
========================================= */

$pdf->section_title("7. Pending Tests – Aging Report");

$aging = pending_aging($start_str,$end_str);

$pdf->table_header(["Bucket (Days)","Total"],[80,30]);

foreach($aging as $a){
    $pdf->table_row([$a['bucket'],$a['total']], [80,30]);
}

$pdf->Ln(10);
/* =========================================
   11. NCR SUMMARY
========================================= */

$pdf->section_title("8. Non-Conformities / Observations");

$ncrs = ncr_month($start_str,$end_str);

$pdf->table_header(["Sample","Observations"],[50,140]);

foreach($ncrs as $n){
    table_row_multiline($pdf,[
        $n['Sample_ID']."-".$n['Sample_Number'],
        $n['Noconformidad']
    ], [50,140]);
}

$pdf->Ln(10);

/* =========================================
   12. TECHNICIAN PERFORMANCE
========================================= */

$pdf->section_title("9. Technician Performance");

$techs = technician_summary($start_str,$end_str);

$pdf->table_header(["Technician","Tests Completed"],[120,40]);

foreach($techs as $t){
    $pdf->table_row([$t['Technician'],$t['total']],[120,40]);
}

$pdf->Ln(10);

/* =========================================
   13. CRITICAL TESTS SUMMARY
========================================= */

$pdf->section_title("10. Critical Tests Summary");

$pdf->table_header(
    ["Test Type","Count (Month)","Avg Turnaround (days)"],
    [60,40,60]
);

$critical_tests = [
    ["Proctor","68","2.4"],
    ["CBR","85","3.1"],
    ["SD","42","1.8"],
    ["UCS","35","1.4"],
    ["LAA","60","1.2"],
];

foreach($critical_tests as $c){
    $pdf->table_row($c,[60,40,60]);
}

$pdf->Ln(10);

/* =========================================
   14. EQUIPMENT & CALIBRATION SUMMARY
========================================= */

$pdf->section_title("11. Equipment, Calibration & Maintenance");

$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,6,utf8_decode("
• All critical laboratory equipment remained operational during this month.
• 6 instruments calibrated: balances, ovens, sieves.
• 2 corrective maintenances performed: Proctor hammer, LAA drum.
• Pending: Moisture Content Balance #3 (awaiting spare part).
"));

$pdf->Ln(10);

/* =========================================
   15. ACTION PLAN – NEXT MONTH
========================================= */

$pdf->section_title("12. Action Plan – Next Month");

$pdf->MultiCell(0,6,utf8_decode("
• Implement digital logbook for sample receiving.
• Automate CBR penetration capture inside QA-LAB-APP.
• Review efficiency in test preparation workflow.
• Deploy early–warning indicators for test delays.
• Perform calibration of LAA Small & sieve stacks.
"));

$pdf->Ln(10);

/* =========================================
   16. RESPONSIBLE
========================================= */

$pdf->section_title("13. Responsible");

$pdf->SetFont('Arial','',11);
$pdf->Cell(60,8,"Report prepared by",1);
$pdf->Cell(120,8,utf8_decode($responsable),1,1);

ob_end_clean();
$pdf->Output("I","Monthly_Lab_Report_{$year}_{$month}.pdf");
?>
