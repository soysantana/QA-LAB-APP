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
$Chart = $input['GrainSizeRockGraph'] ?? null;

$Search = find_by_id('grain_size_full', $_GET['id']);

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('P', array(470, 390));
$pdf->setSourceFile('template/PV-F-02118 Laboratory Sieve Grain Size Distribution for Fine Rock Fill-FRF.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

//Information for the essay
$pdf->SetFont('Arial', 'B', 10);

$pdf->SetXY(100, 46);
$pdf->Cell(30, 4, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(100, 60);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(100, 66);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(100, 73);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(100, 85);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(100, 91);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(100, 96);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(100, 102);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(215, 46);
$pdf->Cell(30, 6, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(215, 60);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(215, 66);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(215, 73);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(215, 85);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(215, 91);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(215, 96);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(215, 102);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(320, 46);
$pdf->Cell(30, 6, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(320, 60);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(320, 66);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(320, 73);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(320, 85);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(320, 91);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(320, 96);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(320, 102);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(122, 121);
$pdf->Cell(37, 8, $Search['TotalPesoSecoSucio'], 0, 1, 'R');
$pdf->SetXY(122, 129);
$pdf->Cell(37, 7, $Search['More3p'], 0, 1, 'R');
$pdf->SetXY(122, 137);
$pdf->Cell(37, 7, $Search['Lees3P'], 0, 1, 'R');
$pdf->SetXY(122, 144);
$pdf->Cell(37, 7, $Search['MoistureContentAvg'] . '%', 0, 1, 'R');
$pdf->SetXY(122, 151);
$pdf->Cell(37, 8, $Search['TotalDryWtSampleLess3g'], 0, 1, 'R');
$pdf->SetXY(122, 159);
$pdf->Cell(37, 7, $Search['TotalPesoLavado'], 0, 1, 'R');
$pdf->SetXY(122, 167);
$pdf->Cell(37, 7, $Search['PerdidaPorLavado'], 0, 1, 'R');
$pdf->SetXY(122, 174);
$pdf->Cell(37, 7, $Search['ConvertionFactor'], 0, 1, 'R');
$pdf->SetXY(122, 181);
$pdf->Cell(37, 7, $Search['PesoSecoSucio'], 0, 1, 'R');

// Summary Parameter
$pdf->SetXY(122, 205);
$pdf->Cell(37, 6, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(122, 211);
$pdf->Cell(37, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(122, 217);
$pdf->Cell(37, 7, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(122, 224);
$pdf->Cell(37, 6, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(122, 230);
$pdf->Cell(37, 6, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(122, 237);
$pdf->Cell(37, 6, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(122, 243);
$pdf->Cell(37, 7, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(122, 251);
$pdf->Cell(37, 6, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(122, 257);
$pdf->Cell(37, 6, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(122, 263);
$pdf->Cell(37, 8, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(122, 270);
$pdf->Cell(37, 6, $Search['Cu'], 0, 1, 'C');

// Grain Size Distribution
$wtRetArray = explode(',', $Search['WtRet']);
$x1 = 245;

// Posiciones Y personalizadas por índice (compartidas)
$customY = [
    0 => 129,
    1 => 136,
    2 => 144,
    3 => 151,
    4 => 158,
    5 => 166,
    6 => 174,
    7 => 181,
    8 => 188,
    9 => 196,
    10 => 204,
    11 => 210,
    12 => 216,
    13 => 223,
    14 => 230,
    15 => 236,
    16 => 243,
    17 => 250,
    18 => 257
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

$pdf->SetXY(245, 263);
$pdf->Cell(28, 7, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(245, 270);
$pdf->Cell(28, 7, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(273, 270);
$pdf->Cell(28, 7, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(298, 270);
$pdf->Cell(24, 7, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(321, 270);
$pdf->Cell(20, 7, $Search['TotalPass'], 0, 1, 'C');

// Classification as per ASTM-D2487
$pdf->SetXY(30, 284);
$pdf->MultiCell(92, 6, $Search['Classification1'] . "\n" . $Search['Classification2'], 0, 'C');

// Comments and Observation
$pdf->SetXY(30, 385);
$pdf->MultiCell(105, 4, utf8_decode($Search['Comments']), 0, 'L');
$pdf->SetXY(160, 388);
$pdf->MultiCell(105, 4, utf8_decode($Search['FieldComment']), 0, 'L');

// Function to insert base64 image into PDF
function insertarImagenBase64($pdf, $base64Str, $x, $y, $w, $h)
{
    if ($base64Str) {
        $base64Str = preg_replace('#^data:image/\w+;base64,#i', '', $base64Str);
        $imageData = base64_decode($base64Str);
        $tmpFile = tempnam(sys_get_temp_dir(), 'img') . '.png';
        file_put_contents($tmpFile, $imageData);
        $pdf->Image($tmpFile, $x, $y, $w, $h);
        unlink($tmpFile);
    }
}
insertarImagenBase64($pdf, $Chart, 190, 278, 0, 100); // ajusta X, Y, ancho, alto

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'GS' . '-' . $Search['Material_Type'] . '.pdf', 'I');
