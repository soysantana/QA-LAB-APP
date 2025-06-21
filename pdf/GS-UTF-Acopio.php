<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_upstream_transition_fill', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(570, 460));

$pdf->setSourceFile('template/PV-F-81259 Laboratory Sieve Grain Size for UTF.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$Sand = $Search['Sand'];
$Fines = $Search['Fines'];

// Condición para "Acepted"
if (
    $Sand >= 39.45 &&
    $Fines <= 4.4
) {
    $resultado = 'Acepted';
} else {
    $resultado = 'Rejected';
}

$pdf->SetFont('Arial', 'B', 10);

//Information for the essay
$pdf->SetXY(105, 85);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(105, 93);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(105, 100);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(105, 119);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(105, 128);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(105, 136);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(105, 143);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(208, 85);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(208, 93);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(208, 100);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(208, 119);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(208, 128);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(208, 136);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(208, 143);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(292, 85);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(292, 93);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(292, 100);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(292, 119);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(292, 128);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(292, 136);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(292, 143);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetXY(390, 85);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(125, 163);
$pdf->Cell(47, 6, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(125, 169);
$pdf->Cell(47, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(125, 175);
$pdf->Cell(47, 6, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(125, 181);
$pdf->Cell(47, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(125, 187);
$pdf->Cell(47, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(125, 193);
$pdf->Cell(47, 6, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(125, 199);
$pdf->Cell(47, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');

// Reactivity Test Method FM13-007
$pdf->SetXY(125, 219);
$pdf->Cell(47, 4, $Search['Total_Sample_Weight'], 0, 1, 'C');
$pdf->SetXY(125, 225);
$pdf->Cell(47, 4, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(125, 231);
$pdf->Cell(47, 4, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(125, 237);
$pdf->Cell(47, 4, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(125, 243);
$pdf->Cell(47, 4, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(125, 249);
$pdf->Cell(47, 4, '', 0, 1, 'C');
$pdf->SetXY(125, 255);
$pdf->Cell(47, 4, '', 0, 1, 'C');
$pdf->SetXY(125, 261);
$pdf->Cell(47, 4, '', 0, 1, 'C');
$pdf->SetXY(125, 267);
$pdf->Cell(47, 4, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(125, 273);
$pdf->Cell(47, 4, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(125, 285);
$pdf->Cell(47, 4, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(250, 176);
$pdf->Cell(29, 4, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(293, 176);
$pdf->Cell(29, 4, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(335, 176);
$pdf->Cell(21, 4, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(359, 176);
$pdf->Cell(43, 4, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(394, 176);
$pdf->Cell(43, 4, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(250, 182);
$pdf->Cell(29, 4, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(293, 182);
$pdf->Cell(29, 4, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(335, 182);
$pdf->Cell(21, 4, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(359, 182);
$pdf->Cell(43, 4, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(394, 182);
$pdf->Cell(43, 4, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(250, 188);
$pdf->Cell(29, 4, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(293, 188);
$pdf->Cell(29, 4, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(335, 188);
$pdf->Cell(21, 4, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(359, 188);
$pdf->Cell(43, 4, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(394, 188);
$pdf->Cell(43, 4, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(250, 194);
$pdf->Cell(29, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(293, 194);
$pdf->Cell(29, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(335, 194);
$pdf->Cell(21, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(359, 194);
$pdf->Cell(43, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(394, 194);
$pdf->Cell(43, 4, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(250, 200);
$pdf->Cell(29, 4, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(293, 200);
$pdf->Cell(29, 4, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(335, 200);
$pdf->Cell(21, 4, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(359, 200);
$pdf->Cell(43, 4, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(394, 200);
$pdf->Cell(43, 4, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(250, 206);
$pdf->Cell(29, 4, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(293, 206);
$pdf->Cell(29, 4, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(335, 206);
$pdf->Cell(21, 4, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(359, 206);
$pdf->Cell(43, 4, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(394, 206);
$pdf->Cell(43, 4, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(250, 212);
$pdf->Cell(29, 4, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(293, 212);
$pdf->Cell(29, 4, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(335, 212);
$pdf->Cell(21, 4, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(359, 212);
$pdf->Cell(43, 4, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(394, 212);
$pdf->Cell(43, 4, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(250, 219);
$pdf->Cell(29, 4, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(293, 219);
$pdf->Cell(29, 4, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(335, 219);
$pdf->Cell(21, 4, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(359, 219);
$pdf->Cell(43, 4, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(394, 219);
$pdf->Cell(43, 4, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(250, 225);
$pdf->Cell(29, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(293, 225);
$pdf->Cell(29, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(335, 225);
$pdf->Cell(21, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(359, 225);
$pdf->Cell(43, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(394, 225);
$pdf->Cell(43, 4, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(250, 231);
$pdf->Cell(29, 4, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(293, 231);
$pdf->Cell(29, 4, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(335, 231);
$pdf->Cell(21, 4, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(359, 231);
$pdf->Cell(43, 4, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(394, 231);
$pdf->Cell(43, 4, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(250, 237);
$pdf->Cell(29, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(293, 237);
$pdf->Cell(29, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(335, 237);
$pdf->Cell(21, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(359, 237);
$pdf->Cell(43, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(394, 237);
$pdf->Cell(43, 4, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(250, 243);
$pdf->Cell(29, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(293, 243);
$pdf->Cell(29, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(335, 243);
$pdf->Cell(21, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(359, 243);
$pdf->Cell(43, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(394, 243);
$pdf->Cell(43, 4, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(250, 249);
$pdf->Cell(29, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(293, 249);
$pdf->Cell(29, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(335, 249);
$pdf->Cell(21, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(359, 249);
$pdf->Cell(43, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(394, 249);
$pdf->Cell(43, 4, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(250, 255);
$pdf->Cell(29, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(293, 255);
$pdf->Cell(29, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(250, 261);
$pdf->Cell(29, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(293, 261);
$pdf->Cell(29, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(335, 261);
$pdf->Cell(21, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(359, 261);
$pdf->Cell(43, 4, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter
$pdf->SetXY(332, 279);
$pdf->Cell(29, 4, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(332, 285);
$pdf->Cell(29, 4, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(332, 291);
$pdf->Cell(29, 4, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(332, 297);
$pdf->Cell(29, 4, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(365, 303);
$pdf->Cell(29, 4, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(365, 308);
$pdf->Cell(29, 4, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(365, 313);
$pdf->Cell(29, 4, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(365, 319);
$pdf->Cell(29, 4, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(365, 325);
$pdf->Cell(29, 4, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(365, 331);
$pdf->Cell(29, 4, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(365, 337);
$pdf->Cell(29, 4, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS
$pdf->SetXY(265, 354);
$pdf->Cell(152, 6, $Search['ClassificationUSCS1'], 0, 1, 'C');
$pdf->SetXY(265, 360);
$pdf->Cell(152, 6, $Search['ClassificationUSCS2'], 0, 1, 'C');

// Grain Size Test Results
$pdf->SetXY(361, 378);
$pdf->Cell(73, 6, $resultado, 0, 1, 'C');

// Comments and observations
$pdf->SetXY(52, 430);
$pdf->MultiCell(145, 4, $Search['Comments'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 38, 290, 200, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
