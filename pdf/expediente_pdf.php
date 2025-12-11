<?php
require_once "../config/load.php";
require "../libs/fpdf/fpdf.php";

/* ============================================================
   0. CAPTURA DE PARÁMETROS
============================================================ */
$sampleID  = $_GET["sample"] ?? '';
$sampleNumRaw = $_GET["num"] ?? null;

if ($sampleID === '') {
    die("Sample ID missing.");
}

// Determinar si es paquete o individual
if ($sampleNumRaw === null || $sampleNumRaw === "") {
    $isPackage = true;
    $sampleNum = "";
} else {
    $isPackage = false;
    $sampleNum = trim($sampleNumRaw);
}

/* ============================================================
   1. CONSULTA BASE SEGÚN MODO
============================================================ */

// PAQUETE (muchos Sample_Number)
if ($isPackage) {

    $base = find_by_sql("
        SELECT *
        FROM lab_test_requisition_form
        WHERE Sample_ID = '{$sampleID}'
        ORDER BY Sample_Number ASC
    ");

    if (!$base) die("Package not found.");

}
// INDIVIDUAL (solo un Sample_Number)
else {

    $base = find_by_sql("
        SELECT *
        FROM lab_test_requisition_form
        WHERE Sample_ID = '{$sampleID}'
          AND Sample_Number = '{$sampleNum}'
        LIMIT 1
    ");

    if (!$base) die("Sample+Number not found.");
}

/* ============================================================
   2. INFO GENERAL
============================================================ */
$first = $base[0];

$client    = $first["Client"];
$material  = $first["Material_Type"];
$structure = $first["Structure"];
$source    = $first["Source"];
$regDate   = $first["Registed_Date"];

/* AGRUPAR sample_number → test types */
$group = [];

foreach ($base as $b) {
    $sn = $b["Sample_Number"];
    $tests = array_map("trim", explode(",", $b["Test_Type"]));
    $group[$sn] = $tests;
}

/* ============================================================
   3. TABLAS POR ENSAYO
============================================================ */
$testTables = [
    "AL"  => ["table"=>"atterberg_limit",      "name"=>"Atterberg Limits"],
    "GS"  => ["table"=>"grain_size_general",   "name"=>"Grain Size Analysis"],
    "SP"  => ["table"=>"standard_proctor",     "name"=>"Standard Proctor"],
    "MC"  => ["table"=>"moisture_oven",        "name"=>"Moisture Content"],
    "UCS" => ["table"=>"unixial_compressive",  "name"=>"Unconfined Compression"],
    "HY"  => ["table"=>"hydrometer",           "name"=>"Hydrometer Analysis"],
    "DHY" => ["table"=>"double_hydrometer",    "name"=>"Double Hydrometer"],
];

/* ============================================================
   4. RESÚMENES
============================================================ */
function pdf_summary_results($t, $row) {

    if ($t=="AL") {
        return "LL {$row['Liquid_Limit_Porce']} - PL {$row['Plastic_Limit_Porce']} - PI {$row['Plasticity_Index_Porce']} - Class: {$row['Classification']}";
    }

    if ($t=="GS") {
        return "Sand {$row['Sand']}% - Gravel {$row['Gravel']}% - Fines {$row['Fines']}%";
    }

    if ($t=="SP") {
        return "MDD {$row['MDD']} - OMC {$row['OMC']}%";
    }

    if ($t=="MC") {
        return "Moisture {$row['Moisture_Content_Porce']}%";
    }

    if ($t=="UCS") {
        return "qu {$row['UCS_q']} MPa - Strain {$row['Strain']}%";
    }

    if ($t=="HY") {
        return "Classification: {$row['Classification1']}";
    }

    return "No summary available";
}

/* ============================================================
   5. PDF CONFIG
============================================================ */
$pdf = new FPDF("P","mm","Letter");
$pdf->AddPage();
$pdf->SetMargins(12,12,12);

/* HEADER */
$pdf->Image("../assets/img/Pueblo-VIejo.jpg", 12, 10, 40);

$pdf->SetFont("Arial","B",12);
$pdf->SetXY(160, 12);
$pdf->Cell(0,6,"SOIL MECHANICS LABORATORY",0,1,"R");

$pdf->SetXY(160, 18);
$pdf->SetFont("Arial","",10);
$pdf->Cell(0,6,"Quality Assurance - PVDJ",0,1,"R");

$pdf->Ln(10);

/* TITLE */
$pdf->SetFont("Arial","B",15);

if ($isPackage)
    $pdf->Cell(0,10,"TEST RESULT SUMMARY PACKAGE - {$sampleID}",0,1,"C");
else
    $pdf->Cell(0,10,"TEST RESULT SUMMARY - {$sampleID} / {$sampleNum}",0,1,"C");

$pdf->Ln(5);

/* ============================================================
   6. GENERAL INFORMATION
============================================================ */
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,8,"GENERAL INFORMATION",0,1);

$pdf->SetFont("Arial","",10);
$pdf->Cell(100,6,"Client: {$client}",0,0);
$pdf->Cell(80,6,"Material: {$material}",0,1);

$pdf->Cell(100,6,"Sample ID: {$sampleID}",0,0);
$pdf->Cell(80,6,"Source: {$source}",0,1);

$pdf->Cell(100,6,"Structure: {$structure}",0,0);
$pdf->Cell(80,6,"Registered Date: {$regDate}",0,1);

$pdf->Ln(4);

/* ============================================================
   7. RECORRER CADA SAMPLE NUMBER
============================================================ */
foreach ($group as $sn => $tests) {

    $pdf->SetFont("Arial","B",12);
    $pdf->Cell(0,6,"Sample Number: {$sn}",0,1);

    // Header tabla
    $pdf->SetFont("Arial","B",10);
    $pdf->SetFillColor(230,230,230);

    $pdf->Cell(25,7,"Code",1,0,"C",true);
    $pdf->Cell(60,7,"Test Name",1,0,"C",true);
    $pdf->Cell(25,7,"Status",1,0,"C",true);
    $pdf->Cell(80,7,"Key Results",1,1,"C",true);

    $pdf->SetFont("Arial","",10);

    foreach ($tests as $t) {

        if (!isset($testTables[$t])) continue;

        $tbl  = $testTables[$t]["table"];
        $name = $testTables[$t]["name"];

        $row = find_by_sql("
            SELECT *
            FROM {$tbl}
            WHERE Sample_ID='{$sampleID}'
              AND Sample_Number='{$sn}'
            LIMIT 1
        ");

        $completed = ($row) ? true : false;
        $status    = $completed ? "Completed" : "Pending";
        $results   = $completed ? pdf_summary_results($t, $row[0]) : "-";

        // ROW
        $pdf->Cell(25,7,$t,1,0,"C");
        $pdf->Cell(60,7,$name,1,0);

        // Color del estado
        if ($completed) $pdf->SetTextColor(0,128,0);
        else $pdf->SetTextColor(200,0,0);

        $pdf->Cell(25,7,$status,1,0,"C");
        $pdf->SetTextColor(0,0,0);

        $pdf->Cell(80,7,$results,1,1);
    }

    $pdf->Ln(3);
}

/* ============================================================
   8. FOOTER
============================================================ */
$pdf->Ln(5);
$pdf->SetFont("Arial","I",8);
$pdf->Cell(0,10,"This document summarizes all laboratory tests performed for Sample ID {$sampleID}. Individual reports are archived.",0,1,"C");

/* ============================================================
   9. OUTPUT FILE NAME
============================================================ */

if ($isPackage)
    $filename = "SamplePackage-{$sampleID}.pdf";
else
    $filename = "Sample-{$sampleID}-{$sampleNum}.pdf";

$pdf->Output("I", $filename);
?>
