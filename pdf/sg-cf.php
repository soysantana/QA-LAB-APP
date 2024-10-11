<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('specific_gravity_coarse', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(300, 240));

$pdf->setSourceFile('PV-TSF-CQA_CF_Specific Gravity Rev 2.pdf'); 
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);
// Project Information
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY(40.5, 43);
$pdf->Cell(30, 3.5, 'Pueblo Viejo', 8, 1, 'L');
$pdf->SetXY(40.5, 47.5);
$pdf->Cell(30, 3.5, $Search['Project_Number'], 8, 1, 'L');
$pdf->SetXY(40.5, 52);
$pdf->Cell(30, 3.5, $Search['Client'], 8, 1, 'L');
// Laboratory Information
$pdf->SetXY(157, 43);
$pdf->Cell(22, 3.5, 'PVDJ Soil Lab', 0, 1, 'L');
$pdf->SetXY(157, 47.5);
$pdf->Cell(22, 3.5, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(157, 52);
$pdf->Cell(22, 3.5, $Search['Sample_Date'], 0, 1, 'L');
$pdf->SetXY(203, 43);
$pdf->Cell(22, 3.5, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(203, 47.5);
$pdf->Cell(22, 3.5, $Search['Registed_Date'], 0, 1, 'L');
$pdf->SetXY(203, 52);
$pdf->Cell(22, 3.5, $Search['Standard'], 0, 1, 'L');
// Sample Information
$pdf->SetXY(40.5, 65);
$pdf->Cell(30, 4, $Search['Structure'], 0, 1, 'L');
$pdf->SetXY(40.5, 69.5);
$pdf->Cell(30, 4, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(40.5, 74);
$pdf->Cell(30, 4, $Search['Source'], 0, 1, 'L');
$pdf->SetXY(92, 65);
$pdf->Cell(21, 4, $Search['Sample_Number'], 0, 1, 'L');
$pdf->SetXY(92, 69.5);
$pdf->Cell(21, 4, $Search['Depth_From'] . '-' . $Search['Depth_To'],  0, 1, 'L');
$pdf->SetXY(92, 74);
$pdf->Cell(21, 4, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(157, 65);
$pdf->Cell(21, 4, $Search['North'], 0, 1, 'L');
$pdf->SetXY(157, 69.5);
$pdf->Cell(21, 4, $Search['East'], 0, 1, 'L');
$pdf->SetXY(157, 74);
$pdf->Cell(21, 4, $Search['Elev'], 0, 1, 'L');
// Test Information
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(40.5, 100);
$pdf->Cell(30, 5, $Search['Oven_Dry_1'], 0, 1, 'C');
$pdf->SetXY(40.5, 104.5);
$pdf->Cell(30, 5, $Search['Oven_Dry_2'], 0, 1, 'C');
$pdf->SetXY(40.5, 109.2);
$pdf->Cell(30, 5, $Search['Oven_Dry_3'], 0, 1, 'C');
$pdf->SetXY(40.5, 113.5);
$pdf->Cell(30, 5, $Search['Oven_Dry_4'], 0, 1, 'C');
$pdf->SetXY(40.5, 118);
$pdf->Cell(30, 5, $Search['Oven_Dry_5'], 0, 1, 'C');
$pdf->SetXY(40.5, 122.5);
$pdf->Cell(30, 5, $Search['Oven_Dry_6'], 0, 1, 'C');
$pdf->SetXY(40.5, 127);
$pdf->Cell(30, 5, $Search['Oven_Dry_7'], 0, 1, 'C');
$pdf->SetXY(40.5, 131.5);
$pdf->Cell(30, 5, $Search['Oven_Dry_8'], 0, 1, 'C');
$pdf->SetXY(40.5, 136);
$pdf->Cell(30, 5, $Search['Oven_Dry_9'], 0, 1, 'C');
$pdf->SetXY(40.5, 140.1);
$pdf->Cell(30, 5, $Search['Oven_Dry_10'], 0, 1, 'C');

$pdf->SetXY(92, 100);
$pdf->Cell(21, 5, $Search['Surface_Dry_1'], 0, 1, 'C');
$pdf->SetXY(92, 104.5);
$pdf->Cell(21, 5, $Search['Surface_Dry_2'], 0, 1, 'C');
$pdf->SetXY(92, 109.2);
$pdf->Cell(21, 5, $Search['Surface_Dry_3'], 0, 1, 'C');
$pdf->SetXY(92, 113.5);
$pdf->Cell(21, 5, $Search['Surface_Dry_4'], 0, 1, 'C');
$pdf->SetXY(92, 118);
$pdf->Cell(21, 5, $Search['Surface_Dry_5'], 0, 1, 'C');
$pdf->SetXY(92, 122.5);
$pdf->Cell(21, 5, $Search['Surface_Dry_6'], 0, 1, 'C');
$pdf->SetXY(92, 127);
$pdf->Cell(21, 5, $Search['Surface_Dry_7'], 0, 1, 'C');
$pdf->SetXY(92, 131.5);
$pdf->Cell(21, 5, $Search['Surface_Dry_8'], 0, 1, 'C');
$pdf->SetXY(92, 136);
$pdf->Cell(21, 5, $Search['Surface_Dry_9'], 0, 1, 'C');
$pdf->SetXY(92, 140.1);
$pdf->Cell(21, 5, $Search['Surface_Dry_10'], 0, 1, 'C');

$pdf->SetXY(157, 100);
$pdf->Cell(22, 5, $Search['Samp_Immers_1'], 0, 1, 'C');
$pdf->SetXY(157, 104.5);
$pdf->Cell(22, 5, $Search['Samp_Immers_2'], 0, 1, 'C');
$pdf->SetXY(157, 109.2);
$pdf->Cell(22, 5, $Search['Samp_Immers_3'], 0, 1, 'C');
$pdf->SetXY(157, 113.5);
$pdf->Cell(22, 5, $Search['Samp_Immers_4'], 0, 1, 'C');
$pdf->SetXY(157, 118);
$pdf->Cell(22, 5, $Search['Samp_Immers_5'], 0, 1, 'C');
$pdf->SetXY(157, 122.5);
$pdf->Cell(22, 5, $Search['Samp_Immers_6'], 0, 1, 'C');
$pdf->SetXY(157, 127);
$pdf->Cell(22, 5, $Search['Samp_Immers_7'], 0, 1, 'C');
$pdf->SetXY(157, 131.5);
$pdf->Cell(22, 5, $Search['Samp_Immers_8'], 0, 1, 'C');
$pdf->SetXY(157, 136);
$pdf->Cell(22, 5, $Search['Samp_Immers_9'], 0, 1, 'C');
$pdf->SetXY(157, 140.1);
$pdf->Cell(22, 5, $Search['Samp_Immers_10'], 0, 1, 'C');

// Results
$pdf->SetXY(71, 149);
$pdf->Cell(20, 5, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(71, 153.4);
$pdf->Cell(20, 5, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(71, 158);
$pdf->Cell(20, 5, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(71, 162.4);
$pdf->Cell(20, 5, $Search['Percent_Absortion'], 0, 1, 'C');


// Comements
$pdf->SetXY(115, 195);
$pdf->MultiCell(114, 4, $Search['Comments'], 0, 'L');


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>