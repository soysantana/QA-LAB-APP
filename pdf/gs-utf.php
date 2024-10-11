<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_upstream_transition_fill', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(331, 330));

$pdf->setSourceFile('PV-TSF-CQA_UTF-Grainsize and Reactivity Rev.2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 8);
// Project Information
$pdf->SetXY(48, 27.5);
$pdf->Cell(20.5, 4, 'Pueblo Viejo', 0, 1, 'L');
$pdf->SetXY(48, 31.5);
$pdf->Cell(20.5, 4, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(48, 36);
$pdf->Cell(20.5, 3.5, $Search['Client'], 0, 1, 'L');
// Sample Information
$pdf->SetXY(89.5, 27.5);
$pdf->Cell(17, 4, $Search['Structure'], 0, 1, 'L');
$pdf->SetXY(89.5, 31.5);
$pdf->Cell(17, 4, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(89.5, 36);
$pdf->Cell(17, 3.5, $Search['Source'], 0, 1, 'L');
$pdf->SetXY(127, 27.5);
$pdf->Cell(24, 4, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(127, 31.5);
$pdf->Cell(24, 4, $Search['Sample_Number'], 0, 1, 'L');
$pdf->SetXY(127, 36);
$pdf->Cell(24, 3.5, $Search['Sample_By'], 0, 1, 'L');
$pdf->SetXY(190.5, 27.5);
$pdf->Cell(17, 4, $Search['North'], 0, 1, 'L');
$pdf->SetXY(190.5, 31.5);
$pdf->Cell(17, 4, $Search['East'], 0, 1, 'L');
$pdf->SetXY(190.5, 36);
$pdf->Cell(17, 3.5, $Search['Elev'], 0, 1, 'L');
// Laboratory Information
$pdf->SetXY(228, 27.5);
$pdf->Cell(22, 4, 'PVDJ Soil Lab', 0, 1, 'L');
$pdf->SetXY(228, 31.5);
$pdf->Cell(22, 4, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(228, 36);
$pdf->Cell(22, 3.5, $Search['Sample_Date'], 0, 1, 'L');
$pdf->SetXY(269.5, 27.5);
$pdf->Cell(24, 4, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(269.5, 31.5);
$pdf->Cell(24, 4, $Search['Registed_Date'], 0, 1, 'L');
$pdf->SetXY(269.5, 36);
$pdf->Cell(24, 3.5, $Search['Standard'], 0, 1, 'L');
// Grain Size Testing Information
$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(68.5, 43.5);
$pdf->Cell(21, 4, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(68.5, 47);
$pdf->Cell(21, 4, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(68.5, 51.8);
$pdf->Cell(21, 4, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(68.5, 56);
$pdf->Cell(21, 4, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(68.5, 61);
$pdf->Cell(21, 4, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(68.5, 65.5);
$pdf->Cell(21, 4, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(68.5, 70);
$pdf->Cell(21, 4, $Search['Wt_Wash_Pan'], 0, 1, 'C');
// Grain Size Distribution
$pdf->SetXY(127, 47);
$pdf->Cell(24, 5, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(151, 47);
$pdf->Cell(20, 5, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(171.5, 47);
$pdf->Cell(19, 5, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(190.5, 47);
$pdf->Cell(17.5, 5, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(208, 47);
$pdf->Cell(20, 5, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(127, 52);
$pdf->Cell(24, 4, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(151, 52);
$pdf->Cell(20, 4, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(171.5, 52);
$pdf->Cell(19, 4, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(190.5, 52);
$pdf->Cell(17.5, 4, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(208, 52);
$pdf->Cell(20, 4, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(127, 56);
$pdf->Cell(24, 5, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(151, 56);
$pdf->Cell(20, 5, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(171.5, 56);
$pdf->Cell(19, 5, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(190.5, 56);
$pdf->Cell(17.5, 5, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(208, 56);
$pdf->Cell(20, 5, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(127, 61);
$pdf->Cell(24, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(151, 61);
$pdf->Cell(20, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(171.5, 61);
$pdf->Cell(19, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(190.5, 61);
$pdf->Cell(17.5, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(208, 61);
$pdf->Cell(20, 4, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(127, 65);
$pdf->Cell(24, 5, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(151, 65);
$pdf->Cell(20, 5, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(171.5, 65);
$pdf->Cell(19, 5, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(190.5, 65);
$pdf->Cell(17.5, 5, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(208, 65);
$pdf->Cell(20, 5, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(127, 70);
$pdf->Cell(24, 4, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(151, 70);
$pdf->Cell(20, 4, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(171.5, 70);
$pdf->Cell(19, 4, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(190.5, 70);
$pdf->Cell(17.5, 4, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(208, 70);
$pdf->Cell(20, 4, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(127, 74);
$pdf->Cell(24, 7, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(151, 74);
$pdf->Cell(20, 7, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(171.5, 74);
$pdf->Cell(19, 7, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(190.5, 74);
$pdf->Cell(17.5, 7, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(208, 74);
$pdf->Cell(20, 7, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(127, 81.5);
$pdf->Cell(24, 4, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(151, 81.5);
$pdf->Cell(20, 4, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(171.5, 81.5);
$pdf->Cell(19, 4, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(190.5, 81.5);
$pdf->Cell(17.5, 4, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(208, 81.5);
$pdf->Cell(20, 4, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(127, 85.5);
$pdf->Cell(24, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(151, 85.5);
$pdf->Cell(20, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(171.5, 85.5);
$pdf->Cell(19, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(190.5, 85.5);
$pdf->Cell(17.5, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(208, 85.5);
$pdf->Cell(20, 4, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(127, 89.5);
$pdf->Cell(24, 4, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(151, 89.5);
$pdf->Cell(20, 4, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(171.5, 89.5);
$pdf->Cell(19, 4, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(190.5, 89.5);
$pdf->Cell(17.5, 4, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(208, 89.5);
$pdf->Cell(20, 4, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(127, 94);
$pdf->Cell(24, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(151, 94);
$pdf->Cell(20, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(171.5, 94);
$pdf->Cell(19, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(190.5, 94);
$pdf->Cell(17.5, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(208, 94);
$pdf->Cell(20, 4, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(127, 98);
$pdf->Cell(24, 5, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(151, 98);
$pdf->Cell(20, 5, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(171.5, 98);
$pdf->Cell(19, 5, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(190.5, 98);
$pdf->Cell(17.5, 5, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(208, 98);
$pdf->Cell(20, 5, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(127, 103);
$pdf->Cell(24, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(151, 103);
$pdf->Cell(20, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(171.5, 103);
$pdf->Cell(19, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(190.5, 103);
$pdf->Cell(17.5, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(208, 103);
$pdf->Cell(20, 4, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(127, 107);
$pdf->Cell(24, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(151, 107);
$pdf->Cell(20, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(127, 111.5);
$pdf->Cell(24, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(151, 111.5);
$pdf->Cell(20, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(171.5, 111.5);
$pdf->Cell(19, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(190.5, 111.5);
$pdf->Cell(17.5, 4, $Search['TotalPass'], 0, 1, 'C');
// Sumary Grain Size Distribution Parameter
$pdf->SetXY(250.5, 74);
$pdf->Cell(19, 7, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(250.5, 81.5);
$pdf->Cell(19, 4, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(250.5, 85.5);
$pdf->Cell(19, 4, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(250.5, 90);
$pdf->Cell(19, 4, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(250.5, 94);
$pdf->Cell(19, 4, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(250.5, 98);
$pdf->Cell(19, 4, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(250.5, 103);
$pdf->Cell(19, 4, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(250.5, 107);
$pdf->Cell(19, 4, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(250.5, 112);
$pdf->Cell(19, 4, $Search['Cu'], 0, 1, 'C');
// Reactivity Test
$pdf->SetXY(68.5, 81.5);
$pdf->Cell(21, 4, $Search['Total_Sample_Weight'], 0, 1, 'C');
$pdf->SetXY(68.5, 85.5);
$pdf->Cell(21, 4, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(68.5, 90);
$pdf->Cell(21, 4, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(68.5, 94);
$pdf->Cell(21, 4, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(68.5, 98.5);
$pdf->Cell(21, 4, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(68.5, 103);
$pdf->Cell(21, 4, $Search['Weight_Mat_Ret_No_4'], 0, 1, 'C');
$pdf->SetXY(68.5, 107);
$pdf->Cell(21, 4, $Search['Weight_Reactive_Part_Ret_No_4'], 0, 1, 'C');
$pdf->SetXY(68.5, 111.5);
$pdf->Cell(21, 4, $Search['Average_Particles_Reactive'], 0, 1, 'C');

// Asignamos la abreviatura según el valor de Reaction_Strength_Result
switch ($Search['Reaction_Strength_Result']) {
    case 'Weak Reaction':
        $reactionStrength = 'W';
        break;
    case 'No Reaction':
        $reactionStrength = 'NR';
        break;
    case 'Moderate Reaction':
        $reactionStrength = 'M';
        break;
    case 'Strong Reaction':
        $reactionStrength = 'S';
        break;
    default:
        $reactionStrength = $Search['Reaction_Strength_Result']; // Mantiene el valor original si no coincide
        break;
}

// Luego, utilizamos el valor en el PDF
$pdf->SetXY(68.5, 116);
$pdf->Cell(21, 4, $reactionStrength, 0, 1, 'C');

//Preparation Method
function setCell($pdf, $x, $y, $condition, $value, $width = 3, $height = 3) {
    $pdf->SetXY($x, $y);
    $pdf->Cell($width, $height, ($condition == $value ? 'X' : ''), 1, 1, 'C');
}

// Para 'Preparation_Method'
setCell($pdf, 245, 48, $Search['Preparation_Method'], 'Oven Dried');
setCell($pdf, 273, 48, $Search['Preparation_Method'], 'Air Dried');

// Para 'Split_Method'
setCell($pdf, 246, 52, $Search['Split_Method'], 'Mechanical');
setCell($pdf, 275, 52, $Search['Split_Method'], 'Manual');

// Test Condition para 'Acid Reactivity Test Result'
setCell($pdf, 250, 135.5, $Search['Acid_Reactivity_Test_Result'], 'Accepted', 19.5, 4);
setCell($pdf, 269.5, 135.5, $Search['Acid_Reactivity_Test_Result'], 'Rejected', 24.5, 4);

// Comments
$pdf->SetXY(26, 159);
$pdf->MultiCell(100, 4, $Search['Comments'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 145, 149, 150, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>