<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('specific_gravity_fine', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('L', array(285, 280));

$pdf->setSourceFile('sg-fine.pdf'); 
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(51, 43);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(51, 49);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(140, 36);
$pdf->Cell(21, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(140, 42);
$pdf->Cell(21, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(140, 47);
$pdf->Cell(21, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(230, 36);
$pdf->Cell(21, 6, $Search['Methods'], 0, 1, 'C');

$pdf->SetXY(55, 67);
$pdf->Cell(21, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(55, 74);
$pdf->Cell(21, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(55, 80);
$pdf->Cell(21, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(55, 87);
$pdf->Cell(21, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(143, 67);
$pdf->Cell(21, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(143, 74);
$pdf->Cell(21, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(143, 80);
$pdf->Cell(21, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(143, 87);
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
$pdf->SetXY(94, 102);
$pdf->Cell(35, 6, $Search['Pycnometer_Number'], 0, 1, 'C');
$pdf->SetXY(94, 107);
$pdf->Cell(35, 7, $Search['Weight_Pycnometer'], 0, 1, 'C');
$pdf->SetXY(94, 113);
$pdf->Cell(35, 8, $Search['Weight_Dry_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(94, 121);
$pdf->Cell(35, 6, $Search['Weight_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(94, 127);
$pdf->Cell(35, 12, $Search['Weight_Saturated_Surface_Dry_Soil_Air'], 0, 1, 'C');
$pdf->SetXY(94, 139);
$pdf->Cell(35, 6, $Search['Temperature_Sample'], 0, 1, 'C');
$pdf->SetXY(94, 144);
$pdf->Cell(35, 12, $Search['Weight_Pycnometer_Soil_Water'], 0, 1, 'C');
$pdf->SetXY(94, 157);
$pdf->Cell(35, 11, $Search['Calibration_Weight_Pycnometer_Desired_Temperature'], 0, 1, 'C');

// RESULTS
$pdf->SetXY(94, 179);
$pdf->Cell(35, 6, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(94, 185);
$pdf->Cell(35, 10, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(94, 195);
$pdf->Cell(35, 10, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(94, 205);
$pdf->Cell(35, 10, $Search['Percent_Absortion'], 0, 1, 'C');

//Comments
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(155, 103);
$pdf->Cell(90, 80, $Search['Comments'], 0, 1, 'C');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>