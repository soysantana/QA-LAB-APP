<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('brazilian', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

// Define manualmente el tamaño de página
$pdf->AddPage('P', array(330, 380));

// Importar una página de otro PDF
$pdf->setSourceFile('PV-F-80982_Laboratory Splitting Tensile Strength of Instact Rock Core Specime- Brazilian_Rev 3.pdf'); // Reemplaza 'ruta/al/archivo.pdf' con la ruta al PDF que deseas importar.
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(50, 41);
$pdf->Cell(21, 5, 'PVDJ SOIL LAB', 0, 1, 'C');
$pdf->SetXY(50, 46);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(50, 51);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(150, 40);
$pdf->Cell(21, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(150, 46);
$pdf->Cell(21, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(150, 51);
$pdf->Cell(21, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(240, 40);
$pdf->Cell(21, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(240, 46);
$pdf->Cell(21, 6, $Search['Extraction_Equipment'], 0, 1, 'C');
$pdf->SetXY(240, 51);
$pdf->Cell(21, 6, $Search['Cutter_Equipment'], 0, 1, 'C');
$pdf->SetXY(240, 57);
$pdf->Cell(21, 6, '', 0, 1, 'C');

$pdf->SetXY(50, 66);
$pdf->Cell(21, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(50, 72);
$pdf->Cell(21, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(50, 77);
$pdf->Cell(21, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(50, 82);
$pdf->Cell(21, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(150, 66);
$pdf->Cell(21, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(150, 72);
$pdf->Cell(21, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(150, 77);
$pdf->Cell(21, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(150, 82);
$pdf->Cell(21, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(240, 66);
$pdf->Cell(21, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(240, 72);
$pdf->Cell(21, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(240, 77);
$pdf->Cell(21, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(240, 82);
$pdf->Cell(21, 6, $Search['East'], 0, 1, 'C');

// test information
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(54, 113);
$pdf->Cell(33, 5, $Search['DcmNo1'], 0, 1, 'C');
$pdf->SetXY(87, 113);
$pdf->Cell(20, 5, $Search['TcmNo1'], 0, 1, 'C');
$pdf->SetXY(107, 113);
$pdf->Cell(30, 5, $Search['ReltdNo1'], 0, 1, 'C');
$pdf->SetXY(137, 113);
$pdf->Cell(29, 5, $Search['LoandNo1'], 0, 1, 'C');
$pdf->SetXY(166, 113);
$pdf->Cell(23, 5, $Search['TimeFaiNo1'], 0, 1, 'C');
$pdf->SetXY(189, 113);
$pdf->Cell(40, 5, $Search['MaxKnNo1'], 0, 1, 'C');
$pdf->SetXY(229, 113);
$pdf->Cell(36, 5, $Search['TensStrNo1'], 0, 1, 'C');
$pdf->SetXY(265, 113);
$pdf->Cell(35, 5, $Search['FailureNo1'], 0, 1, 'C');

$pdf->SetXY(54, 118);
$pdf->Cell(33, 5, $Search['DcmNo2'], 0, 1, 'C');
$pdf->SetXY(87, 118);
$pdf->Cell(20, 5, $Search['TcmNo2'], 0, 1, 'C');
$pdf->SetXY(107, 118);
$pdf->Cell(30, 5, $Search['ReltdNo2'], 0, 1, 'C');
$pdf->SetXY(137, 118);
$pdf->Cell(29, 5, $Search['LoandNo2'], 0, 1, 'C');
$pdf->SetXY(166, 118);
$pdf->Cell(23, 5, $Search['TimeFaiNo2'], 0, 1, 'C');
$pdf->SetXY(189, 118);
$pdf->Cell(40, 5, $Search['MaxKnNo2'], 0, 1, 'C');
$pdf->SetXY(229, 118);
$pdf->Cell(36, 5, $Search['TensStrNo2'], 0, 1, 'C');
$pdf->SetXY(265, 118);
$pdf->Cell(35, 5, $Search['FailureNo2'], 0, 1, 'C');

$pdf->SetXY(54, 123);
$pdf->Cell(33, 5, $Search['DcmNo3'], 0, 1, 'C');
$pdf->SetXY(87, 123);
$pdf->Cell(20, 5, $Search['TcmNo3'], 0, 1, 'C');
$pdf->SetXY(107, 123);
$pdf->Cell(30, 5, $Search['ReltdNo3'], 0, 1, 'C');
$pdf->SetXY(137, 123);
$pdf->Cell(29, 5, $Search['LoandNo3'], 0, 1, 'C');
$pdf->SetXY(166, 123);
$pdf->Cell(23, 5, $Search['TimeFaiNo3'], 0, 1, 'C');
$pdf->SetXY(189, 123);
$pdf->Cell(40, 5, $Search['MaxKnNo3'], 0, 1, 'C');
$pdf->SetXY(229, 123);
$pdf->Cell(36, 5, $Search['TensStrNo3'], 0, 1, 'C');
$pdf->SetXY(265, 123);
$pdf->Cell(35, 5, $Search['FailureNo3'], 0, 1, 'C');

$pdf->SetXY(54, 128);
$pdf->Cell(33, 5, $Search['DcmNo4'], 0, 1, 'C');
$pdf->SetXY(87, 128);
$pdf->Cell(20, 5, $Search['TcmNo4'], 0, 1, 'C');
$pdf->SetXY(107, 128);
$pdf->Cell(30, 5, $Search['ReltdNo4'], 0, 1, 'C');
$pdf->SetXY(137, 128);
$pdf->Cell(29, 5, $Search['LoandNo4'], 0, 1, 'C');
$pdf->SetXY(166, 128);
$pdf->Cell(23, 5, $Search['TimeFaiNo4'], 0, 1, 'C');
$pdf->SetXY(189, 128);
$pdf->Cell(40, 5, $Search['MaxKnNo4'], 0, 1, 'C');
$pdf->SetXY(229, 128);
$pdf->Cell(36, 5, $Search['TensStrNo4'], 0, 1, 'C');
$pdf->SetXY(265, 128);
$pdf->Cell(35, 5, $Search['FailureNo4'], 0, 1, 'C');

$pdf->SetXY(54, 133);
$pdf->Cell(33, 5, $Search['DcmNo5'], 0, 1, 'C');
$pdf->SetXY(87, 133);
$pdf->Cell(20, 5, $Search['TcmNo5'], 0, 1, 'C');
$pdf->SetXY(107, 133);
$pdf->Cell(30, 5, $Search['ReltdNo5'], 0, 1, 'C');
$pdf->SetXY(137, 133);
$pdf->Cell(29, 5, $Search['LoandNo5'], 0, 1, 'C');
$pdf->SetXY(166, 133);
$pdf->Cell(23, 5, $Search['TimeFaiNo5'], 0, 1, 'C');
$pdf->SetXY(189, 133);
$pdf->Cell(40, 5, $Search['MaxKnNo5'], 0, 1, 'C');
$pdf->SetXY(229, 133);
$pdf->Cell(36, 5, $Search['TensStrNo5'], 0, 1, 'C');
$pdf->SetXY(265, 133);
$pdf->Cell(35, 5, $Search['FailureNo5'], 0, 1, 'C');

$pdf->SetXY(54, 138);
$pdf->Cell(33, 5, $Search['DcmNo6'], 0, 1, 'C');
$pdf->SetXY(87, 138);
$pdf->Cell(20, 5, $Search['TcmNo6'], 0, 1, 'C');
$pdf->SetXY(107, 138);
$pdf->Cell(30, 5, $Search['ReltdNo6'], 0, 1, 'C');
$pdf->SetXY(137, 138);
$pdf->Cell(29, 5, $Search['LoandNo6'], 0, 1, 'C');
$pdf->SetXY(166, 138);
$pdf->Cell(23, 5, $Search['TimeFaiNo6'], 0, 1, 'C');
$pdf->SetXY(189, 138);
$pdf->Cell(40, 5, $Search['MaxKnNo6'], 0, 1, 'C');
$pdf->SetXY(229, 138);
$pdf->Cell(36, 5, $Search['TensStrNo6'], 0, 1, 'C');
$pdf->SetXY(265, 138);
$pdf->Cell(35, 5, $Search['FailureNo6'], 0, 1, 'C');

$pdf->SetXY(54, 143);
$pdf->Cell(33, 5, $Search['DcmNo7'], 0, 1, 'C');
$pdf->SetXY(87, 143);
$pdf->Cell(20, 5, $Search['TcmNo7'], 0, 1, 'C');
$pdf->SetXY(107, 143);
$pdf->Cell(30, 5, $Search['ReltdNo7'], 0, 1, 'C');
$pdf->SetXY(137, 143);
$pdf->Cell(29, 5, $Search['LoandNo7'], 0, 1, 'C');
$pdf->SetXY(166, 143);
$pdf->Cell(23, 5, $Search['TimeFaiNo7'], 0, 1, 'C');
$pdf->SetXY(189, 143);
$pdf->Cell(40, 5, $Search['MaxKnNo7'], 0, 1, 'C');
$pdf->SetXY(229, 143);
$pdf->Cell(36, 5, $Search['TensStrNo7'], 0, 1, 'C');
$pdf->SetXY(265, 143);
$pdf->Cell(35, 5, $Search['FailureNo7'], 0, 1, 'C');

$pdf->SetXY(54, 148);
$pdf->Cell(33, 5, $Search['DcmNo8'], 0, 1, 'C');
$pdf->SetXY(87, 148);
$pdf->Cell(20, 5, $Search['TcmNo8'], 0, 1, 'C');
$pdf->SetXY(107, 148);
$pdf->Cell(30, 5, $Search['ReltdNo8'], 0, 1, 'C');
$pdf->SetXY(137, 148);
$pdf->Cell(29, 5, $Search['LoandNo8'], 0, 1, 'C');
$pdf->SetXY(166, 148);
$pdf->Cell(23, 5, $Search['TimeFaiNo8'], 0, 1, 'C');
$pdf->SetXY(189, 148);
$pdf->Cell(40, 5, $Search['MaxKnNo8'], 0, 1, 'C');
$pdf->SetXY(229, 148);
$pdf->Cell(36, 5, $Search['TensStrNo8'], 0, 1, 'C');
$pdf->SetXY(265, 148);
$pdf->Cell(35, 5, $Search['FailureNo8'], 0, 1, 'C');

$pdf->SetXY(54, 153);
$pdf->Cell(33, 5, $Search['DcmNo9'], 0, 1, 'C');
$pdf->SetXY(87, 153);
$pdf->Cell(20, 5, $Search['TcmNo9'], 0, 1, 'C');
$pdf->SetXY(107, 153);
$pdf->Cell(30, 5, $Search['ReltdNo9'], 0, 1, 'C');
$pdf->SetXY(137, 153);
$pdf->Cell(29, 5, $Search['LoandNo9'], 0, 1, 'C');
$pdf->SetXY(166, 153);
$pdf->Cell(23, 5, $Search['TimeFaiNo9'], 0, 1, 'C');
$pdf->SetXY(189, 153);
$pdf->Cell(40, 5, $Search['MaxKnNo9'], 0, 1, 'C');
$pdf->SetXY(229, 153);
$pdf->Cell(36, 5, $Search['TensStrNo9'], 0, 1, 'C');
$pdf->SetXY(265, 153);
$pdf->Cell(35, 5, $Search['FailureNo9'], 0, 1, 'C');

$pdf->SetXY(54, 158);
$pdf->Cell(33, 5, $Search['DcmNo10'], 0, 1, 'C');
$pdf->SetXY(87, 158);
$pdf->Cell(20, 5, $Search['TcmNo10'], 0, 1, 'C');
$pdf->SetXY(107, 158);
$pdf->Cell(30, 5, $Search['ReltdNo10'], 0, 1, 'C');
$pdf->SetXY(137, 158);
$pdf->Cell(29, 5, $Search['LoandNo10'], 0, 1, 'C');
$pdf->SetXY(166, 158);
$pdf->Cell(23, 5, $Search['TimeFaiNo10'], 0, 1, 'C');
$pdf->SetXY(189, 158);
$pdf->Cell(40, 5, $Search['MaxKnNo10'], 0, 1, 'C');
$pdf->SetXY(229, 158);
$pdf->Cell(36, 5, $Search['TensStrNo10'], 0, 1, 'C');
$pdf->SetXY(265, 158);
$pdf->Cell(35, 5, $Search['FailureNo10'], 0, 1, 'C');

$pdf->SetXY(54, 163);
$pdf->Cell(33, 9, $Search['DcmNoAvge'], 0, 1, 'C');
$pdf->SetXY(87, 163);
$pdf->Cell(20, 9, $Search['TcmNoAvge'], 0, 1, 'C');
$pdf->SetXY(107, 163);
$pdf->Cell(30, 9, $Search['ReltdNoAvge'], 0, 1, 'C');
$pdf->SetXY(137, 163);
$pdf->Cell(29, 9, $Search['LoandNoAvge'], 0, 1, 'C');
$pdf->SetXY(166, 163);
$pdf->Cell(23, 9, $Search['TimeFaiNoAvge'], 0, 1, 'C');
$pdf->SetXY(189, 163);
$pdf->Cell(40, 9, $Search['MaxKnNoAvge'], 0, 1, 'C');
$pdf->SetXY(229, 163);
$pdf->Cell(36, 9, $Search['TensStrNoAvge'], 0, 1, 'C');

// Comments
$pdf->SetXY(22, 287);
$pdf->Cell(278, 55, $Search['Comments'], 1, 1, 'C');


// Función para determinar la extensión de la imagen (jpg o png)
function getImageExtension($imageData) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_buffer($finfo, $imageData);
    finfo_close($finfo);

    switch ($mimeType) {
        case 'image/jpeg':
            return 'jpg';
        case 'image/png':
            return 'png';
        default:
            return 'jpg'; // Por defecto a jpg si el tipo no es reconocido
    }
}

// Pictures BTS - Specimen Before Test
$imageData = $Search['SpecimenBefore'];
$extension = getImageExtension($imageData);
$imageFileName1 = "temp_image1.$extension"; // Cambiar el nombre del archivo temporal con la extensión adecuada
file_put_contents($imageFileName1, $imageData);
$pdf->SetXY(25, 180);
$cellWidth = 109;
$cellHeight = 80;
$imagePath1 = "$imageFileName1";
$pdf->Image($imagePath1, $pdf->GetX(), $pdf->GetY(), $cellWidth, $cellHeight);
$pdf->Cell($cellWidth, $cellHeight, "", 0, 1, 'C');
unlink($imageFileName1); // Eliminar el archivo temporal de la primera imagen

// Pictures BTS - Specimen After Test
$imageData = $Search['SpecimenAfter'];
$extension = getImageExtension($imageData);
$imageFileName2 = "temp_image2.$extension"; // Cambiar el nombre del archivo temporal con la extensión adecuada
file_put_contents($imageFileName2, $imageData);
$pdf->SetXY(176, 180);
$cellWidth = 109;
$cellHeight = 80;
$imagePath2 = "$imageFileName2";
$pdf->Image($imagePath2, $pdf->GetX(), $pdf->GetY(), $cellWidth, $cellHeight);
$pdf->Cell($cellWidth, $cellHeight, "", 0, 1, 'C');
unlink($imageFileName2); // Eliminar el archivo temporal de la segunda imagen



$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>