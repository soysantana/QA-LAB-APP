<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('specific_gravity_coarse', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(350, 270));

$pdf->setSourceFile('template/PV-F-01717 Laboratory Specific Gravity and Absortion in Coarse Aggregates_Rev 1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(48, 36);
$pdf->Cell(21, 5, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(48, 49);
$pdf->Cell(21, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(48, 54);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(48, 59);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(48, 72);
$pdf->Cell(21, 5, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(48, 77);
$pdf->Cell(21, 5, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(48, 83);
$pdf->Cell(21, 3.5, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(48, 88);
$pdf->Cell(21, 3.5, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(126, 36);
$pdf->Cell(21, 5, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(126, 49);
$pdf->Cell(21, 5, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(126, 54);
$pdf->Cell(21, 5, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(126, 59);
$pdf->Cell(21, 5, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(126, 72);
$pdf->Cell(21, 5, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(126, 77);
$pdf->Cell(21, 5, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(126, 83);
$pdf->Cell(21, 5, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(126, 88);
$pdf->Cell(21, 3.5, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(210, 36);
$pdf->Cell(21, 5, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(210, 49);
$pdf->Cell(21, 5, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(210, 54);
$pdf->Cell(21, 5, $Search['PMethods'], 0, 1, 'C');
$pdf->SetXY(210, 59);
$pdf->Cell(21, 5, $Search['SMethods'], 0, 1, 'C');
$pdf->SetXY(210, 72);
$pdf->Cell(21, 5, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(210, 77);
$pdf->Cell(21, 5, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(210, 83);
$pdf->Cell(21, 5, $Search['North'], 0, 1, 'C');
$pdf->SetXY(210, 88);
$pdf->Cell(21, 5, $Search['East'], 0, 1, 'C');

// Test Information
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(35, 123);
$pdf->Cell(28, 5, $Search['Oven_Dry_1'], 0, 1, 'C');
$pdf->SetXY(35, 129);
$pdf->Cell(28, 5, $Search['Oven_Dry_2'], 0, 1, 'C');
$pdf->SetXY(35, 135);
$pdf->Cell(28, 5, $Search['Oven_Dry_3'], 0, 1, 'C');
$pdf->SetXY(35, 141);
$pdf->Cell(28, 5, $Search['Oven_Dry_4'], 0, 1, 'C');
$pdf->SetXY(35, 146);
$pdf->Cell(28, 6, $Search['Oven_Dry_5'], 0, 1, 'C');
$pdf->SetXY(35, 152);
$pdf->Cell(28, 6, $Search['Oven_Dry_6'], 0, 1, 'C');
$pdf->SetXY(35, 159);
$pdf->Cell(28, 5, $Search['Oven_Dry_7'], 0, 1, 'C');
$pdf->SetXY(35, 164);
$pdf->Cell(28, 6, $Search['Oven_Dry_8'], 0, 1, 'C');
$pdf->SetXY(35, 170);
$pdf->Cell(28, 5, $Search['Oven_Dry_9'], 0, 1, 'C');
$pdf->SetXY(35, 182);
$pdf->Cell(28, 5, $Search['Oven_Dry_10'], 0, 1, 'C');

$pdf->SetXY(119, 123);
$pdf->Cell(27, 5, $Search['Surface_Dry_1'], 0, 1, 'C');
$pdf->SetXY(119, 129);
$pdf->Cell(27, 5, $Search['Surface_Dry_2'], 0, 1, 'C');
$pdf->SetXY(119, 135);
$pdf->Cell(27, 5, $Search['Surface_Dry_3'], 0, 1, 'C');
$pdf->SetXY(119, 141);
$pdf->Cell(27, 5, $Search['Surface_Dry_4'], 0, 1, 'C');
$pdf->SetXY(119, 146);
$pdf->Cell(27, 6, $Search['Surface_Dry_5'], 0, 1, 'C');
$pdf->SetXY(119, 152);
$pdf->Cell(27, 6, $Search['Surface_Dry_6'], 0, 1, 'C');
$pdf->SetXY(119, 159);
$pdf->Cell(27, 5, $Search['Surface_Dry_7'], 0, 1, 'C');
$pdf->SetXY(119, 164);
$pdf->Cell(27, 6, $Search['Surface_Dry_8'], 0, 1, 'C');
$pdf->SetXY(119, 170);
$pdf->Cell(27, 5, $Search['Surface_Dry_9'], 0, 1, 'C');
$pdf->SetXY(119, 182);
$pdf->Cell(27, 5, $Search['Surface_Dry_10'], 0, 1, 'C');

$pdf->SetXY(196, 123);
$pdf->Cell(22, 5, $Search['Samp_Immers_1'], 1, 1, 'C');
$pdf->SetXY(196, 129);
$pdf->Cell(22, 5, $Search['Samp_Immers_2'], 0, 1, 'C');
$pdf->SetXY(196, 135);
$pdf->Cell(22, 5, $Search['Samp_Immers_3'], 0, 1, 'C');
$pdf->SetXY(196, 141);
$pdf->Cell(22, 5, $Search['Samp_Immers_4'], 0, 1, 'C');
$pdf->SetXY(196, 146);
$pdf->Cell(22, 6, $Search['Samp_Immers_5'], 0, 1, 'C');
$pdf->SetXY(196, 152);
$pdf->Cell(22, 6, $Search['Samp_Immers_6'], 0, 1, 'C');
$pdf->SetXY(196, 159);
$pdf->Cell(22, 5, $Search['Samp_Immers_7'], 0, 1, 'C');
$pdf->SetXY(196, 164);
$pdf->Cell(22, 6, $Search['Samp_Immers_8'], 0, 1, 'C');
$pdf->SetXY(196, 170);
$pdf->Cell(22, 5, $Search['Samp_Immers_9'], 0, 1, 'C');
$pdf->SetXY(196, 182);
$pdf->Cell(22, 5, $Search['Samp_Immers_10'], 0, 1, 'C');

// Results
$pdf->SetXY(63, 198);
$pdf->Cell(19, 5, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(63, 204);
$pdf->Cell(19, 5, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(63, 210);
$pdf->Cell(19, 5, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(63, 216);
$pdf->Cell(19, 6, $Search['Percent_Absortion'], 0, 1, 'C');

// Comparison Information
$pdf->SetXY(169, 198);
$pdf->Cell(26, 5, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(169, 204);
$pdf->Cell(26, 5, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(169, 210);
$pdf->Cell(26, 5, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(169, 216);
$pdf->Cell(26, 6, $Search['Percent_Absortion'], 0, 1, 'C');

// Test Results
$pdf->SetXY(82, 235);
$valor = $Search['Specific_Gravity_OD'];
$texto =  ($valor >= 2.5) ? 'Passed' : 'Failed';
$pdf->Cell(37, 5, $texto, 0, 1, 'C');

// Comements
$pdf->SetXY(10, 256);
$pdf->MultiCell(135, 4, $Search['Comments'], 0, 'L');

$pdf->SetXY(148, 256);
$pdf->MultiCell(100, 4, $Search['FieldComment'], 0, 'L');


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'SG' . '-' . $Search['Material_Type'] . '-' . 'Coarse Particles' . '.pdf', 'D');
