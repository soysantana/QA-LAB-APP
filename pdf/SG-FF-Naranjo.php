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

$pdf->AddPage('L', array(290, 295));

$pdf->setSourceFile('template/PV-F-83833-Laboratory Specific Gravity and Absortion in fine Aggregates.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(60, 38);
$pdf->Cell(21, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(60, 45);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(60, 52);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(60, 69);
$pdf->Cell(21, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(60, 76);
$pdf->Cell(21, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(60, 83);
$pdf->Cell(21, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(60, 89);
$pdf->Cell(21, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(165, 38);
$pdf->Cell(21, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(165, 45);
$pdf->Cell(21, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(165, 52);
$pdf->Cell(21, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(165, 69);
$pdf->Cell(21, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(165, 76);
$pdf->Cell(21, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(165, 83);
$pdf->Cell(21, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(165, 89);
$pdf->Cell(21, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(240, 38);
$pdf->Cell(21, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(240, 69);
$pdf->Cell(21, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(240, 76);
$pdf->Cell(21, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(240, 83);
$pdf->Cell(21, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(240, 89);
$pdf->Cell(21, 6, $Search['East'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(94, 108);
$pdf->Cell(27, 6, utf8_decode($Search['Pycnometer_Number']), 0, 1, 'C');
$pdf->SetXY(94, 113);
$pdf->Cell(27, 7, $Search['Weight_Pycnometer'], 0, 1, 'C');
$pdf->SetXY(94, 119);
$pdf->Cell(27, 8, $Search['Weight_Dry_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(94, 128);
$pdf->Cell(27, 6, $Search['Weight_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(94, 135);
$pdf->Cell(27, 12, $Search['Weight_Saturated_Surface_Dry_Soil_Air'], 0, 1, 'C');
$pdf->SetXY(94, 147);
$pdf->Cell(27, 6, $Search['Temperature_Sample'], 0, 1, 'C');
$pdf->SetXY(94, 154);
$pdf->Cell(27, 12, $Search['Weight_Pycnometer_Soil_Water'], 0, 1, 'C');
$pdf->SetXY(94, 167);
$pdf->Cell(27, 11, $Search['Calibration_Weight_Pycnometer_Desired_Temperature'], 0, 1, 'C');

// Results
$pdf->SetXY(94, 190);
$pdf->Cell(27, 6, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(94, 197);
$pdf->Cell(27, 10, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(94, 207);
$pdf->Cell(27, 10, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(94, 218);
$pdf->Cell(27, 10, $Search['Percent_Absortion'], 0, 1, 'C');

// Commets and Observations
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(154, 115);
$pdf->MultiCell(90, 4, $Search['Comments'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'SG' . '-' . 'Fine Particles' . '.pdf', 'D');
