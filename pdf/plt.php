<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('point_load', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

// Define manualmente el tamaño de página
$pdf->AddPage('P', array(360, 270));

// Importar una página de otro PDF
$pdf->setSourceFile('PV-F-01712_PLT_Rev. 2.pdf'); // Reemplaza 'ruta/al/archivo.pdf' con la ruta al PDF que deseas importar.
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(38, 43);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(38, 51);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(148, 34);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(148, 43);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(148, 51);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(237, 31);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(237, 40);
$pdf->Cell(30, 6, $Search['Extraction_Equipment'], 0, 1, 'C');
$pdf->SetXY(237, 48);
$pdf->Cell(30, 6, $Search['Cutter_Equipment'], 0, 1, 'C');
$pdf->SetXY(237, 56);
$pdf->Cell(30, 6, '', 0, 1, 'C');

$pdf->SetXY(38, 69);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(38, 76);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(38, 82);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(38, 88);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(148, 68);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(148, 75);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(148, 83);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(148, 90);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(237, 68);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(237, 75);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(237, 83);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(237, 90);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// PLT Device Values
$pdf->SetXY(107, 104);
$pdf->Cell(26, 6, $Search['JackPiston'], 0, 1, 'C');
$pdf->SetXY(107, 109);
$pdf->Cell(26, 6, $Search['K1assumed'], 0, 1, 'C');
$pdf->SetXY(107, 114);
$pdf->Cell(26, 6, $Search['K2assumed'], 0, 1, 'C');

// Testing Information
$pdf->SetXY(9, 145);
$pdf->Cell(37, 12, $Search['TypeABCD'], 0, 1, 'C');
$pdf->SetXY(46, 145);
$pdf->Cell(35, 12, $Search['DimensionL'], 0, 1, 'C');
$pdf->SetXY(81, 145);
$pdf->Cell(26, 12, $Search['DimensionD'], 0, 1, 'C');
$pdf->SetXY(107, 145);
$pdf->Cell(26, 12, $Search['PlattensSeparation'], 0, 1, 'C');
$pdf->SetXY(133, 145);
$pdf->Cell(31, 12, utf8_decode($Search['LoadDirection']), 0, 1, 'C');
$pdf->SetXY(164, 145);
$pdf->Cell(32, 12, $Search['GaugeReading'], 0, 1, 'C');
$pdf->SetXY(196, 145);
$pdf->Cell(28, 12, $Search['FailureLoad'], 0, 1, 'C');

$pdf->SetXY(9, 173);
$pdf->Cell(37, 12, $Search['Demm'], 0, 1, 'C');
$pdf->SetXY(46, 173);
$pdf->Cell(35, 12, $Search['IsMpa'], 0, 1, 'C');
$pdf->SetXY(81, 173);
$pdf->Cell(26, 12, $Search['F'], 0, 1, 'C');
$pdf->SetXY(107, 173);
$pdf->Cell(26, 12, $Search['Is50'], 0, 1, 'C');
$pdf->SetXY(133, 173);
$pdf->Cell(31, 12, $Search['UCSK1Mpa'], 0, 1, 'C');
$pdf->SetXY(164, 173);
$pdf->Cell(32, 12, $Search['UCSK2Mpa'], 0, 1, 'C');
$pdf->SetXY(196, 173);
$pdf->Cell(28, 12, $Search['Classification'], 0, 1, 'C');

// Comentario
$pdf->SetXY(9, 287);
$pdf->Cell(215, 25, $Search['Comments'], 0, 1, 'C');

// PIC PLT 
$imageData = $Search['SpecimenBefore'];
$imageFileName1 = 'temp_image1.jpg'; // Cambiar el nombre del archivo temporal
// Guardar los datos de la imagen en un archivo temporal
file_put_contents($imageFileName1, $imageData);
$pdf->SetXY(10, 200);
$cellWidth = 110;
$cellHeight = 85;
$Width = 100;
$Height = 80;
$imagePath1 = "$imageFileName1";
$pdf->Image($imagePath1, $pdf->GetX(), $pdf->GetY(), $Width, $Height);
$pdf->SetXY(5, 197);
$pdf->Cell($cellWidth, $cellHeight, "", 1, 1, 'C');
// Eliminar el archivo temporal de la primera imagen
unlink($imageFileName1);

$imageData = $Search['SpecimenAfter'];
$imageFileName2 = 'temp_image2.jpg'; // Cambiar el nombre del archivo temporal
// Guardar los datos de la imagen en un archivo temporal
file_put_contents($imageFileName2, $imageData);
$pdf->SetXY(125, 200);
$cellWidth = 110;
$cellHeight = 85;
$Width = 100;
$Height = 80;
$imagePath1 = "$imageFileName2";
$pdf->Image($imagePath1, $pdf->GetX(), $pdf->GetY(), $Width, $Height);
$pdf->SetXY(120, 197);
$pdf->Cell($cellWidth, $cellHeight, "", 1, 1, 'C');
// Eliminar el archivo temporal de la primera imagen
unlink($imageFileName2);


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>