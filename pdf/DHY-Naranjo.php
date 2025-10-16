<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('double_hydrometer', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(500, 390));

$pdf->setSourceFile('template/PV-F-01742 Laboratory  Dispersive Characteristics of Clay Soil by Double Hydrometer_Rev5.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 9);

// Laboratory and sample information
$pdf->SetXY(64, 44);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(64, 51);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(64, 57);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(64, 73);
$pdf->Cell(30, 4, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(64, 79);
$pdf->Cell(30, 4, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(64, 83);
$pdf->Cell(30, 4, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(64, 88);
$pdf->Cell(30, 4, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(150, 44);
$pdf->Cell(30, 4, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(150, 51);
$pdf->Cell(30, 4, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(150, 57);
$pdf->Cell(30, 4, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(150, 73);
$pdf->Cell(30, 4, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(150, 79);
$pdf->Cell(30, 4, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(150, 83);
$pdf->Cell(30, 4, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(150, 88);
$pdf->Cell(30, 4, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(240, 44);
$pdf->Cell(30, 4, '-', 0, 1, 'C');
$pdf->SetXY(240, 51);
$pdf->Cell(30, 4, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(240, 57);
$pdf->Cell(30, 4, $Search['DispersionDevice'], 0, 1, 'C');
$pdf->SetXY(240, 73);
$pdf->Cell(30, 4, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(240, 79);
$pdf->Cell(30, 4, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(240, 83);
$pdf->Cell(30, 4, $Search['North'], 0, 1, 'C');
$pdf->SetXY(240, 88);
$pdf->Cell(30, 4, $Search['East'], 0, 1, 'C');

$pdf->SetXY(340, 44);
$pdf->Cell(30, 4, $Search['HydrometerType'], 0, 1, 'C');
$pdf->SetXY(340, 51);
$pdf->Cell(30, 4, $Search['MixingMethod'], 0, 1, 'C');
$pdf->SetXY(340, 57);
$pdf->Cell(30, 4, $Search['SpecificGravitywas'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 8);
// Moisture Content 50g
$pdf->SetXY(87, 104);
$pdf->Cell(21, 5, '1', 0, 1, 'C');
$pdf->SetXY(87, 109);
$pdf->Cell(21, 5, $Search['TareName50g'], 0, 1, 'C');
$pdf->SetXY(87, 114);
$pdf->Cell(21, 6, utf8_decode($Search['OvenTemp50g']), 0, 1, 'C');
$pdf->SetXY(87, 120);
$pdf->Cell(21, 7, $Search['TareWetSoil50g'], 0, 1, 'C');
$pdf->SetXY(87, 127);
$pdf->Cell(21, 5, $Search['TareDrySoil50g'], 0, 1, 'C');
$pdf->SetXY(87, 132);
$pdf->Cell(21, 5, $Search['WaterWw50g'], 0, 1, 'C');
$pdf->SetXY(87, 137);
$pdf->Cell(21, 5, $Search['TareMc50g'], 0, 1, 'C');
$pdf->SetXY(87, 142);
$pdf->Cell(21, 5, $Search['DrySoilWs50g'], 0, 1, 'C');
$pdf->SetXY(87, 148);
$pdf->Cell(21, 5, $Search['MC50g'], 0, 1, 'C');

// Moisture Content 25g
$pdf->SetXY(316, 104);
$pdf->Cell(22, 5, '1', 0, 1, 'C');
$pdf->SetXY(316, 109);
$pdf->Cell(22, 5, $Search['TareName'], 0, 1, 'C');
$pdf->SetXY(316, 114);
$pdf->Cell(22, 6, utf8_decode($Search['OvenTemp']), 0, 1, 'C');
$pdf->SetXY(316, 120);
$pdf->Cell(22, 7, $Search['TareWetSoil'], 0, 1, 'C');
$pdf->SetXY(316, 127);
$pdf->Cell(22, 5, $Search['TareDrySoil'], 0, 1, 'C');
$pdf->SetXY(316, 132);
$pdf->Cell(22, 5, $Search['WaterWw'], 0, 1, 'C');
$pdf->SetXY(316, 137);
$pdf->Cell(22, 5, $Search['TareMc'], 0, 1, 'C');
$pdf->SetXY(316, 142);
$pdf->Cell(22, 5, $Search['DrySoilWs'], 0, 1, 'C');
$pdf->SetXY(316, 148);
$pdf->Cell(22, 5, $Search['MC'], 0, 1, 'C');

// Hydrometer Corrected 25g & 50g
function printHydrometerValues($pdf, $Search, $fieldName, $yPosition)
{
    if (!isset($Search[$fieldName])) return;

    $valuesArray = explode(',', $Search[$fieldName]);
    foreach ($valuesArray as &$v) {
        if ($v === 'null' || trim($v) === '') $v = '';
    }

    $value1 = isset($valuesArray[0]) ? $valuesArray[0] : '';
    $value2 = isset($valuesArray[1]) ? $valuesArray[1] : '';

    $pdf->SetXY(218, $yPosition);
    $pdf->Cell(21, 5, $value1, 0, 1, 'C');

    $pdf->SetXY(195, $yPosition);
    $pdf->Cell(22, 5, $value2, 0, 1, 'C');
}
printHydrometerValues($pdf, $Search, 'AirDriedMassHydrometer', 104);
printHydrometerValues($pdf, $Search, 'DryMassHydrometer', 109);
printHydrometerValues($pdf, $Search, 'MassRetainedAfterHy', 114.4);
printHydrometerValues($pdf, $Search, 'DryMassHySpecimenPassing', 121);
printHydrometerValues($pdf, $Search, 'FineContentHySpecimen', 127);

// Hydrometer calibration 50g
$values = $Search['HyCalibrationTemp50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(23, 172);
$pdf->Cell(31, 4, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(23, 176);
$pdf->Cell(31, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(23, 181);
$pdf->Cell(31, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(23, 185);
$pdf->Cell(31, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(23, 189);
$pdf->Cell(31, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(23, 195);
$pdf->Cell(31, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(23, 201);
$pdf->Cell(31, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(23, 205.4);
$pdf->Cell(31, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(23, 210);
$pdf->Cell(31, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyCalibrationRead50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(54, 172);
$pdf->Cell(33, 4, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(54, 176);
$pdf->Cell(33, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(54, 181);
$pdf->Cell(33, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(54, 185);
$pdf->Cell(33, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(54, 189);
$pdf->Cell(33, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(54, 195);
$pdf->Cell(33, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(54, 201);
$pdf->Cell(33, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(54, 205.4);
$pdf->Cell(33, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(54, 210);
$pdf->Cell(33, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyMeasureTemp50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(87, 172);
$pdf->Cell(21, 4, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(87, 176);
$pdf->Cell(21, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(87, 181);
$pdf->Cell(21, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(87, 185);
$pdf->Cell(21, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(87, 189);
$pdf->Cell(21, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(87, 195);
$pdf->Cell(21, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(87, 201);
$pdf->Cell(21, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(87, 205.4);
$pdf->Cell(21, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(87, 210);
$pdf->Cell(21, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyMeasureFluid50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(108, 172);
$pdf->Cell(20, 4, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(108, 176);
$pdf->Cell(20, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(108, 181);
$pdf->Cell(20, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(108, 185);
$pdf->Cell(20, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(108, 189);
$pdf->Cell(20, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(108, 195);
$pdf->Cell(20, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(108, 201);
$pdf->Cell(20, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(108, 205.4);
$pdf->Cell(20, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(108, 210);
$pdf->Cell(20, 5, $valuesArray[8], 0, 1, 'C');

// Hydrometer calibration 25g
$values = $Search['HyCalibrationTemp'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(268, 172);
$pdf->Cell(25, 4, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(268, 176);
$pdf->Cell(25, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(268, 181);
$pdf->Cell(25, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(268, 185);
$pdf->Cell(25, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(268, 189);
$pdf->Cell(25, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(268, 195);
$pdf->Cell(25, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(268, 201);
$pdf->Cell(25, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(268, 205.4);
$pdf->Cell(25, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(268, 210);
$pdf->Cell(25, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyCalibrationRead'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(293, 172);
$pdf->Cell(22, 4, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(293, 176);
$pdf->Cell(22, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(293, 181);
$pdf->Cell(22, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(293, 185);
$pdf->Cell(22, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(293, 189);
$pdf->Cell(22, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(293, 195);
$pdf->Cell(22, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(293, 201);
$pdf->Cell(22, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(293, 205.4);
$pdf->Cell(22, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(293, 210);
$pdf->Cell(22, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyMeasureTemp'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(316, 172);
$pdf->Cell(22, 4, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(316, 176);
$pdf->Cell(22, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(316, 181);
$pdf->Cell(22, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(316, 185);
$pdf->Cell(22, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(316, 189);
$pdf->Cell(22, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(316, 195);
$pdf->Cell(22, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(316, 201);
$pdf->Cell(22, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(316, 205.4);
$pdf->Cell(22, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(316, 210);
$pdf->Cell(22, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyMeasureFluid'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(338, 172);
$pdf->Cell(18, 4, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(338, 176);
$pdf->Cell(18, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(338, 181);
$pdf->Cell(18, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(338, 185);
$pdf->Cell(18, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(338, 189);
$pdf->Cell(18, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(338, 195);
$pdf->Cell(18, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(338, 201);
$pdf->Cell(18, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(338, 205.4);
$pdf->Cell(18, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(338, 210);
$pdf->Cell(18, 5, $valuesArray[8], 0, 1, 'C');

// Atterberg Limits & Specific Gravity Results
$pdf->SetXY(195, 153);
$pdf->Cell(22, 4, $Search['LiquidLimit'], 0, 1, 'C');
$pdf->SetXY(195, 158);
$pdf->Cell(22, 4, $Search['PlasticityIndex'], 0, 1, 'C');
$pdf->SetXY(195, 167);
$pdf->Cell(22, 5, $Search['SG_Result'], 0, 1, 'C');

// Hydrometer Analysis
$pdf->SetXY(217, 181);
$pdf->Cell(22, 4, $Search['DispersionAgent'], 0, 1, 'C');
$pdf->SetXY(217, 185);
$pdf->Cell(22, 4, $Search['AmountUsed'], 0, 1, 'C');
$pdf->SetXY(217, 189.4);
$pdf->Cell(22, 4, $Search['Temperatureoftest'], 0, 1, 'C');
$pdf->SetXY(217, 196);
$pdf->Cell(22, 4, $Search['Viscosityofwater'], 0, 1, 'C');
$pdf->SetXY(217, 202);
$pdf->Cell(22, 4, $Search['MassdensityofwaterCalibrated'], 0, 1, 'C');
$pdf->SetXY(217, 206.4);
$pdf->Cell(22, 4, $Search['Acceleration'], 0, 1, 'C');
$pdf->SetXY(217, 211);
$pdf->Cell(22, 4, $Search['Volumeofsuspension'], 0, 1, 'C');
$pdf->SetXY(217, 215);
$pdf->Cell(22, 4, $Search['MeniscusCorrection'], 0, 1, 'C');

// Hydrometer table 25g
$pdf->SetXY(24, 244);
$pdf->Cell(30, 6, '1', 0, 1, 'R');
$pdf->SetXY(24, 250);
$pdf->Cell(30, 6, '2', 0, 1, 'R');
$pdf->SetXY(24, 257);
$pdf->Cell(30, 6, '3', 0, 1, 'R');
$pdf->SetXY(24, 263);
$pdf->Cell(30, 6, '4', 0, 1, 'R');
$pdf->SetXY(24, 269);
$pdf->Cell(30, 6, '5', 0, 1, 'R');
$pdf->SetXY(24, 276);
$pdf->Cell(30, 6, '6', 0, 1, 'R');
$pdf->SetXY(24, 282);
$pdf->Cell(30, 6, '7', 0, 1, 'R');
$pdf->SetXY(24, 288);
$pdf->Cell(30, 6, '8', 0, 1, 'R');
$pdf->SetXY(24, 295);
$pdf->Cell(30, 6, '9', 0, 1, 'R');

$values = $Search['Date'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(54, 244);
$pdf->Cell(32.4, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(54, 250);
$pdf->Cell(32.4, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(54, 257);
$pdf->Cell(32.4, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(54, 263);
$pdf->Cell(32.4, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(54, 269);
$pdf->Cell(32.4, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(54, 276);
$pdf->Cell(32.4, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(54, 282);
$pdf->Cell(32.4, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(54, 288);
$pdf->Cell(32.4, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(54, 295);
$pdf->Cell(32.4, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['Hour'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(87, 244);
$pdf->Cell(21, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(87, 250);
$pdf->Cell(21, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(87, 257);
$pdf->Cell(21, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(87, 263);
$pdf->Cell(21, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(87, 269);
$pdf->Cell(21, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(87, 276);
$pdf->Cell(21, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(87, 282);
$pdf->Cell(21, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(87, 288);
$pdf->Cell(21, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(87, 295);
$pdf->Cell(21, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['ReadingTimeT'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(108, 244);
$pdf->Cell(21, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(108, 250);
$pdf->Cell(21, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(108, 257);
$pdf->Cell(21, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(108, 263);
$pdf->Cell(21, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(108, 269);
$pdf->Cell(21, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(108, 276);
$pdf->Cell(21, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(108, 282);
$pdf->Cell(21, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(108, 288);
$pdf->Cell(21, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(108, 295);
$pdf->Cell(21, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['Temp'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(129, 244);
$pdf->Cell(26, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(129, 250);
$pdf->Cell(26, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(129, 257);
$pdf->Cell(26, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(129, 263);
$pdf->Cell(26, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(129, 269);
$pdf->Cell(26, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(129, 276);
$pdf->Cell(26, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(129, 282);
$pdf->Cell(26, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(129, 288);
$pdf->Cell(26, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(129, 295);
$pdf->Cell(26, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyReading'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(155, 244);
$pdf->Cell(40, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(155, 250);
$pdf->Cell(40, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(155, 257);
$pdf->Cell(40, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(155, 263);
$pdf->Cell(40, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(155, 269);
$pdf->Cell(40, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(155, 276);
$pdf->Cell(40, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(155, 282);
$pdf->Cell(40, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(155, 288);
$pdf->Cell(40, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(155, 295);
$pdf->Cell(40, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['ABdependingHy'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(195, 244);
$pdf->Cell(22, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(195, 250);
$pdf->Cell(22, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(195, 257);
$pdf->Cell(22, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(195, 263);
$pdf->Cell(22, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(195, 269);
$pdf->Cell(22, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(195, 276);
$pdf->Cell(22, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(195, 282);
$pdf->Cell(22, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(195, 288);
$pdf->Cell(22, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(195, 295);
$pdf->Cell(22, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['OffsetReading'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(217, 244);
$pdf->Cell(22, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(217, 250);
$pdf->Cell(22, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(217, 257);
$pdf->Cell(22, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(217, 263);
$pdf->Cell(22, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(217, 269);
$pdf->Cell(22, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(217, 276);
$pdf->Cell(22, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(217, 282);
$pdf->Cell(22, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(217, 288);
$pdf->Cell(22, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(217, 295);
$pdf->Cell(22, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['MassPercentFiner'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(239, 244);
$pdf->Cell(29, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(239, 250);
$pdf->Cell(29, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(239, 257);
$pdf->Cell(29, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(239, 263);
$pdf->Cell(29, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(239, 269);
$pdf->Cell(29, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(239, 276);
$pdf->Cell(29, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(239, 282);
$pdf->Cell(29, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(239, 288);
$pdf->Cell(29, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(239, 295);
$pdf->Cell(29, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['EffectiveLength'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(268, 244);
$pdf->Cell(25, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(268, 250);
$pdf->Cell(25, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(268, 257);
$pdf->Cell(25, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(268, 263);
$pdf->Cell(25, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(268, 269);
$pdf->Cell(25, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(268, 276);
$pdf->Cell(25, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(268, 282);
$pdf->Cell(25, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(268, 288);
$pdf->Cell(25, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(268, 295);
$pdf->Cell(25, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['DMm'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(293, 244);
$pdf->Cell(22, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(293, 250);
$pdf->Cell(22, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(293, 257);
$pdf->Cell(22, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(293, 263);
$pdf->Cell(22, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(293, 269);
$pdf->Cell(22, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(293, 276);
$pdf->Cell(22, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(293, 282);
$pdf->Cell(22, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(293, 288);
$pdf->Cell(22, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(293, 295);
$pdf->Cell(22, 6, $valuesArray[8], 0, 1, 'C');

// Hydrometer table 50g
$pdf->SetXY(24, 331);
$pdf->Cell(30, 6, '1', 0, 1, 'R');
$pdf->SetXY(24, 338);
$pdf->Cell(30, 6, '2', 0, 1, 'R');
$pdf->SetXY(24, 344);
$pdf->Cell(30, 6, '3', 0, 1, 'R');
$pdf->SetXY(24, 351);
$pdf->Cell(30, 6, '4', 0, 1, 'R');
$pdf->SetXY(24, 358);
$pdf->Cell(30, 6, '5', 0, 1, 'R');
$pdf->SetXY(24, 365);
$pdf->Cell(30, 6, '6', 0, 1, 'R');
$pdf->SetXY(24, 372);
$pdf->Cell(30, 6, '7', 0, 1, 'R');
$pdf->SetXY(24, 378);
$pdf->Cell(30, 6, '8', 0, 1, 'R');
$pdf->SetXY(24, 385);
$pdf->Cell(30, 6, '9', 0, 1, 'R');

$values = $Search['Date50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(54, 331);
$pdf->Cell(32.4, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(54, 338);
$pdf->Cell(32.4, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(54, 344);
$pdf->Cell(32.4, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(54, 351);
$pdf->Cell(32.4, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(54, 358);
$pdf->Cell(32.4, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(54, 365);
$pdf->Cell(32.4, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(54, 372);
$pdf->Cell(32.4, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(54, 378);
$pdf->Cell(32.4, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(54, 385);
$pdf->Cell(32.4, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['Hour50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(87, 331);
$pdf->Cell(21, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(87, 338);
$pdf->Cell(21, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(87, 344);
$pdf->Cell(21, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(87, 351);
$pdf->Cell(21, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(87, 358);
$pdf->Cell(21, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(87, 365);
$pdf->Cell(21, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(87, 372);
$pdf->Cell(21, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(87, 378);
$pdf->Cell(21, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(87, 385);
$pdf->Cell(21, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['ReadingTimeT50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(108, 331);
$pdf->Cell(21, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(108, 338);
$pdf->Cell(21, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(108, 344);
$pdf->Cell(21, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(108, 351);
$pdf->Cell(21, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(108, 358);
$pdf->Cell(21, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(108, 365);
$pdf->Cell(21, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(108, 372);
$pdf->Cell(21, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(108, 378);
$pdf->Cell(21, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(108, 385);
$pdf->Cell(21, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['Temp50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(129, 331);
$pdf->Cell(26, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(129, 338);
$pdf->Cell(26, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(129, 344);
$pdf->Cell(26, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(129, 351);
$pdf->Cell(26, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(129, 358);
$pdf->Cell(26, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(129, 365);
$pdf->Cell(26, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(129, 372);
$pdf->Cell(26, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(129, 378);
$pdf->Cell(26, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(129, 385);
$pdf->Cell(26, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyReading50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(155, 331);
$pdf->Cell(40, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(155, 338);
$pdf->Cell(40, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(155, 344);
$pdf->Cell(40, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(155, 351);
$pdf->Cell(40, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(155, 358);
$pdf->Cell(40, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(155, 365);
$pdf->Cell(40, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(155, 372);
$pdf->Cell(40, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(155, 378);
$pdf->Cell(40, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(155, 385);
$pdf->Cell(40, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['ABdependingHy50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(195, 331);
$pdf->Cell(22, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(195, 338);
$pdf->Cell(22, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(195, 344);
$pdf->Cell(22, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(195, 351);
$pdf->Cell(22, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(195, 358);
$pdf->Cell(22, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(195, 365);
$pdf->Cell(22, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(195, 372);
$pdf->Cell(22, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(195, 378);
$pdf->Cell(22, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(195, 385);
$pdf->Cell(22, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['OffsetReading50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(217, 331);
$pdf->Cell(22, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(217, 338);
$pdf->Cell(22, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(217, 344);
$pdf->Cell(22, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(217, 351);
$pdf->Cell(22, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(217, 358);
$pdf->Cell(22, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(217, 365);
$pdf->Cell(22, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(217, 372);
$pdf->Cell(22, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(217, 378);
$pdf->Cell(22, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(217, 385);
$pdf->Cell(22, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['MassPercentFiner50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(239, 331);
$pdf->Cell(29, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(239, 338);
$pdf->Cell(29, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(239, 344);
$pdf->Cell(29, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(239, 351);
$pdf->Cell(29, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(239, 358);
$pdf->Cell(29, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(239, 365);
$pdf->Cell(29, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(239, 372);
$pdf->Cell(29, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(239, 378);
$pdf->Cell(29, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(239, 385);
$pdf->Cell(29, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['EffectiveLength50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(268, 331);
$pdf->Cell(25, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(268, 338);
$pdf->Cell(25, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(268, 344);
$pdf->Cell(25, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(268, 351);
$pdf->Cell(25, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(268, 358);
$pdf->Cell(25, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(268, 365);
$pdf->Cell(25, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(268, 372);
$pdf->Cell(25, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(268, 378);
$pdf->Cell(25, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(268, 385);
$pdf->Cell(25, 6, $valuesArray[8], 0, 1, 'C');

$values = $Search['DMm50g'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(293, 331);
$pdf->Cell(22, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(293, 338);
$pdf->Cell(22, 6, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(293, 344);
$pdf->Cell(22, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(293, 351);
$pdf->Cell(22, 6, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(293, 358);
$pdf->Cell(22, 6, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(293, 365);
$pdf->Cell(22, 6, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(293, 372);
$pdf->Cell(22, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(293, 378);
$pdf->Cell(22, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(293, 385);
$pdf->Cell(22, 6, $valuesArray[8], 0, 1, 'C');

// Classification & Percent Dispersion
$values = $Search['Classification'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(54, 404);
$pdf->Cell(54, 10, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(54, 414);
$pdf->Cell(54, 8, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(54, 422);
$pdf->Cell(54, 9, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(54, 431);
$pdf->Cell(54, 8, $valuesArray[3], 0, 1, 'C');

// Laboratory Comments
$pdf->SetXY(155, 410);
$pdf->MultiCell(220, 4, $Search['Comments'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'DHY' . '.pdf', 'I');
