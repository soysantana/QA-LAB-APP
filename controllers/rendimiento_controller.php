<?php
// /controllers/rendimiento_controller.php

date_default_timezone_set('America/Santo_Domingo');

require_once __DIR__ . '/../services/performance_service.php';

/* ============================================================
   1) Alias + filtros + rango
============================================================ */
$aliasMap = perf_load_alias_map($db);

$quick       = perf_v('quick','7d');
$fromInput   = perf_v('from');
$toInput     = perf_v('to');
$filterAlias = perf_v('tech','');
$filterType  = perf_v('ttype','');

$today = date('Y-m-d');
switch ($quick) {
  case 'today':
    $from = $fromInput ?: $today;
    $to   = $toInput   ?: $today;
    break;
  case '30d':
    $from = $fromInput ?: date('Y-m-d', strtotime('-30 days'));
    $to   = $toInput   ?: $today;
    break;
  case '12m':
    $from = $fromInput ?: date('Y-m-d', strtotime('-12 months'));
    $to   = $toInput   ?: $today;
    break;
  case 'custom':
    $from = $fromInput ?: $today;
    $to   = $toInput   ?: $today;
    break;
  case '7d':
  default:
    $from = $fromInput ?: date('Y-m-d', strtotime('-7 days'));
    $to   = $toInput   ?: $today;
    break;
}

$from_dt = $db->escape($from.' 00:00:00');
$to_dt   = $db->escape($to.' 23:59:59');


$days = max(1, (int)floor((strtotime($to) - strtotime($from)) / 86400) + 1);

$filterAlias = strtoupper(trim((string)$filterAlias));
if ($filterAlias !== '' && !isset($aliasMap[$filterAlias])) $filterAlias = '';

/* ============================================================
   2) Combos
============================================================ */
$ttList = perf_load_test_types($db);

/* ============================================================
   3) Cargar filas por etapa
============================================================ */
$rows_reg = perf_load_stage_rows($db, 'lab_test_requisition_form', 'Registed_Date', 'Register_By', $from_dt, $to_dt, $filterType);
$rows_pre = perf_load_stage_rows($db, 'test_preparation',          'Start_Date',    'Technician',   $from_dt, $to_dt, $filterType);
$rows_rea = perf_load_stage_rows($db, 'test_realization',          'Start_Date',    'Technician',   $from_dt, $to_dt, $filterType);
$rows_ent = perf_load_stage_rows($db, 'test_delivery',             'Start_Date',    'Technician',   $from_dt, $to_dt, $filterType);
$rows_dig = perf_load_stage_rows($db, 'test_review',               'Start_Date',    'Register_By',  $from_dt, $to_dt, $filterType);

/* ============================================================
   4) KPIs Globales
============================================================ */
$kpi_reg   = perf_kpi_count($rows_reg);
$kpi_pre   = perf_kpi_count($rows_pre);
$kpi_rea   = perf_kpi_count($rows_rea);
$kpi_ent   = perf_kpi_count($rows_ent);
$kpi_dig   = perf_kpi_count($rows_dig);
$kpi_total = $kpi_reg + $kpi_pre + $kpi_rea + $kpi_ent + $kpi_dig;

/* ============================================================
   5) Repetidos
============================================================ */
[$repRows, $repSet] = perf_load_repeats($db, $from_dt, $to_dt, $filterType);
$kpi_rep = count($repRows);
$global_rep_pct = $kpi_total > 0 ? round(($kpi_rep / $kpi_total) * 100, 1) : 0.0;

/* ============================================================
   6) Stats por técnico
============================================================ */
$stats = [];
$aliasUsed = [];

perf_process_stage_rows($stats, $aliasUsed, $rows_reg, 'reg', false, $aliasMap, $filterAlias);
perf_process_stage_rows($stats, $aliasUsed, $rows_pre, 'pre', false, $aliasMap, $filterAlias);
perf_process_stage_rows($stats, $aliasUsed, $rows_rea, 'rea', true,  $aliasMap, $filterAlias);
perf_process_stage_rows($stats, $aliasUsed, $rows_ent, 'ent', false, $aliasMap, $filterAlias);
perf_process_stage_rows($stats, $aliasUsed, $rows_dig, 'dig', false, $aliasMap, $filterAlias);

// Repetidos por técnico (optimizado con JOIN)
perf_assign_repeats_to_tech($db, $stats, $aliasUsed, $aliasMap, $from_dt, $to_dt, $filterType, $filterAlias);

/* ============================================================
   7) Derivados por técnico + ordenar
============================================================ */
foreach ($stats as $alias => &$st) {
  $st['avg_per_day'] = $st['total'] > 0 ? round($st['total'] / $days, 2) : 0;
  $st['rep_pct']     = $st['total'] > 0 ? round(($st['rep'] / $st['total']) * 100, 1) : 0.0;
}
unset($st);

$statsSorted = $stats;
uasort($statsSorted, function($a,$b){
  return $b['total'] <=> $a['total'];
});

/* ============================================================
   8) Técnico seleccionado
============================================================ */
$selectedAlias = $filterAlias;
if ($selectedAlias === '' && !empty($statsSorted)) {
  $selectedAlias = array_key_first($statsSorted);
}
if ($selectedAlias !== '' && !isset($stats[$selectedAlias])) $selectedAlias = '';

$selectedName = ($selectedAlias !== '' && isset($aliasMap[$selectedAlias]))
  ? $aliasMap[$selectedAlias]['name']
  : '';

/* ============================================================
   9) Chart model (Top 10)
============================================================ */
$chartLabels = [];
$series_reg  = [];
$series_pre  = [];
$series_rea  = [];
$series_ent  = [];
$series_dig  = [];

$chartTechAliases = array_keys($statsSorted);
$chartTechAliases = array_slice($chartTechAliases, 0, 10);

foreach ($chartTechAliases as $alias) {
  $st = $statsSorted[$alias];
  $label = isset($aliasMap[$alias]) ? $aliasMap[$alias]['name'] : $alias;
  $chartLabels[] = $label;
  $series_reg[]  = (int)$st['reg'];
  $series_pre[]  = (int)$st['pre'];
  $series_rea[]  = (int)$st['rea'];
  $series_ent[]  = (int)$st['ent'];
  $series_dig[]  = (int)$st['dig'];
}

/* ============================================================
   10) Donut (mix en realización)
============================================================ */
$donutData = [];
if ($selectedAlias !== '' && isset($stats[$selectedAlias])) {
  $types = $stats[$selectedAlias]['types'] ?? [];
  arsort($types);
  foreach ($types as $tt => $cnt) {
    $donutData[] = ['name'=>$tt, 'value'=>(int)$cnt];
  }
}

/* ============================================================
   11) Últimos ensayos del técnico
============================================================ */
$lastRows = perf_load_last_rows_for_tech($db, $selectedAlias, $aliasMap, $from_dt, $to_dt, $repSet);

/* ============================================================
   12) Lista de técnicos para combo
============================================================ */
$techList = [];
foreach ($stats as $alias => $st) $techList[$alias] = $st['name'];
asort($techList, SORT_NATURAL | SORT_FLAG_CASE);

/* ============================================================
   13) ViewModel final (lo usa la vista)
============================================================ */
$vm = [
  'quick' => $quick,
  'from'  => $from,
  'to'    => $to,
  'days'  => $days,

  'aliasMap' => $aliasMap,
  'techList' => $techList,
  'ttList'   => $ttList,

  'filterAlias' => $filterAlias,
  'filterType'  => $filterType,

  'kpis' => [
    'reg' => $kpi_reg,
    'pre' => $kpi_pre,
    'rea' => $kpi_rea,
    'ent' => $kpi_ent,
    'dig' => $kpi_dig,
    'rep' => $kpi_rep,
    'total' => $kpi_total,
    'rep_pct_global' => $global_rep_pct,
  ],

  'stats'       => $stats,
  'statsSorted' => $statsSorted,

  'selected' => [
    'alias' => $selectedAlias,
    'name'  => $selectedName
  ],

  'charts' => [
    'labels' => $chartLabels,
    'reg' => $series_reg,
    'pre' => $series_pre,
    'rea' => $series_rea,
    'ent' => $series_ent,
    'dig' => $series_dig,
    'donut' => $donutData
  ],

  'lastRows' => $lastRows,
];
