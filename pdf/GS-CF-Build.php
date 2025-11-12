<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

// Leer JSON recibido
$input = json_decode(file_get_contents('php://input'), true);
$GrainSizeChart = $input['GrainSizeChart'] ?? null;

$Search = find_by_id('grain_size_coarse', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(620, 500));

$pdf->setSourceFile('template/PV-F01709 Laboratory Sieve Grain Size Distribution and Acid Reactivity for Coarse Filter-CF.pdf');
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

$pdf->SetFont('Arial', '', 10);

//Information for the essay
$pdf->SetXY(100, 80);
$pdf->Cell(30, 4, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(100, 96);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(100, 102);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(100, 109);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(100, 130);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(100, 137);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(100, 143);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(100, 150);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(210, 80);
$pdf->Cell(30, 6, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(210, 96);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(210, 102);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(210, 109);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(210, 130);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(210, 137);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(210, 143);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(210, 150);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(330, 80);
$pdf->Cell(30, 6, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(330, 96);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(330, 102);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(330, 109);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(330, 130);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(330, 137);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(330, 143);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(330, 150);
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
$pdf->SetXY(152, 172);
$pdf->Cell(47, 6, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(152, 179);
$pdf->Cell(47, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(152, 185);
$pdf->Cell(47, 6, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(152, 191);
$pdf->Cell(47, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(152, 197);
$pdf->Cell(47, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(152, 203);
$pdf->Cell(47, 6, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(152, 210);
$pdf->Cell(47, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');

// Reactivity Test Method FM13-007
$pdf->SetXY(152, 230);
$pdf->Cell(47, 4, $Search['Total_Sample_Weight'], 0, 1, 'C');
$pdf->SetXY(152, 236);
$pdf->Cell(47, 4, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(152, 242);
$pdf->Cell(47, 4, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 248);
$pdf->Cell(47, 4, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 255);
$pdf->Cell(47, 4, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 262);
$pdf->Cell(47, 4, "", 0, 1, 'C');
$pdf->SetXY(152, 268);
$pdf->Cell(47, 4, "", 0, 1, 'C');
$pdf->SetXY(152, 274);
$pdf->Cell(47, 4, "", 0, 1, 'C');
$pdf->SetXY(152, 279);
$pdf->Cell(47, 4, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(152, 286);
$pdf->Cell(47, 4, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(132, 298.5);
$pdf->Cell(47, 4, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(302, 180);
$pdf->Cell(29, 4, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(335, 180);
$pdf->Cell(29, 4, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(366, 180);
$pdf->Cell(21, 4, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(392, 180);
$pdf->Cell(43, 4, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(436, 180);
$pdf->Cell(30, 4, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(302, 186);
$pdf->Cell(29, 4, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(335, 186);
$pdf->Cell(29, 4, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(366, 186);
$pdf->Cell(21, 4, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(392, 186);
$pdf->Cell(43, 4, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(436, 186);
$pdf->Cell(30, 4, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(302, 192);
$pdf->Cell(29, 4, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(335, 192);
$pdf->Cell(29, 4, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(366, 192);
$pdf->Cell(21, 4, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(392, 192);
$pdf->Cell(43, 4, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(436, 192);
$pdf->Cell(30, 4, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(302, 198);
$pdf->Cell(29, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(335, 198);
$pdf->Cell(29, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(366, 198);
$pdf->Cell(21, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(392, 198);
$pdf->Cell(43, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(436, 198);
$pdf->Cell(30, 4, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(302, 204);
$pdf->Cell(29, 4, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(335, 204);
$pdf->Cell(29, 4, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(366, 204);
$pdf->Cell(21, 4, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(392, 204);
$pdf->Cell(43, 4, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(436, 204);
$pdf->Cell(30, 4, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(302, 211);
$pdf->Cell(29, 4, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(335, 211);
$pdf->Cell(29, 4, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(366, 211);
$pdf->Cell(21, 4, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(392, 211);
$pdf->Cell(43, 4, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(436, 211);
$pdf->Cell(30, 4, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(302, 217);
$pdf->Cell(29, 4, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(335, 217);
$pdf->Cell(29, 4, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(366, 217);
$pdf->Cell(21, 4, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(392, 217);
$pdf->Cell(43, 4, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(436, 217);
$pdf->Cell(30, 4, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(302, 223);
$pdf->Cell(29, 4, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(335, 223);
$pdf->Cell(29, 4, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(366, 223);
$pdf->Cell(21, 4, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(392, 223);
$pdf->Cell(43, 4, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(436, 223);
$pdf->Cell(30, 4, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(302, 230);
$pdf->Cell(29, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(335, 230);
$pdf->Cell(29, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(366, 230);
$pdf->Cell(21, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(392, 230);
$pdf->Cell(43, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(436, 230);
$pdf->Cell(30, 4, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(302, 236);
$pdf->Cell(29, 4, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(335, 236);
$pdf->Cell(29, 4, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(366, 236);
$pdf->Cell(21, 4, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(392, 236);
$pdf->Cell(43, 4, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(436, 236);
$pdf->Cell(30, 4, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(302, 242.5);
$pdf->Cell(29, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(335, 242.5);
$pdf->Cell(29, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(366, 242.5);
$pdf->Cell(21, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(392, 242.5);
$pdf->Cell(43, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(436, 242.5);
$pdf->Cell(30, 4, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(302, 249);
$pdf->Cell(29, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(335, 249);
$pdf->Cell(29, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(366, 249);
$pdf->Cell(21, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(392, 249);
$pdf->Cell(43, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(436, 249);
$pdf->Cell(30, 4, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(302, 255);
$pdf->Cell(29, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(335, 255);
$pdf->Cell(29, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(366, 255);
$pdf->Cell(21, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(392, 255);
$pdf->Cell(43, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(436, 255);
$pdf->Cell(30, 4, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(302, 262);
$pdf->Cell(29, 4, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(335, 262);
$pdf->Cell(29, 4, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(366, 262);
$pdf->Cell(21, 4, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(392, 262);
$pdf->Cell(43, 4, $Search['Pass14'], 0, 1, 'C');
$pdf->SetXY(436, 262);
$pdf->Cell(30, 4, $Search['Specs14'], 0, 1, 'C');

$pdf->SetXY(302, 268);
$pdf->Cell(29, 4, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(335, 268);
$pdf->Cell(29, 4, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(366, 268);
$pdf->Cell(21, 4, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(392, 268);
$pdf->Cell(43, 4, $Search['Pass15'], 0, 1, 'C');
$pdf->SetXY(436, 268);
$pdf->Cell(30, 4, $Search['Specs15'], 0, 1, 'C');

$pdf->SetXY(302, 273.5);
$pdf->Cell(29, 4, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(335, 273.5);
$pdf->Cell(29, 4, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(366, 273.5);
$pdf->Cell(21, 4, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(392, 273.5);
$pdf->Cell(43, 4, $Search['Pass16'], 0, 1, 'C');
$pdf->SetXY(436, 273.5);
$pdf->Cell(30, 4, $Search['Specs16'], 0, 1, 'C');

$pdf->SetXY(302, 279);
$pdf->Cell(29, 4, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(335, 279);
$pdf->Cell(29, 4, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(366, 279);
$pdf->Cell(21, 4, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(392, 279);
$pdf->Cell(43, 4, $Search['Pass17'], 0, 1, 'C');
$pdf->SetXY(436, 273.5);
$pdf->Cell(30, 4, $Search['Specs17'], 0, 1, 'C');

$pdf->SetXY(302, 286);
$pdf->Cell(29, 4, $Search['WtRet18'], 0, 1, 'C');
$pdf->SetXY(335, 286);
$pdf->Cell(29, 4, $Search['Ret18'], 0, 1, 'C');
$pdf->SetXY(366, 286);
$pdf->Cell(21, 4, $Search['CumRet18'], 0, 1, 'C');
$pdf->SetXY(392, 286);
$pdf->Cell(43, 4, $Search['Pass18'], 0, 1, 'C');
$pdf->SetXY(436, 286);
$pdf->Cell(30, 4, $Search['Specs18'], 0, 1, 'C');

$pdf->SetXY(302, 292);
$pdf->Cell(29, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(335, 292);
$pdf->Cell(29, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(302, 299);
$pdf->Cell(29, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(335, 299);
$pdf->Cell(29, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(366, 299);
$pdf->Cell(21, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(392, 299);
$pdf->Cell(43, 4, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter
$pdf->SetXY(392, 317);
$pdf->Cell(43, 4, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(392, 322);
$pdf->Cell(43, 4, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(392, 328);
$pdf->Cell(43, 4, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(392, 335);
$pdf->Cell(43, 4, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(392, 341);
$pdf->Cell(43, 4, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(392, 347);
$pdf->Cell(43, 4, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(392, 353);
$pdf->Cell(43, 4, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(392, 360);
$pdf->Cell(43, 4, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(392, 366);
$pdf->Cell(43, 4, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(392, 371);
$pdf->Cell(43, 4, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(392, 377);
$pdf->Cell(43, 4, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS
$pdf->SetXY(300, 394);
$pdf->Cell(165, 6, $Search['ClassificationUSCS1'], 0, 1, 'C');
$pdf->SetXY(300, 401);
$pdf->Cell(165, 6, $Search['ClassificationUSCS2'], 0, 1, 'C');

$pdf->SetXY(52, 532);
$pdf->MultiCell(145, 4, utf8_decode($Search['Comments']), 0, 'L');

$pdf->SetXY(200, 532);
$pdf->MultiCell(145, 4, utf8_decode($Search['FieldComment']), 0, 'L');

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

insertarImagenBase64($pdf, $GrainSizeChart, 30, 320, 230, 170); // ajusta X, Y, ancho, alto

// Condición para validacion
if (
    $pass1p5 == 100 &&
    $pass1 >= 86.5 && $pass1 <= 100 &&
    $pass3p4 >= 69.5 && $pass3p4 <= 100 &&
    $pass3p8 >= 32.5 && $pass3p8 <= 100 &&
    $passn4 >= 6.5 && $passn4 <= 60.4 &&
    $passn10 >= 0 && $passn10 <= 15.4 &&
    $passn20 >= 0 && $passn20 <= 7.4 &&
    $passn200 >= 0 && $passn200 <= 5.4
) {
    $resultado = 'Accepted';
} else {
    $resultado = 'Rejected';
    $pdf->SetTextColor(255, 0, 0); // Rojo
}

$pdf->SetXY(391, 420);
$pdf->Cell(75, 6, $resultado, 0, 1, 'C');

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'GS' . '-' . $Search['Material_Type'] . '.pdf', 'I');
