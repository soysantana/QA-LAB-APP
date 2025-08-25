<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('moisture_oven', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(300, 260));

$pdf->setSourceFile('template/PV-F-01713 Laboratory Moisture Content With Oven.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 11);

// Project Information
$pdf->SetXY(58.5, 37);
$pdf->Cell(25, 1, $Search['Project_Name'], 0, 1, 'L');
$pdf->SetXY(137, 37);
$pdf->Cell(25, 1, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(220, 34);
$pdf->Cell(25, 5, $Search['Client'], 0, 1, 'L');

// Laboratory Information
$pdf->SetXY(58, 52);
$pdf->Cell(27, 1, 'PVDJ SOIL LAB', 0, 1, 'L');
$pdf->SetXY(58, 58);
$pdf->Cell(27, 1, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(58, 64);
$pdf->Cell(27, 1, $Search['Sample_By'], 0, 1, 'L');
$pdf->SetXY(137, 52);
$pdf->Cell(26, 1, $Search['Standard'], 0, 1, 'L');
$pdf->SetXY(137, 55);
$pdf->Cell(27, 5, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(137, 63);
$pdf->Cell(26.5, 1, date('Y-m-d', strtotime($Search['Registed_Date'])), 0, 1, 'L');
$pdf->SetXY(220, 52);
$pdf->Cell(26, 1, $Search['Method'], 0, 1, 'L');

// Sample Information
$pdf->SetXY(58.5, 80);
$pdf->Cell(25, 1, $Search['Structure'], 0, 1, 'L');
$pdf->SetXY(58.5, 86);
$pdf->Cell(25, 1, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(58.5, 92);
$pdf->Cell(25, 1, $Search['Source'], 0, 1, 'L');
$pdf->SetXY(58.5, 98);
$pdf->Cell(25, 1, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(137, 78);
$pdf->Cell(25, 1, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(137, 85);
$pdf->Cell(25, 1, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(137, 93);
$pdf->Cell(25, 1, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(137, 100);
$pdf->Cell(25, 1, $Search['Elev'], 0, 1, 'C');
$pdf->SetXY(220, 79);
$pdf->Cell(25, 1, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(220, 86);
$pdf->Cell(25, 1, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(220, 94);
$pdf->Cell(25, 1, $Search['North'], 0, 1, 'C');
$pdf->SetXY(220, 100);
$pdf->Cell(25, 1, $Search['East'], 0, 1, 'C');

// Test information
$pdf->SetXY(147, 117);
$pdf->Cell(25, 1, '1', 0, 1, 'C');
$pdf->SetXY(147, 123);
$pdf->Cell(25, 1, $Search['Tare_Name'], 0, 1, 'C');
$pdf->SetXY(147, 129);
$pdf->Cell(25, 1, utf8_decode($Search['Temperature']), 0, 1, 'C');
$pdf->SetXY(147, 134);
$pdf->Cell(25, 1, $Search['Tare_Plus_Wet_Soil'], 0, 1, 'C');
$pdf->SetXY(147, 140);
$pdf->Cell(25, 1, $Search['Tare_Plus_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(147, 146);
$pdf->Cell(25, 1, $Search['Water_Ww'], 0, 1, 'C');
$pdf->SetXY(147, 152);
$pdf->Cell(25, 1, $Search['Tare_g'], 0, 1, 'C');
$pdf->SetXY(147, 157.5);
$pdf->Cell(25, 1, $Search['Dry_Soil_Ws'], 0, 1, 'C');
$pdf->SetXY(147, 162.5);
$pdf->Cell(25, 1, $Search['Moisture_Content_Porce'], 0, 1, 'C');

// Test Results
$pdf->SetXY(198, 172.5);
$pdf->Cell(
    25,
    1,
    ($Search['Material_Type'] != "LPF" ||
        ($Search['Moisture_Content_Porce'] >= 14.5 && $Search['Moisture_Content_Porce'] <= 27.4))
        ? "Passed" : "Failed",
    0,
    1,
    'C'
);



// Comparasion Information
$pdf->SetXY(72, 189.5);
$pdf->Cell(25, 1, $Search['Moisture_Content_Porce'], 0, 1, 'C');

// Comments Laboratory
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(24, 208);
$pdf->MultiCell(166, 4, $Search['Comments'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'MC' . '-' . $Search['Material_Type'] . '.pdf', 'I');
