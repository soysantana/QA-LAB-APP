<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_fine', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(620, 500));

$pdf->setSourceFile('template/PV-F-01710 Laboratory sieve Grain size and acid reactivity for Fine FIlter.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

//Information for the essay
$pdf->SetXY(100, 73);
$pdf->Cell(30, 4, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(100, 89);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(100, 95);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(100, 102);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(100, 119);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(100, 126);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(100, 133);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(100, 140);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(210, 73);
$pdf->Cell(30, 6, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(210, 89);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(210, 95);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(210, 102);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(210, 119);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(210, 126);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(210, 133);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(210, 140);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(320, 73);
$pdf->Cell(30, 6, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(320, 89);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(320, 95);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(320, 102);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(320, 119);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(320, 126);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(320, 133);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(320, 140);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(152, 159);
$pdf->Cell(47, 6, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(152, 165);
$pdf->Cell(47, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(152, 172);
$pdf->Cell(47, 6, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(152, 178);
$pdf->Cell(47, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(152, 184);
$pdf->Cell(47, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(152, 190);
$pdf->Cell(47, 6, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(152, 196);
$pdf->Cell(47, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');

// Reactivity Test Method FM13-007
$pdf->SetXY(152, 216);
$pdf->Cell(47, 4, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(152, 222);
$pdf->Cell(47, 4, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 229);
$pdf->Cell(47, 4, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 235);
$pdf->Cell(47, 4, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 241);
$pdf->Cell(47, 4, $Search['D_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 247);
$pdf->Cell(47, 4, $Search['E_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 254);
$pdf->Cell(47, 4, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 260);
$pdf->Cell(47, 4, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(109, 271);
$pdf->Cell(47, 4, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(282, 166);
$pdf->Cell(29, 4, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(317, 166);
$pdf->Cell(29, 4, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(351, 166);
$pdf->Cell(21, 4, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(374, 166);
$pdf->Cell(43, 4, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(404, 166);
$pdf->Cell(43, 4, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(282, 173);
$pdf->Cell(29, 4, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(317, 173);
$pdf->Cell(29, 4, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(351, 173);
$pdf->Cell(21, 4, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(374, 173);
$pdf->Cell(43, 4, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(404, 173);
$pdf->Cell(43, 4, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(282, 179);
$pdf->Cell(29, 4, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(317, 179);
$pdf->Cell(29, 4, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(351, 179);
$pdf->Cell(21, 4, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(374, 179);
$pdf->Cell(43, 4, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(404, 179);
$pdf->Cell(43, 4, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(282, 185);
$pdf->Cell(29, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(317, 185);
$pdf->Cell(29, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(351, 185);
$pdf->Cell(21, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(374, 185);
$pdf->Cell(43, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(404, 185);
$pdf->Cell(43, 4, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(282, 191);
$pdf->Cell(29, 4, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(317, 191);
$pdf->Cell(29, 4, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(351, 191);
$pdf->Cell(21, 4, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(374, 191);
$pdf->Cell(43, 4, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(404, 191);
$pdf->Cell(43, 4, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(282, 197);
$pdf->Cell(29, 4, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(317, 197);
$pdf->Cell(29, 4, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(351, 197);
$pdf->Cell(21, 4, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(374, 197);
$pdf->Cell(43, 4, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(404, 197);
$pdf->Cell(43, 4, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(282, 203);
$pdf->Cell(29, 4, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(317, 203);
$pdf->Cell(29, 4, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(351, 203);
$pdf->Cell(21, 4, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(374, 203);
$pdf->Cell(43, 4, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(404, 203);
$pdf->Cell(43, 4, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(282, 209);
$pdf->Cell(29, 4, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(317, 209);
$pdf->Cell(29, 4, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(351, 209);
$pdf->Cell(21, 4, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(374, 209);
$pdf->Cell(43, 4, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(404, 209);
$pdf->Cell(43, 4, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(282, 216);
$pdf->Cell(29, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(317, 216);
$pdf->Cell(29, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(351, 216);
$pdf->Cell(21, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(374, 216);
$pdf->Cell(43, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(404, 216);
$pdf->Cell(43, 4, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(282, 222);
$pdf->Cell(29, 4, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(317, 222);
$pdf->Cell(29, 4, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(351, 222);
$pdf->Cell(21, 4, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(374, 222);
$pdf->Cell(43, 4, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(404, 222);
$pdf->Cell(43, 4, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(282, 229);
$pdf->Cell(29, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(317, 229);
$pdf->Cell(29, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(351, 229);
$pdf->Cell(21, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(374, 229);
$pdf->Cell(43, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(404, 229);
$pdf->Cell(43, 4, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(282, 235);
$pdf->Cell(29, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(317, 235);
$pdf->Cell(29, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(351, 235);
$pdf->Cell(21, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(374, 235);
$pdf->Cell(43, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(404, 235);
$pdf->Cell(43, 4, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(282, 241);
$pdf->Cell(29, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(317, 241);
$pdf->Cell(29, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(351, 241);
$pdf->Cell(21, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(374, 241);
$pdf->Cell(43, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(404, 241);
$pdf->Cell(43, 4, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(282, 248);
$pdf->Cell(29, 4, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(317, 248);
$pdf->Cell(29, 4, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(351, 248);
$pdf->Cell(21, 4, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(374, 248);
$pdf->Cell(43, 4, $Search['Pass14'], 0, 1, 'C');
$pdf->SetXY(404, 248);
$pdf->Cell(43, 4, $Search['Specs14'], 0, 1, 'C');

$pdf->SetXY(282, 254);
$pdf->Cell(29, 4, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(317, 254);
$pdf->Cell(29, 4, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(351, 254);
$pdf->Cell(21, 4, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(374, 254);
$pdf->Cell(43, 4, $Search['Pass15'], 0, 1, 'C');
$pdf->SetXY(404, 254);
$pdf->Cell(43, 4, $Search['Specs15'], 0, 1, 'C');

$pdf->SetXY(282, 260);
$pdf->Cell(29, 4, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(317, 260);
$pdf->Cell(29, 4, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(351, 260);
$pdf->Cell(21, 4, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(374, 260);
$pdf->Cell(43, 4, $Search['Pass16'], 0, 1, 'C');
$pdf->SetXY(404, 260);
$pdf->Cell(43, 4, $Search['Specs16'], 0, 1, 'C');

$pdf->SetXY(282, 266);
$pdf->Cell(29, 4, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(317, 266);
$pdf->Cell(29, 4, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(351, 266);
$pdf->Cell(21, 4, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(374, 266);
$pdf->Cell(43, 4, $Search['Pass17'], 0, 1, 'C');
$pdf->SetXY(404, 266);
$pdf->Cell(43, 4, $Search['Specs17'], 0, 1, 'C');

$pdf->SetXY(282, 272);
$pdf->Cell(29, 4, $Search['WtRet18'], 0, 1, 'C');
$pdf->SetXY(317, 272);
$pdf->Cell(29, 4, $Search['Ret18'], 0, 1, 'C');
$pdf->SetXY(351, 272);
$pdf->Cell(21, 4, $Search['CumRet18'], 0, 1, 'C');
$pdf->SetXY(374, 272);
$pdf->Cell(43, 4, $Search['Pass18'], 0, 1, 'C');
$pdf->SetXY(404, 272);
$pdf->Cell(43, 4, $Search['Specs18'], 0, 1, 'C');

$pdf->SetXY(282, 278);
$pdf->Cell(29, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(317, 278);
$pdf->Cell(29, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(282, 284);
$pdf->Cell(29, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(317, 284);
$pdf->Cell(29, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(351, 284);
$pdf->Cell(21, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(374, 284);
$pdf->Cell(43, 4, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter
$pdf->SetXY(374, 302);
$pdf->Cell(43, 4, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(374, 308);
$pdf->Cell(43, 4, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(374, 314);
$pdf->Cell(43, 4, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(374, 320);
$pdf->Cell(43, 4, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(374, 326);
$pdf->Cell(43, 4, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(374, 332);
$pdf->Cell(43, 4, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(374, 338);
$pdf->Cell(43, 4, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(374, 344);
$pdf->Cell(43, 4, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(374, 350);
$pdf->Cell(43, 4, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(374, 356);
$pdf->Cell(43, 4, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(374, 362);
$pdf->Cell(43, 4, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS
$pdf->SetXY(287, 379);
$pdf->Cell(152, 6, '', 0, 1, 'C');
$pdf->SetXY(287, 385);
$pdf->Cell(152, 6, '', 0, 1, 'C');

// Grain Size Test Result
$pdf->SetXY(375, 406);
$pdf->Cell(40, 4, 'Passed', 0, 1, 'C');

// Comments and Observations
$pdf->SetXY(40, 505);
$pdf->MultiCell(145, 4, $Search['Comments'], 0, 'L');
$pdf->SetXY(192, 505);
$pdf->MultiCell(145, 4, '', 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 30, 320, 230, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
