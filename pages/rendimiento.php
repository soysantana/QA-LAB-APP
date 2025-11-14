<?php
// rendimiento.php
$page_title = 'Desempeño de Técnicos';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

date_default_timezone_set('America/Santo_Domingo');

/* ============================================================
   Helpers básicos
   ============================================================ */
function v($key, $default = null) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : $default;
}
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function key_triplet($sid,$num,$tt){
  return strtoupper(trim((string)$sid)).'|'.strtoupper(trim((string)$num)).'|'.strtoupper(trim((string)$tt));
}

/* ============================================================
   1) Cargamos usuarios (solo los que tienen alias y name)
   ============================================================ */

$aliasMap = []; // [ALIAS_NORMALIZADO] => ['name'=>..., 'job'=>...]
$resUsers = $db->query("
  SELECT alias, name, job
  FROM users
  WHERE alias IS NOT NULL AND alias <> ''
    AND name  IS NOT NULL AND name  <> ''
");
if ($resUsers) {
  while($u = $resUsers->fetch_assoc()){
    $alias = strtoupper(trim($u['alias']));
    if ($alias === '') continue;

    // Si quieres filtrar solo técnicos, puedes descomentar esto:
    // $job = strtoupper((string)($u['job'] ?? ''));
    // if (strpos($job, 'TECHNICAL') === false && strpos($job, 'SUPERVISOR') === false && strpos($job, 'DOCUMENT') === false) {
    //   continue;
    // }

    $aliasMap[$alias] = [
      'name' => $u['name'],
      'job'  => $u['job'] ?? ''
    ];
  }
}

/* ============================================================
   2) Función para extraer alias válidos desde el campo Technician
      - Soporta: A/J, A-J-C, A+B, A J, A,B,C, A\J, etc.
      - Si hay tokens tipo "AJC" → se divide en letras A, J, C.
      - Solo devuelve alias que existan en $aliasMap.
   ============================================================ */
function extract_valid_aliases($raw, $aliasMap){
  $result = [];
  if ($raw === null) return $result;

  // Normalizamos a mayúsculas
  $s = strtoupper((string)$raw);

  // Unificamos separadores raros en "/"
  $s = str_replace(['\\','+','-','|',';',','], '/', $s);
  // Espacios también separan
  $s = preg_replace('/\s+/', '/', $s);
  $s = trim($s, "/ \t\r\n");
  if ($s === '') return $result;

  $tokens = explode('/', $s);
  foreach ($tokens as $tok) {
    $tok = trim($tok);
    if ($tok === '') continue;

    // Quitar puntos finales tipo "A."
    $tokClean = rtrim($tok, '.');

    // Match directo al alias
    if (isset($aliasMap[$tokClean])) {
      $result[$tokClean] = true;
      continue;
    }

    // Si es un bloque de letras (ej: "AJC") y no hay match directo,
    // tratamos cada letra como posible alias (si existe).
    if (preg_match('/^[A-Z]+$/', $tokClean) && strlen($tokClean) > 1 && strlen($tokClean) <= 5) {
      $chars = str_split($tokClean);
      foreach ($chars as $ch) {
        if (isset($aliasMap[$ch])) {
          $result[$ch] = true;
        }
      }
      continue;
    }

    // Si no hay match, se ignora ese token.
  }

  return array_keys($result); // devolvemos lista de alias únicos (normalizados)
}

/* ============================================================
   3) Filtros de la vista
   ============================================================ */

$quick       = v('quick','7d');
$fromInput   = v('from');
$toInput     = v('to');
$filterAlias = v('tech','');   // aquí guardamos el ALIAS normalizado del filtro
$filterType  = v('ttype','');  // Test_Type filtrado

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

// Días en el rango (para promedio ensayos/día)
$days = max(1, (int)floor((strtotime($to) - strtotime($from)) / 86400) + 1);

// Normalizamos alias del filtro
$filterAlias = strtoupper(trim($filterAlias));
if ($filterAlias !== '' && !isset($aliasMap[$filterAlias])) {
  // si el alias no existe en users, lo limpiamos
  $filterAlias = '';
}

/* ============================================================
   4) Tipos de ensayo (combo)
   ============================================================ */
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
    $t = trim($r['T']);
    if ($t!=='') $ttOptions[$t] = true;
  }
}
$ttList = array_keys($ttOptions);
sort($ttList, SORT_NATURAL | SORT_FLAG_CASE);

/* ============================================================
   5) Carga de filas de cada etapa (ensayos en el rango)
   ============================================================ */

function load_stage_rows($db, $table, $dateField, $techField, $from_dt, $to_dt, $filterType = ''){
  $where = [];
  $where[] = "{$dateField} BETWEEN '{$from_dt}' AND '{$to_dt}'";
  $where[] = "Test_Type IS NOT NULL AND Test_Type <> ''";

  if ($filterType !== '') {
    $ftt = $db->escape(strtoupper(trim($filterType)));
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

$rows_reg = load_stage_rows($db, 'lab_test_requisition_form', 'Registed_Date', 'Register_By', $from_dt, $to_dt, $filterType);
$rows_pre = load_stage_rows($db, 'test_preparation',          'Start_Date',    'Technician',   $from_dt, $to_dt, $filterType);
$rows_rea = load_stage_rows($db, 'test_realization',          'Start_Date',    'Technician',   $from_dt, $to_dt, $filterType);
$rows_ent = load_stage_rows($db, 'test_delivery',             'Start_Date',    'Technician',   $from_dt, $to_dt, $filterType);
$rows_dig = load_stage_rows($db, 'test_review',               'Start_Date',    'Register_By',  $from_dt, $to_dt, $filterType);

/* ============================================================
   6) KPIs globales por etapa (por ensayo, no por técnico)
   ============================================================ */

function kpi_from_rows($rows){
  // Un KPI = número de ensayos (filas) en la etapa
  return count($rows);
}

$kpi_reg = kpi_from_rows($rows_reg);
$kpi_pre = kpi_from_rows($rows_pre);
$kpi_rea = kpi_from_rows($rows_rea);
$kpi_ent = kpi_from_rows($rows_ent);
$kpi_dig = kpi_from_rows($rows_dig);
$kpi_total = $kpi_reg + $kpi_pre + $kpi_rea + $kpi_ent + $kpi_dig;

/* ============================================================
   7) Repetidos (test_repeat) + set global de tests repetidos
   ============================================================ */

$repDateCol = 'Start_Date';
$repWhere = "{$repDateCol} BETWEEN '{$from_dt}' AND '{$to_dt}'";
if ($filterType !== '') {
  $ftt = $db->escape(strtoupper(trim($filterType)));
  $repWhere .= " AND UPPER(TRIM(Test_Type)) = '{$ftt}'";
}

$repRows = [];
$repSet  = []; // para marcar en la tabla de últimos ensayos si un test fue repetido
$resRep = $db->query("
  SELECT Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type
  FROM test_repeat
  WHERE {$repWhere}
");
if ($resRep) {
  while($r = $resRep->fetch_assoc()){
    $repRows[] = $r;
    $repSet[ key_triplet($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true;
  }
}
$kpi_rep = count($repRows); // cantidad de ensayos repetidos (no por técnico)

/* ============================================================
   8) Stats por técnico (alias) usando TODAS las etapas
   ============================================================ */

$stats = [];      // [ALIAS] => ['name'=>..., 'job'=>..., 'reg'=>..,'pre'=>..,'rea'=>..,'ent'=>..,'dig'=>..,'total'=>..,'rep'=>..,'types'=>[]]
$aliasUsed = [];  // para combo de técnicos

function ensure_stat(&$stats, &$aliasMap, $alias){
  if (!isset($aliasMap[$alias])) return; // solo técnicos que existan en users
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
      'types' => []  // solo cargamos tipos en REALIZACION
    ];
  }
}

/**
 * Procesa una etapa:
 *  - $rows      = filas de la tabla
 *  - $stageKey  = 'reg'|'pre'|'rea'|'ent'|'dig'
 *  - $isReal    = true para REALIZACION (para mix de ensayos)
 */
function process_stage_rows(&$stats, &$aliasUsed, $rows, $stageKey, $isReal, $aliasMap, $filterAlias){
  foreach ($rows as $r) {
    $aliases = extract_valid_aliases($r['RawTech'], $aliasMap);
    if (empty($aliases)) continue;

    // Si hay filtro por técnico y ese alias no participa en este ensayo, se salta
    if ($filterAlias !== '' && !in_array($filterAlias, $aliases, true)) {
      continue;
    }

    foreach ($aliases as $alias) {
      if (!isset($aliasMap[$alias])) continue; // seguridad extra

      ensure_stat($stats, $aliasMap, $alias);
      $stats[$alias][$stageKey] += 1;
      $stats[$alias]['total']   += 1;
      $aliasUsed[$alias] = true;

      if ($isReal) {
        $tt = $r['Test_Type'] ?: '—';
        if (!isset($stats[$alias]['types'][$tt])) $stats[$alias]['types'][$tt] = 0;
        $stats[$alias]['types'][$tt] += 1;
      }
    }
  }
}

// Ejecutamos para cada etapa
process_stage_rows($stats, $aliasUsed, $rows_reg, 'reg', false, $aliasMap, $filterAlias);
process_stage_rows($stats, $aliasUsed, $rows_pre, 'pre', false, $aliasMap, $filterAlias);
process_stage_rows($stats, $aliasUsed, $rows_rea, 'rea', true,  $aliasMap, $filterAlias);
process_stage_rows($stats, $aliasUsed, $rows_ent, 'ent', false, $aliasMap, $filterAlias);
process_stage_rows($stats, $aliasUsed, $rows_dig, 'dig', false, $aliasMap, $filterAlias);

/* ============================================================
   9) Repetidos por técnico (usando test_realization → Technician)
   ============================================================ */

foreach ($repRows as $rr) {
  $sid = $db->escape($rr['Sample_ID']);
  $num = $db->escape($rr['Sample_Number']);
  $tt  = $db->escape($rr['Test_Type']);

  $qr = $db->query("
    SELECT Technician
    FROM test_realization
    WHERE Sample_ID = '{$sid}'
      AND Sample_Number = '{$num}'
      AND UPPER(TRIM(Test_Type)) = '{$tt}'
      AND Start_Date BETWEEN '{$from_dt}' AND '{$to_dt}'
  ");
  if ($qr) {
    while($a = $qr->fetch_assoc()){
      $aliases = extract_valid_aliases($a['Technician'], $aliasMap);
      if (empty($aliases)) continue;

      foreach ($aliases as $alias) {
        if (!isset($aliasMap[$alias])) continue;

        // Si hay filtro por técnico y este alias no coincide, lo podemos saltar
        if ($filterAlias !== '' && $alias !== $filterAlias) continue;

        ensure_stat($stats, $aliasMap, $alias);
        $stats[$alias]['rep'] += 1;
        $aliasUsed[$alias] = true;
      }
    }
  }
}

/* ============================================================
   10) Cálculos derivados por técnico (promedios, % rep, orden)
   ============================================================ */

foreach ($stats as $alias => &$st) {
  $st['avg_per_day'] = $st['total'] > 0 ? round($st['total'] / $days, 2) : 0;
  $st['rep_pct']     = $st['total'] > 0 ? round(($st['rep'] / $st['total']) * 100, 1) : 0.0;
}
unset($st);

// Ordenamos técnicos por total DESC
$statsSorted = $stats;
uasort($statsSorted, function($a,$b){
  return $b['total'] <=> $a['total'];
});

// Técnico seleccionado
$selectedAlias = $filterAlias;
if ($selectedAlias === '' && !empty($statsSorted)) {
  $selectedAlias = array_key_first($statsSorted);
}
if ($selectedAlias !== '' && !isset($stats[$selectedAlias])) {
  $selectedAlias = '';
}

// Técnico seleccionado: nombre
$selectedName = ($selectedAlias !== '' && isset($aliasMap[$selectedAlias]))
  ? $aliasMap[$selectedAlias]['name']
  : '';

/* ============================================================
   11) Datos para chart por técnico (top 10)
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
   12) Donut del técnico seleccionado (mix de ensayos en Realización)
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
   13) Últimos ensayos del técnico seleccionado (máx. 50)
   ============================================================ */

$lastRows = [];
if ($selectedAlias !== '') {
  // Traemos todos los ensayos del rango y luego filtramos en PHP por alias
  $sqlLast = "
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

  $resLast = $db->query($sqlLast);
  if ($resLast) {
    while($r = $resLast->fetch_assoc()){
      $aliases = extract_valid_aliases($r['RawTech'], $aliasMap);
      if (empty($aliases)) continue;
      if (!in_array($selectedAlias, $aliases, true)) continue;

      $k = key_triplet($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']);
      $r['is_rep'] = isset($repSet[$k]);
      $lastRows[] = $r;
      if (count($lastRows) >= 50) break;
    }
  }
}

// % global de repetidos
$global_rep_pct = $kpi_total > 0 ? round(($kpi_rep / $kpi_total) * 100, 1) : 0.0;

/* ============================================================
   14) Lista de técnicos para el combo (solo los que tienen stats)
   ============================================================ */

$techList = []; // [alias] => name
foreach ($stats as $alias => $st) {
  $techList[$alias] = $st['name'];
}
asort($techList, SORT_NATURAL | SORT_FLAG_CASE);
?>
<main id="main" class="main" style="padding:18px;">
  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="mb-0">Desempeño de Técnicos</h1>
      <small class="text-muted">
        Resumen de productividad, mix de ensayos y repetición por técnico (solo usuarios definidos en <code>users</code>).
      </small>
    </div>
    <span class="badge bg-light text-dark border d-none d-md-inline-flex align-items-center gap-1">
      <i class="bi bi-calendar-range"></i>
      Rango aplicado: <strong><?= h($from) ?></strong> a <strong><?= h($to) ?></strong>
    </span>
  </div>

  <section class="section mb-3">
    <!-- Filtros -->
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
          <div class="col-12 col-md-3">
            <label class="form-label form-label-sm">Intervalo rápido</label>
            <select name="quick" class="form-select form-select-sm">
              <option value="today" <?= $quick==='today'?'selected':''; ?>>Hoy</option>
              <option value="7d"    <?= $quick==='7d'?'selected':''; ?>>Últimos 7 días</option>
              <option value="30d"   <?= $quick==='30d'?'selected':''; ?>>Últimos 30 días</option>
              <option value="12m"   <?= $quick==='12m'?'selected':''; ?>>Últimos 12 meses</option>
              <option value="custom"<?= $quick==='custom'?'selected':''; ?>>Personalizado</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Desde</label>
            <input type="date" class="form-control form-control-sm" name="from" value="<?= h($from) ?>">
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Hasta</label>
            <input type="date" class="form-control form-control-sm" name="to" value="<?= h($to) ?>">
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Técnico</label>
            <select name="tech" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach ($techList as $alias => $name): ?>
                <option value="<?= h($alias) ?>" <?= $filterAlias===$alias?'selected':''; ?>>
                  <?= h($name) ?> (<?= h($alias) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Tipo de ensayo</label>
            <select name="ttype" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach ($ttList as $tt): ?>
                <option value="<?= h($tt) ?>" <?= strtoupper(trim($filterType))===strtoupper(trim($tt))?'selected':''; ?>>
                  <?= h($tt) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-md-1 d-grid">
            <button class="btn btn-primary btn-sm">
              <i class="bi bi-funnel me-1"></i>Aplicar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- KPIs Globales -->
    <div class="row g-3 mb-3">
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label">Ensayos totales</div>
              <div class="kpi-value"><?= number_format($kpi_total) ?></div>
            </div>
            <div class="kpi-icon kpi-icon-main">
              <i class="bi bi-collection"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Ensayos procesados en el rango seleccionado (todas las etapas).
          </div>
        </div>
      </div>
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label">Registradas</div>
              <div class="kpi-value"><?= number_format($kpi_reg) ?></div>
            </div>
            <div class="kpi-icon kpi-icon-prep">
              <i class="bi bi-clipboard-plus"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Ensayos ingresados al sistema.
          </div>
        </div>
      </div>
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label">Preparadas</div>
              <div class="kpi-value"><?= number_format($kpi_pre) ?></div>
            </div>
            <div class="kpi-icon kpi-icon-prep">
              <i class="bi bi-hammer"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Ensayos que pasaron por preparación.
          </div>
        </div>
      </div>
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label">Realizadas</div>
              <div class="kpi-value"><?= number_format($kpi_rea) ?></div>
            </div>
            <div class="kpi-icon kpi-icon-real">
              <i class="bi bi-activity"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Ensayos ejecutados en laboratorio.
          </div>
        </div>
      </div>
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label">Entregadas</div>
              <div class="kpi-value"><?= number_format($kpi_ent) ?></div>
            </div>
            <div class="kpi-icon kpi-icon-ent">
              <i class="bi bi-box-arrow-up-right"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Ensayos con hoja de trabajo entregada.
          </div>
        </div>
      </div>
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label">Repetidos</div>
              <div class="kpi-value">
                <?= number_format($kpi_rep) ?>
                <span class="kpi-mini text-danger">(<?= $global_rep_pct ?>%)</span>
              </div>
            </div>
            <div class="kpi-icon kpi-icon-repeat">
              <i class="bi bi-arrow-repeat"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Ensayos que terminaron en repetición (asociados a Realización).
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BLOQUE 2: Ranking + Chart -->
  <section class="mb-3">
    <div class="row g-3">
      <!-- Ranking por técnico -->
      <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
              <strong>Ranking por técnico</strong>
              <div class="small text-muted">Volumen por etapa y repetición (solo técnicos definidos en <code>users</code>).</div>
            </div>
            <span class="badge bg-light text-muted border small">
              Técnicos: <?= count($statsSorted) ?>
            </span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Técnico</th>
                    <th class="text-end">Reg</th>
                    <th class="text-end">Prep</th>
                    <th class="text-end">Real</th>
                    <th class="text-end">Ent</th>
                    <th class="text-end">Dig</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">% Repet</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(empty($statsSorted)): ?>
                    <tr><td colspan="8" class="text-muted text-center py-3">Sin datos en el rango seleccionado.</td></tr>
                  <?php else: ?>
                    <?php foreach($statsSorted as $alias => $st): ?>
                      <?php
                        $isSel = ($selectedAlias === $alias);
                        $name  = $st['name'];
                      ?>
                      <tr class="row-tech <?= $isSel ? 'table-primary' : '' ?>">
                        <td>
                          <a href="?quick=<?= h($quick) ?>&from=<?= h($from) ?>&to=<?= h($to) ?>&tech=<?= urlencode($alias) ?>&ttype=<?= urlencode($filterType) ?>"
                             class="d-flex align-items-center gap-2 text-decoration-none text-reset">
                            <div class="avatar-tech"><?= h(mb_substr($name,0,1,'UTF-8')) ?></div>
                            <div>
                              <div class="fw-semibold"><?= h($name) ?></div>
                              <div class="small text-muted">
                                <?= $st['avg_per_day'] ?> ensayos/día
                              </div>
                            </div>
                          </a>
                        </td>
                        <td class="text-end"><?= $st['reg'] ?: '' ?></td>
                        <td class="text-end"><?= $st['pre'] ?: '' ?></td>
                        <td class="text-end"><?= $st['rea'] ?: '' ?></td>
                        <td class="text-end"><?= $st['ent'] ?: '' ?></td>
                        <td class="text-end"><?= $st['dig'] ?: '' ?></td>
                        <td class="text-end fw-semibold"><?= $st['total'] ?></td>
                        <td class="text-end <?= $st['rep_pct']>0?'text-danger':'text-success' ?>">
                          <?= $st['rep'] ? $st['rep'].' ('.$st['rep_pct'].'%)' : '0%' ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer bg-white small text-muted">
            Haz clic en un técnico para ver su detalle abajo.
          </div>
        </div>
      </div>

      <!-- Chart apilado por técnico -->
      <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
              <strong>Distribución por etapa</strong>
              <div class="small text-muted">Ensayos por técnico, apilados por etapa (Top 10).</div>
            </div>
          </div>
          <div class="card-body">
            <div id="chartByTech" style="height: 360px;"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BLOQUE 3: Perfil del técnico seleccionado -->
  <section>
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
          <strong>Perfil del técnico seleccionado</strong>
          <div class="small text-muted">
            Resumen por tipo de ensayo (Realización) y últimos ensayos donde participó.
          </div>
        </div>
        <span class="badge bg-light text-muted border small">
          Seleccionado:
          <strong><?= $selectedAlias!=='' ? h($selectedName).' ('.h($selectedAlias).')' : '—' ?></strong>
        </span>
      </div>
      <div class="card-body">
        <?php if($selectedAlias==='' || !isset($stats[$selectedAlias])): ?>
          <div class="text-muted text-center py-4">
            Selecciona un técnico en el ranking para ver su detalle.
          </div>
        <?php else:
          $stSel = $stats[$selectedAlias];
        ?>
        <div class="row g-3">
          <!-- Resumen + donut -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="avatar-tech avatar-lg"><?= h(mb_substr($selectedName,0,1,'UTF-8')) ?></div>
                  <div>
                    <div class="fw-semibold"><?= h($selectedName) ?></div>
                    <div class="small text-muted"><?= h($aliasMap[$selectedAlias]['job'] ?? 'Técnico de laboratorio') ?></div>
                  </div>
                </div>
                <ul class="list-unstyled mb-2 small">
                  <li><strong>Ensayos totales:</strong> <?= $stSel['total'] ?></li>
                  <li><strong>Ensayos/día (prom):</strong> <?= $stSel['avg_per_day'] ?></li>
                  <li><strong>Repeticiones:</strong> <?= $stSel['rep'] ?> (<?= $stSel['rep_pct'] ?>%)</li>
                </ul>
                <hr>
                <div class="small text-muted mb-1">Resumen por etapa:</div>
                <ul class="list-unstyled small mb-0">
                  <li>Registradas: <strong><?= $stSel['reg'] ?></strong></li>
                  <li>Preparadas: <strong><?= $stSel['pre'] ?></strong></li>
                  <li>Realizadas: <strong><?= $stSel['rea'] ?></strong></li>
                  <li>Entregadas: <strong><?= $stSel['ent'] ?></strong></li>
                  <li>Digitadas: <strong><?= $stSel['dig'] ?></strong></li>
                </ul>
              </div>
            </div>
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="small text-muted mb-2">Distribución por tipo de ensayo (Realización)</div>
                <div id="chartTechDonut" style="height:220px;"></div>
              </div>
            </div>
          </div>

          <!-- Últimos ensayos -->
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="small text-muted">Últimos ensayos donde participó el técnico</div>
                  <span class="badge bg-light text-muted border small">
                    Máx. 50 registros
                  </span>
                </div>
                <div class="table-responsive" style="max-height:320px; overflow:auto;">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Fecha</th>
                        <th>Sample ID</th>
                        <th>#</th>
                        <th>Ensayo</th>
                        <th>Etapa</th>
                        <th>¿Repetido?</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(empty($lastRows)): ?>
                        <tr><td colspan="6" class="text-muted text-center py-3">Sin registros recientes para este técnico.</td></tr>
                      <?php else: ?>
                        <?php foreach($lastRows as $r): ?>
                          <tr>
                            <td><?= h(substr($r['Dt'],0,10)) ?></td>
                            <td><?= h($r['Sample_ID']) ?></td>
                            <td><?= h($r['Sample_Number']) ?></td>
                            <td><code><?= h($r['Test_Type']) ?></code></td>
                            <td>
                              <?php
                                $stage = $r['Stage'];
                                $badgeClass = 'bg-secondary-subtle text-secondary';
                                if ($stage==='Registrada')   $badgeClass = 'bg-light text-muted border';
                                if ($stage==='Preparación') $badgeClass = 'bg-primary-subtle text-primary border';
                                if ($stage==='Realización') $badgeClass = 'bg-info-subtle text-info border';
                                if ($stage==='Entrega')      $badgeClass = 'bg-success-subtle text-success border';
                                if ($stage==='Digitado')     $badgeClass = 'bg-warning-subtle text-warning border';
                              ?>
                              <span class="badge <?= $badgeClass ?>"><?= h($stage) ?></span>
                            </td>
                            <td>
                              <?php if($r['is_rep']): ?>
                                <span class="badge bg-danger-subtle text-danger border">Sí</span>
                              <?php else: ?>
                                —
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
// Chart apilado por técnico
(function(){
  const el = document.getElementById('chartByTech');
  if(!el) return;
  const chart = echarts.init(el);

  const techs  = <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE); ?>;
  const reg    = <?= json_encode($series_reg); ?>;
  const pre    = <?= json_encode($series_pre); ?>;
  const rea    = <?= json_encode($series_rea); ?>;
  const ent    = <?= json_encode($series_ent); ?>;
  const dig    = <?= json_encode($series_dig); ?>;

  const option = {
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    legend: { top: 0 },
    grid: { left: 10, right: 10, bottom: 10, top: 40, containLabel: true },
    xAxis: { type: 'value' },
    yAxis: { type: 'category', data: techs },
    series: [
      { name:'Registradas', type:'bar', stack:'total', data:reg },
      { name:'Preparadas',  type:'bar', stack:'total', data:pre },
      { name:'Realizadas',  type:'bar', stack:'total', data:rea },
      { name:'Entregadas',  type:'bar', stack:'total', data:ent },
      { name:'Digitadas',   type:'bar', stack:'total', data:dig }
    ]
  };
  chart.setOption(option);
  window.addEventListener('resize', () => chart.resize());
})();

// Donut del técnico seleccionado
(function(){
  const el = document.getElementById('chartTechDonut');
  if(!el) return;
  const chart = echarts.init(el);

  const dataDonut = <?= json_encode($donutData, JSON_UNESCAPED_UNICODE); ?>;

  if(!dataDonut || dataDonut.length === 0){
    chart.clear();
    return;
  }

  const option = {
    tooltip: { trigger: 'item' },
    legend: { bottom: 0 },
    series: [
      {
        name: 'Ensayos',
        type: 'pie',
        radius: ['40%','70%'],
        avoidLabelOverlap: false,
        itemStyle: { borderRadius: 6, borderWidth: 2 },
        label: { show: false, position: 'center' },
        emphasis: {
          label: { show: true, fontSize: 14, fontWeight: 'bold' }
        },
        labelLine: { show: false },
        data: dataDonut
      }
    ]
  };
  chart.setOption(option);
  window.addEventListener('resize', () => chart.resize());
})();
</script>

<style>
  .kpi-card{
    border-radius:14px;
    border:1px solid #e5e7eb;
    background:#ffffff;
    box-shadow:0 4px 12px rgba(15,23,42,0.04);
    padding:0.75rem 0.9rem;
    height:100%;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
  }
  .kpi-card-main{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:0.75rem;
  }
  .kpi-label{
    font-size:0.75rem;
    text-transform:uppercase;
    letter-spacing:0.06em;
    color:#64748b;
  }
  .kpi-value{
    font-size:1.5rem;
    font-weight:700;
    color:#0f172a;
  }
  .kpi-mini{
    font-size:0.8rem;
    font-weight:500;
    margin-left:0.15rem;
  }
  .kpi-icon{
    width:36px;
    height:36px;
    border-radius:999px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.1rem;
  }
  .kpi-icon-main{   background:#eff6ff; color:#1d4ed8; }
  .kpi-icon-prep{   background:#ecfeff; color:#0891b2; }
  .kpi-icon-real{   background:#fef9c3; color:#ca8a04; }
  .kpi-icon-ent{    background:#ecfdf3; color:#15803d; }
  .kpi-icon-repeat{ background:#fef2f2; color:#b91c1c; }
  .kpi-subtext{ margin-top:0.25rem; }

  .avatar-tech{
    width:28px;
    height:28px;
    border-radius:999px;
    background:#e0f2fe;
    color:#0369a1;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:0.8rem;
    font-weight:600;
  }
  .avatar-tech.avatar-lg{
    width:40px;
    height:40px;
    font-size:1.1rem;
  }
  .row-tech{
    cursor:pointer;
  }
  .row-tech:hover{
    background:#f8fafc;
  }
</style>

<?php include_once('../components/footer.php'); ?>
