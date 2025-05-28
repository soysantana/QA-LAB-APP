<?php
$page_title = 'Grain Size Menu';
require_once('../config/load.php');
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Grain Size</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active">Grain Size</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Tipos de ensayos</h5>

            <div class="list-group">
              <a href="grain-size.php" class="list-group-item list-group-item-action">General</a>
              <a href="grain-size-coarse-filter.php" class="list-group-item list-group-item-action">CF</a>
              <a href="grain-size-fine-filter.php" class="list-group-item list-group-item-action">FF</a>
              <a href="grain-size-lpf.php" class="list-group-item list-group-item-action">LPF</a>
              <a href="grain-size-upstream-transition-fill.php" class="list-group-item list-group-item-action">UTF</a>
              <a href="grain-size-full.php" class="list-group-item list-group-item-action">TRF</a>
              <a href="grain-size-full.php" class="list-group-item list-group-item-action">UFF</a>
              <a href="grain-size-full.php" class="list-group-item list-group-item-action">FRF</a>
              <a href="grain-size-full.php" class="list-group-item list-group-item-action">IRF</a>
              <a href="grain-size-full.php" class="list-group-item list-group-item-action">RF</a>
              <a href="grain-size-full.php" class="list-group-item list-group-item-action">BF</a>
              <a href="hydrometer.php" class="list-group-item list-group-item-action">Hydrometer</a>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>