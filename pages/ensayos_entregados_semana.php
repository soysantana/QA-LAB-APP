<?php
// pages/ensayos_entregados.php
declare(strict_types=1);

$page_title  = 'Ensayos entregados';
$menu_active = 'reportes';

require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

// =========================
// 1) Filtros: modo + año + (semana/mes/trimestre) + cliente/estructura/test
// =========================

// Valores actuales del sistema
$anioIsoActual  = (int)date('o'); // año ISO (para semana)
$semanaActual   = (int)date('W'); // semana ISO
$anioActual     = (int)date('Y'); // año calendario
$mesActual      = (int)date('n');
$trActual       = (int)floor(($mesActual - 1) / 3) + 1;

// Modo: semana | mes | trimestre | anio
$modo = isset($_GET['modo']) ? strtolower(trim((string)$_GET['modo'])) : 'semana';
if (!in_array($modo, ['semana', 'mes', 'trimestre', 'anio'], true)) {
    $modo = 'semana';
}

// Año base: para semana uso ISO (o), para los demás uso Y
if ($modo === 'semana') {
    $anio = isset($_GET['anio']) && $_GET['anio'] !== ''
        ? (int)$_GET['anio']
        : $anioIsoActual;
    $semana = isset($_GET['semana']) && $_GET['semana'] !== ''
        ? (int)$_GET['semana']
        : $semanaActual;
    // Para consistencia, también usamos $anioCalendario si se quiere mostrar algo, pero aquí basta el ISO
} else {
    $anio = isset($_GET['anio']) && $_GET['anio'] !== ''
        ? (int)$_GET['anio']
        : $anioActual;
    $semana = null;
}

// Mes y trimestre
$mes = isset($_GET['mes']) && $_GET['mes'] !== ''
    ? (int)$_GET['mes']
    : $mesActual;

$trimestre = isset($_GET['trimestre']) && $_GET['trimestre'] !== ''
    ? (int)$_GET['trimestre']
    : $trActual;

// Filtros por cliente, estructura, test_type
$cliente    = isset($_GET['cliente'])    ? trim((string)$_GET['cliente'])    : '';
$estructura = isset($_GET['estructura']) ? trim((string)$_GET['estructura']) : '';
$testType   = isset($_GET['test_type'])  ? trim((string)$_GET['test_type'])  : '';

// =========================
// 2) Combos (años, clientes, estructuras, tipos de ensayo)
// =========================

// Años disponibles según fechas de entrega
$years = find_by_sql("
  SELECT DISTINCT YEAR(Register_Date) AS y
  FROM test_delivery
  WHERE Register_Date IS NOT NULL
  ORDER BY y DESC
");

// Clientes desde la requisición
$clients = find_by_sql("
  SELECT DISTINCT Client
  FROM lab_test_requisition_form
  WHERE Client IS NOT NULL AND Client <> ''
  ORDER BY Client
");

// Estructuras desde la requisición
$structures = find_by_sql("
  SELECT DISTINCT Structure
  FROM lab_test_requisition_form
  WHERE Structure IS NOT NULL AND Structure <> ''
  ORDER BY Structure
");

// Tipos de ensayo desde test_delivery
$testTypes = find_by_sql("
  SELECT DISTINCT Test_Type
  FROM test_delivery
  WHERE Test_Type IS NOT NULL AND Test_Type <> ''
  ORDER BY Test_Type
");

// =========================
// 3) Calcular rango de fechas según modo
// =========================
$inicioPeriodo      = null;
$finPeriodo         = null;
$descripcionPeriodo = '';

try {
    if ($modo === 'semana') {
        // Semana ISO: año ISO + semana ISO
        if ($semana < 1 || $semana > 53) {
            $semana = $semanaActual;
        }
        $dt = new DateTime();
        $dt->setISODate($anio, $semana); // lunes
        $inicioPeriodo = $dt->format('Y-m-d');
        $dt->modify('+6 days');          // domingo
        $finPeriodo = $dt->format('Y-m-d');
        $descripcionPeriodo = sprintf('Semanal - Semana ISO %02d, Año ISO %04d', $semana, $anio);
    } elseif ($modo === 'mes') {
        if ($mes < 1 || $mes > 12) {
            $mes = $mesActual;
        }
        $dt = new DateTime(sprintf('%04d-%02d-01', $anio, $mes));
        $inicioPeriodo = $dt->format('Y-m-d');
        $dt->modify('last day of this month');
        $finPeriodo = $dt->format('Y-m-d');
        $descripcionPeriodo = sprintf('Mensual - %04d-%02d', $anio, $mes);
    } elseif ($modo === 'trimestre') {
        if ($trimestre < 1 || $trimestre > 4) {
            $trimestre = $trActual;
        }
        $mesInicio = ($trimestre - 1) * 3 + 1;
        $mesFin    = $mesInicio + 2;

        $dt = new DateTime(sprintf('%04d-%02d-01', $anio, $mesInicio));
        $inicioPeriodo = $dt->format('Y-m-d');

        $dtFin = new DateTime(sprintf('%04d-%02d-01', $anio, $mesFin));
        $dtFin->modify('last day of this month');
        $finPeriodo = $dtFin->format('Y-m-d');

        $descripcionPeriodo = sprintf('Trimestral - Q%d %04d (Mes %d a %d)', $trimestre, $anio, $mesInicio, $mesFin);
    } else {
        // Anual completo
        $inicioPeriodo = sprintf('%04d-01-01', $anio);
        $finPeriodo    = sprintf('%04d-12-31', $anio);
        $descripcionPeriodo = sprintf('Anual - %04d', $anio);
    }
} catch (Exception $e) {
    $inicioPeriodo      = "$anio-01-01";
    $finPeriodo         = "$anio-12-31";
    $descripcionPeriodo = "Anual (fallback) $anio";
}

// =========================
// 4) Construir WHERE dinámico
// =========================
$where = [];
$where[] = sprintf(
    "d.Register_Date BETWEEN '%s 00:00:00' AND '%s 23:59:59'",
    $db->escape($inicioPeriodo),
    $db->escape($finPeriodo)
);

if ($cliente !== '') {
    $where[] = "r.Client = '" . $db->escape($cliente) . "'";
}

if ($estructura !== '') {
    $where[] = "r.Structure = '" . $db->escape($estructura) . "'";
}

if ($testType !== '') {
    $where[] = "d.Test_Type = '" . $db->escape($testType) . "'";
}

$whereSql = implode(' AND ', $where);

// =========================
// 5) Consultas: detalle + resumen
// =========================

// a) Detalle
$detalle = find_by_sql("
  SELECT
    d.Sample_ID,
    d.Sample_Number,
    d.Test_Type,
    r.Client,
    r.Structure,
    d.Technician,
    d.Register_Date
  FROM test_delivery d
  LEFT JOIN lab_test_requisition_form r
    ON r.Sample_ID     = d.Sample_ID
   AND r.Sample_Number = d.Sample_Number
  WHERE $whereSql
  ORDER BY d.Register_Date ASC, d.Sample_ID, d.Sample_Number, d.Test_Type
");

// b) Resumen por día
$resumen_dia = find_by_sql("
  SELECT
    DATE(d.Register_Date) AS fecha,
    COUNT(*) AS total
  FROM test_delivery d
  LEFT JOIN lab_test_requisition_form r
    ON r.Sample_ID     = d.Sample_ID
   AND r.Sample_Number = d.Sample_Number
  WHERE $whereSql
  GROUP BY DATE(d.Register_Date)
  ORDER BY fecha
");

// c) Resumen por tipo de ensayo
$resumen_test = find_by_sql("
  SELECT
    d.Test_Type,
    COUNT(*) AS total
  FROM test_delivery d
  LEFT JOIN lab_test_requisition_form r
    ON r.Sample_ID     = d.Sample_ID
   AND r.Sample_Number = d.Sample_Number
  WHERE $whereSql
  GROUP BY d.Test_Type
  ORDER BY total DESC
");

// Total ensayos
$total_entregados = 0;
foreach ($resumen_dia as $r) {
    $total_entregados += (int)$r['total'];
}

// =========================
// 6) Vista HTML
// =========================
?>
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Ensayos entregados</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/pages/home.php">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/pages/reportes_varios.php">Reportes</a></li>
          <li class="breadcrumb-item active">Ensayos entregados</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section class="section dashboard">
    <div class="row g-3">

      <!-- =========================
           Filtros
           ========================= -->
      <div class="col-12">
        <div class="card">
          <div class="card-body pt-3">
            <form class="row g-3 align-items-end" method="get">

              <!-- Modo -->
              <div class="col-sm-4 col-md-3 col-lg-2">
                <label for="modo" class="form-label">Modo</label>
                <select name="modo" id="modo" class="form-select"
                        onchange="this.form.submit()">
                  <option value="semana"    <?= $modo === 'semana'    ? 'selected' : ''; ?>>Semanal (ISO)</option>
                  <option value="mes"       <?= $modo === 'mes'       ? 'selected' : ''; ?>>Mensual</option>
                  <option value="trimestre" <?= $modo === 'trimestre' ? 'selected' : ''; ?>>Trimestral</option>
                  <option value="anio"      <?= $modo === 'anio'      ? 'selected' : ''; ?>>Anual</option>
                </select>
              </div>

              <!-- Año -->
              <div class="col-sm-4 col-md-3 col-lg-2">
                <label for="anio" class="form-label">Año<?= $modo === 'semana' ? ' (ISO)' : ''; ?></label>
                <select name="anio" id="anio" class="form-select">
                  <?php foreach ($years as $y): ?>
                    <?php $yy = (int)$y['y']; ?>
                    <option value="<?= $yy; ?>" <?= $yy === $anio ? 'selected' : ''; ?>>
                      <?= $yy; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Semana (si modo = semana) -->
              <?php if ($modo === 'semana'): ?>
                <div class="col-sm-4 col-md-3 col-lg-2">
                  <label for="semana" class="form-label">Semana ISO</label>
                  <input type="number" min="1" max="53" name="semana" id="semana"
                         class="form-control"
                         value="<?= htmlspecialchars((string)$semana); ?>">
                </div>
              <?php endif; ?>

              <!-- Mes (si modo = mes) -->
              <?php if ($modo === 'mes'): ?>
                <div class="col-sm-4 col-md-3 col-lg-2">
                  <label for="mes" class="form-label">Mes</label>
                  <select name="mes" id="mes" class="form-select">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                      <option value="<?= $m; ?>" <?= $m === $mes ? 'selected' : ''; ?>>
                        <?= sprintf('%02d', $m); ?>
                      </option>
                    <?php endfor; ?>
                  </select>
                </div>
              <?php endif; ?>

              <!-- Trimestre (si modo = trimestre) -->
              <?php if ($modo === 'trimestre'): ?>
                <div class="col-sm-4 col-md-3 col-lg-2">
                  <label for="trimestre" class="form-label">Trimestre</label>
                  <select name="trimestre" id="trimestre" class="form-select">
                    <?php for ($t = 1; $t <= 4; $t++): ?>
                      <option value="<?= $t; ?>" <?= $t === $trimestre ? 'selected' : ''; ?>>
                        <?= 'Q' . $t; ?>
                      </option>
                    <?php endfor; ?>
                  </select>
                </div>
              <?php endif; ?>

              <!-- Cliente -->
              <div class="col-sm-6 col-md-4 col-lg-3">
                <label for="cliente" class="form-label">Cliente</label>
                <select name="cliente" id="cliente" class="form-select">
                  <option value="">Todos</option>
                  <?php foreach ($clients as $c): ?>
                    <?php $cName = (string)$c['Client']; ?>
                    <option value="<?= htmlspecialchars($cName); ?>"
                      <?= ($cliente === $cName) ? 'selected' : ''; ?>>
                      <?= htmlspecialchars($cName); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Estructura -->
              <div class="col-sm-6 col-md-4 col-lg-3">
                <label for="estructura" class="form-label">Estructura</label>
                <select name="estructura" id="estructura" class="form-select">
                  <option value="">Todas</option>
                  <?php foreach ($structures as $s): ?>
                    <?php $sName = (string)$s['Structure']; ?>
                    <option value="<?= htmlspecialchars($sName); ?>"
                      <?= ($estructura === $sName) ? 'selected' : ''; ?>>
                      <?= htmlspecialchars($sName); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Test Type -->
              <div class="col-sm-6 col-md-4 col-lg-2">
                <label for="test_type" class="form-label">Test Type</label>
                <select name="test_type" id="test_type" class="form-select">
                  <option value="">Todos</option>
                  <?php foreach ($testTypes as $tt): ?>
                    <?php $ttName = (string)$tt['Test_Type']; ?>
                    <option value="<?= htmlspecialchars($ttName); ?>"
                      <?= ($testType === $ttName) ? 'selected' : ''; ?>>
                      <?= htmlspecialchars($ttName); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Botón filtrar -->
              <div class="col-sm-6 col-md-4 col-lg-2">
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-search"></i> Filtrar
                </button>
              </div>

              <!-- Exportar PDF -->
              <div class="col-12 col-md-4 col-lg-3 ms-auto text-md-end">
                <?php
                $pdfQuery = http_build_query([
                  'modo'       => $modo,
                  'anio'       => $anio,
                  'semana'     => $modo === 'semana'    ? $semana    : null,
                  'mes'        => $modo === 'mes'       ? $mes       : null,
                  'trimestre'  => $modo === 'trimestre' ? $trimestre : null,
                  'cliente'    => $cliente,
                  'estructura' => $estructura,
                  'test_type'  => $testType,
                ]);
                ?>
                <a href="/pdf/ensayos_entregados_pdf.php?<?= $pdfQuery; ?>"
                   class="btn btn-outline-danger">
                  <i class="bi bi-file-pdf"></i> Exportar PDF
                </a>
              </div>

            </form>

            <div class="mt-3">
              <small class="text-muted">
                Periodo: <strong><?= htmlspecialchars($descripcionPeriodo); ?></strong><br>
                Rango: <strong><?= $inicioPeriodo; ?></strong> a <strong><?= $finPeriodo; ?></strong>
                <?php if ($cliente || $estructura || $testType): ?>
                  <br>Filtros aplicados:
                  <?php if ($cliente): ?>
                    <span class="badge bg-secondary">Cliente: <?= htmlspecialchars($cliente); ?></span>
                  <?php endif; ?>
                  <?php if ($estructura): ?>
                    <span class="badge bg-secondary">Estructura: <?= htmlspecialchars($estructura); ?></span>
                  <?php endif; ?>
                  <?php if ($testType): ?>
                    <span class="badge bg-secondary">Test: <?= htmlspecialchars($testType); ?></span>
                  <?php endif; ?>
                <?php endif; ?>
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- =========================
           Tarjetas resumen
           ========================= -->
      <div class="col-12 col-md-4">
        <div class="card info-card">
          <div class="card-body">
            <h5 class="card-title">Total ensayos entregados</h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-clipboard-check"></i>
              </div>
              <div class="ps-3">
                <h3><?= (int)$total_entregados; ?></h3>
                <span class="text-muted small"><?= htmlspecialchars($descripcionPeriodo); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="card info-card">
          <div class="card-body">
            <h5 class="card-title">Días con entregas</h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-calendar-event"></i>
              </div>
              <div class="ps-3">
                <h3><?= count($resumen_dia); ?></h3>
                <span class="text-muted small">dentro del periodo</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="card info-card">
          <div class="card-body">
            <h5 class="card-title">Tipos de ensayo</h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-diagram-3"></i>
              </div>
              <div class="ps-3">
                <h3><?= count($resumen_test); ?></h3>
                <span class="text-muted small">tipos distintos</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- =========================
           Gráfico: ensayos por día
           ========================= -->
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title mb-0">Ensayos entregados por día</h5>
            <small class="text-muted">
              <?= htmlspecialchars($descripcionPeriodo); ?> (<?= $inicioPeriodo; ?> a <?= $finPeriodo; ?>)
            </small>
            <div class="mt-3">
              <canvas id="chartEntregadosPeriodo" height="120"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- =========================
           Resumen por tipo de ensayo
           ========================= -->
      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Resumen por tipo de ensayo</h5>
            <div class="table-responsive">
              <table class="table table-sm table-striped align-middle">
                <thead>
                  <tr>
                    <th>Tipo de ensayo</th>
                    <th class="text-end">Cantidad</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($resumen_test)): ?>
                    <tr><td colspan="2" class="text-center text-muted">Sin registros en este periodo.</td></tr>
                  <?php else: ?>
                    <?php foreach ($resumen_test as $rt): ?>
                      <tr>
                        <td><?= htmlspecialchars((string)$rt['Test_Type']); ?></td>
                        <td class="text-end"><?= (int)$rt['total']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- =========================
           Detalle de ensayos entregados
           ========================= -->
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Detalle de ensayos entregados</h5>
            <div class="table-responsive">
              <table class="table table-sm table-striped table-hover align-middle">
                <thead>
                  <tr>
                    <th>Fecha entrega</th>
                    <th>Sample ID</th>
                    <th>Sample Number</th>
                    <th>Test Type</th>
                    <th>Cliente</th>
                    <th>Estructura</th>
                    <th>Técnico</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($detalle)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No hay ensayos entregados en este periodo.</td></tr>
                  <?php else: ?>
                    <?php foreach ($detalle as $row): ?>
                      <tr>
                        <td><?= htmlspecialchars(substr((string)$row['Register_Date'], 0, 16)); ?></td>
                        <td><?= htmlspecialchars((string)$row['Sample_ID']); ?></td>
                        <td><?= htmlspecialchars((string)$row['Sample_Number']); ?></td>
                        <td><?= htmlspecialchars((string)$row['Test_Type']); ?></td>
                        <td><?= htmlspecialchars((string)($row['Client'] ?? '')); ?></td>
                        <td><?= htmlspecialchars((string)($row['Structure'] ?? '')); ?></td>
                        <td><?= htmlspecialchars((string)$row['Technician']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div><!-- End row -->
  </section>
</main>

<?php include_once('../components/footer.php'); ?>

<!-- =============== Chart.js =============== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function() {
    const ctx = document.getElementById('chartEntregadosPeriodo');
    if (!ctx) return;

    const labels = [
      <?php foreach ($resumen_dia as $r): ?>
        "<?= date('d/m', strtotime($r['fecha'])); ?>",
      <?php endforeach; ?>
    ];
    const dataValores = [
      <?php foreach ($resumen_dia as $r): ?>
        <?= (int)$r['total']; ?>,
      <?php endforeach; ?>
    ];

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Ensayos entregados',
          data: dataValores,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { title: { display: true, text: 'Día' } },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Cantidad de ensayos' },
            ticks: { precision: 0 }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                return ' ' + ctx.parsed.y + ' ensayos';
              }
            }
          }
        }
      }
    });
  })();
</script>
