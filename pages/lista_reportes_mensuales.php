<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

$reporte_mensual = "active";

// Año seleccionado
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

// Lista de meses
$meses = [
    1 => "Enero",
    2 => "Febrero",
    3 => "Marzo",
    4 => "Abril",
    5 => "Mayo",
    6 => "Junio",
    7 => "Julio",
    8 => "Agosto",
    9 => "Septiembre",
    10 => "Octubre",
    11 => "Noviembre",
    12 => "Diciembre"
];

?>

<main id="main" class="main">

  <div class="pagetitle mb-3">
    <h1><i class="bi bi-calendar3"></i> Reportes Mensuales del Año <?= $anio ?></h1>
    <p>Seleccione un mes para generar el PDF mensual</p>
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
              <th>Mes</th>
              <th>Rango de Fechas</th>
              <th>Acción</th>
            </tr>
          </thead>

          <tbody>

          <?php 
          foreach ($meses as $numMes => $nombreMes): 
              
              // Fecha inicio y fin del mes
              $inicio = date("Y-m-01", strtotime("$anio-$numMes-01"));
              $fin    = date("Y-m-t", strtotime($inicio)); // último día del mes
          ?>

            <tr>
              <td><?= $numMes ?></td>

              <td><?= $nombreMes ?> <?= $anio ?></td>

              <td><?= date("d-m-Y", strtotime($inicio)) ?> → <?= date("d-m-Y", strtotime($fin)) ?></td>

              <td class="d-flex gap-2">

                <a href="../pages/reporte_mensual_pdf.php?anio=<?= $anio ?>&mes=<?= $numMes ?>"
                   target="_blank"
                   class="btn btn-danger btn-sm">
                  <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>

              </td>
            </tr>

          <?php endforeach; ?>

          </tbody>

        </table>
      </div>
    </div>
  </section>

</main>

<?php include_once('../components/footer.php'); ?>
