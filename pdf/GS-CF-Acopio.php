<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_coarse', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(560, 470));

$pdf->setSourceFile('template/PV-F-83828_Laboratory sieve Grain size Coarse Aggregates_Rev 6.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pass1p5 = $Search['Pass7'];
$pass1 = $Search['Pass8'];
$pass3p4 = $Search['Pass9'];
$pass3p8 = $Search['Pass11'];
$passn4 = $Search['Pass12'];
$passn10 = $Search['Pass13'];
$passn20 = $Search['Pass15'];
$passn200 = $Search['Pass18'];

// Condición para "Acepted"
if (
    $pass1p5 == 100 &&
    $pass1 >= 87 && $pass1 <= 100 &&
    $pass3p4 >= 80 && $pass3p4 <= 100 &&
    $pass3p8 >= 50 && $pass3p8 <= 100 &&
    $passn4 >= 14.5 && $passn4 <= 60 &&
    $passn10 >= 2 && $passn10 <= 15 &&
    $passn20 >= 0 && $passn20 <= 7 &&
    $passn200 >= 0 && $passn200 <= 2
) {
    $resultado = 'Acepted';
} else {
    $resultado = 'Rejected';
}


$pdf->SetFont('Arial', 'B', 10);

//Information for the essay
$pdf->SetXY(100, 80);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(100, 89);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(100, 95);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(100, 112);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(100, 119);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(100, 126);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(100, 135);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(215, 80);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(215, 89);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(215, 95);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(215, 112);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(215, 119);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(215, 126);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(215, 135);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(295, 80);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(295, 89);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(295, 95);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(295, 112);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(295, 119);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(295, 126);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(295, 135);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetXY(400, 80);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(127, 152);
$pdf->Cell(47, 6, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(127, 158);
$pdf->Cell(47, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(127, 164);
$pdf->Cell(47, 6, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(127, 170);
$pdf->Cell(47, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(127, 176);
$pdf->Cell(47, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(127, 182);
$pdf->Cell(47, 6, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(127, 189);
$pdf->Cell(47, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');

// Reactivity Test Method FM13-007
$pdf->SetXY(127, 208);
$pdf->Cell(47, 4, $Search['Total_Sample_Weight'], 0, 1, 'C');
$pdf->SetXY(127, 214);
$pdf->Cell(47, 4, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(127, 220);
$pdf->Cell(47, 4, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(127, 226);
$pdf->Cell(47, 4, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(127, 232);
$pdf->Cell(47, 4, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(127, 238);
$pdf->Cell(47, 4, "", 0, 1, 'C');
$pdf->SetXY(127, 244);
$pdf->Cell(47, 4, "", 0, 1, 'C');
$pdf->SetXY(127, 250);
$pdf->Cell(47, 4, "", 0, 1, 'C');
$pdf->SetXY(127, 256);
$pdf->Cell(47, 4, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(127, 262);
$pdf->Cell(47, 4, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(127, 274);
$pdf->Cell(47, 4, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(261, 160);
$pdf->Cell(29, 4, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(296, 160);
$pdf->Cell(29, 4, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(336, 160);
$pdf->Cell(21, 4, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(362, 160);
$pdf->Cell(43, 4, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(399, 160);
$pdf->Cell(43, 4, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(261, 166);
$pdf->Cell(29, 4, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(296, 166);
$pdf->Cell(29, 4, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(336, 166);
$pdf->Cell(21, 4, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(362, 166);
$pdf->Cell(43, 4, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(399, 166);
$pdf->Cell(43, 4, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(261, 172);
$pdf->Cell(29, 4, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(296, 172);
$pdf->Cell(29, 4, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(336, 172);
$pdf->Cell(21, 4, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(362, 172);
$pdf->Cell(43, 4, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(399, 172);
$pdf->Cell(43, 4, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(261, 178);
$pdf->Cell(29, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(296, 178);
$pdf->Cell(29, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(336, 178);
$pdf->Cell(21, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(362, 178);
$pdf->Cell(43, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(399, 178);
$pdf->Cell(43, 4, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(261, 184);
$pdf->Cell(29, 4, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(296, 184);
$pdf->Cell(29, 4, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(336, 184);
$pdf->Cell(21, 4, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(362, 184);
$pdf->Cell(43, 4, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(399, 184);
$pdf->Cell(43, 4, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(261, 190);
$pdf->Cell(29, 4, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(296, 190);
$pdf->Cell(29, 4, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(336, 190);
$pdf->Cell(21, 4, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(362, 190);
$pdf->Cell(43, 4, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(399, 190);
$pdf->Cell(43, 4, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(261, 196);
$pdf->Cell(29, 4, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(296, 196);
$pdf->Cell(29, 4, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(336, 196);
$pdf->Cell(21, 4, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(362, 196);
$pdf->Cell(43, 4, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(399, 196);
$pdf->Cell(43, 4, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(261, 202);
$pdf->Cell(29, 4, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(296, 202);
$pdf->Cell(29, 4, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(336, 202);
$pdf->Cell(21, 4, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(362, 202);
$pdf->Cell(43, 4, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(399, 202);
$pdf->Cell(43, 4, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(261, 208);
$pdf->Cell(29, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(296, 208);
$pdf->Cell(29, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(336, 208);
$pdf->Cell(21, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(362, 208);
$pdf->Cell(43, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(399, 208);
$pdf->Cell(43, 4, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(261, 214);
$pdf->Cell(29, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(296, 214);
$pdf->Cell(29, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(336, 214);
$pdf->Cell(21, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(362, 214);
$pdf->Cell(43, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(399, 214);
$pdf->Cell(43, 4, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(261, 221);
$pdf->Cell(29, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(296, 221);
$pdf->Cell(29, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(336, 221);
$pdf->Cell(21, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(362, 221);
$pdf->Cell(43, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(399, 221);
$pdf->Cell(43, 4, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(261, 227);
$pdf->Cell(29, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(296, 227);
$pdf->Cell(29, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(336, 227);
$pdf->Cell(21, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(362, 227);
$pdf->Cell(43, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(399, 227);
$pdf->Cell(43, 4, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(261, 233);
$pdf->Cell(29, 4, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(296, 233);
$pdf->Cell(29, 4, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(336, 233);
$pdf->Cell(21, 4, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(362, 233);
$pdf->Cell(43, 4, $Search['Pass14'], 0, 1, 'C');
$pdf->SetXY(399, 233);
$pdf->Cell(43, 4, $Search['Specs14'], 0, 1, 'C');

$pdf->SetXY(261, 239);
$pdf->Cell(29, 4, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(296, 239);
$pdf->Cell(29, 4, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(336, 239);
$pdf->Cell(21, 4, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(362, 239);
$pdf->Cell(43, 4, $Search['Pass15'], 0, 1, 'C');
$pdf->SetXY(399, 239);
$pdf->Cell(43, 4, $Search['Specs15'], 0, 1, 'C');

$pdf->SetXY(261, 245);
$pdf->Cell(29, 4, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(296, 245);
$pdf->Cell(29, 4, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(336, 245);
$pdf->Cell(21, 4, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(362, 245);
$pdf->Cell(43, 4, $Search['Pass16'], 0, 1, 'C');
$pdf->SetXY(399, 245);
$pdf->Cell(43, 4, $Search['Specs16'], 0, 1, 'C');

$pdf->SetXY(261, 251);
$pdf->Cell(29, 4, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(296, 251);
$pdf->Cell(29, 4, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(336, 251);
$pdf->Cell(21, 4, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(362, 251);
$pdf->Cell(43, 4, $Search['Pass17'], 0, 1, 'C');
$pdf->SetXY(399, 251);
$pdf->Cell(43, 4, $Search['Specs17'], 0, 1, 'C');

$pdf->SetXY(261, 257);
$pdf->Cell(29, 4, $Search['WtRet18'], 0, 1, 'C');
$pdf->SetXY(296, 257);
$pdf->Cell(29, 4, $Search['Ret18'], 0, 1, 'C');
$pdf->SetXY(336, 257);
$pdf->Cell(21, 4, $Search['CumRet18'], 0, 1, 'C');
$pdf->SetXY(362, 257);
$pdf->Cell(43, 4, $Search['Pass18'], 0, 1, 'C');
$pdf->SetXY(399, 257);
$pdf->Cell(43, 4, $Search['Specs18'], 0, 1, 'C');

$pdf->SetXY(261, 262);
$pdf->Cell(29, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(296, 262);
$pdf->Cell(29, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(261, 269);
$pdf->Cell(29, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(296, 269);
$pdf->Cell(29, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(336, 269);
$pdf->Cell(21, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(362, 269);
$pdf->Cell(43, 4, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter
$pdf->SetXY(361, 293);
$pdf->Cell(43, 3, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(361, 298);
$pdf->Cell(43, 3, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(361, 304);
$pdf->Cell(43, 3, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(361, 310);
$pdf->Cell(43, 3, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(361, 316);
$pdf->Cell(43, 3, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(361, 322);
$pdf->Cell(43, 3, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(361, 328);
$pdf->Cell(43, 3, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(361, 334);
$pdf->Cell(43, 3, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(361, 340);
$pdf->Cell(43, 3, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(361, 346);
$pdf->Cell(43, 3, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(361, 352);
$pdf->Cell(43, 3, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS
$pdf->SetXY(280, 367);
$pdf->Cell(152, 6, $Search['ClassificationUSCS1'], 0, 1, 'C');
$pdf->SetXY(280, 373);
$pdf->Cell(152, 6, $Search['ClassificationUSCS2'], 0, 1, 'C');

// Grain Size Test Results
$pdf->SetXY(323, 394);
$pdf->Cell(152, 4, $resultado, 0, 1, 'C');

// Comments and Observations
$pdf->SetXY(54, 425);
$pdf->Cell(360, 4, $Search['Comments'], 0, 1, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 25, 280, 230, 0, 'PNG');
unlink($tempFile);


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
