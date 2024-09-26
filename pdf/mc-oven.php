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

$pdf->AddPage('P', array(300, 250));

$pdf->setSourceFile('PV-F-81248_Laboratory Moisture Content by Oven_Rev 5.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(52, 32);
$pdf->Cell(30, 5, 'PVDJ SOIL LAB', 0, 1, 'L');
$pdf->SetXY(52, 37);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(52, 42);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'L');

$pdf->SetXY(170, 30);
$pdf->Cell(30, 6, $Search['Method'], 0, 1, 'C');
$pdf->SetXY(170, 36);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(170, 42);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');

// Agregar contenido adicional
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(120, 58);
$pdf->Cell(81, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(120, 64);
$pdf->Cell(81, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(120, 69);
$pdf->Cell(81, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(120, 75);
$pdf->Cell(81, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(120, 81);
$pdf->Cell(81, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(120, 87);
$pdf->Cell(81, 6, $Search['Material_Type'], 0, 1, 'C');
$pdf->SetXY(120, 93);
$pdf->Cell(81, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(120, 99);
$pdf->Cell(81, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(120, 104);
$pdf->Cell(81, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(120, 109);
$pdf->Cell(81, 6, $Search['East'], 0, 1, 'C');
$pdf->SetXY(120, 115);
$pdf->Cell(81, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(120, 133);
$pdf->Cell(81, 6, '1', 0, 1, 'C');
$pdf->SetXY(120, 139);
$pdf->Cell(81, 6, $Search['Tare_Name'], 0, 1, 'C');
$pdf->SetXY(120, 145);
$pdf->Cell(81, 6, utf8_decode($Search['Temperature']), 0, 1, 'C');
$pdf->SetXY(120, 150);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil'], 0, 1, 'C');
$pdf->SetXY(120, 156);
$pdf->Cell(81, 6, $Search['Tare_Plus_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(120, 161);
$pdf->Cell(81, 6, $Search['Water_Ww'], 0, 1, 'C');
$pdf->SetXY(120, 167);
$pdf->Cell(81, 6, $Search['Tare_g'], 0, 1, 'C');
$pdf->SetXY(120, 173);
$pdf->Cell(81, 6, $Search['Dry_Soil_Ws'], 0, 1, 'C');
$pdf->SetXY(120, 178);
$pdf->Cell(81, 6, $Search['Moisture_Content_Porce'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(21, 200);
$pdf->Cell(170, 50, $Search['Comments'], 0, 1, 'C');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>