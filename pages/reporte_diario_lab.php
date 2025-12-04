<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

$reporte_diario = "active";

// Año y mes seleccionados
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');
$mes  = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');

// Nombre de meses
$meses = [
    1 => "Enero", 2 => "Febrero", 3 => "Marzo",
    4 => "Abril", 5 => "Mayo", 6 => "Junio",
    7 => "Julio", 8 => "Agosto", 9 => "Septiembre",
    10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
];

// Primer y último día del mes
$inicioMes = "$anio-" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "-01";
$finMes    = date("Y-m-t", strtotime($inicioMes)); 
?>

<main id="main" class="main"> 
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-calendar-day"></i> Reportes Diarios — <?= $meses[$mes] ?> <?= $anio ?></h1>
    <p>Seleccione un día para generar el reporte en PDF o editarlo</p>
  </div>

  <!-- SELECTOR AÑO Y MES -->
  <div class="mb-3">
    <form method="GET" class="d-flex gap-2">

      <select name="anio" class="form-select" style="max-width:150px;">
        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
          <option value="<?= $y ?>" <?= ($y == $anio ? 'selected' : '') ?>>
            <?= $y ?>
          </option>
        <?php endfor; ?>
      </select>

      <select name="mes" class="form-select" style="max-width:180px;">
        <?php foreach ($meses as $num => $nombre): ?>
          <option value="<?= $num ?>" <?= ($num == $mes ? 'selected' : '') ?>>
            <?= $nombre ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button class="btn btn-dark">Filtrar</button>
    </form>
  </div>

  <section class="section">
    <div class="card shadow-sm">
      <div class="card-body pt-4">

        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Acción</th>
            </tr>
          </thead>

          <tbody>

            <?php 
            $i = 1;
            $diaActual = strtotime($inicioMes);

            while ($diaActual <= strtotime($finMes)):

              $fecha = date("Y-m-d", $diaActual);
            ?>

              <tr>
                <td><?= $i++ ?></td>

                <td><?= date("d-m-Y", strtotime($fecha)) ?></td>

                <td class="d-flex gap-2">
                  
                  <a href="../pages/generar_reporte_pdf.php?fecha=<?= $fecha ?>" 
                     target="_blank" 
                     class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                  </a>

                  <a href="../pages/editar_reporteDiario.php?fecha=<?= $fecha ?>"
                     class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square"></i> Editar
                  </a>
                </td>
              </tr>

            <?php 
              // Siguiente día
              $diaActual = strtotime("+1 day", $diaActual);
            endwhile;
            ?>

          </tbody>

        </table>

      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
