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

$pdf->AddPage('P', array(270, 360));

$pdf->setSourceFile('sg-coarse.pdf'); 
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(44, 48);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(44, 53);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');


$pdf->SetXY(150, 41);
$pdf->Cell(21, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(150, 47);
$pdf->Cell(21, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(150, 53);
$pdf->Cell(21, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(230, 40);
$pdf->Cell(21, 6, $Search['Methods'], 0, 1, 'C');

$pdf->SetXY(48, 68);
$pdf->Cell(21, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(48, 74);
$pdf->Cell(21, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(48, 79);
$pdf->Cell(21, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(48, 85);
$pdf->Cell(21, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(150, 68);
$pdf->Cell(21, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(150, 74);
$pdf->Cell(21, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(150, 79);
$pdf->Cell(21, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(150, 85);
$pdf->Cell(21, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(230, 67);
$pdf->Cell(21, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(230, 74);
$pdf->Cell(21, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(230, 80);
$pdf->Cell(21, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(230, 87);
$pdf->Cell(21, 6, $Search['East'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(51, 120);
$pdf->Cell(34, 10, "", 0, 1, 'C');
$pdf->SetXY(51, 130);
$pdf->Cell(34, 10, $Search['Oven_Dry_1'], 0, 1, 'C');
$pdf->SetXY(51, 139);
$pdf->Cell(34, 10, $Search['Oven_Dry_2'], 0, 1, 'C');
$pdf->SetXY(51, 149);
$pdf->Cell(34, 10, $Search['Oven_Dry_3'], 0, 1, 'C');
$pdf->SetXY(51, 158);
$pdf->Cell(34, 10, $Search['Oven_Dry_4'], 0, 1, 'C');
$pdf->SetXY(51, 168);
$pdf->Cell(34, 10, $Search['Oven_Dry_5'], 0, 1, 'C');
$pdf->SetXY(51, 176);
$pdf->Cell(34, 10, $Search['Oven_Dry_6'], 0, 1, 'C');
$pdf->SetXY(51, 186);
$pdf->Cell(34, 10, $Search['Oven_Dry_7'], 0, 1, 'C');
$pdf->SetXY(51, 195);
$pdf->Cell(34, 10, $Search['Oven_Dry_8'], 0, 1, 'C');
$pdf->SetXY(51, 205);
$pdf->Cell(34, 10, $Search['Oven_Dry_9'], 0, 1, 'C');
$pdf->SetXY(51, 214);
$pdf->Cell(34, 10, $Search['Oven_Dry_10'], 0, 1, 'C');

$pdf->SetXY(110, 120);
$pdf->Cell(34, 10, "", 0, 1, 'C');
$pdf->SetXY(110, 130);
$pdf->Cell(34, 10, $Search['Surface_Dry_1'], 0, 1, 'C');
$pdf->SetXY(110, 139);
$pdf->Cell(34, 10, $Search['Surface_Dry_2'], 0, 1, 'C');
$pdf->SetXY(110, 149);
$pdf->Cell(34, 10, $Search['Surface_Dry_3'], 0, 1, 'C');
$pdf->SetXY(110, 158);
$pdf->Cell(34, 10, $Search['Surface_Dry_4'], 0, 1, 'C');
$pdf->SetXY(110, 168);
$pdf->Cell(34, 10, $Search['Surface_Dry_5'], 0, 1, 'C');
$pdf->SetXY(110, 176);
$pdf->Cell(34, 10, $Search['Surface_Dry_6'], 0, 1, 'C');
$pdf->SetXY(110, 186);
$pdf->Cell(34, 10, $Search['Surface_Dry_7'], 0, 1, 'C');
$pdf->SetXY(110, 195);
$pdf->Cell(34, 10, $Search['Surface_Dry_8'], 0, 1, 'C');
$pdf->SetXY(110, 205);
$pdf->Cell(34, 10, $Search['Surface_Dry_9'], 0, 1, 'C');
$pdf->SetXY(110, 214);
$pdf->Cell(34, 10, $Search['Surface_Dry_10'], 0, 1, 'C');

$pdf->SetXY(170, 120);
$pdf->Cell(34, 10, "", 0, 1, 'C');
$pdf->SetXY(170, 130);
$pdf->Cell(34, 10, $Search['Samp_Immers_1'], 0, 1, 'C');
$pdf->SetXY(170, 139);
$pdf->Cell(34, 10, $Search['Samp_Immers_2'], 0, 1, 'C');
$pdf->SetXY(170, 149);
$pdf->Cell(34, 10, $Search['Samp_Immers_3'], 0, 1, 'C');
$pdf->SetXY(170, 158);
$pdf->Cell(34, 10, $Search['Samp_Immers_4'], 0, 1, 'C');
$pdf->SetXY(170, 168);
$pdf->Cell(34, 10, $Search['Samp_Immers_5'], 0, 1, 'C');
$pdf->SetXY(170, 176);
$pdf->Cell(34, 10, $Search['Samp_Immers_6'], 0, 1, 'C');
$pdf->SetXY(170, 186);
$pdf->Cell(34, 10, $Search['Samp_Immers_7'], 0, 1, 'C');
$pdf->SetXY(170, 195);
$pdf->Cell(34, 10, $Search['Samp_Immers_8'], 0, 1, 'C');
$pdf->SetXY(170, 205);
$pdf->Cell(34, 10, $Search['Samp_Immers_9'], 0, 1, 'C');
$pdf->SetXY(170, 214);
$pdf->Cell(34, 10, $Search['Samp_Immers_10'], 0, 1, 'C');

// Results
$pdf->SetXY(85, 229);
$pdf->Cell(24, 8, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(85, 237);
$pdf->Cell(24, 8, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(85, 245);
$pdf->Cell(24, 8, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(85, 253);
$pdf->Cell(24, 8, $Search['Percent_Absortion'], 0, 1, 'C');


// Comements
$pdf->SetXY(16, 270);
$pdf->Cell(205, 33, $Search['Comments'], 0, 1, 'C');


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>