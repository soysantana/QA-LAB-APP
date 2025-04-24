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

// Define manualmente el tamaño de página
$pdf->AddPage('P', array(550, 440));

// Importar una página de otro PDF
$pdf->setSourceFile('template/PV-F-83830 Laboratory sieve Grain size for Fine Aggregates.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(73, 55);
$pdf->Cell(21, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(73, 62);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(73, 68);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(72, 88);
$pdf->Cell(21, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(72, 98);
$pdf->Cell(21, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(72, 108);
$pdf->Cell(21, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(72, 118);
$pdf->Cell(21, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(206, 55);
$pdf->Cell(21, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(206, 62);
$pdf->Cell(21, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(206, 68);
$pdf->Cell(21, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(206, 88);
$pdf->Cell(21, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(206, 98);
$pdf->Cell(21, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(206, 108);
$pdf->Cell(21, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(206, 118);
$pdf->Cell(21, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(280, 55);
$pdf->Cell(21, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(280, 62);
$pdf->Cell(21, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(280, 68);
$pdf->Cell(21, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(280, 88);
$pdf->Cell(21, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(280, 98);
$pdf->Cell(21, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(280, 108);
$pdf->Cell(21, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(280, 118);
$pdf->Cell(21, 6, $Search['East'], 0, 1, 'C');

$pdf->SetXY(380, 55);
$pdf->Cell(21, 6, $Search['Sample_Date'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(126, 143);
$pdf->Cell(35, 6, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(126, 149);
$pdf->Cell(35, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(126, 155);
$pdf->Cell(35, 6, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(126, 161);
$pdf->Cell(35, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(126, 167);
$pdf->Cell(35, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(126, 173);
$pdf->Cell(35, 6, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(126, 179);
$pdf->Cell(35, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');


// Reactivity Test Method FM13-007
$pdf->SetXY(126, 198);
$pdf->Cell(35, 6, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(126, 205);
$pdf->Cell(35, 6, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 211);
$pdf->Cell(35, 6, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 217);
$pdf->Cell(35, 6, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 223);
$pdf->Cell(35, 6, $Search['D_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 229);
$pdf->Cell(35, 6, $Search['E_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 235);
$pdf->Cell(35, 6, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(126, 241);
$pdf->Cell(35, 6, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(126, 253);
$pdf->Cell(35, 6, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(245, 149);
$pdf->Cell(41, 6, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(287, 149);
$pdf->Cell(29, 6, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(317, 149);
$pdf->Cell(25, 6, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(343, 149);
$pdf->Cell(37, 6, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(378, 149);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(245, 155);
$pdf->Cell(41, 6, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(287, 155);
$pdf->Cell(29, 6, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(317, 155);
$pdf->Cell(25, 6, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(343, 155);
$pdf->Cell(37, 6, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(378, 155);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(245, 161);
$pdf->Cell(41, 6, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(287, 161);
$pdf->Cell(29, 6, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(317, 161);
$pdf->Cell(25, 6, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(343, 161);
$pdf->Cell(37, 6, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(378, 161);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(245, 167);
$pdf->Cell(41, 6, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(287, 167);
$pdf->Cell(29, 6, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(317, 167);
$pdf->Cell(25, 6, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(343, 167);
$pdf->Cell(37, 6, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(378, 167);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(245, 173);
$pdf->Cell(41, 6, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(287, 173);
$pdf->Cell(29, 6, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(317, 173);
$pdf->Cell(25, 6, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(343, 173);
$pdf->Cell(37, 6, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(378, 173);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(245, 179);
$pdf->Cell(41, 6, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(287, 179);
$pdf->Cell(29, 6, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(317, 179);
$pdf->Cell(25, 6, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(343, 179);
$pdf->Cell(37, 6, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(378, 179);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(245, 185);
$pdf->Cell(41, 6, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(287, 185);
$pdf->Cell(29, 6, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(317, 185);
$pdf->Cell(25, 6, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(343, 185);
$pdf->Cell(37, 6, $Search['Pass7'], 0, 1, 'C');

$pdf->SetXY(245, 192);
$pdf->Cell(41, 6, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(287, 192);
$pdf->Cell(29, 6, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(317, 192);
$pdf->Cell(25, 6, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(343, 192);
$pdf->Cell(37, 6, $Search['Pass8'], 0, 1, 'C');

$pdf->SetXY(245, 198);
$pdf->Cell(41, 6, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(287, 198);
$pdf->Cell(29, 6, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(317, 198);
$pdf->Cell(25, 6, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(343, 198);
$pdf->Cell(37, 6, $Search['Pass9'], 0, 1, 'C');

$pdf->SetXY(245, 204);
$pdf->Cell(41, 6, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(287, 204);
$pdf->Cell(29, 6, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(317, 204);
$pdf->Cell(25, 6, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(343, 204);
$pdf->Cell(37, 6, $Search['Pass10'], 0, 1, 'C');

$pdf->SetXY(245, 211);
$pdf->Cell(41, 6, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(287, 211);
$pdf->Cell(29, 6, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(317, 211);
$pdf->Cell(25, 6, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(343, 211);
$pdf->Cell(37, 6, $Search['Pass11'], 0, 1, 'C');

$pdf->SetXY(245, 216);
$pdf->Cell(41, 6, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(287, 216);
$pdf->Cell(29, 6, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(317, 216);
$pdf->Cell(25, 6, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(343, 216);
$pdf->Cell(37, 6, $Search['Pass12'], 0, 1, 'C');

$pdf->SetXY(245, 222);
$pdf->Cell(41, 6, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(287, 222);
$pdf->Cell(29, 6, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(317, 222);
$pdf->Cell(25, 6, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(343, 222);
$pdf->Cell(37, 6, $Search['Pass13'], 0, 1, 'C');

$pdf->SetXY(245, 229);
$pdf->Cell(41, 6, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(287, 229);
$pdf->Cell(29, 6, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(317, 229);
$pdf->Cell(25, 6, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(343, 229);
$pdf->Cell(37, 6, $Search['Pass14'], 0, 1, 'C');

$pdf->SetXY(245, 236);
$pdf->Cell(41, 6, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(287, 236);
$pdf->Cell(29, 6, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(317, 236);
$pdf->Cell(25, 6, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(343, 236);
$pdf->Cell(37, 6, $Search['Pass15'], 0, 1, 'C');

$pdf->SetXY(245, 241);
$pdf->Cell(41, 6, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(287, 241);
$pdf->Cell(29, 6, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(317, 241);
$pdf->Cell(25, 6, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(343, 241);
$pdf->Cell(37, 6, $Search['Pass16'], 0, 1, 'C');

$pdf->SetXY(245, 247);
$pdf->Cell(41, 6, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(287, 247);
$pdf->Cell(29, 6, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(317, 247);
$pdf->Cell(25, 6, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(343, 247);
$pdf->Cell(37, 6, $Search['Pass17'], 0, 1, 'C');

$pdf->SetXY(245, 253);
$pdf->Cell(41, 6, $Search['WtRet18'], 0, 1, 'C');
$pdf->SetXY(287, 253);
$pdf->Cell(29, 6, $Search['Ret18'], 0, 1, 'C');
$pdf->SetXY(317, 253);
$pdf->Cell(25, 6, $Search['CumRet18'], 0, 1, 'C');
$pdf->SetXY(343, 253);
$pdf->Cell(37, 6, $Search['Pass18'], 0, 1, 'C');

$pdf->SetXY(245, 259);
$pdf->Cell(41, 6, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(287, 259);
$pdf->Cell(29, 6, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(245, 265);
$pdf->Cell(41, 6, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(287, 265);
$pdf->Cell(29, 6, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(317, 265);
$pdf->Cell(25, 6, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(343, 265);
$pdf->Cell(37, 6, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter
$pdf->SetXY(343, 284);
$pdf->Cell(37, 6, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(343, 289);
$pdf->Cell(37, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(343, 295);
$pdf->Cell(37, 6, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(343, 301);
$pdf->Cell(37, 6, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(343, 307);
$pdf->Cell(37, 6, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(343, 313);
$pdf->Cell(37, 6, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(343, 319);
$pdf->Cell(37, 6, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(343, 325);
$pdf->Cell(37, 6, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(343, 331);
$pdf->Cell(37, 6, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(343, 337);
$pdf->Cell(37, 6, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(343, 343);
$pdf->Cell(37, 5, $Search['Cu'], 0, 1, 'C');

// Fine Grained Classification using the USCS
$pdf->SetXY(250, 361);
$pdf->Cell(154, 6, "", 0, 1, 'C');
$pdf->SetXY(250, 368);
$pdf->Cell(154, 6, "", 0, 1, 'C');

// Grain Size Test Results
$pdf->SetXY(338, 387);
$pdf->Cell(45, 6, "", 0, 1, 'C');

// Comments and Observations
$pdf->SetXY(42, 438);
$pdf->MultiCell(350, 4, $Search['Comments'], 0, 'L');
$pdf->SetXY(42, 473);
$pdf->MultiCell(350, 4, "", 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 20, 278, 220, 0, 'PNG');
unlink($tempFile);


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
