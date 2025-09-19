<?php
$page_title = 'Control Ensayos de Concreto';
$control_concreto = 'active';
require_once('../config/load.php');

page_require_level(3);
include_once('../components/header.php');

// Fecha de hoy (solo fecha, sin hora)
$hoy = date('Y-m-d');

// Filtros
$sample_type = isset($_GET['tipo']) ? trim(strtolower($_GET['tipo'])) : '';
$fecha = isset($_GET['fecha']) ? trim($_GET['fecha']) : '';

// Construir condiciones dinámicas
$wheres = [];
$wheres[] = "LOWER(Test_Type) LIKE '%ucs%'";
$wheres[] = "LOWER(Sample_Type) IN ('cilindro','cubo')";
$wheres[] = "DATEDIFF(CURDATE(), Sample_Date) <= 30";

if ($sample_type !== '') {
  $wheres[] = "LOWER(Sample_Type) = '" . $db->real_escape_string($sample_type) . "'";
}
if ($fecha !== '') {
  // Filtro por día exacto de muestreo
  $wheres[] = "DATE(Sample_Date) = '" . $db->real_escape_string($fecha) . "'";
}

$condiciones = "WHERE " . implode(" AND ", $wheres);

// Query principal: castea fechas objetivo a DATE para comparar con $hoy
$sql = "
SELECT 
  Sample_ID, Sample_Number, Test_Type, Sample_Type, Sample_Date,
  DATE(DATE_ADD(Sample_Date, INTERVAL 3 DAY))  AS fecha_3,
  DATE(DATE_ADD(Sample_Date, INTERVAL 7 DAY))  AS fecha_7,
  DATE(DATE_ADD(Sample_Date, INTERVAL 14 DAY)) AS fecha_14,
  DATE(DATE_ADD(Sample_Date, INTERVAL 28 DAY)) AS fecha_28
FROM lab_test_requisition_form
$condiciones
ORDER BY Sample_Date DESC
";

$result = $db->query($sql);
if (!$result) {
  die("Error en consulta principal: " . $db->error);
}

// Obtener estados registrados con clave compuesta (sample_id + sample_number + dias)
$estados = find_by_sql("
  SELECT sample_id, sample_number, dias, estado
  FROM estado_ensayo_concreto
");

$marcados = [];
foreach ($estados as $e) {
  $key = $e['sample_id'] . '|' . $e['sample_number'];
  $marcados[$key][(int)$e['dias']] = strtolower(trim($e['estado'])); // normalizado
}
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Control de Fechas de Ensayos de Concreto</h1>
  </div>

  <section class="section">
    <div class="card">
      <div class="card-body pt-3">
        <!-- Filtros -->
        <form method="GET" class="row g-3 mb-3">
          <div class="col-md-3">
            <label>Tipo de Muestra</label>
            <select name="tipo" class="form-select">
              <option value="">Todos</option>
              <option value="cilindro" <?= ($sample_type === 'cilindro') ? 'selected' : '' ?>>Cilindro</option>
              <option value="cubo"     <?= ($sample_type === 'cubo') ? 'selected' : '' ?>>Cubo</option>
            </select>
          </div>
          <div class="col-md-3">
            <label>Fecha de Muestreo</label>
            <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" class="form-control">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
          </div>
          <div class="col-md-3 d-flex align-items-end justify-content-end">
            <!-- espacio para acciones futuras (exportar, etc.) -->
          </div>
        </form>

        <!-- Tabla -->
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle text-center table-sm">
            <thead class="table-dark">
              <tr>
                <th>Muestra</th>
                <th>Tipo</th>
                <th>Ensayo</th>
                <th>Fecha Muestreo</th>
                <th>3 días</th>
                <th>7 días</th>
                <th>14 días</th>
                <th>28 días</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                  $sampleId    = $row['Sample_ID'];
                  $sampleNum   = $row['Sample_Number'];
                  $testType    = $row['Test_Type'];
                  $sampleType  = $row['Sample_Type'];
                  $sampleDate  = $row['Sample_Date'];
                  $key = $sampleId . '|' . $sampleNum;
                ?>
                <tr>
                  <td>
                    <?= htmlspecialchars($sampleId) ?>
                    <br>
                    <small class="text-muted"><?= htmlspecialchars($sampleNum) ?></small>
                  </td>
                  <td><span class="badge bg-info"><?= htmlspecialchars(ucfirst($sampleType)) ?></span></td>
                  <td><?= htmlspecialchars($testType) ?></td>
                  <td><?= htmlspecialchars($sampleDate) ?></td>

                  <?php foreach ([3,7,14,28] as $d): 
                    $fecha_key = 'fecha_' . $d;
                    $fecha_val = $row[$fecha_key]; // ya viene como DATE (Y-m-d)
                    $estado_actual = $marcados[$key][$d] ?? null;
                    $hoy_es = ($fecha_val === $hoy);
                  ?>
                  <td class="<?= $hoy_es ? 'bg-warning' : ''; ?>">
                    <?= htmlspecialchars($fecha_val); ?><br>

                    <?php if ($estado_actual): ?>
                      <?php if ($estado_actual === 'realizado'): ?>
                        <span class="badge bg-success">✅ Realizado</span>
                      <?php elseif ($estado_actual === 'no solicitado'): ?>
                        <span class="badge bg-secondary">🚫 No solicitado</span>
                      <?php else: ?>
                        <span class="badge bg-light text-dark"><?= htmlspecialchars($estado_actual) ?></span>
                      <?php endif; ?>
                    <?php else: ?>
                      <div class="d-flex flex-column gap-1">
                        <form method="POST" action="../database/actualizar_estado_concreto.php">
                          <input type="hidden" name="sample_id" value="<?= htmlspecialchars($sampleId); ?>">
                          <input type="hidden" name="sample_number" value="<?= htmlspecialchars($sampleNum); ?>">
                          <input type="hidden" name="dias" value="<?= $d; ?>">
                          <input type="hidden" name="estado" value="Realizado">
                          <button type="submit" class="btn btn-sm btn-success">✅ Realizado</button>
                        </form>

                        <form method="POST" action="../database/actualizar_estado_concreto.php">
                          <input type="hidden" name="sample_id" value="<?= htmlspecialchars($sampleId); ?>">
                          <input type="hidden" name="sample_number" value="<?= htmlspecialchars($sampleNum); ?>">
                          <input type="hidden" name="dias" value="<?= $d; ?>">
                          <input type="hidden" name="estado" value="No solicitado">
                          <button type="submit" class="btn btn-sm btn-secondary">🚫 No solicitado</button>
                        </form>
                      </div>
                    <?php endif; ?>

                  </td>
                  <?php endforeach; ?>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
