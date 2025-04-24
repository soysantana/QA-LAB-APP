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
$pdf->setSourceFile('template/PV-F-01714_Laboratory Moisture Content with Scale_Rev 1.pdf');
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
    [107, 64, $Search['Structure']],
    [107, 70, $Search['Area']],
    [107, 74.5, $Search['Source']],
    [107, 80, $Search['Sample_Date']],
    [107, 85, $Search['Sample_ID']],
    [107, 89.5, $Search['Sample_Number']],
    [107, 95, $Search['Material_Type']],
    [107, 99.5, $Search['Depth_From']],
    [107, 104.5, $Search['Depth_To']],
    [107, 109.5, $Search['North']],
    [107, 115, $Search['East']],
    [107, 120, $Search['Elev']],
    [107, 133.6, '1'],
    [107, 138.5, $Search['Tare_Name']],
    [107, 144, $Search['Moisture_Content_Porce']]
];

foreach ($additional_fields as $field) {
    $pdf->SetXY($field[0], $field[1]);
    $pdf->Cell(81, 1, $field[2], 0, 1, 'C');
}

// Agregar comentarios
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(19, 165);
$pdf->Cell(150, 4, $Search['Comments'], 0, 1, 'L');

// Generar y mostrar el PDF
$filename = sprintf('%s-%s-%s.pdf', $Search['Sample_ID'], $Search['Sample_Number'], $Search['Test_Type']);
$pdf->Output($filename, 'I');

?>
