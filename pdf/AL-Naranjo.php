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
$pdf->AddPage('L', array(370, 290));

// Importar plantilla
$pdf->setSourceFile('template/PV-F-80769 Laboratory Atteberg Limits.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Configurar fuente
$pdf->SetFont('Arial', 'B', 10);

// Definir posiciones y valores en el PDF
$fields = [
    [58, 36, "PVDJ Soil Lab"],
    [58, 43.5, $Search['Technician']],
    [58, 50, $Search['Sample_By']],
    [165, 36, $Search['Standard']],
    [165, 43.5, $Search['Test_Start_Date']],
    [165, 50, $Search['Registed_Date']],
    [267, 36, ""],
    [267, 42, $Search['Split_Method']],
    [267, 48, $Search['Preparation_Method']],
    [58, 67, $Search['Structure']],
    [58, 72, $Search['Area']],
    [58, 77, $Search['Source']],
    [58, 82, $Search['Material_Type']],
    [165, 67, $Search['Sample_ID']],
    [165, 72, $Search['Sample_Number']],
    [165, 77, $Search['Sample_Date']],
    [165, 82, $Search['Elev']],
    [165, 88, $Search['Nat_Mc']],
    [267, 67, $Search['Depth_From']],
    [267, 72, $Search['Depth_To']],
    [267, 77, $Search['North']],
    [267, 82, $Search['East']],
];

foreach ($fields as $field) {
    list($x, $y, $value) = $field;
    $pdf->SetXY($x, $y);
    $pdf->Cell(30, 1, $value, 0, 1, 'C');
}


//Test Information Liquid Limit
$pdf->SetFont('Arial', '', 12);

$pdf->SetXY(66, 112);
$pdf->Cell(35, 4, $Search['LL_Blows_1'], 0, 1, 'C');
$pdf->SetXY(100, 112);
$pdf->Cell(28, 4, $Search['LL_Blows_2'], 0, 1, 'C');
$pdf->SetXY(136, 112);
$pdf->Cell(24, 4, $Search['LL_Blows_3'], 0, 1, 'C');
$pdf->SetXY(66, 120);
$pdf->Cell(35, 4, $Search['LL_Container_1'], 0, 1, 'C');
$pdf->SetXY(100, 120);
$pdf->Cell(28, 4, $Search['LL_Container_2'], 0, 1, 'C');
$pdf->SetXY(136, 120);
$pdf->Cell(24, 4, $Search['LL_Container_3'], 0, 1, 'C');
$pdf->SetXY(66, 128);
$pdf->Cell(35, 4, $Search['LL_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(100, 128);
$pdf->Cell(28, 4, $Search['LL_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(136, 128);
$pdf->Cell(24, 4, $Search['LL_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(66, 136);
$pdf->Cell(35, 4, $Search['LL_Dry_Soil_Tare_1'], 0, 1, 'C');
$pdf->SetXY(100, 136);
$pdf->Cell(28, 4, $Search['LL_Dry_Soil_Tare_2'], 0, 1, 'C');
$pdf->SetXY(136, 136);
$pdf->Cell(24, 4, $Search['LL_Dry_Soil_Tare_3'], 0, 1, 'C');
$pdf->SetXY(66, 144);
$pdf->Cell(35, 4, $Search['LL_Water_1'], 0, 1, 'C');
$pdf->SetXY(100, 144);
$pdf->Cell(28, 4, $Search['LL_Water_2'], 0, 1, 'C');
$pdf->SetXY(136, 144);
$pdf->Cell(24, 4, $Search['LL_Water_3'], 0, 1, 'C');
$pdf->SetXY(66, 153);
$pdf->Cell(35, 4, $Search['LL_Tare_1'], 0, 1, 'C');
$pdf->SetXY(100, 153);
$pdf->Cell(28, 4, $Search['LL_Tare_2'], 0, 1, 'C');
$pdf->SetXY(136, 153);
$pdf->Cell(24, 4, $Search['LL_Tare_3'], 0, 1, 'C');
$pdf->SetXY(66, 161);
$pdf->Cell(35, 4, $Search['LL_Wt_Dry_Soil_1'], 0, 1, 'C');
$pdf->SetXY(100, 161);
$pdf->Cell(28, 4, $Search['LL_Wt_Dry_Soil_2'], 0, 1, 'C');
$pdf->SetXY(136, 161);
$pdf->Cell(24, 4, $Search['LL_Wt_Dry_Soil_3'], 0, 1, 'C');
$pdf->SetXY(66, 169);
$pdf->Cell(35, 4, $Search['LL_MC_Porce_1'], 0, 1, 'C');
$pdf->SetXY(100, 169);
$pdf->Cell(28, 4, $Search['LL_MC_Porce_2'], 0, 1, 'C');
$pdf->SetXY(136, 169);
$pdf->Cell(24, 4, $Search['LL_MC_Porce_3'], 0, 1, 'C');

// Test Information Plastic Limit
$pdf->SetXY(66, 194);
$pdf->Cell(35, 4, $Search['PL_Container_1'], 0, 1, 'C');
$pdf->SetXY(100, 194);
$pdf->Cell(28, 4, $Search['PL_Container_2'], 0, 1, 'C');
$pdf->SetXY(136, 194);
$pdf->Cell(24, 4, $Search['PL_Container_3'], 0, 1, 'C');
$pdf->SetXY(66, 201);
$pdf->Cell(35, 4, $Search['PL_Wet_Soil_1'], 0, 1, 'C');
$pdf->SetXY(100, 201);
$pdf->Cell(28, 4, $Search['PL_Wet_Soil_2'], 0, 1, 'C');
$pdf->SetXY(136, 201);
$pdf->Cell(24, 4, $Search['PL_Wet_Soil_3'], 0, 1, 'C');
$pdf->SetXY(66, 208);
$pdf->Cell(35, 4, $Search['PL_Dry_Soil_Tare_1'], 0, 1, 'C');
$pdf->SetXY(100, 208);
$pdf->Cell(28, 4, $Search['PL_Dry_Soil_Tare_2'], 0, 1, 'C');
$pdf->SetXY(136, 208);
$pdf->Cell(24, 4, $Search['PL_Dry_Soil_Tare_3'], 0, 1, 'C');
$pdf->SetXY(66, 214);
$pdf->Cell(35, 4, $Search['PL_Water_1'], 0, 1, 'C');
$pdf->SetXY(100, 214);
$pdf->Cell(28, 4, $Search['PL_Water_2'], 0, 1, 'C');
$pdf->SetXY(136, 214);
$pdf->Cell(24, 4, $Search['PL_Water_3'], 0, 1, 'C');
$pdf->SetXY(66, 221);
$pdf->Cell(35, 4, $Search['PL_Tare_1'], 0, 1, 'C');
$pdf->SetXY(100, 221);
$pdf->Cell(28, 4, $Search['PL_Tare_2'], 0, 1, 'C');
$pdf->SetXY(136, 221);
$pdf->Cell(24, 4, $Search['PL_Tare_3'], 0, 1, 'C');
$pdf->SetXY(66, 228);
$pdf->Cell(35, 4, $Search['PL_Wt_Dry_Soil_1'], 0, 1, 'C');
$pdf->SetXY(100, 228);
$pdf->Cell(28, 4, $Search['PL_Wt_Dry_Soil_2'], 0, 1, 'C');
$pdf->SetXY(136, 228);
$pdf->Cell(24, 4, $Search['PL_Wt_Dry_Soil_3'], 0, 1, 'C');
$pdf->SetXY(66, 235);
$pdf->Cell(35, 4, $Search['PL_MC_Porce_1'], 0, 1, 'C');
$pdf->SetXY(97, 235);;
$pdf->Cell(35, 4, $Search['PL_MC_Porce_2'], 0, 1, 'C');
$pdf->SetXY(131, 235);;
$pdf->Cell(35, 4, $Search['PL_MC_Porce_3'], 0, 1, 'C');
$pdf->SetXY(97, 242);
$pdf->Cell(35, 4, $Search['PL_Avg_Mc_Porce'], 0, 1, 'C');

// Sumarry Atterberg Limits Parameters
$pdf->SetXY(328, 104);
$pdf->Cell(24, 4, $Search['Liquid_Limit_Porce'], 0, 1, 'C');
$pdf->SetXY(328, 112);
$pdf->Cell(24, 4, $Search['Plastic_Limit_Porce'], 0, 1, 'C');
$pdf->SetXY(328, 120);
$pdf->Cell(24, 4, $Search['Plasticity_Index_Porce'], 0, 1, 'C');
$pdf->SetXY(328, 128);
$pdf->Cell(24, 4, $Search['Liquidity_Index_Porce'], 0, 1, 'C');
$pdf->SetXY(328, 151);
$pdf->Cell(24, 4, $Search['Classification'], 0, 1, 'C');

// Comments for the test
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(293, 182);
$pdf->MultiCell(59, 4, $Search['Comments'], 0, 'L');

// Agregar imágenes al PDF
function addImage($pdf, $base64, $x, $y, $w) {
    $imageData = base64_decode($base64);
    $tempFile = tempnam(sys_get_temp_dir(), 'image');
    file_put_contents($tempFile, $imageData);
    $pdf->Image($tempFile, $x, $y, $w, 0, 'PNG');
    unlink($tempFile);
}

addImage($pdf, $Search['Liquid_Limit_Plot'], 182, 100, 90);
addImage($pdf, $Search['Plasticity_Chart'], 182, 175, 90);

// Salida del archivo PDF
$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
?>
