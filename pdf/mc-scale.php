<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('moisture_scale', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(300, 220));

$pdf->setSourceFile('PV-F-01714_Laboratory Moisture Content with Scale_Rev 1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(52, 38);
$pdf->Cell(30, 1, 'PVDJ SOIL LAB', 0, 1, 'L');
$pdf->SetXY(52, 43);
$pdf->Cell(30, 1, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(52, 49);
$pdf->Cell(30, 1, $Search['Sample_By'], 0, 1, 'L');

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
$pdf->Cell(81, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(120, 75);
$pdf->Cell(81, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(120, 81);
$pdf->Cell(81, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(120, 87);
$pdf->Cell(81, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(120, 93);
$pdf->Cell(81, 6, $Search['Material_Type'], 0, 1, 'C');
$pdf->SetXY(120, 99);
$pdf->Cell(81, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(120, 104);
$pdf->Cell(81, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(120, 109);
$pdf->Cell(81, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(120, 115);
$pdf->Cell(81, 6, $Search['East'], 0, 1, 'C');
$pdf->SetXY(120, 120);
$pdf->Cell(81, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(120, 147);
$pdf->Cell(81, 1, $Search['Tare_Name'], 0, 1, 'C');
$pdf->SetXY(120, 186);
$pdf->Cell(81, 1, $Search['Moisture_Content_Porce'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(21, 210);
$pdf->Cell(170, 0, $Search['Comments'], 0, 1, 'L');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>