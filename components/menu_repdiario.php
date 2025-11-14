<?php
$page_title = 'Menu de Reportes Diarios';
require_once('../config/load.php');
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Reporte Diario</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active">Reporte Diario</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Reporte Diario</h5>

            <div class="list-group">
              <a href="../pages/reporteDiario.php" class="list-group-item list-group-item-action">Digitar Reporte Diario</a>

              <a href="../pages/reporte_diario_lab.php" class="list-group-item list-group-item-action">Lista de Reportes</a>
              <a href="../pages/sample_tracker.php" class="list-group-item list-group-item-action">Otros Reportes</a>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>