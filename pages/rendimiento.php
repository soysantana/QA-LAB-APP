<?php
// rendimiento.php – Desempeño de Técnicos
$page_title   = 'Rendimiento de Técnicos';
$menu_active  = 'reportes';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

date_default_timezone_set('America/Santo_Domingo');

/* ===========================================================
   Helpers básicos
   =========================================================== */
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function v($key, $default = null) {
  return isset($_REQUEST[$key]) ? trim((string)$_REQUEST[$key]) : $default;
}

/* ===========================================================
   1) Filtros de fecha
   =========================================================== */
$quick = v('quick', '7d'); // today, 7d, 30d, 12m, custom
$from  = v('from');
$to    = v('to');

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
    if (!$from || !$to) { $from = $today; $to = $today; }
    break;
  case '7d':
  default:
    $from = $from ?: date('Y-m-d', strtotime('-7 days'));
    $to   = $to   ?: $today;
    break;
}

// Límites completos de día (incluyendo horas)
$from_dt = $db->escape($from . ' 00:00:00');
$to_dt   = $db->escape($to   . ' 23:59:59');

/* ===========================================================
   2) Cargar técnicos desde users (alias + nombre)
   =========================================================== */
$techUsers    = []; // [user_id] => ['name','alias','job']
$aliasToUser  = []; // [ALIAS]   => user_id

$resU = $db->query("
  SELECT id, name, alias, job
  FROM users
  WHERE alias IS NOT NULL
    AND TRIM(alias) <> ''
    AND job LIKE '%Technical%'
");
if ($resU) {
  while ($u = $resU->fetch_assoc()) {
    $uid   = (int)$u['id'];
    $alias = strtoupper(trim((string)$u['alias']));
    if ($alias === '') continue;
    $techUsers[$uid] = [
      'name'  => (string)$u['name'],
      'alias' => $alias,
      'job'   => (string)$u['job'],
    ];
    $aliasToUser[$alias] = $uid;
  }
}

/* ===========================================================
   3) Helpers para normalizar alias y estados
   =========================================================== */

/**
 * Normaliza una cadena de técnicos y devuelve lista de user_id
 * - Reemplaza varios separadores por coma
 * - Hace UPPER, TRIM
 * - Solo devuelve alias que existan en $aliasToUser
 */
function parse_aliases(string $raw, array $aliasToUser): array {
  $raw = trim($raw);
  if ($raw === '') return [];

  // Unificar separadores a coma
  $patterns = [
    '/\s+y\s+/iu',   // " y "
    '/\s+Y\s+/u',
    '/[\/\\\\;&\+\|]/u', // / \ & + |
  ];
  foreach ($patterns as $p) {
    $raw = preg_replace($p, ',', $raw);
  }

  // También reemplazar tab / salto de línea por coma
  $raw = str_replace(["\t","\r","\n"], ',', $raw);

  $parts = explode(',', $raw);
  $ids   = [];
  foreach ($parts as $p) {
    $a = strtoupper(trim($p));
    if ($a === '') continue;
    if (isset($aliasToUser[$a])) {
      $uid = $aliasToUser[$a];
      $ids[$uid] = true; // evitar duplicados
    }
  }
  return array_keys($ids); // lista de user_id únicos
}

/**
 * Mapea un Status de test_workflow / test_activity.To_Status
 * a una etapa "lógica" para el tablero.
 * Usamos:
 *  - Registro  : solo desde requisitions (para no duplicar)
 *  - Preparación, Realización, Entrega, Revisado: desde workflow
 */
function map_status_to_stage(?string $status): ?string {
  if ($status === null) return null;
  $s = trim($status);
  if ($s === '') return null;

  // Importante: NO devolvemos "Registro" para "Registrado"
  // porque esa ya la contamos desde lab_test_requisition_form.
  switch ($s) {
    case 'Preparación': return 'Preparación';
    case 'Realización': return 'Realización';
    case 'Entrega':     return 'Entrega';
    case 'Revisado':    return 'Revisado'; // si quieres medir control documental
    default:            return null;
  }
}

/* ===========================================================
   4) Estructuras de acumulación
   =========================================================== */
$STAGES = ['Registro','Preparación','Realización','Entrega','Revisado'];

$global   = []; // [user_id] => ['name','alias','job','totals'=>[stage=>n], 'total'=>n, 'by_test'=>[TT=>n]]
$byStage  = []; // [stage][user_id] => ['name','alias','job','total'=>n, 'by_test'=>[TT=>n]]
$stageTotals = array_fill_keys($STAGES, 0); // totales por etapa
$stageTestCols = []; // [stage] => set de tipos de ensayo

foreach ($STAGES as $st) {
  $byStage[$st]       = [];
  $stageTestCols[$st] = [];
}

/**
 * Garantiza que exista el slot de un usuario en estructuras globales.
 */
function ensure_user_slot(int $uid, array &$global, array &$byStage, array $techUsers, array $STAGES): void {
  if (!isset($techUsers[$uid])) return; // safety

  if (!isset($global[$uid])) {
    $global[$uid] = [
      'name'    => $techUsers[$uid]['name'],
      'alias'   => $techUsers[$uid]['alias'],
      'job'     => $techUsers[$uid]['job'],
      'totals'  => array_fill_keys($STAGES, 0),
      'total'   => 0,
      'by_test' => [],
    ];
  }
  foreach ($STAGES as $st) {
    if (!isset($byStage[$st][$uid])) {
      $byStage[$st][$uid] = [
        'name'    => $techUsers[$uid]['name'],
        'alias'   => $techUsers[$uid]['alias'],
        'job'     => $techUsers[$uid]['job'],
        'total'   => 0,
        'by_test' => [],
      ];
    }
  }
}

/**
 * Suma una "actividad" para un técnico en una etapa y tipo de ensayo.
 */
function add_event(
  int $uid,
  string $stage,
  string $testType,
  array &$global,
  array &$byStage,
  array &$stageTotals,
  array &$stageTestCols,
  array $techUsers,
  array $STAGES
): void {
  if (!isset($techUsers[$uid])) return;
  if (!in_array($stage, $STAGES, true)) return;

  $tt = strtoupper(trim($testType));
  if ($tt === '' || $tt === 'ENVIO') return; // ignorar ENVIO

  ensure_user_slot($uid, $global, $byStage, $techUsers, $STAGES);

  // Global
  $global[$uid]['totals'][$stage] += 1;
  $global[$uid]['total']          += 1;
  if (!isset($global[$uid]['by_test'][$tt])) {
    $global[$uid]['by_test'][$tt] = 0;
  }
  $global[$uid]['by_test'][$tt] += 1;

  // Por etapa
  $byStage[$stage][$uid]['total'] += 1;
  if (!isset($byStage[$stage][$uid]['by_test'][$tt])) {
    $byStage[$stage][$uid]['by_test'][$tt] = 0;
  }
  $byStage[$stage][$uid]['by_test'][$tt] += 1;

  // Totales de etapa
  $stageTotals[$stage] += 1;

  // Set de columnas de tipos de ensayo por etapa
  $stageTestCols[$stage][$tt] = true;
}

/* ===========================================================
   5)  FUENTE 1 – Registro desde lab_test_requisition_form
   =========================================================== */
$sqlReg = "
  SELECT
    Sample_ID,
    Sample_Number,
    Test_Type,
    Registed_Date,
    Register_By
  FROM lab_test_requisition_form
  WHERE Registed_Date IS NOT NULL
    AND Registed_Date BETWEEN '{$from_dt}' AND '{$to_dt}'
    AND Test_Type IS NOT NULL
    AND TRIM(Test_Type) <> ''
    AND Register_By IS NOT NULL
    AND TRIM(Register_By) <> ''
";
$resReg = $db->query($sqlReg);

if ($resReg) {
  while ($row = $resReg->fetch_assoc()) {
    $ttRaw   = (string)$row['Test_Type'];
    $regBy   = (string)$row['Register_By'];

    // Técnicos involucrados en el registro
    $uids = parse_aliases($regBy, $aliasToUser);
    if (empty($uids)) continue;

    // Tipos de ensayo (pueden venir en lista separada por coma)
    $ttNormalized = str_replace(' ', '', $ttRaw);
    $testTypes    = array_filter(array_map('trim', explode(',', $ttNormalized)));

    if (empty($testTypes)) continue;

    foreach ($testTypes as $tt) {
      $ttUp = strtoupper($tt);
      if ($ttUp === '' || $ttUp === 'ENVIO') continue;
      foreach ($uids as $uid) {
        add_event($uid, 'Registro', $ttUp, $global, $byStage, $stageTotals, $stageTestCols, $techUsers, $STAGES);
      }
    }
  }
}

/* ===========================================================
   6)  FUENTE 2 – Workflow: test_activity + test_activity_technician
   =========================================================== */
$sqlAct = "
  SELECT
    a.id           AS activity_id,
    a.test_id      AS test_id,
    a.From_Status  AS from_status,
    a.To_Status    AS to_status,
    a.Changed_At   AS process_at,
    a.Changed_By   AS updated_by,
    w.Sample_ID,
    w.Sample_Number,
    w.Test_Type,
    tat.Technician AS tech_alias
  FROM test_activity a
  JOIN test_workflow w
    ON w.id = a.test_id
  LEFT JOIN test_activity_technician tat
    ON tat.activity_id = a.id
  WHERE a.Changed_At IS NOT NULL
    AND a.Changed_At BETWEEN '{$from_dt}' AND '{$to_dt}'
    AND w.Test_Type IS NOT NULL
    AND TRIM(w.Test_Type) <> ''
";

$resAct = $db->query($sqlAct);
if ($resAct) {
  while ($row = $resAct->fetch_assoc()) {
    $stage = map_status_to_stage($row['to_status'] ?? null);
    if ($stage === null) continue; // ignorar estados que no mapeamos

    $tt = strtoupper(trim((string)$row['Test_Type']));
    if ($tt === '' || $tt === 'ENVIO') continue;

    // De dónde sacamos los alias:
    // 1) test_activity_technician.Technician
    // 2) Si está vacío, fallback a a.Updated_By
    $rawTech = '';
    if (!empty($row['tech_alias'])) {
      $rawTech = (string)$row['tech_alias'];
    } elseif (!empty($row['updated_by'])) {
      $rawTech = (string)$row['updated_by'];
    }

    if ($rawTech === '') continue;

    $uids = parse_aliases($rawTech, $aliasToUser);
    if (empty($uids)) continue;

    foreach ($uids as $uid) {
      add_event($uid, $stage, $tt, $global, $byStage, $stageTotals, $stageTestCols, $techUsers, $STAGES);
    }
  }
}

/* ===========================================================
   7) Preparar datos para ranking, chart y tablas
   =========================================================== */

// Ordenar técnicos por total global descendente
$sortedUsers = array_keys($global);
usort($sortedUsers, function($a, $b) use ($global) {
  $ta = $global[$a]['total'] ?? 0;
  $tb = $global[$b]['total'] ?? 0;
  if ($ta === $tb) {
    return strcmp($global[$a]['name'], $global[$b]['name']);
  }
  return $tb <=> $ta;
});

// Categorías y series para el gráfico (por alias)
$chartCategories = []; // alias en eje X
$chartSeriesData = []; // [stage] => [val,...] por categoria

foreach ($STAGES as $st) {
  $chartSeriesData[$st] = [];
}

foreach ($sortedUsers as $uid) {
  $chartCategories[] = $global[$uid]['alias'];
  foreach ($STAGES as $st) {
    $chartSeriesData[$st][] = (int)($global[$uid]['totals'][$st] ?? 0);
  }
}

// Preparar columnas por etapa (tipos de ensayo)
$stageCols = [];
foreach ($STAGES as $st) {
  $cols = array_keys($stageTestCols[$st]);
  sort($cols, SORT_NATURAL | SORT_FLAG_CASE);
  $stageCols[$st] = $cols;
}

/**
 * Helper para determinar estilo de badge según total
 */
function badge_for_total(int $n): string {
  if ($n >= 50) return 'bg-success';
  if ($n >= 20) return 'bg-primary';
  if ($n >= 10) return 'bg-warning text-dark';
  if ($n > 0)   return 'bg-secondary';
  return 'bg-light text-muted';
}

/**
 * Render de tabla pivot por etapa: filas = técnico, columnas = tipos de ensayo
 */
function render_stage_pivot(string $stageKey, string $stageLabel, array $byStage, array $stageCols): void {
  $rows = $byStage[$stageKey] ?? [];
  $cols = $stageCols[$stageKey] ?? [];

  echo "<div class='card shadow-sm mb-4'>
          <div class='card-header bg-light d-flex justify-content-between align-items-center'>
            <strong>".h($stageLabel)."</strong>
            <small class='text-muted'>Técnico × Tipo de ensayo</small>
          </div>
          <div class='card-body'>
            <div class='table-responsive'>
              <table class='table table-sm table-striped table-bordered align-middle mb-0'>
                <thead class='table-light'>
                  <tr>
                    <th style='white-space:nowrap'>Técnico</th>";
  foreach ($cols as $c) {
    echo "<th class='text-end' style='white-space:nowrap'><code>".h($c)."</code></th>";
  }
  echo "          <th class='text-end'>Total</th>
                  </tr>
                </thead>
                <tbody>";
  if (empty($rows)) {
    echo "<tr><td colspan='".(count($cols)+2)."' class='text-muted text-center py-3'>Sin datos para esta etapa en el rango seleccionado.</td></tr>";
  } else {
    foreach ($rows as $uid => $info) {
      $rowTotal = (int)($info['total'] ?? 0);
      echo "<tr>";
      echo "<td style='white-space:nowrap'>
              <div class='fw-semibold'>".h($info['name'])."</div>
              <div class='text-muted small'>".h($info['alias'])."</div>
            </td>";
      foreach ($cols as $c) {
        $v = (int)($info['by_test'][$c] ?? 0);
        echo "<td class='text-end'>".($v > 0 ? $v : '')."</td>";
      }
      echo "<td class='text-end fw-bold'>".$rowTotal."</td>";
      echo "</tr>";
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
  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1 class="mb-0">Rendimiento de Técnicos</h1>
      <small class="text-muted">Actividad por técnico, etapa y tipo de ensayo</small>
    </div>
    <div class="d-none d-md-block">
      <span class="badge bg-light text-dark border">
        Rango aplicado:
        <strong><?= h($from) ?></strong> a <strong><?= h($to) ?></strong>
      </span>
    </div>
  </div>

  <section class="section">

    <!-- Filtros -->
    <form class="card shadow-sm mb-4 p-3" method="get">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label form-label-sm">Intervalo rápido</label>
          <select name="quick" class="form-select form-select-sm">
            <option value="today"  <?= $quick==='today'?'selected':'';  ?>>Hoy</option>
            <option value="7d"     <?= $quick==='7d'?'selected':'';     ?>>Últimos 7 días</option>
            <option value="30d"    <?= $quick==='30d'?'selected':'';    ?>>Últimos 30 días</option>
            <option value="12m"    <?= $quick==='12m'?'selected':'';    ?>>Últimos 12 meses</option>
            <option value="custom" <?= $quick==='custom'?'selected':''; ?>>Personalizado</option>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label form-label-sm">Desde</label>
          <input type="date" class="form-control form-control-sm" name="from" value="<?= h($from) ?>">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label form-label-sm">Hasta</label>
          <input type="date" class="form-control form-control-sm" name="to" value="<?= h($to) ?>">
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
          <button class="btn btn-primary btn-sm flex-fill">
            <i class="bi bi-funnel"></i> Aplicar
          </button>
          <a href="rendimiento.php" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
            <i class="bi bi-x-circle"></i>
          </a>
        </div>
      </div>
      <div class="small text-muted mt-2">
        Solo se consideran usuarios con <code>job LIKE '%Technical%'</code> y alias definidos en la tabla <code>users</code>.
      </div>
    </form>

    <!-- KPIs por etapa -->
    <?php
      $kpiOrder = ['Registro','Preparación','Realización','Entrega','Revisado'];
      $icons = [
        'Registro'    => 'bi-clipboard-plus',
        'Preparación' => 'bi-gear-wide-connected',
        'Realización' => 'bi-activity',
        'Entrega'     => 'bi-box-arrow-up',
        'Revisado'    => 'bi-check2-square',
      ];
    ?>
    <div class="row g-3 mb-4">
      <?php foreach ($kpiOrder as $st): 
        $n = (int)($stageTotals[$st] ?? 0);
        $badgeClass = badge_for_total($n);
      ?>
        <div class="col-6 col-md">
          <div class="card shadow-sm kpi-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <div class="kpi-label"><?= h($st) ?></div>
                <div class="kpi-value"><?= $n ?></div>
              </div>
              <div class="d-flex flex-column align-items-end">
                <div class="kpi-icon-round">
                  <i class="bi <?= h($icons[$st] ?? 'bi-dot') ?>"></i>
                </div>
                <span class="badge mt-2 <?= $badgeClass ?>">
                  <?= $n ?> event<?= $n===1?'':'s' ?>
                </span>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Row: Chart + Ranking -->
    <div class="row g-3 mb-4">
      <div class="col-lg-7">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Distribución por etapa (por técnico)</strong>
            <small class="text-muted">Barras apiladas · eje X = alias del técnico</small>
          </div>
          <div class="card-body">
            <div id="chartStackedTech" style="height:420px;"></div>
            <?php if (empty($sortedUsers)): ?>
              <div class="text-muted small text-center mt-3">
                Sin datos en el rango seleccionado.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Ranking de Técnicos</strong>
            <small class="text-muted">Top por actividad total</small>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive ranking-table-wrap">
              <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Técnico</th>
                    <th class="text-end">Reg</th>
                    <th class="text-end">Prep</th>
                    <th class="text-end">Real</th>
                    <th class="text-end">Ent</th>
                    <th class="text-end">Rev</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($sortedUsers)): ?>
                    <tr>
                      <td colspan="8" class="text-center text-muted py-3">
                        No hay actividad registrada para técnicos en este rango de fechas.
                      </td>
                    </tr>
                  <?php else: 
                    $pos = 0;
                    foreach ($sortedUsers as $uid):
                      $pos++;
                      $info = $global[$uid];
                      $reg = (int)$info['totals']['Registro'];
                      $pre = (int)$info['totals']['Preparación'];
                      $rea = (int)$info['totals']['Realización'];
                      $ent = (int)$info['totals']['Entrega'];
                      $rev = (int)$info['totals']['Revisado'];
                      $tot = (int)$info['total'];
                      $badgeClass = badge_for_total($tot);
                  ?>
                    <tr>
                      <td class="text-muted small"><?= $pos ?></td>
                      <td>
                        <div class="fw-semibold"><?= h($info['name']) ?></div>
                        <div class="text-muted small"><?= h($info['alias']) ?></div>
                      </td>
                      <td class="text-end"><?= $reg ?: '' ?></td>
                      <td class="text-end"><?= $pre ?: '' ?></td>
                      <td class="text-end"><?= $rea ?: '' ?></td>
                      <td class="text-end"><?= $ent ?: '' ?></td>
                      <td class="text-end"><?= $rev ?: '' ?></td>
                      <td class="text-end">
                        <span class="badge <?= $badgeClass ?>"><?= $tot ?></span>
                      </td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer bg-light small text-muted">
            Cada actividad cuenta una vez por técnico y por tipo de ensayo.
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs por etapa con pivote Técnico × Tipo -->
    <ul class="nav nav-pills mb-3" id="pills-etapas" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-registro" data-bs-toggle="pill" data-bs-target="#pane-registro" type="button" role="tab">
          Registro
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-prep" data-bs-toggle="pill" data-bs-target="#pane-prep" type="button" role="tab">
          Preparación
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-real" data-bs-toggle="pill" data-bs-target="#pane-real" type="button" role="tab">
          Realización
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-ent" data-bs-toggle="pill" data-bs-target="#pane-ent" type="button" role="tab">
          Entrega
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-rev" data-bs-toggle="pill" data-bs-target="#pane-rev" type="button" role="tab">
          Revisado
        </button>
      </li>
    </ul>

    <div class="tab-content" id="pills-etapasContent">
      <div class="tab-pane fade show active" id="pane-registro" role="tabpanel">
        <?php render_stage_pivot('Registro', 'Registro de ensayos (lab_test_requisition_form.Register_By)', $byStage, $stageCols); ?>
      </div>
      <div class="tab-pane fade" id="pane-prep" role="tabpanel">
        <?php render_stage_pivot('Preparación', 'Preparación de ensayos (workflow)', $byStage, $stageCols); ?>
      </div>
      <div class="tab-pane fade" id="pane-real" role="tabpanel">
        <?php render_stage_pivot('Realización', 'Realización de ensayos (workflow)', $byStage, $stageCols); ?>
      </div>
      <div class="tab-pane fade" id="pane-ent" role="tabpanel">
        <?php render_stage_pivot('Entrega', 'Entrega de ensayos (workflow)', $byStage, $stageCols); ?>
      </div>
      <div class="tab-pane fade" id="pane-rev" role="tabpanel">
        <?php render_stage_pivot('Revisado', 'Ensayos revisados / digitados (workflow)', $byStage, $stageCols); ?>
      </div>
    </div>

  </section>
</main>

<!-- ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
(function() {
  const el = document.getElementById('chartStackedTech');
  if (!el) return;
  const chart = echarts.init(el);

  const categories = <?= json_encode($chartCategories, JSON_UNESCAPED_UNICODE); ?>;
  const dataReg    = <?= json_encode($chartSeriesData['Registro'] ?? [], JSON_NUMERIC_CHECK); ?>;
  const dataPre    = <?= json_encode($chartSeriesData['Preparación'] ?? [], JSON_NUMERIC_CHECK); ?>;
  const dataRea    = <?= json_encode($chartSeriesData['Realización'] ?? [], JSON_NUMERIC_CHECK); ?>;
  const dataEnt    = <?= json_encode($chartSeriesData['Entrega'] ?? [], JSON_NUMERIC_CHECK); ?>;
  const dataRev    = <?= json_encode($chartSeriesData['Revisado'] ?? [], JSON_NUMERIC_CHECK); ?>;

  const option = {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' }
    },
    legend: {
      top: 0
    },
    grid: {
      left: 10,
      right: 10,
      bottom: 10,
      top: 40,
      containLabel: true
    },
    xAxis: {
      type: 'category',
      data: categories,
      axisLabel: {
        rotate: 30,
        fontSize: 10
      }
    },
    yAxis: {
      type: 'value',
      name: 'Actividades'
    },
    series: [
      { name: 'Registro',    type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: dataReg },
      { name: 'Preparación', type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: dataPre },
      { name: 'Realización', type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: dataRea },
      { name: 'Entrega',     type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: dataEnt },
      { name: 'Revisado',    type: 'bar', stack: 'total', emphasis: { focus: 'series' }, data: dataRev }
    ]
  };

  chart.setOption(option);
  window.addEventListener('resize', () => chart.resize());
})();
</script>

<style>
  .kpi-card{
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    background:#ffffff;
    box-shadow:0 6px 18px rgba(15,23,42,0.06);
  }
  .kpi-label{
    font-size:0.75rem;
    text-transform:uppercase;
    letter-spacing:0.08em;
    color:#64748b;
    margin-bottom:0.15rem;
  }
  .kpi-value{
    font-size:1.8rem;
    font-weight:700;
    color:#0f172a;
    line-height:1;
  }
  .kpi-icon-round{
    width:36px;
    height:36px;
    border-radius:999px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#eff6ff;
    color:#1d4ed8;
  }
  .ranking-table-wrap{
    max-height:380px;
    overflow-y:auto;
  }
  .nav-pills .nav-link{
    border-radius:999px;
    padding:0.25rem 0.9rem;
    font-size:0.85rem;
  }
  .nav-pills .nav-link.active{
    background:#0f172a;
  }
</style>

<?php include_once('../components/footer.php'); ?>
