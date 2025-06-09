<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

$reporte_diario = "active";
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
?>

<main id="main" class="main">
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-journal-text"></i> Reporte Diario del Laboratorio</h1>
    <?php if ($fecha): ?>
      <p>Muestras registradas desde: <strong><?= date('d-m-Y', strtotime("$fecha -7 days")) ?></strong> hasta <strong><?= date('d-m-Y', strtotime($fecha)) ?></strong></p>
    <?php else: ?>
      <p>Seleccione una fecha para generar el reporte.</p>
    <?php endif; ?>
  </div>

  <section class="section">
    <form method="GET" class="mb-4">
      <div class="row g-2">
        <div class="col-auto">
          <label for="fecha" class="form-label">Seleccionar fecha:</label>
          <input type="date" id="fecha" name="fecha" class="form-control" required value="<?= $fecha ?>">
        </div>
        <div class="col-auto align-self-end">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Generar Reporte</button>
        </div>
      </div>
    </form>

    <?php if ($fecha): ?>
      <?php
        $desde_semana = date('Y-m-d', strtotime("$fecha -7 days"));
        $hasta_dia = "$fecha 23:59:59";

        $query = "
        SELECT 
         
          r.Sample_Number,
          r.Project_Name,
          d.Register_Date AS delivery_date,
          p.Register_Date AS preparation_date,
          z.Register_Date AS realization_date,
          rep.Start_Date AS repeat_date,
          rv.Start_Date AS review_date,
          rd.Start_Date AS reviewed_date,
          IF(rd.Start_Date IS NOT NULL, 'Completado',
            IF(rv.Start_Date IS NOT NULL, 'Revisión Final',
            IF(rep.Start_Date IS NOT NULL, 'Repetido',
            IF(z.Register_Date IS NOT NULL, 'Realizado',
            IF(p.Register_Date IS NOT NULL, 'Preparado',
            IF(d.Register_Date IS NOT NULL, 'Entregado',
            IF(r.Registed_Date IS NOT NULL, 'Solicitado', NULL))))))) AS estado
        FROM lab_test_requisition_form r
        LEFT JOIN test_delivery d ON r.Sample_Number = d.Sample_Number
        LEFT JOIN test_preparation p ON r.Sample_Number = p.Sample_Number
        LEFT JOIN test_realization z ON r.Sample_Number = z.Sample_Number
        LEFT JOIN test_repeat rep ON r.Sample_Number = rep.Sample_Number
        LEFT JOIN test_review rv ON r.Sample_Number = rv.Sample_Number
        LEFT JOIN test_reviewed rd ON r.Sample_Number = rd.Sample_Number
        WHERE r.Registed_Date BETWEEN '$desde_semana' AND '$hasta_dia'
        ORDER BY r.Sample_Number;
        ";

        $results = $db->query($query);
      ?>

      <div class="card shadow-sm">
        <div class="card-body pt-4">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Nombre Muestra</th>
                  <th>Número</th>
                  <th>Proyecto</th>
                  <th>Estado</th>
                  <th>Último Registro</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; while ($row = $results->fetch_assoc()): ?>
                  <tr>
                    <td><?= $i++ ?></td>             
                    <td><?= $row['Sample_Number'] ?></td>
                    <td><?= $row['Project_Name'] ?></td>
                    <td><?= $row['estado'] ?: '-' ?></td>
                    <td>
                      <?= $row['reviewed_date'] ?: 
                          $row['review_date'] ?: 
                          $row['repeat_date'] ?: 
                          $row['realization_date'] ?: 
                          $row['preparation_date'] ?: 
                          $row['delivery_date'] ?: 
                          $row['Registed_Date'] ?: '-' ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
                <?php if ($results->num_rows == 0): ?>
                  <tr><td colspan="6" class="text-center">No se encontraron datos para esa semana.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
