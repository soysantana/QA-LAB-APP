<?php
  $page_title = 'Muestra repetida';
  $tracking_show = 'show';
  $class_tracking = ' ';
  $repeat = 'active';
  require_once('../config/load.php');
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Muestra repetida</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Formularios</li>
      <li class="breadcrumb-item active">Muestra repetida</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<div class="col-md-4"><?php echo display_msg($msg); ?></div>

<section class="section">
  <div class="row">

  <div class="col-lg-9">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">LISTA DE MUESTRAS EN REPETICIÓN</h5>

        <?php $week = date('Y-m-d', strtotime('-30 days')); ?>
        <?php $Seach = find_by_sql("SELECT * FROM test_repeat WHERE Start_Date >= '{$week}'");?>
        <!-- Bordered Table -->
        <table class="table table-bordered datatable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre de la muestra</th>
              <th scope="col">Numero de muestra</th>
              <th scope="col">Tipo de prueba</th>
              <th scope="col">Registrado por</th>
              <th scope="col">Fecha de inicio</th>
              <th scope="col">Comentario</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($Seach as $Seach):?>
              <tr>
                <td><?php echo count_id();?></td>
                <td><?php echo $Seach['Sample_Name']; ?></td>
                <td><?php echo $Seach['Sample_Number']; ?></td>
                <td><?php echo $Seach['Test_Type']; ?></td>
                <td><?php echo $Seach['Register_By']; ?></td>
                <td><?php echo $Seach['Start_Date']; ?></td>
                <td><?php echo $Seach['Comment']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <!-- End Bordered Table -->

      </div>
    </div>
  </div>

  </div>
</section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>