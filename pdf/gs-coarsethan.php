<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_coarsethan', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(550, 440));

$pdf->setSourceFile('PV-F-83829_Laboratory Sieve Grain Size for Coarse Than Aggregate_Rev 1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(82, 72);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(82, 78);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(210, 66);
$pdf->Cell(21, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(210, 72);
$pdf->Cell(21, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(210, 78);
$pdf->Cell(21, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(288, 65);
$pdf->Cell(21, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(288, 72);
$pdf->Cell(21, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(288, 78);
$pdf->Cell(21, 6, $Search['Split_Method'], 0, 1, 'C');

$pdf->SetXY(85, 97);
$pdf->Cell(21, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(85, 104);
$pdf->Cell(21, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(85, 114);
$pdf->Cell(21, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(85, 123);
$pdf->Cell(21, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(210, 97);
$pdf->Cell(21, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(210, 104);
$pdf->Cell(21, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(210, 114);
$pdf->Cell(21, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(210, 123);
$pdf->Cell(21, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(288, 97);
$pdf->Cell(21, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(288, 104);
$pdf->Cell(21, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(288, 114);
$pdf->Cell(21, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(288, 123);
$pdf->Cell(21, 6, $Search['East'], 0, 1, 'C');

$pdf->SetXY(385, 66);
$pdf->Cell(21, 6, "", 0, 1, 'C');
$pdf->SetXY(385, 72);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(341, 82);
$pdf->Cell(25, 6, "", 0, 1, 'C');
$pdf->SetXY(341, 88);
$pdf->Cell(25, 5, "", 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(130, 137);
$pdf->Cell(37, 7, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(130, 143);
$pdf->Cell(37, 7, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(130, 149);
$pdf->Cell(37, 7, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(130, 155);
$pdf->Cell(37, 7, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(130, 161);
$pdf->Cell(37, 7, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(130, 167);
$pdf->Cell(37, 7, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(130, 174);
$pdf->Cell(37, 7, $Search['Wt_Wash_Pan'], 0, 1, 'C');


// Reactivity Test Method FM13-007
$pdf->SetXY(130, 194);
$pdf->Cell(37, 6, $Search['Total_Sample_Weight'], 0, 1, 'C');
$pdf->SetXY(130, 200);
$pdf->Cell(37, 6, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(130, 206);
$pdf->Cell(37, 6, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 212);
$pdf->Cell(37, 6, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 218);
$pdf->Cell(37, 6, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 225);
$pdf->Cell(37, 6, $Search['Weight_Mat_Ret_No_4'], 0, 1, 'C');
$pdf->SetXY(130, 232);
$pdf->Cell(37, 5, $Search['Weight_Reactive_Part_Ret_No_4'], 0, 1, 'C');
$pdf->SetXY(130, 237);
$pdf->Cell(37, 6, $Search['Percent_Reactive_Particles'], 0, 1, 'C');
$pdf->SetXY(130, 243);
$pdf->Cell(37, 5, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 248);
$pdf->Cell(37, 6, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(130, 261);
$pdf->Cell(37, 6, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(251, 144);
$pdf->Cell(23, 6, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(274, 144);
$pdf->Cell(21, 6, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(295, 144);
$pdf->Cell(27, 6, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(322, 144);
$pdf->Cell(42, 6, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(364, 144);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 150);
$pdf->Cell(23, 6, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(274, 150);
$pdf->Cell(21, 6, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(295, 150);
$pdf->Cell(27, 6, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(322, 150);
$pdf->Cell(42, 6, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(364, 150);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 156);
$pdf->Cell(23, 6, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(274, 156);
$pdf->Cell(21, 6, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(295, 156);
$pdf->Cell(27, 6, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(322, 156);
$pdf->Cell(42, 6, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(364, 156);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 162);
$pdf->Cell(23, 6, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(274, 162);
$pdf->Cell(21, 6, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(295, 162);
$pdf->Cell(27, 6, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(322, 162);
$pdf->Cell(42, 6, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(364, 162);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 168);
$pdf->Cell(23, 6, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(274, 168);
$pdf->Cell(21, 6, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(295, 168);
$pdf->Cell(27, 6, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(322, 168);
$pdf->Cell(42, 6, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(364, 168);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 174);
$pdf->Cell(23, 6, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(274, 174);
$pdf->Cell(21, 6, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(295, 174);
$pdf->Cell(27, 6, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(322, 174);
$pdf->Cell(42, 6, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(364, 174);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 180);
$pdf->Cell(23, 6, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(274, 180);
$pdf->Cell(21, 6, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(295, 180);
$pdf->Cell(27, 6, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(322, 180);
$pdf->Cell(42, 6, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(364, 180);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 187);
$pdf->Cell(23, 6, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(274, 187);
$pdf->Cell(21, 6, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(295, 187);
$pdf->Cell(27, 6, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(322, 187);
$pdf->Cell(42, 6, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(364, 187);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 193);
$pdf->Cell(23, 6, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(274, 193);
$pdf->Cell(21, 6, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(295, 193);
$pdf->Cell(27, 6, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(322, 193);
$pdf->Cell(42, 6, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(364, 193);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 200);
$pdf->Cell(23, 6, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(274, 200);
$pdf->Cell(21, 6, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(295, 200);
$pdf->Cell(27, 6, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(322, 200);
$pdf->Cell(42, 6, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(364, 200);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 206);
$pdf->Cell(23, 6, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(274, 206);
$pdf->Cell(21, 6, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(295, 206);
$pdf->Cell(27, 6, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(322, 206);
$pdf->Cell(42, 6, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(364, 206);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 212);
$pdf->Cell(23, 6, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(274, 212);
$pdf->Cell(21, 6, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(295, 212);
$pdf->Cell(27, 6, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(322, 212);
$pdf->Cell(42, 6, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(364, 212);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 218);
$pdf->Cell(23, 6, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(274, 218);
$pdf->Cell(21, 6, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(295, 218);
$pdf->Cell(27, 6, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(322, 218);
$pdf->Cell(42, 6, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(364, 218);
$pdf->Cell(25, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 224);
$pdf->Cell(23, 6, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(274, 224);
$pdf->Cell(21, 6, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(251, 231);
$pdf->Cell(23, 6, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(274, 231);
$pdf->Cell(21, 6, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(295, 231);
$pdf->Cell(27, 6, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(322, 231);
$pdf->Cell(42, 6, $Search['TotalPass'], 0, 1, 'C');


// Summary Grain Size Distribution Parameter

$pdf->SetXY(295, 248);
$pdf->Cell(27, 6, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(295, 255);
$pdf->Cell(27, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(295, 261);
$pdf->Cell(27, 6, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(295, 267);
$pdf->Cell(27, 6, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(322, 273);
$pdf->Cell(41, 6, $Search['D10'], 1, 1, 'C');
$pdf->SetXY(322, 279);
$pdf->Cell(41, 6, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(322, 285);
$pdf->Cell(41, 6, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(322, 291);
$pdf->Cell(41, 6, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(322, 297);
$pdf->Cell(41, 6, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(322, 303);
$pdf->Cell(41, 6, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(322, 309);
$pdf->Cell(41, 5, $Search['Cu'], 0, 1, 'C');


//Coarse Grained Classification & Comments
$pdf->SetXY(250, 327);
$pdf->Cell(138, 6, "", 0, 1, 'C');
$pdf->SetXY(322, 350);
$pdf->Cell(67, 6, "", 0, 1, 'C');

$pdf->SetXY(53, 406);
$pdf->MultiCell(108, 5, $Search['Comments'], 0, 'L');


// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 30, 260, 200, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>