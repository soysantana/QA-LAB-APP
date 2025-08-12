<?php
$page_title = 'Solicitados vs Entregados por Cliente';
$menu_active = 'reportes';
require_once('../config/load.php');

page_require_level(2);
include_once('../components/header.php');

// ====== Filtros ======
$anio   = isset($_GET['anio'])   ? trim($_GET['anio'])   : '';
$mes    = isset($_GET['mes'])    ? trim($_GET['mes'])    : '';
$cliente= isset($_GET['cliente'])? trim($_GET['cliente']): '';

// Construir WHERE dinámico
$where = [];
if ($anio !== '')   { $where[] = "YEAR(r.Sample_Date) = '". $db->escape($anio) ."'"; }
if ($mes  !== '')   { $where[] = "MONTH(r.Sample_Date) = '". (int)$mes ."'"; }
if ($cliente !== ''){ $where[] = "r.Client = '". $db->escape($cliente) ."'"; }
$whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ====== Datos para combos (años, meses, clientes) ======
$years   = find_by_sql("SELECT DISTINCT YEAR(Sample_Date) AS y FROM lab_test_requisition_form ORDER BY y DESC");
$clients = find_by_sql("SELECT DISTINCT Client FROM lab_test_requisition_form ORDER BY Client");

// ====== Query principal (agrupado) ======
$sql = "
  SELECT 
    r.Client,
    YEAR(r.Sample_Date)  AS anio,
    MONTH(r.Sample_Date) AS mes,
    COUNT(*) AS solicitados,
    SUM(
      EXISTS (
        SELECT 1 
        FROM test_delivery d
        WHERE d.Sample_ID = r.Sample_ID
          AND d.Test_Type = r.Test_Type
      )
    ) AS entregados
  FROM lab_test_requisition_form r
  $whereSql
  GROUP BY r.Client, anio, mes
  ORDER BY anio DESC, mes DESC, r.Client
";
$res = $db->query($sql);
if (!$res) {
  die('Error en consulta: ' . $db->error);
}

// Totales para KPI
$total_solic = 0;
$total_entr  = 0;
$rows = [];
while ($row = $res->fetch_assoc()) {
  $rows[] = $row;
  $total_solic += (int)$row['solicitados'];
  $total_entr  += (int)$row['entregados'];
}
$pct_global = $total_solic > 0 ? round($total_entr / $total_solic * 100, 1) : 0.0;

// Función helpers
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
                <div class="text-muted small">Solicitados</div>
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
                <div class="text-muted small">Entregados</div>
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
              <?php foreach ($years as $y): 
                $val = (int)$y['y']; ?>
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
              <?php foreach ($clients as $c): 
                $cl = $c['Client'] ?? '';
                ?>
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
                <th style="width:220px;">% Entrega</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($rows)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Sin datos para los filtros seleccionados.</td></tr>
              <?php else: ?>
                <?php foreach ($rows as $r): 
                  $pct = $r['solicitados'] > 0 ? round($r['entregados'] / $r['solicitados'] * 100, 1) : 0;
                  $badge = pctBadgeClass($pct);
                ?>
                  <tr>
                    <td><span class="fw-semibold"><?= htmlspecialchars($r['Client'] ?: '(Sin cliente)') ?></span></td>
                    <td><?= (int)$r['anio'] ?></td>
                    <td><?= str_pad($r['mes'], 2, '0', STR_PAD_LEFT) . ' - ' . monthName($r['mes']) ?></td>
                    <td class="text-end"><?= number_format($r['solicitados']) ?></td>
                    <td class="text-end"><?= number_format($r['entregados']) ?></td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 10px;">
                          <div class="progress-bar <?= $badge ?>" role="progressbar" style="width: <?= (float)$pct ?>%;" aria-valuenow="<?= (float)$pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="badge <?= $badge ?>"><?= $pct ?>%</span>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Resumen inferior -->
        <div class="border-top pt-3 d-flex justify-content-end">
          <div class="text-end">
            <div class="small text-muted">Totales (según filtros)</div>
            <div class="fw-semibold">Solicitados: <?= number_format($total_solic) ?> | Entregados: <?= number_format($total_entr) ?> | %: <?= $pct_global ?>%</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
