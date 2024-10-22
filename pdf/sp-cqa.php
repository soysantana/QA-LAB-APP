<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('standard_proctor', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('L', array(530, 390));

$pdf->setSourceFile('PV-TSF-CQA-Standard Proctor Rev.2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);
// Project Information
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetXY(90.5, 50.5);
$pdf->Cell(31, 6.5, 'Pueblo Viejo', 0, 1, 'L');
$pdf->SetXY(90.5, 57.5);
$pdf->Cell(31, 6.5, $Search['Project_Number'], 0, 1, 'L');
$pdf->SetXY(90.5, 64);
$pdf->Cell(31, 6.5, $Search['Client'], 0, 1, 'L');
// Sample Information
$pdf->SetXY(157, 50.5);
$pdf->Cell(31, 6.5, $Search['Standard'], 0, 1, 'L');
$pdf->SetXY(157, 57.5);
$pdf->Cell(31, 6.5, $Search['Area'], 0, 1, 'L');
$pdf->SetXY(157, 64);
$pdf->Cell(31, 6.5, $Search['Registed_Date'], 0, 1, 'L');
$pdf->SetXY(222, 50.5);
$pdf->Cell(30, 6.5, $Search['Sample_Number'], 0, 1, 'L');
$pdf->SetXY(222, 57.5);
$pdf->Cell(30, 6.5, $Search['Material_Type'], 0, 1, 'L');
$pdf->SetXY(222, 64);
$pdf->Cell(30, 6.5, $Search['Depth_From'] . '-' . $Search['Depth_To'], 0, 1, 'L');
$pdf->SetXY(279.5, 50.5);
$pdf->Cell(31, 6.5, $Search['North'], 0, 1, 'L');
$pdf->SetXY(279.5, 57.5);
$pdf->Cell(31, 6.5, $Search['East'], 0, 1, 'L');
$pdf->SetXY(279.5, 64);
$pdf->Cell(31, 6.5, $Search['Elev'], 0, 1, 'L');
$pdf->SetXY(339, 50.5);
$pdf->Cell(28, 6.5, $Search['Sample_Date'], 0, 1, 'L');
$pdf->SetXY(339, 57.5);
$pdf->Cell(28, 6.5, $Search['Sample_By'], 0, 1, 'L');
// Laboratory Information
$pdf->SetXY(392.5, 50.5);
$pdf->Cell(26, 6.5, 'PVDJ Soil Lab', 0, 1, 'L');
$pdf->SetXY(392.5, 57.5);
$pdf->Cell(26, 6.5, $Search['Technician'], 0, 1, 'L');
$pdf->SetXY(392.5, 64);
$pdf->Cell(26, 6.5, $Search['Standard'], 0, 1, 'L');
$pdf->SetXY(445, 50.5);
$pdf->Cell(26, 6.5, $Search['Methods'], 0, 1, 'L');
$pdf->SetXY(445, 57.5);
$pdf->Cell(26, 6.5, $Search['Test_Start_Date'], 0, 1, 'L');
$pdf->SetXY(445, 64);
$pdf->Cell(26, 6.5, $Search['Registed_Date'], 0, 1, 'L');
// Testing Information
$pdf->SetXY(90.5, 78);
$pdf->Cell(31, 6.5, $Search['Nat_Mc'], 0, 1, 'L');
$pdf->SetXY(90.5, 84.5);
$pdf->Cell(31, 6.5, $Search['Spec_Gravity'], 0, 1, 'L');
$pdf->SetXY(188.5, 78);
$pdf->Cell(33, 6.5, $Search['Preparation_Method'], 0, 1, 'L');
$pdf->SetXY(188.5, 84.5);
$pdf->Cell(33, 6.5, $Search['Split_Method'], 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);

// Testing Information
$pdf->SetXY(90.5, 101.5);
$pdf->Cell(31, 6.5, $Search['WetSoilMod1'], 0, 1, 'C');
$pdf->SetXY(122, 101.5);
$pdf->Cell(35, 6.5, $Search['WetSoilMod2'], 0, 1, 'C');
$pdf->SetXY(157, 101.5);
$pdf->Cell(31.5, 6.5, $Search['WetSoilMod3'], 0, 1, 'C');
$pdf->SetXY(188.5, 101.5);
$pdf->Cell(33.5, 6.5, $Search['WetSoilMod4'], 0, 1, 'C');
$pdf->SetXY(222, 101.5);
$pdf->Cell(30, 6.5, $Search['WetSoilMod5'], 0, 1, 'C');
$pdf->SetXY(252, 101.5);
$pdf->Cell(27.5, 6.5, $Search['WetSoilMod6'], 0, 1, 'C');

$pdf->SetXY(90.5, 108.5);
$pdf->Cell(31, 6.5, $Search['WtMold1'], 0, 1, 'C');
$pdf->SetXY(122, 108.5);
$pdf->Cell(35, 6.5, $Search['WtMold2'], 0, 1, 'C');
$pdf->SetXY(157, 108.5);
$pdf->Cell(31.5, 6.5, $Search['WtMold3'], 0, 1, 'C');
$pdf->SetXY(188.5, 108.5);
$pdf->Cell(33.5, 6.5, $Search['WtMold4'], 0, 1, 'C');
$pdf->SetXY(222, 108.5);
$pdf->Cell(30, 6.5, $Search['WtMold5'], 0, 1, 'C');
$pdf->SetXY(252, 108.5);
$pdf->Cell(27.5, 6.5, $Search['WtMold6'], 0, 1, 'C');

$pdf->SetXY(90.5, 115);
$pdf->Cell(31, 6.5, $Search['WtSoil1'], 0, 1, 'C');
$pdf->SetXY(122, 115);
$pdf->Cell(35, 6.5, $Search['WtSoil2'], 0, 1, 'C');
$pdf->SetXY(157, 115);
$pdf->Cell(31.5, 6.5, $Search['WtSoil3'], 0, 1, 'C');
$pdf->SetXY(188.5, 115);
$pdf->Cell(33.5, 6.5, $Search['WtSoil4'], 0, 1, 'C');
$pdf->SetXY(222, 115);
$pdf->Cell(30, 6.5, $Search['WtSoil5'], 0, 1, 'C');
$pdf->SetXY(252, 115);
$pdf->Cell(27.5, 6.5, $Search['WtSoil6'], 0, 1, 'C');

$pdf->SetXY(90.5, 122);
$pdf->Cell(31, 6.5, $Search['VolMold1'], 0, 1, 'C');
$pdf->SetXY(122, 122);
$pdf->Cell(35, 6.5, $Search['VolMold2'], 0, 1, 'C');
$pdf->SetXY(157, 122);
$pdf->Cell(31.5, 6.5, $Search['VolMold3'], 0, 1, 'C');
$pdf->SetXY(188.5, 122);
$pdf->Cell(33.5, 6.5, $Search['VolMold4'], 0, 1, 'C');
$pdf->SetXY(222, 122);
$pdf->Cell(30, 6.5, $Search['VolMold5'], 0, 1, 'C');
$pdf->SetXY(252, 122);
$pdf->Cell(27.5, 6.5, $Search['VolMold6'], 0, 1, 'C');

$pdf->SetXY(90.5, 129);
$pdf->Cell(31, 6.5, $Search['WetDensity1'], 0, 1, 'C');
$pdf->SetXY(122, 129);
$pdf->Cell(35, 6.5, $Search['WetDensity2'], 0, 1, 'C');
$pdf->SetXY(157, 129);
$pdf->Cell(31.5, 6.5, $Search['WetDensity3'], 0, 1, 'C');
$pdf->SetXY(188.5, 129);
$pdf->Cell(33.5, 6.5, $Search['WetDensity4'], 0, 1, 'C');
$pdf->SetXY(222, 129);
$pdf->Cell(30, 6.5, $Search['WetDensity5'], 0, 1, 'C');
$pdf->SetXY(252, 129);
$pdf->Cell(27.5, 6.5, $Search['WetDensity6'], 0, 1, 'C');

$pdf->SetXY(90.5, 135);
$pdf->Cell(31, 6.5, $Search['DryDensity1'], 0, 1, 'C');
$pdf->SetXY(122, 135);
$pdf->Cell(35, 6.5, $Search['DryDensity2'], 0, 1, 'C');
$pdf->SetXY(157, 135);
$pdf->Cell(31.5, 6.5, $Search['DryDensity3'], 0, 1, 'C');
$pdf->SetXY(188.5, 135);
$pdf->Cell(33.5, 6.5, $Search['DryDensity4'], 0, 1, 'C');
$pdf->SetXY(222, 135);
$pdf->Cell(30, 6.5, $Search['DryDensity5'], 0, 1, 'C');
$pdf->SetXY(252, 135);
$pdf->Cell(27.5, 6.5, $Search['DryDensity6'], 0, 1, 'C');

$pdf->SetXY(90.5, 142.5);
$pdf->Cell(31, 6.5, $Search['DensyCorrected1'], 0, 1, 'C');
$pdf->SetXY(122, 142.5);
$pdf->Cell(35, 6.5, $Search['DensyCorrected2'], 0, 1, 'C');
$pdf->SetXY(157, 142.5);
$pdf->Cell(31.5, 6.5, $Search['DensyCorrected3'], 0, 1, 'C');
$pdf->SetXY(188.5, 142.5);
$pdf->Cell(33.5, 6.5, $Search['DensyCorrected4'], 0, 1, 'C');
$pdf->SetXY(222, 142.5);
$pdf->Cell(30, 6.5, $Search['DensyCorrected5'], 0, 1, 'C');
$pdf->SetXY(252, 142.5);
$pdf->Cell(27.5, 6.5, $Search['DensyCorrected6'], 0, 1, 'C');

// Testing Information Second table
$pdf->SetXY(90.5, 163);
$pdf->Cell(31, 6.5, $Search['Container1'], 0, 1, 'C');
$pdf->SetXY(122, 163);
$pdf->Cell(35, 6.5, $Search['Container2'], 0, 1, 'C');
$pdf->SetXY(157, 163);
$pdf->Cell(31.5, 6.5, $Search['Container3'], 0, 1, 'C');
$pdf->SetXY(188.5, 163);
$pdf->Cell(33.5, 6.5, $Search['Container4'], 0, 1, 'C');
$pdf->SetXY(222, 163);
$pdf->Cell(30, 6.5, $Search['Container5'], 0, 1, 'C');
$pdf->SetXY(252, 163);
$pdf->Cell(27.5, 6.5, $Search['Container6'], 0, 1, 'C');

$pdf->SetXY(90.5, 170);
$pdf->Cell(31, 6.5, $Search['WetSoilTare1'], 0, 1, 'C');
$pdf->SetXY(122, 170);
$pdf->Cell(35, 6.5, $Search['WetSoilTare2'], 0, 1, 'C');
$pdf->SetXY(157, 170);
$pdf->Cell(31.5, 6.5, $Search['WetSoilTare3'], 0, 1, 'C');
$pdf->SetXY(188.5, 170);
$pdf->Cell(33.5, 6.5, $Search['WetSoilTare4'], 0, 1, 'C');
$pdf->SetXY(222, 170);
$pdf->Cell(30, 6.5, $Search['WetSoilTare5'], 0, 1, 'C');
$pdf->SetXY(252, 170);
$pdf->Cell(27.5, 6.5, $Search['WetSoilTare6'], 0, 1, 'C');

$pdf->SetXY(90.5, 177);
$pdf->Cell(31, 6.5, $Search['WetDryTare1'], 0, 1, 'C');
$pdf->SetXY(122, 177);
$pdf->Cell(35, 6.5, $Search['WetDryTare2'], 0, 1, 'C');
$pdf->SetXY(157, 177);
$pdf->Cell(31.5, 6.5, $Search['WetDryTare3'], 0, 1, 'C');
$pdf->SetXY(188.5, 177);
$pdf->Cell(33.5, 6.5, $Search['WetDryTare4'], 0, 1, 'C');
$pdf->SetXY(222, 177);
$pdf->Cell(30, 6.5, $Search['WetDryTare5'], 0, 1, 'C');
$pdf->SetXY(252, 177);
$pdf->Cell(27.5, 6.5, $Search['WetDryTare6'], 0, 1, 'C');

$pdf->SetXY(90.5, 184);
$pdf->Cell(31, 6.5, $Search['WtWater1'], 0, 1, 'C');
$pdf->SetXY(122, 184);
$pdf->Cell(35, 6.5, $Search['WtWater2'], 0, 1, 'C');
$pdf->SetXY(157, 184);
$pdf->Cell(31.5, 6.5, $Search['WtWater3'], 0, 1, 'C');
$pdf->SetXY(188.5, 184);
$pdf->Cell(33.5, 6.5, $Search['WtWater4'], 0, 1, 'C');
$pdf->SetXY(222, 184);
$pdf->Cell(30, 6.5, $Search['WtWater5'], 0, 1, 'C');
$pdf->SetXY(252, 184);
$pdf->Cell(27.5, 6.5, $Search['WtWater6'], 0, 1, 'C');

$pdf->SetXY(90.5, 190.5);
$pdf->Cell(31, 6.5, $Search['Tare1'], 0, 1, 'C');
$pdf->SetXY(122, 190.5);
$pdf->Cell(35, 6.5, $Search['Tare2'], 0, 1, 'C');
$pdf->SetXY(157, 190.5);
$pdf->Cell(31.5, 6.5, $Search['Tare3'], 0, 1, 'C');
$pdf->SetXY(188.5, 190.5);
$pdf->Cell(33.5, 6.5, $Search['Tare4'], 0, 1, 'C');
$pdf->SetXY(222, 190.5);
$pdf->Cell(30, 6.5, $Search['Tare5'], 0, 1, 'C');
$pdf->SetXY(252, 190.5);
$pdf->Cell(27.5, 6.5, $Search['Tare6'], 0, 1, 'C');

$pdf->SetXY(90.5, 198);
$pdf->Cell(31, 6.5, $Search['DrySoil1'], 0, 1, 'C');
$pdf->SetXY(122, 198);
$pdf->Cell(35, 6.5, $Search['DrySoil2'], 0, 1, 'C');
$pdf->SetXY(157, 198);
$pdf->Cell(31.5, 6.5, $Search['DrySoil3'], 0, 1, 'C');
$pdf->SetXY(188.5, 198);
$pdf->Cell(33.5, 6.5, $Search['DrySoil4'], 0, 1, 'C');
$pdf->SetXY(222, 198);
$pdf->Cell(30, 6.5, $Search['DrySoil5'], 0, 1, 'C');
$pdf->SetXY(252, 198);
$pdf->Cell(27.5, 6.5, $Search['DrySoil6'], 0, 1, 'C');

$pdf->SetXY(90.5, 204.5);
$pdf->Cell(31, 6.5, $Search['MoisturePorce1'], 0, 1, 'C');
$pdf->SetXY(122, 204.5);
$pdf->Cell(35, 6.5, $Search['MoisturePorce2'], 0, 1, 'C');
$pdf->SetXY(157, 204.5);
$pdf->Cell(31.5, 6.5, $Search['MoisturePorce3'], 0, 1, 'C');
$pdf->SetXY(188.5, 204.5);
$pdf->Cell(33.5, 6.5, $Search['MoisturePorce4'], 0, 1, 'C');
$pdf->SetXY(222, 204.5);
$pdf->Cell(30, 6.5, $Search['MoisturePorce5'], 0, 1, 'C');
$pdf->SetXY(252, 204.5);
$pdf->Cell(27.5, 6.5, $Search['MoisturePorce6'], 0, 1, 'C');

$pdf->SetXY(90.5, 211);
$pdf->Cell(31, 6.5, $Search['MCcorrected1'], 0, 1, 'C');
$pdf->SetXY(122, 211);
$pdf->Cell(35, 6.5, $Search['MCcorrected2'], 0, 1, 'C');
$pdf->SetXY(157, 211);
$pdf->Cell(31.5, 6.5, $Search['MCcorrected3'], 0, 1, 'C');
$pdf->SetXY(188.5, 211);
$pdf->Cell(33.5, 6.5, $Search['MCcorrected4'], 0, 1, 'C');
$pdf->SetXY(222, 211);
$pdf->Cell(30, 6.5, $Search['MCcorrected5'], 0, 1, 'C');
$pdf->SetXY(252, 211);
$pdf->Cell(27.5, 6.5, $Search['MCcorrected6'], 0, 1, 'C');


$pdf->SetXY(90.5, 218);
$pdf->Cell(31, 7, $Search['Max_Dry_Density_kgm3'], 0, 1, 'C');
$pdf->SetXY(90.5, 225);
$pdf->Cell(31, 7, $Search['Optimun_MC_Porce'], 0, 1, 'C');

$pdf->SetXY(418, 177);
$pdf->Cell(27, 13, $Search['Corrected_Dry_Unit_Weigt'], 1, 1, 'C');
$pdf->SetXY(418, 190.5);
$pdf->Cell(27, 13, $Search['Corrected_Water_Content_Finer'], 1, 1, 'C');

$pdf->SetXY(418.5, 135);
$pdf->Cell(26, 7, $Search['YDF_Porce'], 0, 1, 'C');

$pdf->SetXY(418.5, 142);
$pdf->Cell(26, 7, $Search['PF_Porce'], 0, 1, 'C');

$pdf->SetXY(418.5, 149);
$pdf->Cell(26, 6, $Search['YDT_Porce'], 0, 1, 'C');

$pdf->SetXY(367.5, 135);
$pdf->Cell(25, 7, $Search['Wc_Porce'], 0, 1, 'C');

$pdf->SetXY(367.5, 142);
$pdf->Cell(25, 7, $Search['PC_Porce'], 0, 1, 'C');

$pdf->SetXY(367.5, 149);
$pdf->Cell(25, 6, $Search['GM_Porce'], 0, 1, 'C');

$pdf->SetXY(367.5, 155.5);
$pdf->Cell(25, 6, $Search['Yw_KnM3'], 0, 1, 'C');

$pdf->SetXY(282, 312);
$pdf->MultiCell(185, 4, $Search['Comments'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 10, 240, 260, 0, 'PNG');
unlink($tempFile);

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>