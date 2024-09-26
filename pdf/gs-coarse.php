<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_coarse', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(550, 440));

$pdf->setSourceFile('PV-F-83828_Laboratory sieved Grain size Coarse Aggregate_Rev 1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(74, 64);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(74, 70);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(210, 57);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(210, 64);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(210, 70);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(290, 57);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(290, 64);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(290, 70);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');

$pdf->SetXY(77, 89);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(77, 97);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(77, 106);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(77, 116);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(210, 89);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(210, 97);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(210, 106);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(210, 116);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(290, 89);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(290, 97);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(290, 106);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(290, 116);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetXY(385, 56);
$pdf->Cell(30, 6, "", 0, 1, 'C');
$pdf->SetXY(385, 63);
$pdf->Cell(30, 6, "", 0, 1, 'C');
$pdf->SetXY(385, 69);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(318, 75);
$pdf->Cell(64, 6, "", 0, 1, 'C');
$pdf->SetXY(318, 81);
$pdf->Cell(64, 5, "", 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(126, 132);
$pdf->Cell(47, 6, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(126, 138);
$pdf->Cell(47, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(126, 144);
$pdf->Cell(47, 6, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(126, 151);
$pdf->Cell(47, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(126, 157);
$pdf->Cell(47, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(126, 163);
$pdf->Cell(47, 6, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(126, 170);
$pdf->Cell(47, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');

// Reactivity Test Method FM13-007
$pdf->SetXY(126, 189);
$pdf->Cell(47, 6, $Search['Total_Sample_Weight'], 0, 1, 'C');
$pdf->SetXY(126, 196);
$pdf->Cell(47, 6, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(126, 202);
$pdf->Cell(47, 6, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 208);
$pdf->Cell(47, 6, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 214);
$pdf->Cell(47, 6, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 221);
$pdf->Cell(47, 6, $Search['Weight_Mat_Ret_No_4'], 0, 1, 'C');
$pdf->SetXY(126, 228);
$pdf->Cell(47, 6, $Search['Weight_Reactive_Part_Ret_No_4'], 0, 1, 'C');
$pdf->SetXY(126, 233);
$pdf->Cell(47, 6, $Search['Percent_Reactive_Particles'], 0, 1, 'C');
$pdf->SetXY(126, 239);
$pdf->Cell(47, 6, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 245);
$pdf->Cell(47, 6, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(126, 258);
$pdf->Cell(47, 6, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(260, 138);
$pdf->Cell(29, 6, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(289, 138);
$pdf->Cell(29, 6, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(318, 138);
$pdf->Cell(21, 6, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(339, 138);
$pdf->Cell(43, 6, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(382, 138);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 144);
$pdf->Cell(29, 6, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(289, 144);
$pdf->Cell(29, 6, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(318, 144);
$pdf->Cell(21, 6, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(339, 144);
$pdf->Cell(43, 6, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(382, 144);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 150);
$pdf->Cell(29, 6, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(289, 150);
$pdf->Cell(29, 6, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(318, 150);
$pdf->Cell(21, 6, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(339, 150);
$pdf->Cell(43, 6, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(382, 150);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 156);
$pdf->Cell(29, 6, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(289, 156);
$pdf->Cell(29, 6, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(318, 156);
$pdf->Cell(21, 6, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(339, 156);
$pdf->Cell(43, 6, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(382, 156);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 163);
$pdf->Cell(29, 6, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(289, 163);
$pdf->Cell(29, 6, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(318, 163);
$pdf->Cell(21, 6, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(339, 163);
$pdf->Cell(43, 6, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(382, 163);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 170);
$pdf->Cell(29, 6, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(289, 170);
$pdf->Cell(29, 6, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(318, 170);
$pdf->Cell(21, 6, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(339, 170);
$pdf->Cell(43, 6, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(382, 170);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 176);
$pdf->Cell(29, 6, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(289, 176);
$pdf->Cell(29, 6, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(318, 176);
$pdf->Cell(21, 6, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(339, 176);
$pdf->Cell(43, 6, $Search['Pass7'], 0, 1, 'C');

$pdf->SetXY(260, 182);
$pdf->Cell(29, 6, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(289, 182);
$pdf->Cell(29, 6, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(318, 182);
$pdf->Cell(21, 6, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(339, 182);
$pdf->Cell(43, 6, $Search['Pass8'], 0, 1, 'C');

$pdf->SetXY(260, 189);
$pdf->Cell(29, 6, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(289, 189);
$pdf->Cell(29, 6, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(318, 189);
$pdf->Cell(21, 6, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(339, 189);
$pdf->Cell(43, 6, $Search['Pass9'], 0, 1, 'C');

$pdf->SetXY(260, 195);
$pdf->Cell(29, 6, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(289, 195);
$pdf->Cell(29, 6, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(318, 195);
$pdf->Cell(21, 6, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(339, 195);
$pdf->Cell(43, 6, $Search['Pass10'], 0, 1, 'C');

$pdf->SetXY(260, 202);
$pdf->Cell(29, 6, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(289, 202);
$pdf->Cell(29, 6, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(318, 202);
$pdf->Cell(21, 6, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(339, 202);
$pdf->Cell(43, 6, $Search['Pass11'], 0, 1, 'C');

$pdf->SetXY(260, 208);
$pdf->Cell(29, 6, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(289, 208);
$pdf->Cell(29, 6, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(318, 208);
$pdf->Cell(21, 6, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(339, 208);
$pdf->Cell(43, 6, $Search['Pass12'], 0, 1, 'C');

$pdf->SetXY(260, 214);
$pdf->Cell(29, 6, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(289, 214);
$pdf->Cell(29, 6, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(318, 214);
$pdf->Cell(21, 6, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(339, 214);
$pdf->Cell(43, 6, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(382, 214);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 221);
$pdf->Cell(29, 6, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(289, 221);
$pdf->Cell(29, 6, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(318, 221);
$pdf->Cell(21, 6, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(339, 221);
$pdf->Cell(43, 6, $Search['Pass14'], 0, 1, 'C');

$pdf->SetXY(260, 228);
$pdf->Cell(29, 6, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(289, 228);
$pdf->Cell(29, 6, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(318, 228);
$pdf->Cell(21, 6, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(339, 228);
$pdf->Cell(43, 6, $Search['Pass15'], 0, 1, 'C');
$pdf->SetXY(382, 228);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 234);
$pdf->Cell(29, 6, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(289, 234);
$pdf->Cell(29, 6, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(318, 234);
$pdf->Cell(21, 6, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(339, 234);
$pdf->Cell(43, 6, $Search['Pass16'], 0, 1, 'C');
$pdf->SetXY(382, 234);
$pdf->Cell(30, 6, "", 0, 1, 'C');

$pdf->SetXY(260, 239);
$pdf->Cell(29, 6, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(289, 239);
$pdf->Cell(29, 6, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(318, 239);
$pdf->Cell(21, 6, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(339, 239);
$pdf->Cell(43, 6, $Search['Pass17'], 0, 1, 'C');

$pdf->SetXY(260, 245);
$pdf->Cell(29, 6, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(289, 245);
$pdf->Cell(29, 6, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(260, 252);
$pdf->Cell(29, 6, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(289, 252);
$pdf->Cell(29, 6, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(318, 252);
$pdf->Cell(21, 6, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(339, 252);
$pdf->Cell(43, 6, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter

$pdf->SetXY(339, 277);
$pdf->Cell(43, 6, $Search['Coarser_than_Gravel'], 1, 1, 'C');
$pdf->SetXY(339, 282);
$pdf->Cell(43, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(339, 288);
$pdf->Cell(43, 6, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(339, 295);
$pdf->Cell(43, 6, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(339, 301);
$pdf->Cell(43, 6, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(339, 307);
$pdf->Cell(43, 6, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(339, 314);
$pdf->Cell(43, 6, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(339, 320);
$pdf->Cell(43, 6, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(339, 326);
$pdf->Cell(43, 6, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(339, 333);
$pdf->Cell(43, 6, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(339, 339);
$pdf->Cell(43, 5, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS

$pdf->SetXY(260, 357);
$pdf->Cell(152, 6, "", 0, 1, 'C');
$pdf->SetXY(339, 383);
$pdf->Cell(73, 6, "", 0, 1, 'C');

$pdf->SetXY(52, 408);
$pdf->MultiCell(360, 70, $Search['Comments'], 0, 1, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 20, 270, 230, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>