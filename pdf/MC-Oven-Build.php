<?php
require "../libs/fpdf/fpdf.php";
require "../libs/fpdi/src/autoload.php";
require_once "../config/load.php";

use setasign\Fpdi\Fpdi;

// ---------- Helpers ----------
function json_error(int $code, string $msg): never
{
  http_response_code($code);
  header("Content-Type: application/json");
  echo json_encode(["ok" => false, "error" => $msg], JSON_UNESCAPED_UNICODE);
  exit();
}
function json_ok(array $data): never
{
  header("Content-Type: application/json");
  echo json_encode(["ok" => true] + $data, JSON_UNESCAPED_UNICODE);
  exit();
}
function ensure_dir(string $dir): void
{
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }
}
function s($v): string
{
  return preg_replace(
    "/[^A-Za-z0-9\-_.]/",
    "-",
    trim((string) $v) === "" ? "NA" : (string) $v,
  );
}

// ---------- Entrada ----------
page_require_level(2);

$id = $_GET["id"] ?? "";
if ($id === "") {
  json_error(400, "Falta id");
}

$Search = find_by_id("moisture_oven", $id);
if (!$Search) {
  json_error(404, "Ensayo no encontrado");
}

// ---------- PDF ----------
class PDF extends Fpdi
{
  function Header() {}
  function Footer() {}
}

$pdf = new PDF("P", "mm", [300, 260]);
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage("P", [300, 260]);

$root = realpath(__DIR__ . "/.."); // raíz del proyecto
$template =
  $root . "/pdf/template/PV-F-01713 Laboratory Moisture Content With Oven.pdf";
if (!file_exists($template)) {
  json_error(500, "Plantilla no encontrada");
}

$pdf->setSourceFile($template);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont("Arial", "B", 11);

// Project Information
$pdf->SetXY(58.5, 37);
$pdf->Cell(25, 1, (string) ($Search["Project_Name"] ?? ""), 0, 1, "L");
$pdf->SetXY(137, 37);
$pdf->Cell(25, 1, (string) ($Search["Project_Number"] ?? ""), 0, 1, "L");
$pdf->SetXY(220, 34);
$pdf->Cell(25, 5, (string) ($Search["Client"] ?? ""), 0, 1, "L");

// Laboratory Information
$pdf->SetXY(58, 52);
$pdf->Cell(27, 1, "PVDJ SOIL LAB", 0, 1, "L");
$pdf->SetXY(58, 58);
$pdf->Cell(27, 1, (string) ($Search["Technician"] ?? ""), 0, 1, "L");
$pdf->SetXY(58, 64);
$pdf->Cell(27, 1, (string) ($Search["Sample_By"] ?? ""), 0, 1, "L");
$pdf->SetXY(137, 52);
$pdf->Cell(26, 1, (string) ($Search["Standard"] ?? ""), 0, 1, "L");
$pdf->SetXY(137, 55);
$pdf->Cell(27, 5, (string) ($Search["Test_Start_Date"] ?? ""), 0, 1, "L");
$pdf->SetXY(137, 63);
$pdf->Cell(
  26.5,
  1,
  (string) (isset($Search["Registed_Date"])
    ? date("Y-m-d", strtotime($Search["Registed_Date"]))
    : ""),
  0,
  1,
  "L",
);
$pdf->SetXY(220, 52);
$pdf->Cell(26, 1, (string) ($Search["Method"] ?? ""), 0, 1, "L");

// Sample Information
$pdf->SetXY(58.5, 80);
$pdf->Cell(25, 1, (string) ($Search["Structure"] ?? ""), 0, 1, "L");
$pdf->SetXY(58.5, 86);
$pdf->Cell(25, 1, (string) ($Search["Area"] ?? ""), 0, 1, "L");
$pdf->SetXY(58.5, 92);
$pdf->Cell(25, 1, (string) ($Search["Source"] ?? ""), 0, 1, "L");
$pdf->SetXY(58.5, 98);
$pdf->Cell(25, 1, (string) ($Search["Material_Type"] ?? ""), 0, 1, "L");
$pdf->SetXY(137, 78);
$pdf->Cell(25, 1, (string) ($Search["Sample_ID"] ?? ""), 0, 1, "C");
$pdf->SetXY(137, 85);
$pdf->Cell(25, 1, (string) ($Search["Sample_Number"] ?? ""), 0, 1, "C");
$pdf->SetXY(137, 93);
$pdf->Cell(25, 1, (string) ($Search["Sample_Date"] ?? ""), 0, 1, "C");
$pdf->SetXY(137, 100);
$pdf->Cell(25, 1, (string) ($Search["Elev"] ?? ""), 0, 1, "C");
$pdf->SetXY(220, 79);
$pdf->Cell(25, 1, (string) ($Search["Depth_From"] ?? ""), 0, 1, "C");
$pdf->SetXY(220, 86);
$pdf->Cell(25, 1, (string) ($Search["Depth_To"] ?? ""), 0, 1, "C");
$pdf->SetXY(220, 94);
$pdf->Cell(25, 1, (string) ($Search["North"] ?? ""), 0, 1, "C");
$pdf->SetXY(220, 100);
$pdf->Cell(25, 1, (string) ($Search["East"] ?? ""), 0, 1, "C");

// Test information
$pdf->SetXY(147, 117);
$pdf->Cell(25, 1, "1", 0, 1, "C");
$pdf->SetXY(147, 123);
$pdf->Cell(25, 1, (string) ($Search["Tare_Name"] ?? ""), 0, 1, "C");
$pdf->SetXY(147, 129);
$pdf->Cell(
  25,
  1,
  mb_convert_encoding(
    (string) ($Search["Temperature"] ?? ""),
    "ISO-8859-1",
    "UTF-8",
  ),
  0,
  1,
  "C",
);
$pdf->SetXY(147, 134);
$pdf->Cell(25, 1, (string) ($Search["Tare_Plus_Wet_Soil"] ?? ""), 0, 1, "C");
$pdf->SetXY(147, 140);
$pdf->Cell(25, 1, (string) ($Search["Tare_Plus_Dry_Soil"] ?? ""), 0, 1, "C");
$pdf->SetXY(147, 146);
$pdf->Cell(25, 1, (string) ($Search["Water_Ww"] ?? ""), 0, 1, "C");
$pdf->SetXY(147, 152);
$pdf->Cell(25, 1, (string) ($Search["Tare_g"] ?? ""), 0, 1, "C");
$pdf->SetXY(147, 157.5);
$pdf->Cell(25, 1, (string) ($Search["Dry_Soil_Ws"] ?? ""), 0, 1, "C");
$pdf->SetXY(147, 162.5);
$pdf->Cell(
  25,
  1,
  (string) ($Search["Moisture_Content_Porce"] ?? ""),
  0,
  1,
  "C",
);

// Test Results
$passed =
  ($Search["Material_Type"] ?? "") !== "LPF" ||
  (isset($Search["Moisture_Content_Porce"]) &&
    $Search["Moisture_Content_Porce"] >= 14.5 &&
    $Search["Moisture_Content_Porce"] <= 27.4);
$pdf->SetXY(198, 172.5);
$pdf->Cell(25, 1, $passed ? "Passed" : "Failed", 0, 1, "C");

// Comparison Information
$pdf->SetXY(72, 189.5);
$pdf->Cell(
  25,
  1,
  (string) ($Search["Moisture_Content_Porce"] ?? ""),
  0,
  1,
  "C",
);

// Comments Laboratory
$pdf->SetFont("Arial", "", 12);
$pdf->SetXY(24, 208);
$pdf->MultiCell(166, 4, (string) ($Search["Comments"] ?? ""), 0, "L");

$pdf->SetFont("Arial", "B", 12);
$pdf->SetXY(24, 227);
$pdf->MultiCell(166, 4, (string) "Field Comments:", 0, "L");

$pdf->SetFont("Arial", "", 12);
$pdf->SetXY(24, 232);
$pdf->MultiCell(166, 4, (string) ($Search["FieldComment"] ?? ""), 0, "L");

// ----- Generar en memoria -----
$pdfBytes = $pdf->Output("S");
if (!$pdfBytes || strlen($pdfBytes) < 1000) {
  json_error(500, "No se pudo generar PDF");
}

// ---------- Versionado + guardado + doc_files ----------
$sample_id = (string) ($Search["Sample_ID"] ?? "");
$sample_number = (string) ($Search["Sample_Number"] ?? "");
$test_type = (string) ($Search["Test_Type"] ?? "MC_Oven");
$templateName = "MO-Oven";

// next version
$max = find_by_sql(
  sprintf(
    "SELECT MAX(version) AS v FROM doc_files WHERE sample_id='%s' AND sample_number='%s' AND test_type='%s'",
    $db->escape($sample_id),
    $db->escape($sample_number),
    $db->escape($test_type),
  ),
);
$nextVer = (int) ($max[0]["v"] ?? 0) + 1;

// path
$dir = $root . "/uploads/results/" . date("Y/m");
ensure_dir($dir);

$filename = sprintf(
  "%s_%s_%s_v%d.pdf",
  s($sample_id),
  s($sample_number),
  s($templateName),
  $nextVer,
);
$abs = $dir . "/" . $filename;
if (file_put_contents($abs, $pdfBytes) === false) {
  json_error(500, "No se pudo escribir el archivo en disco");
}
$rel = str_replace($root, "", $abs);

// insert doc_files
$db->query(
  sprintf(
    "INSERT INTO doc_files (sample_id,sample_number,test_type,template,version,source,file_path,file_name,status)
   VALUES ('%s','%s','%s','%s',%d,'system','%s','%s','awaiting_signature')",
    $db->escape($sample_id),
    $db->escape($sample_number),
    $db->escape($test_type),
    $db->escape($templateName),
    $nextVer,
    $db->escape($rel),
    $db->escape($filename),
  ),
);

// ---------- Respuesta ----------
json_ok([
  "filename" => $filename,
  "path" => $rel,
  "version" => $nextVer,
  "sample_id" => $sample_id,
  "sample_number" => $sample_number,
  "test_type" => $test_type,
]);
