<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}
    function Footer() {}
}

// Leer JSON recibido
$input = json_decode(file_get_contents('php://input'), true);
$base64Image = $input['imagen'] ?? null;

$Search = find_by_id('grain_size_full', $_GET['id']);

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', array(470, 390));
$pdf->setSourceFile('template/PV-F-01727 Laboratory Sieve Grain Size Distribution for Upstream Facing Fill-UFF.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

//Information for the essay
$pdf->SetFont('Arial', 'B', 10);

$pdf->SetXY(100, 51);
$pdf->Cell(30, 4, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(100, 65);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(100, 70);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(100, 76);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(100, 89);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(100, 94);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(100, 100);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(100, 105);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(215, 51);
$pdf->Cell(30, 6, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(215, 65);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(215, 70);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(215, 76);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(215, 89);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(215, 94);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(215, 100);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(215, 105);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(320, 51);
$pdf->Cell(30, 6, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(320, 65);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(320, 70);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(320, 76);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(320, 89);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(320, 94);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(320, 100);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(320, 105);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

// Testing Information
$pdf->SetXY(122, 124);
$pdf->Cell(37, 8, number_format($Search['TotalPesoSecoSucio'], 1), 0, 1, 'C');
$pdf->SetXY(122, 132);
$pdf->Cell(37, 7, $Search['More3p'], 0, 1, 'C');
$pdf->SetXY(122, 139);
$pdf->Cell(37, 7, $Search['Lees3P'], 0, 1, 'C');
$pdf->SetXY(122, 147);
$pdf->Cell(37, 7, $Search['MoistureContentAvg'] . '%', 0, 1, 'C');
$pdf->SetXY(122, 154);
$pdf->Cell(37, 8, $Search['TotalDryWtSampleLess3g'], 0, 1, 'C');
$pdf->SetXY(122, 162);
$pdf->Cell(37, 7, $Search['TotalPesoLavado'], 0, 1, 'C');
$pdf->SetXY(122, 169);
$pdf->Cell(37, 7, $Search['PerdidaPorLavado'], 0, 1, 'C');
$pdf->SetXY(122, 177);
$pdf->Cell(37, 7, $Search['ConvertionFactor'], 0, 1, 'C');
$pdf->SetXY(122, 184);
$pdf->Cell(37, 7, $Search['PesoSecoSucio'], 0, 1, 'C');

// Summary Parameter
$pdf->SetXY(122, 207);
$pdf->Cell(37, 6, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(122, 214);
$pdf->Cell(37, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(122, 220);
$pdf->Cell(37, 7, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(122, 227);
$pdf->Cell(37, 6, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(122, 233);
$pdf->Cell(37, 6, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(122, 240);
$pdf->Cell(37, 6, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(122, 246);
$pdf->Cell(37, 7, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(122, 253);
$pdf->Cell(37, 6, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(122, 260);
$pdf->Cell(37, 6, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(122, 266);
$pdf->Cell(37, 8, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(122, 274);
$pdf->Cell(37, 6, $Search['Cu'], 0, 1, 'C');

// Classification as per ASTM-D2487
$pdf->SetXY(30, 286);
$pdf->Cell(92, 7, $Search['ClassificationUSCS1'], 0, 1, 'C');
$pdf->SetXY(30, 293);
$pdf->Cell(92, 6, '', 0, 1, 'C');

// Grain Size Distribution
$wtRetArray = explode(',', $Search['WtRet']);
$x1 = 245;

// Posiciones Y personalizadas por índice (compartidas)
$customY = [
    0 => 132,
    1 => 139,
    2 => 147,
    3 => 154,
    4 => 162,
    5 => 169,
    6 => 177,
    7 => 184,
    8 => 192,
    9 => 199,
    10 => 206,
    11 => 213,
    12 => 220,
    13 => 226,
    14 => 233,
    15 => 239,
    16 => 246,
    17 => 253,
    18 => 259
];

foreach ($wtRetArray as $index => $value) {
    if (isset($customY[$index])) {
        $y = $customY[$index];
        $pdf->SetXY($x1, $y);
        $pdf->Cell(28, 7, trim($value), 0, 1, 'C');
    }
}

// Retained (Ret)
$retArray = explode(',', $Search['Ret']);
$x2 = 273;

foreach ($retArray as $index => $value) {
    if (isset($customY[$index])) {
        $y = $customY[$index];
        $pdf->SetXY($x2, $y);
        $pdf->Cell(25, 7, trim($value), 0, 1, 'C');
    }
}

// Cum % Ret
$CumRetArray = explode(',', $Search['CumRet']);
$x3 = 298;

foreach ($CumRetArray as $index => $value) {
    if (isset($customY[$index])) {
        $y = $customY[$index];
        $pdf->SetXY($x3, $y);
        $pdf->Cell(25, 7, trim($value), 0, 1, 'C');
    }
}

// Porcentaje Pasante
$PassArray = explode(',', $Search['Pass']);
$x4 = 319;

foreach ($PassArray as $index => $value) {
    if (isset($customY[$index])) {
        $y = $customY[$index];
        $pdf->SetXY($x4, $y);
        $pdf->Cell(25, 7, trim($value), 0, 1, 'C');
    }
}


// Specs del material
//$pdf->SetXY(341, 132);
//$pdf->Cell(21, 7, $Search['More3p'], 1, 1, 'C');


$pdf->SetXY(245, 266);
$pdf->Cell(28, 7, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(245, 273);
$pdf->Cell(28, 7, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(273, 273);
$pdf->Cell(28, 7, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(298, 273);
$pdf->Cell(24, 7, $Search['TotalCumRet'], 1, 1, 'C');
$pdf->SetXY(321, 273);
$pdf->Cell(20, 7, $Search['TotalPass'], 0, 1, 'C');

$pdf->SetXY(30, 385);
$pdf->MultiCell(105, 4, utf8_decode($Search['Comments']), 0, 'L');
$pdf->SetXY(160, 388);
$pdf->MultiCell(105, 4, utf8_decode($Search['FieldComment']), 0, 'L');


// Insertar la imagen base64 en el PDF
if ($base64Image) {
    $base64String = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
    $imageData = base64_decode($base64String);

    $tmpFile = tempnam(sys_get_temp_dir(), 'img') . '.png';
    file_put_contents($tmpFile, $imageData);

    // Ajusta X, Y, ancho, alto según tu layout
    $pdf->Image($tmpFile, 200, 280, 150, 95);

    unlink($tmpFile);
}

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
