<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_general', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(440, 360));

$pdf->setSourceFile('PV-F-81247_Laboratory Sieve Grain Size for General Soil_Rev 5.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(70, 46);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(70, 52);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(185, 39);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(185, 46);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(185, 52);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(270, 38);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(270, 45);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(270, 51);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');

$pdf->SetXY(70, 69);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(70, 75);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(70, 82);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(70, 90);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(185, 69);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(185, 75);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(185, 83);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(185, 90);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(270, 69);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(270, 75);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(270, 83);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(270, 90);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(120, 110);
$pdf->Cell(28, 8, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(120, 117);
$pdf->Cell(28, 8, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(120, 124);
$pdf->Cell(28, 7, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(120, 131);
$pdf->Cell(28, 7, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(120, 138);
$pdf->Cell(28, 7, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(120, 145);
$pdf->Cell(28, 7, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(120, 152);
$pdf->Cell(28, 7, $Search['Wt_Wash_Pan'], 0, 1, 'C');

//Grain Size Distribution
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(235, 117);
$pdf->Cell(17, 7, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(253, 117);
$pdf->Cell(21, 7, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(275, 117);
$pdf->Cell(21, 7, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(296, 117);
$pdf->Cell(18, 7, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(314, 117);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 124);
$pdf->Cell(17, 7, "", 0, 1, 'C');
$pdf->SetXY(253, 124);
$pdf->Cell(21, 7, "0.00", 0, 1, 'C');
$pdf->SetXY(275, 124);
$pdf->Cell(21, 7, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(296, 124);
$pdf->Cell(18, 7, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(314, 124);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 131);
$pdf->Cell(17, 7, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(253, 131);
$pdf->Cell(21, 7, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(275, 131);
$pdf->Cell(21, 7, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(296, 131);
$pdf->Cell(18, 7, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(314, 131);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 138);
$pdf->Cell(17, 7, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(253, 138);
$pdf->Cell(21, 7, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(275, 138);
$pdf->Cell(21, 7, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(296, 138);
$pdf->Cell(18, 7, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(314, 138);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 145);
$pdf->Cell(17, 7, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(253, 145);
$pdf->Cell(21, 7, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(275, 145);
$pdf->Cell(21, 7, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(296, 145);
$pdf->Cell(18, 7, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(314, 145);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 152);
$pdf->Cell(17, 7, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(253, 152);
$pdf->Cell(21, 7, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(275, 152);
$pdf->Cell(21, 7, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(296, 152);
$pdf->Cell(18, 7, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(314, 152);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 158.5);
$pdf->Cell(17, 7, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(253, 158.5);
$pdf->Cell(21, 7, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(275, 158.5);
$pdf->Cell(21, 7, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(296, 158.5);
$pdf->Cell(18, 7, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(314, 158.5);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 165);
$pdf->Cell(17, 7, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(253, 165);
$pdf->Cell(21, 7, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(275, 165);
$pdf->Cell(21, 7, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(296, 165);
$pdf->Cell(18, 7, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(314, 165);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 171);
$pdf->Cell(17, 7, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(253, 171);
$pdf->Cell(21, 7, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(275, 171);
$pdf->Cell(21, 7, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(296, 171);
$pdf->Cell(18, 7, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(314, 171);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 177);
$pdf->Cell(17, 7, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(253, 177);
$pdf->Cell(21, 7, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(275, 177);
$pdf->Cell(21, 7, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(296, 177);
$pdf->Cell(18, 7, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(314, 177);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 183);
$pdf->Cell(17, 6, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(253, 183);
$pdf->Cell(21, 6, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(275, 183);
$pdf->Cell(21, 6, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(296, 183);
$pdf->Cell(18, 6, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(314, 183);
//$pdf->Cell(19, 6, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 188);
$pdf->Cell(17, 7, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(253, 188);
$pdf->Cell(21, 7, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(275, 188);
$pdf->Cell(21, 7, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(296, 188);
$pdf->Cell(18, 7, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(314, 188);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 194);
$pdf->Cell(17, 7, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(253, 194);
$pdf->Cell(21, 7, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(275, 194);
$pdf->Cell(21, 7, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(296, 194);
$pdf->Cell(18, 7, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(314, 194);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 200);
$pdf->Cell(17, 7, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(253, 200);
$pdf->Cell(21, 7, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(275, 200);
$pdf->Cell(21, 7, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(296, 200);
$pdf->Cell(18, 7, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(314, 200);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 206);
$pdf->Cell(17, 7, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(253, 206);
$pdf->Cell(21, 7, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(275, 206);
$pdf->Cell(21, 7, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(296, 206);
$pdf->Cell(18, 7, $Search['Pass14'], 0, 1, 'C');
$pdf->SetXY(314, 206);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 212);
$pdf->Cell(17, 7, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(253, 212);
$pdf->Cell(21, 7, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(275, 212);
$pdf->Cell(21, 7, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(296, 212);
$pdf->Cell(18, 7, $Search['Pass15'], 0, 1, 'C');
$pdf->SetXY(314, 212);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 217.5);
$pdf->Cell(17, 7, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(253, 217.5);
$pdf->Cell(21, 7, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(275, 217.5);
$pdf->Cell(21, 7, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(296, 217.5);
$pdf->Cell(18, 7, $Search['Pass16'], 0, 1, 'C');
$pdf->SetXY(314, 217.5);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 223);
$pdf->Cell(17, 7, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(253, 223);
$pdf->Cell(21, 7, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(275, 223);
$pdf->Cell(21, 7, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(296, 223);
$pdf->Cell(18, 7, $Search['Pass17'], 0, 1, 'C');
$pdf->SetXY(314, 223);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 229);
$pdf->Cell(17, 7, $Search['WtRet18'], 0, 1, 'C');
$pdf->SetXY(253, 229);
$pdf->Cell(21, 7, $Search['Ret18'], 0, 1, 'C');
$pdf->SetXY(275, 229);
$pdf->Cell(21, 7, $Search['CumRet18'], 0, 1, 'C');
$pdf->SetXY(296, 229);
$pdf->Cell(18, 7, $Search['Pass18'], 0, 1, 'C');
$pdf->SetXY(314, 229);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 235);
$pdf->Cell(17, 7, $Search['WtRet19'], 0, 1, 'C');
$pdf->SetXY(253, 235);
$pdf->Cell(21, 7, $Search['Ret19'], 0, 1, 'C');
$pdf->SetXY(275, 235);
$pdf->Cell(21, 7, $Search['CumRet19'], 0, 1, 'C');
$pdf->SetXY(296, 235);
$pdf->Cell(18, 7, $Search['Pass19'], 0, 1, 'C');
$pdf->SetXY(314, 235);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 241);
$pdf->Cell(17, 7, $Search['WtRet20'], 0, 1, 'C');
$pdf->SetXY(253, 241);
$pdf->Cell(21, 7, $Search['Ret20'], 0, 1, 'C');
$pdf->SetXY(275, 241);
$pdf->Cell(21, 7, $Search['CumRet20'], 0, 1, 'C');
$pdf->SetXY(296, 241);
$pdf->Cell(18, 7, $Search['Pass20'], 0, 1, 'C');
$pdf->SetXY(314, 241);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 246);
$pdf->Cell(17, 7, $Search['WtRet21'], 0, 1, 'C');
$pdf->SetXY(253, 246);
$pdf->Cell(21, 7, $Search['Ret21'], 0, 1, 'C');
$pdf->SetXY(275, 246);
$pdf->Cell(21, 7, $Search['CumRet21'], 0, 1, 'C');
$pdf->SetXY(296, 246);
$pdf->Cell(18, 7, $Search['Pass21'], 0, 1, 'C');
$pdf->SetXY(314, 246);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 252);
$pdf->Cell(17, 7, $Search['WtRet22'], 0, 1, 'C');
$pdf->SetXY(253, 252);
$pdf->Cell(21, 7, $Search['Ret22'], 0, 1, 'C');
$pdf->SetXY(275, 252);
$pdf->Cell(21, 7, $Search['CumRet22'], 0, 1, 'C');
$pdf->SetXY(296, 252);
$pdf->Cell(18, 7, $Search['Pass22'], 0, 1, 'C');
$pdf->SetXY(314, 252);
//$pdf->Cell(19, 7, $Search['Wt_Ret_8_203'], 0, 1, 'C');

$pdf->SetXY(235, 258);
$pdf->Cell(17, 7, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(253, 258);
$pdf->Cell(21, 7, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(235, 264);
$pdf->Cell(17, 6, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(253, 264);
$pdf->Cell(21, 6, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(275, 264);
$pdf->Cell(21, 6, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(296, 264);
$pdf->Cell(18, 6, $Search['TotalPass'], 0, 1, 'C');
$pdf->SetXY(314, 264);
//$pdf->Cell(19, 6, $Search['Wt_Ret_8_203'], 0, 1, 'C');

//Sumary grain Size Distribution Parameter

$pdf->SetXY(314, 280);
$pdf->Cell(19, 6, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(314, 286);
$pdf->Cell(19, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(314, 293);
$pdf->Cell(19, 6, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(314, 299);
$pdf->Cell(19, 6, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(314, 305);
$pdf->Cell(19, 6, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(314, 312);
$pdf->Cell(19, 6, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(314, 318);
$pdf->Cell(19, 6, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(314, 324);
$pdf->Cell(19, 6, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(314, 331);
$pdf->Cell(19, 6, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(314, 337);
$pdf->Cell(19, 6, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(314, 343);
$pdf->Cell(19, 6, $Search['Cu'], 0, 1, 'C');

$pdf->SetXY(41, 170);
$pdf->MultiCell(108, 4, $Search['Comments'], 0, 'L');


// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 20, 280, 230, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>