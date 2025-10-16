<?php
$page_title = 'Solicitados vs Entregados por Cliente';
$menu_active = 'reportes';
require_once('../config/load.php');

page_require_level(2);
include_once('../components/header.php');

// =======================
// Filtros
// =======================
$anio    = isset($_GET['anio'])    ? trim($_GET['anio'])    : '';
$mes     = isset($_GET['mes'])     ? trim($_GET['mes'])     : '';
$cliente = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';

$years   = find_by_sql("SELECT DISTINCT YEAR(Sample_Date) AS y FROM lab_test_requisition_form ORDER BY y DESC");
$clients = find_by_sql("SELECT DISTINCT Client FROM lab_test_requisition_form ORDER BY Client");

// =======================
// Funciones auxiliares
// =======================
function sql_norm($col) {
  return "LOWER(REPLACE(REPLACE(TRIM($col),' ',''),'í','i'))";
}
$norm_e = sql_norm('e.Test_Type');
$norm_d = sql_norm('d.Test_Type');

function pctBadgeClass($pct) {
  if ($pct >= 90) return 'bg-success';
  if ($pct >= 70) return 'bg-warning text-dark';
  return 'bg-danger';
}
function monthName($m) {
  $names = [1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  return isset($names[(int)$m]) ? $names[(int)$m] : $m;
}

// =======================
// Subconsulta expandida (hasta 30 tokens)
// =======================
$numbers = "SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL
            SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL
            SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL
            SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL
            SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL
            SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL SELECT 30";

$expandedSubquery = "
  SELECT 
    r.Client,
    r.Sample_ID,
    r.Sample_Number,
    r.Sample_Date,
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
  JOIN ( $numbers ) n
    ON n.n <= 1 
      + LENGTH(REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','))
      - LENGTH(REPLACE(REPLACE(REPLACE(COALESCE(r.Test_Type,''), ' ', ''), ',,', ','), ',', ''))
";

// =======================
// WHERE unificado
// =======================
$whereExp = [];
if ($anio   !== '') $whereExp[] = "YEAR(e.Sample_Date) = '". $db->escape($anio) ."'";
if ($mes    !== '') $whereExp[] = "MONTH(e.Sample_Date) = '". (int)$mes ."'";
if ($cliente!== '') $whereExp[] = "e.Client = '". $db->escape($cliente) ."'";

$base = [
  "e.Test_Type IS NOT NULL",
  "e.Test_Type <> ''",
  $norm_e . " <> 'envio'"
];

$allWhere = 'WHERE ' . implode(' AND ', array_merge($base, $whereExp));

// =======================
// Query principal
// =======================
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
          AND " . $norm_d . " = " . $norm_e . "
      )
    ) AS entregados
  FROM (
    SELECT DISTINCT t.Client, t.Sample_ID, t.Sample_Number, t.Sample_Date, t.Test_Type
    FROM ( $expandedSubquery ) t
  ) e
  $allWhere
  GROUP BY e.Client, anio, mes
  ORDER BY anio DESC, mes DESC, e.Client
";

$res = $db->query($sql);
if (!$res) {
  error_log('Error en consulta principal: ' . $db->error);
  die('Error al cargar datos.');
}

// =======================
// KPIs
// =======================
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
            <div class="text-muted small">Ensayos solicitados</div>
            <div class="fs-3 fw-bold"><?= number_format($total_solic) ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="text-muted small">Ensayos entregados</div>
            <div class="fs-3 fw-bold"><?= number_format($total_entr) ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="text-muted small">Porcentaje de entrega</div>
            <div class="fs-3 fw-bold"><?= $pct_global ?>%</div>
            <div class="progress mt-2" style="height:10px;">
              <div class="progress-bar <?= pctBadgeClass($pct_global) ?>" style="width: <?= $pct_global ?>%;"></div>
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
              <?php foreach ($years as $y): $val=(int)$y['y']; ?>
              <option value="<?= $val ?>" <?= ($anio==$val?'selected':'') ?>><?= $val ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Mes</label>
            <select name="mes" class="form-select">
              <option value="">Todos</option>
              <?php for($m=1;$m<=12;$m++): ?>
              <option value="<?= $m ?>" <?= ($mes==$m?'selected':'') ?>>
                <?= str_pad($m,2,'0',STR_PAD_LEFT).' - '.monthName($m) ?>
              </option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cliente</label>
            <select name="cliente" class="form-select">
              <option value="">Todos</option>
              <?php foreach ($clients as $c): $cl=$c['Client']??''; ?>
              <option value="<?= htmlspecialchars($cl) ?>" <?= ($cliente===$cl?'selected':'') ?>>
                <?= htmlspecialchars($cl?:'(Sin cliente)') ?>
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
                <th class="text-center">% Entrega</th>
              </tr>
            </thead>
            <tbody>
<?php
if (empty($rows)) {
  echo "<tr><td colspan='7' class='text-center text-muted py-4'>Sin datos.</td></tr>";
} else {
  foreach ($rows as $r) {
    $pct   = $r['solicitados'] > 0 ? round($r['entregados'] / $r['solicitados'] * 100, 1) : 0;
    $badge = pctBadgeClass($pct);
    $pend  = max(0, (int)$r['pendientes']);
?>
<tr>
  <td><?= htmlspecialchars($r['Client']) ?></td>
  <td><?= $r['anio'] ?></td>
  <td><?= str_pad($r['mes'],2,'0',STR_PAD_LEFT).' - '.monthName($r['mes']) ?></td>
  <td class="text-end"><?= number_format($r['solicitados']) ?></td>
  <td class="text-end"><?= number_format($r['entregados']) ?></td>
  <td class="text-end"><?= number_format($pend) ?></td>
  <td class="text-center"><span class="badge <?= $badge ?>"><?= $pct ?>%</span></td>
</tr>
<?php } } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
