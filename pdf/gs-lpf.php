<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('grain_size_lpf', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(360, 280));

$pdf->setSourceFile('PV-TSF-CQA_ LPF_Grainsize_Rev. 3.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 8);
// Project Information
$pdf->SetXY(35.5, 35);
$pdf->Cell(19, 6, 'Pueblo Viejo', 0, 1, 'L');
$pdf->SetXY(35.5, 41.5);
$pdf->Cell(19, 6, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(35.5, 47.5);
$pdf->Cell(19, 6, $Search['Client'], 0, 1, 'L');
// Sample Information
$pdf->SetXY(35.5, 64.5);
$pdf->Cell(19, 7, $Search['Structure'], 0, 1, 'L');
$pdf->SetXY(35.5, 71.5);
$pdf->Cell(19, 7, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(35.5, 78.5);
$pdf->Cell(19, 7, $Search['Source'], 0, 1, 'L');
$pdf->SetXY(78, 64.5);
$pdf->Cell(27, 7, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(78, 71.5);
$pdf->Cell(27, 7, $Search['Sample_Number'], 0, 1, 'L');
$pdf->SetXY(78, 78.5);
$pdf->Cell(27, 7, $Search['Sample_By'], 0, 1, 'L');
$pdf->SetXY(148.5, 64.5);
$pdf->Cell(28, 7, $Search['North'], 0, 1, 'L');
$pdf->SetXY(148.5, 71.5);
$pdf->Cell(28, 7, $Search['East'], 0, 1, 'L');
$pdf->SetXY(148.5, 78.5);
$pdf->Cell(28, 7, $Search['Elev'], 0, 1, 'L');
// Laboratory Information
$pdf->SetXY(198, 35);
$pdf->Cell(22, 6, 'PVDJ Soil Lab', 0, 1, 'L');
$pdf->SetXY(198, 41.5);
$pdf->Cell(22, 6, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(198, 47.5);
$pdf->Cell(22, 6, $Search['Sample_Date'], 0, 1, 'L');
$pdf->SetXY(241.5, 35);
$pdf->Cell(21, 6, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(241.5, 41.5);
$pdf->Cell(21, 6, $Search['Registed_Date'], 0, 1, 'L');
$pdf->SetXY(241.5, 47.5);
$pdf->Cell(21, 6, $Search['Standard'], 0, 1, 'L');
$pdf->SetXY(241.5, 53.5);
$pdf->Cell(21, 6, $Search['Methods'], 0, 1, 'L');
// Grain Size Testing Information
$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(55, 109);
$pdf->Cell(22.5, 7, $Search['Container'], 0, 1, 'C');
$pdf->SetXY(55, 116);
$pdf->Cell(22.5, 6, $Search['Wet_Soil_Tare'], 0, 1, 'C');
$pdf->SetXY(55, 122);
$pdf->Cell(22.5, 7, $Search['Wet_Dry_Tare'], 0, 1, 'C');
$pdf->SetXY(55, 129);
$pdf->Cell(22.5, 7, $Search['Tare'], 0, 1, 'C');
$pdf->SetXY(55, 136);
$pdf->Cell(22.5, 6, $Search['Wt_Dry_Soil'], 0, 1, 'C');
$pdf->SetXY(55, 142);
$pdf->Cell(22.5, 7, $Search['Wt_Washed'], 0, 1, 'C');
$pdf->SetXY(55, 149.5);
$pdf->Cell(22.5, 6, $Search['Wt_Wash_Pan'], 0, 1, 'C');
// Grain Size Distribution

$pdf->SetXY(149, 98.5);
$pdf->Cell(28, 5, $Search['WtRet1'], 0, 1, 'C');
$pdf->SetXY(177, 98.5);
$pdf->Cell(21, 5, $Search['Ret1'], 0, 1, 'C');
$pdf->SetXY(198, 98.5);
$pdf->Cell(22, 5, $Search['CumRet1'], 0, 1, 'C');
$pdf->SetXY(220, 98.5);
$pdf->Cell(21, 5, $Search['Pass1'], 0, 1, 'C');
$pdf->SetXY(241.5, 98.5);
$pdf->Cell(21, 5, $Search['Specs1'], 0, 1, 'C');

$pdf->SetXY(149, 104);
$pdf->Cell(28, 5, $Search['WtRet2'], 0, 1, 'C');
$pdf->SetXY(177, 104);
$pdf->Cell(21, 5, $Search['Ret2'], 0, 1, 'C');
$pdf->SetXY(198, 104);
$pdf->Cell(22, 5, $Search['CumRet2'], 0, 1, 'C');
$pdf->SetXY(220, 104);
$pdf->Cell(21, 5, $Search['Pass2'], 0, 1, 'C');
$pdf->SetXY(241.5, 104);
$pdf->Cell(21, 5, $Search['Specs2'], 0, 1, 'C');

$pdf->SetXY(149, 109);
$pdf->Cell(28, 7, $Search['WtRet3'], 0, 1, 'C');
$pdf->SetXY(177, 109);
$pdf->Cell(21, 7, $Search['Ret3'], 0, 1, 'C');
$pdf->SetXY(198, 109);
$pdf->Cell(22, 7, $Search['CumRet3'], 0, 1, 'C');
$pdf->SetXY(220, 109);
$pdf->Cell(21, 7, $Search['Pass3'], 0, 1, 'C');
$pdf->SetXY(241.5, 109);
$pdf->Cell(21, 7, $Search['Specs3'], 0, 1, 'C');

$pdf->SetXY(149, 116);
$pdf->Cell(28, 6, $Search['WtRet4'], 0, 1, 'C');
$pdf->SetXY(177, 116);
$pdf->Cell(21, 6, $Search['Ret4'], 0, 1, 'C');
$pdf->SetXY(198, 116);
$pdf->Cell(22, 6, $Search['CumRet4'], 0, 1, 'C');
$pdf->SetXY(220, 116);
$pdf->Cell(21, 6, $Search['Pass4'], 0, 1, 'C');
$pdf->SetXY(241.5, 116);
$pdf->Cell(21, 6, $Search['Specs4'], 0, 1, 'C');

$pdf->SetXY(149, 122);
$pdf->Cell(28, 7, $Search['WtRet5'], 0, 1, 'C');
$pdf->SetXY(177, 122);
$pdf->Cell(21, 7, $Search['Ret5'], 0, 1, 'C');
$pdf->SetXY(198, 122);
$pdf->Cell(22, 7, $Search['CumRet5'], 0, 1, 'C');
$pdf->SetXY(220, 122);
$pdf->Cell(21, 7, $Search['Pass5'], 0, 1, 'C');
$pdf->SetXY(241.5, 122);
$pdf->Cell(21, 7, $Search['Specs5'], 0, 1, 'C');

$pdf->SetXY(149, 129.5);
$pdf->Cell(28, 6, $Search['WtRet6'], 0, 1, 'C');
$pdf->SetXY(177, 129.5);
$pdf->Cell(21, 6, $Search['Ret6'], 0, 1, 'C');
$pdf->SetXY(198, 129.5);
$pdf->Cell(22, 6, $Search['CumRet6'], 0, 1, 'C');
$pdf->SetXY(220, 129.5);
$pdf->Cell(21, 6, $Search['Pass6'], 0, 1, 'C');
$pdf->SetXY(241.5, 129.5);
$pdf->Cell(21, 6, $Search['Specs6'], 0, 1, 'C');

$pdf->SetXY(149, 136);
$pdf->Cell(28, 6, $Search['WtRet7'], 0, 1, 'C');
$pdf->SetXY(177, 136);
$pdf->Cell(21, 6, $Search['Ret7'], 0, 1, 'C');
$pdf->SetXY(198, 136);
$pdf->Cell(22, 6, $Search['CumRet7'], 0, 1, 'C');
$pdf->SetXY(220, 136);
$pdf->Cell(21, 6, $Search['Pass7'], 0, 1, 'C');
$pdf->SetXY(241.5, 136);
$pdf->Cell(21, 6, $Search['Specs7'], 0, 1, 'C');

$pdf->SetXY(149, 142.5);
$pdf->Cell(28, 7, $Search['WtRet8'], 0, 1, 'C');
$pdf->SetXY(177, 142.5);
$pdf->Cell(21, 7, $Search['Ret8'], 0, 1, 'C');
$pdf->SetXY(198, 142.5);
$pdf->Cell(22, 7, $Search['CumRet8'], 0, 1, 'C');
$pdf->SetXY(220, 142.5);
$pdf->Cell(21, 7, $Search['Pass8'], 0, 1, 'C');
$pdf->SetXY(241.5, 142.5);
$pdf->Cell(21, 7, $Search['Specs8'], 0, 1, 'C');

$pdf->SetXY(149, 149.5);
$pdf->Cell(28, 6, $Search['WtRet9'], 0, 1, 'C');
$pdf->SetXY(177, 149.5);
$pdf->Cell(21, 6, $Search['Ret9'], 0, 1, 'C');
$pdf->SetXY(198, 149.5);
$pdf->Cell(22, 6, $Search['CumRet9'], 0, 1, 'C');
$pdf->SetXY(220, 149.5);
$pdf->Cell(21, 6, $Search['Pass9'], 0, 1, 'C');
$pdf->SetXY(241.5, 149.5);
$pdf->Cell(21, 6, $Search['Specs9'], 0, 1, 'C');

$pdf->SetXY(149, 155.5);
$pdf->Cell(28, 6.5, $Search['WtRet10'], 0, 1, 'C');
$pdf->SetXY(177, 155.5);
$pdf->Cell(21, 6.5, $Search['Ret10'], 0, 1, 'C');
$pdf->SetXY(198, 155.5);
$pdf->Cell(22, 6.5, $Search['CumRet10'], 0, 1, 'C');
$pdf->SetXY(220, 155.5);
$pdf->Cell(21, 6.5, $Search['Pass10'], 0, 1, 'C');
$pdf->SetXY(241.5, 155.5);
$pdf->Cell(21, 6.5, $Search['Specs10'], 0, 1, 'C');

$pdf->SetXY(149, 162);
$pdf->Cell(28, 6, $Search['WtRet11'], 0, 1, 'C');
$pdf->SetXY(177, 162);
$pdf->Cell(21, 6, $Search['Ret11'], 0, 1, 'C');
$pdf->SetXY(198, 162);
$pdf->Cell(22, 6, $Search['CumRet11'], 0, 1, 'C');
$pdf->SetXY(220, 162);
$pdf->Cell(21, 6, $Search['Pass11'], 0, 1, 'C');
$pdf->SetXY(241.5, 162);
$pdf->Cell(21, 6, $Search['Specs11'], 0, 1, 'C');

$pdf->SetXY(149, 168);
$pdf->Cell(28, 7, $Search['WtRet12'], 0, 1, 'C');
$pdf->SetXY(177, 168);
$pdf->Cell(21, 7, $Search['Ret12'], 0, 1, 'C');
$pdf->SetXY(198, 168);
$pdf->Cell(22, 7, $Search['CumRet12'], 0, 1, 'C');
$pdf->SetXY(220, 168);
$pdf->Cell(21, 7, $Search['Pass12'], 0, 1, 'C');
$pdf->SetXY(241.5, 168);
$pdf->Cell(21, 7, $Search['Specs12'], 0, 1, 'C');

$pdf->SetXY(149, 175);
$pdf->Cell(28, 6.5, $Search['WtRet13'], 0, 1, 'C');
$pdf->SetXY(177, 175);
$pdf->Cell(21, 6.5, $Search['Ret13'], 0, 1, 'C');
$pdf->SetXY(198, 175);
$pdf->Cell(22, 6.5, $Search['CumRet13'], 0, 1, 'C');
$pdf->SetXY(220, 175);
$pdf->Cell(21, 6.5, $Search['Pass13'], 0, 1, 'C');
$pdf->SetXY(241.5, 175);
$pdf->Cell(21, 6.5, $Search['Specs13'], 0, 1, 'C');

$pdf->SetXY(149, 182);
$pdf->Cell(28, 5, $Search['PanWtRen'], 0, 1, 'C');
$pdf->SetXY(177, 182);
$pdf->Cell(21, 5, $Search['PanRet'], 0, 1, 'C');

$pdf->SetXY(149, 187.5);
$pdf->Cell(28, 7, $Search['TotalWtRet'], 0, 1, 'C');
$pdf->SetXY(177, 187.5);
$pdf->Cell(21, 7, $Search['TotalRet'], 0, 1, 'C');
$pdf->SetXY(198, 187.5);
$pdf->Cell(22, 7, $Search['TotalCumRet'], 0, 1, 'C');
$pdf->SetXY(220, 187.5);
$pdf->Cell(21, 7, $Search['TotalPass'], 0, 1, 'C');
// Sumary Grain Size Distribution Parameter

$pdf->SetXY(242, 199);
$pdf->Cell(20, 7, $Search['Coarser_than_Gravel'], 0, 1, 'C');
$pdf->SetXY(242, 206.5);
$pdf->Cell(20, 6, $Search['Gravel'], 0, 1, 'C');
$pdf->SetXY(242, 213);
$pdf->Cell(20, 6, $Search['Sand'], 0, 1, 'C');
$pdf->SetXY(242, 220);
$pdf->Cell(20, 6, $Search['Fines'], 0, 1, 'C');
//Preparation Method
function setCell($pdf, $x, $y, $condition, $value, $width = 4, $height = 3.5) {
    $pdf->SetXY($x, $y);
    $pdf->Cell($width, $height, ($condition == $value ? 'X' : ''), 1, 1, 'C');
}

// Para 'Preparation_Method'
setCell($pdf, 198, 67, $Search['Preparation_Method'], 'Oven Dried');
setCell($pdf, 233, 67, $Search['Preparation_Method'], 'Air Dried');

// Para 'Split_Method'
setCell($pdf, 198, 75, $Search['Split_Method'], 'Mechanical');
setCell($pdf, 233, 75, $Search['Split_Method'], 'Manual');


// Comments
$pdf->SetXY(199, 230);
$pdf->MultiCell(62, 4, $Search['Comments'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 4, 259, 185, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>