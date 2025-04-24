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

$pdf->setSourceFile('template/PV-F-01720_Laboratory Soundness Test.pdf');
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
$pdf->Cell(30, 6, '', 0, 1, 'C');
$pdf->SetXY(360, 93);
$pdf->Cell(30, 6, '', 0, 1, 'C');
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
$pdf->SetXY(105, 194);
$pdf->Cell(25, 5, $Search['WtRetCoarse1'], 0, 1, 'C');
$pdf->SetXY(105, 199);
$pdf->Cell(25, 5, $Search['WtRetCoarse2'], 0, 1, 'C');
$pdf->SetXY(105, 204);
$pdf->Cell(25, 6, $Search['WtRetCoarse3'], 0, 1, 'C');
$pdf->SetXY(105, 209);
$pdf->Cell(25, 5, $Search['WtRetCoarse4'], 0, 1, 'C');
$pdf->SetXY(105, 215);
$pdf->Cell(25, 5, $Search['WtRetCoarse5'], 0, 1, 'C');
$pdf->SetXY(105, 220);
$pdf->Cell(25, 5, $Search['WtRetCoarse6'], 0, 1, 'C');
$pdf->SetXY(105, 225);
$pdf->Cell(25, 6, $Search['WtRetCoarse7'], 0, 1, 'C');
$pdf->SetXY(105, 230);
$pdf->Cell(25, 6, $Search['WtRetCoarse8'], 0, 1, 'C');
$pdf->SetXY(105, 236);
$pdf->Cell(25, 5, $Search['WtRetCoarse9'], 0, 1, 'C');
$pdf->SetXY(105, 241);
$pdf->Cell(25, 5, $Search['WtRetCoarse10'], 0, 1, 'C');
$pdf->SetXY(105, 246);
$pdf->Cell(25, 5, $Search['WtRetCoarse11'], 0, 1, 'C');
$pdf->SetXY(105, 251);
$pdf->Cell(25, 5, $Search['WtRetCoarseTotal'], 0, 1, 'C');

$pdf->SetXY(130, 194);
$pdf->Cell(20, 5, $Search['PctRetCoarse1'], 0, 1, 'C');
$pdf->SetXY(130, 199);
$pdf->Cell(20, 5, $Search['PctRetCoarse2'], 0, 1, 'C');
$pdf->SetXY(130, 204);
$pdf->Cell(20, 6, $Search['PctRetCoarse3'], 0, 1, 'C');
$pdf->SetXY(130, 209);
$pdf->Cell(20, 6, $Search['PctRetCoarse4'], 0, 1, 'C');
$pdf->SetXY(130, 215);
$pdf->Cell(20, 5, $Search['PctRetCoarse5'], 0, 1, 'C');
$pdf->SetXY(130, 220);
$pdf->Cell(20, 5, $Search['PctRetCoarse6'], 0, 1, 'C');
$pdf->SetXY(130, 225);
$pdf->Cell(20, 6, $Search['PctRetCoarse7'], 0, 1, 'C');
$pdf->SetXY(130, 230);
$pdf->Cell(20, 6, $Search['PctRetCoarse8'], 0, 1, 'C');
$pdf->SetXY(130, 236);
$pdf->Cell(20, 5, $Search['PctRetCoarse9'], 0, 1, 'C');
$pdf->SetXY(130, 241);
$pdf->Cell(20, 5, $Search['PctRetCoarse10'], 0, 1, 'C');
$pdf->SetXY(130, 246);
$pdf->Cell(20, 5, $Search['PctRetCoarse11'], 0, 1, 'C');
$pdf->SetXY(130, 251);
$pdf->Cell(20, 5, $Search['PctRetCoarseTotal'], 0, 1, 'C');

// Fine Aggregate
$pdf->SetXY(105, 262);
$pdf->Cell(25, 5, $Search['WtRetFine1'], 0, 1, 'C');
$pdf->SetXY(105, 267);
$pdf->Cell(25, 5, $Search['WtRetFine2'], 0, 1, 'C');
$pdf->SetXY(105, 272);
$pdf->Cell(25, 6, $Search['WtRetFine3'], 0, 1, 'C');
$pdf->SetXY(105, 277);
$pdf->Cell(25, 6, $Search['WtRetFine4'], 0, 1, 'C');
$pdf->SetXY(105, 282);
$pdf->Cell(25, 6, $Search['WtRetFine5'], 0, 1, 'C');
$pdf->SetXY(105, 288);
$pdf->Cell(25, 5, $Search['WtRetFine6'], 0, 1, 'C');
$pdf->SetXY(105, 294);
$pdf->Cell(25, 6, $Search['WtRetFine7'], 0, 1, 'C');
$pdf->SetXY(105, 304);
$pdf->Cell(25, 5, $Search['WtRetFineTotal'], 0, 1, 'C');

$pdf->SetXY(130, 262);
$pdf->Cell(20, 5, $Search['PctRetFine1'], 0, 1, 'C');
$pdf->SetXY(130, 267);
$pdf->Cell(20, 5, $Search['PctRetFine2'], 0, 1, 'C');
$pdf->SetXY(130, 272);
$pdf->Cell(20, 6, $Search['PctRetFine3'], 0, 1, 'C');
$pdf->SetXY(130, 277);
$pdf->Cell(20, 6, $Search['PctRetFine4'], 0, 1, 'C');
$pdf->SetXY(130, 282);
$pdf->Cell(20, 6, $Search['PctRetFine5'], 0, 1, 'C');
$pdf->SetXY(130, 288);
$pdf->Cell(20, 5, $Search['PctRetFine6'], 0, 1, 'C');
$pdf->SetXY(130, 294);
$pdf->Cell(20, 6, $Search['PctRetFine7'], 0, 1, 'C');
$pdf->SetXY(130, 304);
$pdf->Cell(20, 5, $Search['PctRetFineTotal'], 0, 1, 'C');

// Soundness Test Fine Aggregate
$pdf->SetXY(280, 183);
$pdf->Cell(28, 5, $Search['StarWeightRet1'], 0, 1, 'C');
$pdf->SetXY(280, 188);
$pdf->Cell(28, 5, $Search['StarWeightRet2'], 0, 1, 'C');
$pdf->SetXY(280, 194);
$pdf->Cell(28, 5, $Search['StarWeightRet3'], 0, 1, 'C');
$pdf->SetXY(280, 199);
$pdf->Cell(28, 6, $Search['StarWeightRet4'], 0, 1, 'C');
$pdf->SetXY(280, 204);
$pdf->Cell(28, 6, $Search['StarWeightRet5'], 0, 1, 'C');
$pdf->SetXY(280, 209);
$pdf->Cell(28, 6, $Search['StarWeightRet6'], 0, 1, 'C');
$pdf->SetXY(280, 215);
$pdf->Cell(28, 5, $Search['StarWeightRet7'], 0, 1, 'C');

$pdf->SetXY(337, 183);
$pdf->Cell(22, 5, $Search['FinalWeightRet1'], 0, 1, 'C');
$pdf->SetXY(337, 188);
$pdf->Cell(22, 6, $Search['FinalWeightRet2'], 0, 1, 'C');
$pdf->SetXY(337, 194);
$pdf->Cell(22, 6, $Search['FinalWeightRet3'], 0, 1, 'C');
$pdf->SetXY(337, 199);
$pdf->Cell(22, 6, $Search['FinalWeightRet4'], 0, 1, 'C');
$pdf->SetXY(337, 204);
$pdf->Cell(22, 6, $Search['FinalWeightRet5'], 0, 1, 'C');
$pdf->SetXY(337, 209);
$pdf->Cell(22, 5, $Search['FinalWeightRet6'], 0, 1, 'C');
$pdf->SetXY(337, 215);
$pdf->Cell(22, 5, $Search['FinalWeightRet7'], 0, 1, 'C');

$pdf->SetXY(359, 183);
$pdf->Cell(22, 5, $Search['PercentagePassing1'], 0, 1, 'C');
$pdf->SetXY(359, 188);
$pdf->Cell(22, 6, $Search['PercentagePassing2'], 0, 1, 'C');
$pdf->SetXY(359, 194);
$pdf->Cell(22, 6, $Search['PercentagePassing3'], 0, 1, 'C');
$pdf->SetXY(359, 199);
$pdf->Cell(22, 6, $Search['PercentagePassing4'], 0, 1, 'C');
$pdf->SetXY(359, 204);
$pdf->Cell(22, 6, $Search['PercentagePassing5'], 0, 1, 'C');
$pdf->SetXY(359, 209);
$pdf->Cell(22, 5, $Search['PercentagePassing6'], 0, 1, 'C');
$pdf->SetXY(359, 215);
$pdf->Cell(22, 5, $Search['PercentagePassing7'], 0, 1, 'C');

$pdf->SetXY(381, 183);
$pdf->Cell(22, 5, $Search['WeightedLoss1'], 0, 1, 'C');
$pdf->SetXY(381, 188);
$pdf->Cell(22, 6, $Search['WeightedLoss2'], 0, 1, 'C');
$pdf->SetXY(381, 194);
$pdf->Cell(22, 6, $Search['WeightedLoss3'], 0, 1, 'C');
$pdf->SetXY(381, 199);
$pdf->Cell(22, 6, $Search['WeightedLoss4'], 0, 1, 'C');
$pdf->SetXY(381, 204);
$pdf->Cell(22, 6, $Search['WeightedLoss5'], 0, 1, 'C');
$pdf->SetXY(381, 209);
$pdf->Cell(22, 5, $Search['WeightedLoss6'], 0, 1, 'C');
$pdf->SetXY(381, 215);
$pdf->Cell(22, 5, $Search['WeightedLoss7'], 0, 1, 'C');


// Comments and Observations
$pdf->SetXY(27, 451);
$pdf->MultiCell(195, 4, $Search['Comments'], 0, 'L');


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
