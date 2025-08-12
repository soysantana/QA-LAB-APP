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
    r.Client,
    r.Sample_ID,
    r.Sample_Number,
    r.Sample_Date,
    -- Tomamos el token n-ésimo al partir por coma, quitando espacios/comillas
    TRIM(BOTH '\"' FROM TRIM(
      SUBSTRING_INDEX(
        SUBSTRING_INDEX(
          REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','), 
          ',', n.n
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
";

// ====== Query principal (AGRUPADO POR ENSAYO) ======
$sql = "
  SELECT
    e.Client,
    YEAR(e.Sample_Date)  AS anio,
    MONTH(e.Sample_Date) AS mes,
    COUNT(*) AS solicitados,
    SUM(
      EXISTS (
        SELECT 1
        FROM test_delivery d
        WHERE d.Sample_ID     = e.Sample_ID
          AND d.Sample_Number = e.Sample_Number
          AND LOWER(REPLACE(TRIM(d.Test_Type),' ',''))
              = LOWER(REPLACE(TRIM(e.Test_Type),' ',''))
      )
    ) AS entregados
  FROM ( $expandedSubquery ) e
  $whereSqlExp
  WHERE e.Test_Type IS NOT NULL AND e.Test_Type <> ''
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
  $row['pendientes'] = (int)$row['solicitados'] - (int)$row['entregados'];
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
    <h1>Solicitados vs Entregados por Cliente</h1>
  </div>

  <!-- KPIs -->
  <section class="section">
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-muted small">Ensayos solicitados</div>
                <div class="fs-3 fw-bold"><?= number_format($total_solic) ?></div>
              </div>
              <div class="display-6">📥</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-muted small">Ensayos entregados</div>
                <div class="fs-3 fw-bold"><?= number_format($total_entr) ?></div>
              </div>
              <div class="display-6">📤</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="w-100">
                <div class="d-flex justify-content-between">
                  <div class="text-muted small">Porcentaje de Entrega</div>
                  <div class="fw-semibold"><?= $pct_global ?>%</div>
                </div>
                <div class="progress mt-2" style="height:10px;">
                  <div class="progress-bar <?= pctBadgeClass($pct_global) ?>" role="progressbar" style="width: <?= (float)$pct_global ?>%;" aria-valuenow="<?= (float)$pct_global ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="ms-3 display-6">📈</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros -->  
    <div class="card mt-3">
      <div class="card-body">
        <form method="GET" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Año</label>
            <select name="anio" class="form-select">
              <option value="">Todos</option>
              <?php foreach ($years as $y): $val = (int)$y['y']; ?>
                <option value="<?= $val ?>" <?= ($anio == $val ? 'selected' : '') ?>><?= $val ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Mes</label>
            <select name="mes" class="form-select">
              <option value="">Todos</option>
              <?php for ($m=1; $m<=12; $m++): ?>
                <option value="<?= $m ?>" <?= ($mes == (string)$m ? 'selected' : '') ?>><?= str_pad($m,2,'0',STR_PAD_LEFT) . ' - ' . monthName($m) ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cliente</label>
            <select name="cliente" class="form-select">
              <option value="">Todos</option>
              <?php foreach ($clients as $c): $cl = $c['Client'] ?? ''; ?>
                <option value="<?= htmlspecialchars($cl) ?>" <?= ($cliente === $cl ? 'selected' : '') ?>>
                  <?= htmlspecialchars($cl ?: '(Sin cliente)') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">Aplicar</button>
            <a href="? " class="btn btn-outline-secondary">Limpiar</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabla -->
    <div class="card mt-3">
      <div class="card-body pt-3">
        <div class="table-responsive">
          <table class="table table-hover align-middle table-sm">
            <thead class="table-dark position-sticky top-0">
              <tr>
                <th>Cliente</th>
                <th>Año</th>
                <th>Mes</th>
                <th class="text-end">Solicitados</th>
                <th class="text-end">Entregados</th>
                <th class="text-end">Pendientes</th>
                <th style="width:220px;">% Entrega</th>
                <th class="text-center" style="width:140px;">Detalle</th>
              </tr>
            </thead>
            <tbody>
<?php
if (empty($rows)) {
  echo "<tr><td colspan='8' class='text-center text-muted py-4'>Sin datos para los filtros seleccionados.</td></tr>";
} else {
  $idx = 0;
  foreach ($rows as $r) {
    $pct   = $r['solicitados'] > 0 ? round($r['entregados'] / $r['solicitados'] * 100, 1) : 0;
    $badge = pctBadgeClass($pct);
    $pend  = max(0, (int)$r['pendientes']);
    $idx++;
    $modalId = 'detalles_' . $idx;

    // Filtros por fila
    $clienteRow = $db->escape($r['Client']);
    $anioRow    = (int)$r['anio'];
    $mesRow     = (int)$r['mes'];

    // Subquery expanded (reutilizable)
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

    // === SOLICITADOS (expandido a ensayos) ===
    $sqlSolic = "
      SELECT e.Sample_ID, e.Sample_Number, e.Test_Type, DATE(e.Sample_Date) AS fecha
      FROM ( $expanded ) e
      WHERE e.Test_Type IS NOT NULL AND e.Test_Type <> ''
      ORDER BY e.Sample_Date DESC, e.Sample_ID
    ";
    $detSolic = [];
    $rs = $db->query($sqlSolic);
    if ($rs) {
      while ($d = $rs->fetch_assoc()) { $detSolic[] = $d; }
    } else {
      die('Error solicitados: ' . $db->error);
    }

    // === PENDIENTES (solicitados SIN entrega) ===
    $sqlPend = "
      SELECT e.Sample_ID, e.Sample_Number, e.Test_Type, DATE(e.Sample_Date) AS fecha
      FROM ( $expanded ) e
      LEFT JOIN test_delivery d
        ON d.Sample_ID     = e.Sample_ID
       AND d.Sample_Number = e.Sample_Number
       AND LOWER(REPLACE(TRIM(d.Test_Type),' ',''))
           = LOWER(REPLACE(TRIM(e.Test_Type),' ',''))
      WHERE e.Test_Type IS NOT NULL AND e.Test_Type <> ''
        AND d.Sample_ID IS NULL
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
              <tr>
                <td><span class="fw-semibold"><?= htmlspecialchars($r['Client'] ?: '(Sin cliente)') ?></span></td>
                <td><?= (int)$r['anio'] ?></td>
                <td><?= str_pad($r['mes'], 2, '0', STR_PAD_LEFT) . ' - ' . monthName($r['mes']) ?></td>
                <td class="text-end"><?= number_format($r['solicitados']) ?></td>
                <td class="text-end"><?= number_format($r['entregados']) ?></td>
                <td class="text-end"><?= number_format($pend) ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 10px;">
                      <div class="progress-bar <?= $badge ?>" role="progressbar" style="width: <?= (float)$pct ?>%;" aria-valuenow="<?= (float)$pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="badge <?= $badge ?>"><?= $pct ?>%</span>
                  </div>
                </td>
                <td class="text-center">
                  <button type="button"
                          class="btn btn-sm btn-outline-primary btn-ver-detalle"
                          data-bs-toggle="modal"
                          data-bs-target="#<?= $modalId ?>">
                    Ver <?php if ($pend > 0): ?><span class="badge bg-danger ms-1"><?= (int)$pend ?></span><?php endif; ?>
                  </button>
                </td>
              </tr>

              <!-- Modal por fila -->
              <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">
                        <?= htmlspecialchars($r['Client'] ?: '(Sin cliente)') ?> — <?= (int)$r['anio'] ?>/<?= str_pad($r['mes'], 2, '0', STR_PAD_LEFT) ?>
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                      <ul class="nav nav-tabs" id="tab-<?= $modalId ?>" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link active" id="solic-<?= $modalId ?>-tab" data-bs-toggle="tab" data-bs-target="#solic-<?= $modalId ?>" type="button" role="tab" aria-controls="solic-<?= $modalId ?>" aria-selected="true">
                            Solicitados (<?= count($detSolic) ?>)
                          </button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" id="pend-<?= $modalId ?>-tab" data-bs-toggle="tab" data-bs-target="#pend-<?= $modalId ?>" type="button" role="tab" aria-controls="pend-<?= $modalId ?>" aria-selected="false">
                            Pendientes por entregar (<?= count($detPend) ?>)
                          </button>
                        </li>
                      </ul>
                      <div class="tab-content pt-3">
                        <!-- Tab Solicitados -->
                        <div class="tab-pane fade show active" id="solic-<?= $modalId ?>" role="tabpanel" aria-labelledby="solic-<?= $modalId ?>-tab">
                          <?php if (empty($detSolic)) { ?>
                            <div class="text-muted">No hay solicitados para esta combinación.</div>
                          <?php } else { ?>
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
                                  <?php foreach ($detSolic as $d) { ?>
                                    <tr>
                                      <td><?= htmlspecialchars($d['fecha']) ?></td>
                                      <td><?= htmlspecialchars($d['Sample_ID']) ?></td>
                                      <td><?= htmlspecialchars($d['Sample_Number']) ?></td>
                                      <td><?= htmlspecialchars($d['Test_Type']) ?></td>
                                    </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          <?php } ?>
                        </div>
                        <!-- Tab Pendientes -->
                        <div class="tab-pane fade" id="pend-<?= $modalId ?>" role="tabpanel" aria-labelledby="pend-<?= $modalId ?>-tab">
                          <?php if (empty($detPend)) { ?>
                            <div class="text-success">No hay pendientes por entregar. ✅</div>
                          <?php } else { ?>
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
                                  <?php foreach ($detPend as $p) { ?>
                                    <tr>
                                      <td><?= htmlspecialchars($p['fecha']) ?></td>
                                      <td><?= htmlspecialchars($p['Sample_ID']) ?></td>
                                      <td><?= htmlspecialchars($p['Sample_Number']) ?></td>
                                      <td><?= htmlspecialchars($p['Test_Type']) ?></td>
                                    </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /Modal -->
<?php
  } // end foreach
} // end if/else
?>
            </tbody>
          </table>
        </div>

        <!-- Resumen inferior -->
        <div class="border-top pt-3 d-flex justify-content-end">
          <div class="text-end">
            <div class="small text-muted">Totales (según filtros)</div>
            <div class="fw-semibold">
              Solicitados: <?= number_format($total_solic) ?> | 
              Entregados: <?= number_format($total_entr) ?> | 
              Pendientes: <?= number_format(max(0, $total_solic - $total_entr)) ?> | 
              %: <?= $pct_global ?>%
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
<script>
// Si la fila tiene pendientes, al abrir el modal cambia a la pestaña "Pendientes"
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
