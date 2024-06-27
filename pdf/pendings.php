<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$columna = isset($_GET['columna']) ? $_GET['columna'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

$Requisition = find_all("lab_test_requisition_form");
$Preparation = find_all("test_preparation");
$Review = find_all("test_review");
$testTypes = [];

foreach ($Requisition as $requisition) {
    $testTypeKey = $columna;

        if (
            isset($requisition[$testTypeKey]) &&
            $requisition[$testTypeKey] !== null &&
            $requisition[$testTypeKey] !== ""
        ) {
            $matchingPreparations = array_filter($Preparation, function (
                $preparation
            ) use ($requisition, $testTypeKey) {
                return $preparation["Sample_Name"] ===
                    $requisition["Sample_ID"] &&
                    $preparation["Sample_Number"] ===
                    $requisition["Sample_Number"] &&
                    $preparation["Test_Type"] === $requisition[$testTypeKey];
            });

            $matchingReviews = array_filter($Review, function (
                $review
            ) use ($requisition, $testTypeKey) {
                return $review["Sample_Name"] ===
                    $requisition["Sample_ID"] &&
                    $review["Sample_Number"] ===
                    $requisition["Sample_Number"] &&
                    $review["Test_Type"] === $requisition[$testTypeKey];
            });

            if (empty($matchingPreparations) && empty($matchingReviews)) {
                $testTypes[] = [
                    "Sample_ID" => $requisition["Sample_ID"],
                    "Sample_Number" => $requisition["Sample_Number"],
                    "Sample_Date" => $requisition["Sample_Date"],
                    "Test_Type" => $requisition[$testTypeKey],
                ];
            }
        }
    }

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi {
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(8.5 * 25.4, 11 * 25.4));

// Importar una página de otro PDF
$pdf->setSourceFile('pendings.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Calcular el centro de la página
$centerX = $pdf->GetPageWidth() / 2;
$centerY = $pdf->GetPageHeight() / 2;

// Agregar un título a la tabla
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetXY($centerX - 50, 40); // Ajusta la posición del título
$pdf->Cell(100, 10, 'Prioridad de ' . $type, 0, 1, 'C');

// Definir el ancho de la tabla
$tableWidth = 180; // Ancho total de las celdas de la tabla

// Calcular la posición de la tabla para centrarla
$tableX = $centerX - ($tableWidth / 2);
$tableY = 50; // Ajusta esto según la posición vertical deseada

// Establecer la posición de la tabla
$pdf->SetXY($tableX, $tableY);

// Encabezados de la tabla
$pdf->SetFillColor(200, 220, 255); // Color de fondo
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(45, 10, 'Sample Date', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Sample Name', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Sample Number', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Metodo', 1, 1, 'C', true);

// Tipo Letra
$pdf->SetFont('Arial', '', 10);

foreach ($testTypes as $index => $sample) {
    
    if ($sample['Test_Type'] === 'SP') { 
        $GrainResults = find_by_sql("SELECT * FROM grain_size_general WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");

        foreach ($GrainResults as $Grain) {
            if ($Grain) {
                $T3p4 = (float)$Grain['CumRet11'];
                $T3p8 = (float)$Grain['CumRet13'];
                $TNo4 = (float)$Grain['CumRet14'];
                $resultado = '';

                if ($T3p4 > 0) {
                    $resultado = "C";
                } elseif ($T3p8 > 0 && $T3p4 == 0) {
                    $resultado = "B";
                } elseif ($TNo4 > 0 && $T3p4 == 0 && $T3p8 == 0) {
                    $resultado = "A";
                } else {
                    $resultado = "No se puede determinar el método";
                }

                $pdf->SetX($tableX); 
                $pdf->Cell(45, 10, $sample['Sample_Date'], 1, 0, 'C'); 
                $pdf->Cell(45, 10, $sample['Sample_ID'], 1, 0, 'C');
                $pdf->Cell(45, 10, $sample['Sample_Number'], 1, 0, 'C'); 
                $pdf->Cell(45, 10, $resultado, 1, 1, 'C');
            }
        }
    } else {
        $pdf->SetX($tableX); 
        $pdf->Cell(45, 10, $sample['Sample_Date'], 1, 0, 'C'); 
        $pdf->Cell(45, 10, $sample['Sample_ID'], 1, 0, 'C');
        $pdf->Cell(45, 10, $sample['Sample_Number'], 1, 0, 'C'); 
        $pdf->Cell(45, 10, '', 1, 1, 'C'); // Método en blanco
    }
}

$pdf->Output();
?>
