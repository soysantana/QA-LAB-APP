<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('los_angeles_abrasion_coarse_filter', $_GET['id']);

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
$pdf->setSourceFile('template/PV-F-01715 Laboratory Los Angeles Abrasion for Coarse Filtes-CF.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(75, 91);
$pdf->Cell(40, 5, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(75, 112);
$pdf->Cell(40, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(75, 119);
$pdf->Cell(40, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(75, 126);
$pdf->Cell(40, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(75, 146);
$pdf->Cell(40, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(75, 153);
$pdf->Cell(40, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(75, 160);
$pdf->Cell(40, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(75, 167);
$pdf->Cell(40, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(175, 91);
$pdf->Cell(40, 5, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(175, 112);
$pdf->Cell(40, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(175, 119);
$pdf->Cell(40, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(175, 126);
$pdf->Cell(40, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(175, 146);
$pdf->Cell(40, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(175, 153);
$pdf->Cell(40, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(175, 160);
$pdf->Cell(40, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(175, 167);
$pdf->Cell(40, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(285, 91);
$pdf->Cell(40, 5, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(285, 112);
$pdf->Cell(40, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(285, 119);
$pdf->Cell(40, 6, '', 0, 1, 'C');
$pdf->SetXY(285, 126);
$pdf->Cell(40, 6, '', 0, 1, 'C');
$pdf->SetXY(285, 146);
$pdf->Cell(40, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(285, 153);
$pdf->Cell(40, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(285, 160);
$pdf->Cell(40, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(285, 167);
$pdf->Cell(40, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// Test Information and values
$pdf->SetXY(198, 213);
$pdf->Cell(33, 8, $Search['Grading'], 0, 1, 'C');
$pdf->SetXY(198, 246);
$pdf->Cell(33, 8, $Search['Initial_Weight'], 0, 1, 'C');
$pdf->SetXY(198, 254);
$pdf->Cell(33, 8, $Search['Final_Weight'], 0, 1, 'C');
$pdf->SetXY(198, 263);
$pdf->Cell(33, 8, $Search['Weight_Loss'], 0, 1, 'C');
$pdf->SetXY(198, 271);
$pdf->Cell(33, 8, $Search['Weight_Loss_Porce'], 0, 1, 'C');

// Test Results
$pdf->SetXY(198, 291);
$valor = $Search['Weight_Loss_Porce'];
$texto =  ($valor < 45) ? 'Passed' : 'Failed';
$pdf->Cell(33, 5, $texto, 0, 1, 'C');

// Comparision Information
$pdf->SetXY(132, 318);
$pdf->Cell(33, 5, $Search['Weight_Loss_Porce'], 0, 1, 'C');

// Comments and observations
$pdf->SetXY(63, 357);
$pdf->MultiCell(130, 4, $Search['Comments'], 0, 'L');


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
