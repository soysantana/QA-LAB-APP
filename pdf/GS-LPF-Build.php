<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_lpf', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(320, 420));

$pdf->setSourceFile('template/PV-F-01704 Laboratory Sieve Grain Size Distribution for Low Permeability Fill-LPF.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

//Information for the essay
$pdf->SetXY(60, 32);
$pdf->Cell(30, 4, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(60, 48);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(60, 54);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(60, 60);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(60, 77);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(60, 84);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(60, 91);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(60, 99);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(145, 32);
$pdf->Cell(30, 6, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(145, 48);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(145, 54);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(145, 60);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(145, 77);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(145, 84);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(145, 91);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(145, 99);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(225, 32);
$pdf->Cell(30, 6, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(225, 48);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(225, 54);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(225, 60);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(225, 77);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(225, 84);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(225, 91);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(225, 99);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(83, 122);
$pdf->Cell(22, 7, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(83, 129);
$pdf->Cell(22, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(83, 135);
$pdf->Cell(22, 5, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(83, 140);
$pdf->Cell(22, 7, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(83, 147);
$pdf->Cell(22, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(83, 153);
$pdf->Cell(22, 7, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(83, 160);
$pdf->Cell(22, 7, $Search['Wt_Wash_Pan'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(188, 129);
$pdf->Cell(29, 6, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(217, 129);
$pdf->Cell(21, 6, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(238, 129);
$pdf->Cell(22, 6, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(260, 129);
$pdf->Cell(20, 6, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(281, 129);
$pdf->Cell(30, 6, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(188, 135);
$pdf->Cell(29, 5, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(217, 135);
$pdf->Cell(21, 5, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(238, 135);
$pdf->Cell(22, 5, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(260, 135);
$pdf->Cell(20, 5, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(281, 135);
$pdf->Cell(30, 5, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(188, 142);
$pdf->Cell(29, 4, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(217, 142);
$pdf->Cell(21, 4, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(238, 142);
$pdf->Cell(22, 4, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(260, 142);
$pdf->Cell(20, 4, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(281, 142);
$pdf->Cell(30, 5, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(188, 149);
$pdf->Cell(29, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(217, 149);
$pdf->Cell(21, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(238, 149);
$pdf->Cell(22, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(260, 149);
$pdf->Cell(20, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(281, 149);
$pdf->Cell(30, 5, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(188, 155);
$pdf->Cell(29, 4, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(217, 155);
$pdf->Cell(21, 4, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(238, 155);
$pdf->Cell(22, 4, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(260, 155);
$pdf->Cell(20, 4, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(281, 155);
$pdf->Cell(30, 5, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(188, 162);
$pdf->Cell(29, 4, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(217, 162);
$pdf->Cell(21, 4, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(238, 162);
$pdf->Cell(22, 4, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(260, 162);
$pdf->Cell(20, 4, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(281, 162);
$pdf->Cell(30, 5, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(188, 169);
$pdf->Cell(29, 4, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(217, 169);
$pdf->Cell(21, 4, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(238, 169);
$pdf->Cell(22, 4, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(260, 169);
$pdf->Cell(20, 4, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(281, 169);
$pdf->Cell(30, 5, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(188, 176);
$pdf->Cell(29, 4, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(217, 176);
$pdf->Cell(21, 4, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(238, 176);
$pdf->Cell(22, 4, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(260, 176);
$pdf->Cell(20, 4, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(281, 176);
$pdf->Cell(30, 5, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(188, 183);
$pdf->Cell(29, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(217, 183);
$pdf->Cell(21, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(238, 183);
$pdf->Cell(22, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(260, 183);
$pdf->Cell(20, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(281, 183);
$pdf->Cell(30, 5, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(188, 190);
$pdf->Cell(29, 4, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(217, 190);
$pdf->Cell(21, 4, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(238, 190);
$pdf->Cell(22, 4, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(260, 190);
$pdf->Cell(20, 4, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(281, 190);
$pdf->Cell(30, 5, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(188, 196);
$pdf->Cell(29, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(217, 196);
$pdf->Cell(21, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(238, 196);
$pdf->Cell(22, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(260, 196);
$pdf->Cell(20, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(281, 196);
$pdf->Cell(30, 5, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(188, 203);
$pdf->Cell(29, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(217, 203);
$pdf->Cell(21, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(238, 203);
$pdf->Cell(22, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(260, 203);
$pdf->Cell(20, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(281, 203);
$pdf->Cell(30, 5, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(188, 209);
$pdf->Cell(29, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(217, 209);
$pdf->Cell(21, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(238, 209);
$pdf->Cell(22, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(260, 209);
$pdf->Cell(20, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(281, 209);
$pdf->Cell(30, 5, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(188, 215);
$pdf->Cell(29, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(217, 215);
$pdf->Cell(21, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(188, 222);
$pdf->Cell(29, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(217, 222);
$pdf->Cell(21, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(238, 222);
$pdf->Cell(22, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(260, 222);
$pdf->Cell(20, 4, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter
$pdf->SetXY(83, 174);
$pdf->Cell(22, 7, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(83, 181);
$pdf->Cell(22, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(83, 188);
$pdf->Cell(22, 6, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(83, 194);
$pdf->Cell(22, 6, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(83, 200);
$pdf->Cell(22, 7, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(83, 207);
$pdf->Cell(22, 7, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(83, 214);
$pdf->Cell(22, 6, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(83, 220);
$pdf->Cell(22, 7, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(83, 227);
$pdf->Cell(22, 5, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(83, 232);
$pdf->Cell(22, 7, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(83, 239);
$pdf->Cell(22, 7, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS
$pdf->SetXY(27, 253);
$pdf->Cell(78, 6, "", 0, 1, 'C');
$pdf->SetXY(27, 260);
$pdf->Cell(78, 6, "", 0, 1, 'C');

// Comparison Information
$pdf->SetFont('Arial', '', 10);

// Specifications
$pdf->SetXY(56, 289);
$pdf->Cell(27, 4, $Search['Specs2'], 0, 1, 'C');
$pdf->SetXY(56, 294);
$pdf->Cell(27, 4, $Search['Specs5'], 0, 1, 'C');
$pdf->SetXY(56, 299);
$pdf->Cell(27, 4, $Search['Specs6'], 0, 1, 'C');
$pdf->SetXY(56, 303);
$pdf->Cell(27, 4, $Search['Specs7'], 0, 1, 'C');
$pdf->SetXY(56, 307);
$pdf->Cell(27, 5, $Search['Specs8'], 0, 1, 'C');
$pdf->SetXY(56, 312);
$pdf->Cell(27, 4, $Search['Specs13'], 0, 1, 'C');
// CQA % Pass
$pdf->SetXY(83, 289);
$pdf->Cell(22, 4, $Search['Specs2'], 0, 1, 'C');
$pdf->SetXY(83, 294);
$pdf->Cell(22, 4, $Search['Specs5'], 0, 1, 'C');
$pdf->SetXY(83, 299);
$pdf->Cell(22, 4, $Search['Specs6'], 0, 1, 'C');
$pdf->SetXY(83, 303);
$pdf->Cell(22, 4, $Search['Specs7'], 0, 1, 'C');
$pdf->SetXY(83, 307);
$pdf->Cell(22, 5, $Search['Specs8'], 0, 1, 'C');
$pdf->SetXY(83, 312);
$pdf->Cell(22, 4, $Search['Specs13'], 0, 1, 'C');

// Test Result Condition
$pdf->SetXY(83, 325);
$pdf->Cell(53, 4, '', 0, 1, 'C');

$pdf->SetXY(28, 345);
$pdf->MultiCell(105, 4, utf8_decode($Search['Comments']), 0, 'L');
/*
// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 30, 320, 230, 170, 'PNG');
unlink($tempFile);
*/
$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
