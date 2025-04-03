<?php

require('../libs/fpdf/fpdf.php');
require('../libs/fpdi/src/autoload.php');
require_once('../config/load.php');

use setasign\Fpdi\Fpdi;

// Obtener los datos de la base de datos
$Search = find_by_id('moisture_scale', $_GET['id']);

class PDF extends Fpdi {
    function Header() {
        // Se puede agregar un encabezado personalizado si es necesario
    }

    function Footer() {
        // Se puede agregar un pie de página personalizado si es necesario
    }
}

$pdf = new PDF();
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage('L', array(340, 250));

// Importar plantilla de PDF
$pdf->setSourceFile('template/PV-F-01714 Laboratory Moisture Content with Scale.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Configurar fuente
$pdf->SetFont('Arial', 'B', 10);

// Project y LAB Information
$data_fields = [
    [80, 41.5, $Search['Project_Name']],
    [80, 57, 'PVDJ SOIL LAB'],
    [80, 62.5, $Search['Technician']],
    [80, 68, $Search['Sample_By']],
    [158, 41.5, $Search['Project_Number']],
    [158, 57, ""],
    [158, 62.5, $Search['Test_Start_Date']],
    [158, 68, $Search['Registed_Date']],
    [250, 41.5, $Search['Client']],
    [250, 57, $Search['Methods']],
    [250, 62.5, ""],
    [250, 68, ""]
];

foreach ($data_fields as $field) {
    $align = isset($field[3]) ? $field[3] : 'L';
    $pdf->SetXY($field[0], $field[1]);
    $pdf->Cell(1, 1, $field[2], 0, 1, $align);
}

// Sample Information
$additional_fields = [
    [80, 84, $Search['Structure']],
    [80, 90, $Search['Area']],
    [80, 97, $Search['Source']],
    [80, 103, $Search['Material_Type']],
    [158, 84, $Search['Sample_ID']],
    [158, 90, $Search['Sample_Number']],
    [158, 97, $Search['Sample_Date']],
    [158, 103, $Search['Elev']],
    [250, 84, $Search['Depth_From']],
    [250, 90, $Search['Depth_To']],
    [250, 97, $Search['North']],
    [250, 103, $Search['East']],
    [120, 130, '1'],
    [120, 134.5, $Search['Moisture_Content_Porce']]
];

foreach ($additional_fields as $field) {
    $pdf->SetXY($field[0], $field[1]);
    $pdf->Cell(5, 1, $field[2], 0, 1, 'L');
}

// Agregar comentarios
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(50, 165);
$pdf->Cell(145, 4, $Search['Comments'], 0, 1, 'L');

// Generar y mostrar el PDF
$filename = sprintf('%s-%s-%s.pdf', $Search['Sample_ID'], $Search['Sample_Number'], $Search['Test_Type']);
$pdf->Output($filename, 'I');

?>
