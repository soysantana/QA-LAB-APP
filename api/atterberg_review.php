<?php
@ob_clean();
header('Content-Type: application/json; charset=utf-8');
require_once('../config/load.php');

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing ID"]);
    exit;
}

$id = $db->escape($_GET['id']);

// ============================================================
// 1. OBTENER ENSAYO
// ============================================================
$q = $db->query("
    SELECT id, Technician, Classification, Structure,
           Liquid_Limit_Porce AS LL,
           Plastic_Limit_Porce AS PL,
           Plasticity_Index_Porce AS PI,
           LL_MC_Porce_1, LL_MC_Porce_2, LL_MC_Porce_3,
           PL_MC_Porce_1, PL_MC_Porce_2, PL_MC_Porce_3
    FROM atterberg_limit
    WHERE id = '{$id}'
");

if ($q->num_rows == 0) {
    echo json_encode(["error" => "Test not found"]);
    exit;
}

$t = $q->fetch_assoc();

// ============================================================
// 2. NORMALIZAR TÉCNICOS
// ============================================================
$rawTechs = strtoupper($t['Technician']);
$techs = preg_split("/[\s\/,;|]+/", trim($rawTechs), -1, PREG_SPLIT_NO_EMPTY);

// ============================================================
// 3. LL, PL Y PI POR TRIAL
// ============================================================
$LL_vals = array_filter([
    floatval($t['LL_MC_Porce_1']),
    floatval($t['LL_MC_Porce_2']),
    floatval($t['LL_MC_Porce_3'])
], fn($v) => $v > 0);

$PL_vals = array_filter([
    floatval($t['PL_MC_Porce_1']),
    floatval($t['PL_MC_Porce_2']),
    floatval($t['PL_MC_Porce_3'])
], fn($v) => $v > 0);

$PI_vals = [];
for ($i = 1; $i <= 3; $i++) {
    $ll = floatval($t["LL_MC_Porce_$i"]);
    $pl = floatval($t["PL_MC_Porce_$i"]);
    if ($ll > 0 && $pl > 0) {
        $PI_vals[] = $ll - $pl;
    }
}

// ============================================================
// 4. FUNCIONES ASTM SR y SD
// ============================================================
function calcSR($arr) {
    return (count($arr) >= 2) ? (max($arr) - min($arr)) : null;
}
function calcSD($sr) {
    if ($sr === null) return null;
    if ($sr == 0) return 0;
    return $sr / 2.83; // ASTM
}

$LL_SR = calcSR($LL_vals);
$LL_SD = calcSD($LL_SR);

$PL_SR = calcSR($PL_vals);
$PL_SD = calcSD($PL_SR);

$PI_SR = calcSR($PI_vals);
$PI_SD = calcSD($PI_SR);

// ============================================================
// 5. ASTM LIMITES
// ============================================================
$ASTM = [
  "LL_repeat" => 2.9,
  "PL_repeat" => 2.0,
  "PI_repeat" => 4.0
];

$LL_ok = ($LL_SR !== null && $LL_SR <= $ASTM["LL_repeat"]);
$PL_ok = ($PL_SR !== null && $PL_SR <= $ASTM["PL_repeat"]);
$PI_ok = ($PI_SR !== null && $PI_SR <= $ASTM["PI_repeat"]);

// ============================================================
// 6. REGLA DE PROYECTO PARA PI (CORREGIDA)
//    APLICA A LLD / SD1 / SD2 / SD3 (INCLUYE SUFIJOS)
// ============================================================
$S = strtoupper(trim($t["Structure"]));

$applyPI =
    str_starts_with($S, "LLD") ||
    str_starts_with($S, "SD1") ||
    str_starts_with($S, "SD2") ||
    str_starts_with($S, "SD3");

$PI_value = floatval($t["PI"]);

$PI_req = [
  "applies" => $applyPI,
  "required_min" => ($applyPI ? 15 : null),
  "actual" => $PI_value,
  "status" => ($applyPI
      ? ($PI_value >= 15 ? "OK" : "Fail")
      : "N/A"
  )
];

// ============================================================
// 7. RESPONSE JSON FINAL
// ============================================================
echo json_encode([
  "test" => [
    "LL" => floatval($t["LL"]),
    "PL" => floatval($t["PL"]),
    "PI" => floatval($t["PI"]),
    "Classification" => $t["Classification"],
    "Structure" => $t["Structure"],
    "Technicians" => $techs,
    "LL_vals" => $LL_vals,
    "PL_vals" => $PL_vals,
    "PI_vals" => $PI_vals
  ],

  "ASTM" => [
    "LL_SR" => $LL_SR,
    "LL_SD" => $LL_SD,
    "PL_SR" => $PL_SR,
    "PL_SD" => $PL_SD,
    "PI_SR" => $PI_SR,
    "PI_SD" => $PI_SD,
    "LL_ok" => $LL_ok,
    "PL_ok" => $PL_ok,
    "PI_ok" => $PI_ok
  ],

  "PI_requirement" => $PI_req
]);
?>
