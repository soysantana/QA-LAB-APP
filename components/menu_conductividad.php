<?php
  $page_title = 'Menu de Conductividad Hidráulica';
  require_once('../config/load.php');
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Conductividad Hidráulica</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Pages</li>
      <li class="breadcrumb-item active">Conductividad Hidráulica</li>
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
            <a href="/pages/d2434-entry.php" class="list-group-item list-group-item-action">Carga Constante</a>
            <a href="" class="list-group-item list-group-item-action">Carga Variable</a>
          </div>

        </div>
      </div>

    </div>
    
  </div>
</section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>