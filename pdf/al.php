<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('atterberg_limit', (int)$_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('L', array(360, 290));

$pdf->setSourceFile('al.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(58, 39);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(58, 45);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');

$pdf->SetXY(165, 32);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(165, 39);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(165, 46);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');

$pdf->SetXY(260, 32);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(260, 38);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');

$pdf->SetXY(62, 64);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(62, 70);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(62, 75);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(62, 80);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(169, 64);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(169, 69.5);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(169, 75);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(169, 80);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(260, 64);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(260, 69.5);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(260, 75);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(260, 80);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(69, 102);
$pdf->Cell(35, 9, '1', 0, 1, 'C');
$pdf->SetXY(104, 102);
$pdf->Cell(28, 9, '2', 0, 1, 'C');
$pdf->SetXY(132, 102);
$pdf->Cell(24, 9, '3', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(69, 188);
$pdf->Cell(35, 9, '1', 0, 1, 'C');
$pdf->SetXY(104, 188);
$pdf->Cell(28, 9, '2', 0, 1, 'C');
$pdf->SetXY(132, 188);
$pdf->Cell(24, 9, '3', 0, 1, 'C');

//lIQUID lIMIT

$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(69, 110);
$pdf->Cell(35, 9, $Search['LL_Blows_1'], 0, 1, 'C');
$pdf->SetXY(104, 110);
$pdf->Cell(28, 9, $Search['LL_Blows_2'], 0, 1, 'C');
$pdf->SetXY(132, 110);
$pdf->Cell(24, 9, $Search['LL_Blows_3'], 0, 1, 'C');
$pdf->SetXY(69, 119);
$pdf->Cell(35, 9, $Search['LL_Container_1'], 0, 1, 'C');
$pdf->SetXY(104, 119);
$pdf->Cell(28, 9, $Search['LL_Container_2'], 0, 1, 'C');
$pdf->SetXY(132, 119);
$pdf->Cell(24, 9, $Search['LL_Container_3'], 0, 1, 'C');
$pdf->SetXY(69, 127);
$pdf->Cell(35, 9, $Search['LL_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(104, 127);
$pdf->Cell(28, 9, $Search['LL_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(132, 127);
$pdf->Cell(24, 9, $Search['LL_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(69, 136);
$pdf->Cell(35, 9, $Search['LL_Dry_Soil_Tare_1'], 0, 1, 'C');
$pdf->SetXY(104, 136);
$pdf->Cell(28, 9, $Search['LL_Dry_Soil_Tare_2'], 0, 1, 'C');
$pdf->SetXY(132, 136);
$pdf->Cell(24, 9, $Search['LL_Dry_Soil_Tare_3'], 0, 1, 'C');
$pdf->SetXY(69, 144);
$pdf->Cell(35, 9, $Search['LL_Water_1'], 0, 1, 'C');
$pdf->SetXY(104, 144);
$pdf->Cell(28, 9, $Search['LL_Water_2'], 0, 1, 'C');
$pdf->SetXY(132, 144);
$pdf->Cell(24, 9, $Search['LL_Water_3'], 0, 1, 'C');
$pdf->SetXY(69, 153);
$pdf->Cell(35, 9, $Search['LL_Tare_1'], 0, 1, 'C');
$pdf->SetXY(104, 153);
$pdf->Cell(28, 9, $Search['LL_Tare_2'], 0, 1, 'C');
$pdf->SetXY(132, 153);
$pdf->Cell(24, 9, $Search['LL_Tare_3'], 0, 1, 'C');
$pdf->SetXY(69, 161);
$pdf->Cell(35, 9, $Search['LL_Wt_Dry_Soil_1'], 0, 1, 'C');
$pdf->SetXY(104, 161);
$pdf->Cell(28, 9, $Search['LL_Wt_Dry_Soil_2'], 0, 1, 'C');
$pdf->SetXY(132, 161);
$pdf->Cell(24, 9, $Search['LL_Wt_Dry_Soil_3'], 0, 1, 'C');
$pdf->SetXY(69, 169);
$pdf->Cell(35, 9, $Search['LL_MC_Porce_1'], 0, 1, 'C');
$pdf->SetXY(104, 169);
$pdf->Cell(28, 9, $Search['LL_MC_Porce_2'], 0, 1, 'C');
$pdf->SetXY(132, 169);
$pdf->Cell(24, 9, $Search['LL_MC_Porce_3'], 0, 1, 'C');

// PLASTIC LIMIT

$pdf->SetXY(69, 195);
$pdf->Cell(35, 9, $Search['PL_Container_1'], 0, 1, 'C');
$pdf->SetXY(104, 195);
$pdf->Cell(28, 9, $Search['PL_Container_2'], 0, 1, 'C');
$pdf->SetXY(132, 195);
$pdf->Cell(24, 9, $Search['PL_Container_3'], 0, 1, 'C');
$pdf->SetXY(69, 203);
$pdf->Cell(35, 9, $Search['PL_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(104, 203);
$pdf->Cell(28, 9, $Search['PL_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(132, 203);
$pdf->Cell(24, 9, $Search['PL_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(69, 210);
$pdf->Cell(35, 9, $Search['PL_Dry_Soil_Tare_1'], 0, 1, 'C');
$pdf->SetXY(104, 210);
$pdf->Cell(28, 9, $Search['PL_Dry_Soil_Tare_2'], 0, 1, 'C');
$pdf->SetXY(132, 210);
$pdf->Cell(24, 9, $Search['PL_Dry_Soil_Tare_3'], 0, 1, 'C');
$pdf->SetXY(69, 217);
$pdf->Cell(35, 9, $Search['PL_Water_1'], 0, 1, 'C');
$pdf->SetXY(104, 217);
$pdf->Cell(28, 9, $Search['PL_Water_2'], 0, 1, 'C');
$pdf->SetXY(132, 217);
$pdf->Cell(24, 9, $Search['PL_Water_3'], 0, 1, 'C');
$pdf->SetXY(69, 224);
$pdf->Cell(35, 9, $Search['PL_Tare_1'], 0, 1, 'C');
$pdf->SetXY(104, 224);
$pdf->Cell(28, 9, $Search['PL_Tare_2'], 0, 1, 'C');
$pdf->SetXY(132, 224);
$pdf->Cell(24, 9, $Search['PL_Tare_3'], 0, 1, 'C');
$pdf->SetXY(69, 231);
$pdf->Cell(35, 9, $Search['PL_Wt_Dry_Soil_1'], 0, 1, 'C');
$pdf->SetXY(104, 231);
$pdf->Cell(28, 9, $Search['PL_Wt_Dry_Soil_2'], 0, 1, 'C');
$pdf->SetXY(132, 231);
$pdf->Cell(24, 9, $Search['PL_Wt_Dry_Soil_3'], 0, 1, 'C');
$pdf->SetXY(69, 238.5);
$pdf->Cell(35, 9, $Search['PL_MC_Porce_1'], 0, 1, 'C');
$pdf->SetXY(104, 238.5);
$pdf->Cell(28, 9, $Search['PL_MC_Porce_2'], 0, 1, 'C');
$pdf->SetXY(132, 238.5);
$pdf->Cell(24, 9, $Search['PL_MC_Porce_3'], 0, 1, 'C');
$pdf->SetXY(69, 246);
$pdf->Cell(87, 7, $Search['PL_Avg_Mc_Porce'], 0, 1, 'C');

// SUMMARY Atteberg Limit Parameter

$pdf->SetXY(320, 102);
$pdf->Cell(24, 9, $Search['Liquid_Limit_Porce'], 0, 1, 'C');
$pdf->SetXY(320, 111);
$pdf->Cell(24, 8, $Search['Plastic_Limit_Porce'], 0, 1, 'C');
$pdf->SetXY(320, 119);
$pdf->Cell(24, 9, $Search['Plasticity_Index_Porce'], 0, 1, 'C');
$pdf->SetXY(320, 127);
$pdf->Cell(24, 9, $Search['Liquidity_Index_Porce'], 0, 1, 'C');
$pdf->SetXY(320, 144);
$pdf->Cell(24, 25, $Search['Classification'], 0, 1, 'C');

// Laboratory Comments

$pdf->SetXY(284, 179);
$pdf->Cell(60, 68, $Search['Comments'], 0, 1, 'C');

// GRAFICAS DEL LIMITER
$imageBase64 = $Search['Liquid_Limit_Plot'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 170, 100, 100, 0, 'PNG');
unlink($tempFile);

// GRAFICAS DEL Plasticity
$imageBase64 = $Search['Plasticity_Chart'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 175, 185, 100, 0, 'PNG');
unlink($tempFile);


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>