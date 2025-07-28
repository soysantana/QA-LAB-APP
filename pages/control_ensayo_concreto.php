<?php
$page_title = 'Control Ensayos de Concreto';
$control_concreto = 'active';
require_once('../config/load.php');
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php'); ?>

<?php
$hoy = date('Y-m-d');

// Filtros
$sample_type = $_GET['tipo'] ?? '';
$fecha = $_GET['fecha'] ?? '';

$condiciones = "WHERE LOWER(Test_Type) LIKE '%ucs%' 
  AND LOWER(Sample_Type) IN ('cilindro','cubo') 
  AND DATEDIFF(CURDATE(), Sample_Date) <= 30";


// Query principal
$sql = "
SELECT 
  Sample_ID, Sample_Number, Test_Type, Sample_Type, Sample_Date,
  DATE_ADD(Sample_Date, INTERVAL 3 DAY) AS fecha_3,
  DATE_ADD(Sample_Date, INTERVAL 7 DAY) AS fecha_7,
  DATE_ADD(Sample_Date, INTERVAL 14 DAY) AS fecha_14,
  DATE_ADD(Sample_Date, INTERVAL 28 DAY) AS fecha_28
FROM lab_test_requisition_form
$condiciones
ORDER BY Sample_Date DESC";
$result = $db->query($sql);

// Obtener estados registrados
$estados = find_by_sql("SELECT sample_id, dias, estado FROM estado_ensayo_concreto");
$marcados = [];
foreach ($estados as $e) {
  $marcados[$e['sample_id']][$e['dias']] = $e['estado'];
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
              <option value="cilindro" <?= $sample_type == 'cilindro' ? 'selected' : '' ?>>Cilindro</option>
              <option value="cubo" <?= $sample_type == 'cubo' ? 'selected' : '' ?>>Cubo</option>
            </select>
          </div>
          <div class="col-md-3">
            <label>Fecha de Muestreo</label>
            <input type="date" name="fecha" value="<?= $fecha ?>" class="form-control">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
          </div>
          <div class="col-md-3 d-flex align-items-end justify-content-end">
            <a ></a>
          </div>
        </form>

        <!-- Tabla -->
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
              <tr>
                <td><?= $row['Sample_ID'] . "<br><small class='text-muted'>" . $row['Sample_Number'] . "</small>"; ?></td>
                <td><span class="badge bg-info"><?= ucfirst($row['Sample_Type']); ?></span></td>
                <td><?= $row['Test_Type']; ?></td>
                <td><?= $row['Sample_Date']; ?></td>

                <?php foreach ([3,7,14,28] as $d): 
                  $fecha_key = 'fecha_' . $d;
                  $fecha_val = $row[$fecha_key];
                  $estado_actual = $marcados[$row['Sample_ID']][$d] ?? null;
                  $hoy_es = $fecha_val == $hoy;
                ?>
                <td class="<?= $hoy_es ? 'bg-warning' : ''; ?>">
                  <?= $fecha_val; ?><br>
                  <?php if ($estado_actual): ?>
                    <?php if ($estado_actual == 'Realizado'): ?>
                      <span class="badge bg-success">✅ Realizado</span>
                    <?php elseif ($estado_actual == 'No solicitado'): ?>
                      <span class="badge bg-secondary">🚫 No solicitado</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <div class="d-flex flex-column gap-1">
                     <form method="POST" action="../database/actualizar_estado_concreto.php">
  <input type="hidden" name="sample_id" value="<?= $row['Sample_ID']; ?>">
  <input type="hidden" name="sample_number" value="<?= $row['Sample_Number']; ?>">
  <input type="hidden" name="dias" value="<?= $d; ?>">
  <input type="hidden" name="estado" value="Realizado">
  <button type="submit" class="btn btn-sm btn-success">✅ Realizado</button>
</form>

<form method="POST" action="../database/actualizar_estado_concreto.php">
  <input type="hidden" name="sample_id" value="<?= $row['Sample_ID']; ?>">
  <input type="hidden" name="sample_number" value="<?= $row['Sample_Number']; ?>">
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
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
