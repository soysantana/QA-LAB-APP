<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('moisture_oven', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(300, 260));

$pdf->setSourceFile('PV-TSF-CQA_General-Moisture Content Rev.2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 9);
// Project Information
$pdf->SetXY(58.5, 50);
$pdf->Cell(25, 4, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(58.5, 54);
$pdf->Cell(25, 5, $Search['Client'], 0, 1, 'L');
// Laboratory Information
$pdf->SetXY(137.5, 45.5);
$pdf->Cell(27, 4, 'PVDJ SOIL LAB', 0, 1, 'L');
$pdf->SetXY(137.5, 50);
$pdf->Cell(27, 4, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(137.5, 54);
$pdf->Cell(27, 5, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(196, 45.5);
$pdf->Cell(26.5, 4, date('Y-m-d', strtotime($Search['Registed_Date'])), 0, 1, 'L');
$pdf->SetXY(196, 50);
$pdf->Cell(26, 4, $Search['Standard'], 1, 1, 'L');

// Agregar contenido adicional
$pdf->SetFont('Arial', '', 10);

$pdf->SetXY(58.5, 68.5);
$pdf->Cell(25, 4, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(58.5, 73);
$pdf->Cell(25, 4, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(58.5, 77);
$pdf->Cell(25, 4, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(58.5, 81.5);
$pdf->Cell(25, 4, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(58.5, 86);
$pdf->Cell(25, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(58.5, 90);
$pdf->Cell(25, 4, $Search['North'], 0, 1, 'C');
$pdf->SetXY(58.5, 94.5);
$pdf->Cell(25, 4, $Search['East'], 0, 1, 'C');
$pdf->SetXY(58.5, 99);
$pdf->Cell(25, 4, $Search['Elev'], 0, 1, 'C');
$pdf->SetXY(58.5, 104);
$pdf->Cell(25, 4, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(58.5, 109);
$pdf->Cell(25, 4, $Search['Material_Type'], 0, 1, 'C');
$pdf->SetXY(58.5, 113.5);
$pdf->Cell(25, 4, $Search['Depth_From'] . '-' . $Search['Depth_To'], 1, 1, 'C');


$pdf->SetXY(58.5, 126.5);
$pdf->Cell(25, 4, '1', 0, 1, 'C');
$pdf->SetXY(58.5, 131);
$pdf->Cell(25, 4, $Search['Tare_Name'], 0, 1, 'C');
$pdf->SetXY(58.5, 135);
$pdf->Cell(25, 4, utf8_decode($Search['Temperature']), 0, 1, 'C');
$pdf->SetXY(58.5, 139.5);
$pdf->Cell(25, 4, $Search['Tare_Plus_Wet_Soil'], 0, 1, 'C');
$pdf->SetXY(58.5, 144);
$pdf->Cell(25, 4, $Search['Tare_Plus_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(58.5, 148);
$pdf->Cell(25, 4, $Search['Water_Ww'], 0, 1, 'C');
$pdf->SetXY(58.5, 152.5);
$pdf->Cell(25, 4, $Search['Tare_g'], 0, 1, 'C');
$pdf->SetXY(58.5, 157);
$pdf->Cell(25, 4, $Search['Dry_Soil_Ws'], 0, 1, 'C');
$pdf->SetXY(58.5, 161);
$pdf->Cell(25, 8, $Search['Moisture_Content_Porce'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(165, 70);
$pdf->MultiCell(83, 135, $Search['Comments'], 0, 'C');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>