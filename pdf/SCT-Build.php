<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('sand_castle_test', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('L', array(240, 340));

$pdf->setSourceFile('template/PV-F-01711 Laboratory Sand Castle Test_Rev1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(45, 40);
$pdf->Cell(31, 4, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(45, 54);
$pdf->Cell(31, 4, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(45, 59);
$pdf->Cell(31, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(45, 65);
$pdf->Cell(31, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(45, 79);
$pdf->Cell(31, 4, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(45, 85);
$pdf->Cell(31, 4, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(45, 91);
$pdf->Cell(31, 4, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(45, 97);
$pdf->Cell(31, 4, $Search['Material_Type'], 0, 1, 'C');
$pdf->SetXY(45, 102);
$pdf->Cell(31, 4, $Search['natMc'], 0, 1, 'C');

$pdf->SetXY(130, 40);
$pdf->Cell(31, 4, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(130, 54);
$pdf->Cell(31, 4, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(130, 59);
$pdf->Cell(31, 4, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(130, 65);
$pdf->Cell(31, 4, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(130, 79);
$pdf->Cell(31, 4, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(130, 85);
$pdf->Cell(31, 4, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(130, 91);
$pdf->Cell(31, 4, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(130, 97);
$pdf->Cell(31, 4, $Search['Elev'], 0, 1, 'C');
$pdf->SetXY(130, 102);
$pdf->Cell(31, 4, $Search['optimunMc'], 0, 1, 'C');

$pdf->SetXY(215, 40);
$pdf->Cell(31, 4, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(215, 54);
$pdf->Cell(31, 4, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(215, 59);
$pdf->Cell(31, 4, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(215, 65);
$pdf->Cell(31, 4, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(215, 79);
$pdf->Cell(31, 4, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(215, 85);
$pdf->Cell(31, 4, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(215, 91);
$pdf->Cell(31, 4, $Search['North'], 0, 1, 'C');
$pdf->SetXY(215, 97);
$pdf->Cell(31, 4, $Search['East'], 0, 1, 'C');

// Test Result Time, Colla
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(39, 116);
$pdf->Cell(23, 4, $Search['Time'], 0, 1, 'C');
$collapsedArray = explode(',', $Search['Collapsed']);
$y = 120;
$x = 39;
foreach ($collapsedArray as $collapsed) {
    if ($collapsed === 'null' || $collapsed === '') continue;
    $pdf->SetXY($x, $y);
    $pdf->Cell(23, 4, $collapsed, 0, 1, 'C');
    $y += 4.5;
}

$pdf->SetXY(39, 196.5);
$pdf->Cell(23, 4, $Search['TimeSet'], 0, 1, 'C');

$pdf->SetXY(148, 116);
$pdf->Cell(22, 4, $Search['initialHeight'], 0, 1, 'C');
$pdf->SetXY(148, 120);
$pdf->Cell(22, 4, $Search['FinalHeight'], 0, 1, 'C');

$radiusArray = explode(',', $Search['Radius']);
$y = 138;
$x = 125;
foreach ($radiusArray as $radius) {
    if ($radius === 'null' || $radius === '') continue;
    $pdf->SetXY($x, $y);
    $pdf->Cell(23, 4, $radius, 0, 1, 'C');
    $y += 4.5;
}

$angleArray = explode(',', $Search['Angle']);
$y = 174;
$x = 125;
foreach ($angleArray as $angle) {
    if ($angle === 'null' || $angle === '') continue;
    $pdf->SetXY($x, $y);
    $pdf->Cell(23, 4, $angle, 0, 1, 'C');
    $y += 4.5;
}

$pdf->SetXY(125, 196.5);
$pdf->Cell(23, 4, $Search['Average'], 0, 1, 'C');

// Comparison Information & Test Result
$pdf->SetXY(196, 138);
$pdf->Cell(21, 4, $Search['Average'], 0, 1, 'C');
$pdf->SetXY(196, 151.4);
$pdf->Cell(21, 4, $Search['testResult'], 0, 1, 'C');

// Comments and Observations
$pdf->SetXY(170, 165);
$pdf->MultiCell(140, 4, $Search['Comments'], 0, 'L');
$pdf->SetXY(170, 188);
$pdf->MultiCell(140, 4, $Search['FieldComment'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'SCT' . '-' . $Search['Material_Type'] . '.pdf', 'I');
