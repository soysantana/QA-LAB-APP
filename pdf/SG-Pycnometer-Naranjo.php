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

$pdf->AddPage('L', array(305, 250));

$pdf->setSourceFile('template/PV-F-83462 Laboratory Specific Gravity of Soil Solids by Water Pycnometer_Rev5.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(51, 36);
$pdf->Cell(30, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(51, 41.5);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(51, 47);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(55, 62);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(55, 68);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(55, 74);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(55, 79);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(153, 35);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(153, 41);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(153, 47);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(153, 61);
$pdf->Cell(35, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(153, 68);
$pdf->Cell(35, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(153, 74);
$pdf->Cell(35, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(153, 80);
$pdf->Cell(35, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(232, 36);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(232, 62);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(232, 68);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(232, 74);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(232, 80);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// Testing Information
$pdf->SetXY(152, 95);
$pdf->Cell(43, 4, $Search['Pycnometer_Used'], 0, 1, 'C');
$pdf->SetXY(152, 100);
$pdf->Cell(43, 5, $Search['Pycnometer_Number'], 0, 1, 'C');
$pdf->SetXY(152, 105);
$pdf->Cell(43, 5, $Search['Test_Temperatur'], 0, 1, 'C');
$pdf->SetXY(152, 110);
$pdf->Cell(43, 5, $Search['Average_Calibrated_Mass_Dry_Pycnometer_Mp'], 0, 1, 'C');
$pdf->SetXY(152, 115);
$pdf->Cell(43, 6, $Search['Average_Calibrated_Volume_Pycnometer_Vp'], 0, 1, 'C');
$pdf->SetXY(152, 121);
$pdf->Cell(43, 5, $Search['Density_Water_Test_Temperature'], 0, 1, 'C');
$pdf->SetXY(152, 126);
$pdf->Cell(43, 10, $Search['Calibration_Weight_Pynometer_Temperature_Mpw'], 0, 1, 'C');
$pdf->SetXY(152, 136);
$pdf->Cell(43, 5, $Search['Weight__Dry_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(152, 141);
$pdf->Cell(43, 5, $Search['Weight_Dry_Soil_Ms'], 0, 1, 'C');
$pdf->SetXY(152, 146);
$pdf->Cell(43, 6, $Search['Weight_Pycnometer_Soil_Water_Mpws'], 0, 1, 'C');
$pdf->SetXY(152, 152);
$pdf->Cell(43, 5, $Search['Test_Temperatur_After'], 0, 1, 'C');
$pdf->SetXY(152, 157);
$pdf->Cell(43, 5, $Search['Density_Water_Test_Temperature_After'], 0, 1, 'C');
$pdf->SetXY(152, 162);
$pdf->Cell(43, 12, $Search['Calibration_Weight_Pynometer_Temp_After'], 0, 1, 'C');
$pdf->SetXY(152, 174);
$pdf->Cell(43, 6, $Search['Specific_Gravity_Soil_Solid_Test_Temp_Gt'], 0, 1, 'C');
$pdf->SetXY(152, 180);
$pdf->Cell(43, 5, $Search['Temperature_Coefficent_K'], 0, 1, 'C');
$pdf->SetXY(152, 185);
$pdf->Cell(43, 5, $Search['Specific_Gravity_Soil_Solid'], 0, 1, 'C');

// Comments and Observations
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(205, 95);
$pdf->MultiCell(78, 4.2, $Search['Comments'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
