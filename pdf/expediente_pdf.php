<?php
require_once "../config/load.php";
require "../libs/fpdf/fpdf.php";

$sampleID  = $_GET["sample"];
$sampleNum = $_GET["num"];

// Buscar información general
$info = find_by_sql("
    SELECT *
    FROM lab_test_requisition_form
    WHERE Sample_ID='{$sampleID}'
      AND Sample_Number='{$sampleNum}'
    LIMIT 1
");

if (!$info) { die("Muestra no encontrada"); }

$info = $info[0];

// Ensayos solicitados
$tests = array_map("trim", explode(",", $info["Test_Type"]));


// TABLAS por ensayo (igual a expediente.php)
$testTables = [
    "AL" => ["table"=>"atterberg_limit", "name"=>"Atterberg Limit"],
    "GS" => ["table"=>"grain_size_general", "name"=>"Grain Size"],
    "SP" => ["table"=>"standard_proctor", "name"=>"Standard Proctor"],
    "MC" => ["table"=>"moisture_oven", "name"=>"Moisture Content"],
    "UCS" => ["table"=>"unixial_compressive", "name"=>"Unconfined Compression"],
    "HY" => ["table"=>"hydrometer", "name"=>"Hydrometer"],
    "DHY"=> ["table"=>"double_hydrometer", "name"=>"Double Hydrometer"],
];


// Función para obtener resumen
function resumen_clave_pdf($testType, $row) {

    if ($testType=="AL") {
        return "LL {$row['Liquid_Limit_Porce']}  -  PL {$row['Plastic_Limit_Porce']}  -  PI {$row['Plasticity_Index_Porce']}  -  {$row['Classification']}";
    }

    if ($testType=="GS") {
        return "No.4 {$row['Pass14']}% - No.10 {$row['Pass15']}% - No.200 {$row['Pass22']}%  | Gravel {$row['Gravel']}% •- Sand {$row['Sand']}% - Fines {$row['Fines']}%";
    }

    if ($testType=="SP") {
        return "MDD {$row['MDD']}  |  OMC {$row['OMC']}%";
    }

    if ($testType=="MC") {
        return "Humedad: {$row['Moisture_Content_Porce']}%";
    }

    if ($testType=="UCS") {
        return "qu {$row['UCS_q']} kPa  |  Strain {$row['Strain']}%";
    }

    if ($testType=="HY") {
        return "Clasificación: {$row['Classification1']}";
    }

    return "Sin resumen";
}


// Crear PDF
$pdf = new FPDF("P","mm","Letter");
$pdf->AddPage();
$pdf->SetFont("Arial","B",14);

// Logo
$pdf->Image("../assets/img/Pueblo-VIejo.jpg",10,10,45);
$pdf->Ln(25);

// Título
$pdf->Cell(0,10,"RESUMEN TECNICO - ".$info["Sample_ID"],0,1,"C");
$pdf->Ln(3);


// ===========================
// DATOS GENERALES
// ===========================
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,8,"DATOS GENERALES",0,1);

$pdf->SetFont("Arial","",10);
$pdf->Cell(100,6,"Cliente: ".$info["Client"],0,0);
$pdf->Cell(80,6,"Material: ".$info["Material_Type"],0,1);

$pdf->Cell(100,6,"Sample ID: ".$info["Sample_ID"],0,0);
$pdf->Cell(80,6,"Sample Number: ".$info["Sample_Number"],0,1);

$pdf->Cell(100,6,"Estructura: ".$info["Structure"],0,0);
$pdf->Cell(80,6,"Fecha Registro: ".$info["Registed_Date"],0,1);

$pdf->Ln(3);


// ===========================
// ENSAYOS
// ===========================
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,8,"ENSAYOS REALIZADOS",0,1);

$pdf->SetFont("Arial","",10);

foreach ($tests as $t) {

    if (!isset($testTables[$t])) continue;

    $tbl   = $testTables[$t]["table"];
    $name  = $testTables[$t]["name"];

    // Buscar registro
    $row = find_by_sql("
        SELECT *
        FROM {$tbl}
        WHERE Sample_ID='{$info['Sample_ID']}'
          AND Sample_Number='{$info['Sample_Number']}'
        LIMIT 1
    ");

    $completed = ($row)? true : false;

    $status = $completed ? "✔ Completado" : "✖ Pendiente";
    $color  = $completed ? [0,128,0] : [200,0,0];

    // Nombre ensayo
    $pdf->SetFont("Arial","B",11);
    $pdf->SetTextColor($color[0],$color[1],$color[2]);
    $pdf->Cell(0,7,"{$t}  -  {$name}  -  ".$status,0,1);

    $pdf->SetTextColor(0,0,0);

    if ($completed) {
        $row = $row[0];
        $resumen = resumen_clave_pdf($t,$row);

        $pdf->SetFont("Arial","",9);
        $pdf->MultiCell(0,5,"   ".$resumen);
    }

    $pdf->Ln(2);
}

$pdf->Ln(4);


// PIE
$pdf->SetFont("Arial","I",8);
$pdf->Cell(0,10,"Este documento forma parte del expediente tecnico del laboratorio PVDC",0,1,"C");

$pdf->Output();
