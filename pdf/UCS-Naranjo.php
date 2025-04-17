<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('unixial_compressive', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

// Define manualmente el tamaño de página
$pdf->AddPage('P', array(470, 430));

// Importar una página de otro PDF
$pdf->setSourceFile('template/PV-F-83440 Laboratory Compressive Strength Test - Core Specimens_Rev 5.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(40, 44);
$pdf->Cell(40, 5, 'PVDJ SOIL LAB', 0, 1, 'C');
$pdf->SetXY(40, 49);
$pdf->Cell(40, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(40, 54);
$pdf->Cell(40, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(140, 43);
$pdf->Cell(40, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(140, 49);
$pdf->Cell(40, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(140, 55);
$pdf->Cell(40, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(245, 42);
$pdf->Cell(40, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(245, 48);
$pdf->Cell(40, 6, $Search['Extraction_Equipment'], 0, 1, 'C');
$pdf->SetXY(245, 54);
$pdf->Cell(40, 6, $Search['Cutter_Equipment'], 0, 1, 'C');
$pdf->SetXY(245, 60);
$pdf->Cell(40, 6, $Search['Test_Device'], 0, 1, 'C');

$pdf->SetXY(40, 70);
$pdf->Cell(40, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(40, 76);
$pdf->Cell(40, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(40, 81);
$pdf->Cell(40, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(40, 87);
$pdf->Cell(40, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(140, 70);
$pdf->Cell(40, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(140, 76);
$pdf->Cell(40, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(140, 81);
$pdf->Cell(40, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(140, 87);
$pdf->Cell(40, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(245, 70);
$pdf->Cell(40, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(245, 76);
$pdf->Cell(40, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(245, 81);
$pdf->Cell(40, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(245, 87);
$pdf->Cell(40, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

//Testing Information
$pdf->SetXY(13, 118);
$pdf->Cell(33, 12, $Search['DimensionD'], 0, 1, 'C');
$pdf->SetXY(46, 118);
$pdf->Cell(38, 12, $Search['DimensionH'], 0, 1, 'C');
$pdf->SetXY(84, 118);
$pdf->Cell(18, 12, $Search['RelationHD'], 0, 1, 'C');
$pdf->SetXY(102, 118);
$pdf->Cell(34, 12, $Search['AreaM2'], 0, 1, 'C');
$pdf->SetXY(136, 118);
$pdf->Cell(40, 12, $Search['VolM3'], 0, 1, 'C');

$pdf->SetXY(12, 151);
$pdf->Cell(34, 11, $Search['WeightKg'], 0, 1, 'C');
$pdf->SetXY(46, 151);
$pdf->Cell(38, 11, $Search['UnitWeigKgm3'], 0, 1, 'C');
$pdf->SetXY(84, 151);
$pdf->Cell(18, 11, $Search['FailLoadKn'], 0, 1, 'C');
$pdf->SetXY(102, 151);
$pdf->Cell(34, 11, $Search['TestTimingS'], 0, 1, 'C');
$pdf->SetXY(136, 151);
$pdf->Cell(40, 11, $Search['LoadpMpas'], 0, 1, 'C');
$pdf->SetXY(12, 189);
$pdf->Cell(34, 13, $Search['UCSMpa'], 0, 1, 'C');
$pdf->SetXY(46, 189);
$pdf->Cell(38, 13, $Search['FailureType'], 0, 1, 'C');

$pdf->SetXY(12, 342);
$pdf->MultiCell(353, 10, $Search['Comments'], 0, 'C');

// Función para determinar la extensión de la imagen (jpg o png)
function getImageExtension($imageData)
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_buffer($finfo, $imageData);
    finfo_close($finfo);

    switch ($mimeType) {
        case 'image/jpeg':
            return 'jpg';
        case 'image/png':
            return 'png';
        default:
            return 'jpg';
    }
}

// PIC UCS - SpecimenBefore
$imageData = $Search['SpecimenBefore'];
$extension = getImageExtension($imageData);
$imageFileName1 = "temp_image1.$extension";
file_put_contents($imageFileName1, $imageData);
$pdf->SetXY(35, 220);
$cellWidth = 105;
$cellHeight = 90;
$imagePath1 = "$imageFileName1";
$pdf->Image($imagePath1, $pdf->GetX(), $pdf->GetY(), $cellWidth, $cellHeight);
$pdf->Cell($cellWidth, $cellHeight, "", 1, 1, 'C');
unlink($imageFileName1);

// PIC UCS - SpecimenAfter
$imageData = $Search['SpecimenAfter'];
$extension = getImageExtension($imageData);
$imageFileName2 = "temp_image2.$extension";
file_put_contents($imageFileName2, $imageData);
$pdf->SetXY(240, 220);
$cellWidth = 105;
$cellHeight = 90;
$imagePath2 = "$imageFileName2";
$pdf->Image($imagePath2, $pdf->GetX(), $pdf->GetY(), $cellWidth, $cellHeight);
$pdf->Cell($cellWidth, $cellHeight, "", 1, 1, 'C');
unlink($imageFileName2);

// PIC UCS - Graphic
$imageData = $Search['Graphic'];
$extension = getImageExtension($imageData);
$imageFileName3 = "temp_image3.$extension";
file_put_contents($imageFileName3, $imageData);
$pdf->SetXY(240, 106);
$cellWidth = 105;
$cellHeight = 90;
$imagePath3 = "$imageFileName3";
$pdf->Image($imagePath3, $pdf->GetX(), $pdf->GetY(), $cellWidth, $cellHeight);
$pdf->Cell($cellWidth, $cellHeight, "", 1, 1, 'C');
unlink($imageFileName3);



$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
