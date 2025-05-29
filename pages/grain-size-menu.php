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
              <a href="grain-size-coarse-filter.php" class="list-group-item list-group-item-action">Coarse Filter (CF)</a>
              <a href="grain-size-fine-filter.php" class="list-group-item list-group-item-action">Fine Filter (FF)</a>
              <a href="grain-size-lpf.php" class="list-group-item list-group-item-action">Low Permeability Fill (LPF)</a>
              <a href="grain-size-upstream-transition-fill.php" class="list-group-item list-group-item-action">Upstream Transition Fill (UTF)</a>
              <a href="grain-size-full.php" class="list-group-item list-group-item-action">Full Grain Size</a>         
              <a href="hydrometer.php" class="list-group-item list-group-item-action">Hydrometer</a>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>