<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('moisture_constant_mass', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(320, 260));

$pdf->setSourceFile('PV-F-83815_Laboratory Moisture Content Constant Mass_Rev 2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(65, 45);
$pdf->Cell(30, 6, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(65, 51);
$pdf->Cell(30, 6, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(200, 39);
$pdf->Cell(30, 6, $Search['Method'], 0, 1, 'C');
$pdf->SetXY(200, 45);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(200, 51);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');

// Agregar contenido adicional
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(163, 65);
$pdf->Cell(81, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(163, 70);
$pdf->Cell(81, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(163, 76);
$pdf->Cell(81, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(163, 83);
$pdf->Cell(81, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(163, 89);
$pdf->Cell(81, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(163, 95);
$pdf->Cell(81, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(163, 100);
$pdf->Cell(81, 6, $Search['Material_Type'], 0, 1, 'C');
$pdf->SetXY(163, 106);
$pdf->Cell(81, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(163, 111);
$pdf->Cell(81, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(163, 116);
$pdf->Cell(81, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(163, 121);
$pdf->Cell(81, 6, $Search['East'], 0, 1, 'C');
$pdf->SetXY(163, 126);
$pdf->Cell(81, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(163, 143);
$pdf->Cell(81, 6, '1', 0, 1, 'C');
$pdf->SetXY(163, 149);
$pdf->Cell(81, 6, $Search['Tare_Name'], 0, 1, 'C');
$pdf->SetXY(163, 155);
$pdf->Cell(81, 6, utf8_decode($Search['Temperature']), 0, 1, 'C');
$pdf->SetXY(163, 161);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil'], 0, 1, 'C');
$pdf->SetXY(163, 168);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(163, 174);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(163, 179);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(163, 185);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil_4'], 0, 1, 'C');
$pdf->SetXY(163, 191);
$pdf->Cell(81, 6, $Search['Water_Ww'], 0, 1, 'C');
$pdf->SetXY(163, 196);
$pdf->Cell(81, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(163, 202);
$pdf->Cell(81, 6, $Search['Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(163, 208);
$pdf->Cell(81, 6, $Search['Moisture_Content_Porce'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(15, 230);
$pdf->Cell(229, 45, $Search['Comments'], 0, 1, 'C');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>