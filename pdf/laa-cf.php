<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('los_angeles_abrasion_coarse_filter', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

// Define manualmente el tamaño de página
$pdf->AddPage('P', array(480, 380));

// Importar una página de otro PDF
$pdf->setSourceFile('LAA-CF.pdf'); // Reemplaza 'ruta/al/archivo.pdf' con la ruta al PDF que deseas importar.
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(80, 41);
$pdf->Cell(40, 5, 'PVDJ SOIL LAB', 0, 1, 'C');
$pdf->SetXY(80, 46);
$pdf->Cell(40, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(80, 51);
$pdf->Cell(40, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(190, 40);
$pdf->Cell(40, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(190, 46);
$pdf->Cell(40, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(190, 52);
$pdf->Cell(40, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(280, 38);
$pdf->Cell(40, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(280, 45);
$pdf->Cell(40, 6, '', 0, 1, 'C');
$pdf->SetXY(280, 51);
$pdf->Cell(40, 6, '', 0, 1, 'C');

$pdf->SetXY(80, 70);
$pdf->Cell(40, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(80, 78);
$pdf->Cell(40, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(80, 84);
$pdf->Cell(40, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(80, 90);
$pdf->Cell(40, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(190, 69);
$pdf->Cell(40, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(190, 77);
$pdf->Cell(40, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(190, 84);
$pdf->Cell(40, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(190, 91);
$pdf->Cell(40, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(280, 69);
$pdf->Cell(40, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(280, 77);
$pdf->Cell(40, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(280, 84);
$pdf->Cell(40, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(280, 91);
$pdf->Cell(40, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// PLT Device Values
$pdf->SetXY(85, 231);
$pdf->Cell(71, 6, $Search['Grading'], 0, 1, 'C');
$pdf->SetXY(85, 243);
$pdf->Cell(71, 11, $Search['Weight_Spheres'], 0, 1, 'C');
$pdf->SetXY(85, 254);
$pdf->Cell(71, 6, $Search['Revolutions'], 0, 1, 'C');

// Testing Information
$pdf->SetXY(127, 278);
$pdf->Cell(29, 8, $Search['Initial_Weight'], 0, 1, 'C');
$pdf->SetXY(127, 286);
$pdf->Cell(29, 9, $Search['Final_Weight'], 0, 1, 'C');
$pdf->SetXY(127, 295);
$pdf->Cell(29, 10, $Search['Weight_Loss'], 0, 1, 'C');
$pdf->SetXY(127, 305);
$pdf->Cell(29, 9, $Search['Weight_Loss_Porce'], 0, 1, 'C');

// Comentario
$pdf->SetXY(49, 327);
$pdf->Cell(192, 21, $Search['Comments'], 0, 1, 'C');


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>