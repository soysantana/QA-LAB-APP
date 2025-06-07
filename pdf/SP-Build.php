<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('standard_proctor', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(530, 430));

$pdf->setSourceFile('template/PV-F-01721 Laboratory Compaction Standard Proctor Effort_Rev 1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(100, 63);
$pdf->Cell(31, 4, $Search['Project_Name'], 0, 1, 'C');
$pdf->SetXY(100, 86);
$pdf->Cell(31, 4, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(100, 94);
$pdf->Cell(31, 4, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(100, 102);
$pdf->Cell(31, 4, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(100, 123);
$pdf->Cell(31, 4, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(100, 131);
$pdf->Cell(31, 4, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(100, 139);
$pdf->Cell(31, 4, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(100, 147);
$pdf->Cell(31, 4, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(240, 63);
$pdf->Cell(31, 4, $Search['Project_Number'], 0, 1, 'C');
$pdf->SetXY(240, 86);
$pdf->Cell(31, 4, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(240, 94);
$pdf->Cell(31, 4, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(240, 102);
$pdf->Cell(31, 4, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(240, 123);
$pdf->Cell(31, 4, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(240, 131);
$pdf->Cell(31, 4, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(240, 139);
$pdf->Cell(31, 4, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(240, 147);
$pdf->Cell(31, 4, $Search['Elev'], 0, 1, 'C');
$pdf->SetXY(240, 156);
$pdf->Cell(31, 4, $Search['Nat_Mc'], 0, 1, 'C');

$pdf->SetXY(380, 63);
$pdf->Cell(31, 4, $Search['Client'], 0, 1, 'C');
$pdf->SetXY(380, 86);
$pdf->Cell(31, 4, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(380, 94);
$pdf->Cell(31, 4, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(380, 102);
$pdf->Cell(31, 4, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(380, 123);
$pdf->Cell(31, 4, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(380, 131);
$pdf->Cell(31, 4, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(380, 139);
$pdf->Cell(31, 4, $Search['North'], 0, 1, 'C');
$pdf->SetXY(380, 147);
$pdf->Cell(31, 4, $Search['East'], 0, 1, 'C');
$pdf->SetXY(380, 156);
$pdf->Cell(31, 4, $Search['Spec_Gravity'], 0, 1, 'C');

// Testing Information Firts table
$pdf->SetFont('Arial', '', 10);

$pdf->SetXY(121, 180);
$pdf->Cell(29, 5, $Search['WetSoilMod1'], 0, 1, 'C');
$pdf->SetXY(150, 180);
$pdf->Cell(28, 5, $Search['WetSoilMod2'], 0, 1, 'C');
$pdf->SetXY(178, 180);
$pdf->Cell(29, 5, $Search['WetSoilMod3'], 0, 1, 'C');
$pdf->SetXY(208, 180);
$pdf->Cell(27, 5, $Search['WetSoilMod4'], 0, 1, 'C');
$pdf->SetXY(235, 180);
$pdf->Cell(27, 5, $Search['WetSoilMod5'], 0, 1, 'C');
$pdf->SetXY(262, 180);
$pdf->Cell(28, 5, $Search['WetSoilMod6'], 0, 1, 'C');

$pdf->SetXY(121, 185);
$pdf->Cell(29, 5, $Search['WtMold1'], 0, 1, 'C');
$pdf->SetXY(150, 185);
$pdf->Cell(28, 5, $Search['WtMold2'], 0, 1, 'C');
$pdf->SetXY(178, 185);
$pdf->Cell(29, 5, $Search['WtMold3'], 0, 1, 'C');
$pdf->SetXY(208, 185);
$pdf->Cell(27, 5, $Search['WtMold4'], 0, 1, 'C');
$pdf->SetXY(235, 185);
$pdf->Cell(27, 5, $Search['WtMold5'], 0, 1, 'C');
$pdf->SetXY(262, 185);
$pdf->Cell(28, 5, $Search['WtMold6'], 0, 1, 'C');

$pdf->SetXY(121, 191);
$pdf->Cell(29, 5, $Search['WtSoil1'], 0, 1, 'C');
$pdf->SetXY(150, 191);
$pdf->Cell(28, 5, $Search['WtSoil2'], 0, 1, 'C');
$pdf->SetXY(178, 191);
$pdf->Cell(29, 5, $Search['WtSoil3'], 0, 1, 'C');
$pdf->SetXY(208, 191);
$pdf->Cell(27, 5, $Search['WtSoil4'], 0, 1, 'C');
$pdf->SetXY(235, 191);
$pdf->Cell(27, 5, $Search['WtSoil5'], 0, 1, 'C');
$pdf->SetXY(262, 191);
$pdf->Cell(28, 5, $Search['WtSoil6'], 0, 1, 'C');

$pdf->SetXY(121, 196);
$pdf->Cell(29, 5, $Search['VolMold1'], 0, 1, 'C');
$pdf->SetXY(150, 196);
$pdf->Cell(28, 5, $Search['VolMold2'], 0, 1, 'C');
$pdf->SetXY(178, 196);
$pdf->Cell(29, 5, $Search['VolMold3'], 0, 1, 'C');
$pdf->SetXY(208, 196);
$pdf->Cell(27, 5, $Search['VolMold4'], 0, 1, 'C');
$pdf->SetXY(235, 196);
$pdf->Cell(27, 5, $Search['VolMold5'], 0, 1, 'C');
$pdf->SetXY(262, 196);
$pdf->Cell(28, 5, $Search['VolMold6'], 0, 1, 'C');

$pdf->SetXY(121, 202);
$pdf->Cell(29, 5, $Search['WetDensity1'], 0, 1, 'C');
$pdf->SetXY(150, 202);
$pdf->Cell(28, 5, $Search['WetDensity2'], 0, 1, 'C');
$pdf->SetXY(178, 202);
$pdf->Cell(29, 5, $Search['WetDensity3'], 0, 1, 'C');
$pdf->SetXY(208, 202);
$pdf->Cell(27, 5, $Search['WetDensity4'], 0, 1, 'C');
$pdf->SetXY(235, 202);
$pdf->Cell(27, 5, $Search['WetDensity5'], 0, 1, 'C');
$pdf->SetXY(262, 202);
$pdf->Cell(28, 5, $Search['WetDensity6'], 0, 1, 'C');

$pdf->SetXY(121, 207);
$pdf->Cell(29, 5, $Search['DryDensity1'], 0, 1, 'C');
$pdf->SetXY(150, 207);
$pdf->Cell(28, 5, $Search['DryDensity2'], 0, 1, 'C');
$pdf->SetXY(178, 207);
$pdf->Cell(29, 5, $Search['DryDensity3'], 0, 1, 'C');
$pdf->SetXY(208, 207);
$pdf->Cell(27, 5, $Search['DryDensity4'], 0, 1, 'C');
$pdf->SetXY(235, 207);
$pdf->Cell(27, 5, $Search['DryDensity5'], 0, 1, 'C');
$pdf->SetXY(262, 207);
$pdf->Cell(28, 5, $Search['DryDensity6'], 0, 1, 'C');

$pdf->SetXY(121, 212);
$pdf->Cell(29, 5, $Search['DensyCorrected1'], 0, 1, 'C');
$pdf->SetXY(150, 212);
$pdf->Cell(28, 5, $Search['DensyCorrected2'], 0, 1, 'C');
$pdf->SetXY(178, 212);
$pdf->Cell(29, 5, $Search['DensyCorrected3'], 0, 1, 'C');
$pdf->SetXY(208, 212);
$pdf->Cell(27, 5, $Search['DensyCorrected4'], 0, 1, 'C');
$pdf->SetXY(235, 212);
$pdf->Cell(27, 5, $Search['DensyCorrected5'], 0, 1, 'C');
$pdf->SetXY(262, 212);
$pdf->Cell(28, 5, $Search['DensyCorrected6'], 0, 1, 'C');


// Testing Information Second table
$pdf->SetXY(121, 228);
$pdf->Cell(29, 5, $Search['Container1'], 0, 1, 'C');
$pdf->SetXY(150, 228);
$pdf->Cell(28, 5, $Search['Container2'], 0, 1, 'C');
$pdf->SetXY(178, 228);
$pdf->Cell(29, 5, $Search['Container3'], 0, 1, 'C');
$pdf->SetXY(208, 228);
$pdf->Cell(27, 5, $Search['Container4'], 0, 1, 'C');
$pdf->SetXY(235, 228);
$pdf->Cell(27, 5, $Search['Container5'], 0, 1, 'C');
$pdf->SetXY(262, 228);
$pdf->Cell(28, 5, $Search['Container6'], 0, 1, 'C');

$pdf->SetXY(121, 234);
$pdf->Cell(29, 5, $Search['WetSoilTare1'], 0, 1, 'C');
$pdf->SetXY(150, 234);
$pdf->Cell(28, 5, $Search['WetSoilTare2'], 0, 1, 'C');
$pdf->SetXY(178, 234);
$pdf->Cell(29, 5, $Search['WetSoilTare3'], 0, 1, 'C');
$pdf->SetXY(208, 234);
$pdf->Cell(27, 5, $Search['WetSoilTare4'], 0, 1, 'C');
$pdf->SetXY(235, 234);
$pdf->Cell(27, 5, $Search['WetSoilTare5'], 0, 1, 'C');
$pdf->SetXY(262, 234);
$pdf->Cell(28, 5, $Search['WetSoilTare6'], 0, 1, 'C');

$pdf->SetXY(121, 239);
$pdf->Cell(29, 5, $Search['WetDryTare1'], 0, 1, 'C');
$pdf->SetXY(150, 239);
$pdf->Cell(28, 5, $Search['WetDryTare2'], 0, 1, 'C');
$pdf->SetXY(178, 239);
$pdf->Cell(29, 5, $Search['WetDryTare3'], 0, 1, 'C');
$pdf->SetXY(208, 239);
$pdf->Cell(27, 5, $Search['WetDryTare4'], 0, 1, 'C');
$pdf->SetXY(235, 239);
$pdf->Cell(27, 5, $Search['WetDryTare5'], 0, 1, 'C');
$pdf->SetXY(262, 239);
$pdf->Cell(28, 5, $Search['WetDryTare6'], 0, 1, 'C');

$pdf->SetXY(121, 244);
$pdf->Cell(29, 5, $Search['WtWater1'], 0, 1, 'C');
$pdf->SetXY(150, 244);
$pdf->Cell(28, 5, $Search['WtWater2'], 0, 1, 'C');
$pdf->SetXY(178, 244);
$pdf->Cell(29, 5, $Search['WtWater3'], 0, 1, 'C');
$pdf->SetXY(208, 244);
$pdf->Cell(27, 5, $Search['WtWater4'], 0, 1, 'C');
$pdf->SetXY(235, 244);
$pdf->Cell(27, 5, $Search['WtWater5'], 0, 1, 'C');
$pdf->SetXY(262, 244);
$pdf->Cell(28, 5, $Search['WtWater6'], 0, 1, 'C');

$pdf->SetXY(121, 249);
$pdf->Cell(29, 6, $Search['Tare1'], 0, 1, 'C');
$pdf->SetXY(150, 249);
$pdf->Cell(28, 6, $Search['Tare2'], 0, 1, 'C');
$pdf->SetXY(178, 249);
$pdf->Cell(29, 6, $Search['Tare3'], 0, 1, 'C');
$pdf->SetXY(208, 249);
$pdf->Cell(27, 6, $Search['Tare4'], 0, 1, 'C');
$pdf->SetXY(235, 249);
$pdf->Cell(27, 6, $Search['Tare5'], 0, 1, 'C');
$pdf->SetXY(262, 249);
$pdf->Cell(28, 6, $Search['Tare6'], 0, 1, 'C');

$pdf->SetXY(121, 255);
$pdf->Cell(29, 5, $Search['DrySoil1'], 0, 1, 'C');
$pdf->SetXY(150, 255);
$pdf->Cell(28, 5, $Search['DrySoil2'], 0, 1, 'C');
$pdf->SetXY(178, 255);
$pdf->Cell(29, 5, $Search['DrySoil3'], 0, 1, 'C');
$pdf->SetXY(208, 255);
$pdf->Cell(27, 5, $Search['DrySoil4'], 0, 1, 'C');
$pdf->SetXY(235, 255);
$pdf->Cell(27, 5, $Search['DrySoil5'], 0, 1, 'C');
$pdf->SetXY(262, 255);
$pdf->Cell(28, 5, $Search['DrySoil6'], 0, 1, 'C');

$pdf->SetXY(121, 260);
$pdf->Cell(29, 5, $Search['MoisturePorce1'], 0, 1, 'C');
$pdf->SetXY(150, 260);
$pdf->Cell(28, 5, $Search['MoisturePorce2'], 0, 1, 'C');
$pdf->SetXY(178, 260);
$pdf->Cell(29, 5, $Search['MoisturePorce3'], 0, 1, 'C');
$pdf->SetXY(208, 260);
$pdf->Cell(27, 5, $Search['MoisturePorce4'], 0, 1, 'C');
$pdf->SetXY(235, 260);
$pdf->Cell(27, 5, $Search['MoisturePorce5'], 0, 1, 'C');
$pdf->SetXY(262, 260);
$pdf->Cell(28, 5, $Search['MoisturePorce6'], 0, 1, 'C');

$pdf->SetXY(121, 265);
$pdf->Cell(29, 5, $Search['MCcorrected1'], 0, 1, 'C');
$pdf->SetXY(150, 265);
$pdf->Cell(28, 5, $Search['MCcorrected2'], 0, 1, 'C');
$pdf->SetXY(178, 265);
$pdf->Cell(29, 5, $Search['MCcorrected3'], 0, 1, 'C');
$pdf->SetXY(208, 265);
$pdf->Cell(27, 5, $Search['MCcorrected4'], 0, 1, 'C');
$pdf->SetXY(235, 265);
$pdf->Cell(27, 5, $Search['MCcorrected5'], 0, 1, 'C');
$pdf->SetXY(262, 265);
$pdf->Cell(28, 5, $Search['MCcorrected6'], 0, 1, 'C');

// Max Dry Density and Optimum Moisture Content
$pdf->SetXY(121, 271);
$pdf->Cell(29, 7, $Search['Max_Dry_Density_kgm3'], 0, 1, 'C');
$pdf->SetXY(208, 271);
$pdf->Cell(27, 7, $Search['Optimun_MC_Porce'], 0, 1, 'C');

// Corrected Dry Unit Weight and Water Content Finer
$pdf->SetXY(150, 292);
$pdf->Cell(28, 12, $Search['Corrected_Dry_Unit_Weigt'], 0, 1, 'C');
$pdf->SetXY(150, 304);
$pdf->Cell(28, 12, $Search['Corrected_Water_Content_Finer'], 0, 1, 'C');
$pdf->SetXY(150, 316);
$pdf->Cell(28, 5, $Search['YDF_Porce'], 0, 1, 'C');
$pdf->SetXY(150, 321);
$pdf->Cell(28, 7, $Search['PF_Porce'], 0, 1, 'C');
$pdf->SetXY(150, 328);
$pdf->Cell(28, 13, $Search['YDT_Porce'], 0, 1, 'C');
$pdf->SetXY(82, 316);
$pdf->Cell(39, 5, $Search['Wc_Porce'], 1, 1, 'C');
$pdf->SetXY(82, 321);
$pdf->Cell(39, 7, $Search['PC_Porce'], 0, 1, 'C');
$pdf->SetXY(82, 328);
$pdf->Cell(39, 6, $Search['GM_Porce'], 0, 1, 'C');
$pdf->SetXY(82, 335);
$pdf->Cell(39, 6, $Search['Yw_KnM3'], 0, 1, 'C');

// Test Result
$pdf->SetXY(82, 351);
$pdf->Cell(39, 10, '', 0, 1, 'C');

// Comparison Information
$pdf->SetXY(82, 378);
$pdf->Cell(39, 7, $Search['Max_Dry_Density_kgm3'], 0, 1, 'C');
$pdf->SetXY(82, 385);
$pdf->Cell(39, 10, $Search['Optimun_MC_Porce'], 0, 1, 'C');

// Comments and Observations
$pdf->SetXY(46, 410);
$pdf->MultiCell(160, 4, $Search['Comments'], 0, 'L');
$pdf->SetXY(210, 410);
$pdf->MultiCell(160, 4, $Search['FieldComment'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 190, 290, 0, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
