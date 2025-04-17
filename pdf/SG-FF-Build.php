<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('specific_gravity_fine', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(335, 360));

$pdf->setSourceFile('template/PV-F-01744_Density Relative Specific Gravity and Absorption of Fine Agregate_Rev 2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 11);

// Information for the test
$pdf->SetXY(59, 44);
$pdf->Cell(23, 5, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(59, 66);
$pdf->Cell(21, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(59, 74);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(59, 82);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(59, 104);
$pdf->Cell(21, 5, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(59, 112);
$pdf->Cell(21, 5, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(59, 121);
$pdf->Cell(21, 3.5, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(59, 128);
$pdf->Cell(21, 3.5, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(146, 44);
$pdf->Cell(23, 5, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(146, 66);
$pdf->Cell(21, 5, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(146, 74);
$pdf->Cell(21, 5, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(146, 82);
$pdf->Cell(21, 5, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(146, 104);
$pdf->Cell(21, 5, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(146, 112);
$pdf->Cell(21, 5, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(146, 121);
$pdf->Cell(21, 5, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(146, 128);
$pdf->Cell(21, 3.5, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(230, 44);
$pdf->Cell(23, 5, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(230, 66);
$pdf->Cell(21, 5, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(230, 104);
$pdf->Cell(21, 5, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(230, 112);
$pdf->Cell(21, 5, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(230, 121);
$pdf->Cell(21, 5, $Search['North'], 0, 1, 'C');
$pdf->SetXY(230, 128);
$pdf->Cell(21, 5, $Search['East'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(103, 144);
$pdf->Cell(39, 11, $Search['Pycnometer_Number'], 0, 1, 'C');
$pdf->SetXY(103, 155);
$pdf->Cell(39, 9, $Search['Weight_Pycnometer'], 0, 1, 'C');
$pdf->SetXY(103, 164);
$pdf->Cell(39, 9, $Search['Weight_Dry_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(103, 173);
$pdf->Cell(39, 10, $Search['Weight_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(248, 144);
$pdf->Cell(43, 11, $Search['Weight_Saturated_Surface_Dry_Soil_Air'], 0, 1, 'C');
$pdf->SetXY(248, 155);
$pdf->Cell(43, 9, $Search['Temperature_Sample'], 0, 1, 'C');
$pdf->SetXY(248, 164);
$pdf->Cell(43, 9, $Search['Weight_Pycnometer_Soil_Water'], 0, 1, 'C');
$pdf->SetXY(248, 173);
$pdf->Cell(43, 10, $Search['Calibration_Weight_Pycnometer_Desired_Temperature'], 0, 1, 'C');

// Results
$pdf->SetXY(103, 200);
$pdf->Cell(39, 8, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(103, 208);
$pdf->Cell(39, 8, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(103, 217);
$pdf->Cell(39, 8, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(103, 225);
$pdf->Cell(39, 8, $Search['Percent_Absortion'], 0, 1, 'C');

// Comparison Information
$pdf->SetXY(218, 200);
$pdf->Cell(30, 8, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(218, 208);
$pdf->Cell(30, 8, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(218, 217);
$pdf->Cell(30, 8, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(218, 225);
$pdf->Cell(30, 8, $Search['Percent_Absortion'], 0, 1, 'C');

// Test Results
$pdf->SetXY(164, 238);
$valor = $Search['Specific_Gravity_OD'];
$texto =  ($valor >= 2.5) ? 'Passed' : 'Failed';
$pdf->Cell(53, 8, $texto, 0, 1, 'C');

//Comments
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(27, 260);
$pdf->MultiCell(114, 4, $Search['Comments'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
