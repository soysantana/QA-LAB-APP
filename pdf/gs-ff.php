<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_fine_filter', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(400, 380));

$pdf->setSourceFile('PV-TSF-CQA_FF-Grainsize and Reactivity Rev.2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 9);
// Project Information
$pdf->SetXY(54, 34.5);
$pdf->Cell(20, 4, 'Pueblo Viejo', 0, 1, 'L');
$pdf->SetXY(54, 38.5);
$pdf->Cell(20, 4, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(54, 42.5);
$pdf->Cell(20, 3.5, $Search['Client'], 0, 1, 'L');
// Sample Information
$pdf->SetXY(96, 34.5);
$pdf->Cell(21, 4, $Search['Structure'], 0, 1, 'L');
$pdf->SetXY(96, 38.5);
$pdf->Cell(21, 4, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(96, 42.5);
$pdf->Cell(21, 3.5, $Search['Source'], 0, 1, 'L');
$pdf->SetXY(143.5, 34.5);
$pdf->Cell(21, 4, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(143.5, 38.5);
$pdf->Cell(21, 4, $Search['Sample_Number'], 0, 1, 'L');
$pdf->SetXY(143.5, 42.5);
$pdf->Cell(21, 3.5, $Search['Sample_By'], 0, 1, 'L');
$pdf->SetXY(213, 34.5);
$pdf->Cell(21, 4, $Search['North'], 0, 1, 'L');
$pdf->SetXY(213, 38.5);
$pdf->Cell(21, 4, $Search['East'], 0, 1, 'L');
$pdf->SetXY(213, 42.5);
$pdf->Cell(21, 3.5, $Search['Elev'], 0, 1, 'L');
// Laboratory Information
$pdf->SetXY(251, 34.5);
$pdf->Cell(21, 4, 'PVDJ Soil Lab', 0, 1, 'L');
$pdf->SetXY(251, 38.5);
$pdf->Cell(21, 4, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(251, 42.5);
$pdf->Cell(21, 3.5, $Search['Sample_Date'], 0, 1, 'L');
$pdf->SetXY(303, 34.5);
$pdf->Cell(21, 4, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(303, 38.5);
$pdf->Cell(21, 4, $Search['Registed_Date'], 0, 1, 'L');
$pdf->SetXY(303, 42.5);
$pdf->Cell(21, 3.5, $Search['Standard'], 0, 1, 'L');
// Grain Size Testing Information
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(75, 50.5);
$pdf->Cell(20, 4, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(75, 55);
$pdf->Cell(20, 4, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(75, 59.5);
$pdf->Cell(20, 4, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(75, 64);
$pdf->Cell(20, 4, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(75, 68.5);
$pdf->Cell(20, 4, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(75, 72.5);
$pdf->Cell(20, 8, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(75, 81);
$pdf->Cell(20, 5, $Search['Wt_Wash_Pan'], 0, 1, 'C');
// Grain Size Distribution
$pdf->SetXY(144, 55);
$pdf->Cell(27, 4, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(171, 55);
$pdf->Cell(22, 4, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(193, 55);
$pdf->Cell(20, 4, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(213, 55);
$pdf->Cell(19, 4, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(232, 55);
$pdf->Cell(19, 4, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(144, 59.5);
$pdf->Cell(27, 4, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(171, 59.5);
$pdf->Cell(22, 4, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(193, 59.5);
$pdf->Cell(20, 4, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(213, 59.5);
$pdf->Cell(19, 4, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(232, 59.5);
$pdf->Cell(19, 4, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(144, 64);
$pdf->Cell(27, 4, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(171, 64);
$pdf->Cell(22, 4, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(193, 64);
$pdf->Cell(20, 4, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(213, 64);
$pdf->Cell(19, 4, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(232, 64);
$pdf->Cell(19, 4, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(144, 68.5);
$pdf->Cell(27, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(171, 68.5);
$pdf->Cell(22, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(193, 68.5);
$pdf->Cell(20, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(213, 68.5);
$pdf->Cell(19, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(232, 68.5);
$pdf->Cell(19, 4, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(144, 72.5);
$pdf->Cell(27, 8, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(171, 72.5);
$pdf->Cell(22, 8, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(193, 72.5);
$pdf->Cell(20, 8, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(213, 72.5);
$pdf->Cell(19, 8, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(232, 72.5);
$pdf->Cell(19, 8, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(144, 81);
$pdf->Cell(27, 5, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(171, 81);
$pdf->Cell(22, 5, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(193, 81);
$pdf->Cell(20, 5, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(213, 81);
$pdf->Cell(19, 5, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(232, 81);
$pdf->Cell(19, 5, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(144, 86);
$pdf->Cell(27, 4, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(171, 86);
$pdf->Cell(22, 4, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(193, 86);
$pdf->Cell(20, 4, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(213, 86);
$pdf->Cell(19, 4, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(232, 86);
$pdf->Cell(19, 4, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(144, 90);
$pdf->Cell(27, 6, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(171, 90);
$pdf->Cell(22, 6, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(193, 90);
$pdf->Cell(20, 6, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(213, 90);
$pdf->Cell(19, 6, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(232, 90);
$pdf->Cell(19, 6, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(144, 96.5);
$pdf->Cell(27, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(171, 96.5);
$pdf->Cell(22, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(193, 96.5);
$pdf->Cell(20, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(213, 96.5);
$pdf->Cell(19, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(232, 96.5);
$pdf->Cell(19, 4, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(144, 100.5);
$pdf->Cell(27, 4, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(171, 100.5);
$pdf->Cell(22, 4, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(193, 100.5);
$pdf->Cell(20, 4, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(213, 100.5);
$pdf->Cell(19, 4, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(232, 100.5);
$pdf->Cell(19, 4, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(144, 105);
$pdf->Cell(27, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(171, 105);
$pdf->Cell(22, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(193, 105);
$pdf->Cell(20, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(213, 105);
$pdf->Cell(19, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(232, 105);
$pdf->Cell(19, 4, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(144, 109);
$pdf->Cell(27, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(171, 109);
$pdf->Cell(22, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(193, 109);
$pdf->Cell(20, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(213, 109);
$pdf->Cell(19, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(232, 109);
$pdf->Cell(19, 4, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(144, 113.5);
$pdf->Cell(27, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(171, 113.5);
$pdf->Cell(22, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(193, 113.5);
$pdf->Cell(20, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(213, 113.5);
$pdf->Cell(19, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(232, 113.5);
$pdf->Cell(19, 4, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(144, 118);
$pdf->Cell(27, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(171, 118);
$pdf->Cell(22, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(144, 122);
$pdf->Cell(27, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(171, 122);
$pdf->Cell(22, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(193, 122);
$pdf->Cell(20, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(213, 122);
$pdf->Cell(19, 4, $Search['TotalPass'], 0, 1, 'C');
// Sumary Grain Size Distribution Parameter
$pdf->SetXY(274, 72);
$pdf->Cell(29, 9, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(274, 81);
$pdf->Cell(29, 5, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(274, 86);
$pdf->Cell(29, 4, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(274, 90.5);
$pdf->Cell(29, 5, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(274, 96);
$pdf->Cell(29, 4, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(274, 101);
$pdf->Cell(29, 4, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(274, 105);
$pdf->Cell(29, 4, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(274, 109);
$pdf->Cell(29, 4, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(274, 113.5);
$pdf->Cell(29, 4, $Search['Cu'], 0, 1, 'C');
// Reactivity Test
$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(75.5, 96);
$pdf->Cell(20, 4, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(75.5, 100.5);
$pdf->Cell(20, 4, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(75.5, 105);
$pdf->Cell(20, 4, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(75.5, 109);
$pdf->Cell(20, 4, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(75.5, 113.5);
$pdf->Cell(20, 4, $Search['D_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(75.5, 117.5);
$pdf->Cell(20, 4, $Search['E_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(75.5, 122);
$pdf->Cell(20, 4, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(75.5, 126.5);
$pdf->Cell(20, 4, $Search['Reaction_Strength_Result'], 0, 1, 'C');
//Preparation Method
$pdf->SetFont('Arial', 'B', 7);
function setCell($pdf, $x, $y, $condition, $value, $width = 3.5, $height = 3) {
    $pdf->SetXY($x, $y);
    $pdf->Cell($width, $height, ($condition == $value ? 'X' : ''), 1, 1, 'C');
}

// Para 'Preparation_Method'
setCell($pdf, 268, 51.5, $Search['Preparation_Method'], 'Oven Dried');
setCell($pdf, 304, 51.5, $Search['Preparation_Method'], 'Air Dried');

// Para 'Split_Method'
setCell($pdf, 271, 55.5, $Search['Split_Method'], 'Mechanical');
setCell($pdf, 305, 55.5, $Search['Split_Method'], 'Manual');

// Test Condition para 'Acid Reactivity Test Result'
setCell($pdf, 123, 135, $Search['Acid_Reactivity_Test_Result'], 'Accepted', 20.5, 4);
setCell($pdf, 143.5, 135, $Search['Acid_Reactivity_Test_Result'], 'Rejected', 27, 4);

// Comments
$pdf->SetXY(96, 143);
$pdf->MultiCell(74, 4, $Search['Comments'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 175, 128, 135, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>