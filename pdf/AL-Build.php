<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

// Obtener datos de la base de datos
$Search = find_by_id('atterberg_limit', $_GET['id']);

// Definir la clase PDF personalizada
class PDF extends Fpdi {}


$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('L', array(420, 330));

// Importar plantilla
$pdf->setSourceFile('template/PV-F-01718 Laboratory Atterberg Limit Test_Rev2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Configurar fuente
$pdf->SetFont('Arial', '', 12);

// Definir posiciones y valores en el PDF
$fields = [
    [58, 36, $Search['Project_Name']],
    [58, 54, "PVDJ Soil Lab"],
    [58, 60, $Search['Technician']],
    [58, 67, $Search['Sample_By']],
    [58, 83, $Search['Structure']],
    [58, 88, $Search['Area']],
    [58, 93, $Search['Source']],
    [58, 98, $Search['Material_Type']],
    [175, 36, $Search['Project_Number']],
    [175, 54, $Search['Standard']],
    [175, 60, $Search['Test_Start_Date']],
    [175, 67, $Search['Registed_Date']],
    [175, 83, $Search['Sample_ID']],
    [175, 88, $Search['Sample_Number']],
    [175, 93, $Search['Sample_Date']],
    [175, 98, $Search['Elev']],
    [175, 104, $Search['Nat_Mc']],
    [285, 36, $Search['Client']],
    [285, 54, ""],
    [285, 60, $Search['Preparation_Method']],
    [285, 67, $Search['Split_Method']],
    [285, 83, $Search['Depth_From']],
    [285, 89, $Search['Depth_To']],
    [285, 95, $Search['North']],
    [285, 100, $Search['East']],
];

foreach ($fields as $field) {
    list($x, $y, $value) = $field;
    $pdf->SetXY($x, $y);
    $pdf->Cell(30, 1, $value, 0, 1, 'C');
}


//Test Information Liquid Limit
$pdf->SetFont('Arial', '', 12);

$pdf->SetXY(68, 128);
$pdf->Cell(35, 4, $Search['LL_Blows_1'], 0, 1, 'C');
$pdf->SetXY(104, 128);
$pdf->Cell(28, 4, $Search['LL_Blows_2'], 0, 1, 'C');
$pdf->SetXY(139, 128);
$pdf->Cell(24, 4, $Search['LL_Blows_3'], 0, 1, 'C');
$pdf->SetXY(68, 136);
$pdf->Cell(35, 4, $Search['LL_Container_1'], 0, 1, 'C');
$pdf->SetXY(104, 136);
$pdf->Cell(28, 4, $Search['LL_Container_2'], 0, 1, 'C');
$pdf->SetXY(139, 136);
$pdf->Cell(24, 4, $Search['LL_Container_3'], 0, 1, 'C');
$pdf->SetXY(68, 145);
$pdf->Cell(35, 4, $Search['LL_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(104, 145);
$pdf->Cell(28, 4, $Search['LL_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(139, 145);
$pdf->Cell(24, 4, $Search['LL_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(68, 153);
$pdf->Cell(35, 4, $Search['LL_Dry_Soil_Tare_1'], 0, 1, 'C');
$pdf->SetXY(104, 153);
$pdf->Cell(28, 4, $Search['LL_Dry_Soil_Tare_2'], 0, 1, 'C');
$pdf->SetXY(139, 153);
$pdf->Cell(24, 4, $Search['LL_Dry_Soil_Tare_3'], 0, 1, 'C');
$pdf->SetXY(68, 161);
$pdf->Cell(35, 4, $Search['LL_Water_1'], 0, 1, 'C');
$pdf->SetXY(104, 161);
$pdf->Cell(28, 4, $Search['LL_Water_2'], 0, 1, 'C');
$pdf->SetXY(139, 161);
$pdf->Cell(24, 4, $Search['LL_Water_3'], 0, 1, 'C');
$pdf->SetXY(68, 170);
$pdf->Cell(35, 4, $Search['LL_Tare_1'], 0, 1, 'C');
$pdf->SetXY(104, 170);
$pdf->Cell(28, 4, $Search['LL_Tare_2'], 0, 1, 'C');
$pdf->SetXY(139, 170);
$pdf->Cell(24, 4, $Search['LL_Tare_3'], 0, 1, 'C');
$pdf->SetXY(68, 178);
$pdf->Cell(35, 4, $Search['LL_Wt_Dry_Soil_1'], 0, 1, 'C');
$pdf->SetXY(104, 178);
$pdf->Cell(28, 4, $Search['LL_Wt_Dry_Soil_2'], 0, 1, 'C');
$pdf->SetXY(139, 178);
$pdf->Cell(24, 4, $Search['LL_Wt_Dry_Soil_3'], 0, 1, 'C');
$pdf->SetXY(68, 187);
$pdf->Cell(35, 4, $Search['LL_MC_Porce_1'], 0, 1, 'C');
$pdf->SetXY(104, 187);
$pdf->Cell(28, 4, $Search['LL_MC_Porce_2'], 0, 1, 'C');
$pdf->SetXY(139, 187);
$pdf->Cell(24, 4, $Search['LL_MC_Porce_3'], 0, 1, 'C');

// Test Information Plastic Limit
$pdf->SetXY(68, 215);
$pdf->Cell(35, 4, $Search['PL_Container_1'], 0, 1, 'C');
$pdf->SetXY(104, 215);
$pdf->Cell(28, 4, $Search['PL_Container_2'], 0, 1, 'C');
$pdf->SetXY(139, 215);
$pdf->Cell(24, 4, $Search['PL_Container_3'], 0, 1, 'C');
$pdf->SetXY(68, 222);
$pdf->Cell(35, 4, $Search['PL_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(104, 222);
$pdf->Cell(28, 4, $Search['PL_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(139, 222);
$pdf->Cell(24, 4, $Search['PL_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(68, 229);
$pdf->Cell(35, 4, $Search['PL_Dry_Soil_Tare_1'], 0, 1, 'C');
$pdf->SetXY(104, 229);
$pdf->Cell(28, 4, $Search['PL_Dry_Soil_Tare_2'], 0, 1, 'C');
$pdf->SetXY(139, 229);
$pdf->Cell(24, 4, $Search['PL_Dry_Soil_Tare_3'], 0, 1, 'C');
$pdf->SetXY(68, 236);
$pdf->Cell(35, 4, $Search['PL_Water_1'], 0, 1, 'C');
$pdf->SetXY(104, 236);
$pdf->Cell(28, 4, $Search['PL_Water_2'], 0, 1, 'C');
$pdf->SetXY(139, 236);
$pdf->Cell(24, 4, $Search['PL_Water_3'], 0, 1, 'C');
$pdf->SetXY(68, 243);
$pdf->Cell(35, 4, $Search['PL_Tare_1'], 0, 1, 'C');
$pdf->SetXY(104, 243);
$pdf->Cell(28, 4, $Search['PL_Tare_2'], 0, 1, 'C');
$pdf->SetXY(139, 243);
$pdf->Cell(24, 4, $Search['PL_Tare_3'], 0, 1, 'C');
$pdf->SetXY(68, 250);
$pdf->Cell(35, 4, $Search['PL_Wt_Dry_Soil_1'], 0, 1, 'C');
$pdf->SetXY(104, 250);
$pdf->Cell(28, 4, $Search['PL_Wt_Dry_Soil_2'], 0, 1, 'C');
$pdf->SetXY(139, 250);
$pdf->Cell(24, 4, $Search['PL_Wt_Dry_Soil_3'], 0, 1, 'C');
$pdf->SetXY(68, 257);
$pdf->Cell(35, 4, $Search['PL_MC_Porce_1'], 0, 1, 'C');
$pdf->SetXY(100, 257);;
$pdf->Cell(35, 4, $Search['PL_MC_Porce_2'], 0, 1, 'C');
$pdf->SetXY(134, 257);;
$pdf->Cell(35, 4, $Search['PL_MC_Porce_3'], 0, 1, 'C');
$pdf->SetXY(100, 264);
$pdf->Cell(35, 4, $Search['PL_Avg_Mc_Porce'], 0, 1, 'C');

// Sumarry Atterberg Limits Parameters
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(349, 120);
$pdf->Cell(24, 4, $Search['Liquid_Limit_Porce'], 0, 1, 'C');
$pdf->SetXY(349, 128);
$pdf->Cell(24, 4, $Search['Plastic_Limit_Porce'], 0, 1, 'C');
$pdf->SetXY(349, 136);
$pdf->Cell(24, 4, $Search['Plasticity_Index_Porce'], 0, 1, 'C');
$pdf->SetXY(349, 145);
$pdf->Cell(24, 4, $Search['Liquidity_Index_Porce'], 0, 1, 'C');
$pdf->SetXY(349, 153);
$pdf->Cell(24, 4, ($Search['Plasticity_Index_Porce'] >= 14.5) ? "Passed" : "Failed", 0, 1, 'C');
$pdf->SetXY(139, 278.5);
$pdf->Cell(24, 4, $Search['Classification'], 0, 1, 'C');

// Comparison information
$pdf->SetXY(349, 179);
$pdf->Cell(24, 4, $Search['Liquid_Limit_Porce'], 0, 1, 'C');
$pdf->SetXY(349, 187);
$pdf->Cell(24, 4, $Search['Plastic_Limit_Porce'], 0, 1, 'C');
$pdf->SetXY(349, 194.5);
$pdf->Cell(24, 4, $Search['Plasticity_Index_Porce'], 0, 1, 'C');
$pdf->SetXY(349, 201);
$pdf->Cell(24, 4, $Search['Liquidity_Index_Porce'], 0, 1, 'C');

// Comments for the test
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(313, 220);
$pdf->MultiCell(86, 4, $Search['Comments'], 0, 'L');

// Agregar imágenes al PDF
function addImage($pdf, $base64, $x, $y, $w)
{
    $imageData = base64_decode($base64);
    $tempFile = tempnam(sys_get_temp_dir(), 'image');
    file_put_contents($tempFile, $imageData);
    $pdf->Image($tempFile, $x, $y, $w, 0, 'PNG');
    unlink($tempFile);
}

addImage($pdf, $Search['Liquid_Limit_Plot'], 190, 110, 95);
addImage($pdf, $Search['Plasticity_Chart'], 190, 200, 95);

// Salida del archivo PDF
$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
