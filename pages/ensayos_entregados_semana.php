<?php
// pages/ensayos_entregados_semana.php
declare(strict_types=1);

$page_title  = 'Ensayos entregados por semana ISO';
$menu_active = 'reportes';

require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

// =========================
// 1) Filtros: año + semana
// =========================

// Año actual e ISO week actual por defecto
$anioActual    = (int)date('o'); // año ISO
$semanaActual  = (int)date('W'); // semana ISO

$anio   = isset($_GET['anio'])   && $_GET['anio']   !== '' ? (int)$_GET['anio']   : $anioActual;
$semana = isset($_GET['semana']) && $_GET['semana'] !== '' ? (int)$_GET['semana'] : $semanaActual;

// Lista de años disponibles según fechas de entrega
$years = find_by_sql("
  SELECT DISTINCT YEAR(Register_Date) AS y
  FROM test_delivery
  WHERE Register_Date IS NOT NULL
  ORDER BY y DESC
");

// =========================
// 2) Calcular rango de fecha de la semana ISO
// =========================
$inicioSemana = null;
$finSemana    = null;

try {
    $dt = new DateTime();
    // setISODate(año ISO, semana ISO)
    $dt->setISODate($anio, $semana);
    $inicioSemana = $dt->format('Y-m-d');
    $dt->modify('+6 days');
    $finSemana = $dt->format('Y-m-d');
} catch (Exception $e) {
    // Fallback por si algo raro pasa
    $inicioSemana = "$anio-01-01";
    $finSemana    = "$anio-01-07";
}

// =========================
// 3) Consultas: detalle + resumen
// =========================

// a) Detalle de ensayos entregados en la semana
//    Client viene de lab_test_requisition_form (JOIN por Sample_ID + Sample_Number)
$detalle = find_by_sql(sprintf("
  SELECT
    d.Sample_ID,
    d.Sample_Number,
    d.Test_Type,
    r.Client,
    d.Technician,
    d.Register_Date
  FROM test_delivery d
  LEFT JOIN lab_test_requisition_form r
    ON r.Sample_ID     = d.Sample_ID
   AND r.Sample_Number = d.Sample_Number
  WHERE d.Register_Date BETWEEN '%s 00:00:00' AND '%s 23:59:59'
  ORDER BY d.Register_Date ASC, d.Sample_ID, d.Sample_Number, d.Test_Type
", $db->escape($inicioSemana), $db->escape($finSemana)));

// b) Resumen por día: cantidad de ensayos entregados
$resumen_dia = find_by_sql(sprintf("
  SELECT
    DATE(d.Register_Date) AS fecha,
    COUNT(*) AS total
  FROM test_delivery d
  WHERE d.Register_Date BETWEEN '%s 00:00:00' AND '%s 23:59:59'
  GROUP BY DATE(d.Register_Date)
  ORDER BY fecha
", $db->escape($inicioSemana), $db->escape($finSemana)));

// c) Resumen por tipo de ensayo (en esa semana)
$resumen_test = find_by_sql(sprintf("
  SELECT
    d.Test_Type,
    COUNT(*) AS total
  FROM test_delivery d
  WHERE d.Register_Date BETWEEN '%s 00:00:00' AND '%s 23:59:59'
  GROUP BY d.Test_Type
  ORDER BY total DESC
", $db->escape($inicioSemana), $db->escape($finSemana)));

?>
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Ensayos entregados por semana ISO</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/pages/home.php">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/pages/reportes_varios.php">Reportes</a></li>
          <li class="breadcrumb-item active">Entregados por semana ISO</li>
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
              <div class="col-sm-4 col-md-3 col-lg-2">
                <label for="anio" class="form-label">Año (ISO)</label>
                <select name="anio" id="anio" class="form-select">
                  <?php foreach ($years as $y): ?>
                    <option value="<?= (int)$y['y']; ?>"
                      <?= ((int)$y['y'] === $anio) ? 'selected' : ''; ?>>
                      <?= (int)$y['y']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-sm-4 col-md-3 col-lg-2">
                <label for="semana" class="form-label">Semana ISO</label>
                <input type="number" min="1" max="53" name="semana" id="semana"
                       class="form-control"
                       value="<?= htmlspecialchars((string)$semana); ?>">
              </div>

              <div class="col-sm-4 col-md-3 col-lg-2">
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-search"></i> Filtrar
                </button>
              </div>

              <div class="col-sm-12 col-md-4 col-lg-3 ms-auto text-md-end">
                <!-- Botón Exportar PDF (endpoint a implementar) -->
                <a href="/pdf/ensayos_entregados_semana_pdf.php?anio=<?= $anio; ?>&semana=<?= $semana; ?>"
                   class="btn btn-outline-danger">
                  <i class="bi bi-file-pdf"></i> Exportar PDF
                </a>
              </div>
            </form>

            <div class="mt-3">
              <small class="text-muted">
                Semana ISO <strong><?= $semana; ?></strong> del año <strong><?= $anio; ?></strong>:
                desde <strong><?= $inicioSemana; ?></strong> hasta <strong><?= $finSemana; ?></strong>
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
                <h3>
                  <?php
                  $total_entregados = 0;
                  foreach ($resumen_dia as $r) {
                    $total_entregados += (int)$r['total'];
                  }
                  echo $total_entregados;
                  ?>
                </h3>
                <span class="text-muted small">en la semana ISO <?= $semana; ?></span>
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
                <span class="text-muted small">dentro de la semana</span>
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
                <span class="text-muted small">tipos de ensayo distintos</span>
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
            <h5 class="card-title mb-0">Ensayos entregados por día de la semana</h5>
            <small class="text-muted">Semana ISO <?= $semana; ?> (<?= $inicioSemana; ?> a <?= $finSemana; ?>)</small>
            <div class="mt-3">
              <canvas id="chartEntregadosSemana" height="120"></canvas>
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
                    <tr><td colspan="2" class="text-center text-muted">Sin registros en esta semana.</td></tr>
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
                    <th>Técnico</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($detalle)): ?>
                    <tr><td colspan="6" class="text-center text-muted">No hay ensayos entregados en esta semana.</td></tr>
                  <?php else: ?>
                    <?php foreach ($detalle as $row): ?>
                      <tr>
                        <td><?= htmlspecialchars(substr((string)$row['Register_Date'], 0, 16)); ?></td>
                        <td><?= htmlspecialchars((string)$row['Sample_ID']); ?></td>
                        <td><?= htmlspecialchars((string)$row['Sample_Number']); ?></td>
                        <td><?= htmlspecialchars((string)$row['Test_Type']); ?></td>
                        <td><?= htmlspecialchars((string)($row['Client'] ?? '')); ?></td>
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
    const ctx = document.getElementById('chartEntregadosSemana');
    if (!ctx) return;

    // Datos desde PHP -> JS
    const labels = [
      <?php foreach ($resumen_dia as $r): ?>
        "<?= date('D d/m', strtotime($r['fecha'])); ?>",
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
