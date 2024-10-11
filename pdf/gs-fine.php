<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_fine', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

// Define manualmente el tamaño de página
$pdf->AddPage('P', array(550, 440));

// Importar una página de otro PDF
$pdf->setSourceFile('PV-F-83830_Laboratory sieve Grain size for Fine Aggregates_Rev 1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(73, 64);
$pdf->Cell(21, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(73, 70);
$pdf->Cell(21, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(210, 57);
$pdf->Cell(21, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(210, 64);
$pdf->Cell(21, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(210, 70);
$pdf->Cell(21, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(288, 57);
$pdf->Cell(21, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(288, 64);
$pdf->Cell(21, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(288, 70);
$pdf->Cell(21, 6, $Search['Split_Method'], 0, 1, 'C');

$pdf->SetXY(72, 90);
$pdf->Cell(21, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(72, 101);
$pdf->Cell(21, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(72, 111);
$pdf->Cell(21, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(72, 120);
$pdf->Cell(21, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(210, 90);
$pdf->Cell(21, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(210, 101);
$pdf->Cell(21, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(210, 111);
$pdf->Cell(21, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(210, 120);
$pdf->Cell(21, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(288, 90);
$pdf->Cell(21, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(288, 101);
$pdf->Cell(21, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(288, 111);
$pdf->Cell(21, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(288, 120);
$pdf->Cell(21, 6, $Search['East'], 0, 1, 'C');

$pdf->SetXY(385, 56);
$pdf->Cell(21, 6, "", 0, 1, 'C');
$pdf->SetXY(385, 63);
$pdf->Cell(21, 6, "", 0, 1, 'C');
$pdf->SetXY(385, 69);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(341, 75);
$pdf->Cell(25, 6, "", 0, 1, 'C');
$pdf->SetXY(341, 81);
$pdf->Cell(25, 5, "", 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(130, 146);
$pdf->Cell(35, 6, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(130, 152);
$pdf->Cell(35, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(130, 158);
$pdf->Cell(35, 6, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(130, 164.5);
$pdf->Cell(35, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(130, 170);
$pdf->Cell(35, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(130, 176);
$pdf->Cell(35, 6, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(130, 183);
$pdf->Cell(35, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');


// Reactivity Test Method FM13-007
$pdf->SetXY(130, 202);
$pdf->Cell(35, 6, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(130, 208);
$pdf->Cell(35, 6, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 214);
$pdf->Cell(35, 6, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 220);
$pdf->Cell(35, 6, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 226);
$pdf->Cell(35, 6, $Search['D_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 233);
$pdf->Cell(35, 6, $Search['E_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 240);
$pdf->Cell(35, 6, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 245);
$pdf->Cell(35, 6, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(130, 257);
$pdf->Cell(35, 6, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(251, 152);
$pdf->Cell(41, 6, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(292, 152);
$pdf->Cell(29, 6, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(321, 152);
$pdf->Cell(25, 6, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(346, 152);
$pdf->Cell(37, 6, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(383, 152);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 158);
$pdf->Cell(41, 6, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(292, 158);
$pdf->Cell(29, 6, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(321, 158);
$pdf->Cell(25, 6, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(346, 158);
$pdf->Cell(37, 6, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(383, 158);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 164);
$pdf->Cell(41, 6, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(292, 164);
$pdf->Cell(29, 6, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(321, 164);
$pdf->Cell(25, 6, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(346, 164);
$pdf->Cell(37, 6, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(383, 164);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 170);
$pdf->Cell(41, 6, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(292, 170);
$pdf->Cell(29, 6, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(321, 170);
$pdf->Cell(25, 6, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(346, 170);
$pdf->Cell(37, 6, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(383, 170);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 176);
$pdf->Cell(41, 6, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(292, 176);
$pdf->Cell(29, 6, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(321, 176);
$pdf->Cell(25, 6, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(346, 176);
$pdf->Cell(37, 6, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(383, 176);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 183);
$pdf->Cell(41, 6, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(292, 183);
$pdf->Cell(29, 6, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(321, 183);
$pdf->Cell(25, 6, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(346, 183);
$pdf->Cell(37, 6, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(383, 183);
$pdf->Cell(21, 6, "", 0, 1, 'C');

$pdf->SetXY(251, 189);
$pdf->Cell(41, 6, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(292, 189);
$pdf->Cell(29, 6, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(321, 189);
$pdf->Cell(25, 6, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(346, 189);
$pdf->Cell(37, 6, $Search['Pass7'], 0, 1, 'C');

$pdf->SetXY(251, 195);
$pdf->Cell(41, 6, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(292, 195);
$pdf->Cell(29, 6, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(321, 195);
$pdf->Cell(25, 6, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(346, 195);
$pdf->Cell(37, 6, $Search['Pass8'], 0, 1, 'C');

$pdf->SetXY(251, 202);
$pdf->Cell(41, 6, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(292, 202);
$pdf->Cell(29, 6, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(321, 202);
$pdf->Cell(25, 6, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(346, 202);
$pdf->Cell(37, 6, $Search['Pass9'], 0, 1, 'C');

$pdf->SetXY(251, 208);
$pdf->Cell(41, 6, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(292, 208);
$pdf->Cell(29, 6, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(321, 208);
$pdf->Cell(25, 6, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(346, 208);
$pdf->Cell(37, 6, $Search['Pass10'], 0, 1, 'C');

$pdf->SetXY(251, 215);
$pdf->Cell(41, 6, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(292, 215);
$pdf->Cell(29, 6, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(321, 215);
$pdf->Cell(25, 6, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(346, 215);
$pdf->Cell(37, 6, $Search['Pass11'], 0, 1, 'C');

$pdf->SetXY(251, 220);
$pdf->Cell(41, 6, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(292, 220);
$pdf->Cell(29, 6, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(321, 220);
$pdf->Cell(25, 6, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(346, 220);
$pdf->Cell(37, 6, $Search['Pass12'], 0, 1, 'C');

$pdf->SetXY(251, 227);
$pdf->Cell(41, 6, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(292, 227);
$pdf->Cell(29, 6, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(321, 227);
$pdf->Cell(25, 6, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(346, 227);
$pdf->Cell(37, 6, $Search['Pass13'], 0, 1, 'C');

$pdf->SetXY(251, 233);
$pdf->Cell(41, 6, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(292, 233);
$pdf->Cell(29, 6, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(321, 233);
$pdf->Cell(25, 6, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(346, 233);
$pdf->Cell(37, 6, $Search['Pass14'], 0, 1, 'C');

$pdf->SetXY(251, 239);
$pdf->Cell(41, 6, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(292, 239);
$pdf->Cell(29, 6, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(321, 239);
$pdf->Cell(25, 6, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(346, 239);
$pdf->Cell(37, 6, $Search['Pass15'], 0, 1, 'C');

$pdf->SetXY(251, 245);
$pdf->Cell(41, 6, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(292, 245);
$pdf->Cell(29, 6, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(321, 245);
$pdf->Cell(25, 6, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(346, 245);
$pdf->Cell(37, 6, $Search['Pass16'], 0, 1, 'C');

$pdf->SetXY(251, 251);
$pdf->Cell(41, 6, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(292, 251);
$pdf->Cell(29, 6, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(321, 251);
$pdf->Cell(25, 6, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(346, 251);
$pdf->Cell(37, 6, $Search['Pass17'], 0, 1, 'C');

$pdf->SetXY(251, 257);
$pdf->Cell(41, 6, $Search['WtRet18'], 0, 1, 'C');
$pdf->SetXY(292, 257);
$pdf->Cell(29, 6, $Search['Ret18'], 0, 1, 'C');
$pdf->SetXY(321, 257);
$pdf->Cell(25, 6, $Search['CumRet18'], 0, 1, 'C');
$pdf->SetXY(346, 257);
$pdf->Cell(37, 6, $Search['Pass18'], 0, 1, 'C');

$pdf->SetXY(251, 263.5);
$pdf->Cell(41, 6, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(292, 263.5);
$pdf->Cell(29, 6, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(251, 270);
$pdf->Cell(41, 6, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(292, 270);
$pdf->Cell(29, 6, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(321, 270);
$pdf->Cell(25, 6, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(346, 270);
$pdf->Cell(37, 6, $Search['TotalPass'], 0, 1, 'C');



// Summary Grain Size Distribution Parameter

$pdf->SetXY(346, 288);
$pdf->Cell(37, 6, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(346, 293);
$pdf->Cell(37, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(346, 299);
$pdf->Cell(37, 6, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(346, 305.5);
$pdf->Cell(37, 6, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(346, 311);
$pdf->Cell(37, 6, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(346, 318);
$pdf->Cell(37, 6, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(346, 324);
$pdf->Cell(37, 6, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(346, 330);
$pdf->Cell(37, 6, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(346, 336.5);
$pdf->Cell(37, 6, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(346, 343);
$pdf->Cell(37, 6, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(346, 349);
$pdf->Cell(37, 5, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS
$pdf->SetXY(250, 366);
$pdf->Cell(154, 6, "", 0, 1, 'C');
$pdf->SetXY(346, 392);
$pdf->Cell(37, 6, "", 0, 1, 'C');

$pdf->SetXY(44, 438);
$pdf->MultiCell(360, 4, $Search['Comments'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 20, 280, 230, 0, 'PNG');
unlink($tempFile);


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>