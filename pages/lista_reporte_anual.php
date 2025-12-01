<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');


$reporte_anual = "active";

// Años disponibles
$anioActual = date('Y');
$anioMinimo = 2020;

// Año seleccionado
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : $anioActual;

?>

<main id="main" class="main">

  <div class="pagetitle mb-3">
    <h1><i class="bi bi-calendar2-year"></i> Reportes Anuales del Laboratorio</h1>
    <p>Seleccione un año y genere el Reporte Anual completo en PDF.</p>
  </div>

  <!-- Selector de Año -->
  <div class="mb-3">
    <form method="GET" class="d-flex gap-2">
      <select name="anio" class="form-select" style="max-width:180px;">
        <?php for ($y = $anioActual; $y >= $anioMinimo; $y--): ?>
          <option value="<?= $y ?>" <?= ($y == $anio ? 'selected' : '') ?>>
            <?= $y ?>
          </option>
        <?php endfor; ?>
      </select>
      <button class="btn btn-dark">Mostrar</button>
    </form>
  </div>

  <section class="section">
    <div class="card shadow-sm">
      <div class="card-body pt-4">

        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th style="width:70px;">#</th>
              <th>Año</th>
              <th>Rango de Fechas</th>
              <th style="width:150px;">Acción</th>
            </tr>
          </thead>

          <tbody>

            <tr>
              <td>1</td>

              <td><?= $anio ?></td>

              <td>
                <?= "01-01-$anio" ?> → <?= "31-12-$anio" ?>
              </td>

              <td class="d-flex gap-2">
                <a href="../pages/reporte_anual_pdf.php?year=<?= $anio ?>"
                   target="_blank"
                   class="btn btn-danger btn-sm w-100">
                  <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
              </td>
            </tr>

          </tbody>

        </table>

      </div>
    </div>
  </section>

</main>

<?php include_once('../components/footer.php'); ?>
