<?php
$page_title = 'Solicitados vs Entregados por Cliente';
$menu_active = 'reportes';
require_once('../config/load.php');

page_require_level(2);
include_once('../components/header.php');

// ====== Filtros ======
$anio    = isset($_GET['anio'])    ? trim($_GET['anio'])    : '';
$mes     = isset($_GET['mes'])     ? trim($_GET['mes'])     : '';
$cliente = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';

// Combos
$years   = find_by_sql("SELECT DISTINCT YEAR(Sample_Date) AS y FROM lab_test_requisition_form ORDER BY y DESC");
$clients = find_by_sql("SELECT DISTINCT Client FROM lab_test_requisition_form ORDER BY Client");

// ====== WHERE dinámico para consultas sobre alias 'e' (expandidas) ======
$whereExp = [];
if ($anio   !== '') $whereExp[] = "YEAR(e.Sample_Date) = '". $db->escape($anio) ."'";
if ($mes    !== '') $whereExp[] = "MONTH(e.Sample_Date) = '". (int)$mes ."'";
if ($cliente!== '') $whereExp[] = "e.Client = '". $db->escape($cliente) ."'";
$whereSqlExp = count($whereExp) ? 'WHERE ' . implode(' AND ', $whereExp) : '';

// ====== Subconsulta (EXPANSIÓN por comas, compatible MySQL 5.7/8.0) ======
$expandedSubquery = "
  SELECT 
    t.Client,
    t.Sample_ID,
    t.Sample_Number,
    t.Sample_Date,
    t.Test_Type
  FROM (
    SELECT 
      r.Client,
      r.Sample_ID,
      r.Sample_Number,
      r.Sample_Date,
      -- Token n-ésimo, sin espacios ni comillas
      TRIM(BOTH '\"' FROM TRIM(
        SUBSTRING_INDEX(
          SUBSTRING_INDEX(
            REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','), ',', n.n
          ),
          ',', -1
        )
      )) AS Test_Type
    FROM lab_test_requisition_form r
    JOIN (
      SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
      UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
    ) n
      ON n.n <= 1 
           + LENGTH(REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','))
           - LENGTH(REPLACE(REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','), ',', ''))
  ) t
  WHERE t.Test_Type IS NOT NULL 
    AND t.Test_Type <> ''
    -- Normaliza acento y compara contra 'envio'
    AND LOWER(REPLACE(t.Test_Type,'í','i')) <> 'envio'
";


// ====== Query principal (AGRUPADO POR ENSAYO) ======
$sql = "
  SELECT
    e.Client,
    YEAR(e.Sample_Date)  AS anio,
    MONTH(e.Sample_Date) AS mes,

    COUNT(*) AS solicitados,

    -- Aparece en PREPARATION
    SUM(
      EXISTS (
        SELECT 1
        FROM test_preparation p
        WHERE p.Sample_ID     = e.Sample_ID
          AND p.Sample_Number = e.Sample_Number
          AND LOWER(REPLACE(TRIM(p.Test_Type),' ','')) 
              = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
      )
    ) AS en_preparacion,

    -- Aparece en REALIZATION
    SUM(
      EXISTS (
        SELECT 1
        FROM test_realization r
        WHERE r.Sample_ID     = e.Sample_ID
          AND r.Sample_Number = e.Sample_Number
          AND LOWER(REPLACE(TRIM(r.Test_Type),' ','')) 
              = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
      )
    ) AS en_realizacion,

    -- Aparece en DELIVERY
    SUM(
      EXISTS (
        SELECT 1
        FROM test_delivery d
        WHERE d.Sample_ID     = e.Sample_ID
          AND d.Sample_Number = e.Sample_Number
          AND LOWER(REPLACE(TRIM(d.Test_Type),' ','')) 
              = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
      )
    ) AS entregados,

    -- Aparece en cualquiera de las tres (para “atendidos”)
    SUM(
      (EXISTS (SELECT 1 FROM test_delivery    d WHERE d.Sample_ID=e.Sample_ID AND d.Sample_Number=e.Sample_Number AND LOWER(REPLACE(TRIM(d.Test_Type),' ',''))=LOWER(REPLACE(TRIM(e.Test_Type),' ',''))))
      OR
      (EXISTS (SELECT 1 FROM test_realization r WHERE r.Sample_ID=e.Sample_ID AND r.Sample_Number=e.Sample_Number AND LOWER(REPLACE(TRIM(r.Test_Type),' ',''))=LOWER(REPLACE(TRIM(e.Test_Type),' ',''))))
      OR
      (EXISTS (SELECT 1 FROM test_preparation p WHERE p.Sample_ID=e.Sample_ID AND p.Sample_Number=e.Sample_Number AND LOWER(REPLACE(TRIM(p.Test_Type),' ',''))=LOWER(REPLACE(TRIM(e.Test_Type),' ',''))))
    ) AS atendidos,

    -- *** NUEVO: Pendientes por entregar = (Prep OR Real OR Workflow) AND (NOT Delivery)
    SUM(
      (
        (EXISTS (
          SELECT 1
          FROM test_preparation p
          WHERE p.Sample_ID     = e.Sample_ID
            AND p.Sample_Number = e.Sample_Number
            AND LOWER(REPLACE(TRIM(p.Test_Type),' ','')) 
                = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
        ))
        OR
        (EXISTS (
          SELECT 1
          FROM test_realization r2
          WHERE r2.Sample_ID     = e.Sample_ID
            AND r2.Sample_Number = e.Sample_Number
            AND LOWER(REPLACE(TRIM(r2.Test_Type),' ','')) 
                = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
        ))
        OR
        (EXISTS (
          SELECT 1
          FROM test_workflow w
          WHERE w.Sample_ID     = e.Sample_ID
            AND w.Sample_Number = e.Sample_Number
            AND LOWER(REPLACE(TRIM(w.Test_Type),' ','')) 
                = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
        ))
      )
      AND
      NOT EXISTS (
        SELECT 1
        FROM test_delivery d2
        WHERE d2.Sample_ID     = e.Sample_ID
          AND d2.Sample_Number = e.Sample_Number
          AND LOWER(REPLACE(TRIM(d2.Test_Type),' ','')) 
              = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
      )
    ) AS pendientes_entrega

  FROM ( $expandedSubquery ) e
  $whereSqlExp

  GROUP BY e.Client, anio, mes
  ORDER BY anio DESC, mes DESC, e.Client
";


$res = $db->query($sql);
if (!$res) {
  die('Error en consulta principal: ' . $db->error);
}
// Totales KPI
$total_solic = 0;
$total_entr  = 0;
$rows = [];
while ($row = $res->fetch_assoc()) {
  // Usar el conteo "pendientes_entrega" calculado en SQL
  $row['pendientes'] = (int)$row['pendientes_entrega'];
  $rows[] = $row;
  $total_solic += (int)$row['solicitados'];
  $total_entr  += (int)$row['entregados'];
}
$pct_global = $total_solic > 0 ? round($total_entr / $total_solic * 100, 1) : 0.0;

// Helpers
function pctBadgeClass($pct) {
  if ($pct >= 90) return 'bg-success';
  if ($pct >= 70) return 'bg-warning text-dark';
  return 'bg-danger';
}
function monthName($m) {
  $names = [1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  return isset($names[(int)$m]) ? $names[(int)$m] : $m;
}
?>

<main id="main" class="main">
  <div class="pagetitle d-flex align-items-center justify-content-between">
    <div>
      <h1 class="mb-0">Solicitados vs Entregados por Cliente</h1>
      <small class="text-muted">Visión consolidada por cliente, año y mes</small>
    </div>
    <div class="text-end d-none d-md-block">
      <span class="badge bg-light text-dark border">
        <i class="bi bi-funnel me-1"></i>
        Filtros activos:
        <?= $anio ? "Año {$anio}" : 'Todos los años' ?> ·
        <?= $mes ? "Mes ".str_pad($mes,2,'0',STR_PAD_LEFT) : 'Todos los meses' ?> ·
        <?= $cliente ?: 'Todos los clientes' ?>
      </span>
    </div>
  </div>

  <!-- KPIs -->
  <section class="section">
    <div class="row g-3 mb-3 kpi-row">
      <!-- KPI 1: Solicitados -->
      <div class="col-md-4">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label">Ensayos solicitados</div>
              <div class="kpi-value"><?= number_format($total_solic) ?></div>
            </div>
            <div class="kpi-icon kpi-icon-in">
              <i class="bi bi-inbox"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Incluye todos los ensayos solicitados según los filtros seleccionados.
          </div>
        </div>
      </div>

      <!-- KPI 2: Entregados -->
      <div class="col-md-4">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label">Ensayos entregados</div>
              <div class="kpi-value"><?= number_format($total_entr) ?></div>
            </div>
            <div class="kpi-icon kpi-icon-out">
              <i class="bi bi-send-check"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Entregados en cualquier estado de delivery, de acuerdo a tu base de datos.
          </div>
        </div>
      </div>

      <!-- KPI 3: % Entrega Global -->
      <div class="col-md-4">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div class="w-100">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="kpi-label">Porcentaje de entrega</div>
                <div class="kpi-value-sm">
                  <?= $pct_global ?>%
                </div>
              </div>
              <div class="progress kpi-progress">
                <div class="progress-bar <?= pctBadgeClass($pct_global) ?>"
                     role="progressbar"
                     style="width: <?= (float)$pct_global ?>%;"
                     aria-valuenow="<?= (float)$pct_global ?>"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
              </div>
            </div>
            <div class="kpi-icon kpi-icon-pct">
              <i class="bi bi-activity"></i>
            </div>
          </div>
          <div class="kpi-subtext text-muted small">
            Ratio de ensayos entregados vs solicitados en el conjunto actual.
          </div>
        </div>
      </div>
    </div>
    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-3 mb-3">
      <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label form-label-sm">Año</label>
            <select name="anio" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach ($years as $y): $val = (int)$y['y']; ?>
                <option value="<?= $val ?>" <?= ($anio == $val ? 'selected' : '') ?>>
                  <?= $val ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label form-label-sm">Mes</label>
            <select name="mes" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php for ($m=1; $m<=12; $m++): ?>
                <option value="<?= $m ?>" <?= ($mes == (string)$m ? 'selected' : '') ?>>
                  <?= str_pad($m,2,'0',STR_PAD_LEFT) . ' - ' . monthName($m) ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label form-label-sm">Cliente</label>
            <select name="cliente" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach ($clients as $c): $cl = $c['Client'] ?? ''; ?>
                <option value="<?= htmlspecialchars($cl) ?>" <?= ($cliente === $cl ? 'selected' : '') ?>>
                  <?= htmlspecialchars($cl ?: '(Sin cliente)') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">
              <i class="bi bi-play-fill me-1"></i>Aplicar
            </button>
            <a href="?" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-x-circle"></i>
            </a>
          </div>
        </form>
      </div>
    </div>

    <!-- Vista por tarjetas (cards) -->
    <div class="card mt-3 border-0 shadow-sm">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0">Resumen por cliente / mes</h5>
          <small class="text-muted">
            Cada tarjeta representa una combinación Cliente + Año + Mes.
          </small>
        </div>
        <div class="d-none d-md-flex gap-2 align-items-center small">
          <span class="badge bg-success-subtle text-success border">≥ 90% OK</span>
          <span class="badge bg-warning-subtle text-warning border text-dark">70–89% Atención</span>
          <span class="badge bg-danger-subtle text-danger border">&lt; 70% Crítico</span>
        </div>
      </div>

      <div class="card-body pt-2 pb-3">
        <?php if (empty($rows)): ?>
          <div class="text-center text-muted py-5">
            <div class="empty-state">
              <div class="empty-icon">🤷‍♂️</div>
              <div class="empty-title">Sin datos para los filtros seleccionados</div>
              <div class="empty-subtitle">Ajusta el año, mes o cliente para visualizar resultados.</div>
            </div>
          </div>
        <?php else: ?>
          <div class="row g-3">
            <?php
            $idx = 0;
            foreach ($rows as $r):
              $pct   = $r['solicitados'] > 0 ? round($r['entregados'] / $r['solicitados'] * 100, 1) : 0;
              $badge = pctBadgeClass($pct);
              $pend  = max(0, (int)$r['pendientes']); // ya neto desde SQL
              $idx++;
              $modalId = 'detalles_' . $idx;

              // Filtros por fila
              $clienteRow = $db->escape($r['Client']);
              $anioRow    = (int)$r['anio'];
              $mesRow     = (int)$r['mes'];

              // Subquery expandida reutilizable
              $expanded = "
                SELECT 
                  r.Client,
                  r.Sample_ID,
                  r.Sample_Number,
                  r.Sample_Date,
                  TRIM(BOTH '\"' FROM TRIM(
                    SUBSTRING_INDEX(
                      SUBSTRING_INDEX(REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','), ',', n.n),
                      ',', -1
                    )
                  )) AS Test_Type
                FROM lab_test_requisition_form r
                JOIN (
                  SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                  UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                  UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
                  UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
                ) n
                  ON n.n <= 1 
                       + LENGTH(REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','))
                       - LENGTH(REPLACE(REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','), ',', ''))
                WHERE r.Client = '{$clienteRow}'
                  AND YEAR(r.Sample_Date) = {$anioRow}
                  AND MONTH(r.Sample_Date) = {$mesRow}
              ";

              // Solicitados (ensayos)
              $sqlSolic = "
                SELECT e.Sample_ID, e.Sample_Number, e.Test_Type, DATE(e.Sample_Date) AS fecha
                FROM ( $expanded ) e
                WHERE e.Test_Type IS NOT NULL 
                  AND e.Test_Type <> ''
                  AND LOWER(TRIM(e.Test_Type)) <> 'envio'
                ORDER BY e.Sample_Date DESC, e.Sample_ID
              ";

              $detSolic = [];
              $rs = $db->query($sqlSolic);
              if ($rs) {
                while ($d = $rs->fetch_assoc()) { $detSolic[] = $d; }
              } else {
                die('Error solicitados: ' . $db->error);
              }

              // Pendientes por entregar (detalle)
              $sqlPend = "
                SELECT e.Sample_ID, e.Sample_Number, e.Test_Type, DATE(e.Sample_Date) AS fecha
                FROM ( $expanded ) e
                LEFT JOIN test_delivery d
                  ON d.Sample_ID     = e.Sample_ID
                 AND d.Sample_Number = e.Sample_Number
                 AND LOWER(REPLACE(TRIM(d.Test_Type),' ','')) 
                     = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
                WHERE e.Test_Type IS NOT NULL 
                  AND e.Test_Type <> ''
                  AND LOWER(TRIM(e.Test_Type)) <> 'envio'
                  AND d.Sample_ID IS NULL
                  AND (
                        EXISTS (
                          SELECT 1 FROM test_preparation p
                          WHERE p.Sample_ID = e.Sample_ID
                            AND p.Sample_Number = e.Sample_Number
                            AND LOWER(REPLACE(TRIM(p.Test_Type),' ','')) 
                                = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
                        )
                        OR
                        EXISTS (
                          SELECT 1 FROM test_realization r2
                          WHERE r2.Sample_ID = e.Sample_ID
                            AND r2.Sample_Number = e.Sample_Number
                            AND LOWER(REPLACE(TRIM(r2.Test_Type),' ','')) 
                                = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
                        )
                        OR
                        EXISTS (
                          SELECT 1 FROM test_workflow w
                          WHERE w.Sample_ID = e.Sample_ID
                            AND w.Sample_Number = e.Sample_Number
                            AND LOWER(REPLACE(TRIM(w.Test_Type),' ','')) 
                                = LOWER(REPLACE(TRIM(e.Test_Type),' ','')) 
                        )
                      )
                ORDER BY e.Sample_Date DESC, e.Sample_ID
              ";

              $detPend = [];
              $rp = $db->query($sqlPend);
              if ($rp) {
                while ($p = $rp->fetch_assoc()) { $detPend[] = $p; }
              } else {
                die('Error pendientes: ' . $db->error);
              }
            ?>
              <div class="col-md-6 col-xl-4">
                <div class="client-card h-100">
                  <!-- Header -->
                  <div class="client-card-header">
                    <div>
                      <div class="client-name">
                        <?= htmlspecialchars($r['Client'] ?: '(Sin cliente)') ?>
                      </div>
                      <div class="client-period">
                        <?= (int)$r['anio'] ?> · <?= monthName($r['mes']) ?>
                      </div>
                    </div>
                    <div class="client-badge">
                      <span class="badge <?= $badge ?>"><?= $pct ?>%</span>
                    </div>
                  </div>

                  <!-- Body: métricas -->
                  <div class="client-card-body">
                    <div class="client-metrics">
                      <div class="metric">
                        <div class="metric-label">Solicitados</div>
                        <div class="metric-value"><?= number_format($r['solicitados']) ?></div>
                      </div>
                      <div class="metric">
                        <div class="metric-label">Entregados</div>
                        <div class="metric-value text-success"><?= number_format($r['entregados']) ?></div>
                      </div>
                      <div class="metric">
                        <div class="metric-label">Pendientes</div>
                        <div class="metric-value <?= $pend > 0 ? 'text-danger' : 'text-muted' ?>">
                          <?= number_format($pend) ?>
                        </div>
                      </div>
                    </div>

                    <!-- Progress -->
                    <div class="client-progress mt-2">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="client-progress-label text-muted">Nivel de entrega</span>
                        <span class="client-progress-val small"><?= $pct ?>%</span>
                      </div>
                      <div class="progress progress-thin">
                        <div class="progress-bar <?= $badge ?>"
                             role="progressbar"
                             style="width: <?= (float)$pct ?>%;"
                             aria-valuenow="<?= (float)$pct ?>"
                             aria-valuemin="0"
                             aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>

                  <!-- Footer: acciones -->
                  <div class="client-card-footer">
                    <div class="small text-muted">
                      <?= count($detSolic) ?> ensayos<br>
                      <?= count($detPend) ?> pendientes por entregar
                    </div>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary btn-ver-detalle"
                            data-bs-toggle="modal"
                            data-bs-target="#<?= $modalId ?>">
                      Ver detalle
                      <?php if ($pend > 0): ?>
                        <span class="badge bg-danger ms-1"><?= (int)$pend ?></span>
                      <?php endif; ?>
                    </button>
                  </div>
                </div>
              </div>
              <!-- Modal por tarjeta -->
              <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">
                        <?= htmlspecialchars($r['Client'] ?: '(Sin cliente)') ?>
                        <span class="text-muted fs-6">
                          · <?= (int)$r['anio'] ?>/<?= str_pad($r['mes'], 2, '0', STR_PAD_LEFT) ?>
                        </span>
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                      <ul class="nav nav-tabs" id="tab-<?= $modalId ?>" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link active"
                                  id="solic-<?= $modalId ?>-tab"
                                  data-bs-toggle="tab"
                                  data-bs-target="#solic-<?= $modalId ?>"
                                  type="button" role="tab"
                                  aria-controls="solic-<?= $modalId ?>" aria-selected="true">
                            Solicitados (<?= count($detSolic) ?>)
                          </button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link"
                                  id="pend-<?= $modalId ?>-tab"
                                  data-bs-toggle="tab"
                                  data-bs-target="#pend-<?= $modalId ?>"
                                  type="button" role="tab"
                                  aria-controls="pend-<?= $modalId ?>" aria-selected="false">
                            Pendientes por entregar (<?= count($detPend) ?>)
                          </button>
                        </li>
                      </ul>
                      <div class="tab-content pt-3">
                        <!-- Tab Solicitados -->
                        <div class="tab-pane fade show active"
                             id="solic-<?= $modalId ?>" role="tabpanel"
                             aria-labelledby="solic-<?= $modalId ?>-tab">
                          <?php if (empty($detSolic)): ?>
                            <div class="text-muted">No hay solicitados para esta combinación.</div>
                          <?php else: ?>
                            <div class="table-responsive">
                              <table class="table table-sm table-striped align-middle">
                                <thead>
                                  <tr>
                                    <th>Fecha</th>
                                    <th>Sample ID</th>
                                    <th>Sample Number</th>
                                    <th>Test Type</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php foreach ($detSolic as $d): ?>
                                    <tr>
                                      <td><?= htmlspecialchars($d['fecha']) ?></td>
                                      <td><?= htmlspecialchars($d['Sample_ID']) ?></td>
                                      <td><?= htmlspecialchars($d['Sample_Number']) ?></td>
                                      <td><code><?= htmlspecialchars($d['Test_Type']) ?></code></td>
                                    </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                          <?php endif; ?>
                        </div>

                        <!-- Tab Pendientes -->
                        <div class="tab-pane fade"
                             id="pend-<?= $modalId ?>" role="tabpanel"
                             aria-labelledby="pend-<?= $modalId ?>-tab">
                          <?php if (empty($detPend)): ?>
                            <div class="text-success">No hay pendientes por entregar. ✅</div>
                          <?php else: ?>
                            <div class="table-responsive">
                              <table class="table table-sm table-striped align-middle">
                                <thead>
                                  <tr>
                                    <th>Fecha</th>
                                    <th>Sample ID</th>
                                    <th>Sample Number</th>
                                    <th>Test Type</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php foreach ($detPend as $p): ?>
                                    <tr>
                                      <td><?= htmlspecialchars($p['fecha']) ?></td>
                                      <td><?= htmlspecialchars($p['Sample_ID']) ?></td>
                                      <td><?= htmlspecialchars($p['Sample_Number']) ?></td>
                                      <td><code><?= htmlspecialchars($p['Test_Type']) ?></code></td>
                                    </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /Modal -->
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Resumen inferior -->
      <div class="border-top px-3 py-3 d-flex justify-content-end bg-light-subtle">
        <div class="text-end small">
          <div class="text-muted">Totales según filtros</div>
          <div class="fw-semibold">
            Solicitados: <?= number_format($total_solic) ?> ·
            Entregados: <?= number_format($total_entr) ?> ·
            Pendientes: <?= number_format(array_sum(array_map(function($r){ return (int)$r['pendientes']; }, $rows))) ?> ·
            % Global: <?= $pct_global ?>%
          </div>
        </div>
      </div>
    </div> <!-- /card de tarjetas -->
  </section>
</main>
<?php include_once('../components/footer.php'); ?>

<script>
// Si la tarjeta tiene pendientes, al abrir el modal cambia a la pestaña "Pendientes"
document.addEventListener('shown.bs.modal', function (e) {
  const trigger = document.querySelector('[data-bs-target="#' + e.target.id + '"].btn-ver-detalle');
  if (!trigger) return;
  const badge = trigger.querySelector('.badge.bg-danger');
  if (badge && parseInt(badge.textContent, 10) > 0) {
    const pendTabBtn = e.target.querySelector('[id^="pend-"][id$="-tab"]');
    if (pendTabBtn) pendTabBtn.click();
  }
}, true);
</script>

<style>
  .client-card{
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    background:#ffffff;
    box-shadow:0 6px 18px rgba(15,23,42,0.06);
    display:flex;
    flex-direction:column;
    padding:0.8rem 0.9rem;
    gap:0.4rem;
  }
  .client-card-header{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:0.5rem;
  }
  .client-name{
    font-weight:600;
    font-size:0.95rem;
    color:#0f172a;
  }
  .client-period{
    font-size:0.78rem;
    color:#64748b;
  }
  .client-badge .badge{
    font-size:0.78rem;
  }
  .client-card-body{
    padding-top:0.2rem;
  }
  .client-metrics{
    display:flex;
    justify-content:space-between;
    gap:0.4rem;
  }
  .metric{
    flex:1;
  }
  .metric-label{
    font-size:0.7rem;
    color:#94a3b8;
    text-transform:uppercase;
    letter-spacing:0.06em;
  }
  .metric-value{
    font-size:0.95rem;
    font-weight:600;
  }
  .client-progress-label{
    font-size:0.75rem;
  }
  .client-progress-val{
    font-size:0.8rem;
    font-weight:600;
  }
  .progress-thin{
    height:0.45rem;
    border-radius:999px;
    overflow:hidden;
  }
  .client-card-footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding-top:0.3rem;
    border-top:1px dashed #e2e8f0;
    margin-top:0.3rem;
  }

  .empty-state{
    max-width:360px;
    margin:0 auto;
  }
  .empty-icon{
    font-size:1.8rem;
    margin-bottom:0.25rem;
  }
  .empty-title{
    font-weight:600;
    font-size:0.95rem;
  }
  .empty-subtitle{
    font-size:0.8rem;
    color:#64748b;
  }

  /* KPI cards (para que todo mantenga el estilo) */
  .kpi-row .kpi-card{
    border-radius: 14px;
    padding: 0.9rem 1rem;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 18px rgba(15,23,42,0.06);
  }
  .kpi-card-main{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:0.75rem;
  }
  .kpi-label{
    font-size:0.78rem;
    text-transform:uppercase;
    letter-spacing:0.06em;
    color:#64748b;
    margin-bottom:0.15rem;
  }
  .kpi-value{
    font-size:1.7rem;
    font-weight:700;
    color:#0f172a;
    line-height:1.1;
  }
  .kpi-value-sm{
    font-size:1.25rem;
    font-weight:600;
    color:#0f172a;
  }
  .kpi-subtext{
    margin-top:0.35rem;
  }
  .kpi-icon{
    width:44px;
    height:44px;
    border-radius:999px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.4rem;
  }
  .kpi-icon-in{
    background:rgba(59,130,246,0.08);
    color:#1d4ed8;
  }
  .kpi-icon-out{
    background:rgba(16,185,129,0.08);
    color:#059669;
  }
  .kpi-icon-pct{
    background:rgba(234,179,8,0.08);
    color:#b45309;
  }
  .kpi-progress{
    height:10px;
    border-radius:999px;
    overflow:hidden;
  }
</style>
