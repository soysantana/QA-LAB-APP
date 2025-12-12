<?php
// /services/performance_service.php

/* ============================================================
   Helpers generales
============================================================ */
function perf_v($key, $default = null) {
  return isset($_REQUEST[$key]) ? trim((string)$_REQUEST[$key]) : $default;
}
function perf_h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function perf_key_triplet($sid,$num,$tt){
  return strtoupper(trim((string)$sid)).'|'.strtoupper(trim((string)$num)).'|'.strtoupper(trim((string)$tt));
}

/* ============================================================
   0) Escape compatible (tu $db es MySqli_DB)
============================================================ */
function perf_escape($db, $s){
  // En tu sistema normalmente existe $db->escape()
  if (method_exists($db, 'escape')) return $db->escape((string)$s);

  // fallback si no existe
  if (method_exists($db, 'escape_string')) return $db->escape_string((string)$s);

  // último recurso: sin escape (no recomendado)
  return (string)$s;
}

/* ============================================================
   1) Alias map desde users
============================================================ */
function perf_load_alias_map($db): array {
  $aliasMap = [];

  $resUsers = $db->query("
    SELECT alias, name, job
    FROM users
    WHERE alias IS NOT NULL AND alias <> ''
      AND name  IS NOT NULL AND name  <> ''
  ");

  if ($resUsers) {
    while($u = $resUsers->fetch_assoc()){
      $alias = strtoupper(trim((string)$u['alias']));
      if ($alias === '') continue;
      $aliasMap[$alias] = [
        'name' => (string)$u['name'],
        'job'  => (string)($u['job'] ?? '')
      ];
    }
  }

  return $aliasMap;
}

/* ============================================================
   2) Extractor de alias desde campo Technician/Register_By
============================================================ */
function perf_extract_valid_aliases($raw, array $aliasMap): array {
  $result = [];
  if ($raw === null) return $result;

  $s = strtoupper((string)$raw);
  $s = str_replace(['\\','+','-','|',';',','], '/', $s);
  $s = preg_replace('/\s+/', '/', $s);
  $s = trim($s, "/ \t\r\n");
  if ($s === '') return $result;

  $tokens = explode('/', $s);

  foreach ($tokens as $tok) {
    $tok = trim($tok);
    if ($tok === '') continue;

    $tokClean = rtrim($tok, '.');

    if (isset($aliasMap[$tokClean])) {
      $result[$tokClean] = true;
      continue;
    }

    // Bloque letras tipo "AJC" => A,J,C (si existen)
    if (preg_match('/^[A-Z]+$/', $tokClean) && strlen($tokClean) > 1 && strlen($tokClean) <= 5) {
      $chars = str_split($tokClean);
      foreach ($chars as $ch) {
        if (isset($aliasMap[$ch])) $result[$ch] = true;
      }
    }
  }

  return array_keys($result);
}

/* ============================================================
   3) Carga de tipos de ensayo (combo)
============================================================ */
function perf_load_test_types($db): array {
  $ttOptions = [];

  $resTT = $db->query("
    SELECT DISTINCT UPPER(TRIM(Test_Type)) AS T
    FROM (
      SELECT Test_Type FROM lab_test_requisition_form
      UNION ALL
      SELECT Test_Type FROM test_preparation
      UNION ALL
      SELECT Test_Type FROM test_realization
      UNION ALL
      SELECT Test_Type FROM test_delivery
    ) x
    WHERE Test_Type IS NOT NULL AND Test_Type <> ''
    ORDER BY T
  ");

  if ($resTT) {
    while($r = $resTT->fetch_assoc()) {
      $t = trim((string)$r['T']);
      if ($t !== '') $ttOptions[$t] = true;
    }
  }

  $ttList = array_keys($ttOptions);
  sort($ttList, SORT_NATURAL | SORT_FLAG_CASE);
  return $ttList;
}

/* ============================================================
   4) Carga filas por etapa
============================================================ */
function perf_load_stage_rows(
  $db,
  string $table,
  string $dateField,
  string $techField,
  string $from_dt,
  string $to_dt,
  string $filterType = ''
): array {

  $where = [];
  $where[] = "{$dateField} BETWEEN '{$from_dt}' AND '{$to_dt}'";
  $where[] = "Test_Type IS NOT NULL AND Test_Type <> ''";

  if ($filterType !== '') {
    $ftt = perf_escape($db, strtoupper(trim($filterType)));
    $where[] = "UPPER(TRIM(Test_Type)) = '{$ftt}'";
  }

  $whereSql = implode(' AND ', $where);

  $sql = "
    SELECT
      {$dateField} AS Dt,
      Sample_ID,
      Sample_Number,
      UPPER(TRIM(Test_Type)) AS Test_Type,
      {$techField} AS RawTech
    FROM {$table}
    WHERE {$whereSql}
  ";

  $rows = [];
  $res = $db->query($sql);
  if ($res) {
    while($r = $res->fetch_assoc()){
      $rows[] = $r;
    }
  }
  return $rows;
}

/* ============================================================
   5) KPIs simples
============================================================ */
function perf_kpi_count(array $rows): int {
  return count($rows);
}

/* ============================================================
   6) Repetidos: rows + set
============================================================ */
function perf_load_repeats(
  $db,
  string $from_dt,
  string $to_dt,
  string $filterType = ''
): array {
  $repWhere = "Start_Date BETWEEN '{$from_dt}' AND '{$to_dt}'";
  if ($filterType !== '') {
    $ftt = perf_escape($db, strtoupper(trim($filterType)));
    $repWhere .= " AND UPPER(TRIM(Test_Type)) = '{$ftt}'";
  }

  $repRows = [];
  $repSet  = [];

  $resRep = $db->query("
    SELECT Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type
    FROM test_repeat
    WHERE {$repWhere}
  ");

  if ($resRep) {
    while($r = $resRep->fetch_assoc()){
      $repRows[] = $r;
      $repSet[ perf_key_triplet($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true;
    }
  }

  return [$repRows, $repSet];
}

/* ============================================================
   7) Stats por técnico
============================================================ */
function perf_ensure_stat(array &$stats, array $aliasMap, string $alias): void {
  if (!isset($aliasMap[$alias])) return;

  if (!isset($stats[$alias])) {
    $stats[$alias] = [
      'name'  => $aliasMap[$alias]['name'],
      'job'   => $aliasMap[$alias]['job'],
      'reg'   => 0,
      'pre'   => 0,
      'rea'   => 0,
      'ent'   => 0,
      'dig'   => 0,
      'total' => 0,
      'rep'   => 0,
      'types' => []
    ];
  }
}

function perf_process_stage_rows(
  array &$stats,
  array &$aliasUsed,
  array $rows,
  string $stageKey,
  bool $isReal,
  array $aliasMap,
  string $filterAlias
): void {
  foreach ($rows as $r) {
    $aliases = perf_extract_valid_aliases($r['RawTech'], $aliasMap);
    if (empty($aliases)) continue;

    if ($filterAlias !== '' && !in_array($filterAlias, $aliases, true)) {
      continue;
    }

    foreach ($aliases as $alias) {
      if (!isset($aliasMap[$alias])) continue;

      perf_ensure_stat($stats, $aliasMap, $alias);
      $stats[$alias][$stageKey] += 1;
      $stats[$alias]['total']   += 1;
      $aliasUsed[$alias] = true;

      if ($isReal) {
        $tt = (string)($r['Test_Type'] ?: '—');
        if (!isset($stats[$alias]['types'][$tt])) $stats[$alias]['types'][$tt] = 0;
        $stats[$alias]['types'][$tt] += 1;
      }
    }
  }
}

/* ============================================================
   8) Repetidos por técnico SIN N+1 (JOIN)
============================================================ */
function perf_assign_repeats_to_tech(
  $db,
  array &$stats,
  array &$aliasUsed,
  array $aliasMap,
  string $from_dt,
  string $to_dt,
  string $filterType,
  string $filterAlias
): void {

  $where = [];
  $where[] = "tr.Start_Date BETWEEN '{$from_dt}' AND '{$to_dt}'";

  if ($filterType !== '') {
    $ftt = perf_escape($db, strtoupper(trim($filterType)));
    $where[] = "UPPER(TRIM(tr.Test_Type)) = '{$ftt}'";
  }

  $whereSql = implode(' AND ', $where);

  $sql = "
    SELECT
      tr.Sample_ID,
      tr.Sample_Number,
      UPPER(TRIM(tr.Test_Type)) AS Test_Type,
      r.Technician
    FROM test_repeat tr
    LEFT JOIN test_realization r
      ON r.Sample_ID = tr.Sample_ID
     AND r.Sample_Number = tr.Sample_Number
     AND UPPER(TRIM(r.Test_Type)) = UPPER(TRIM(tr.Test_Type))
     AND r.Start_Date BETWEEN '{$from_dt}' AND '{$to_dt}'
    WHERE {$whereSql}
  ";

  $res = $db->query($sql);
  if (!$res) return;

  while($row = $res->fetch_assoc()){
    $aliases = perf_extract_valid_aliases($row['Technician'], $aliasMap);
    if (empty($aliases)) continue;

    foreach ($aliases as $alias) {
      if (!isset($aliasMap[$alias])) continue;
      if ($filterAlias !== '' && $alias !== $filterAlias) continue;

      perf_ensure_stat($stats, $aliasMap, $alias);
      $stats[$alias]['rep'] += 1;
      $aliasUsed[$alias] = true;
    }
  }
}

/* ============================================================
   9) Últimos ensayos del técnico seleccionado (max 50)
============================================================ */
function perf_load_last_rows_for_tech(
  $db,
  string $selectedAlias,
  array $aliasMap,
  string $from_dt,
  string $to_dt,
  array $repSet
): array {

  if ($selectedAlias === '') return [];

  $sql = "
    SELECT Registed_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type,
           Register_By AS RawTech, 'Registrada' AS Stage
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$from_dt}' AND '{$to_dt}'
      AND Test_Type IS NOT NULL AND Test_Type <> ''
  UNION ALL
    SELECT Start_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type,
           Technician AS RawTech, 'Preparación' AS Stage
    FROM test_preparation
    WHERE Start_Date BETWEEN '{$from_dt}' AND '{$to_dt}'
      AND Test_Type IS NOT NULL AND Test_Type <> ''
  UNION ALL
    SELECT Start_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type,
           Technician AS RawTech, 'Realización' AS Stage
    FROM test_realization
    WHERE Start_Date BETWEEN '{$from_dt}' AND '{$to_dt}'
      AND Test_Type IS NOT NULL AND Test_Type <> ''
  UNION ALL
    SELECT Start_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type,
           Technician AS RawTech, 'Entrega' AS Stage
    FROM test_delivery
    WHERE Start_Date BETWEEN '{$from_dt}' AND '{$to_dt}'
      AND Test_Type IS NOT NULL AND Test_Type <> ''
  UNION ALL
    SELECT Start_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type,
           Register_By AS RawTech, 'Digitado' AS Stage
    FROM test_review
    WHERE Start_Date BETWEEN '{$from_dt}' AND '{$to_dt}'
      AND Test_Type IS NOT NULL AND Test_Type <> ''
    ORDER BY Dt DESC
  ";

  $lastRows = [];
  $res = $db->query($sql);
  if (!$res) return $lastRows;

  while($r = $res->fetch_assoc()){
    $aliases = perf_extract_valid_aliases($r['RawTech'], $aliasMap);
    if (empty($aliases)) continue;
    if (!in_array($selectedAlias, $aliases, true)) continue;

    $k = perf_key_triplet($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']);
    $r['is_rep'] = isset($repSet[$k]);

    $lastRows[] = $r;
    if (count($lastRows) >= 50) break;
  }

  return $lastRows;
}
