<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

$reporte_diario = "active";
$hoy = date('Y-m-d');

// Generar arreglo con los últimos 7 días
$fechas = [];
for ($i = 0; $i < 7; $i++) {
  $fechas[] = date('Y-m-d', strtotime("-$i days", strtotime($hoy)));
}
?>

<main id="main" class="main"> 
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-calendar-week"></i> Lista de Reportes Semanales</h1>
    <p>Seleccione un día para generar el reporte en PDF o editar su contenido</p>
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
            <?php $i = 1; foreach ($fechas as $fecha): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= date('d-m-Y', strtotime($fecha)) ?></td>
                <td class="d-flex gap-2">
                  <a href="../pages/generar_reporte_pdf.php?fecha=<?= $fecha ?>" target="_blank" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                  </a>
                  <a href="../pages/editar_reporteDiario.php?fecha=<?= $fecha ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square"></i> Editar
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
