<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('specific_gravity_fine', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('L', array(325, 250));

$pdf->setSourceFile('PV-TSF-CQA_FF_Specific Gravity Rev 1.pdf'); 
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY(59, 33);
$pdf->Cell(23, 5, 'Pueblo Viejo', 0, 1, 'L');
$pdf->SetXY(59, 38.5);
$pdf->Cell(23, 5, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(59, 44);
$pdf->Cell(23, 5, $Search['Client'], 0, 1, 'L');
// Laboratory Information
$pdf->SetXY(227, 33);
$pdf->Cell(21, 5, 'PVDJ Soil Lab', 0, 1, 'L');
$pdf->SetXY(227, 38.5);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(227, 44);
$pdf->Cell(21, 5, $Search['Sample_Date'], 0, 1, 'L');
$pdf->SetXY(270, 33);
$pdf->Cell(21, 5, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(270, 38.5);
$pdf->Cell(21, 5, $Search['Standard'], 0, 1, 'L');
$pdf->SetXY(270, 44);
$pdf->Cell(21, 5, $Search['Methods'], 0, 1, 'L');
// Sample Information
$pdf->SetXY(102, 49);
$pdf->Cell(21, 5, $Search['Structure'], 0, 1, 'L');
$pdf->SetXY(102, 54);
$pdf->Cell(21, 5, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(102, 59.5);
$pdf->Cell(21, 3.5, $Search['Source'], 0, 1, 'L');
$pdf->SetXY(145, 49);
$pdf->Cell(21, 5, $Search['Sample_Number'], 0, 1, 'L');
$pdf->SetXY(145, 54);
$pdf->Cell(21, 5, $Search['Depth_From'] . '-' . $Search['Depth_To'], 0, 1, 'L');
$pdf->SetXY(145, 59.5);
$pdf->Cell(21, 3.5, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(199, 49);
$pdf->Cell(21, 5, $Search['North'], 0, 1, 'L');
$pdf->SetXY(199, 54);
$pdf->Cell(21, 5, $Search['East'], 0, 1, 'L');
$pdf->SetXY(199, 59.5);
$pdf->Cell(21, 3.5, $Search['Elev'], 0, 1, 'L');
// Testing Information
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(199, 91);
$pdf->Cell(28, 7, $Search['Pycnometer_Number'], 0, 1, 'C');
$pdf->SetXY(199, 98);
$pdf->Cell(28, 5, $Search['Weight_Pycnometer'], 0, 1, 'C');
$pdf->SetXY(199, 103);
$pdf->Cell(28, 7, $Search['Weight_Dry_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(199, 110);
$pdf->Cell(28, 12, $Search['Weight_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(199, 122);
$pdf->Cell(28, 9, $Search['Weight_Saturated_Surface_Dry_Soil_Air'], 0, 1, 'C');
$pdf->SetXY(199, 131);
$pdf->Cell(28, 5, $Search['Temperature_Sample'], 0, 1, 'C');
$pdf->SetXY(199, 137);
$pdf->Cell(28, 11, $Search['Weight_Pycnometer_Soil_Water'], 0, 1, 'C');
$pdf->SetXY(199, 148);
$pdf->Cell(28, 11, $Search['Calibration_Weight_Pycnometer_Desired_Temperature'], 0, 1, 'C');

// RESULTS
$pdf->SetXY(248, 79);
$pdf->Cell(21, 6, $Search['Specific_Gravity_OD'], 0, 1, 'C');
$pdf->SetXY(248, 85);
$pdf->Cell(21, 13, $Search['Specific_Gravity_SSD'], 0, 1, 'C');
$pdf->SetXY(248, 98);
$pdf->Cell(21, 12, $Search['Apparent_Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(248, 110);
$pdf->Cell(21, 21, $Search['Percent_Absortion'], 0, 1, 'C');

//Comments
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(24, 164);
$pdf->MultiCell(119, 4, $Search['Comments'], 0, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>