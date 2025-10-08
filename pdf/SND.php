<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('soundness', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(560, 430));

$pdf->setSourceFile('template/PV-F-01720_Laboratory_Soundness_SND_Rev2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

//Information for the essay
$pdf->SetXY(90, 56);
$pdf->Cell(30, 4, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(90, 77);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(90, 85);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(90, 93);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(90, 112);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(90, 120);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(90, 127);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(90, 136);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(220, 56);
$pdf->Cell(30, 4, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(220, 77);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(220, 85);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(220, 93);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(220, 112);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(220, 120);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(220, 127);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(220, 136);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(360, 56);
$pdf->Cell(30, 6, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(360, 77);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(360, 85);
$pdf->Cell(30, 6, $Search['Preparation_Methods'], 0, 1, 'C');
$pdf->SetXY(360, 93);
$pdf->Cell(30, 6, $Search['Split_Methods'], 0, 1, 'C');
$pdf->SetXY(360, 112);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(360, 120);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(360, 127);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(360, 136);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

// Define the font for the rest of the document
$pdf->SetFont('Arial', '', 12);

// Grain Size Distribution
$pdf->SetXY(65, 147);
$pdf->Cell(40, 8, $Search['WtDrySoil'], 0, 1, 'C');
$pdf->SetXY(65, 155);
$pdf->Cell(40, 23, $Search['WtWashed'], 0, 1, 'C');

// Coarse Aggregate
$values = $Search['WtRetCoarse'];

$valuesArray = explode(',', $values);

foreach ($valuesArray as $k => $v) {
    if ($v === 'null') $valuesArray[$k] = '';
}

$pdf->SetXY(105, 194);
$pdf->Cell(25, 5, $valuesArray[0], 0, 1, 'C');
$pdf->SetXY(105, 199);
$pdf->Cell(25, 5, $valuesArray[1], 0, 1, 'C');
$pdf->SetXY(105, 204);
$pdf->Cell(25, 6, $valuesArray[2], 0, 1, 'C');
$pdf->SetXY(105, 209);
$pdf->Cell(25, 5, $valuesArray[3], 0, 1, 'C');
$pdf->SetXY(105, 215);
$pdf->Cell(25, 5, $valuesArray[4], 0, 1, 'C');
$pdf->SetXY(105, 220);
$pdf->Cell(25, 5, $valuesArray[5], 0, 1, 'C');
$pdf->SetXY(105, 225);
$pdf->Cell(25, 6, $valuesArray[6], 0, 1, 'C');
$pdf->SetXY(105, 230);
$pdf->Cell(25, 6, $valuesArray[7], 0, 1, 'C');
$pdf->SetXY(105, 236);
$pdf->Cell(25, 5, $valuesArray[8], 0, 1, 'C');
$pdf->SetXY(105, 241);
$pdf->Cell(25, 5, $valuesArray[9], 0, 1, 'C');
$pdf->SetXY(105, 246);
$pdf->Cell(25, 5, $valuesArray[10], 0, 1, 'C');
$pdf->SetXY(105, 251);
$pdf->Cell(25, 5, $Search['WtRetCoarseTotal'], 0, 1, 'C');

// Fine Aggregate Percentages
$pctValues = $Search['PctRetCoarse'];

$pctArray = explode(',', $pctValues);

foreach ($pctArray as $k => $v) {
    if ($v === 'null') $pctArray[$k] = '';
}

$y = 194;
for ($i = 0; $i < count($pctArray); $i++) {
    $pdf->SetXY(130, $y);
    $pdf->Cell(20, 5, $pctArray[$i], 0, 1, 'C');
    $y += ($i == 2 || $i == 6) ? 6 : 5;
}
$pdf->SetXY(130, 251);
$pdf->Cell(20, 5, $Search['PctRetCoarseTotal'], 0, 1, 'C');

// Fine Aggregate
$fineValues = $Search['WtRetFine'];

$fineArray = explode(',', $fineValues);

foreach ($fineArray as $k => $v) {
    if ($v === 'null') $fineArray[$k] = '';
}

$y = 262;
for ($i = 0; $i < count($fineArray); $i++) {
    $pdf->SetXY(105, $y);
    $pdf->Cell(25, 5, $fineArray[$i], 0, 1, 'C');
    $y += ($i == 2 || $i == 3 || $i == 5) ? 6 : 5;
}
$pdf->SetXY(105, 304);
$pdf->Cell(25, 5, $Search['WtRetFineTotal'], 0, 1, 'C');

$pctFineValues = $Search['PctRetFine'];

$pctFineArray = explode(',', $pctFineValues);

foreach ($pctFineArray as $k => $v) {
    if ($v === 'null') $pctFineArray[$k] = '';
}

$y = 262;
for ($i = 0; $i < count($pctFineArray); $i++) {
    $pdf->SetXY(130, $y);
    $pdf->Cell(20, 5, $pctFineArray[$i], 0, 1, 'C');
    $y += ($i == 2 || $i == 3 || $i == 5) ? 6 : 5;
}
$pdf->SetXY(130, 304);
$pdf->Cell(20, 5, $Search['PctRetFineTotal'], 0, 1, 'C');

// Soundness Test Fine Aggregate
$starValues = $Search['StarWeightRetFine'];

$starArray = explode(',', $starValues);

foreach ($starArray as $k => $v) {
    if ($v === 'null') $starArray[$k] = '';
}

$y = 183;
for ($i = 0; $i < count($starArray); $i++) {
    $pdf->SetXY(280, $y);
    $pdf->Cell(28, 5, $starArray[$i], 0, 1, 'C');
    $y += ($i == 2 || $i == 3 || $i == 4) ? 6 : 4.5;
}
$pdf->SetXY(280, 220);
$pdf->Cell(28, 5, $Search['TotalStarWeightRetFine'], 0, 1, 'C');

$Values = $Search['FinalWeightRetFine'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 183;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(337, $y);
    $pdf->Cell(22, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 2 || $i == 3 || $i == 4) ? 6 : 4.5;
}
$pdf->SetXY(337, 220);
$pdf->Cell(22, 5, $Search['TotalFinalWeightRetFine'], 0, 1, 'C');

$Values = $Search['PercentagePassingFine'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 183;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(359, $y);
    $pdf->Cell(22, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 2 || $i == 3 || $i == 4) ? 6 : 4.5;
}

$Values = $Search['WeightedLossFine'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 183;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(381, $y);
    $pdf->Cell(22, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 2 || $i == 3 || $i == 4) ? 6 : 4.5;
}

$Values = $Search['StarWeightRetCoarse'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 231;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(280, $y);
    $pdf->Cell(28, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 2 || $i == 3 || $i == 4) ? 5 : 5.2;
}
$pdf->SetXY(280, 283);
$pdf->Cell(28, 5, $Search['TotalStarWeightRetCoarse'], 0, 1, 'C');

$Values = $Search['FinalWeightRetCoarse'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 231;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(337, $y);
    $pdf->Cell(22, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 4 || $i == 5 || $i == 6 || $i == 7) ? 5 : 9;
}
$pdf->SetXY(337, 283);
$pdf->Cell(22, 5, $Search['TotalFinalWeightRetCoarse'], 0, 1, 'C');

$Values = $Search['PercentagePassingCoarse'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 231;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(359, $y);
    $pdf->Cell(22, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 4 || $i == 5 || $i == 6 || $i == 7) ? 5 : 9;
}

$Values = $Search['WeightedLossCoarse'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 231;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(381, $y);
    $pdf->Cell(22, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 4 || $i == 5 || $i == 6 || $i == 7) ? 5 : 9;
}

// Cycle Information
$Values = $Search['StartDate'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 315;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(225, $y);
    $pdf->Cell(24, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 3 || $i == 2) ? 4.5 : 6.5;
}

$Values = $Search['RoomTemp'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 315;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(249, $y);
    $pdf->Cell(31, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 3 || $i == 2) ? 4.5 : 6.5;
}

$Values = $Search['SolutionTemp'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 315;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(280, $y);
    $pdf->Cell(28, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 3 || $i == 2) ? 4.5 : 6.5;
}

$Values = $Search['SpecificGravity'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 315;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(308, $y);
    $pdf->Cell(28, 5, $Array[$i], 0, 1, 'C');
    $y += ($i == 3 || $i == 2) ? 4.5 : 6.5;
}
// Particles Distress Qualitative
$Values = $Search['SplittingNo'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(66, $y);
    $pdf->Cell(38, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}

$Values = $Search['SplittingPct'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(105, $y);
    $pdf->Cell(25, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}

$Values = $Search['CrumblingNo'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(128, $y);
    $pdf->Cell(25, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}

$Values = $Search['CrumblingPct'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(150, $y);
    $pdf->Cell(22, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}

$Values = $Search['CrackingNo'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(172, $y);
    $pdf->Cell(52, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}

$Values = $Search['CrackingPct'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(224, $y);
    $pdf->Cell(25, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}

$Values = $Search['FlakingNo'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(249, $y);
    $pdf->Cell(31, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}

$Values = $Search['FlakingPct'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(280, $y);
    $pdf->Cell(28, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}

$Values = $Search['TotalParticles'];

$Array = explode(',', $Values);

foreach ($Array as $k => $v) {
    if ($v === 'null') $Array[$k] = '';
}

$y = 388;
for ($i = 0; $i < count($Array); $i++) {
    $pdf->SetXY(308, $y);
    $pdf->Cell(29, 10, $Array[$i], 0, 1, 'C');
    $y += ($i == 3) ? 10 : 11;
}


// Comments and Observations
$pdf->SetXY(27, 451);
$pdf->MultiCell(195, 4, $Search['Comments'], 0, 'L');
$pdf->SetXY(225, 451);
$pdf->MultiCell(110, 4, $Search['FieldComment'], 0, 'L');

// Test Result Passed or Failed
$pdf->SetFont('Arial', '', 12);

$a = $Search['TotalWeightedLossFine'];
$b = $Search['TotalWeightedLossCoarse'];

$pdf->SetXY(381, 220);
$pdf->SetTextColor($a >= 12 ? 255 : 0, $a >= 12 ? 0 : 0, $a >= 12 ? 0 : 0);
$pdf->Cell(22, 5, $a, 0, 1, 'C');
$pdf->SetXY(381, 283);
$pdf->SetTextColor($b >= 12 ? 255 : 0, $b >= 12 ? 0 : 0, $b >= 12 ? 0 : 0);
$pdf->Cell(22, 5, $b, 0, 1, 'C');

// Condición para "Acepted"
if (
    $a < 12 &&
    $b < 12
) {
    $resultado = 'Passed';
    $pdf->SetTextColor(0, 0, 0);
} else {
    $resultado = 'Failed';
    $pdf->SetTextColor(255, 0, 0);
}
$pdf->SetXY(65, 327);
$pdf->Cell(65, 6, $resultado, 0, 1, 'C');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'SND' . '-' . $Search['Material_Type'] . '.pdf', 'I');
