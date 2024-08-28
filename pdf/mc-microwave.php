<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('moisture_microwave', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(300, 250));

$pdf->setSourceFile('mc-microwave.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(67, 37);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(67, 42);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(185, 30);
$pdf->Cell(30, 6, $Search['Method'], 0, 1, 'C');
$pdf->SetXY(185, 36);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(185, 42);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(135, 58);
$pdf->Cell(81, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(135, 64);
$pdf->Cell(81, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(135, 69);
$pdf->Cell(81, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(135, 76);
$pdf->Cell(81, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(135, 82.5);
$pdf->Cell(81, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(135, 89);
$pdf->Cell(81, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(135, 94.5);
$pdf->Cell(81, 6, $Search['Material_Type'], 0, 1, 'C');
$pdf->SetXY(135, 100);
$pdf->Cell(81, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(135, 105);
$pdf->Cell(81, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(135, 111);
$pdf->Cell(81, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(135, 116);
$pdf->Cell(81, 6, $Search['East'], 0, 1, 'C');
$pdf->SetXY(135, 122.5);
$pdf->Cell(81, 6, $Search['Elev'], 0, 1, 'C');


$pdf->SetXY(135, 146);
$pdf->Cell(81, 6, $Search['Tare_Name'], 0, 1, 'C');
$pdf->SetXY(135, 152);
$pdf->Cell(81, 6, $Search['Microwave_Model'], 0, 1, 'C');
$pdf->SetXY(135, 158);
$pdf->Cell(81, 6, "", 0, 1, 'C');
$pdf->SetXY(135, 164);
$pdf->Cell(81, 6, "", 0, 1, 'C');
$pdf->SetXY(135, 169.5);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil'], 0, 1, 'C');
$pdf->SetXY(135, 175);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(135, 181);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(135, 187);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(135, 192);
$pdf->Cell(81, 6, $Search['Tare_Plus_Wet_Soil_4'], 0, 1, 'C');
$pdf->SetXY(135, 198);
$pdf->Cell(81, 6, $Search['Water_Ww'], 0, 1, 'C');
$pdf->SetXY(135, 203);
$pdf->Cell(81, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(135, 208.5);
$pdf->Cell(81, 6, $Search['Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(135, 214);
$pdf->Cell(81, 6, $Search['Moisture_Content_Porce'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(21, 230);
$pdf->Cell(183, 25, $Search['Comments'], 0, 1, 'C');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>