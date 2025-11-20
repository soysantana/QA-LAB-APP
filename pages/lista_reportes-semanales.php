<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');


$reporte_semanal = "active";

// Año seleccionado
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

// Semanas totales del año
$total_semanas = 52;

// Si el año tiene 53 semanas ISO
$ultima_semana = (int) date("W", strtotime("$anio-12-28"));
if ($ultima_semana == 53) {
    $total_semanas = 53;
}
?>

<main id="main" class="main"> 
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-calendar3"></i> Reportes Semanales del Año <?= $anio ?></h1>
    <p>Seleccione una semana para generar el PDF o editar el contenido</p>
  </div>

  <!-- Selector de Año -->
  <div class="mb-3">
    <form method="GET" class="d-flex gap-2">
      <select name="anio" class="form-select" style="max-width:180px;">
        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
          <option value="<?= $y ?>" <?= ($y == $anio ? 'selected' : '') ?>>
            <?= $y ?>
          </option>
        <?php endfor; ?>
      </select>
      <button class="btn btn-dark">Filtrar Año</button>
    </form>
  </div>

  <section class="section">
    <div class="card shadow-sm">
      <div class="card-body pt-4">
        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Semana ISO</th>
              <th>Rango (Lunes - Domingo)</th>
              <th>Acción</th>
            </tr>
          </thead>

          <tbody>
            <?php for ($semana = 1; $semana <= $total_semanas; $semana++): ?>

              <?php
              $inicio = date("Y-m-d", strtotime($anio . "W" . str_pad($semana, 2, '0', STR_PAD_LEFT)));
              $fin    = date("Y-m-d", strtotime($inicio . " +6 days"));
              ?>

              <tr>
                <td><?= $semana ?></td>

                <td><?= $anio ?> - Semana <?= $semana ?></td>

                <td><?= date('d-m-Y', strtotime($inicio)) ?> → <?= date('d-m-Y', strtotime($fin)) ?></td>

                <td class="d-flex gap-2">
                  <a href="../pages/reporte_semanal_pdf.php?anio=<?= $anio ?>&semana=<?= $semana ?>"
                     target="_blank"
                     class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                  </a>

                
                </td>
              </tr>

            <?php endfor; ?>
          </tbody>

        </table>
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
