<?php
// ../database/d2434.php

// Requiere que load.php ya esté cargado (por tu página)
$db = $GLOBALS['db'];

function hnum($v){
  if($v === null) return null;
  $v = str_replace(',', '.', trim((string)$v));
  return is_numeric($v) ? (float)$v : null;
}

/* ===== μ(T) por tabla (mPa·s) con interpolación ===== */
function mu_water_mpas($T){
  if($T === null) return null;

  $table = [
    15=>1.138, 16=>1.111, 17=>1.086, 18=>1.060, 19=>1.036,
    20=>1.002, 21=>0.980, 22=>0.958, 23=>0.936, 24=>0.914,
    25=>0.890, 26=>0.868, 27=>0.847, 28=>0.826, 29=>0.806,
    30=>0.797
  ];

  if($T <= 15) return $table[15];
  if($T >= 30) return $table[30];

  $t1 = floor($T);
  $t2 = $t1 + 1;

  if(!isset($table[$t1]) || !isset($table[$t2])) return null;

  $mu1 = $table[$t1];
  $mu2 = $table[$t2];

  // Interpolación lineal
  $mu = $mu1 + (($T - $t1) / ($t2 - $t1)) * ($mu2 - $mu1);
  return $mu;
}

function classifyK($K){
  if($K === null) return null;
  if($K < 1e-9) return "Clay behavior";
  if($K < 1e-6) return "Silt behavior";
  if($K < 1e-5) return "Silty sand behavior";
  if($K < 1e-4) return "Fine sand behavior";
  if($K < 1e-3) return "Medium sand behavior";
  if($K < 1e-2) return "Coarse sand behavior";
  return "Gravel behavior";
}

$MU20 = 1.002; // mPa·s

// ====== Leer cabecera ======
$Technician = $db->escape($_POST['Technician'] ?? '');
$Standard   = $db->escape($_POST['Standard'] ?? 'ASTM D2434');
$TestDate   = $db->escape($_POST['TestDate'] ?? '');
$ReportDate = $db->escape($_POST['ReportDate'] ?? '');
$TestMethod = $db->escape($_POST['TestMethod'] ?? 'Constant Head');
$PMethods   = $db->escape($_POST['PMethods'] ?? '');

$MDD = hnum($_POST['MDD'] ?? null);
$OMC = hnum($_POST['OMC'] ?? null);
$Gs  = hnum($_POST['Gs']  ?? null);

$D = hnum($_POST['D'] ?? null);
$L = hnum($_POST['L'] ?? null);
$A = hnum($_POST['A'] ?? null);
$V = hnum($_POST['V'] ?? null);

$SpecWeight = hnum($_POST['SpecWeight'] ?? null);
$SpecDensity = null;
if($SpecWeight !== null && $V !== null && $V > 0){
  $SpecDensity = $SpecWeight / $V;
}

$RelDensity = hnum($_POST['RelDensity'] ?? null);
$Comments = $db->escape($_POST['Comments'] ?? '');

$rows = $_POST['rows'] ?? [];

// Validación mínima
if(!$rows || !is_array($rows)){
  $msg[] = "No se recibieron corridas.";
  return;
}
if($A === null || $A <= 0 || $L === null || $L <= 0){
  $msg[] = "Área (A) y Longitud (L) deben ser > 0.";
  return;
}

// ====== Calcular runs + promedio ======
$sumK20 = 0.0;
$countK20 = 0;

$runsCalc = []; // guardaremos cálculos para insertar

for($i=1; $i<=15; $i++){
  $r = $rows[$i] ?? [];

  $h1 = hnum($r['h1'] ?? null);
  $h2 = hnum($r['h2'] ?? null);
  $Q  = hnum($r['Q']  ?? null);
  $t  = hnum($r['t']  ?? null);
  $T  = hnum($r['Temp'] ?? null);

  if($h1 === null || $h2 === null || $Q === null || $t === null || $t <= 0){
    // permite que el usuario deje filas en blanco si quiere
    $runsCalc[$i] = [
      'h1'=>$h1,'h2'=>$h2,'h'=>null,'Q'=>$Q,'t'=>$t,'v'=>null,'i'=>null,
      'T'=>$T,'mu'=>null,'K'=>null,'K20'=>null
    ];
    continue;
  }

  $h = $h1 - $h2;
  $iGrad = ($L > 0) ? ($h / $L) : null;

  $v = $Q / ($A * $t);

  $mu = mu_water_mpas($T);
  $K = ($iGrad && $iGrad != 0) ? ($v / $iGrad) : null;
  $K20 = ($K !== null && $mu !== null) ? ($K * ($mu / $MU20)) : null;

  if($K20 !== null){
    $sumK20 += $K20;
    $countK20++;
  }

  $runsCalc[$i] = [
    'h1'=>$h1,'h2'=>$h2,'h'=>$h,'Q'=>$Q,'t'=>$t,'v'=>$v,'i'=>$iGrad,
    'T'=>$T,'mu'=>$mu,'K'=>$K,'K20'=>$K20
  ];
}

$AvgK20 = ($countK20 > 0) ? ($sumK20 / $countK20) : null;
$HydBehavior = classifyK($AvgK20);

// ====== Guardar en BD (transacción) ======
$db->query("START TRANSACTION");

try {

  $sql = "
    INSERT INTO d2434_tests
    (technician, standard, test_date, report_date, test_method, prep_methods,
     mdd, omc, gs, d_m, l_m, a_m2, v_m3,
     spec_weight_kg, spec_density_kgm3, rel_density_pct,
     avg_k20_ms, hyd_behavior, comments)
    VALUES
    ('$Technician', '$Standard', ".($TestDate? "'$TestDate'":"NULL").", ".($ReportDate? "'$ReportDate'":"NULL").",
     '$TestMethod', '$PMethods',
     ".($MDD!==null?$MDD:"NULL").", ".($OMC!==null?$OMC:"NULL").", ".($Gs!==null?$Gs:"NULL").",
     ".($D!==null?$D:"NULL").", ".($L!==null?$L:"NULL").", ".($A!==null?$A:"NULL").", ".($V!==null?$V:"NULL").",
     ".($SpecWeight!==null?$SpecWeight:"NULL").", ".($SpecDensity!==null?$SpecDensity:"NULL").", ".($RelDensity!==null?$RelDensity:"NULL").",
     ".($AvgK20!==null?$AvgK20:"NULL").", ".($HydBehavior?("'".$db->escape($HydBehavior)."'"):"NULL").",
     '$Comments'
    )
  ";
  $db->query($sql);

  // Obtener ID insertado
  $test_id = (int)$db->insert_id();
  if($test_id <= 0) throw new Exception("No se pudo obtener test_id.");

  // Insertar runs
  for($i=1; $i<=15; $i++){
    $c = $runsCalc[$i];

    // si fila totalmente vacía, no guardes
    if($c['h1']===null && $c['h2']===null && $c['Q']===null && $c['t']===null && $c['T']===null){
      continue;
    }

    $sqlr = "
      INSERT INTO d2434_runs
      (test_id, run_no, h1_m, h2_m, h_m, q_m3, t_s, v_ms, i_grad, temp_c, mu_mpas, k_ms, k20_ms)
      VALUES
      ($test_id, $i,
       ".($c['h1']!==null?$c['h1']:"NULL").",
       ".($c['h2']!==null?$c['h2']:"NULL").",
       ".($c['h']!==null?$c['h']:"NULL").",
       ".($c['Q']!==null?$c['Q']:"NULL").",
       ".($c['t']!==null?$c['t']:"NULL").",
       ".($c['v']!==null?$c['v']:"NULL").",
       ".($c['i']!==null?$c['i']:"NULL").",
       ".($c['T']!==null?$c['T']:"NULL").",
       ".($c['mu']!==null?$c['mu']:"NULL").",
       ".($c['K']!==null?$c['K']:"NULL").",
       ".($c['K20']!==null?$c['K20']:"NULL")."
      )
      ON DUPLICATE KEY UPDATE
        h1_m=VALUES(h1_m), h2_m=VALUES(h2_m), h_m=VALUES(h_m),
        q_m3=VALUES(q_m3), t_s=VALUES(t_s),
        v_ms=VALUES(v_ms), i_grad=VALUES(i_grad),
        temp_c=VALUES(temp_c), mu_mpas=VALUES(mu_mpas),
        k_ms=VALUES(k_ms), k20_ms=VALUES(k20_ms)
    ";
    $db->query($sqlr);
  }

  $db->query("COMMIT");
  $msg[] = "Ensayo D2434 guardado correctamente (ID: $test_id).";

} catch(Exception $ex){
  $db->query("ROLLBACK");
  $msg[] = "Error guardando D2434: ".$ex->getMessage();
}
