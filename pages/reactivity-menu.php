<?php
$page_title = 'Reactivity Menu';
require_once('../config/load.php');
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Reactivity</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active">Reactivity</li>
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
              <a href="reactivity-coarse.php" class="list-group-item list-group-item-action">Coarse Particles</a>
              <a href="reactivity-fine.php" class="list-group-item list-group-item-action">Fine Particles</a>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>