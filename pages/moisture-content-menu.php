<?php
  $page_title = 'Moisture Content Menu';
  require_once('../config/load.php');
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Moisture Content</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Pages</li>
      <li class="breadcrumb-item active">Moisture Content</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Tipo de ensayos</h5>
          
          <div class="list-group">
            <a href="moisture-oven.php" class="list-group-item list-group-item-action">Oven</a>
            <a href="moisture-scale.php" class="list-group-item list-group-item-action">Scale</a>
            <a href="moisture-microwave.php" class="list-group-item list-group-item-action">Microwave</a>
            <a href="moisture-constant-mass.php" class="list-group-item list-group-item-action">Constant Mass</a>
          </div>

        </div>
      </div>

    </div>
    
  </div>
</section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>