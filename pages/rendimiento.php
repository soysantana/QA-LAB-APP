<?php
// desempeno.php
$page_title = 'Desempeño';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

// =========================
// CONFIG
// =========================
date_default_timezone_set('America/Santo_Domingo');

// Utilidad: limpiar entradas simples
function v($key, $default = null) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : $default;
}

// Intervalo rápido o rango manual
$quick = v('quick', '7d'); // opciones: today, 7d, 30d, 12m, custom
$from  = v('from');
$to    = v('to');

// Resolver fechas
$today = date('Y-m-d');
switch ($quick) {
  case 'today':
    $from = $from ?: $today;
    $to   = $to   ?: $today;
    break;
  case '30d':
    $from = $from ?: date('Y-m-d', strtotime('-30 days'));
    $to   = $to   ?: $today;
    break;
  case '12m':
    $from = $from ?: date('Y-m-d', strtotime('-12 months'));
    $to   = $to   ?: $today;
    break;
  case 'custom':
    // Mantener $from y $to tal cual vengan (obligatorio en UI)
    if(!$from || !$to){ $from = $today; $to = $today; }
    break;
  case '7d':
  default:
    $from = $from ?: date('Y-m-d', strtotime('-7 days'));
    $to   = $to   ?: $today;
}

// Normalizamos límites (inclusivo)
$from_dt = $db->escape($from . ' 00:00:00');
$to_dt   = $db->escape($to   . ' 23:59:59');

// =========================
// QUERIES REUTILIZABLES
// =========================
function get_test_types($db) {
  $sql = "SELECT DISTINCT Test_Type 
          FROM lab_test_requisition_form 
          WHERE Test_Type IS NOT NULL AND Test_Type != ''
          ORDER BY Test_Type";
  $res = $db->query($sql);
  $arr = [];
  if ($res) while($r = $res->fetch_assoc()) $arr[] = $r['Test_Type'];
  return $arr;
}

// Generar query agrupada: técnico × tipo × conteo
// $table: nombre de tabla
// $dateField: campo de fecha
// $techField: campo técnico (en requisitions es Register_By; en otras tablas suelen tener Technician)
function fetch_group($db, $table, $dateField, $techField, $from_dt, $to_dt) {
  $sql = sprintf(
    "SELECT %s AS Technician, Test_Type, COUNT(*) AS Total
     FROM %s
     WHERE %s BETWEEN '%s' AND '%s'
       AND (Test_Type IS NOT NULL AND Test_Type != '')
     GROUP BY %s, Test_Type
     ORDER BY %s, Test_Type",
     $techField, $table, $dateField, $from_dt, $to_dt, $techField, $techField
  );
  return $db->query($sql);
}

// Pivot simple: filas = Technician, columnas = Test_Type
function pivot_rows($result) {
  $data = [];      // [tech][type] = count
  $colset = [];    // set de tipos
  if ($result) {
    while ($row = $result->fetch_assoc()) {
      $t  = $row['Technician'] ?: '—';
      $ty = $row['Test_Type'] ?: '—';
      $n  = (int)$row['Total'];
      if (!isset($data[$t])) $data[$t] = [];
      if (!isset($data[$t][$ty])) $data[$t][$ty] = 0;
      $data[$t][$ty] += $n;
      $colset[$ty] = true;
    }
  }
  $cols = array_keys($colset);
  sort($cols, SORT_NATURAL | SORT_FLAG_CASE);
  ksort($data, SORT_NATURAL | SORT_FLAG_CASE);
  return [$data, $cols];
}

// Totales por etapa para KPIs y chart
function total_count($db, $table, $dateField, $from_dt, $to_dt) {
  $sql = sprintf(
    "SELECT COUNT(*) AS n FROM %s WHERE %s BETWEEN '%s' AND '%s'",
    $table, $dateField, $from_dt, $to_dt
  );
  $r = $db->query($sql);
  $n = 0;
  if ($r && $a = $r->fetch_assoc()) $n = (int)$a['n'];
  return $n;
}

// =========================
// DATOS
// =========================
$testTypes = get_test_types($db);

// Etapas
$registradas = fetch_group($db, 'lab_test_requisition_form', 'Registed_Date', 'Register_By', $from_dt, $to_dt);
$preparadas  = fetch_group($db, 'test_preparation',          'Start_Date',     'Technician',   $from_dt, $to_dt);
$realizadas  = fetch_group($db, 'test_realization',          'Start_Date',     'Technician',   $from_dt, $to_dt);
$entregadas  = fetch_group($db, 'test_delivery',             'Start_Date',     'Technician',   $from_dt, $to_dt);
$digitadas   = fetch_group($db, 'test_review',               'Start_Date',     'Register_By',  $from_dt, $to_dt);

// Pivot para tablas
list($piv_reg, $cols_reg) = pivot_rows($registradas);
list($piv_pre, $cols_pre) = pivot_rows($preparadas);
list($piv_rea, $cols_rea) = pivot_rows($realizadas);
list($piv_ent, $cols_ent) = pivot_rows($entregadas);
list($piv_dig, $cols_dig) = pivot_rows($digitadas);

// KPIs
$kpi_reg = total_count($db, 'lab_test_requisition_form', 'Registed_Date', $from_dt, $to_dt);
$kpi_pre = total_count($db, 'test_preparation',          'Start_Date',    $from_dt, $to_dt);
$kpi_rea = total_count($db, 'test_realization',          'Start_Date',    $from_dt, $to_dt);
$kpi_ent = total_count($db, 'test_delivery',             'Start_Date',    $from_dt, $to_dt);
$kpi_dig = total_count($db, 'test_review',               'Start_Date',    $from_dt, $to_dt);

// Datos para gráfico apilado global: por tipo de ensayo, totales en cada etapa
function totals_by_type($result) {
  $sum = []; // [type] => total
  if ($result) {
    $result->data_seek(0);
    while ($r = $result->fetch_assoc()) {
      $ty = $r['Test_Type'] ?: '—';
      $sum[$ty] = ($sum[$ty] ?? 0) + (int)$r['Total'];
    }
  }
  ksort($sum, SORT_NATURAL | SORT_FLAG_CASE);
  return $sum;
}
$sum_reg = totals_by_type($registradas);
$sum_pre = totals_by_type($preparadas);
$sum_rea = totals_by_type($realizadas);
$sum_ent = totals_by_type($entregadas);
$sum_dig = totals_by_type($digitadas);

// Unificar categorías (tipos) para el chart
$cat_types = array_values(array_unique(array_merge(
  array_keys($sum_reg), array_keys($sum_pre),
  array_keys($sum_rea), array_keys($sum_ent),
  array_keys($sum_dig)
)));
sort($cat_types, SORT_NATURAL | SORT_FLAG_CASE);

// Series apiladas
function series_for($cats, $map) {
  $data = [];
  foreach ($cats as $c) $data[] = (int)($map[$c] ?? 0);
  return $data;
}
$series_reg = series_for($cat_types, $sum_reg);
$series_pre = series_for($cat_types, $sum_pre);
$series_rea = series_for($cat_types, $sum_rea);
$series_ent = series_for($cat_types, $sum_ent);
$series_dig = series_for($cat_types, $sum_dig);

// Helper para generar tabla HTML pivot
function render_pivot_table($title, $piv, $cols) {
  echo "<div class='card shadow-sm mb-4'>
          <div class='card-header bg-light'>
            <strong>$title</strong>
          </div>
          <div class='card-body'>
            <div class='table-responsive'>
            <table class='table table-sm table-striped table-bordered align-middle'>
              <thead class='table-light'>
                <tr>
                  <th style='white-space:nowrap'>Técnico</th>";
  foreach ($cols as $c) {
    echo "<th style='white-space:nowrap'>".htmlspecialchars($c)."</th>";
  }
  echo "        <th>Total</th>
                </tr>
              </thead>
              <tbody>";
  if (empty($piv)) {
    echo "<tr><td colspan='".(count($cols)+2)."' class='text-muted'>Sin datos.</td></tr>";
  } else {
    foreach ($piv as $tech => $row) {
      $rowTotal = 0;
      echo "<tr><td>".htmlspecialchars($tech ?: '—')."</td>";
      foreach ($cols as $c) {
        $v = (int)($row[$c] ?? 0);
        $rowTotal += $v;
        echo "<td class='text-end'>".($v ?: '')."</td>";
      }
      echo "<td class='text-end fw-bold'>$rowTotal</td></tr>";
    }
  }
  echo "      </tbody>
            </table>
            </div>
          </div>
        </div>";
}
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Desempeño</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Reportes</li>
        <li class="breadcrumb-item active">Desempeño</li>
      </ol>
    </nav>
  </div>

  <section class="section">

    <!-- Filtros -->
    <form class="card shadow-sm mb-4 p-3">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-6">
          <label class="form-label">Intervalo rápido</label>
          <select name="quick" class="form-select">
            <option value="today" <?= $quick==='today'?'selected':''; ?>>Hoy</option>
            <option value="7d"    <?= $quick==='7d'?'selected':''; ?>>Últimos 7 días</option>
            <option value="30d"   <?= $quick==='30d'?'selected':''; ?>>Últimos 30 días</option>
            <option value="12m"   <?= $quick==='12m'?'selected':''; ?>>Últimos 12 meses</option>
            <option value="custom"<?= $quick==='custom'?'selected':''; ?>>Personalizado</option>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Desde</label>
          <input type="date" class="form-control" name="from" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Hasta</label>
          <input type="date" class="form-control" name="to" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-12">
          <button class="btn btn-primary"><i class="bi bi-funnel"></i> Aplicar</button>
        </div>
      </div>
      <div class="small text-muted mt-2">
        Rango aplicado: <strong><?= htmlspecialchars($from) ?></strong> a <strong><?= htmlspecialchars($to) ?></strong>
      </div>
    </form>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
      <?php
      $kpis = [
        ['title'=>'Registradas', 'value'=>$kpi_reg, 'icon'=>'bi-clipboard-plus'],
        ['title'=>'Preparadas',  'value'=>$kpi_pre, 'icon'=>'bi-hammer'],
        ['title'=>'Realizadas',  'value'=>$kpi_rea, 'icon'=>'bi-activity'],
        ['title'=>'Entregadas',  'value'=>$kpi_ent, 'icon'=>'bi-box-arrow-up'],
        ['title'=>'Digitadas',   'value'=>$kpi_dig, 'icon'=>'bi-keyboard'],
      ];
      foreach ($kpis as $k) {
        echo "<div class='col-6 col-md'>
                <div class='card shadow-sm h-100'>
                  <div class='card-body d-flex justify-content-between align-items-center'>
                    <div>
                      <div class='text-muted small'>{$k['title']}</div>
                      <div class='fs-3 fw-bold'>{$k['value']}</div>
                    </div>
                    <i class='bi {$k['icon']} fs-1 text-secondary'></i>
                  </div>
                </div>
              </div>";
      }
      ?>
    </div>

    <!-- Chart apilado por tipo -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light">
        <strong>Distribución por Tipo de Ensayo (apilado por etapa)</strong>
      </div>
      <div class="card-body">
        <div id="chartStacked" style="height: 430px;"></div>
      </div>
    </div>

    <!-- Tabs por etapa -->
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-registradas-tab" data-bs-toggle="pill" data-bs-target="#pills-registradas" type="button" role="tab">Registradas</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-preparadas-tab" data-bs-toggle="pill" data-bs-target="#pills-preparadas" type="button" role="tab">Preparadas</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-realizadas-tab" data-bs-toggle="pill" data-bs-target="#pills-realizadas" type="button" role="tab">Realizadas</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-entregadas-tab" data-bs-toggle="pill" data-bs-target="#pills-entregadas" type="button" role="tab">Entregadas</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-digitadas-tab" data-bs-toggle="pill" data-bs-target="#pills-digitadas" type="button" role="tab">Digitadas</button>
      </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
      <div class="tab-pane fade show active" id="pills-registradas" role="tabpanel">
        <?php render_pivot_table('Muestras Registradas (por Registrador × Tipo)', $piv_reg, $cols_reg); ?>
      </div>
      <div class="tab-pane fade" id="pills-preparadas" role="tabpanel">
        <?php render_pivot_table('Muestras Preparadas (por Técnico × Tipo)', $piv_pre, $cols_pre); ?>
      </div>
      <div class="tab-pane fade" id="pills-realizadas" role="tabpanel">
        <?php render_pivot_table('Muestras Realizadas (por Técnico × Tipo)', $piv_rea, $cols_rea); ?>
      </div>
      <div class="tab-pane fade" id="pills-entregadas" role="tabpanel">
        <?php render_pivot_table('Muestras Entregadas (por Técnico × Tipo)', $piv_ent, $cols_ent); ?>
      </div>
      <div class="tab-pane fade" id="pills-digitadas" role="tabpanel">
        <?php render_pivot_table('Ensayos Digitados (por Registrador × Tipo)', $piv_dig, $cols_dig); ?>
      </div>
    </div>

  </section>
</main>

<!-- ECharts CDN (si usas assets locales, apunta a tu archivo local) -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
(function(){
  const el = document.getElementById('chartStacked');
  if(!el) return;
  const chart = echarts.init(el);

  const categories = <?= json_encode($cat_types, JSON_UNESCAPED_UNICODE); ?>;

  const option = {
    tooltip: { trigger: 'axis' },
    legend: { top: 0 },
    grid: { left: 10, right: 10, bottom: 10, top: 40, containLabel: true },
    xAxis: { type: 'category', data: categories, axisLabel: { rotate: 30 } },
    yAxis: { type: 'value' },
    series: [
      { name: 'Registradas', type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: <?= json_encode($series_reg); ?> },
      { name: 'Preparadas',  type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: <?= json_encode($series_pre); ?> },
      { name: 'Realizadas',  type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: <?= json_encode($series_rea); ?> },
      { name: 'Entregadas',  type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: <?= json_encode($series_ent); ?> },
      { name: 'Digitadas',   type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: <?= json_encode($series_dig); ?> }
    ]
  };

  chart.setOption(option);
  window.addEventListener('resize', () => chart.resize());
})();
</script>

<?php include_once('../components/footer.php'); ?>
