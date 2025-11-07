<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

// Leer JSON recibido
$input = json_decode(file_get_contents('php://input'), true);
$GrainSizeChart = $input['GrainSizeChart'] ?? null;

$Search = find_by_id('grain_size_upstream_transition_fill', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(570, 480));

$pdf->setSourceFile('template/PV-F-81259 Laboratory Sieve Grain Size for UTF_Rev4.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 11);

$Sand = $Search['Sand'];
$Fines = $Search['Fines'];

$pdf->SetXY(361, 304);
$pdf->SetTextColor($Sand < 40 ? 255 : 0, $Sand < 40 ? 0 : 0, $Sand < 40 ? 0 : 0);
$pdf->Cell(29, 6, $Sand, 0, 1, 'C');
$pdf->SetXY(361, 311);
$pdf->SetTextColor($Fines > 4 ? 255 : 0, $Fines > 4 ? 0 : 0, $Fines > 4 ? 0 : 0);
$pdf->Cell(29, 6, $Fines, 0, 1, 'C');

// Condición para "Acepted"
if (
    $Sand >= 40 &&
    $Fines <= 4
) {
    $resultado = 'Acepted';
    $pdf->SetTextColor(0, 0, 0);
} else {
    $resultado = 'Rejected';
    $pdf->SetTextColor(255, 0, 0);
}

$pdf->SetXY(390, 394);
$pdf->Cell(65, 7, $resultado, 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0);

//Information for the essay
$pdf->SetXY(108, 106);
$pdf->Cell(30, 4, "PVDJ Soil Lab", 0, 1, 'C');
$pdf->SetXY(108, 113);
$pdf->Cell(30, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(108, 119);
$pdf->Cell(30, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(108, 136);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(108, 143);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(108, 150);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(108, 157);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(215, 106);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(215, 113);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(215, 119);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(215, 136);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(215, 143);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(215, 150);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(215, 157);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(316, 106);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(316, 113);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(316, 119);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(316, 136);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(316, 143);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(316, 150);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(316, 157);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetXY(442, 106);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');

// Testing Information
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY(163, 174);
$pdf->Cell(40, 5, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(163, 180);
$pdf->Cell(40, 5, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(163, 186);
$pdf->Cell(40, 5, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(163, 193);
$pdf->Cell(40, 5, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(163, 199);
$pdf->Cell(40, 5, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(163, 205);
$pdf->Cell(40, 5, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(163, 211);
$pdf->Cell(40, 5, $Search['Wt_Wash_Pan'], 0, 1, 'C');

// Reactivity Test Method FM13-007
$pdf->SetXY(163, 230);
$pdf->Cell(41, 5, $Search['Total_Sample_Weight'], 0, 1, 'C');
$pdf->SetXY(163, 237);
$pdf->Cell(41, 5, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(163, 243);
$pdf->Cell(41, 5, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(163, 249);
$pdf->Cell(41, 5, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(163, 255);
$pdf->Cell(41, 5, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(163, 262);
$pdf->Cell(41, 5, '', 0, 1, 'C');
$pdf->SetXY(163, 268);
$pdf->Cell(41, 5, '', 0, 1, 'C');
$pdf->SetXY(163, 274);
$pdf->Cell(41, 5, '', 0, 1, 'C');
$pdf->SetXY(163, 280);
$pdf->Cell(41, 5, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(163, 286);
$pdf->Cell(41, 5, $Search['Reaction_Strength_Result'], 0, 1, 'C');
$pdf->SetXY(163, 298);
$pdf->Cell(41, 5, $Search['Acid_Reactivity_Test_Result'], 0, 1, 'C');

// Gran size Distribution
$pdf->SetXY(290, 186);
$pdf->Cell(35, 6, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(326, 186);
$pdf->Cell(35, 6, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(360, 186);
$pdf->Cell(30, 6, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(391, 186);
$pdf->Cell(40, 6, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(431, 186);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 192);
$pdf->Cell(35, 6, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(326, 192);
$pdf->Cell(35, 6, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(360, 192);
$pdf->Cell(30, 6, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(391, 192);
$pdf->Cell(40, 6, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(431, 192);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 198);
$pdf->Cell(35, 6, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(326, 198);
$pdf->Cell(35, 6, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(360, 198);
$pdf->Cell(30, 6, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(391, 198);
$pdf->Cell(40, 6, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(431, 198);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 204);
$pdf->Cell(35, 6, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(326, 204);
$pdf->Cell(35, 6, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(360, 204);
$pdf->Cell(30, 6, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(391, 204);
$pdf->Cell(40, 6, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(431, 204);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 211);
$pdf->Cell(35, 6, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(326, 211);
$pdf->Cell(35, 6, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(360, 211);
$pdf->Cell(30, 6, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(391, 211);
$pdf->Cell(40, 6, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(431, 211);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 217);
$pdf->Cell(35, 6, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(326, 217);
$pdf->Cell(35, 6, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(360, 217);
$pdf->Cell(30, 6, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(391, 217);
$pdf->Cell(40, 6, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(431, 217);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 223);
$pdf->Cell(35, 6, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(326, 223);
$pdf->Cell(35, 6, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(360, 223);
$pdf->Cell(30, 6, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(391, 223);
$pdf->Cell(40, 6, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(431, 223);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 230);
$pdf->Cell(35, 6, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(326, 230);
$pdf->Cell(35, 6, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(360, 230);
$pdf->Cell(30, 6, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(391, 230);
$pdf->Cell(40, 6, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(431, 230);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 236);
$pdf->Cell(35, 6, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(326, 236);
$pdf->Cell(35, 6, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(360, 236);
$pdf->Cell(30, 6, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(391, 236);
$pdf->Cell(40, 6, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(431, 236);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 242.4);
$pdf->Cell(35, 6, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(326, 242.4);
$pdf->Cell(35, 6, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(360, 242.4);
$pdf->Cell(30, 6, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(391, 242.4);
$pdf->Cell(40, 6, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(431, 242.4);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 249);
$pdf->Cell(35, 6, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(326, 249);
$pdf->Cell(35, 6, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(360, 249);
$pdf->Cell(30, 6, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(391, 249);
$pdf->Cell(40, 6, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(431, 249);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 255);
$pdf->Cell(35, 6, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(326, 255);
$pdf->Cell(35, 6, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(360, 255);
$pdf->Cell(30, 6, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(391, 255);
$pdf->Cell(40, 6, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(431, 255);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 261);
$pdf->Cell(35, 6, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(326, 261);
$pdf->Cell(35, 6, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(360, 261);
$pdf->Cell(30, 6, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(391, 261);
$pdf->Cell(40, 6, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(431, 261);
$pdf->Cell(24, 6, '', 0, 1, 'C');

$pdf->SetXY(290, 268);
$pdf->Cell(35, 6, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(326, 268);
$pdf->Cell(35, 6, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(290, 274);
$pdf->Cell(35, 6, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(326, 274);
$pdf->Cell(35, 6, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(360, 274);
$pdf->Cell(30, 6, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(391, 274);
$pdf->Cell(40, 6, $Search['TotalPass'], 0, 1, 'C');

// Summary Grain Size Distribution Parameter
$pdf->SetXY(361, 292);
$pdf->Cell(29, 6, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(361, 298);
$pdf->Cell(29, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(391, 317);
$pdf->Cell(39, 5, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(391, 323);
$pdf->Cell(39, 5, $Search['D15'], 0, 1, 'C');
$pdf->SetXY(391, 328);
$pdf->Cell(39, 5, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(391, 334);
$pdf->Cell(39, 5, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(391, 340);
$pdf->Cell(39, 5, $Search['D85'], 0, 1, 'C');
$pdf->SetXY(391, 347);
$pdf->Cell(39, 5, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(391, 353);
$pdf->Cell(39, 5, $Search['Cu'], 0, 1, 'C');

//Coarse Grained Classification using the USCS
$pdf->SetXY(289, 371);
$pdf->Cell(166, 6, $Search['ClassificationUSCS1'], 1, 1, 'C');
$pdf->SetXY(289, 377);
$pdf->Cell(166, 5, $Search['ClassificationUSCS2'], 1, 1, 'C');

// Comments and observations
$pdf->SetXY(52, 435);
$pdf->MultiCell(145, 4, utf8_decode($Search['Comments']), 0, 'L');
$pdf->SetXY(52, 483);
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

insertarImagenBase64($pdf, $GrainSizeChart, 40, 308, 200, 0); // ajusta X, Y, ancho, alto

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'GS' . '-' . $Search['Material_Type'] . '.pdf', 'I');
