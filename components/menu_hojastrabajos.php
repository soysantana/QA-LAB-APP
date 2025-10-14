<?php
$page_title = 'Menu Hojas ';
require_once('../config/load.php');
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Hojas de trabajos</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active">Hojas de Trabajos</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Hojas de Trabajos</h5>

            <div class="list-group">
              <a href="../pages/formaparticulas.php" class="list-group-item list-group-item-action">Forma de Particulas</a>
             
              <a href="#" class="list-group-item list-group-item-action"></a>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>