<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$Search = find_by_id('pinhole_test', $_GET['id']);

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(410, 360));

$pdf->setSourceFile('template/PV-F-01743 Laboratory Identification and Classification of Dispersive Clay Soils by the Pinhole Test_Rev 4.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont('Arial', 'B', 10);

// Information for the test
$pdf->SetXY(60, 49);
$pdf->Cell(30, 5, $Search['Technician'], 0, 1, 'C');
$pdf->SetXY(60, 54);
$pdf->Cell(30, 5, $Search['Sample_By'], 0, 1, 'C');
$pdf->SetXY(60, 69);
$pdf->Cell(30, 6, $Search['Structure'], 0, 1, 'C');
$pdf->SetXY(60, 75);
$pdf->Cell(30, 6, $Search['Area'], 0, 1, 'C');
$pdf->SetXY(60, 80);
$pdf->Cell(30, 6, $Search['Source'], 0, 1, 'C');
$pdf->SetXY(60, 85);
$pdf->Cell(30, 6, $Search['Material_Type'], 0, 1, 'C');

$pdf->SetXY(167, 43);
$pdf->Cell(30, 6, $Search['Standard'], 0, 1, 'C');
$pdf->SetXY(167, 49);
$pdf->Cell(30, 6, $Search['Test_Start_Date'], 0, 1, 'C');
$pdf->SetXY(167, 55);
$pdf->Cell(30, 6, $Search['Registed_Date'], 0, 1, 'C');
$pdf->SetXY(167, 69);
$pdf->Cell(30, 6, $Search['Sample_ID'], 0, 1, 'C');
$pdf->SetXY(167, 75);
$pdf->Cell(30, 6, $Search['Sample_Number'], 0, 1, 'C');
$pdf->SetXY(167, 80);
$pdf->Cell(30, 6, $Search['Sample_Date'], 0, 1, 'C');
$pdf->SetXY(167, 85);
$pdf->Cell(30, 6, $Search['Elev'], 0, 1, 'C');

$pdf->SetXY(250, 42);
$pdf->Cell(30, 6, $Search['Methods'], 0, 1, 'C');
$pdf->SetXY(250, 69);
$pdf->Cell(30, 6, $Search['Depth_From'], 0, 1, 'C');
$pdf->SetXY(250, 74);
$pdf->Cell(30, 6, $Search['Depth_To'], 0, 1, 'C');
$pdf->SetXY(250, 80);
$pdf->Cell(30, 6, $Search['North'], 0, 1, 'C');
$pdf->SetXY(250, 85);
$pdf->Cell(30, 6, $Search['East'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// Testing Information
$pdf->SetXY(104, 101);
$pdf->Cell(28, 6, $Search['MC_Before_Test'], 0, 1, 'C');
$pdf->SetXY(104, 108);
$pdf->Cell(28, 6, $Search['Specific_Gravity'], 0, 1, 'C');
$pdf->SetXY(104, 115);
$pdf->Cell(28, 6, $Search['Max_Dry_Density'], 0, 1, 'C');
$pdf->SetXY(104, 121);
$pdf->Cell(28, 6, $Search['Optimum_MC'], 0, 1, 'C');
$pdf->SetXY(104, 128);
$pdf->Cell(28, 6, $Search['Wet_Soil_Mold'], 0, 1, 'C');
$pdf->SetXY(104, 135);
$pdf->Cell(28, 6, $Search['Wet_Mold'], 0, 1, 'C');
$pdf->SetXY(104, 142);
$pdf->Cell(28, 6, $Search['Wet_Soil'], 0, 1, 'C');
$pdf->SetXY(104, 148);
$pdf->Cell(28, 6, $Search['Vol_Specimen'], 0, 1, 'C');
$pdf->SetXY(104, 155);
$pdf->Cell(28, 6, $Search['Wet_Density'], 0, 1, 'C');
$pdf->SetXY(104, 162);
$pdf->Cell(28, 6, $Search['Dry_Density'], 0, 1, 'C');
$pdf->SetXY(104, 169);
$pdf->Cell(28, 6, $Search['Porce_Compaction'], 0, 1, 'C');
$pdf->SetXY(104, 175);
$pdf->Cell(28, 6, $Search['MC_After_Test'], 0, 1, 'C');
$pdf->SetXY(104, 181);
$pdf->Cell(28, 6, $Search['Wire_Punch_Diameter'], 0, 1, 'C');


// TESTING FLOW DISPERSION
$pdf->SetXY(67, 217);
$pdf->Cell(37, 5, $Search['ML_1'], 0, 1, 'C');
$pdf->SetXY(67, 222);
$pdf->Cell(37, 5, $Search['ML_2'], 0, 1, 'C');
$pdf->SetXY(67, 227);
$pdf->Cell(37, 5, $Search['ML_3'], 0, 1, 'C');
$pdf->SetXY(67, 232);
$pdf->Cell(37, 5, $Search['ML_4'], 0, 1, 'C');
$pdf->SetXY(67, 237);
$pdf->Cell(37, 5, $Search['ML_5'], 0, 1, 'C');
$pdf->SetXY(67, 242);
$pdf->Cell(37, 5, $Search['ML_6'], 0, 1, 'C');
$pdf->SetXY(67, 247);
$pdf->Cell(37, 5, $Search['ML_7'], 0, 1, 'C');
$pdf->SetXY(67, 252);
$pdf->Cell(37, 5, $Search['ML_8'], 0, 1, 'C');
$pdf->SetXY(67, 257);
$pdf->Cell(37, 5, $Search['ML_9'], 0, 1, 'C');
$pdf->SetXY(67, 262);
$pdf->Cell(37, 5, $Search['ML_10'], 0, 1, 'C');
$pdf->SetXY(67, 267);
$pdf->Cell(37, 5, $Search['ML_11'], 0, 1, 'C');
$pdf->SetXY(67, 272);
$pdf->Cell(37, 5, $Search['ML_12'], 0, 1, 'C');
$pdf->SetXY(67, 277);
$pdf->Cell(37, 5, $Search['ML_13'], 0, 1, 'C');
$pdf->SetXY(67, 282);
$pdf->Cell(37, 5, $Search['ML_14'], 0, 1, 'C');
$pdf->SetXY(67, 287);
$pdf->Cell(37, 5, $Search['ML_15'], 0, 1, 'C');
$pdf->SetXY(67, 292);
$pdf->Cell(37, 5, $Search['ML_16'], 0, 1, 'C');
$pdf->SetXY(67, 297);
$pdf->Cell(37, 5, $Search['ML_17'], 0, 1, 'C');
$pdf->SetXY(67, 302);
$pdf->Cell(37, 5, $Search['ML_18'], 0, 1, 'C');
$pdf->SetXY(67, 307);
$pdf->Cell(37, 5, $Search['ML_19'], 0, 1, 'C');
$pdf->SetXY(67, 312);
$pdf->Cell(37, 5, $Search['ML_20'], 0, 1, 'C');
$pdf->SetXY(67, 317);
$pdf->Cell(37, 5, $Search['ML_21'], 0, 1, 'C');
$pdf->SetXY(67, 322);
$pdf->Cell(37, 5, $Search['ML_22'], 0, 1, 'C');

$pdf->SetXY(104, 217);
$pdf->Cell(28, 5, $Search['Seg_1'], 0, 1, 'C');
$pdf->SetXY(104, 222);
$pdf->Cell(28, 5, $Search['Seg_2'], 0, 1, 'C');
$pdf->SetXY(104, 227);
$pdf->Cell(28, 5, $Search['Seg_3'], 0, 1, 'C');
$pdf->SetXY(104, 232);
$pdf->Cell(28, 5, $Search['Seg_4'], 0, 1, 'C');
$pdf->SetXY(104, 237);
$pdf->Cell(28, 5, $Search['Seg_5'], 0, 1, 'C');
$pdf->SetXY(104, 242);
$pdf->Cell(28, 5, $Search['Seg_6'], 0, 1, 'C');
$pdf->SetXY(104, 247);
$pdf->Cell(28, 5, $Search['Seg_7'], 0, 1, 'C');
$pdf->SetXY(104, 252);
$pdf->Cell(28, 5, $Search['Seg_8'], 0, 1, 'C');
$pdf->SetXY(104, 257);
$pdf->Cell(28, 5, $Search['Seg_9'], 0, 1, 'C');
$pdf->SetXY(104, 262);
$pdf->Cell(28, 5, $Search['Seg_10'], 0, 1, 'C');
$pdf->SetXY(104, 267);
$pdf->Cell(28, 5, $Search['Seg_11'], 0, 1, 'C');
$pdf->SetXY(104, 272);
$pdf->Cell(28, 5, $Search['Seg_12'], 0, 1, 'C');
$pdf->SetXY(104, 277);
$pdf->Cell(28, 5, $Search['Seg_13'], 0, 1, 'C');
$pdf->SetXY(104, 282);
$pdf->Cell(28, 5, $Search['Seg_14'], 0, 1, 'C');
$pdf->SetXY(104, 287);
$pdf->Cell(28, 5, $Search['Seg_15'], 0, 1, 'C');
$pdf->SetXY(104, 292);
$pdf->Cell(28, 5, $Search['Seg_16'], 0, 1, 'C');
$pdf->SetXY(104, 297);
$pdf->Cell(28, 5, $Search['Seg_17'], 0, 1, 'C');
$pdf->SetXY(104, 302);
$pdf->Cell(28, 5, $Search['Seg_18'], 0, 1, 'C');
$pdf->SetXY(104, 307);
$pdf->Cell(28, 5, $Search['Seg_19'], 0, 1, 'C');
$pdf->SetXY(104, 312);
$pdf->Cell(28, 5, $Search['Seg_20'], 0, 1, 'C');
$pdf->SetXY(104, 317);
$pdf->Cell(28, 5, $Search['Seg_21'], 0, 1, 'C');
$pdf->SetXY(104, 322);
$pdf->Cell(28, 5, $Search['Seg_22'], 0, 1, 'C');

$pdf->SetXY(132, 217);
$pdf->Cell(20, 5, $Search['Flow_Rate_1'], 0, 1, 'C');
$pdf->SetXY(132, 222);
$pdf->Cell(20, 5, $Search['Flow_Rate_2'], 0, 1, 'C');
$pdf->SetXY(132, 227);
$pdf->Cell(20, 5, $Search['Flow_Rate_3'], 0, 1, 'C');
$pdf->SetXY(132, 232);
$pdf->Cell(20, 5, $Search['Flow_Rate_4'], 0, 1, 'C');
$pdf->SetXY(132, 237);
$pdf->Cell(20, 5, $Search['Flow_Rate_5'], 0, 1, 'C');
$pdf->SetXY(132, 242);
$pdf->Cell(20, 5, $Search['Flow_Rate_6'], 0, 1, 'C');
$pdf->SetXY(132, 247);
$pdf->Cell(20, 5, $Search['Flow_Rate_7'], 0, 1, 'C');
$pdf->SetXY(132, 252);
$pdf->Cell(20, 5, $Search['Flow_Rate_8'], 0, 1, 'C');
$pdf->SetXY(132, 257);
$pdf->Cell(20, 5, $Search['Flow_Rate_9'], 0, 1, 'C');
$pdf->SetXY(132, 262);
$pdf->Cell(20, 5, $Search['Flow_Rate_10'], 0, 1, 'C');
$pdf->SetXY(132, 267);
$pdf->Cell(20, 5, $Search['Flow_Rate_11'], 0, 1, 'C');
$pdf->SetXY(132, 272);
$pdf->Cell(20, 5, $Search['Flow_Rate_12'], 0, 1, 'C');
$pdf->SetXY(132, 277);
$pdf->Cell(20, 5, $Search['Flow_Rate_13'], 0, 1, 'C');
$pdf->SetXY(132, 282);
$pdf->Cell(20, 5, $Search['Flow_Rate_14'], 0, 1, 'C');
$pdf->SetXY(132, 287);
$pdf->Cell(20, 5, $Search['Flow_Rate_15'], 0, 1, 'C');
$pdf->SetXY(132, 292);
$pdf->Cell(20, 5, $Search['Flow_Rate_16'], 0, 1, 'C');
$pdf->SetXY(132, 297);
$pdf->Cell(20, 5, $Search['Flow_Rate_17'], 0, 1, 'C');
$pdf->SetXY(132, 302);
$pdf->Cell(20, 5, $Search['Flow_Rate_18'], 0, 1, 'C');
$pdf->SetXY(132, 307);
$pdf->Cell(20, 5, $Search['Flow_Rate_19'], 0, 1, 'C');
$pdf->SetXY(132, 312);
$pdf->Cell(20, 5, $Search['Flow_Rate_20'], 0, 1, 'C');
$pdf->SetXY(132, 317);
$pdf->Cell(20, 5, $Search['Flow_Rate_21'], 0, 1, 'C');
$pdf->SetXY(132, 322);
$pdf->Cell(20, 5, $Search['Flow_Rate_22'], 0, 1, 'C');

$pdf->SetXY(152, 217);
$pdf->Cell(32, 5, $Search['From_Side_1'], 0, 1, 'C');
$pdf->SetXY(152, 222);
$pdf->Cell(32, 5, $Search['From_Side_2'], 0, 1, 'C');
$pdf->SetXY(152, 227);
$pdf->Cell(32, 5, $Search['From_Side_3'], 0, 1, 'C');
$pdf->SetXY(152, 232);
$pdf->Cell(32, 5, $Search['From_Side_4'], 0, 1, 'C');
$pdf->SetXY(152, 237);
$pdf->Cell(32, 5, $Search['From_Side_5'], 0, 1, 'C');
$pdf->SetXY(152, 242);
$pdf->Cell(32, 5, $Search['From_Side_6'], 0, 1, 'C');
$pdf->SetXY(152, 247);
$pdf->Cell(32, 5, $Search['From_Side_7'], 0, 1, 'C');
$pdf->SetXY(152, 252);
$pdf->Cell(32, 5, $Search['From_Side_8'], 0, 1, 'C');
$pdf->SetXY(152, 257);
$pdf->Cell(32, 5, $Search['From_Side_9'], 0, 1, 'C');
$pdf->SetXY(152, 262);
$pdf->Cell(32, 5, $Search['From_Side_10'], 0, 1, 'C');
$pdf->SetXY(152, 267);
$pdf->Cell(32, 5, $Search['From_Side_11'], 0, 1, 'C');
$pdf->SetXY(152, 272);
$pdf->Cell(32, 5, $Search['From_Side_12'], 0, 1, 'C');
$pdf->SetXY(152, 277);
$pdf->Cell(32, 5, $Search['From_Side_13'], 0, 1, 'C');
$pdf->SetXY(152, 282);
$pdf->Cell(32, 5, $Search['From_Side_14'], 0, 1, 'C');
$pdf->SetXY(152, 287);
$pdf->Cell(32, 5, $Search['From_Side_15'], 0, 1, 'C');
$pdf->SetXY(152, 292);
$pdf->Cell(32, 5, $Search['From_Side_16'], 0, 1, 'C');
$pdf->SetXY(152, 297);
$pdf->Cell(32, 5, $Search['From_Side_17'], 0, 1, 'C');
$pdf->SetXY(152, 302);
$pdf->Cell(32, 5, $Search['From_Side_18'], 0, 1, 'C');
$pdf->SetXY(152, 307);
$pdf->Cell(32, 5, $Search['From_Side_19'], 0, 1, 'C');
$pdf->SetXY(152, 312);
$pdf->Cell(32, 5, $Search['From_Side_20'], 0, 1, 'C');
$pdf->SetXY(152, 317);
$pdf->Cell(32, 5, $Search['From_Side_21'], 0, 1, 'C');
$pdf->SetXY(152, 322);
$pdf->Cell(32, 5, $Search['From_Side_22'], 0, 1, 'C');

$pdf->SetXY(184, 217);
$pdf->Cell(25, 5, $Search['From_Top_1'], 0, 1, 'C');
$pdf->SetXY(184, 222);
$pdf->Cell(25, 5, $Search['From_Top_2'], 0, 1, 'C');
$pdf->SetXY(184, 227);
$pdf->Cell(25, 5, $Search['From_Top_3'], 0, 1, 'C');
$pdf->SetXY(184, 232);
$pdf->Cell(25, 5, $Search['From_Top_4'], 0, 1, 'C');
$pdf->SetXY(184, 237);
$pdf->Cell(25, 5, $Search['From_Top_5'], 0, 1, 'C');
$pdf->SetXY(184, 242);
$pdf->Cell(25, 5, $Search['From_Top_6'], 0, 1, 'C');
$pdf->SetXY(184, 247);
$pdf->Cell(25, 5, $Search['From_Top_7'], 0, 1, 'C');
$pdf->SetXY(184, 252);
$pdf->Cell(25, 5, $Search['From_Top_8'], 0, 1, 'C');
$pdf->SetXY(184, 257);
$pdf->Cell(25, 5, $Search['From_Top_9'], 0, 1, 'C');
$pdf->SetXY(184, 262);
$pdf->Cell(25, 5, $Search['From_Top_10'], 0, 1, 'C');
$pdf->SetXY(184, 267);
$pdf->Cell(25, 5, $Search['From_Top_11'], 0, 1, 'C');
$pdf->SetXY(184, 272);
$pdf->Cell(25, 5, $Search['From_Top_12'], 0, 1, 'C');
$pdf->SetXY(184, 277);
$pdf->Cell(25, 5, $Search['From_Top_13'], 0, 1, 'C');
$pdf->SetXY(184, 282);
$pdf->Cell(25, 5, $Search['From_Top_14'], 0, 1, 'C');
$pdf->SetXY(184, 287);
$pdf->Cell(25, 5, $Search['From_Top_15'], 0, 1, 'C');
$pdf->SetXY(184, 292);
$pdf->Cell(25, 5, $Search['From_Top_16'], 0, 1, 'C');
$pdf->SetXY(184, 297);
$pdf->Cell(25, 5, $Search['From_Top_17'], 0, 1, 'C');
$pdf->SetXY(184, 302);
$pdf->Cell(25, 5, $Search['From_Top_18'], 0, 1, 'C');
$pdf->SetXY(184, 307);
$pdf->Cell(25, 5, $Search['From_Top_19'], 0, 1, 'C');
$pdf->SetXY(184, 312);
$pdf->Cell(25, 5, $Search['From_Top_20'], 0, 1, 'C');
$pdf->SetXY(184, 317);
$pdf->Cell(25, 5, $Search['From_Top_21'], 0, 1, 'C');
$pdf->SetXY(184, 322);
$pdf->Cell(25, 5, $Search['From_Top_22'], 0, 1, 'C');

$pdf->SetXY(209, 217);
$pdf->Cell(48, 5, $Search['Observation_1'], 0, 1, 'C');
$pdf->SetXY(209, 222);
$pdf->Cell(48, 5, $Search['Observation_2'], 0, 1, 'C');
$pdf->SetXY(209, 227);
$pdf->Cell(48, 5, $Search['Observation_3'], 0, 1, 'C');
$pdf->SetXY(209, 232);
$pdf->Cell(48, 5, $Search['Observation_4'], 0, 1, 'C');
$pdf->SetXY(209, 237);
$pdf->Cell(48, 5, $Search['Observation_5'], 0, 1, 'C');
$pdf->SetXY(209, 242);
$pdf->Cell(48, 5, $Search['Observation_6'], 0, 1, 'C');
$pdf->SetXY(209, 247);
$pdf->Cell(48, 5, $Search['Observation_7'], 0, 1, 'C');
$pdf->SetXY(209, 252);
$pdf->Cell(48, 5, $Search['Observation_8'], 0, 1, 'C');
$pdf->SetXY(209, 257);
$pdf->Cell(48, 5, $Search['Observation_9'], 0, 1, 'C');
$pdf->SetXY(209, 262);
$pdf->Cell(48, 5, $Search['Observation_10'], 0, 1, 'C');
$pdf->SetXY(209, 267);
$pdf->Cell(48, 5, $Search['Observation_11'], 0, 1, 'C');
$pdf->SetXY(209, 272);
$pdf->Cell(48, 5, $Search['Observation_12'], 0, 1, 'C');
$pdf->SetXY(209, 277);
$pdf->Cell(48, 5, $Search['Observation_13'], 0, 1, 'C');
$pdf->SetXY(209, 282);
$pdf->Cell(48, 5, $Search['Observation_14'], 0, 1, 'C');
$pdf->SetXY(209, 287);
$pdf->Cell(48, 5, $Search['Observation_15'], 0, 1, 'C');
$pdf->SetXY(209, 292);
$pdf->Cell(48, 5, $Search['Observation_16'], 0, 1, 'C');
$pdf->SetXY(209, 297);
$pdf->Cell(48, 5, $Search['Observation_17'], 0, 1, 'C');
$pdf->SetXY(209, 302);
$pdf->Cell(48, 5, $Search['Observation_18'], 0, 1, 'C');
$pdf->SetXY(209, 307);
$pdf->Cell(48, 5, $Search['Observation_19'], 0, 1, 'C');
$pdf->SetXY(209, 312);
$pdf->Cell(48, 5, $Search['Observation_20'], 0, 1, 'C');
$pdf->SetXY(209, 317);
$pdf->Cell(48, 5, $Search['Observation_21'], 0, 1, 'C');
$pdf->SetXY(209, 322);
$pdf->Cell(48, 5, $Search['Observation_22'], 0, 1, 'C');

// Hole Size After Test mm Dispersive Classification
$pdf->SetFont('Arial', '', 13);
$pdf->SetXY(257, 217);
$pdf->Cell(48, 110, $Search['Hole_Size_After'], 0, 1, 'C');
$pdf->SetXY(305, 217);
$pdf->Cell(33, 110, $Search['Dispersive_Classification'], 0, 1, 'C');

// Comments and Observations
$pdf->SetXY(24, 340);
$pdf->MultiCell(314, 4, $Search['Comments'], 0, 'L');

// GRAFICAS
$imageBase64 = $Search['Graph'];
$imageData = base64_decode($imageBase64);
$tempFile = tempnam(sys_get_temp_dir(), 'image');
file_put_contents($tempFile, $imageData);
$pdf->Image($tempFile, 140, 95, 200, 0, 'PNG');
unlink($tempFile);


$pdf->Output($Search['Sample_ID'] . '-' . $Search['Sample_Number'] . '-' . $Search['Test_Type'] . '.pdf', 'I');
