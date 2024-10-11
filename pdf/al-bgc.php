<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('atterberg_limit', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('L', array(360, 300));

$pdf->setSourceFile('PV-TSF-CQA_LPF-Atterberg Limits Rev.2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 9);
// Project Information
$pdf->SetXY(58, 27);
$pdf->Cell(26, 4, 'Pueblo Viejo', 0, 1, 'L');
$pdf->SetXY(58, 31.5);
$pdf->Cell(26, 4, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(58, 36);
$pdf->Cell(26, 4, $Search['Client'], 0, 1, 'L');
// Laboratory Information
$pdf->SetXY(196, 27);
$pdf->Cell(24, 4, 'PVDJ Soil Lab', 0, 1, 'L');
$pdf->SetXY(196, 31.5);
$pdf->Cell(24, 4, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(196, 36);
$pdf->Cell(24, 4, $Search['Standard'], 0, 1, 'L');
$pdf->SetXY(252, 27);
$pdf->Cell(26, 4, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(252, 31.5);
$pdf->Cell(26, 4, $Search['Registed_Date'], 0, 1, 'L');
// Testing Information
$pdf->SetXY(280, 41);
$pdf->Cell(13, 3, $Search['Split_Method'], 0, 1, 'L');
$pdf->SetXY(292, 45.5);
if ($Search['Preparation_Method'] == 'Air Dried') {
    $pdf->Cell(5, 4, 'X', 1, 1, 'C');
} else {
    $pdf->Cell(5, 4, '', 1, 1, 'C');
}
$pdf->SetXY(267.5, 45.5);
if ($Search['Preparation_Method'] == 'Oven Dried') {
    $pdf->Cell(5, 4, 'X', 1, 1, 'C');
} else {
    $pdf->Cell(5, 4, '', 1, 1, 'C');
}
// Sample Information
$pdf->SetXY(58, 45.5);
$pdf->Cell(26, 4, $Search['Structure'], 0, 1, 'L');
$pdf->SetXY(58, 50);
$pdf->Cell(26, 4, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(58, 55);
$pdf->Cell(26, 4, $Search['Source'], 0, 1, 'L');
$pdf->SetXY(133, 41);
$pdf->Cell(28, 4, $Search['Nat_Mc'], 0, 1, 'L');
$pdf->SetXY(108, 45.5);
$pdf->Cell(25, 4, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(108, 50);
$pdf->Cell(25, 4, $Search['Sample_By'], 0, 1, 'L');
$pdf->SetXY(108, 55);
$pdf->Cell(25, 4, $Search['Sample_Date'], 0, 1, 'L');
$pdf->SetXY(161.5, 45.5);
$pdf->Cell(34, 4, $Search['Sample_Number'], 0, 1, 'L');
$pdf->SetXY(161.5, 50);
$pdf->Cell(34, 4, $Search['Depth_From'], 0, 1, 'L');
$pdf->SetXY(161.5, 55);
$pdf->Cell(34, 4, $Search['Depth_To'], 0, 1, 'L');
$pdf->SetXY(221, 45.5);
$pdf->Cell(30, 4, $Search['North'], 0, 1, 'L');
$pdf->SetXY(221, 50);
$pdf->Cell(30, 4, $Search['East'], 0, 1, 'L');
$pdf->SetXY(221, 55);
$pdf->Cell(30, 4, $Search['Elev'], 0, 1, 'L');
//LIQUID LIMIT
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(58, 68);
$pdf->Cell(26, 4, $Search['LL_Blows_1'], 0, 1, 'C');
$pdf->SetXY(84, 68);
$pdf->Cell(24, 4, $Search['LL_Blows_2'], 0, 1, 'C');
$pdf->SetXY(108.5, 68);
$pdf->Cell(24, 4, $Search['LL_Blows_3'], 0, 1, 'C');
$pdf->SetXY(58, 72.5);
$pdf->Cell(26, 4, $Search['LL_Container_1'], 0, 1, 'C');
$pdf->SetXY(84, 72.5);
$pdf->Cell(24, 4, $Search['LL_Container_2'], 0, 1, 'C');
$pdf->SetXY(108.5, 72.5);
$pdf->Cell(24, 4, $Search['LL_Container_3'], 0, 1, 'C');
$pdf->SetXY(58, 77);
$pdf->Cell(26, 4, $Search['LL_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(84, 77);
$pdf->Cell(24, 4, $Search['LL_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(108.5, 77);
$pdf->Cell(24, 4, $Search['LL_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(58, 81.3);
$pdf->Cell(26, 4, $Search['LL_Dry_Soil_Tare_1'], 0, 1, 'C');
$pdf->SetXY(84, 81.3);
$pdf->Cell(24, 4, $Search['LL_Dry_Soil_Tare_2'], 0, 1, 'C');
$pdf->SetXY(108.5, 81.3);
$pdf->Cell(24, 4, $Search['LL_Dry_Soil_Tare_3'], 0, 1, 'C');
$pdf->SetXY(58, 85.5);
$pdf->Cell(26, 4, $Search['LL_Water_1'], 0, 1, 'C');
$pdf->SetXY(84, 85.5);
$pdf->Cell(24, 4, $Search['LL_Water_2'], 0, 1, 'C');
$pdf->SetXY(108.5, 85.5);
$pdf->Cell(24, 4, $Search['LL_Water_3'], 0, 1, 'C');
$pdf->SetXY(58, 90);
$pdf->Cell(26, 4, $Search['LL_Tare_1'], 0, 1, 'C');
$pdf->SetXY(84, 90);
$pdf->Cell(24, 4, $Search['LL_Tare_2'], 0, 1, 'C');
$pdf->SetXY(108.5, 90);
$pdf->Cell(24, 4, $Search['LL_Tare_3'], 0, 1, 'C');
$pdf->SetXY(58, 94.5);
$pdf->Cell(26, 7, $Search['LL_Wt_Dry_Soil_1'], 0, 1, 'C');
$pdf->SetXY(84, 94.5);
$pdf->Cell(24, 7, $Search['LL_Wt_Dry_Soil_2'], 0, 1, 'C');
$pdf->SetXY(108.5, 94.5);
$pdf->Cell(24, 7, $Search['LL_Wt_Dry_Soil_3'], 0, 1, 'C');
$pdf->SetXY(58, 102);
$pdf->Cell(26, 9, $Search['LL_MC_Porce_1'], 0, 1, 'C');
$pdf->SetXY(84, 102);
$pdf->Cell(24, 9, $Search['LL_MC_Porce_2'], 0, 1, 'C');
$pdf->SetXY(108.5, 102);
$pdf->Cell(24, 9, $Search['LL_MC_Porce_3'], 0, 1, 'C');
// PLASTIC LIMIT
$pdf->SetXY(196, 68);
$pdf->Cell(24, 4, $Search['PL_Container_1'], 0, 1, 'C');
$pdf->SetXY(221, 68);
$pdf->Cell(30, 4, $Search['PL_Container_2'], 0, 1, 'C');
$pdf->SetXY(252, 68);
$pdf->Cell(27, 4, $Search['PL_Container_3'], 0, 1, 'C');
$pdf->SetXY(196, 72.5);
$pdf->Cell(24, 4, $Search['PL_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(221, 72.5);
$pdf->Cell(30, 4, $Search['PL_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(252, 72.5);
$pdf->Cell(27, 4, $Search['PL_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(196, 77);
$pdf->Cell(24, 4, $Search['PL_Dry_Soil_Tare_1'], 0, 1, 'C');
$pdf->SetXY(221, 77);
$pdf->Cell(30, 4, $Search['PL_Dry_Soil_Tare_2'], 0, 1, 'C');
$pdf->SetXY(252, 77);
$pdf->Cell(27, 4, $Search['PL_Dry_Soil_Tare_3'], 0, 1, 'C');
$pdf->SetXY(196, 81.3);
$pdf->Cell(24, 4, $Search['PL_Water_1'], 0, 1, 'C');
$pdf->SetXY(221, 81.3);
$pdf->Cell(30, 4, $Search['PL_Water_2'], 0, 1, 'C');
$pdf->SetXY(252, 81.3);
$pdf->Cell(27, 4, $Search['PL_Water_3'], 0, 1, 'C');
$pdf->SetXY(196, 85.5);
$pdf->Cell(24, 4, $Search['PL_Tare_1'], 0, 1, 'C');
$pdf->SetXY(221, 85.5);
$pdf->Cell(30, 4, $Search['PL_Tare_2'], 0, 1, 'C');
$pdf->SetXY(252, 85.5);
$pdf->Cell(27, 4, $Search['PL_Tare_3'], 0, 1, 'C');
$pdf->SetXY(196, 90);
$pdf->Cell(24, 4, $Search['PL_Wt_Dry_Soil_1'], 0, 1, 'C');
$pdf->SetXY(221, 90);
$pdf->Cell(30, 4, $Search['PL_Wt_Dry_Soil_2'], 0, 1, 'C');
$pdf->SetXY(252, 90);
$pdf->Cell(27, 4, $Search['PL_Wt_Dry_Soil_3'], 0, 1, 'C');
$pdf->SetXY(196, 94.5);
$pdf->Cell(24, 7, $Search['PL_MC_Porce_1'], 0, 1, 'C');
$pdf->SetXY(221, 94.5);
$pdf->Cell(30, 7, $Search['PL_MC_Porce_2'], 0, 1, 'C');
$pdf->SetXY(252, 94.5);
$pdf->Cell(27, 7, $Search['PL_MC_Porce_3'], 0, 1, 'C');
$pdf->SetXY(196, 102);
$pdf->Cell(83, 9, $Search['PL_Avg_Mc_Porce'], 0, 1, 'C');

// SUMMARY Atteberg Limit Parameter
$pdf->SetXY(46, 119.5);
$pdf->Cell(36, 2, $Search['Liquid_Limit_Porce'], 0, 1, 'L');
$pdf->SetXY(109, 119.5);
$pdf->Cell(36, 2, $Search['Plastic_Limit_Porce'], 0, 1, 'L');
$pdf->SetXY(190, 119.5);
$pdf->Cell(30, 2, $Search['Plasticity_Index_Porce'], 0, 1, 'L');
$pdf->SetXY(248, 119.5);
$pdf->Cell(30, 2, $Search['Liquidity_Index_Porce'], 0, 1, 'L');
$pdf->SetXY(221, 209);
$pdf->Cell(30, 8, $Search['Classification'], 0, 1, 'C');

// Laboratory Comments
$pdf->SetXY(133, 158);
$pdf->MultiCell(118, 4, $Search['Comments'], 0, 'L');

// GRAFICAS DEL LIMITER
$imageBase64 = $Search['Liquid_Limit_Plot'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 25, 130, 90, 0, 'PNG');
unlink($tempFile);

// GRAFICAS DEL Plasticity
$imageBase64 = $Search['Plasticity_Chart'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 25, 208, 90, 0, 'PNG');
unlink($tempFile);


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>