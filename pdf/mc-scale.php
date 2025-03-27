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
$pdf->AddPage('P', array(300, 220));

// Importar plantilla de PDF
$pdf->setSourceFile('PV-F-01714_Laboratory Moisture Content with Scale_Rev 1.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

// Configurar fuente
$pdf->SetFont('Arial', '', 10);

// Agregar información al PDF
$data_fields = [
    [52, 38, 'PVDJ SOIL LAB'],
    [52, 43, $Search['Technician']],
    [52, 49, $Search['Sample_By']],
    [150, 38, $Search['Methods'], 'C'],
    [150, 43, $Search['Test_Start_Date'], 'C'],
    [150, 49, $Search['Registed_Date'], 'C']
];

foreach ($data_fields as $field) {
    $align = isset($field[3]) ? $field[3] : 'L';
    $pdf->SetXY($field[0], $field[1]);
    $pdf->Cell(30, 1, $field[2], 0, 1, $align);
}

// Información adicional
$pdf->SetFont('Arial', '', 11);
$additional_fields = [
    [109, 64, $Search['Structure']],
    [109, 70, $Search['Area']],
    [109, 74.5, $Search['Source']],
    [109, 80, $Search['Sample_Date']],
    [109, 85, $Search['Sample_ID']],
    [109, 89.5, $Search['Sample_Number']],
    [109, 95, $Search['Material_Type']],
    [109, 99.5, $Search['Depth_From']],
    [109, 104.5, $Search['Depth_To']],
    [109, 109.5, $Search['North']],
    [109, 115, $Search['East']],
    [109, 120, $Search['Elev']],
    [109, 133.5, '1'],
    [109, 138.5, $Search['Tare_Name']],
    [109, 144, $Search['Moisture_Content_Porce']]
];

foreach ($additional_fields as $field) {
    $pdf->SetXY($field[0], $field[1]);
    $pdf->Cell(81, 1, $field[2], 0, 1, 'C');
}

// Agregar comentarios
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(19, 168);
$pdf->Cell(170, 0, $Search['Comments'], 0, 1, 'L');

// Generar y mostrar el PDF
$filename = sprintf('%s-%s-%s.pdf', $Search['Sample_ID'], $Search['Sample_Number'], $Search['Test_Type']);
$pdf->Output($filename, 'I');

?>
