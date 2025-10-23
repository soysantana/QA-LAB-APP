<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('los_angeles_abrasion_small', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

// Define manualmente el tamaño de página
$pdf->AddPage('P', array(420, 450));

// Importar una página de otro PDF
$pdf->setSourceFile('template/PV-F-01716 Laboratory Los Angeles Abrasion for large agregate.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(75, 93);
$pdf->Cell(40, 5, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(75, 114);
$pdf->Cell(40, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(75, 121);
$pdf->Cell(40, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(75, 128);
$pdf->Cell(40, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(75, 148);
$pdf->Cell(40, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(75, 155);
$pdf->Cell(40, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(75, 162);
$pdf->Cell(40, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(75, 169);
$pdf->Cell(40, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(175, 93);
$pdf->Cell(40, 5, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(175, 114);
$pdf->Cell(40, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(175, 121);
$pdf->Cell(40, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(175, 128);
$pdf->Cell(40, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(175, 148);
$pdf->Cell(40, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(175, 155);
$pdf->Cell(40, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(175, 162);
$pdf->Cell(40, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(175, 169);
$pdf->Cell(40, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(285, 93);
$pdf->Cell(40, 5, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(285, 114);
$pdf->Cell(40, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(285, 121);
$pdf->Cell(40, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(285, 128);
$pdf->Cell(40, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(285, 148);
$pdf->Cell(40, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(285, 155);
$pdf->Cell(40, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(285, 162);
$pdf->Cell(40, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(285, 169);
$pdf->Cell(40, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// Test Information and values
$pdf->SetXY(198, 209);
$pdf->Cell(33, 8, $Search['NominalMaxSize'], 0, 1, 'C');
$pdf->SetXY(198, 217);
$pdf->Cell(33, 8, $Search['Grading'], 0, 1, 'C');
$pdf->SetXY(198, 225);
$pdf->Cell(33, 8, $Search['Weight_Spheres'], 0, 1, 'C');
$pdf->SetXY(198, 233);
$pdf->Cell(33, 8, $Search['Revolutions'], 0, 1, 'C');
$pdf->SetXY(198, 241);
$pdf->Cell(33, 8, $Search['Initial_Weight'], 0, 1, 'C');
$pdf->SetXY(198, 249);
$pdf->Cell(33, 8, $Search['Final_Weight'], 0, 1, 'C');
$pdf->SetXY(198, 258);
$pdf->Cell(33, 8, $Search['Weight_Loss'], 0, 1, 'C');
$pdf->SetXY(198, 266);
$pdf->Cell(33, 8, $Search['Weight_Loss_Porce'], 0, 1, 'C');

// Test Results
$pdf->SetXY(198, 287);
$valor = $Search['Weight_Loss_Porce'];
$texto =  ($valor < 45) ? 'Passed' : 'Failed';
$pdf->Cell(33, 5, $texto, 0, 1, 'C');

// Comparision Information
$pdf->SetXY(132, 314);
$pdf->Cell(33, 5, $Search['Weight_Loss_Porce'], 0, 1, 'C');

// Comments and observations
$pdf->SetXY(63, 354);
$pdf->MultiCell(130, 4, $Search['Comments'], 0, 'L');

$pdf->SetXY(200, 354);
$pdf->MultiCell(130, 4, $Search['FieldComment'], 0, 'L');


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'LAA' . '-' . $Search['Material_Type'] . '.pdf', 'I');
