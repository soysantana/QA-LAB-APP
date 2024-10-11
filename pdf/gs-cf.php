<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_coarse_filter', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(300, 290));

$pdf->setSourceFile('PV-TSF-CQA_CF-Grainsize and Reactivity Rev.2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 9);
// Project Information
$pdf->SetXY(35.5, 26);
$pdf->Cell(22, 4, 'Pueblo Viejo', 0, 1, 'L');
$pdf->SetXY(35.5, 30.5);
$pdf->Cell(22, 4, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(35.5, 34.5);
$pdf->Cell(22, 3.5, $Search['Client'], 0, 1, 'L');
// Sample Information
$pdf->SetXY(80.5, 26);
$pdf->Cell(21, 4, $Search['Structure'], 0, 1, 'L');
$pdf->SetXY(80.5, 30.5);
$pdf->Cell(21, 4, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(80.5, 34.5);
$pdf->Cell(21, 3.5, $Search['Source'], 0, 1, 'L');
$pdf->SetXY(121.5, 26);
$pdf->Cell(21, 4, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(121.5, 30.5);
$pdf->Cell(21, 4, $Search['Sample_Number'], 0, 1, 'L');
$pdf->SetXY(121.5, 34.5);
$pdf->Cell(21, 3.5, $Search['Sample_By'], 0, 1, 'L');
$pdf->SetXY(176, 26);
$pdf->Cell(21, 4, $Search['North'], 0, 1, 'L');
$pdf->SetXY(176, 30.5);
$pdf->Cell(21, 4, $Search['East'], 0, 1, 'L');
$pdf->SetXY(176, 34.5);
$pdf->Cell(21, 3.5, $Search['Elev'], 0, 1, 'L');
// Laboratory Information
$pdf->SetXY(218, 26);
$pdf->Cell(21, 4, 'PVDJ Soil Lab', 0, 1, 'L');
$pdf->SetXY(218, 30.5);
$pdf->Cell(21, 4, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(218, 34.5);
$pdf->Cell(21, 3.5, $Search['Sample_Date'], 0, 1, 'L');
$pdf->SetXY(260, 26);
$pdf->Cell(21, 4, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(260, 30.5);
$pdf->Cell(21, 4, $Search['Registed_Date'], 0, 1, 'L');
$pdf->SetXY(260, 34.5);
$pdf->Cell(21, 3.5, $Search['Standard'], 0, 1, 'L');
// Grain Size Testing Information
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(58, 42);
$pdf->Cell(22, 4, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(58, 46);
$pdf->Cell(22, 4, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(58, 50.5);
$pdf->Cell(22, 4, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(58, 55);
$pdf->Cell(22, 4, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(58, 59.5);
$pdf->Cell(22, 4, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(58, 64);
$pdf->Cell(22, 4, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(58, 68.5);
$pdf->Cell(22, 4, $Search['Wt_Wash_Pan'], 0, 1, 'C');
// Grain Size Distribution
$pdf->SetXY(122, 46);
$pdf->Cell(22, 4, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(145, 46);
$pdf->Cell(16, 4, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(161, 46);
$pdf->Cell(15, 4, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(176, 46);
$pdf->Cell(22, 4, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(199, 46);
$pdf->Cell(19, 4, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(122, 50.5);
$pdf->Cell(22, 4, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(145, 50.5);
$pdf->Cell(16, 4, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(161, 50.5);
$pdf->Cell(15, 4, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(176, 50.5);
$pdf->Cell(22, 4, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(199, 50.5);
$pdf->Cell(19, 4, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(122, 55);
$pdf->Cell(22, 4, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(145, 55);
$pdf->Cell(16, 4, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(161, 55);
$pdf->Cell(15, 4, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(176, 55);
$pdf->Cell(22, 4, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(199, 55);
$pdf->Cell(19, 4, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(122, 59.5);
$pdf->Cell(22, 4, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(145, 59.5);
$pdf->Cell(16, 4, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(161, 59.5);
$pdf->Cell(15, 4, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(176, 59.5);
$pdf->Cell(22, 4, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(199, 59.5);
$pdf->Cell(19, 4, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(122, 64);
$pdf->Cell(22, 4, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(145, 64);
$pdf->Cell(16, 4, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(161, 64);
$pdf->Cell(15, 4, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(176, 64);
$pdf->Cell(22, 4, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(199, 64);
$pdf->Cell(19, 4, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(122, 68.5);
$pdf->Cell(22, 4, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(145, 68.5);
$pdf->Cell(16, 4, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(161, 68.5);
$pdf->Cell(15, 4, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(176, 68.5);
$pdf->Cell(22, 4, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(199, 68.5);
$pdf->Cell(19, 4, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(122, 73);
$pdf->Cell(22, 6, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(145, 73);
$pdf->Cell(16, 6, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(161, 73);
$pdf->Cell(15, 6, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(176, 73);
$pdf->Cell(22, 6, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(199, 73);
$pdf->Cell(19, 6, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(122, 79.5);
$pdf->Cell(22, 4, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(145, 79.5);
$pdf->Cell(16, 4, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(161, 79.5);
$pdf->Cell(15, 4, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(176, 79.5);
$pdf->Cell(22, 4, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(199, 79.5);
$pdf->Cell(19, 4, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(122, 83.5);
$pdf->Cell(22, 4, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(145, 83.5);
$pdf->Cell(16, 4, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(161, 83.5);
$pdf->Cell(15, 4, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(176, 83.5);
$pdf->Cell(22, 4, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(199, 83.5);
$pdf->Cell(19, 4, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(122, 87.5);
$pdf->Cell(22, 4, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(145, 87.5);
$pdf->Cell(16, 4, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(161, 87.5);
$pdf->Cell(15, 4, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(176, 87.5);
$pdf->Cell(22, 4, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(199, 87.5);
$pdf->Cell(19, 4, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(122, 92);
$pdf->Cell(22, 4, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(145, 92);
$pdf->Cell(16, 4, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(161, 92);
$pdf->Cell(15, 4, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(176, 92);
$pdf->Cell(22, 4, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(199, 92);
$pdf->Cell(19, 4, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(122, 96.5);
$pdf->Cell(22, 4, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(145, 96.5);
$pdf->Cell(16, 4, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(161, 96.5);
$pdf->Cell(15, 4, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(176, 96.5);
$pdf->Cell(22, 4, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(199, 96.5);
$pdf->Cell(19, 4, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(122, 101);
$pdf->Cell(22, 4, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(145, 101);
$pdf->Cell(16, 4, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(161, 101);
$pdf->Cell(15, 4, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(176, 101);
$pdf->Cell(22, 4, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(199, 101);
$pdf->Cell(19, 4, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(122, 105.5);
$pdf->Cell(22, 4, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(145, 105.5);
$pdf->Cell(16, 4, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(122, 109.5);
$pdf->Cell(22, 4, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(145, 109.5);
$pdf->Cell(16, 4, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(161, 109.5);
$pdf->Cell(15, 4, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(176, 109.5);
$pdf->Cell(22, 4, $Search['TotalPass'], 0, 1, 'C');
// Sumary Grain Size Distribution Parameter
$pdf->SetXY(241, 73);
$pdf->Cell(19, 6, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(241, 79.5);
$pdf->Cell(19, 4, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(241, 83.5);
$pdf->Cell(19, 4, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(241, 88);
$pdf->Cell(19, 4, $Search['Fines'], 0, 1, 'C');
$pdf->SetXY(241, 92);
$pdf->Cell(19, 4, $Search['D60'], 0, 1, 'C');
$pdf->SetXY(241, 97);
$pdf->Cell(19, 4, $Search['D30'], 0, 1, 'C');
$pdf->SetXY(241, 101);
$pdf->Cell(19, 4, $Search['D10'], 0, 1, 'C');
$pdf->SetXY(241, 105);
$pdf->Cell(19, 4, $Search['Cc'], 0, 1, 'C');
$pdf->SetXY(241, 110);
$pdf->Cell(19, 4, $Search['Cu'], 0, 1, 'C');
// Reactivity Test
$pdf->SetXY(58, 79.5);
$pdf->Cell(22, 4, $Search['Total_Sample_Weight'], 0, 1, 'C');
$pdf->SetXY(58, 83);
$pdf->Cell(22, 4, $Search['Weight_Used_For_The_Test'], 0, 1, 'C');
$pdf->SetXY(58, 88);
$pdf->Cell(22, 4, $Search['A_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(58, 92);
$pdf->Cell(22, 4, $Search['B_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(58, 97);
$pdf->Cell(22, 4, $Search['C_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(58, 101);
$pdf->Cell(22, 4, $Search['Weight_Mat_Ret_No_4'], 0, 1, 'C');
$pdf->SetXY(58, 105.5);
$pdf->Cell(22, 4, $Search['Weight_Reactive_Part_Ret_No_4'], 0, 1, 'C');
$pdf->SetXY(58, 110);
$pdf->Cell(22, 4, $Search['Percent_Reactive_Particles'], 0, 1, 'C');
$pdf->SetXY(58, 114);
$pdf->Cell(22, 4, $Search['Average_Particles_Reactive'], 0, 1, 'C');
$pdf->SetXY(58, 118.5);
$pdf->Cell(22, 4, $Search['Reaction_Strength_Result'], 0, 1, 'C');
//Preparation Method
function setCell($pdf, $x, $y, $condition, $value, $width = 4, $height = 3.5) {
    $pdf->SetXY($x, $y);
    $pdf->Cell($width, $height, ($condition == $value ? 'X' : ''), 1, 1, 'C');
}

// Para 'Preparation_Method'
setCell($pdf, 237, 46, $Search['Preparation_Method'], 'Oven Dried');
setCell($pdf, 267, 46, $Search['Preparation_Method'], 'Air Dried');

// Para 'Split_Method'
setCell($pdf, 237, 50.5, $Search['Split_Method'], 'Mechanical');
setCell($pdf, 267, 50.5, $Search['Split_Method'], 'Manual');

// Test Condition para 'Acid Reactivity Test Result'
setCell($pdf, 102, 127, $Search['Acid_Reactivity_Test_Result'], 'Accepted', 20, 4);
setCell($pdf, 122, 127, $Search['Acid_Reactivity_Test_Result'], 'Rejected', 22, 4);

// Comments
$pdf->SetXY(82, 135);
$pdf->MultiCell(60, 4, $Search['Comments'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 145, 116, 150, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>