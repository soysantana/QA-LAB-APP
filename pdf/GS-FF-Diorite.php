<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

// Leer JSON recibido
$input = json_decode(file_get_contents('php://input'), true);
$GrainSizeChart = $input['GrainSizeChart'] ?? null;

$Search = find_by_id('grain_size_fine', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(600, 500));

$pdf->setSourceFile('template/PV-F-02272 Laboratory sieve Grain size for Diorite Fine Filter_Rev 0.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pass3p8 = $Search['Pass11'];
$passn4 = $Search['Pass12'];
$passn10 = $Search['Pass13'];
$passn16 = $Search['Pass14'];
$passn50 = $Search['Pass16'];
$passn60 = $Search['Pass17'];
$passn200 = $Search['Pass18'];

// Condición para "Acepted"
if (
    $pass3p8 == 100 &&
    $passn4 >= 95 && $passn4 <= 100 &&
    $passn10 >= 75 && $passn10 <= 100 &&
    $passn16 >= 50 && $passn16 <= 85 &&
    $passn50 >= 5 && $passn50 <= 30 &&
    $passn60 >= 0 && $passn60 <= 25 &&
    $passn200 >= 0 && $passn200 <= 2
) {
    $resultado = 'Acepted';
} else {
    $resultado = 'Rejected';
}

$pdf->SetFont('Arial', 'B', 10);

//Information for the essay
$pdf->SetXY(105, 94);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(105, 101);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(105, 108);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(105, 124);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(105, 135);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(105, 145);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(105, 155);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(210, 94);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(210, 101);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(210, 108);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(210, 124);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(210, 135);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(210, 145);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(210, 155);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(290, 94);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(290, 101);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(290, 108);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(290, 124);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(290, 135);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(290, 145);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(290, 155);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetXY(400, 94);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(130, 182);
$pdf->Cell(47, 6, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(130, 189);
$pdf->Cell(47, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(130, 195);
$pdf->Cell(47, 6, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(130, 201);
$pdf->Cell(47, 6, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(130, 207);
$pdf->Cell(47, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(130, 214);
$pdf->Cell(47, 6, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(130, 220);
$pdf->Cell(47, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');

// Reactivity Test Method FM13-007
$pdf->SetXY(130, 240);
$pdf->Cell(47, 4, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(130, 246);
$pdf->Cell(47, 4, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 252);
$pdf->Cell(47, 4, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 258);
$pdf->Cell(47, 4, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 264);
$pdf->Cell(47, 4, $Search['D_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 271);
$pdf->Cell(47, 4, $Search['E_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 277);
$pdf->Cell(47, 4, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(130, 284);
$pdf->Cell(47, 4, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(130, 295);
$pdf->Cell(47, 4, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(258, 190);
$pdf->Cell(29, 4, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(299, 190);
$pdf->Cell(29, 4, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(338, 190);
$pdf->Cell(21, 4, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(365, 190);
$pdf->Cell(43, 4, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(407, 190);
$pdf->Cell(43, 4, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(258, 196);
$pdf->Cell(29, 4, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(299, 196);
$pdf->Cell(29, 4, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(338, 196);
$pdf->Cell(21, 4, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(365, 196);
$pdf->Cell(43, 4, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(407, 196);
$pdf->Cell(43, 4, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(258, 202);
$pdf->Cell(29, 4, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(299, 202);
$pdf->Cell(29, 4, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(338, 202);
$pdf->Cell(21, 4, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(365, 202);
$pdf->Cell(43, 4, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(407, 202);
$pdf->Cell(43, 4, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(258, 208);
$pdf->Cell(29, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(299, 208);
$pdf->Cell(29, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(338, 208);
$pdf->Cell(21, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(365, 208);
$pdf->Cell(43, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(407, 208);
$pdf->Cell(43, 4, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(258, 214);
$pdf->Cell(29, 4, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(299, 214);
$pdf->Cell(29, 4, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(338, 214);
$pdf->Cell(21, 4, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(365, 214);
$pdf->Cell(43, 4, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(407, 214);
$pdf->Cell(43, 4, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(258, 221);
$pdf->Cell(29, 4, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(299, 221);
$pdf->Cell(29, 4, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(338, 221);
$pdf->Cell(21, 4, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(365, 221);
$pdf->Cell(43, 4, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(407, 221);
$pdf->Cell(43, 4, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(258, 227);
$pdf->Cell(29, 4, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(299, 227);
$pdf->Cell(29, 4, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(338, 227);
$pdf->Cell(21, 4, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(365, 227);
$pdf->Cell(43, 4, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(407, 227);
$pdf->Cell(43, 4, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(258, 233);
$pdf->Cell(29, 4, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(299, 233);
$pdf->Cell(29, 4, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(338, 233);
$pdf->Cell(21, 4, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(365, 233);
$pdf->Cell(43, 4, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(407, 233);
$pdf->Cell(43, 4, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(258, 240);
$pdf->Cell(29, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(299, 240);
$pdf->Cell(29, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(338, 240);
$pdf->Cell(21, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(365, 240);
$pdf->Cell(43, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(407, 240);
$pdf->Cell(43, 4, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(258, 246);
$pdf->Cell(29, 4, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(299, 246);
$pdf->Cell(29, 4, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(338, 246);
$pdf->Cell(21, 4, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(365, 246);
$pdf->Cell(43, 4, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(407, 246);
$pdf->Cell(43, 4, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(258, 252);
$pdf->Cell(29, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(299, 252);
$pdf->Cell(29, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(338, 252);
$pdf->Cell(21, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(365, 252);
$pdf->Cell(43, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(407, 252);
$pdf->Cell(43, 4, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(258, 258);
$pdf->Cell(29, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(299, 258);
$pdf->Cell(29, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(338, 258);
$pdf->Cell(21, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(365, 258);
$pdf->Cell(43, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(407, 258);
$pdf->Cell(43, 4, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(258, 264);
$pdf->Cell(29, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(299, 264);
$pdf->Cell(29, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(338, 264);
$pdf->Cell(21, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(365, 264);
$pdf->Cell(43, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(407, 264);
$pdf->Cell(43, 4, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(258, 271);
$pdf->Cell(29, 4, $Search['WtRet14'], 0, 1, 'C');
$pdf->SetXY(299, 271);
$pdf->Cell(29, 4, $Search['Ret14'], 0, 1, 'C');
$pdf->SetXY(338, 271);
$pdf->Cell(21, 4, $Search['CumRet14'], 0, 1, 'C');
$pdf->SetXY(365, 271);
$pdf->Cell(43, 4, $Search['Pass14'], 0, 1, 'C');
$pdf->SetXY(407, 271);
$pdf->Cell(43, 4, $Search['Specs14'], 0, 1, 'C');

$pdf->SetXY(258, 278);
$pdf->Cell(29, 4, $Search['WtRet15'], 0, 1, 'C');
$pdf->SetXY(299, 278);
$pdf->Cell(29, 4, $Search['Ret15'], 0, 1, 'C');
$pdf->SetXY(338, 278);
$pdf->Cell(21, 4, $Search['CumRet15'], 0, 1, 'C');
$pdf->SetXY(365, 278);
$pdf->Cell(43, 4, $Search['Pass15'], 0, 1, 'C');
$pdf->SetXY(407, 278);
$pdf->Cell(43, 4, $Search['Specs15'], 0, 1, 'C');

$pdf->SetXY(258, 284);
$pdf->Cell(29, 4, $Search['WtRet16'], 0, 1, 'C');
$pdf->SetXY(299, 284);
$pdf->Cell(29, 4, $Search['Ret16'], 0, 1, 'C');
$pdf->SetXY(338, 284);
$pdf->Cell(21, 4, $Search['CumRet16'], 0, 1, 'C');
$pdf->SetXY(365, 284);
$pdf->Cell(43, 4, $Search['Pass16'], 0, 1, 'C');
$pdf->SetXY(407, 284);
$pdf->Cell(43, 4, $Search['Specs16'], 0, 1, 'C');

$pdf->SetXY(258, 289.5);
$pdf->Cell(29, 4, $Search['WtRet17'], 0, 1, 'C');
$pdf->SetXY(299, 289.5);
$pdf->Cell(29, 4, $Search['Ret17'], 0, 1, 'C');
$pdf->SetXY(338, 289.5);
$pdf->Cell(21, 4, $Search['CumRet17'], 0, 1, 'C');
$pdf->SetXY(365, 289.5);
$pdf->Cell(43, 4, $Search['Pass17'], 0, 1, 'C');
$pdf->SetXY(407, 289.5);
$pdf->Cell(43, 4, $Search['Specs17'], 0, 1, 'C');

$pdf->SetXY(258, 296);
$pdf->Cell(29, 4, $Search['WtRet18'], 0, 1, 'C');
$pdf->SetXY(299, 296);
$pdf->Cell(29, 4, $Search['Ret18'], 0, 1, 'C');
$pdf->SetXY(338, 296);
$pdf->Cell(21, 4, $Search['CumRet18'], 0, 1, 'C');
$pdf->SetXY(365, 296);
$pdf->Cell(43, 4, $Search['Pass18'], 0, 1, 'C');
$pdf->SetXY(407, 296);
$pdf->Cell(43, 4, $Search['Specs18'], 0, 1, 'C');

$pdf->SetXY(258, 302);
$pdf->Cell(29, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(299, 302);
$pdf->Cell(29, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(258, 308);
$pdf->Cell(29, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(299, 308);
$pdf->Cell(29, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(338, 308);
$pdf->Cell(21, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(365, 308);
$pdf->Cell(43, 4, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter
$pdf->SetXY(365, 327);
$pdf->Cell(43, 3, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(365, 333);
$pdf->Cell(43, 3, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(365, 339);
$pdf->Cell(43, 3, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(365, 345);
$pdf->Cell(43, 3, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(365, 351);
$pdf->Cell(43, 3, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(365, 357);
$pdf->Cell(43, 3, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(365, 363);
$pdf->Cell(43, 3, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(365, 369);
$pdf->Cell(43, 3, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(365, 375);
$pdf->Cell(43, 3, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(365, 381);
$pdf->Cell(43, 3, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(365, 387);
$pdf->Cell(43, 3, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS
$pdf->SetXY(280, 404);
$pdf->Cell(152, 6, $Search['ClassificationUSCS1'], 0, 1, 'C');
$pdf->SetXY(280, 410);
$pdf->Cell(152, 6, $Search['ClassificationUSCS2'], 0, 1, 'C');

// Grain Size Test Results
$pdf->SetXY(310, 430);
$pdf->Cell(152, 4, $resultado, 0, 1, 'C');

// Comments and Observations
$pdf->SetXY(54, 480);
$pdf->MultiCell(360, 4, $Search['Comments'], 0, 'L');
$pdf->SetXY(54, 520);
$pdf->MultiCell(360, 4, $Search['FieldComment'], 0, 'L');

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

insertarImagenBase64($pdf, $GrainSizeChart, 35, 330, 210, 0); // ajusta X, Y, ancho, alto

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'GS' . '-' . $Search['Material_Type'] . '.pdf', 'I');
