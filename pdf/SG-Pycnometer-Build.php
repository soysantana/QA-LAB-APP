<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('specific_gravity', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(350, 380));

$pdf->setSourceFile('template/PV-F-01724 Laboratory Specific Gravity of soil solids by Water Pycnometer_Rev 1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(70, 41);
$pdf->Cell(30, 5, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(70, 61);
$pdf->Cell(30, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(70, 69);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(70, 78);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(70, 96);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(70, 104);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(70, 112);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(70, 120);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(180, 41);
$pdf->Cell(30, 6, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(180, 61);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(180, 69);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(180, 78);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(180, 96);
$pdf->Cell(35, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(180, 104);
$pdf->Cell(35, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(180, 112);
$pdf->Cell(35, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(180, 120);
$pdf->Cell(35, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(270, 41);
$pdf->Cell(30, 6, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(270, 61);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(270, 69);
$pdf->Cell(30, 6, $Search['PMethods'], 0, 1, 'C');
$pdf->SetXY(270, 78);
$pdf->Cell(30, 6, $Search['SMethods'], 0, 1, 'C');
$pdf->SetXY(270, 96);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(270, 104);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(270, 112);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(270, 120);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);

// Testing Information
$pdf->SetXY(135, 140);
$pdf->Cell(36, 7, $Search['Pycnometer_Used'], 0, 1, 'C');
$pdf->SetXY(135, 148);
$pdf->Cell(36, 5, utf8_decode($Search['Pycnometer_Number']), 0, 1, 'C');
$pdf->SetXY(135, 156);
$pdf->Cell(36, 5, $Search['Test_Temperatur'], 0, 1, 'C');
$pdf->SetXY(135, 163);
$pdf->Cell(36, 5, $Search['Average_Calibrated_Mass_Dry_Pycnometer_Mp'], 0, 1, 'C');
$pdf->SetXY(135, 170);
$pdf->Cell(36, 6, $Search['Average_Calibrated_Volume_Pycnometer_Vp'], 0, 1, 'C');
$pdf->SetXY(135, 178);
$pdf->Cell(36, 5, $Search['Density_Water_Test_Temperature'], 0, 1, 'C');
$pdf->SetXY(135, 185);
$pdf->Cell(36, 10, $Search['Calibration_Weight_Pynometer_Temperature_Mpw'], 0, 1, 'C');
$pdf->SetXY(135, 200);
$pdf->Cell(36, 5, $Search['Weight__Dry_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(135, 208);
$pdf->Cell(36, 5, $Search['Weight_Dry_Soil_Ms'], 0, 1, 'C');
$pdf->SetXY(135, 215);
$pdf->Cell(36, 6, $Search['Weight_Pycnometer_Soil_Water_Mpws'], 0, 1, 'C');
$pdf->SetXY(135, 224);
$pdf->Cell(36, 5, $Search['Test_Temperatur_After'], 0, 1, 'C');
$pdf->SetXY(135, 233);
$pdf->Cell(36, 5, $Search['Density_Water_Test_Temperature_After'], 0, 1, 'C');
$pdf->SetXY(135, 240);
$pdf->Cell(36, 12, $Search['Calibration_Weight_Pynometer_Temp_After'], 0, 1, 'C');
$pdf->SetXY(135, 254);
$pdf->Cell(36, 6, $Search['Specific_Gravity_Soil_Solid_Test_Temp_Gt'], 0, 1, 'C');
$pdf->SetXY(135, 264);
$pdf->Cell(36, 5, $Search['Temperature_Coefficent_K'], 0, 1, 'C');
$pdf->SetXY(135, 273);
$pdf->Cell(36, 5, $Search['Specific_Gravity_Soil_Solid'], 0, 1, 'C');

// Test Results
$pdf->SetXY(254, 148);
$valor = $Search['Specific_Gravity_Soil_Solid'];
$texto =  ($valor >= 2.6) ? 'Passed' : 'Failed';
$pdf->Cell(18, 6, $texto, 0, 1, 'C');

// Comparison information
$pdf->SetXY(245, 188);
$pdf->Cell(36, 5, $Search['Specific_Gravity_Soil_Solid'], 0, 1, 'C');

// Comments and Observations
$pdf->SetXY(17, 293);
$pdf->MultiCell(154, 4.2, $Search['Comments'], 0, 'L');

$pdf->SetXY(172, 293);
$pdf->MultiCell(130, 4.2, $Search['FieldComment'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'SG' . '-' . $Search['Material_Type'] . '.pdf', 'I');
