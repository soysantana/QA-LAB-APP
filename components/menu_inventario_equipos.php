<?php
$page_title = 'Menu de Inventario de Equipos';
require_once('../config/load.php');
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Inventarios</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active">Inventarios</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Inventarios de Equipos y Herramientas</h5>

            <div class="list-group">
              <a href="../pages/categories.php" class="list-group-item list-group-item-action">Categorias</a>
               <a href="../pages/product.php" class="list-group-item list-group-item-action">Articulos</a>             
              <a href="../pages/media.php" class="list-group-item list-group-item-action">Imagenes</a>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>