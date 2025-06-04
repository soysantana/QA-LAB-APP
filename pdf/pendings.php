<?php
require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

$type = isset($_GET['type']) ? strtoupper(trim($_GET['type'])) : '';

// Obtener datos
$data = [
    "Requisition" => find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Sample_Date FROM lab_test_requisition_form"),
    "Preparation" => find_by_sql("SELECT Sample_Name, Sample_Number, Test_Type FROM test_preparation"),
    "Delivery" => find_by_sql("SELECT Sample_Name, Sample_Number, Test_Type FROM test_delivery"),
    "Review" => find_by_sql("SELECT Sample_Name, Sample_Number, Test_Type FROM test_review")
];

// Normalizar
function normalize($value)
{
    return strtoupper(trim($value));
}

// Indexar Preparation, Delivery y Review
$indexedStatus = [];

foreach (["Preparation", "Delivery", "Review"] as $category) {
    foreach ($data[$category] as $entry) {
        $key = normalize($entry["Sample_Name"]) . "|" . normalize($entry["Sample_Number"]) . "|" . normalize($entry["Test_Type"]);
        $indexedStatus[$key] = true;
    }
}

$testTypes = [];
$seen = [];

foreach ($data["Requisition"] as $requisition) {
    if (!empty($requisition["Test_Type"])) {
        $sampleID = normalize($requisition["Sample_ID"]);
        $sampleNumber = normalize($requisition["Sample_Number"]);
        $testList = array_map('normalize', explode(",", $requisition["Test_Type"]));

        // Si la muestra tiene el tipo solicitado (por ejemplo GS)
        if (in_array($type, $testList)) {
            $key = $sampleID . "|" . $sampleNumber . "|" . $type;

            // Solo si no está hecho y no lo hemos visto antes
            if (empty($indexedStatus[$key]) && !isset($seen[$key])) {
                $testTypes[] = [
                    "Sample_ID" => $requisition["Sample_ID"],
                    "Sample_Number" => $requisition["Sample_Number"],
                    "Sample_Date" => $requisition["Sample_Date"],
                    "Test_Type" => $type, // Solo el que se pidió
                ];
                $seen[$key] = true;
            }
        }
    }
}

use setasign\Fpdi\Fpdi;

class PDF extends Fpdi
{
    function Header() {}

    function Footer() {}
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);

$pdf->AddPage('P', array(8.5 * 25.4, 11 * 25.4));

// Importar una página de otro PDF
$pdf->setSourceFile('template/Pendings-List.pdf');
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
$pdf->Cell(45, 10, 'Sample ID', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Test Type', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Metodo', 1, 1, 'C', true);

// Tipo Letra
$pdf->SetFont('Arial', '', 10);

foreach ($testTypes as $index => $sample) {

    if ($sample['Test_Type'] === 'SP') {
        // Verificamos si hay resultados para el tamaño de grano
        $GrainResults = find_by_sql("SELECT * FROM grain_size_general WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");

        if (!empty($GrainResults)) {
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
                        $resultado = "No se puede determinar el metodo";
                    }

                    $pdf->SetX($tableX);
                    $pdf->Cell(45, 10, $sample['Sample_Date'], 1, 0, 'C');
                    $pdf->Cell(45, 10, $sample['Sample_ID'] . '-' . $sample['Sample_Number'], 1, 0, 'C');
                    $pdf->Cell(45, 10, $sample['Test_Type'], 1, 0, 'C');
                    $pdf->Cell(45, 10, $resultado, 1, 1, 'C');
                }
            }
        } else {
            // Si no hay resultados de Grain Size
            $pdf->SetX($tableX);
            $pdf->Cell(45, 10, $sample['Sample_Date'], 1, 0, 'C');
            $pdf->Cell(45, 10, $sample['Sample_ID'] . '-' . $sample['Sample_Number'], 1, 0, 'C');
            $pdf->Cell(45, 10, $sample['Test_Type'], 1, 0, 'C');
            $pdf->Cell(45, 10, 'No data', 1, 1, 'C'); // Colocamos "No data" si no hay tamaño de grano
        }
    } else {
        // Si no es tipo SP
        $pdf->SetX($tableX);
        $pdf->Cell(45, 10, $sample['Sample_Date'], 1, 0, 'C');
        $pdf->Cell(45, 10, $sample['Sample_ID'] . '-' . $sample['Sample_Number'], 1, 0, 'C');
        $pdf->Cell(45, 10, $sample['Test_Type'], 1, 0, 'C');
        $pdf->Cell(45, 10, '', 1, 1, 'C'); // Método en blanco
    }
}

$pdf->Output();
