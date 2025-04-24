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
        <?php $reviewed_check = "(SELECT 1 FROM test_reviewed trw WHERE trw.Sample_Name = p.Sample_Name AND trw.Sample_Number = p.Sample_Number AND trw.Test_Type = p.Test_Type AND trw.Signed = 1)"; ?>
        <?php $Seach = find_by_sql("SELECT * FROM test_repeat p WHERE NOT EXISTS $reviewed_check ORDER BY Start_Date DESC");?>
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
          <?php foreach ($Seach as $index => $item): ?>
      <tr>
        <td><?php echo count_id(); ?></td>
        <td><?php echo htmlspecialchars($item['Sample_Name'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['Sample_Number'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['Test_Type'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['Register_By'] ?? ''); ?></td>
        <td><?php echo date('Y-m-d', strtotime($item['Start_Date'])); ?></td>
        <td><?php echo htmlspecialchars($item['Comment'] ?? ''); ?></td>
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