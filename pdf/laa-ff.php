<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('los_angeles_abrasion_coarse_aggregate', $_GET['id']);

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
$pdf->setSourceFile('LAA-FF.pdf'); // Reemplaza 'ruta/al/archivo.pdf' con la ruta al PDF que deseas importar.
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(80, 65);
$pdf->Cell(40, 5, 'PVDJ SOIL LAB', 0, 1, 'C');
$pdf->SetXY(80, 72);
$pdf->Cell(40, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(80, 79);
$pdf->Cell(40, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(190, 65);
$pdf->Cell(40, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(190, 72);
$pdf->Cell(40, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(190, 79);
$pdf->Cell(40, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(280, 65);
$pdf->Cell(40, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(280, 72);
$pdf->Cell(40, 6, '', 0, 1, 'C');
$pdf->SetXY(280, 79);
$pdf->Cell(40, 6, '', 0, 1, 'C');

$pdf->SetXY(80, 94);
$pdf->Cell(40, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(80, 101);
$pdf->Cell(40, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(80, 108);
$pdf->Cell(40, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(80, 115);
$pdf->Cell(40, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(190, 94);
$pdf->Cell(40, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(190, 101);
$pdf->Cell(40, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(190, 108);
$pdf->Cell(40, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(190, 115);
$pdf->Cell(40, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(280, 94);
$pdf->Cell(40, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(280, 101);
$pdf->Cell(40, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(280, 108);
$pdf->Cell(40, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(280, 115);
$pdf->Cell(40, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// PLT Device Values
$pdf->SetXY(122, 207);
$pdf->Cell(102, 6, $Search['Grading'], 0, 1, 'C');

// Testing Information
$pdf->SetXY(122, 219);
$pdf->Cell(102, 5, $Search['Initial_Weight'], 0, 1, 'C');
$pdf->SetXY(122, 225);
$pdf->Cell(102, 5, $Search['Final_Weight'], 0, 1, 'C');
$pdf->SetXY(122, 231);
$pdf->Cell(102, 5, $Search['Weight_Loss'], 0, 1, 'C');
$pdf->SetXY(122, 236);
$pdf->Cell(102, 5, $Search['Weight_Loss_Porce'], 0, 1, 'C');

// Comentario
$pdf->SetXY(46, 255);
$pdf->Cell(192, 21, $Search['Comments'], 0, 1, 'C');


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>