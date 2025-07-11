<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('reactivity', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(470, 400));

$pdf->setSourceFile('template/PV-F-01956 Laboratory Acid Reativity Test for Coarse Aggregates.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);


$pdf->SetFont('Arial', 'B', 10);

//Information for the essay
$pdf->SetXY(100, 52);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(100, 65);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(100, 77);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(100, 96);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(100, 104);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(100, 113);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(100, 121);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(230, 52);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(230, 65);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(230, 77);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(230, 96);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(230, 104);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(230, 113);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(230, 121);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(320, 52);
$pdf->Cell(30, 6, '', 0, 1, 'C');
$pdf->SetXY(320, 65);
$pdf->Cell(30, 6, '', 0, 1, 'C');
$pdf->SetXY(320, 77);
$pdf->Cell(30, 6, '', 0, 1, 'C');
$pdf->SetXY(320, 96);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(320, 104);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(320, 113);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(320, 121);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);

// Reactivity Test Method FM13-007
$pdf->SetXY(225, 138);
$pdf->Cell(37, 8, $Search['TotalWeight'], 0, 1, 'C');
$pdf->SetXY(225, 146);
$pdf->Cell(37, 8, $Search['WeigtTest'], 0, 1, 'C');

$reactiveValues = explode(',', $Search['ParticlesReactive']);
$y = 155;

foreach ($reactiveValues as $value) {
    $pdf->SetXY(225, $y);
    $pdf->Cell(37, 8, trim($value), 0, 1, 'C');
    $y += 9; // salto vertical, puedes ajustar si es 8 o 9
}

$pdf->SetXY(225, 181);
$pdf->Cell(37, 12, $Search['WeightNo4'], 0, 1, 'C');
$pdf->SetXY(225, 193);
$pdf->Cell(37, 12, $Search['WeightReactiveNo4'], 0, 1, 'C');
$pdf->SetXY(225, 205);
$pdf->Cell(37, 12, $Search['PercentReactive'], 0, 1, 'C');
$pdf->SetXY(225, 217);
$pdf->Cell(37, 12, $Search['AvgParticles'], 0, 1, 'C');
$pdf->SetXY(225, 229);
$pdf->Cell(37, 12, $Search['ReactionResult'], 0, 1, 'C');
$pdf->SetXY(225, 254);
$pdf->Cell(37, 12, $Search['AcidReactivityResult'], 0, 1, 'C');

$pdf->SetXY(73, 291);
$pdf->MultiCell(266, 4, $Search['Comments'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
