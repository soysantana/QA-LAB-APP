<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

// Leer JSON recibido
$input = json_decode(file_get_contents('php://input'), true);
$Chart = $input['StandardProctor'] ?? null;

$Search = find_by_id('standard_proctor', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(390, 310));

$pdf->setSourceFile('template/PV-F-83538_Laboratory Standard Proctor Test_Rev. 4.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(50, 35);
$pdf->Cell(30, 5, 'PVDJ Soil Lab', 0, 1, 'C');
$pdf->SetXY(50, 41);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(50, 47);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(50, 61);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(50, 66);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(50, 71);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(50, 77);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');
$pdf->SetXY(50, 82);
$pdf->Cell(30, 6, $Search['Spec_Gravity'], 0, 1, 'C');

$pdf->SetXY(148, 34);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(148, 41);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(148, 47);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(148, 61);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(148, 66);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(148, 71);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(148, 77);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');
$pdf->SetXY(148, 82);
$pdf->Cell(30, 6, $Search['Nat_Mc'], 0, 1, 'C');

$pdf->SetXY(220, 34);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(220, 40);
$pdf->Cell(30, 6, $Search['Preparation_Method'], 0, 1, 'C');
$pdf->SetXY(220, 46);
$pdf->Cell(30, 6, $Search['Split_Method'], 0, 1, 'C');
$pdf->SetXY(220, 61);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(220, 66);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(220, 71);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(220, 77);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

//Testing Information
$pdf->SetXY(81, 102);
$pdf->Cell(25, 6, $Search['WetSoilMod1'], 0, 1, 'C');
$pdf->SetXY(106, 102);
$pdf->Cell(30, 6, $Search['WetSoilMod2'], 0, 1, 'C');
$pdf->SetXY(136, 102);
$pdf->Cell(27, 6, $Search['WetSoilMod3'], 0, 1, 'C');
$pdf->SetXY(163, 102);
$pdf->Cell(26, 6, $Search['WetSoilMod4'], 0, 1, 'C');
$pdf->SetXY(189, 102);
$pdf->Cell(24, 6, $Search['WetSoilMod5'], 0, 1, 'C');
$pdf->SetXY(213, 102);
$pdf->Cell(24, 6, $Search['WetSoilMod6'], 0, 1, 'C');

$pdf->SetXY(81, 108.5);
$pdf->Cell(25, 6, $Search['WtMold1'], 0, 1, 'C');
$pdf->SetXY(106, 108.5);
$pdf->Cell(30, 6, $Search['WtMold2'], 0, 1, 'C');
$pdf->SetXY(136, 108.5);
$pdf->Cell(27, 6, $Search['WtMold3'], 0, 1, 'C');
$pdf->SetXY(163, 108.5);
$pdf->Cell(26, 6, $Search['WtMold4'], 0, 1, 'C');
$pdf->SetXY(189, 108.5);
$pdf->Cell(24, 6, $Search['WtMold5'], 0, 1, 'C');
$pdf->SetXY(213, 108.5);
$pdf->Cell(24, 6, $Search['WtMold6'], 0, 1, 'C');

$pdf->SetXY(81, 115);
$pdf->Cell(25, 6, $Search['WtSoil1'], 0, 1, 'C');
$pdf->SetXY(106, 115);
$pdf->Cell(30, 6, $Search['WtSoil2'], 0, 1, 'C');
$pdf->SetXY(136, 115);
$pdf->Cell(27, 6, $Search['WtSoil3'], 0, 1, 'C');
$pdf->SetXY(163, 115);
$pdf->Cell(26, 6, $Search['WtSoil4'], 0, 1, 'C');
$pdf->SetXY(189, 115);
$pdf->Cell(24, 6, $Search['WtSoil5'], 0, 1, 'C');
$pdf->SetXY(213, 115);
$pdf->Cell(24, 6, $Search['WtSoil6'], 0, 1, 'C');

$pdf->SetXY(81, 121);
$pdf->Cell(25, 6, $Search['VolMold1'], 0, 1, 'C');
$pdf->SetXY(106, 121);
$pdf->Cell(30, 6, $Search['VolMold2'], 0, 1, 'C');
$pdf->SetXY(136, 121);
$pdf->Cell(27, 6, $Search['VolMold3'], 0, 1, 'C');
$pdf->SetXY(163, 121);
$pdf->Cell(26, 6, $Search['VolMold4'], 0, 1, 'C');
$pdf->SetXY(189, 121);
$pdf->Cell(24, 6, $Search['VolMold5'], 0, 1, 'C');
$pdf->SetXY(213, 121);
$pdf->Cell(24, 6, $Search['VolMold6'], 0, 1, 'C');

$pdf->SetXY(81, 127.5);
$pdf->Cell(25, 6, $Search['WetDensity1'], 0, 1, 'C');
$pdf->SetXY(106, 127.5);
$pdf->Cell(30, 6, $Search['WetDensity2'], 0, 1, 'C');
$pdf->SetXY(136, 127.5);
$pdf->Cell(27, 6, $Search['WetDensity3'], 0, 1, 'C');
$pdf->SetXY(163, 127.5);
$pdf->Cell(26, 6, $Search['WetDensity4'], 0, 1, 'C');
$pdf->SetXY(189, 127.5);
$pdf->Cell(24, 6, $Search['WetDensity5'], 0, 1, 'C');
$pdf->SetXY(213, 127.5);
$pdf->Cell(24, 6, $Search['WetDensity6'], 0, 1, 'C');

$pdf->SetXY(81, 133.5);
$pdf->Cell(25, 6, $Search['DryDensity1'], 0, 1, 'C');
$pdf->SetXY(106, 133.5);
$pdf->Cell(30, 6, $Search['DryDensity2'], 0, 1, 'C');
$pdf->SetXY(136, 133.5);
$pdf->Cell(27, 6, $Search['DryDensity3'], 0, 1, 'C');
$pdf->SetXY(163, 133.5);
$pdf->Cell(26, 6, $Search['DryDensity4'], 0, 1, 'C');
$pdf->SetXY(189, 133.5);
$pdf->Cell(24, 6, $Search['DryDensity5'], 0, 1, 'C');
$pdf->SetXY(213, 133.5);
$pdf->Cell(24, 6, $Search['DryDensity6'], 0, 1, 'C');

$pdf->SetXY(81, 140);
$pdf->Cell(25, 6, $Search['DensyCorrected1'], 0, 1, 'C');
$pdf->SetXY(106, 140);
$pdf->Cell(30, 6, $Search['DensyCorrected2'], 0, 1, 'C');
$pdf->SetXY(136, 140);
$pdf->Cell(27, 6, $Search['DensyCorrected3'], 0, 1, 'C');
$pdf->SetXY(163, 140);
$pdf->Cell(26, 6, $Search['DensyCorrected4'], 0, 1, 'C');
$pdf->SetXY(189, 140);
$pdf->Cell(24, 6, $Search['DensyCorrected5'], 0, 1, 'C');
$pdf->SetXY(213, 140);
$pdf->Cell(24, 6, $Search['DensyCorrected6'], 0, 1, 'C');

//Information Table
$pdf->SetXY(81, 155);
$pdf->Cell(25, 6, $Search['Container1'], 0, 1, 'C');
$pdf->SetXY(106, 155);
$pdf->Cell(30, 6, $Search['Container2'], 0, 1, 'C');
$pdf->SetXY(136, 155);
$pdf->Cell(27, 6, $Search['Container3'], 0, 1, 'C');
$pdf->SetXY(163, 155);
$pdf->Cell(26, 6, $Search['Container4'], 0, 1, 'C');
$pdf->SetXY(189, 155);
$pdf->Cell(24, 6, $Search['Container5'], 0, 1, 'C');
$pdf->SetXY(213, 155);
$pdf->Cell(24, 6, $Search['Container6'], 0, 1, 'C');

$pdf->SetXY(81, 160);
$pdf->Cell(25, 6, $Search['WetSoilTare1'], 0, 1, 'C');
$pdf->SetXY(106, 160);
$pdf->Cell(30, 6, $Search['WetSoilTare2'], 0, 1, 'C');
$pdf->SetXY(136, 160);
$pdf->Cell(27, 6, $Search['WetSoilTare3'], 0, 1, 'C');
$pdf->SetXY(163, 160);
$pdf->Cell(26, 6, $Search['WetSoilTare4'], 0, 1, 'C');
$pdf->SetXY(189, 160);
$pdf->Cell(24, 6, $Search['WetSoilTare5'], 0, 1, 'C');
$pdf->SetXY(213, 160);
$pdf->Cell(24, 6, $Search['WetSoilTare6'], 0, 1, 'C');

$pdf->SetXY(81, 165);
$pdf->Cell(25, 6, $Search['WetDryTare1'], 0, 1, 'C');
$pdf->SetXY(106, 165);
$pdf->Cell(30, 6, $Search['WetDryTare2'], 0, 1, 'C');
$pdf->SetXY(136, 165);
$pdf->Cell(27, 6, $Search['WetDryTare3'], 0, 1, 'C');
$pdf->SetXY(163, 165);
$pdf->Cell(26, 6, $Search['WetDryTare4'], 0, 1, 'C');
$pdf->SetXY(189, 165);
$pdf->Cell(24, 6, $Search['WetDryTare5'], 0, 1, 'C');
$pdf->SetXY(213, 165);
$pdf->Cell(24, 6, $Search['WetDryTare6'], 0, 1, 'C');

$pdf->SetXY(81, 170);
$pdf->Cell(25, 6, $Search['WtWater1'], 0, 1, 'C');
$pdf->SetXY(106, 170);
$pdf->Cell(30, 6, $Search['WtWater2'], 0, 1, 'C');
$pdf->SetXY(136, 170);
$pdf->Cell(27, 6, $Search['WtWater3'], 0, 1, 'C');
$pdf->SetXY(163, 170);
$pdf->Cell(26, 6, $Search['WtWater4'], 0, 1, 'C');
$pdf->SetXY(189, 170);
$pdf->Cell(24, 6, $Search['WtWater5'], 0, 1, 'C');
$pdf->SetXY(213, 170);
$pdf->Cell(24, 6, $Search['WtWater6'], 0, 1, 'C');

$pdf->SetXY(81, 175);
$pdf->Cell(25, 6, $Search['Tare1'], 0, 1, 'C');
$pdf->SetXY(106, 175);
$pdf->Cell(30, 6, $Search['Tare2'], 0, 1, 'C');
$pdf->SetXY(136, 175);
$pdf->Cell(27, 6, $Search['Tare3'], 0, 1, 'C');
$pdf->SetXY(163, 175);
$pdf->Cell(26, 6, $Search['Tare4'], 0, 1, 'C');
$pdf->SetXY(189, 175);
$pdf->Cell(24, 6, $Search['Tare5'], 0, 1, 'C');
$pdf->SetXY(213, 175);
$pdf->Cell(24, 6, $Search['Tare6'], 0, 1, 'C');

$pdf->SetXY(81, 180);
$pdf->Cell(25, 6, $Search['DrySoil1'], 0, 1, 'C');
$pdf->SetXY(106, 180);
$pdf->Cell(30, 6, $Search['DrySoil2'], 0, 1, 'C');
$pdf->SetXY(136, 180);
$pdf->Cell(27, 6, $Search['DrySoil3'], 0, 1, 'C');
$pdf->SetXY(163, 180);
$pdf->Cell(26, 6, $Search['DrySoil4'], 0, 1, 'C');
$pdf->SetXY(189, 180);
$pdf->Cell(24, 6, $Search['DrySoil5'], 0, 1, 'C');
$pdf->SetXY(213, 180);
$pdf->Cell(24, 6, $Search['DrySoil6'], 0, 1, 'C');

$pdf->SetXY(81, 185);
$pdf->Cell(25, 6, $Search['MoisturePorce1'], 0, 1, 'C');
$pdf->SetXY(106, 185);
$pdf->Cell(30, 6, $Search['MoisturePorce2'], 0, 1, 'C');
$pdf->SetXY(136, 185);
$pdf->Cell(27, 6, $Search['MoisturePorce3'], 0, 1, 'C');
$pdf->SetXY(163, 185);
$pdf->Cell(26, 6, $Search['MoisturePorce4'], 0, 1, 'C');
$pdf->SetXY(189, 185);
$pdf->Cell(24, 6, $Search['MoisturePorce5'], 0, 1, 'C');
$pdf->SetXY(213, 185);
$pdf->Cell(24, 6, $Search['MoisturePorce6'], 0, 1, 'C');

$pdf->SetXY(81, 190);
$pdf->Cell(25, 6, $Search['MCcorrected1'], 0, 1, 'C');
$pdf->SetXY(106, 190);
$pdf->Cell(30, 6, $Search['MCcorrected2'], 0, 1, 'C');
$pdf->SetXY(136, 190);
$pdf->Cell(27, 6, $Search['MCcorrected3'], 0, 1, 'C');
$pdf->SetXY(163, 190);
$pdf->Cell(26, 6, $Search['MCcorrected4'], 0, 1, 'C');
$pdf->SetXY(189, 190);
$pdf->Cell(24, 6, $Search['MCcorrected5'], 0, 1, 'C');
$pdf->SetXY(213, 190);
$pdf->Cell(24, 6, $Search['MCcorrected6'], 0, 1, 'C');

$pdf->SetXY(81, 196);
$pdf->Cell(25, 6, $Search['Max_Dry_Density_kgm3'], 0, 1, 'C');
$pdf->SetXY(163, 196);
$pdf->Cell(26, 6, $Search['Optimun_MC_Porce'], 0, 1, 'C');

$pdf->SetXY(106, 214);
$pdf->Cell(30, 9, $Search['Corrected_Dry_Unit_Weigt'], 0, 1, 'C');
$pdf->SetXY(106, 223);
$pdf->Cell(30, 8, $Search['Corrected_Water_Content_Finer'], 0, 1, 'C');

$pdf->SetXY(106, 230);
$pdf->Cell(30, 6, $Search['YDF_Porce'], 0, 1, 'C');

$pdf->SetXY(106, 236);
$pdf->Cell(30, 6, $Search['PF_Porce'], 0, 1, 'C');

$pdf->SetXY(106, 242);
$pdf->Cell(30, 5, $Search['YDT_Porce'], 0, 1, 'C');

$pdf->SetXY(53, 231);
$pdf->Cell(28, 5, $Search['Wc_Porce'], 0, 1, 'C');

$pdf->SetXY(53, 236);
$pdf->Cell(28, 5, $Search['PC_Porce'], 0, 1, 'C');

$pdf->SetXY(53, 242);
$pdf->Cell(28, 5, $Search['GM_Porce'], 0, 1, 'C');

$pdf->SetXY(53, 247);
$pdf->Cell(28, 5, $Search['Yw_KnM3'], 0, 1, 'C');

$pdf->SetXY(163, 211);
$pdf->MultiCell(104, 4, $Search['Comments'], 0, 'L');

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

insertarImagenBase64($pdf, $Chart, 40, 260, 230, 0); // ajusta X, Y, ancho, alto

$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . 'SP' . '.pdf', 'I');
