<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

// Leer JSON recibido
$input = json_decode(file_get_contents('php://input'), true);
$Chart = $input['HydrometerGraph'] ?? null;

$Search = find_by_id('hydrometer', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(560, 470));

$pdf->setSourceFile('template/PV-F-83817_Laboratory Particle Size Distribution of fine soils using the Hydrometer Analysis_Rev5.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 9);

// Laboratory and sample information
$pdf->SetXY(60, 80);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(60, 87);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(60, 96);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(60, 116);
$pdf->Cell(30, 4, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(60, 121);
$pdf->Cell(30, 4, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(60, 126);
$pdf->Cell(30, 4, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(60, 132);
$pdf->Cell(30, 4, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(160, 80);
$pdf->Cell(30, 4, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(160, 87);
$pdf->Cell(30, 4, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(160, 96);
$pdf->Cell(30, 4, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(160, 116);
$pdf->Cell(30, 4, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(160, 121);
$pdf->Cell(30, 4, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(160, 126);
$pdf->Cell(30, 4, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(160, 132);
$pdf->Cell(30, 4, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(275, 80);
$pdf->Cell(30, 4, '-', 0, 1, 'C');
$pdf->SetXY(275, 87);
$pdf->Cell(30, 4, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(275, 96);
$pdf->Cell(30, 4, $Search['DispersionDevice'], 0, 1, 'C');
$pdf->SetXY(275, 116);
$pdf->Cell(30, 4, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(275, 121);
$pdf->Cell(30, 4, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(275, 126);
$pdf->Cell(30, 4, $Search['North'], 0, 1, 'C');
$pdf->SetXY(275, 132);
$pdf->Cell(30, 4, $Search['East'], 0, 1, 'C');

$pdf->SetXY(385, 80);
$pdf->Cell(30, 4, $Search['HydrometerType'], 0, 1, 'C');
$pdf->SetXY(385, 87);
$pdf->Cell(30, 4, $Search['MixingMethod'], 0, 1, 'C');
$pdf->SetXY(385, 96);
$pdf->Cell(30, 4, $Search['SpecificGravitywas'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
// Hydrometer Analysis
$pdf->SetXY(90, 158);
$pdf->Cell(21, 4, $Search['AmountUsed'], 0, 1, 'C');
$pdf->SetXY(90, 164);
$pdf->Cell(21, 4, $Search['Temperatureoftest'], 0, 1, 'C');
$pdf->SetXY(90, 169.4);
$pdf->Cell(21, 4, $Search['Viscosityofwater'], 0, 1, 'C');
$pdf->SetXY(90, 174.4);
$pdf->Cell(21, 4, $Search['MassdensityofwaterCalibrated'], 0, 1, 'C');
$pdf->SetXY(90, 179.4);
$pdf->Cell(21, 4, $Search['Acceleration'], 0, 1, 'C');
$pdf->SetXY(90, 184.4);
$pdf->Cell(21, 4, $Search['Volumeofsuspension'], 0, 1, 'C');
$pdf->SetXY(90, 190);
$pdf->Cell(21, 4, $Search['MeniscusCorrection'], 0, 1, 'C');
$pdf->SetXY(90, 195.4);
$pdf->Cell(21, 4, '', 0, 1, 'C');

// Atterberg Limits & Specific Gravity Results
$pdf->SetXY(60.5, 210);
$pdf->Cell(30, 5, $Search['LiquidLimit'], 0, 1, 'C');
$pdf->SetXY(60.5, 215.5);
$pdf->Cell(30, 5, $Search['PlasticityIndex'], 0, 1, 'C');
$pdf->SetXY(60.5, 231.4);
$pdf->Cell(30, 5, $Search['SG_Result'], 0, 1, 'C');

// Hydrometer calibration
$values = $Search['HyCalibrationTemp'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(23, 276.4);
$pdf->Cell(37, 9, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(23, 286);
$pdf->Cell(37, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(23, 291.4);
$pdf->Cell(37, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(23, 296);
$pdf->Cell(37, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(23, 301);
$pdf->Cell(37, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(23, 307);
$pdf->Cell(37, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(23, 313);
$pdf->Cell(37, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(23, 320);
$pdf->Cell(37, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(23, 328);
$pdf->Cell(37, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyCalibrationRead'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(61, 276.4);
$pdf->Cell(29, 9, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(61, 286);
$pdf->Cell(29, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(61, 291.4);
$pdf->Cell(29, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(61, 296);
$pdf->Cell(29, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(61, 301);
$pdf->Cell(29, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(61, 307);
$pdf->Cell(29, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(61, 313);
$pdf->Cell(29, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(61, 320);
$pdf->Cell(29, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(61, 328);
$pdf->Cell(29, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyMeasureTemp'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(90.4, 276.4);
$pdf->Cell(20, 9, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(90.4, 286);
$pdf->Cell(20, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(90.4, 291.4);
$pdf->Cell(20, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(90.4, 296);
$pdf->Cell(20, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(90.4, 301);
$pdf->Cell(20, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(90.4, 307);
$pdf->Cell(20, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(90.4, 313);
$pdf->Cell(20, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(90.4, 320);
$pdf->Cell(20, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(90.4, 328);
$pdf->Cell(20, 5, $valuesArray[8], 0, 1, 'C');

$values = $Search['HyMeasureFluid'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '0';
}
$pdf->SetXY(111, 276.4);
$pdf->Cell(37, 9, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(111, 286);
$pdf->Cell(37, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(111, 291.4);
$pdf->Cell(37, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(111, 296);
$pdf->Cell(37, 4, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(111, 301);
$pdf->Cell(37, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(111, 307);
$pdf->Cell(37, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(111, 313);
$pdf->Cell(37, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(111, 320);
$pdf->Cell(37, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(111, 328);
$pdf->Cell(37, 5, $valuesArray[8], 0, 1, 'C');

// Moisture Content
$pdf->SetXY(222, 184);
$pdf->Cell(26, 5, '1', 0, 1, 'C');
$pdf->SetXY(222, 189);
$pdf->Cell(26, 5, $Search['TareName'], 0, 1, 'C');
$pdf->SetXY(222, 195);
$pdf->Cell(26, 5, utf8_decode($Search['OvenTemp']), 0, 1, 'C');
$pdf->SetXY(222, 200);
$pdf->Cell(26, 4, $Search['TareWetSoil'], 0, 1, 'C');
$pdf->SetXY(222, 205);
$pdf->Cell(26, 5, $Search['TareDrySoil'], 0, 1, 'C');
$pdf->SetXY(222, 210);
$pdf->Cell(26, 5, $Search['WaterWw'], 0, 1, 'C');
$pdf->SetXY(222, 215.4);
$pdf->Cell(26, 5, $Search['TareMc'], 0, 1, 'C');
$pdf->SetXY(222, 221);
$pdf->Cell(26, 5, $Search['DrySoilWs'], 0, 1, 'C');
$pdf->SetXY(222, 226.4);
$pdf->Cell(26, 5, $Search['MC'], 0, 1, 'C');

// Corrections the hydrometer
$pdf->SetXY(248.4, 245);
$pdf->Cell(20, 12, $Search['AirDriedMassHydrometer'], 0, 1, 'C');
$pdf->SetXY(248.4, 257);
$pdf->Cell(20, 10, $Search['DryMassHydrometer'], 0, 1, 'C');
$pdf->SetXY(248.4, 267);
$pdf->Cell(20, 9, $Search['MassRetainedAfterHy'], 0, 1, 'C');
$pdf->SetXY(248.4, 276);
$pdf->Cell(20, 9.4, $Search['DryMassHySpecimenPassing'], 0, 1, 'C');
$pdf->SetXY(248.4, 286);
$pdf->Cell(20, 5, $Search['FineContentHySpecimen'], 0, 1, 'C');

// Grain Size Analysis
$pdf->SetXY(269, 141);
$pdf->Cell(42, 4, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(269, 145);
$pdf->Cell(42, 7, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(269, 152.4);
$pdf->Cell(42, 5, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(269, 157.4);
$pdf->Cell(42, 5, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(269, 163);
$pdf->Cell(42, 5, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(269, 168.4);
$pdf->Cell(42, 5, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(269, 174);
$pdf->Cell(42, 5, $Search['Wt_Wash_Pan'], 0, 1, 'C');
// Sieve Wt Ret
$pdf->SetXY(359, 145.4);
$pdf->Cell(21, 7, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(359, 152.4);
$pdf->Cell(21, 5, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(359, 157.4);
$pdf->Cell(21, 5, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(359, 163);
$pdf->Cell(21, 5, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(359, 168.4);
$pdf->Cell(21, 5, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(359, 174);
$pdf->Cell(21, 5, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(359, 179);
$pdf->Cell(21, 5, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(359, 184);
$pdf->Cell(21, 5, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(359, 189);
$pdf->Cell(21, 5, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(359, 195);
$pdf->Cell(21, 5, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(359, 200);
$pdf->Cell(21, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(359, 205);
$pdf->Cell(21, 5, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(359, 210);
$pdf->Cell(21, 5, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(359, 215.4);
$pdf->Cell(21, 5, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(359, 221);
$pdf->Cell(21, 5, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(359, 226.4);
$pdf->Cell(21, 5, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(359, 231.4);
$pdf->Cell(21, 5, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(359, 236.4);
$pdf->Cell(21, 4, $Search['TotalWtRet'], 0, 1, 'C');
// Sieve % Ret
$pdf->SetXY(380.4, 145.4);
$pdf->Cell(17, 7, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(380.4, 152.4);
$pdf->Cell(17, 5, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(380.4, 157.4);
$pdf->Cell(17, 5, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(380.4, 163);
$pdf->Cell(17, 5, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(380.4, 168.4);
$pdf->Cell(17, 5, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(380.4, 174);
$pdf->Cell(17, 5, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(380.4, 179);
$pdf->Cell(17, 5, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(380.4, 184);
$pdf->Cell(17, 5, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(380.4, 189);
$pdf->Cell(17, 5, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(380.4, 195);
$pdf->Cell(17, 5, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(380.4, 200);
$pdf->Cell(17, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(380.4, 205);
$pdf->Cell(17, 5, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(380.4, 210);
$pdf->Cell(17, 5, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(380.4, 215.4);
$pdf->Cell(17, 5, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(380.4, 221);
$pdf->Cell(17, 5, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(380.4, 226.4);
$pdf->Cell(17, 5, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(380.4, 231.4);
$pdf->Cell(17, 5, $Search['PanRet'], 0, 1, 'C');
$pdf->SetXY(380.4, 236.4);
$pdf->Cell(17, 4, $Search['TotalRet'], 0, 1, 'C');
// Sieve Cum % Ret
$pdf->SetXY(397, 145.4);
$pdf->Cell(18, 7, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(397, 152.4);
$pdf->Cell(18, 5, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(397, 157.4);
$pdf->Cell(18, 5, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(397, 163);
$pdf->Cell(18, 5, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(397, 168.4);
$pdf->Cell(18, 5, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(397, 174);
$pdf->Cell(18, 5, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(397, 179);
$pdf->Cell(18, 5, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(397, 184);
$pdf->Cell(18, 5, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(397, 189);
$pdf->Cell(18, 5, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(397, 195);
$pdf->Cell(18, 5, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(397, 200);
$pdf->Cell(18, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(397, 205);
$pdf->Cell(18, 5, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(397, 210);
$pdf->Cell(18, 5, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(397, 215.4);
$pdf->Cell(18, 5, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(397, 221);
$pdf->Cell(18, 5, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(397, 226.4);
$pdf->Cell(18, 5, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(397, 231.4);
$pdf->Cell(18, 5, '', 0, 1, 'C');
$pdf->SetXY(397, 236.4);
$pdf->Cell(18, 4, $Search['TotalCumRet'], 0, 1, 'C');
// Sieve $ Pass
$pdf->SetXY(416, 145.4);
$pdf->Cell(29, 7, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(416, 152.4);
$pdf->Cell(29, 5, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(416, 157.4);
$pdf->Cell(29, 5, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(416, 163);
$pdf->Cell(29, 5, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(416, 168.4);
$pdf->Cell(29, 5, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(416, 174);
$pdf->Cell(29, 5, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(416, 179);
$pdf->Cell(29, 5, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(416, 184);
$pdf->Cell(29, 5, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(416, 189);
$pdf->Cell(29, 5, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(416, 195);
$pdf->Cell(29, 5, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(416, 200);
$pdf->Cell(29, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(416, 205);
$pdf->Cell(29, 5, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(416, 210);
$pdf->Cell(29, 5, $Search['Pass14'], 0, 1, 'C');
$pdf->SetXY(416, 215.4);
$pdf->Cell(29, 5, $Search['Pass15'], 0, 1, 'C');
$pdf->SetXY(416, 221);
$pdf->Cell(29, 5, $Search['Pass16'], 0, 1, 'C');
$pdf->SetXY(416, 226.4);
$pdf->Cell(29, 5, $Search['Pass17'], 0, 1, 'C');
$pdf->SetXY(416, 231.4);
$pdf->Cell(29, 5, '', 0, 1, 'C');
$pdf->SetXY(416, 236.4);
$pdf->Cell(29, 4, $Search['TotalPass'], 0, 1, 'C');

// Summary GS Distribution Parameter
$pdf->SetXY(359, 257);
$pdf->Cell(21, 10, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(359, 267.4);
$pdf->Cell(21, 9, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(359, 276.4);
$pdf->Cell(21, 9, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(359, 286);
$pdf->Cell(21, 5, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(359, 291.4);
$pdf->Cell(21, 4, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(359, 296);
$pdf->Cell(21, 4, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(359, 300);
$pdf->Cell(21, 6, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(359, 307);
$pdf->Cell(21, 6, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(359, 313);
$pdf->Cell(21, 6, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(359, 320);
$pdf->Cell(21, 6, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(359, 326);
$pdf->Cell(21, 8, $Search['Cu'], 0, 1, 'C');

// Hydrometer Reading Data
$values = $Search['Date'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(61, 359);
$pdf->Cell(29, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(61, 366.4);
$pdf->Cell(29, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(61, 372.4);
$pdf->Cell(29, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(61, 377);
$pdf->Cell(29, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(61, 382);
$pdf->Cell(29, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(61, 392);
$pdf->Cell(29, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(61, 399);
$pdf->Cell(29, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(61, 406);
$pdf->Cell(29, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(61, 413);
$pdf->Cell(29, 7, $valuesArray[8], 0, 1, 'C');
// hour
$values = $Search['Hour'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(90.4, 359);
$pdf->Cell(20, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(90.4, 366.4);
$pdf->Cell(20, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(90.4, 372.4);
$pdf->Cell(20, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(90.4, 377);
$pdf->Cell(20, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(90.4, 382);
$pdf->Cell(20, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(90.4, 392);
$pdf->Cell(20, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(90.4, 399);
$pdf->Cell(20, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(90.4, 406);
$pdf->Cell(20, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(90.4, 413);
$pdf->Cell(20, 7, $valuesArray[8], 0, 1, 'C');
// Readind Time Min
$values = $Search['ReadingTimeT'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(111, 359);
$pdf->Cell(37, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(111, 366.4);
$pdf->Cell(37, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(111, 372.4);
$pdf->Cell(37, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(111, 377);
$pdf->Cell(37, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(111, 382);
$pdf->Cell(37, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(111, 392);
$pdf->Cell(37, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(111, 399);
$pdf->Cell(37, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(111, 406);
$pdf->Cell(37, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(111, 413);
$pdf->Cell(37, 7, $valuesArray[8], 0, 1, 'C');
// Temp Celcius
$values = $Search['Temp'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(148.4, 359);
$pdf->Cell(30, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(148.4, 366.4);
$pdf->Cell(30, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(148.4, 372.4);
$pdf->Cell(30, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(148.4, 377);
$pdf->Cell(30, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(148.4, 382);
$pdf->Cell(30, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(148.4, 392);
$pdf->Cell(30, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(148.4, 399);
$pdf->Cell(30, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(148.4, 406);
$pdf->Cell(30, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(148.4, 413);
$pdf->Cell(30, 7, $valuesArray[8], 0, 1, 'C');
// Hydrometer Reading RM
$values = $Search['HyReading'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(179, 359);
$pdf->Cell(42, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(179, 366.4);
$pdf->Cell(42, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(179, 372.4);
$pdf->Cell(42, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(179, 377);
$pdf->Cell(42, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(179, 382);
$pdf->Cell(42, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(179, 392);
$pdf->Cell(42, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(179, 399);
$pdf->Cell(42, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(179, 406);
$pdf->Cell(42, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(179, 413);
$pdf->Cell(42, 7, $valuesArray[8], 0, 1, 'C');
// A or B Depeding of the HY type
$values = $Search['ABdependingHy'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(222, 359);
$pdf->Cell(26, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(222, 366.4);
$pdf->Cell(26, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(222, 372.4);
$pdf->Cell(26, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(222, 377);
$pdf->Cell(26, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(222, 382);
$pdf->Cell(26, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(222, 392);
$pdf->Cell(26, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(222, 399);
$pdf->Cell(26, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(222, 406);
$pdf->Cell(26, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(222, 413);
$pdf->Cell(26, 7, $valuesArray[8], 0, 1, 'C');
// Offset at Reading (rdm)
$values = $Search['OffsetReading'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(248.5, 359);
$pdf->Cell(20, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(248.5, 366.4);
$pdf->Cell(20, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(248.5, 372.4);
$pdf->Cell(20, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(248.5, 377);
$pdf->Cell(20, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(248.5, 382);
$pdf->Cell(20, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(248.5, 392);
$pdf->Cell(20, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(248.5, 399);
$pdf->Cell(20, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(248.5, 406);
$pdf->Cell(20, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(248.5, 413);
$pdf->Cell(20, 7, $valuesArray[8], 0, 1, 'C');
// Mass Percent Finer (Nm)
$values = $Search['MassPercentFiner'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(269, 359);
$pdf->Cell(42, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(269, 366.4);
$pdf->Cell(42, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(269, 372.4);
$pdf->Cell(42, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(269, 377);
$pdf->Cell(42, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(269, 382);
$pdf->Cell(42, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(269, 392);
$pdf->Cell(42, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(269, 399);
$pdf->Cell(42, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(269, 406);
$pdf->Cell(42, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(269, 413);
$pdf->Cell(42, 7, $valuesArray[8], 0, 1, 'C');
// Effective Length (Hm)
$values = $Search['EffectiveLength'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(311, 359);
$pdf->Cell(25, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(311, 366.4);
$pdf->Cell(25, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(311, 372.4);
$pdf->Cell(25, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(311, 377);
$pdf->Cell(25, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(311, 382);
$pdf->Cell(25, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(311, 392);
$pdf->Cell(25, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(311, 399);
$pdf->Cell(25, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(311, 406);
$pdf->Cell(25, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(311, 413);
$pdf->Cell(25, 7, $valuesArray[8], 0, 1, 'C');
// Diameter, mm (Dmm)
$values = $Search['DMm'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(336.4, 359);
$pdf->Cell(22, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(336.4, 366.4);
$pdf->Cell(22, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(336.4, 372.4);
$pdf->Cell(22, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(336.4, 377);
$pdf->Cell(22, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(336.4, 382);
$pdf->Cell(22, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(336.4, 392);
$pdf->Cell(22, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(336.4, 399);
$pdf->Cell(22, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(336.4, 406);
$pdf->Cell(22, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(336.4, 413);
$pdf->Cell(22, 7, $valuesArray[8], 0, 1, 'C');
// Passing % respect to the total sample
$values = $Search['PassingPerceTotalSample'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}
$pdf->SetXY(358.4, 359);
$pdf->Cell(22, 6, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(358.4, 366.4);
$pdf->Cell(22, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(358.4, 372.4);
$pdf->Cell(22, 4, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(358.4, 377);
$pdf->Cell(22, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(358.4, 382);
$pdf->Cell(22, 10, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(358.4, 392);
$pdf->Cell(22, 7, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(358.4, 399);
$pdf->Cell(22, 7, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(358.4, 406);
$pdf->Cell(22, 7, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(358.4, 413);
$pdf->Cell(22, 7, $valuesArray[8], 0, 1, 'C');

// Classification of soils as per USCS ASTM designation D2487-06
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(180, 307);
$pdf->Cell(87, 26, $Search['Classification1'], 0, 1, 'C');

// Comments the laboratory
$pdf->SetXY(270, 440);
$pdf->MultiCell(144, 4, utf8_decode($Search['Comments']), 0, 'L');

// Function to insert base64 image into PDF
function insertarImagenBase64($pdf, $base64Str, $x, $y, $w, $h)
{
    if ($base64Str) {
        $base64Str = preg_replace('#^data:image/\w+;base64,#i', '', $base64Str);
        $imageData = base64_decode($base64Str);
        $tmpFile = tempnam(sys_get_temp_dir(), 'img') . '.png';
        file_put_contents($tmpFile, $imageData);
        $pdf->Image($tmpFile, $x, $y, $w, $h);
        unlink($tmpFile);
    }
}

insertarImagenBase64($pdf, $Chart, 20, 420, 0, 0); // ajusta X, Y, ancho, alto

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'HY' . '.pdf', 'I');
